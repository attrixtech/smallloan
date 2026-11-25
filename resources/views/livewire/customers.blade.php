<div class="space-y-4">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-lg font-bold text-slate-800 tracking-tight">Customer Directory</h1>
            <p class="text-xs text-slate-500">Manage borrower profiles, contact records, KYC identification, and loan histories.</p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="btn-primary">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Add New Customer
            </button>
        </div>
    </div>

    <!-- Filter and Search Row -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-2 flex-1 min-w-[280px]">
            <div class="relative w-full">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, phone, code, Aadhaar or PAN..." class="input-compact pl-8">
                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            @if($search)
                <button wire:click="$set('search', '')" class="text-xs text-slate-400 hover:text-slate-600 px-2">Clear</button>
            @endif
        </div>

        <div class="flex items-center space-x-2">
            <span class="text-xs text-slate-500">Status:</span>
            <select wire:model.live="statusFilter" class="input-compact !w-auto">
                <option value="all">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Customer Table -->
    <div class="bg-white rounded border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-compact">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>City / Address</th>
                        <th>KYC Details</th>
                        <th>Total Active Balance</th>
                        <th>Gold Pledged</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td class="font-bold text-amber-700">{{ $c->customer_code }}</td>
                            <td>
                                <div class="font-semibold text-slate-900">{{ $c->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $c->loans_count }} total loans</div>
                            </td>
                            <td>
                                <div class="text-slate-800 font-medium">{{ $c->mobile }}</div>
                                @if($c->secondary_phone)
                                    <div class="text-[10px] text-slate-400">Alt: {{ $c->secondary_phone }}</div>
                                @endif
                            </td>
                            <td class="max-w-[180px] truncate" title="{{ $c->address }}">
                                <span class="font-medium text-slate-700">{{ $c->city ?? 'Mumbai' }}</span>
                                <div class="text-[10px] text-slate-400 truncate">{{ $c->address }}</div>
                            </td>
                            <td>
                                @if($c->pan_number)
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 border border-slate-200 font-mono">PAN: {{ $c->pan_number }}</span>
                                @elseif($c->aadhaar_number)
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 border border-slate-200 font-mono">UID: {{ $c->aadhaar_number }}</span>
                                @else
                                    <span class="text-slate-400 text-[10px]">None</span>
                                @endif
                            </td>
                            <td class="font-bold text-slate-800">
                                ₹{{ number_format($c->total_outstanding, 2) }}
                            </td>
                            <td class="font-medium text-amber-700">
                                {{ number_format($c->total_gold_pledged_grams, 3) }} g
                            </td>
                            <td>
                                <span class="badge {{ $c->status === 'active' ? 'badge-active' : 'badge-closed' }}">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                            <td class="text-right space-x-1">
                                <button wire:click="viewProfile({{ $c->id }})" class="btn-secondary !py-1 !px-2 text-[11px]" title="View Profile & Ledger">
                                    Profile
                                </button>
                                <button wire:click="openEditModal({{ $c->id }})" class="btn-secondary !py-1 !px-2 text-[11px]" title="Edit Customer">
                                    Edit
                                </button>
                                <button wire:click="confirmDelete({{ $c->id }})" class="text-rose-600 hover:text-rose-800 text-[11px] px-1.5 py-1">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-6 text-slate-400 text-xs">
                                No customer records found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-200 bg-slate-50/50">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- Add Customer Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-lg shadow-xl border border-slate-200 w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-xs">Register New Customer (Code: {{ $customer_code }})</h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>
                <div class="p-4 overflow-y-auto space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Full Name *</label>
                            <input type="text" wire:model="name" class="input-compact" placeholder="e.g. Ramesh Chandra Verma">
                            @error('name') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Primary Mobile *</label>
                            <input type="text" wire:model="mobile" class="input-compact" placeholder="10-digit mobile number">
                            @error('mobile') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Secondary Phone</label>
                            <input type="text" wire:model="secondary_phone" class="input-compact" placeholder="Alternative phone">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Email Address</label>
                            <input type="email" wire:model="email" class="input-compact" placeholder="customer@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Residential Address *</label>
                        <textarea wire:model="address" rows="2" class="input-compact" placeholder="House/Flat No, Building, Street, Area"></textarea>
                        @error('address') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">City</label>
                            <input type="text" wire:model="city" class="input-compact">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">State</label>
                            <input type="text" wire:model="state" class="input-compact">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Pincode</label>
                            <input type="text" wire:model="pincode" class="input-compact">
                        </div>
                    </div>

                    <!-- KYC Details -->
                    <div class="p-3 bg-slate-50 rounded border border-slate-200">
                        <div class="text-[11px] font-bold text-slate-700 uppercase mb-2">KYC & Identity Verification</div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">PAN Card Number</label>
                                <input type="text" wire:model="pan_number" class="input-compact uppercase" placeholder="ABCDE1234F">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Aadhaar Number</label>
                                <input type="text" wire:model="aadhaar_number" class="input-compact" placeholder="12-digit Aadhaar">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Other ID Proof Type</label>
                                <select wire:model="id_proof_type" class="input-compact">
                                    <option value="Aadhaar">Aadhaar Card</option>
                                    <option value="PAN">PAN Card</option>
                                    <option value="Voter ID">Voter ID Card</option>
                                    <option value="Driving License">Driving License</option>
                                    <option value="Passport">Passport</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Notes & References</label>
                        <input type="text" wire:model="notes" class="input-compact" placeholder="Guarantor, reference or special notes">
                    </div>
                </div>
                <div class="px-4 py-2.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end space-x-2">
                    <button wire:click="$set('showCreateModal', false)" class="btn-secondary">Cancel</button>
                    <button wire:click="createCustomer" class="btn-primary">Save Customer</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Customer Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-lg shadow-xl border border-slate-200 w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-xs">Edit Customer: {{ $customer_code }}</h3>
                    <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>
                <div class="p-4 overflow-y-auto space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Full Name *</label>
                            <input type="text" wire:model="name" class="input-compact">
                            @error('name') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Primary Mobile *</label>
                            <input type="text" wire:model="mobile" class="input-compact">
                            @error('mobile') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Secondary Phone</label>
                            <input type="text" wire:model="secondary_phone" class="input-compact">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Email Address</label>
                            <input type="email" wire:model="email" class="input-compact">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Address *</label>
                        <textarea wire:model="address" rows="2" class="input-compact"></textarea>
                        @error('address') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">City</label>
                            <input type="text" wire:model="city" class="input-compact">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">State</label>
                            <input type="text" wire:model="state" class="input-compact">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Status</label>
                            <select wire:model="status" class="input-compact">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 rounded border border-slate-200">
                        <div class="text-[11px] font-bold text-slate-700 uppercase mb-2">KYC Details</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">PAN Number</label>
                                <input type="text" wire:model="pan_number" class="input-compact uppercase">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Aadhaar Number</label>
                                <input type="text" wire:model="aadhaar_number" class="input-compact">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-2.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end space-x-2">
                    <button wire:click="$set('showEditModal', false)" class="btn-secondary">Cancel</button>
                    <button wire:click="updateCustomer" class="btn-primary">Update Profile</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Customer Profile View Modal / Drawer -->
    @if($showViewModal && $selectedCustomer)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-lg shadow-xl border border-slate-200 w-full max-w-4xl overflow-hidden max-h-[92vh] flex flex-col">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-xs">{{ $selectedCustomer->name }} ({{ $selectedCustomer->customer_code }})</h3>
                        <span class="text-[10px] text-slate-500">Customer Profile & Complete Financial Ledger</span>
                    </div>
                    <button wire:click="$set('showViewModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <div class="p-4 overflow-y-auto space-y-4">
                    <!-- Customer Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="p-3 rounded bg-slate-50 border border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold">Total Outstanding</span>
                            <div class="text-base font-bold text-slate-900 mt-0.5">₹{{ number_format($selectedCustomer->total_outstanding, 2) }}</div>
                        </div>
                        <div class="p-3 rounded bg-slate-50 border border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold">Gold In Vault</span>
                            <div class="text-base font-bold text-amber-700 mt-0.5">{{ number_format($selectedCustomer->total_gold_pledged_grams, 3) }} g</div>
                        </div>
                        <div class="p-3 rounded bg-slate-50 border border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold">Contact</span>
                            <div class="text-xs font-semibold text-slate-800 mt-0.5">{{ $selectedCustomer->mobile }}</div>
                            <div class="text-[10px] text-slate-400">{{ $selectedCustomer->email ?? 'No email' }}</div>
                        </div>
                        <div class="p-3 rounded bg-slate-50 border border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold">KYC Verification</span>
                            <div class="text-xs text-slate-800 mt-0.5">PAN: {{ $selectedCustomer->pan_number ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-500">UID: {{ $selectedCustomer->aadhaar_number ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <!-- Customer Loans History -->
                    <div>
                        <div class="text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                            <span>Pledge Contracts & Loan History ({{ $selectedCustomer->loans->count() }})</span>
                        </div>
                        <div class="overflow-x-auto rounded border border-slate-200">
                            <table class="w-full table-compact">
                                <thead>
                                    <tr>
                                        <th>Loan No</th>
                                        <th>Date</th>
                                        <th>Principal</th>
                                        <th>Rate</th>
                                        <th>Balance</th>
                                        <th>Maturity</th>
                                        <th>Status</th>
                                        <th>Items</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedCustomer->loans as $l)
                                        <tr>
                                            <td class="font-bold text-amber-700">{{ $l->loan_no }}</td>
                                            <td>{{ $l->issue_date ? $l->issue_date->format('d/m/Y') : '-' }}</td>
                                            <td>₹{{ number_format($l->principal_amount, 2) }}</td>
                                            <td>{{ $l->interest_rate }}% pm</td>
                                            <td class="font-bold text-slate-900">₹{{ number_format($l->current_balance_principal, 2) }}</td>
                                            <td>{{ $l->maturity_date ? $l->maturity_date->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                <span class="badge {{ $l->status === 'Active' ? 'badge-active' : ($l->status === 'Overdue' ? 'badge-overdue' : 'badge-closed') }}">
                                                    {{ $l->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-[11px] text-slate-600">{{ $l->pledgedItems->count() }} items ({{ $l->total_net_weight }}g)</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-xs text-slate-400">No loan records found for this customer.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Customer Payments History -->
                    <div>
                        <div class="text-xs font-bold text-slate-800 mb-1">Payment Receipts History ({{ $selectedCustomer->payments->count() }})</div>
                        <div class="overflow-x-auto rounded border border-slate-200">
                            <table class="w-full table-compact">
                                <thead>
                                    <tr>
                                        <th>Receipt No</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Mode</th>
                                        <th>Interest Paid</th>
                                        <th>Principal Paid</th>
                                        <th>Total Paid</th>
                                        <th>Balance After</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedCustomer->payments as $p)
                                        <tr>
                                            <td class="font-medium text-slate-800">{{ $p->payment_no }}</td>
                                            <td>{{ $p->payment_date ? $p->payment_date->format('d/m/Y') : '-' }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $p->payment_type)) }}</td>
                                            <td class="uppercase text-[10px]">{{ $p->payment_mode }}</td>
                                            <td class="text-emerald-700">₹{{ number_format($p->interest_amount, 2) }}</td>
                                            <td class="text-blue-700">₹{{ number_format($p->principal_amount, 2) }}</td>
                                            <td class="font-bold text-slate-900">₹{{ number_format($p->total_amount_paid, 2) }}</td>
                                            <td>₹{{ number_format($p->principal_balance_after, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-xs text-slate-400">No payment receipts on record.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-2.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end">
                    <button wire:click="$set('showViewModal', false)" class="btn-secondary">Close</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-lg shadow-xl border border-slate-200 w-full max-w-sm p-4 space-y-3">
                <h3 class="font-bold text-slate-800 text-xs">Confirm Delete</h3>
                <p class="text-xs text-slate-600">
                    Are you sure you want to delete customer <strong>{{ $selectedCustomer->name ?? '' }}</strong>?
                    This will archive their record.
                </p>
                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">Cancel</button>
                    <button wire:click="deleteCustomer" class="btn-danger">Yes, Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
