<?php

namespace App\Livewire;

use App\Models\BusinessSetting;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\PledgedItem;
use Livewire\Component;
use Livewire\WithPagination;

class Pledges extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = 'all';
    public $purityFilter = 'all';
    public $statusFilter = 'all';
    public $lockerFilter = 'all';

    public $showCreateModal = false;
    public $showEditModal = false;
    public $showViewModal = false;
    public $showTagModal = false;

    // Item form fields
    public $pledgeId;
    public $customer_id;
    public $loan_id;
    public $item_name;
    public $category = 'Ring';
    public $gross_weight = 0;
    public $stone_weight = 0;
    public $net_gold_weight = 0;
    public $purity = '22K';
    public $gold_rate_per_gram = 6850;
    public $valuation_amount = 0;
    public $packet_number;
    public $locker_reference = 'Locker A - Tray 1';
    public $remarks;
    public $status = 'Pledged';

    public $selectedPledge = null;

    protected $queryString = ['search', 'categoryFilter', 'purityFilter', 'statusFilter'];

    public function mount()
    {
        $this->gold_rate_per_gram = (float) BusinessSetting::get('gold_rate_22k', 6850);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedGrossWeight()
    {
        $this->recalcWeights();
    }

    public function updatedStoneWeight()
    {
        $this->recalcWeights();
    }

    public function updatedPurity()
    {
        $rateKey = match($this->purity) {
            '24K' => 'gold_rate_24k',
            '22K' => 'gold_rate_22k',
            '20K' => 'gold_rate_20k',
            '18K' => 'gold_rate_18k',
            default => 'gold_rate_22k'
        };
        $defaultRate = match($this->purity) {
            '24K' => 7450,
            '22K' => 6850,
            '20K' => 6250,
            '18K' => 5600,
            default => 6850
        };
        $this->gold_rate_per_gram = (float) BusinessSetting::get($rateKey, $defaultRate);
        $this->recalcWeights();
    }

    public function updatedGoldRatePerGram()
    {
        $this->recalcWeights();
    }

    public function recalcWeights()
    {
        $gross = floatval($this->gross_weight);
        $stone = floatval($this->stone_weight);
        $this->net_gold_weight = max(0, round($gross - $stone, 3));
        $this->valuation_amount = round($this->net_gold_weight * floatval($this->gold_rate_per_gram), 2);
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset([
            'pledgeId', 'customer_id', 'item_name', 'gross_weight',
            'stone_weight', 'net_gold_weight', 'valuation_amount',
            'packet_number', 'locker_reference', 'remarks'
        ]);
        $this->category = 'Ring';
        $this->purity = '22K';
        $this->gold_rate_per_gram = (float) BusinessSetting::get('gold_rate_22k', 6850);
        $this->status = 'Pledged';

        $lastPkt = PledgedItem::latest('id')->first();
        $this->packet_number = 'PKT-' . str_pad($lastPkt ? ($lastPkt->id + 1) : 1, 4, '0', STR_PAD_LEFT);
        $this->locker_reference = 'Locker A - Tray 1';
        $this->showCreateModal = true;
    }

    public function saveItem()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'item_name' => 'required|string|max:150',
            'category' => 'required|string',
            'gross_weight' => 'required|numeric|min:0.001',
            'stone_weight' => 'required|numeric|min:0',
            'purity' => 'required|in:18K,20K,22K,24K',
            'gold_rate_per_gram' => 'required|numeric|min:1',
            'packet_number' => 'nullable|string|max:50',
            'locker_reference' => 'nullable|string|max:50',
        ]);

        $this->recalcWeights();

        $item = PledgedItem::create([
            'customer_id' => $this->customer_id,
            'item_name' => $this->item_name,
            'category' => $this->category,
            'gross_weight' => $this->gross_weight,
            'stone_weight' => $this->stone_weight,
            'net_gold_weight' => $this->net_gold_weight,
            'purity' => $this->purity,
            'gold_rate_per_gram' => $this->gold_rate_per_gram,
            'valuation_amount' => $this->valuation_amount,
            'packet_number' => $this->packet_number,
            'locker_reference' => $this->locker_reference,
            'status' => 'Pledged',
            'remarks' => $this->remarks,
        ]);

        $this->showCreateModal = false;
        session()->flash('success', "Jewellery item {$item->item_name} added to vault!");
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $item = PledgedItem::findOrFail($id);
        $this->pledgeId = $item->id;
        $this->customer_id = $item->customer_id;
        $this->item_name = $item->item_name;
        $this->category = $item->category;
        $this->gross_weight = $item->gross_weight;
        $this->stone_weight = $item->stone_weight;
        $this->net_gold_weight = $item->net_gold_weight;
        $this->purity = $item->purity;
        $this->gold_rate_per_gram = $item->gold_rate_per_gram;
        $this->valuation_amount = $item->valuation_amount;
        $this->packet_number = $item->packet_number;
        $this->locker_reference = $item->locker_reference;
        $this->status = $item->status;
        $this->remarks = $item->remarks;

        $this->showEditModal = true;
    }

    public function updateItem()
    {
        $this->validate([
            'item_name' => 'required|string|max:150',
            'category' => 'required|string',
            'gross_weight' => 'required|numeric|min:0.001',
            'stone_weight' => 'required|numeric|min:0',
            'purity' => 'required|in:18K,20K,22K,24K',
            'gold_rate_per_gram' => 'required|numeric|min:1',
            'packet_number' => 'nullable|string|max:50',
            'locker_reference' => 'nullable|string|max:50',
        ]);

        $this->recalcWeights();

        $item = PledgedItem::findOrFail($this->pledgeId);
        $item->update([
            'item_name' => $this->item_name,
            'category' => $this->category,
            'gross_weight' => $this->gross_weight,
            'stone_weight' => $this->stone_weight,
            'net_gold_weight' => $this->net_gold_weight,
            'purity' => $this->purity,
            'gold_rate_per_gram' => $this->gold_rate_per_gram,
            'valuation_amount' => $this->valuation_amount,
            'packet_number' => $this->packet_number,
            'locker_reference' => $this->locker_reference,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ]);

        $this->showEditModal = false;
        session()->flash('success', "Jewellery item updated.");
    }

    public function printTag($id)
    {
        $this->selectedPledge = PledgedItem::with(['customer', 'loan'])->findOrFail($id);
        $this->showTagModal = true;
    }

    public function render()
    {
        $query = PledgedItem::with(['customer', 'loan']);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('item_name', 'like', "%{$this->search}%")
                  ->orWhere('packet_number', 'like', "%{$this->search}%")
                  ->orWhere('locker_reference', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function($cq) {
                      $cq->where('name', 'like', "%{$this->search}%")
                         ->orWhere('mobile', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('loan', function($lq) {
                      $lq->where('loan_no', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->purityFilter !== 'all') {
            $query->where('purity', $this->purityFilter);
        }

        $items = $query->orderBy('id', 'desc')->paginate(15);
        $customers = Customer::where('status', 'active')->orderBy('name')->get();

        $totalNetGrams = (float) PledgedItem::where('status', 'Pledged')->sum('net_gold_weight');
        $totalValuation = (float) PledgedItem::where('status', 'Pledged')->sum('valuation_amount');

        $data = array_merge(get_object_vars($this), [
            'items' => $items,
            'customers' => $customers,
            'totalNetGrams' => $totalNetGrams,
            'totalValuation' => $totalValuation,
        ]);

        return view('livewire.pledges', $data)->layout('layouts.app', ['title' => 'Gold Pledge Management']);
    }
}
