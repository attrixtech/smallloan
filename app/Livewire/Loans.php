<?php

namespace App\Livewire;

use App\Models\BusinessSetting;
use App\Models\Customer;
use App\Models\FinancialLog;
use App\Models\Loan;
use App\Models\PledgedItem;
use App\Services\InterestCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Loans extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';

    public $showCreateModal = false;
    public $showViewModal = false;
    public $showPrintModal = false;
    public $showReceiptModal = false;

    // Loan Form Fields
    public $customer_id;
    public $loan_no;
    public $principal_amount;
    public $interest_rate = 1.50;
    public $interest_rate_type = 'per_month';
    public $interest_calculation_method = 'monthly';
    public $issue_date;
    public $tenure_months = 12;
    public $maturity_date;
    public $notes;

    // Dynamic Pledged Items in new loan
    public $items = [];

    // Selected loan for View / Print
    public $selectedLoan = null;

    protected $queryString = ['search', 'statusFilter'];

    public function mount()
    {
        $this->issue_date = date('Y-m-d');
        $this->updateMaturityDate();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedTenureMonths()
    {
        $this->updateMaturityDate();
    }

    public function updatedIssueDate()
    {
        $this->updateMaturityDate();
    }

    public function updateMaturityDate()
    {
        if ($this->issue_date && $this->tenure_months) {
            $this->maturity_date = Carbon::parse($this->issue_date)->addMonths(intval($this->tenure_months))->format('Y-m-d');
        }
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->customer_id = '';
        $this->loan_no = Loan::generateLoanNo();
        $this->principal_amount = '';
        $this->interest_rate = (float) BusinessSetting::get('default_interest_rate', 1.50);
        $this->interest_calculation_method = BusinessSetting::get('default_interest_method', 'monthly');
        $this->interest_rate_type = 'per_month';
        $this->tenure_months = (int) BusinessSetting::get('default_loan_tenure_months', 12);
        $this->issue_date = date('Y-m-d');
        $this->updateMaturityDate();
        $this->notes = '';

        $defaultRate = (float) BusinessSetting::get('gold_rate_22k', 6850);
        $this->items = [
            [
                'item_name' => '',
                'category' => 'Ring',
                'gross_weight' => 0,
                'stone_weight' => 0,
                'net_gold_weight' => 0,
                'purity' => '22K',
                'gold_rate_per_gram' => $defaultRate,
                'valuation_amount' => 0,
                'packet_number' => 'PKT-' . rand(1000, 9999),
                'locker_reference' => 'Locker A - Shelf 1',
                'remarks' => '',
            ]
        ];

        $this->showCreateModal = true;
    }

    public function addItemRow()
    {
        $defaultRate = (float) BusinessSetting::get('gold_rate_22k', 6850);
        $this->items[] = [
            'item_name' => '',
            'category' => 'Ring',
            'gross_weight' => 0,
            'stone_weight' => 0,
            'net_gold_weight' => 0,
            'purity' => '22K',
            'gold_rate_per_gram' => $defaultRate,
            'valuation_amount' => 0,
            'packet_number' => 'PKT-' . rand(1000, 9999),
            'locker_reference' => 'Locker A - Shelf 1',
            'remarks' => '',
        ];
    }

    public function removeItemRow($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function recalculateItemRow($index)
    {
        $this->updateItemCalculation($index);
    }

    public function updateItemCalculation($index)
    {
        if (!isset($this->items[$index])) return;

        $purity = $this->items[$index]['purity'] ?? '22K';
        $rateKey = match($purity) {
            '24K' => 'gold_rate_24k',
            '22K' => 'gold_rate_22k',
            '20K' => 'gold_rate_20k',
            '18K' => 'gold_rate_18k',
            default => 'gold_rate_22k'
        };
        $defaultRate = match($purity) {
            '24K' => 7450,
            '22K' => 6850,
            '20K' => 6250,
            '18K' => 5600,
            default => 6850
        };

        if (empty($this->items[$index]['gold_rate_per_gram'])) {
            $this->items[$index]['gold_rate_per_gram'] = (float) BusinessSetting::get($rateKey, $defaultRate);
        }

        $gross = floatval($this->items[$index]['gross_weight'] ?? 0);
        $stone = floatval($this->items[$index]['stone_weight'] ?? 0);
        $net = max(0, round($gross - $stone, 3));
        $this->items[$index]['net_gold_weight'] = $net;

        $rate = floatval($this->items[$index]['gold_rate_per_gram'] ?? 0);
        $this->items[$index]['valuation_amount'] = round($net * $rate, 2);
    }

    public function getTotalValuationProperty()
    {
        return array_sum(array_map('floatval', array_column($this->items, 'valuation_amount')));
    }

    public function getTotalNetWeightProperty()
    {
        return array_sum(array_map('floatval', array_column($this->items, 'net_gold_weight')));
    }

    public function getTotalGrossWeightProperty()
    {
        return array_sum(array_map('floatval', array_column($this->items, 'gross_weight')));
    }

    public function getTotalStoneWeightProperty()
    {
        return array_sum(array_map('floatval', array_column($this->items, 'stone_weight')));
    }

    public function getCalculatedLtvProperty()
    {
        $val = $this->total_valuation;
        $principal = floatval($this->principal_amount ?? 0);
        return $val > 0 ? round(($principal / $val) * 100, 2) : 0;
    }

    public function createLoan()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'principal_amount' => 'required|numeric|min:500',
            'interest_rate' => 'required|numeric|min:0.1',
            'interest_calculation_method' => 'required|in:monthly,daily,annual,flat,reducing_balance',
            'issue_date' => 'required|date',
            'maturity_date' => 'required|date|after_or_equal:issue_date',
            'tenure_months' => 'required|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.gross_weight' => 'required|numeric|min:0.001',
            'items.*.purity' => 'required|string',
            'items.*.gold_rate_per_gram' => 'required|numeric|min:1',
        ]);

        foreach ($this->items as $idx => $item) {
            $this->updateItemCalculation($idx);
        }

        $totalValuation = $this->total_valuation;
        $totalGross = $this->total_gross_weight;
        $totalStone = $this->total_stone_weight;
        $totalNet = $this->total_net_weight;

        $ltv = $totalValuation > 0 ? round(($this->principal_amount / $totalValuation) * 100, 2) : 0;

        DB::transaction(function () use ($totalValuation, $totalGross, $totalStone, $totalNet, $ltv) {
            $loan = Loan::create([
                'loan_no' => Loan::generateLoanNo(),
                'customer_id' => $this->customer_id,
                'principal_amount' => $this->principal_amount,
                'interest_rate' => $this->interest_rate,
                'interest_rate_type' => $this->interest_rate_type,
                'interest_calculation_method' => $this->interest_calculation_method,
                'issue_date' => $this->issue_date,
                'maturity_date' => $this->maturity_date,
                'tenure_months' => $this->tenure_months,
                'total_pledged_items' => count($this->items),
                'total_gross_weight' => $totalGross,
                'total_stone_weight' => $totalStone,
                'total_net_weight' => $totalNet,
                'total_valuation_amount' => $totalValuation,
                'ltv_ratio' => $ltv,
                'total_interest_paid' => 0,
                'total_principal_paid' => 0,
                'current_balance_principal' => $this->principal_amount,
                'status' => 'Active',
                'notes' => $this->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($this->items as $item) {
                PledgedItem::create([
                    'loan_id' => $loan->id,
                    'customer_id' => $this->customer_id,
                    'item_name' => $item['item_name'],
                    'category' => $item['category'] ?? 'Ring',
                    'gross_weight' => $item['gross_weight'],
                    'stone_weight' => $item['stone_weight'] ?? 0,
                    'net_gold_weight' => $item['net_gold_weight'],
                    'purity' => $item['purity'],
                    'gold_rate_per_gram' => $item['gold_rate_per_gram'],
                    'valuation_amount' => $item['valuation_amount'],
                    'packet_number' => $item['packet_number'] ?? null,
                    'locker_reference' => $item['locker_reference'] ?? 'Locker A',
                    'status' => 'Pledged',
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            FinancialLog::record(
                'loan_created',
                "Loan issued: {$loan->loan_no} for ₹" . number_format($loan->principal_amount, 2),
                $loan->principal_amount,
                $loan->id,
                $loan->customer_id,
                $loan->loan_no,
                ['items_count' => count($this->items), 'net_weight' => $totalNet, 'ltv' => $ltv]
            );
        });

        $this->showCreateModal = false;
        session()->flash('success', "Loan {$this->loan_no} disbursed successfully!");
    }

    public function viewLoan($id)
    {
        $loan = Loan::with(['customer', 'pledgedItems', 'payments', 'renewals', 'goldReleases'])->findOrFail($id);
        $loan->checkAndUpdateOverdueStatus();
        $this->selectedLoan = $loan;
        $this->showViewModal = true;
    }

    public function printReceipt($id)
    {
        $loan = Loan::with(['customer', 'pledgedItems', 'creator'])->findOrFail($id);
        $this->selectedLoan = $loan;
        $this->showPrintModal = true;
        $this->showReceiptModal = true;
    }

    public function render()
    {
        $query = Loan::with(['customer', 'pledgedItems']);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('loan_no', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function($cq) {
                      $cq->where('name', 'like', "%{$this->search}%")
                         ->orWhere('mobile', 'like', "%{$this->search}%")
                         ->orWhere('customer_code', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $loans = $query->orderBy('id', 'desc')->paginate(12);
        $customers = Customer::where('status', 'active')->orderBy('name')->get();

        $data = array_merge(get_object_vars($this), [
            'loans' => $loans,
            'customers' => $customers,
        ]);

        return view('livewire.loans', $data)->layout('layouts.app', ['title' => 'Loan Management']);
    }
}
