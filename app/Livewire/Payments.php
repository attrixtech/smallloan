<?php

namespace App\Livewire;

use App\Models\FinancialLog;
use App\Models\Loan;
use App\Models\Payment;
use App\Services\InterestCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Payments extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = 'all';
    public $modeFilter = 'all';

    public $showCreateModal = false;
    public $showPrintModal = false;

    // Payment Form fields
    public $loan_id;
    public $payment_no;
    public $payment_date;
    public $payment_type = 'interest_only';
    public $interest_amount = 0;
    public $principal_amount = 0;
    public $penalty_amount = 0;
    public $discount_amount = 0;
    public $total_amount_paid = 0;
    public $payment_mode = 'cash';
    public $reference_number = '';
    public $notes = '';

    // Live calculation state for selected loan
    public $activeLoans = [];
    public $selectedLoanDetails = null;
    public $liveInterestCalc = null;

    // Selected payment for printing
    public $selectedPayment = null;

    protected $queryString = ['search', 'typeFilter', 'modeFilter'];

    public function mount()
    {
        $this->payment_date = date('Y-m-d');
        $this->payment_no = Payment::generatePaymentNo();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->payment_no = Payment::generatePaymentNo();
        $this->payment_date = date('Y-m-d');
        $this->loan_id = '';
        $this->payment_type = 'interest_only';
        $this->interest_amount = 0;
        $this->principal_amount = 0;
        $this->penalty_amount = 0;
        $this->discount_amount = 0;
        $this->total_amount_paid = 0;
        $this->payment_mode = 'cash';
        $this->reference_number = '';
        $this->notes = '';
        $this->selectedLoanDetails = null;
        $this->liveInterestCalc = null;

        $this->activeLoans = Loan::with('customer')
            ->whereIn('status', ['Active', 'Overdue'])
            ->orderBy('id', 'desc')
            ->get();

        $this->showCreateModal = true;
    }

    public function updatedLoanId()
    {
        if (empty($this->loan_id)) {
            $this->selectedLoanDetails = null;
            $this->liveInterestCalc = null;
            return;
        }

        $loan = Loan::with('customer')->find($this->loan_id);
        if ($loan) {
            $this->selectedLoanDetails = $loan;
            $this->liveInterestCalc = InterestCalculator::calculate($loan, $this->payment_date);
            $this->applyPaymentTypeDefaults();
        }
    }

    public function updatedPaymentDate()
    {
        if ($this->selectedLoanDetails) {
            $this->liveInterestCalc = InterestCalculator::calculate($this->selectedLoanDetails, $this->payment_date);
            $this->applyPaymentTypeDefaults();
        }
    }

    public function updatedPaymentType()
    {
        $this->applyPaymentTypeDefaults();
    }

    public function applyPaymentTypeDefaults()
    {
        if (!$this->liveInterestCalc) return;

        $netInterest = floatval($this->liveInterestCalc['net_interest_due']);
        $principal = floatval($this->liveInterestCalc['principal']);

        switch ($this->payment_type) {
            case 'interest_only':
                $this->interest_amount = $netInterest;
                $this->principal_amount = 0;
                break;

            case 'principal_only':
                $this->interest_amount = 0;
                $this->principal_amount = min(5000, $principal);
                break;

            case 'partial_both':
                $this->interest_amount = $netInterest;
                $this->principal_amount = round($principal * 0.25, 2);
                break;

            case 'full_settlement':
                $this->interest_amount = $netInterest;
                $this->principal_amount = $principal;
                break;
        }

        $this->recalcTotal();
    }

    public function updatedInterestAmount()
    {
        $this->recalcTotal();
    }

    public function updatedPrincipalAmount()
    {
        $this->recalcTotal();
    }

    public function updatedPenaltyAmount()
    {
        $this->recalcTotal();
    }

    public function updatedDiscountAmount()
    {
        $this->recalcTotal();
    }

    public function recalcTotal()
    {
        $interest = floatval($this->interest_amount);
        $principal = floatval($this->principal_amount);
        $penalty = floatval($this->penalty_amount);
        $discount = floatval($this->discount_amount);

        $this->total_amount_paid = max(0, round($interest + $principal + $penalty - $discount, 2));
    }

    public function processPayment()
    {
        $this->validate([
            'loan_id' => 'required|exists:loans,id',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:interest_only,principal_only,partial_both,full_settlement',
            'interest_amount' => 'required|numeric|min:0',
            'principal_amount' => 'required|numeric|min:0',
            'penalty_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount_paid' => 'required|numeric|min:1',
            'payment_mode' => 'required|in:cash,upi,bank_transfer,cheque',
        ]);

        $loan = Loan::findOrFail($this->loan_id);

        if (floatval($this->principal_amount) > floatval($loan->current_balance_principal)) {
            $this->addError('principal_amount', 'Principal payment cannot exceed current balance of ₹' . number_format($loan->current_balance_principal, 2));
            return;
        }

        $paymentNo = Payment::generatePaymentNo();

        DB::transaction(function () use ($loan, $paymentNo) {
            $principalPaid = floatval($this->principal_amount);
            $interestPaid = floatval($this->interest_amount);
            $newPrincipalBalance = max(0, round($loan->current_balance_principal - $principalPaid, 2));

            $payment = Payment::create([
                'payment_no' => $paymentNo,
                'loan_id' => $loan->id,
                'customer_id' => $loan->customer_id,
                'payment_date' => $this->payment_date,
                'payment_type' => $this->payment_type,
                'interest_amount' => $interestPaid,
                'principal_amount' => $principalPaid,
                'penalty_amount' => floatval($this->penalty_amount),
                'discount_amount' => floatval($this->discount_amount),
                'total_amount_paid' => $this->total_amount_paid,
                'payment_mode' => $this->payment_mode,
                'reference_number' => $this->reference_number,
                'principal_balance_after' => $newPrincipalBalance,
                'notes' => $this->notes,
                'received_by' => auth()->id(),
                'is_immutable' => true,
            ]);

            $loan->total_interest_paid = round($loan->total_interest_paid + $interestPaid, 2);
            $loan->total_principal_paid = round($loan->total_principal_paid + $principalPaid, 2);
            $loan->current_balance_principal = $newPrincipalBalance;

            if ($newPrincipalBalance <= 0 || $this->payment_type === 'full_settlement') {
                $loan->status = 'Closed';
                $loan->closed_at = now();
                $loan->settlement_discount = floatval($this->discount_amount);
            }

            $loan->save();

            FinancialLog::record(
                'payment_received',
                "Payment received: {$paymentNo} for ₹" . number_format($this->total_amount_paid, 2) . " [{$this->payment_type}]",
                $this->total_amount_paid,
                $loan->id,
                $loan->customer_id,
                $paymentNo,
                [
                    'interest' => $interestPaid,
                    'principal' => $principalPaid,
                    'balance_after' => $newPrincipalBalance,
                    'mode' => $this->payment_mode,
                ]
            );
        });

        $this->showCreateModal = false;
        session()->flash('success', "Payment receipt {$paymentNo} generated and ledger updated successfully!");
    }

    public function printReceipt($id)
    {
        $this->selectedPayment = Payment::with(['loan.pledgedItems', 'customer', 'receiver'])->findOrFail($id);
        $this->showPrintModal = true;
    }

    public function render()
    {
        $query = Payment::with(['loan', 'customer', 'receiver']);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('payment_no', 'like', "%{$this->search}%")
                  ->orWhere('reference_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function($cq) {
                      $cq->where('name', 'like', "%{$this->search}%")
                         ->orWhere('mobile', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('loan', function($lq) {
                      $lq->where('loan_no', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->typeFilter !== 'all') {
            $query->where('payment_type', $this->typeFilter);
        }

        if ($this->modeFilter !== 'all') {
            $query->where('payment_mode', $this->modeFilter);
        }

        $payments = $query->orderBy('id', 'desc')->paginate(15);

        $data = array_merge(get_object_vars($this), [
            'payments' => $payments,
        ]);

        return view('livewire.payments', $data)->layout('layouts.app', ['title' => 'Payment Collections']);
    }
}
