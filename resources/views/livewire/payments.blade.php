<div class="space-y-4">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-slate-200">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Payment Collections & Receipts</h2>
            <p class="text-xs text-slate-500">Collect interest payments, principal reductions, full loan settlements and print vouchers</p>
        </div>
        <div class="flex items-center space-x-2">
            <button wire:click="openCreateModal" class="btn-success">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Receive Payment
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-2xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center space-x-2 flex-1 min-w-[240px]">
            <div class="relative w-full max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search receipt no, loan no, customer, ref..."
                       class="input-compact pl-8">
            </div>
            @if($search)
                <button wire:click="$set('search', '')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>

        <div class="flex items-center space-x-2">
            <div>
                <select wire:model.live="typeFilter" class="input-compact w-32">
                    <option value="all">All Types</option>
                    <option value="interest_only">Interest Only</option>
                    <option value="principal_only">Principal Only</option>
                    <option value="partial_both">Partial (Both)</option>
                    <option value="full_settlement">Full Settlement</option>
                </select>
            </div>
            <div>
                <select wire:model.live="modeFilter" class="input-compact w-28">
                    <option value="all">All Modes</option>
                    <option value="cash">Cash</option>
                    <option value="upi">UPI</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cheque">Cheque</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-compact">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Date</th>
                        <th>Loan Contract</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Mode</th>
                        <th>Interest Paid</th>
                        <th>Principal Paid</th>
                        <th>Total Collected</th>
                        <th>Principal Balance After</th>
                        <th class="text-right">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td class="font-bold text-amber-700 font-mono">
                                <a href="javascript:void(0)" wire:click="printReceipt({{ $p->id }})" class="hover:underline">
                                    {{ $p->payment_no }}
                                </a>
                            </td>
                            <td class="text-slate-600 text-[11px]">{{ $p->payment_date->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('loans', ['search' => $p->loan->loan_no ?? '']) }}" class="font-mono text-slate-800 hover:text-amber-700 hover:underline">
                                    {{ $p->loan->loan_no ?? 'N/A' }}
                                </a>
                            </td>
                            <td>
                                <div class="font-medium text-slate-900">{{ $p->customer->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $p->customer->mobile ?? '' }}</div>
                            </td>
                            <td>
                                <span class="badge uppercase {{ $p->payment_type === 'full_settlement' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                                    {{ str_replace('_', ' ', $p->payment_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge uppercase bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                    {{ $p->payment_mode }}
                                </span>
                                @if($p->reference_number)
                                    <span class="text-[9px] text-slate-400 block truncate max-w-[80px]" title="{{ $p->reference_number }}">{{ $p->reference_number }}</span>
                                @endif
                            </td>
                            <td class="text-emerald-700 font-medium">₹{{ number_format($p->interest_amount, 2) }}</td>
                            <td class="text-blue-700 font-medium">₹{{ number_format($p->principal_amount, 2) }}</td>
                            <td class="font-bold text-slate-900">₹{{ number_format($p->total_amount_paid, 2) }}</td>
                            <td class="font-semibold text-slate-800">₹{{ number_format($p->principal_balance_after, 2) }}</td>
                            <td class="text-right">
                                <button wire:click="printReceipt({{ $p->id }})" class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded text-[11px] font-medium transition cursor-pointer">
                                    Print Receipt
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-6 text-slate-400 text-xs">
                                No payment receipts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $payments->links() }}
        </div>
    </div>

    <!-- Modal: Receive Payment -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-2xl overflow-hidden my-6">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Receive Loan Repayment</h3>
                        <p class="text-[11px] text-slate-500">Auto generated receipt: <strong class="text-amber-800 font-mono">{{ $payment_no }}</strong></p>
                    </div>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="savePayment" class="p-4 space-y-4 max-h-[80vh] overflow-y-auto text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Select Active Loan <span class="text-rose-500">*</span></label>
                            <select wire:model.live="loan_id" class="input-compact">
                                <option value="">-- Choose Loan Contract --</option>
                                @foreach($activeLoans as $al)
                                    <option value="{{ $al->id }}">
                                        {{ $al->loan_no }} &bull; {{ $al->customer->name ?? '' }} (Bal: ₹{{ number_format($al->current_balance_principal, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('loan_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Payment Date <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model.live="payment_date" class="input-compact">
                            @error('payment_date') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Live Computation Card for selected loan -->
                    @if($selectedLoan && $interestCalc)
                        <div class="bg-amber-50/70 p-3 rounded border border-amber-200/80 space-y-2">
                            <div class="flex items-center justify-between text-[11px] font-bold text-amber-900 uppercase">
                                <span>Real-Time Interest & Balance Position</span>
                                <span>{{ $interestCalc['days'] }} Days Elapsed ({{ $interestCalc['months'] }} Months)</span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <div class="bg-white p-2 rounded border border-amber-200/60">
                                    <span class="text-[10px] text-slate-500 block">Principal Balance</span>
                                    <strong class="text-slate-900 font-bold">₹{{ number_format($interestCalc['principal'], 2) }}</strong>
                                </div>
                                <div class="bg-white p-2 rounded border border-amber-200/60">
                                    <span class="text-[10px] text-slate-500 block">Interest Paid Earlier</span>
                                    <strong class="text-slate-700 font-bold">₹{{ number_format($interestCalc['interest_paid'], 2) }}</strong>
                                </div>
                                <div class="bg-white p-2 rounded border border-amber-200/60">
                                    <span class="text-[10px] text-slate-500 block">Accrued Interest Due</span>
                                    <strong class="text-amber-800 font-bold">₹{{ number_format($interestCalc['net_interest_due'], 2) }}</strong>
                                </div>
                                <div class="bg-white p-2 rounded border border-amber-200/60">
                                    <span class="text-[10px] text-slate-500 block">Full Settlement Total</span>
                                    <strong class="text-emerald-700 font-bold">₹{{ number_format($interestCalc['total_settlement_amount'], 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Payment Type Selection -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Payment Type <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <label class="flex items-center space-x-2 p-2 rounded border border-slate-200 cursor-pointer hover:bg-slate-50 {{ $payment_type === 'interest_only' ? 'bg-amber-50/60 border-amber-500 font-semibold text-amber-900' : '' }}">
                                <input type="radio" wire:model.live="payment_type" value="interest_only" class="text-amber-600 focus:ring-amber-500">
                                <span>Interest Only</span>
                            </label>
                            <label class="flex items-center space-x-2 p-2 rounded border border-slate-200 cursor-pointer hover:bg-slate-50 {{ $payment_type === 'principal_only' ? 'bg-amber-50/60 border-amber-500 font-semibold text-amber-900' : '' }}">
                                <input type="radio" wire:model.live="payment_type" value="principal_only" class="text-amber-600 focus:ring-amber-500">
                                <span>Principal Only</span>
                            </label>
                            <label class="flex items-center space-x-2 p-2 rounded border border-slate-200 cursor-pointer hover:bg-slate-50 {{ $payment_type === 'partial_both' ? 'bg-amber-50/60 border-amber-500 font-semibold text-amber-900' : '' }}">
                                <input type="radio" wire:model.live="payment_type" value="partial_both" class="text-amber-600 focus:ring-amber-500">
                                <span>Partial (Both)</span>
                            </label>
                            <label class="flex items-center space-x-2 p-2 rounded border border-slate-200 cursor-pointer hover:bg-slate-50 {{ $payment_type === 'full_settlement' ? 'bg-emerald-50 border-emerald-500 font-semibold text-emerald-900' : '' }}">
                                <input type="radio" wire:model.live="payment_type" value="full_settlement" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Full Settlement</span>
                            </label>
                        </div>
                    </div>

                    <!-- Amounts Breakdown -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-3 rounded border border-slate-200">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Interest Amount (₹)</label>
                            <input type="number" step="0.01" wire:model.live="interest_amount" class="input-compact font-bold text-emerald-800">
                            @error('interest_amount') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Principal Amount (₹)</label>
                            <input type="number" step="0.01" wire:model.live="principal_amount" class="input-compact font-bold text-blue-800">
                            @error('principal_amount') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Penalty / Late Fee (₹)</label>
                            <input type="number" step="0.01" wire:model.live="penalty_amount" class="input-compact">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Discount / Waiver (₹)</label>
                            <input type="number" step="0.01" wire:model.live="discount_amount" class="input-compact text-rose-700">
                        </div>
                    </div>

                    <!-- Total Amount Display -->
                    <div class="p-3 rounded bg-amber-50 border border-amber-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800">Total Net Amount Collected:</span>
                            <span class="text-[11px] text-slate-500 block">Interest + Principal + Penalty - Discount</span>
                        </div>
                        <div class="text-lg font-bold text-slate-900">
                            ₹{{ number_format($total_amount_paid, 2) }}
                        </div>
                    </div>

                    <!-- Payment Mode & Reference -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Payment Mode <span class="text-rose-500">*</span></label>
                            <select wire:model="payment_mode" class="input-compact font-semibold">
                                <option value="cash">Cash Counter</option>
                                <option value="upi">UPI (GPay / PhonePe / Paytm)</option>
                                <option value="bank_transfer">Bank Transfer (NEFT / RTGS / IMPS)</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Txn / UTR / Cheque Reference #</label>
                            <input type="text" wire:model="reference_number" class="input-compact font-mono" placeholder="Optional transaction ID">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Receipt Notes / Remarks</label>
                        <input type="text" wire:model="notes" class="input-compact" placeholder="Payment remarks...">
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-success">Record Payment & Issue Receipt</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal: Printable Payment Receipt -->
    @if($showPrintModal && $selectedPayment)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-xl overflow-hidden my-6">
                <div class="px-4 py-2 border-b border-slate-200 flex items-center justify-between bg-slate-50 no-print">
                    <span class="font-bold text-xs text-slate-800">Printable Payment Receipt</span>
                    <button wire:click="$set('showPrintModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Printable Voucher -->
                <div class="p-6 bg-white text-slate-900 text-xs font-sans space-y-4 print-receipt-container" id="printablePaymentReceipt">
                    <div class="text-center pb-3 border-b-2 border-slate-900">
                        <h2 class="text-base font-extrabold uppercase tracking-wide text-slate-900">
                            {{ \App\Models\BusinessSetting::get('shop_name', 'WeBotApp Gold Loan & Girvi') }}
                        </h2>
                        <p class="text-[11px] text-slate-600">{{ \App\Models\BusinessSetting::get('shop_address', 'Main Road, Zaveri Bazaar, Mumbai') }}</p>
                        <p class="text-[11px] text-slate-600">Phone: {{ \App\Models\BusinessSetting::get('phone_number', '+91 98765 43210') }}</p>
                        <div class="inline-block px-3 py-0.5 mt-2 rounded bg-slate-100 text-[11px] font-bold tracking-wider uppercase border border-slate-300">
                            Official Payment Receipt / Acknowledgement
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border border-slate-300 p-3 rounded">
                        <div>
                            <p><strong>Receipt No:</strong> <span class="font-mono text-sm font-bold">{{ $selectedPayment->payment_no }}</span></p>
                            <p><strong>Payment Date:</strong> {{ $selectedPayment->payment_date->format('d/m/Y') }}</p>
                            <p><strong>Loan No:</strong> <span class="font-mono font-bold">{{ $selectedPayment->loan->loan_no ?? '' }}</span></p>
                            <p><strong>Payment Mode:</strong> <span class="uppercase font-semibold">{{ $selectedPayment->payment_mode }}</span></p>
                            @if($selectedPayment->reference_number)
                                <p><strong>Ref / UTR:</strong> {{ $selectedPayment->reference_number }}</p>
                            @endif
                        </div>
                        <div>
                            <p><strong>Customer Name:</strong> <span class="font-bold">{{ $selectedPayment->customer->name ?? '' }}</span></p>
                            <p><strong>Customer Code:</strong> {{ $selectedPayment->customer->customer_code ?? '' }}</p>
                            <p><strong>Mobile:</strong> {{ $selectedPayment->customer->mobile ?? '' }}</p>
                            <p><strong>Received By:</strong> {{ $selectedPayment->receiver->name ?? 'Staff' }}</p>
                        </div>
                    </div>

                    <!-- Payment Breakdown Table -->
                    <table class="w-full text-left border border-slate-300 text-xs">
                        <thead class="bg-slate-100 border-b border-slate-300">
                            <tr>
                                <th class="p-2 border-r border-slate-300">Description</th>
                                <th class="p-2 text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-slate-200">
                                <td class="p-2 border-r border-slate-200">Interest Payment Cleared</td>
                                <td class="p-2 text-right font-medium">₹{{ number_format($selectedPayment->interest_amount, 2) }}</td>
                            </tr>
                            <tr class="border-b border-slate-200">
                                <td class="p-2 border-r border-slate-200">Principal Repayment</td>
                                <td class="p-2 text-right font-medium">₹{{ number_format($selectedPayment->principal_amount, 2) }}</td>
                            </tr>
                            @if($selectedPayment->penalty_amount > 0)
                                <tr class="border-b border-slate-200">
                                    <td class="p-2 border-r border-slate-200">Late Fee / Penalty</td>
                                    <td class="p-2 text-right font-medium">₹{{ number_format($selectedPayment->penalty_amount, 2) }}</td>
                                </tr>
                            @endif
                            @if($selectedPayment->discount_amount > 0)
                                <tr class="border-b border-slate-200 text-rose-700">
                                    <td class="p-2 border-r border-slate-200">Settlement Discount / Concession</td>
                                    <td class="p-2 text-right font-medium">- ₹{{ number_format($selectedPayment->discount_amount, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-400">
                            <tr>
                                <td class="p-2 border-r border-slate-300 text-right">TOTAL AMOUNT COLLECTED:</td>
                                <td class="p-2 text-right text-sm font-bold text-slate-900">₹{{ number_format($selectedPayment->total_amount_paid, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="bg-slate-50 p-2.5 rounded border border-slate-300 flex justify-between items-center text-xs">
                        <span>Remaining Principal Balance After This Payment:</span>
                        <strong class="text-sm font-bold text-slate-900">₹{{ number_format($selectedPayment->principal_balance_after, 2) }}</strong>
                    </div>

                    <div class="text-[10px] text-slate-500 pt-1">
                        * This is an automated immutable computer generated receipt. Retain for your accounts.
                    </div>

                    <div class="grid grid-cols-2 gap-8 pt-8 text-center text-xs">
                        <div class="border-t border-slate-400 pt-1 font-semibold">
                            Borrower Signature
                        </div>
                        <div class="border-t border-slate-400 pt-1 font-semibold">
                            Cashier / Authorized Signatory
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 border-t border-slate-200 flex items-center justify-end space-x-2 bg-slate-50 no-print">
                    <button type="button" wire:click="$set('showPrintModal', false)" class="btn-secondary">Close</button>
                    <button type="button" onclick="window.print()" class="btn-primary">Print Receipt</button>
                </div>
            </div>
        </div>
    @endif
</div>
