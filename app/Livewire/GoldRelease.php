<?php

namespace App\Livewire;

use App\Models\FinancialLog;
use App\Models\GoldRelease as GoldReleaseModel;
use App\Models\Loan;
use App\Models\PledgedItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class GoldRelease extends Component
{
    use WithPagination;

    public $search = '';

    public $showCreateModal = false;
    public $showPrintModal = false;

    // Release form
    public $loan_id;
    public $release_no;
    public $release_date;
    public $handed_over_to = '';
    public $recipient_relation = 'self';
    public $recipient_id_proof = '';
    public $recipient_signature_note = '';
    public $is_authorized_override = false;
    public $override_reason = '';

    // Selected loan preview
    public $eligibleLoans = [];
    public $selectedLoan = null;

    // Selected release for print
    public $selectedRelease = null;

    protected $queryString = ['search'];

    public function mount()
    {
        $this->release_date = date('Y-m-d');
        $this->release_no = GoldReleaseModel::generateReleaseNo();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->release_no = GoldReleaseModel::generateReleaseNo();
        $this->release_date = date('Y-m-d');
        $this->loan_id = '';
        $this->handed_over_to = '';
        $this->recipient_relation = 'self';
        $this->recipient_id_proof = '';
        $this->recipient_signature_note = '';
        $this->is_authorized_override = false;
        $this->override_reason = '';
        $this->selectedLoan = null;

        $this->eligibleLoans = Loan::with('customer')
            ->whereHas('pledgedItems', function($q) {
                $q->where('status', 'Pledged');
            })
            ->orderBy('id', 'desc')
            ->get();

        $this->showCreateModal = true;
    }

    public function updatedLoanId()
    {
        if (empty($this->loan_id)) {
            $this->selectedLoan = null;
            return;
        }

        $loan = Loan::with(['customer', 'pledgedItems' => function($q) {
            $q->where('status', 'Pledged');
        }])->find($this->loan_id);

        if ($loan) {
            $this->selectedLoan = $loan;
            $this->handed_over_to = $loan->customer->name ?? '';
            $this->recipient_id_proof = ($loan->customer->id_proof_type ?? 'Aadhaar') . ': ' . ($loan->customer->id_proof_number ?? $loan->customer->aadhaar_number ?? '');
        }
    }

    public function processRelease()
    {
        $this->validate([
            'loan_id' => 'required|exists:loans,id',
            'release_date' => 'required|date',
            'handed_over_to' => 'required|string|max:100',
            'recipient_relation' => 'required|string',
            'recipient_id_proof' => 'nullable|string|max:100',
        ]);

        $loan = Loan::with('pledgedItems')->findOrFail($this->loan_id);

        $isSettled = ($loan->status === 'Closed' || floatval($loan->current_balance_principal) <= 0);

        if (!$isSettled && !$this->is_authorized_override) {
            $this->addError('is_authorized_override', 'ACCIDENTAL RELEASE PREVENTED: This loan has an outstanding balance of ₹' . number_format($loan->current_balance_principal, 2) . '. You must authorize an override to proceed.');
            return;
        }

        if (!$isSettled && empty(trim($this->override_reason))) {
            $this->addError('override_reason', 'Please provide a valid authorized override reason for releasing gold on an unsettled loan.');
            return;
        }

        $releaseNo = GoldReleaseModel::generateReleaseNo();
        $itemsToRelease = $loan->pledgedItems()->where('status', 'Pledged')->get();

        if ($itemsToRelease->isEmpty()) {
            $this->addError('loan_id', 'All pledged items for this loan have already been released.');
            return;
        }

        $totalNetWeight = $itemsToRelease->sum('net_gold_weight');

        DB::transaction(function () use ($loan, $releaseNo, $itemsToRelease, $totalNetWeight, $isSettled) {
            $release = GoldReleaseModel::create([
                'release_no' => $releaseNo,
                'loan_id' => $loan->id,
                'customer_id' => $loan->customer_id,
                'release_date' => $this->release_date,
                'items_count' => $itemsToRelease->count(),
                'total_net_weight' => $totalNetWeight,
                'handed_over_to' => $this->handed_over_to,
                'recipient_relation' => $this->recipient_relation,
                'recipient_id_proof' => $this->recipient_id_proof,
                'recipient_signature_note' => $this->recipient_signature_note,
                'is_authorized_override' => !$isSettled,
                'override_reason' => !$isSettled ? $this->override_reason : null,
                'authorized_by' => !$isSettled ? auth()->id() : null,
                'processed_by' => auth()->id(),
            ]);

            foreach ($itemsToRelease as $item) {
                $item->update([
                    'status' => 'Released',
                    'released_at' => now(),
                    'release_receipt_no' => $releaseNo,
                ]);
            }

            FinancialLog::record(
                'gold_released',
                "Gold jewellery released: {$itemsToRelease->count()} items ({$totalNetWeight}g) for Loan {$loan->loan_no} to {$this->handed_over_to}",
                0,
                $loan->id,
                $loan->customer_id,
                $releaseNo,
                [
                    'override' => !$isSettled,
                    'override_reason' => $this->override_reason,
                    'recipient' => $this->handed_over_to,
                ]
            );

            $this->selectedRelease = $release->load(['loan.pledgedItems', 'customer', 'processor']);
        });

        $this->showCreateModal = false;
        $this->showPrintModal = true;
        session()->flash('success', "Gold Release Certificate {$releaseNo} processed successfully!");
    }

    public function printRelease($id)
    {
        $this->selectedRelease = GoldReleaseModel::with(['loan.pledgedItems', 'customer', 'processor'])->findOrFail($id);
        $this->showPrintModal = true;
    }

    public function render()
    {
        $query = GoldReleaseModel::with(['loan', 'customer', 'processor']);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('release_no', 'like', "%{$this->search}%")
                  ->orWhere('handed_over_to', 'like', "%{$this->search}%")
                  ->orWhereHas('loan', function($lq) {
                      $lq->where('loan_no', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('customer', function($cq) {
                      $cq->where('name', 'like', "%{$this->search}%")
                         ->orWhere('mobile', 'like', "%{$this->search}%");
                  });
            });
        }

        $releases = $query->orderBy('id', 'desc')->paginate(12);

        return view('livewire.gold-release', array_merge(get_object_vars($this), [
            'releases' => $releases,
        ]))->layout('layouts.app', ['title' => 'Gold Release & Handover']);
    }
}
