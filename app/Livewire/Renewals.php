<?php

namespace App\Livewire;

use App\Models\FinancialLog;
use App\Models\Loan;
use App\Models\LoanRenewal;
use App\Models\Payment;
use App\Services\InterestCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Renewals extends Component
{
    use WithPagination;

    public $search = '';

    public $showCreateModal = false;
    public $showDetailModal = false;
    public $showPrintModal = false;

    // Renewal form fields
    public $loan_id;
    public $renewal_no;
    public $renewal_date;
    public $extension_months = 12;
    public $new_maturity_date;
    public $interest_cleared_amount = 0;
    public $principal_adjustment = 0;
    public $renewal_fee = 0;
    public $payment_mode = 'cash';
    public $reference_number = '';
    public $notes = '';

    // Selected loan preview
    public $activeLoans = [];
    public $selectedLoan = null;
    public $liveCalc = null;

    // Selected renewal for detail view
    public $selectedRenewal = null;

    protected $queryString = ['search'];

    public function mount()
    {
        $this->renewal_date = date('Y-m-d');
        $this->renewal_no = LoanRenewal::generateRenewalNo();
        $this->recalcNewMaturity();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->loan_id = '';
        $this->renewal_date = date('Y-m-d');
        $this->renewal_no = LoanRenewal::generateRenewalNo();
        $this->extension_months = 12;
        $this->recalcNewMaturity();
        $this->interest_cleared_amount = 0;
        $this->principal_adjustment = 0;
        $this->renewal_fee = 0;
        $this->payment_mode = 'cash';
        $this->reference_number = '';
        $this->notes = '';
        $this->selectedLoan = null;
        $this->liveCalc = null;

        $this->activeLoans = Loan::with('customer')
            ->whereIn('status', ['Active', 'Overdue'])
            ->orderBy('id', 'desc')
            ->get();

        $this->showCreateModal = true;
    }

    public function updatedLoanId()
    {
        if (empty($this->loan_id)) {
            $this->selectedLoan = null;
            $this->liveCalc = null;
            return;
        }

        $loan = Loan::with('customer')->find($this->loan_id);
        if ($loan) {
            $this->selectedLoan = $loan;
            $this->liveCalc = InterestCalculator::calculate($loan, $this->renewal_date);
            $this->interest_cleared_amount = $this->liveCalc['net_interest_due'];
            $this->recalcNewMaturity();
        }
    }

    public function updatedExtensionMonths()
    {
        $this->recalcNewMaturity();
    }

    public function updatedRenewalDate()
    {
        if ($this->selectedLoan) {
            $this->liveCalc = InterestCalculator::calculate($this->selectedLoan, $this->renewal_date);
            $this->interest_cleared_amount = $this->liveCalc['net_interest_due'];
        }
        $this->recalcNewMaturity();
    }

    public function recalcNewMaturity()
    {
        $baseDate = $this->selectedLoan ? Carbon::parse($this->selectedLoan->maturity_date) : Carbon::parse($this->renewal_date);
        if (Carbon::parse($this->renewal_date)->gt($baseDate)) {
            $baseDate = Carbon::parse($this->renewal_date);
        }
        $this->new_maturity_date = $baseDate->copy()->addMonths(intval($this->extension_months))->format('Y-m-d');
    }

    public function processRenewal()
    {
        $this->validate([
            'loan_id' => 'required|exists:loans,id',
            'renewal_date' => 'required|date',
            'new_maturity_date' => 'required|date|after:renewal_date',
            'extension_months' => 'required|integer|min:1',
            'interest_cleared_amount' => 'required|numeric|min:0',
            'principal_adjustment' => 'nullable|numeric',
            'renewal_fee' => 'nullable|numeric|min:0',
            'payment_mode' => 'required|in:cash,upi,bank_transfer,cheque',
        ]);

        $loan = Loan::findOrFail($this->loan_id);
        $adj = floatval($this->principal_adjustment);
        $newPrincipal = round($loan->current_balance_principal + $adj, 2);

        if ($newPrincipal <= 0) {
            $this->addError('principal_adjustment', 'Principal cannot be reduced to 0 in renewal. Please use Full Settlement in Payments instead.');
            return;
        }

        $renewalNo = LoanRenewal::generateRenewalNo();
        $interestPaid = floatval($this->interest_cleared_amount);

        DB::transaction(function () use ($loan, $renewalNo, $interestPaid, $newPrincipal, $adj) {
            $paymentId = null;

            $totalCashIn = $interestPaid + ($adj < 0 ? abs($adj) : 0) + floatval($this->renewal_fee);
            if ($totalCashIn > 0) {
                $pay = Payment::create([
                    'payment_no' => Payment::generatePaymentNo(),
                    'loan_id' => $loan->id,
                    'customer_id' => $loan->customer_id,
                    'payment_date' => $this->renewal_date,
                    'payment_type' => ($adj < 0) ? 'partial_both' : 'interest_only',
                    'interest_amount' => $interestPaid,
                    'principal_amount' => ($adj < 0) ? abs($adj) : 0,
                    'penalty_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount_paid' => $totalCashIn,
                    'payment_mode' => $this->payment_mode,
                    'reference_number' => $this->reference_number,
                    'principal_balance_after' => $newPrincipal,
                    'notes' => "Collected during Loan Renewal {$renewalNo}",
                    'received_by' => auth()->id(),
                    'is_immutable' => true,
                ]);
                $paymentId = $pay->id;

                $loan->total_interest_paid = round($loan->total_interest_paid + $interestPaid, 2);
                if ($adj < 0) {
                    $loan->total_principal_paid = round($loan->total_principal_paid + abs($adj), 2);
                }
            }

            LoanRenewal::create([
                'renewal_no' => $renewalNo,
                'loan_id' => $loan->id,
                'customer_id' => $loan->customer_id,
                'previous_maturity_date' => $loan->maturity_date,
                'new_maturity_date' => $this->new_maturity_date,
                'renewal_date' => $this->renewal_date,
                'interest_cleared_amount' => $interestPaid,
                'payment_id' => $paymentId,
                'principal_adjusted' => $adj,
                'new_principal_amount' => $newPrincipal,
                'renewal_fee' => floatval($this->renewal_fee),
                'notes' => $this->notes,
                'processed_by' => auth()->id(),
            ]);

            $loan->maturity_date = $this->new_maturity_date;
            $loan->current_balance_principal = $newPrincipal;
            $loan->status = 'Active';
            $loan->save();

            FinancialLog::record(
                'loan_renewed',
                "Loan {$loan->loan_no} renewed ({$renewalNo}) extended to {$this->new_maturity_date}. Interest cleared: ₹{$interestPaid}",
                $totalCashIn,
                $loan->id,
                $loan->customer_id,
                $renewalNo
            );
        });

        $this->showCreateModal = false;
        session()->flash('success', "Loan renewal {$renewalNo} completed successfully!");
    }

    public function viewRenewal($id)
    {
        $this->selectedRenewal = LoanRenewal::with(['loan.customer', 'payment', 'processor'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function render()
    {
        $query = LoanRenewal::with(['loan.customer', 'payment', 'processor']);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('renewal_no', 'like', "%{$this->search}%")
                  ->orWhereHas('loan', function($lq) {
                      $lq->where('loan_no', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('customer', function($cq) {
                      $cq->where('name', 'like', "%{$this->search}%")
                         ->orWhere('mobile', 'like', "%{$this->search}%");
                  });
            });
        }

        $renewals = $query->orderBy('id', 'desc')->paginate(12);

        $data = array_merge(get_object_vars($this), [
            'renewals' => $renewals,
        ]);

        return view('livewire.renewals', $data)->layout('layouts.app', ['title' => 'Loan Renewals']);
    }
}
