<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-lg font-bold text-slate-800 tracking-tight">Loan Renewals & Tenure Extensions</h1>
            <p class="text-xs text-slate-500">Extend pledge maturity dates, clear accrued interest, and record historical loan extensions.</p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="btn-primary">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Renew Loan
            </button>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex items-center justify-between gap-3">
        <div class="relative w-full max-w-md">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by renewal no, loan no, customer..." class="input-compact pl-8">
            <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    <!-- Renewals Table -->
    <div class="bg-white rounded border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-compact">
                <thead>
                    <tr>
                        <th>Renewal No</th>
                        <th>Renewal Date</th>
                        <th>Loan Contract</th>
                        <th>Borrower</th>
                        <th>Previous Maturity</th>
                        <th>Extended Maturity</th>
                        <th>Interest Cleared</th>
                        <th>New Principal</th>
                        <th>Processed By</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($renewals as $rnw)
                        <tr>
                            <td class="font-bold text-amber-700 font-mono">{{ $rnw->renewal_no }}</td>
                            <td>{{ $rnw->renewal_date->format('d/m/Y') }}</td>
                            <td class="font-bold font-mono">{{ $rnw->loan->loan_no ?? '-' }}</td>
                            <td>
                                <div class="font-semibold text-slate-800">{{ $rnw->customer->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $rnw->customer->mobile ?? '' }}</div>
                            </td>
                            <td class="text-slate-500">{{ $rnw->previous_maturity_date->format('d/m/Y') }}</td>
                            <td class="font-bold text-emerald-800">{{ $rnw->new_maturity_date->format('d/m/Y') }}</td>
                            <td class="text-emerald-700 font-medium">₹{{ number_format($rnw->interest_cleared_amount, 2) }}</td>
                            <td class="font-bold text-slate-900">₹{{ number_format($rnw->new_principal_amount, 2) }}</td>
                            <td class="text-slate-500 text-xs">{{ $rnw->processor->name ?? 'Staff' }}</td>
                            <td class="text-right">
                                <button wire:click="printRenewal({{ $rnw->id }})" class="btn-secondary !py-1 !px-2 text-[11px]">
                                    Voucher
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-6 text-slate-400 text-xs">
                                No loan renewal history found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-200 bg-slate-50/50">
            {{ $renewals->links() }}
        </div>
    </div>

    <!-- Renew Loan Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-lg shadow-xl border border-slate-200 w-full max-w-xl overflow-hidden max-h-[94vh] flex flex-col">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-xs">Execute Loan Renewal ({{ $renewal_no }})</h3>
                        <span class="text-[10px] text-slate-500">Collect due interest, extend loan tenure and refresh agreement status.</span>
                    </div>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <div class="p-4 overflow-y-auto space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Select Active Loan *</label>
                            <select wire:model.live="loan_id" class="input-compact font-semibold">
                                <option value="">-- Choose Loan Contract --</option>
                                @foreach($activeLoans as $al)
                                    <option value="{{ $al->id }}">
                                        {{ $al->loan_no }} - {{ $al->customer->name ?? '' }} (Due: {{ $al->maturity_date->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('loan_id') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Renewal Date *</label>
                            <input type="date" wire:model.live="renewal_date" class="input-compact">
                            @error('renewal_date') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if($selectedLoan && $liveInterestCalc)
                        <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-amber-950 uppercase tracking-wide">Current Contract Status</span>
                                <span class="text-[10px] text-slate-500">Maturity: {{ $selectedLoan->maturity_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="bg-white p-2 rounded border border-amber-200">
                                    <span class="text-[10px] text-slate-400 block">Principal Balance</span>
                                    <strong class="text-slate-900">₹{{ number_format($selectedLoan->current_balance_principal, 2) }}</strong>
                                </div>
                                <div class="bg-white p-2 rounded border border-amber-200">
                                    <span class="text-[10px] text-slate-400 block">Days Elapsed</span>
                                    <strong>{{ $liveInterestCalc['days'] }} days</strong>
                                </div>
                                <div class="bg-white p-2 rounded border border-amber-200">
                                    <span class="text-[10px] text-slate-400 block">Accrued Interest Due</span>
                                    <strong class="text-rose-700 font-bold">₹{{ number_format($liveInterestCalc['net_interest_due'], 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Extension Terms -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Extend By (Months) *</label>
                            <select wire:model.live="months_to_extend" class="input-compact font-semibold">
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="12">12 Months (1 Year)</option>
                                <option value="24">24 Months (2 Years)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">New Maturity Due Date</label>
                            <input type="date" wire:model="new_maturity_date" class="input-compact font-bold text-emerald-800 bg-slate-50">
                        </div>
                    </div>

                    <!-- Payment Collected during Renewal -->
                    <div class="p-3 bg-slate-50 rounded border border-slate-200 space-y-2">
                        <span class="text-[11px] font-bold text-slate-800 uppercase tracking-wide block">Renewal Settlement Collection</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Interest Paid (₹) *</label>
                                <input type="number" step="0.01" wire:model="interest_cleared_amount" class="input-compact font-bold text-emerald-800">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Principal Adj (₹)</label>
                                <input type="number" step="0.01" wire:model="principal_adjustment" class="input-compact" placeholder="Negative to reduce">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Renewal Fee (₹)</label>
                                <input type="number" step="0.01" wire:model="renewal_fee" class="input-compact">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200">
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-600 mb-0.5">Collection Mode</label>
                                <select wire:model="payment_mode" class="input-compact">
                                    <option value="cash">Cash</option>
                                    <option value="upi">UPI</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="text-right flex flex-col justify-end">
                                <span class="text-[10px] text-slate-500">Total Due on Renewal:</span>
                                <strong class="text-slate-900 text-sm">
                                    ₹{{ number_format(floatval($interest_cleared_amount) + ($principal_adjustment < 0 ? abs($principal_adjustment) : 0) + floatval($renewal_fee), 2) }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Renewal Notes</label>
                        <input type="text" wire:model="notes" class="input-compact" placeholder="Terms discussed or renewal endorsement notes">
                    </div>
                </div>

                <div class="px-4 py-2.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end space-x-2">
                    <button wire:click="$set('showCreateModal', false)" class="btn-secondary">Cancel</button>
                    <button wire:click="processRenewal" class="btn-primary">Confirm Renewal & Extend Contract</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Printable Renewal Voucher Modal -->
    @if($showPrintModal && $selectedRenewal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-lg shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden max-h-[95vh] flex flex-col">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between no-print">
                    <h3 class="font-bold text-slate-800 text-xs">Loan Renewal Voucher ({{ $selectedRenewal->renewal_no }})</h3>
                    <button wire:click="$set('showPrintModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto space-y-4 text-slate-900 print-only">
                    <!-- Shop Header -->
                    <div class="text-center border-b border-slate-300 pb-3">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ \App\Models\BusinessSetting::get('shop_name', 'WeBotApp Gold Loan & Girvi') }}</h2>
                        <div class="text-[11px] text-slate-600">{{ \App\Models\BusinessSetting::get('shop_address', 'Shop 12, Gold Market, Zaveri Bazaar, Mumbai 400002') }}</div>
                        <div class="text-[11px] text-slate-600">Phone: {{ \App\Models\BusinessSetting::get('shop_phone', '+91 98200 12345') }}</div>
                        <div class="mt-2 inline-block px-3 py-0.5 rounded bg-slate-100 border border-slate-300 text-slate-800 font-bold text-xs uppercase tracking-wide">
                            PLEDGE RENEWAL & MATURITY EXTENSION ENDORSEMENT
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <div><strong class="text-slate-500">Renewal No:</strong> <span class="font-mono font-bold">{{ $selectedRenewal->renewal_no }}</span></div>
                            <div><strong class="text-slate-500">Endorsement Date:</strong> {{ $selectedRenewal->renewal_date->format('d/m/Y') }}</div>
                            <div><strong class="text-slate-500">Loan Contract:</strong> <span class="font-mono font-bold">{{ $selectedRenewal->loan->loan_no ?? '-' }}</span></div>
                        </div>
                        <div class="text-right">
                            <div><strong class="text-slate-500">Customer:</strong> <span class="font-bold">{{ $selectedRenewal->customer->name ?? '' }}</span></div>
                            <div><strong class="text-slate-500">Mobile:</strong> {{ $selectedRenewal->customer->mobile ?? '' }}</div>
                            <div><strong class="text-slate-500">Code:</strong> {{ $selectedRenewal->customer->customer_code ?? '' }}</div>
                        </div>
                    </div>

                    <!-- Renewal Terms Grid -->
                    <div class="border border-slate-300 rounded p-3 bg-slate-50 text-xs space-y-2">
                        <div class="flex justify-between border-b border-slate-200 pb-1.5">
                            <span class="text-slate-500">Previous Maturity Date:</span>
                            <span class="text-slate-700">{{ $selectedRenewal->previous_maturity_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-1.5 font-bold">
                            <span class="text-slate-700">New Extended Maturity Date:</span>
                            <span class="text-emerald-800">{{ $selectedRenewal->new_maturity_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-1.5">
                            <span class="text-slate-500">Interest Cleared up to Renewal:</span>
                            <span class="font-bold text-emerald-800">₹{{ number_format($selectedRenewal->interest_cleared_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-1.5">
                            <span class="text-slate-500">Renewal Documentation Fee:</span>
                            <span>₹{{ number_format($selectedRenewal->renewal_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-sm pt-1">
                            <span>New Continuing Principal:</span>
                            <span class="text-slate-900">₹{{ number_format($selectedRenewal->new_principal_amount, 2) }}</span>
                        </div>
                    </div>

                    <div class="text-[10px] text-slate-500">
                        * Note: All original gold jewellery articles pledged under contract {{ $selectedRenewal->loan->loan_no ?? '' }} remain securely held in vault storage under the extended tenure terms.
                    </div>

                    <!-- Signatures -->
                    <div class="grid grid-cols-2 gap-8 pt-8 text-xs text-center">
                        <div class="border-t border-slate-400 pt-1">
                            Borrower Signature / Acceptance
                        </div>
                        <div class="border-t border-slate-400 pt-1">
                            Processed by: {{ $selectedRenewal->processor->name ?? 'Manager' }}
                        </div>
                    </div>
                </div>

                <div class="px-4 py-2.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end space-x-2 no-print">
                    <button wire:click="$set('showPrintModal', false)" class="btn-secondary">Close</button>
                    <button onclick="window.print()" class="btn-primary">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Voucher
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
