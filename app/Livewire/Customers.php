<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\FinancialLog;
use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';

    public $showCreateModal = false;
    public $showEditModal = false;
    public $showViewModal = false;
    public $showDeleteModal = false;

    public $customerId;
    public $customer_code;
    public $name = '';
    public $mobile = '';
    public $secondary_phone = '';
    public $email = '';
    public $address = '';
    public $city = 'Mumbai';
    public $state = 'Maharashtra';
    public $pincode = '400001';
    public $pan_number = '';
    public $aadhaar_number = '';
    public $id_proof_type = 'Aadhaar';
    public $id_proof_number = '';
    public $photo_path;
    public $notes = '';
    public $status = 'active';

    public $selectedCustomer = null;

    protected $queryString = ['search', 'statusFilter'];

    public function mount()
    {
        $this->customer_code = Customer::generateCode();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset([
            'customerId', 'name', 'mobile', 'secondary_phone', 'email',
            'address', 'pan_number', 'aadhaar_number', 'id_proof_number',
            'photo_path', 'notes'
        ]);
        $this->city = 'Mumbai';
        $this->state = 'Maharashtra';
        $this->pincode = '400001';
        $this->id_proof_type = 'Aadhaar';
        $this->status = 'active';
        $this->customer_code = Customer::generateCode();
        $this->showCreateModal = true;
    }

    public function createCustomer()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'mobile' => 'required|string|max:15',
            'address' => 'required|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        $code = Customer::generateCode();
        $customer = Customer::create([
            'customer_code' => $code,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'secondary_phone' => $this->secondary_phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'pan_number' => $this->pan_number ? strtoupper($this->pan_number) : null,
            'aadhaar_number' => $this->aadhaar_number,
            'id_proof_type' => $this->id_proof_type,
            'id_proof_number' => $this->id_proof_number,
            'notes' => $this->notes,
            'status' => $this->status,
        ]);

        FinancialLog::record(
            'customer_created',
            "New customer registered: {$customer->name} ({$customer->customer_code})",
            0,
            null,
            $customer->id,
            $customer->customer_code
        );

        $this->showCreateModal = false;
        session()->flash('success', "Customer {$customer->name} registered successfully!");
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->customer_code = $customer->customer_code;
        $this->name = $customer->name;
        $this->mobile = $customer->mobile;
        $this->secondary_phone = $customer->secondary_phone;
        $this->email = $customer->email;
        $this->address = $customer->address;
        $this->city = $customer->city;
        $this->state = $customer->state;
        $this->pincode = $customer->pincode;
        $this->pan_number = $customer->pan_number;
        $this->aadhaar_number = $customer->aadhaar_number;
        $this->id_proof_type = $customer->id_proof_type ?? 'Aadhaar';
        $this->id_proof_number = $customer->id_proof_number;
        $this->photo_path = $customer->photo_path;
        $this->notes = $customer->notes;
        $this->status = $customer->status;

        $this->showEditModal = true;
    }

    public function updateCustomer()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'mobile' => 'required|string|max:15',
            'address' => 'required|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        $customer = Customer::findOrFail($this->customerId);
        $customer->update([
            'name' => $this->name,
            'mobile' => $this->mobile,
            'secondary_phone' => $this->secondary_phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'pan_number' => $this->pan_number ? strtoupper($this->pan_number) : null,
            'aadhaar_number' => $this->aadhaar_number,
            'id_proof_type' => $this->id_proof_type,
            'id_proof_number' => $this->id_proof_number,
            'notes' => $this->notes,
            'status' => $this->status,
        ]);

        $this->showEditModal = false;
        session()->flash('success', "Customer profile updated successfully!");
    }

    public function viewProfile($id)
    {
        $this->selectedCustomer = Customer::with(['loans.pledgedItems', 'payments'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function confirmDelete($id)
    {
        $this->selectedCustomer = Customer::withCount(['loans' => function($q) {
            $q->whereIn('status', ['Active', 'Overdue']);
        }])->findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function deleteCustomer()
    {
        if (!$this->selectedCustomer) return;

        if ($this->selectedCustomer->loans()->whereIn('status', ['Active', 'Overdue'])->exists()) {
            session()->flash('error', "Cannot delete customer with active or overdue loans!");
            $this->showDeleteModal = false;
            return;
        }

        $this->selectedCustomer->delete();
        $this->showDeleteModal = false;
        session()->flash('success', "Customer record archived successfully.");
    }

    public function render()
    {
        $query = Customer::query()->withCount('loans');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('mobile', 'like', "%{$this->search}%")
                  ->orWhere('customer_code', 'like', "%{$this->search}%")
                  ->orWhere('pan_number', 'like', "%{$this->search}%")
                  ->orWhere('aadhaar_number', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $customers = $query->orderBy('id', 'desc')->paginate(12);

        return view('livewire.customers', array_merge(get_object_vars($this), [
            'customers' => $customers,
        ]))->layout('layouts.app', ['title' => 'Customer Management']);
    }
}
