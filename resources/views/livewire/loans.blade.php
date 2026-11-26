<div class="space-y-4">
    <!-- Top Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-slate-200">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Gold Loan Portfolio</h2>
            <p class="text-xs text-slate-500">Originate gold loans, calculate interest accural, manage collateral LTV and print pledge agreements</p>
        </div>
        <div class="flex items-center space-x-2">
            <button wire:click="openCreateModal" class="btn-primary">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Disburse New Loan
            </button>
        </div>
    </div>

    <!-- Search & Filters Toolbar -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-2xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center space-x-2 flex-1 min-w-[240px]">
            <div class="relative w-full max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search loan no, customer name, mobile..."
                       class="input-compact pl-8">
            </div>
            @if($search)
                <button wire:click="$set('search', '')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>

        <div class="flex items-center space-x-2">
            <label class="text-slate-500 font-medium text-xs">Status:</label>
            <select wire:model.live="statusFilter" class="input-compact w-28">
                <option value="all">All Loans</option>
                <option value="Active">Active</option>
                <option value="Overdue">Overdue</option>
                <option value="Closed">Closed</option>
            </select>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-compact">
                <thead>
                    <tr>
                        <th>Loan No</th>
                        <th>Borrower</th>
                        <th>Principal</th>
                        <th>Balance</th>
                        <th>Rate / Method</th>
                        <th>Collateral / Net Wt</th>
                        <th>Valuation / LTV</th>
                        <th>Issue / Due Date</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td class="font-semibold text-amber-700 font-mono">
                                <a href="javascript:void(0)" wire:click="viewLoan({{ $loan->id }})" class="hover:underline">
                                    {{ $loan->loan_no }}
                                </a>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-900">{{ $loan->customer->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $loan->customer->mobile ?? '' }}</div>
                            </td>
                            <td class="font-semibold text-slate-800">
                                ₹{{ number_format($loan->principal_amount, 2) }}
                            </td>
                            <td class="font-bold text-slate-900">
                                ₹{{ number_format($loan->current_balance_principal, 2) }}
                            </td>
                            <td>
                                <div class="text-slate-700 font-medium">{{ $loan->interest_rate }}% pm</div>
                                <div class="text-[10px] text-slate-400 capitalize">{{ str_replace('_', ' ', $loan->interest_calculation_method) }}</div>
                            </td>
                            <td>
                                <div class="font-semibold text-amber-800">{{ number_format($loan->total_net_weight, 3) }} g</div>
                                <div class="text-[10px] text-slate-400">{{ $loan->total_pledged_items }} item(s)</div>
                            </td>
                            <td>
                                <div class="text-slate-800 font-medium">₹{{ number_format($loan->total_valuation_amount, 2) }}</div>
                                <div class="text-[10px] text-slate-500 font-semibold">{{ $loan->ltv_ratio }}% LTV</div>
                            </td>
                            <td>
                                <div class="text-slate-700 text-[11px]">{{ $loan->issue_date->format('d/m/Y') }}</div>
                                <div class="text-[10px] {{ $loan->is_overdue ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                    Due: {{ $loan->maturity_date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                @if($loan->status === 'Active')
                                    <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                                @elseif($loan->status === 'Overdue')
                                    <span class="badge bg-rose-50 text-rose-700 border border-rose-200">Overdue</span>
                                @else
                                    <span class="badge bg-slate-100 text-slate-600 border border-slate-200">Closed</span>
                                @endif
                            </td>
                            <td class="text-right space-x-1">
                                <button wire:click="viewLoan({{ $loan->id }})" class="px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded text-[11px] font-medium transition cursor-pointer" title="View details and live calculation">
                                    View
                                </button>
                                <button wire:click="printReceipt({{ $loan->id }})" class="px-2 py-1 bg-slate-50 hover:bg-slate-100 text-amber-700 border border-slate-200 rounded text-[11px] font-medium transition cursor-pointer" title="Print Pledge Receipt">
                                    Receipt
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-6 text-slate-400 text-xs">
                                No loan records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $loans->links() }}
        </div>
    </div>

    <!-- Create Loan Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-4xl overflow-hidden my-6">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Originate New Gold Loan ({{ $loan_no }})</h3>
                        <p class="text-[11px] text-slate-500">Record borrower, jewellery pledge items, terms and interest parameters</p>
                    </div>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form wire:submit.prevent="createLoan" class="p-4 space-y-4 max-h-[80vh] overflow-y-auto">
                    <!-- Section 1: Customer & Loan Principal -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="md:col-span-1">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700">Select Customer / Borrower <span class="text-rose-500">*</span></label>
                                <button type="button" wire:click="$toggle('showQuickCustomerForm')" class="text-[11px] font-semibold text-amber-700 hover:text-amber-900 cursor-pointer">
                                    {{ $showQuickCustomerForm ? '× Close' : '+ New Customer' }}
                                </button>
                            </div>
                            <select wire:model="customer_id" class="input-compact">
                                <option value="">-- Choose Customer --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->mobile }})</option>
                                @endforeach
                            </select>
                            @error('customer_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Principal Amount (₹) <span class="text-rose-500">*</span></label>
                            <input type="number" step="100" wire:model.live="principal_amount" class="input-compact font-bold text-slate-900" placeholder="e.g. 50000">
                            @error('principal_amount') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Interest Rate (% pm) <span class="text-rose-500">*</span></label>
                            <div class="flex items-center space-x-1">
                                <input type="number" step="0.01" wire:model="interest_rate" class="input-compact font-bold w-24">
                                <select wire:model="interest_rate_type" class="input-compact flex-1">
                                    <option value="per_month">% Per Month</option>
                                    <option value="per_annum">% Per Annum</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @if($showQuickCustomerForm)
                        <div class="bg-slate-50 border border-amber-300 rounded p-3 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-amber-900 uppercase tracking-wider">Quick Register New Customer</span>
                                <button type="button" wire:click="$set('showQuickCustomerForm', false)" class="text-slate-400 hover:text-slate-600 text-xs">Cancel</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Full Name <span class="text-rose-500">*</span></label>
                                    <input type="text" wire:model="quick_customer_name" class="input-compact" placeholder="e.g. Rajesh Kumar Sharma">
                                    @error('quick_customer_name') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Mobile <span class="text-rose-500">*</span></label>
                                    <input type="text" wire:model="quick_customer_mobile" class="input-compact" placeholder="98XXXXXXXX">
                                    @error('quick_customer_mobile') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Address <span class="text-rose-500">*</span></label>
                                    <input type="text" wire:model="quick_customer_address" class="input-compact" placeholder="House/Flat No, Street, City">
                                    @error('quick_customer_address') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-end">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Aadhaar (Optional)</label>
                                    <input type="text" wire:model="quick_customer_aadhaar" class="input-compact font-mono" placeholder="XXXX-XXXX-XXXX">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">PAN (Optional)</label>
                                    <input type="text" wire:model="quick_customer_pan" class="input-compact uppercase font-mono" placeholder="ABCDE1234F">
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" wire:click="createQuickCustomer" class="btn-primary !py-1.5 text-xs">
                                        Save &amp; Select Customer
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Section 2: Calculation Method & Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Interest Calculation Method <span class="text-rose-500">*</span></label>
                            <select wire:model="interest_calculation_method" class="input-compact font-medium">
                                <option value="monthly">Monthly Interest</option>
                                <option value="daily">Daily Interest</option>
                                <option value="reducing_balance">Reducing Balance</option>
                                <option value="flat">Flat Interest</option>
                                <option value="annual">Annual Interest</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Disbursement Date</label>
                            <input type="date" wire:model.live="issue_date" class="input-compact">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tenure (Months)</label>
                            <input type="number" wire:model.live="tenure_months" class="input-compact">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Maturity / Due Date</label>
                            <input type="date" wire:model="maturity_date" class="input-compact bg-slate-50 font-medium">
                        </div>
                    </div>

                    <!-- Section 3: Pledged Gold Jewellery Items Dynamic Form -->
                    <div class="bg-amber-50/40 rounded border border-amber-200/70 p-3 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-amber-950 uppercase tracking-wider block">Pledged Jewellery Articles (Collateral)</span>
                                <span class="text-[11px] text-slate-500">Add ornaments, specify weights and calculate valuation</span>
                            </div>
                            <button type="button" wire:click="addItemRow" class="btn-secondary !py-1 text-xs">
                                + Add Another Item
                            </button>
                        </div>

                        <div class="space-y-3">
                            @foreach($items as $index => $item)
                                <div class="bg-white p-2.5 rounded border border-slate-200 space-y-2" wire:key="item-row-{{ $index }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-slate-700">Item #{{ $index + 1 }}</span>
                                        @if(count($items) > 1)
                                            <button type="button" wire:click="removeItemRow({{ $index }})" class="text-rose-600 hover:text-rose-800 text-[11px]">
                                                &times; Remove
                                            </button>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                        <div class="md:col-span-2">
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Description <span class="text-rose-500">*</span></label>
                                            <input type="text" wire:model="items.{{ $index }}.item_name" class="input-compact" placeholder="e.g. Gold Necklace with Ruby stones">
                                            @error("items.{$index}.item_name") <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Category</label>
                                            <select wire:model="items.{{ $index }}.category" class="input-compact">
                                                <option value="Ring">Ring</option>
                                                <option value="Chain">Chain</option>
                                                <option value="Bangle">Bangle</option>
                                                <option value="Necklace">Necklace</option>
                                                <option value="Earring">Earring</option>
                                                <option value="Mangalsutra">Mangalsutra</option>
                                                <option value="Coin">Coin</option>
                                                <option value="Bracelet">Bracelet</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Purity</label>
                                            <select wire:model="items.{{ $index }}.purity" wire:change="recalculateItemRow({{ $index }})" class="input-compact font-bold">
                                                <option value="24K">24K (99.9%)</option>
                                                <option value="22K">22K (91.6%)</option>
                                                <option value="20K">20K (83.3%)</option>
                                                <option value="18K">18K (75.0%)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 pt-1 border-t border-slate-100">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Gross Wt (g) <span class="text-rose-500">*</span></label>
                                            <input type="number" step="0.001" wire:model.live.debounce.300ms="items.{{ $index }}.gross_weight" wire:change="recalculateItemRow({{ $index }})" class="input-compact font-bold" placeholder="0.000">
                                            @error("items.{$index}.gross_weight") <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Stone Wt (g)</label>
                                            <input type="number" step="0.001" wire:model.live.debounce.300ms="items.{{ $index }}.stone_weight" wire:change="recalculateItemRow({{ $index }})" class="input-compact" placeholder="0.000">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Net Gold Wt (g)</label>
                                            <input type="text" wire:model="items.{{ $index }}.net_gold_weight" readonly class="input-compact bg-amber-50 font-bold text-amber-900">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Rate / g (₹)</label>
                                            <input type="number" step="1" wire:model.live.debounce.300ms="items.{{ $index }}.gold_rate_per_gram" wire:change="recalculateItemRow({{ $index }})" class="input-compact">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Valuation (₹)</label>
                                            <input type="text" wire:model="items.{{ $index }}.valuation_amount" readonly class="input-compact bg-emerald-50 font-bold text-emerald-800">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 pt-1">
                                        <div>
                                            <input type="text" wire:model="items.{{ $index }}.packet_number" class="input-compact font-mono text-[11px]" placeholder="Packet Ref (e.g. PKT-001)">
                                        </div>
                                        <div>
                                            <input type="text" wire:model="items.{{ $index }}.locker_reference" class="input-compact text-[11px]" placeholder="Storage Locker (e.g. Locker A - Tray 2)">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Total Valuation & LTV Summary Banner -->
                        <div class="bg-amber-100/60 p-2.5 rounded border border-amber-300/80 flex flex-wrap items-center justify-between text-xs">
                            <div class="space-x-4">
                                <span>Gross Wt: <strong>{{ number_format($this->totalGrossWeight, 3) }} g</strong></span>
                                <span>Net Pure Gold: <strong class="text-amber-900">{{ number_format($this->totalNetWeight, 3) }} g</strong></span>
                                <span>Total Valuation: <strong class="text-emerald-900">₹{{ number_format($this->totalValuation, 2) }}</strong></span>
                                <span>Est. Monthly EMI (P+I): <strong class="text-blue-800">₹{{ number_format($this->estimatedEmi['emi'] ?? 0, 2) }}</strong></span>
                            </div>
                            <div>
                                <span class="font-bold text-slate-800">Calculated LTV: </span>
                                <span class="font-bold {{ $this->calculatedLtv > 80 ? 'text-rose-700' : 'text-emerald-700' }}">
                                    {{ $this->calculatedLtv }}%
                                </span>
                                <span class="text-[10px] text-slate-500 ml-1">(RBI norm: up to 75-85%)</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Terms / Guarantor Reference</label>
                        <input type="text" wire:model="notes" class="input-compact" placeholder="Special terms, witness details, etc.">
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Disburse Loan & Generate Agreement</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- View Loan Details Modal -->
    @if($showViewModal && $selectedLoan)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-3xl overflow-hidden my-6">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span>Loan Statement: {{ $selectedLoan->loan_no }}</span>
                            @if($selectedLoan->status === 'Active')
                                <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                            @elseif($selectedLoan->status === 'Overdue')
                                <span class="badge bg-rose-50 text-rose-700 border border-rose-200">Overdue</span>
                            @else
                                <span class="badge bg-slate-100 text-slate-600 border border-slate-200">Closed</span>
                            @endif
                        </h3>
                    </div>
                    <button wire:click="$set('showViewModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-4 space-y-4 max-h-[75vh] overflow-y-auto text-xs">
                    <!-- Key Parameters Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-slate-50 p-2.5 rounded border border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Principal Balance</span>
                            <span class="text-base font-bold text-slate-900">₹{{ number_format($selectedLoan->current_balance_principal, 2) }}</span>
                            <span class="text-[10px] text-slate-400 block">Original: ₹{{ number_format($selectedLoan->principal_amount, 2) }}</span>
                        </div>
                        <div class="bg-slate-50 p-2.5 rounded border border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Interest Rate</span>
                            <span class="text-base font-bold text-slate-900">{{ $selectedLoan->interest_rate }}% pm</span>
                            <span class="text-[10px] text-slate-500 capitalize block">{{ str_replace('_', ' ', $selectedLoan->interest_calculation_method) }}</span>
                        </div>
                        <div class="bg-slate-50 p-2.5 rounded border border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Interest Collected</span>
                            <span class="text-base font-bold text-emerald-700">₹{{ number_format($selectedLoan->total_interest_paid, 2) }}</span>
                            <span class="text-[10px] text-slate-400 block">Total paid to date</span>
                        </div>
                        <div class="bg-slate-50 p-2.5 rounded border border-slate-200">
                            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Collateral (Net Wt)</span>
                            <span class="text-base font-bold text-amber-800">{{ number_format($selectedLoan->total_net_weight, 3) }} g</span>
                            <span class="text-[10px] text-slate-500 block">Valuation: ₹{{ number_format($selectedLoan->total_valuation_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Live Interest Accrual Breakdown (Section 6 Requirement) -->
                    @php
                        $calc = $selectedLoan->interest_calculation;
                    @endphp
                    <div class="bg-amber-500/10 border border-amber-500/30 rounded p-3 space-y-1.5">
                        <div class="flex items-center justify-between pb-1 border-b border-amber-500/20">
                            <span class="font-bold text-slate-900 uppercase text-[11px] tracking-wide">Live Interest Calculation & Settlement Breakdown</span>
                            <span class="text-[10px] text-slate-500 font-mono">Calculated as of Today ({{ now()->format('d/m/Y') }})</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                            <div>
                                <span class="text-slate-500">Days Elapsed:</span>
                                <strong class="ml-1 text-slate-800">{{ $calc['days'] }} days ({{ $calc['months'] }} mo)</strong>
                            </div>
                            <div>
                                <span class="text-slate-500">Gross Interest Accrued:</span>
                                <strong class="ml-1 text-slate-900">₹{{ number_format($calc['gross_interest_accrued'], 2) }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-500">Net Interest Due:</span>
                                <strong class="ml-1 text-amber-800 font-bold">₹{{ number_format($calc['net_interest_due'], 2) }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-500">Full Settlement Total:</span>
                                <strong class="ml-1 text-emerald-800 font-bold">₹{{ number_format($calc['total_settlement_amount'], 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Pledged Jewellery List -->
                    <div>
                        <span class="font-bold text-slate-800 text-xs block mb-1">Pledged Jewellery Articles In Custody</span>
                        <table class="w-full text-left table-compact border border-slate-200 rounded">
                            <thead>
                                <tr>
                                    <th>Packet #</th>
                                    <th>Item Description</th>
                                    <th>Purity</th>
                                    <th>Gross Wt</th>
                                    <th>Stone Wt</th>
                                    <th>Net Wt</th>
                                    <th>Valuation</th>
                                    <th>Locker</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedLoan->pledgedItems as $item)
                                    <tr>
                                        <td class="font-mono text-amber-800">{{ $item->packet_number }}</td>
                                        <td class="font-medium text-slate-900">{{ $item->item_name }}</td>
                                        <td>{{ $item->purity }}</td>
                                        <td>{{ number_format($item->gross_weight, 3) }} g</td>
                                        <td>{{ number_format($item->stone_weight, 3) }} g</td>
                                        <td class="font-bold text-slate-800">{{ number_format($item->net_gold_weight, 3) }} g</td>
                                        <td>₹{{ number_format($item->valuation_amount, 2) }}</td>
                                        <td class="font-mono">{{ $item->locker_reference }}</td>
                                        <td>
                                            @if($item->status === 'Pledged')
                                                <span class="badge bg-amber-50 text-amber-800 border border-amber-200">In Locker</span>
                                            @else
                                                <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">Released</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Repayments & Collections History -->
                    <div>
                        <span class="font-bold text-slate-800 text-xs block mb-1">Payment Receipts History</span>
                        <table class="w-full text-left table-compact border border-slate-200 rounded">
                            <thead>
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Interest Paid</th>
                                    <th>Principal Paid</th>
                                    <th>Total Received</th>
                                    <th>Balance Left</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($selectedLoan->payments as $payment)
                                    <tr>
                                        <td class="font-mono text-amber-800">{{ $payment->payment_no }}</td>
                                        <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                        <td class="capitalize">{{ str_replace('_', ' ', $payment->payment_type) }}</td>
                                        <td class="text-emerald-700 font-medium">₹{{ number_format($payment->interest_amount, 2) }}</td>
                                        <td class="text-blue-700 font-medium">₹{{ number_format($payment->principal_amount, 2) }}</td>
                                        <td class="font-bold text-slate-900">₹{{ number_format($payment->total_amount_paid, 2) }}</td>
                                        <td>₹{{ number_format($payment->principal_balance_after, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-2 text-slate-400">No repayment entries recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-4 py-3 border-t border-slate-200 flex items-center justify-between bg-slate-50">
                    <button wire:click="printReceipt({{ $selectedLoan->id }})" class="btn-secondary">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Agreement / Pledge Receipt
                    </button>
                    <button wire:click="$set('showViewModal', false)" class="btn-primary">Done</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Printable Pledge Receipt / Loan Agreement Modal -->
    @if($showReceiptModal && $selectedLoan)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-2xl overflow-hidden my-6">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50 no-print">
                    <span class="font-bold text-slate-800 text-xs">Print Gold Pledge Receipt / Agreement</span>
                    <button wire:click="$set('showReceiptModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Printable Area -->
                <div class="p-6 text-slate-900 space-y-4 print-receipt-container">
                    <!-- Shop Header -->
                    <div class="text-center pb-3 border-b-2 border-slate-900">
                        <h2 class="text-lg font-black tracking-tight text-slate-900 uppercase">
                            {{ \App\Models\BusinessSetting::get('shop_name', 'thrumble.smallloan') }}
                        </h2>
                        <p class="text-xs text-slate-600">
                            {{ \App\Models\BusinessSetting::get('shop_address', '123 Sarafa Bazaar, Mumbai 400001') }} &bull; Ph: {{ \App\Models\BusinessSetting::get('phone_number', '+91 98765 43210') }}
                        </p>
                        <div class="mt-1 text-xs font-bold uppercase tracking-wider text-amber-800">
                            PLEDGE AGREEMENT & GOLD LOAN RECEIPT
                        </div>
                    </div>

                    <!-- Loan & Customer Reference Info -->
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="space-y-0.5">
                            <div><span class="text-slate-500">Loan Number:</span> <strong class="font-mono text-sm text-amber-900">{{ $selectedLoan->loan_no }}</strong></div>
                            <div><span class="text-slate-500">Disbursement Date:</span> <strong>{{ $selectedLoan->issue_date->format('d/m/Y') }}</strong></div>
                            <div><span class="text-slate-500">Due / Maturity Date:</span> <strong>{{ $selectedLoan->maturity_date->format('d/m/Y') }}</strong></div>
                            <div><span class="text-slate-500">Interest Terms:</span> <strong>{{ $selectedLoan->interest_rate }}% pm ({{ $selectedLoan->interest_calculation_method }})</strong></div>
                        </div>
                        <div class="space-y-0.5 text-right">
                            <div><span class="text-slate-500">Customer:</span> <strong>{{ $selectedLoan->customer->name ?? '' }}</strong></div>
                            <div><span class="text-slate-500">Customer ID:</span> <span class="font-mono">{{ $selectedLoan->customer->customer_code ?? '' }}</span></div>
                            <div><span class="text-slate-500">Phone:</span> <strong>{{ $selectedLoan->customer->mobile ?? '' }}</strong></div>
                            <div><span class="text-slate-500">Address:</span> {{ $selectedLoan->customer->address ?? '' }}</div>
                        </div>
                    </div>

                    <!-- Financial Terms Box -->
                    <div class="bg-slate-50 p-3 rounded border border-slate-300 grid grid-cols-3 gap-2 text-center text-xs">
                        <div>
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Sanctioned Principal</span>
                            <span class="text-base font-extrabold text-slate-900">₹{{ number_format($selectedLoan->principal_amount, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Assessed Gold Value</span>
                            <span class="text-base font-extrabold text-slate-900">₹{{ number_format($selectedLoan->total_valuation_amount, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Net Pure Gold Wt</span>
                            <span class="text-base font-extrabold text-amber-900">{{ number_format($selectedLoan->total_net_weight, 3) }} g</span>
                        </div>
                    </div>

                    <!-- Pledged Jewellery Table -->
                    <div>
                        <div class="font-bold text-xs uppercase mb-1">Details of Pledged Ornaments:</div>
                        <table class="w-full text-left table-compact border border-slate-300 text-xs">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Purity</th>
                                    <th>Gross Wt</th>
                                    <th>Stone Wt</th>
                                    <th>Net Wt</th>
                                    <th>Rate/g</th>
                                    <th>Appraised Value</th>
                                    <th>Packet Ref</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedLoan->pledgedItems as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="font-semibold">{{ $item->item_name }}</td>
                                        <td>{{ $item->purity }}</td>
                                        <td>{{ number_format($item->gross_weight, 3) }} g</td>
                                        <td>{{ number_format($item->stone_weight, 3) }} g</td>
                                        <td class="font-bold">{{ number_format($item->net_gold_weight, 3) }} g</td>
                                        <td>₹{{ number_format($item->gold_rate_per_gram, 0) }}</td>
                                        <td class="font-bold">₹{{ number_format($item->valuation_amount, 2) }}</td>
                                        <td class="font-mono text-[10px]">{{ $item->packet_number }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Terms & Disclaimer -->
                    <div class="text-[10px] text-slate-600 border-t border-slate-200 pt-2 space-y-1">
                        <p>1. The borrower agrees that pledged ornaments are deposited as security against the loan sum indicated above.</p>
                        <p>2. Interest is chargeable according to the agreed method from the date of disbursal until full settlement.</p>
                        <p>3. Pledged gold shall be stored in safe vault custody and released only upon full principal and interest clearance.</p>
                    </div>

                    <!-- Signatures -->
                    <div class="pt-8 grid grid-cols-2 gap-8 text-center text-xs">
                        <div class="border-t border-slate-400 pt-1">
                            <span class="font-semibold text-slate-800">Borrower's Signature / Thumb Impression</span>
                        </div>
                        <div class="border-t border-slate-400 pt-1">
                            <span class="font-semibold text-slate-800">For {{ \App\Models\BusinessSetting::get('shop_name', 'thrumble.smallloan') }} (Authorized Signatory)</span>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 border-t border-slate-200 flex items-center justify-end space-x-2 bg-slate-50 no-print">
                    <button wire:click="$set('showReceiptModal', false)" class="btn-secondary">Close</button>
                    <button type="button" onclick="window.print()" class="btn-primary">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Receipt
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
