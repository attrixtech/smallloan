<div class="space-y-4">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-slate-200">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Financial Reports & Auditing</h2>
            <p class="text-xs text-slate-500">Comprehensive collection statements, loan book auditing, customer ledgers & portfolio valuation</p>
        </div>
        <div class="flex items-center space-x-2 no-print">
            <button wire:click="exportCsv" class="btn-secondary">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </button>
            <button onclick="window.print()" class="btn-primary">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Report
            </button>
        </div>
    </div>

    <!-- Report Sub-Navigation Tabs -->
    <div class="flex items-center space-x-1 border-b border-slate-200 no-print overflow-x-auto">
        <button wire:click="$set('activeTab', 'daily')" class="px-3 py-2 text-xs font-semibold whitespace-nowrap cursor-pointer transition {{ $activeTab === 'daily' ? 'border-b-2 border-amber-600 text-amber-900 bg-white' : 'text-slate-600 hover:text-slate-900' }}">
            Daily Collection
        </button>
        <button wire:click="$set('activeTab', 'monthly')" class="px-3 py-2 text-xs font-semibold whitespace-nowrap cursor-pointer transition {{ $activeTab === 'monthly' ? 'border-b-2 border-amber-600 text-amber-900 bg-white' : 'text-slate-600 hover:text-slate-900' }}">
            Monthly Collection
        </button>
        <button wire:click="$set('activeTab', 'active_loans')" class="px-3 py-2 text-xs font-semibold whitespace-nowrap cursor-pointer transition {{ $activeTab === 'active_loans' ? 'border-b-2 border-amber-600 text-amber-900 bg-white' : 'text-slate-600 hover:text-slate-900' }}">
            Active Loans
        </button>
        <button wire:click="$set('activeTab', 'overdue_loans')" class="px-3 py-2 text-xs font-semibold whitespace-nowrap cursor-pointer transition {{ $activeTab === 'overdue_loans' ? 'border-b-2 border-amber-600 text-amber-900 bg-white' : 'text-slate-600 hover:text-slate-900' }}">
            Overdue Loans
        </button>
        <button wire:click="$set('activeTab', 'closed_loans')" class="px-3 py-2 text-xs font-semibold whitespace-nowrap cursor-pointer transition {{ $activeTab === 'closed_loans' ? 'border-b-2 border-amber-600 text-amber-900 bg-white' : 'text-slate-600 hover:text-slate-900' }}">
            Closed Loans
        </button>
        <button wire:click="$set('activeTab', 'customer_ledger')" class="px-3 py-2 text-xs font-semibold whitespace-nowrap cursor-pointer transition {{ $activeTab === 'customer_ledger' ? 'border-b-2 border-amber-600 text-amber-900 bg-white' : 'text-slate-600 hover:text-slate-900' }}">
            Customer Ledger
        </button>
        <button wire:click="$set('activeTab', 'vault_summary')" class="px-3 py-2 text-xs font-semibold whitespace-nowrap cursor-pointer transition {{ $activeTab === 'vault_summary' ? 'border-b-2 border-amber-600 text-amber-900 bg-white' : 'text-slate-600 hover:text-slate-900' }}">
            Vault & Collateral Summary
        </button>
    </div>

    <!-- Tab 1: Daily Collection Report -->
    @if($activeTab === 'daily')
        <div class="space-y-3">
            <div class="bg-white p-3 rounded border border-slate-200 shadow-2xs flex items-center justify-between no-print">
                <div class="flex items-center space-x-2">
                    <label class="text-xs font-semibold text-slate-700">Collection Date:</label>
                    <input type="date" wire:model.live="dailyDate" class="input-compact w-40">
                </div>
                <div class="text-xs text-slate-500">
                    Showing payments collected on <strong>{{ \Carbon\Carbon::parse($dailyDate)->format('d F Y') }}</strong>
                </div>
            </div>

            <!-- Daily Summary Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white p-2.5 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Total Collected</span>
                    <span class="text-base font-bold text-slate-900">₹{{ number_format($dailyTotalCollected, 2) }}</span>
                </div>
                <div class="bg-white p-2.5 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Interest Collected</span>
                    <span class="text-base font-bold text-emerald-700">₹{{ number_format($dailyTotalInterest, 2) }}</span>
                </div>
                <div class="bg-white p-2.5 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Principal Recovered</span>
                    <span class="text-base font-bold text-blue-700">₹{{ number_format($dailyTotalPrincipal, 2) }}</span>
                </div>
                <div class="bg-white p-2.5 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Cash vs UPI/Bank</span>
                    <span class="text-xs font-medium text-slate-700">
                        Cash: ₹{{ number_format($dailyCash, 0) }} &bull; UPI: ₹{{ number_format($dailyUpi, 0) }}
                    </span>
                </div>
            </div>

            <!-- Daily Payments Table -->
            <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
                <table class="w-full text-left table-compact">
                    <thead>
                        <tr>
                            <th>Receipt No</th>
                            <th>Time / Date</th>
                            <th>Loan No</th>
                            <th>Customer</th>
                            <th>Mode</th>
                            <th>Interest</th>
                            <th>Principal</th>
                            <th>Total Collected</th>
                            <th>Bal After</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyPayments as $p)
                            <tr>
                                <td class="font-mono text-amber-700 font-semibold">{{ $p->payment_no }}</td>
                                <td class="text-slate-600 text-[11px]">{{ $p->created_at->format('h:i A') }}</td>
                                <td class="font-mono">{{ $p->loan->loan_no ?? '-' }}</td>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $p->customer->name ?? '' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $p->customer->mobile ?? '' }}</div>
                                </td>
                                <td><span class="badge uppercase bg-slate-100 text-slate-700">{{ $p->payment_mode }}</span></td>
                                <td class="text-emerald-700 font-medium">₹{{ number_format($p->interest_amount, 2) }}</td>
                                <td class="text-blue-700 font-medium">₹{{ number_format($p->principal_amount, 2) }}</td>
                                <td class="font-bold text-slate-900">₹{{ number_format($p->total_amount_paid, 2) }}</td>
                                <td>₹{{ number_format($p->principal_balance_after, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-6 text-slate-400 text-xs">No collections recorded on this date.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Tab 2: Monthly Collection Report -->
    @if($activeTab === 'monthly')
        <div class="space-y-3">
            <div class="bg-white p-3 rounded border border-slate-200 shadow-2xs flex items-center space-x-3 no-print">
                <div class="flex items-center space-x-2">
                    <label class="text-xs font-semibold text-slate-700">Month:</label>
                    <select wire:model.live="monthlyMonth" class="input-compact w-32">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-xs font-semibold text-slate-700">Year:</label>
                    <input type="number" wire:model.live="monthlyYear" class="input-compact w-24">
                </div>
            </div>

            <!-- Monthly Summary Cards -->
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white p-3 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Total Monthly Collection</span>
                    <span class="text-lg font-bold text-slate-900">₹{{ number_format($monthlyTotalCollected, 2) }}</span>
                </div>
                <div class="bg-white p-3 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Net Interest Income</span>
                    <span class="text-lg font-bold text-emerald-700">₹{{ number_format($monthlyTotalInterest, 2) }}</span>
                </div>
                <div class="bg-white p-3 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Principal Capital Recovered</span>
                    <span class="text-lg font-bold text-blue-700">₹{{ number_format($monthlyTotalPrincipal, 2) }}</span>
                </div>
            </div>

            <!-- Monthly Payments List -->
            <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
                <table class="w-full text-left table-compact">
                    <thead>
                        <tr>
                            <th>Receipt No</th>
                            <th>Date</th>
                            <th>Loan No</th>
                            <th>Customer</th>
                            <th>Mode</th>
                            <th>Interest</th>
                            <th>Principal</th>
                            <th>Total Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyPayments as $p)
                            <tr>
                                <td class="font-mono text-amber-700 font-semibold">{{ $p->payment_no }}</td>
                                <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                                <td class="font-mono">{{ $p->loan->loan_no ?? '-' }}</td>
                                <td>{{ $p->customer->name ?? '' }}</td>
                                <td><span class="badge uppercase bg-slate-100 text-slate-700">{{ $p->payment_mode }}</span></td>
                                <td class="text-emerald-700 font-medium">₹{{ number_format($p->interest_amount, 2) }}</td>
                                <td class="text-blue-700 font-medium">₹{{ number_format($p->principal_amount, 2) }}</td>
                                <td class="font-bold text-slate-900">₹{{ number_format($p->total_amount_paid, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-6 text-slate-400 text-xs">No collections recorded for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Tab 3: Active Loans Report -->
    @if($activeTab === 'active_loans')
        <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
            <div class="px-3 py-2 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="font-bold text-xs text-slate-800">All Active Pledge Loans ({{ $activeLoans->count() }})</span>
                <span class="text-xs text-slate-600">Total Outstanding Principal: <strong>₹{{ number_format($activeLoans->sum('current_balance_principal'), 2) }}</strong></span>
            </div>
            <table class="w-full text-left table-compact">
                <thead>
                    <tr>
                        <th>Loan No</th>
                        <th>Borrower</th>
                        <th>Issue Date</th>
                        <th>Maturity Date</th>
                        <th>Adv. Principal</th>
                        <th>Current Balance</th>
                        <th>Rate</th>
                        <th>Pledged Gold</th>
                        <th>Valuation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeLoans as $l)
                        <tr>
                            <td class="font-mono font-semibold text-amber-700">{{ $l->loan_no }}</td>
                            <td>
                                <div class="font-medium text-slate-900">{{ $l->customer->name ?? '' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $l->customer->mobile ?? '' }}</div>
                            </td>
                            <td>{{ $l->issue_date->format('d/m/Y') }}</td>
                            <td>{{ $l->maturity_date->format('d/m/Y') }}</td>
                            <td>₹{{ number_format($l->principal_amount, 2) }}</td>
                            <td class="font-bold text-slate-900">₹{{ number_format($l->current_balance_principal, 2) }}</td>
                            <td>{{ $l->interest_rate }}% pm</td>
                            <td>{{ number_format($l->total_net_weight, 3) }} g</td>
                            <td>₹{{ number_format($l->total_valuation_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Tab 4: Overdue Loans Report -->
    @if($activeTab === 'overdue_loans')
        <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
            <div class="px-3 py-2 border-b border-slate-200 bg-rose-50 flex items-center justify-between text-rose-800">
                <span class="font-bold text-xs">Overdue Default Risk Loans ({{ $overdueLoans->count() }})</span>
                <span class="text-xs">At Risk Principal: <strong>₹{{ number_format($overdueLoans->sum('current_balance_principal'), 2) }}</strong></span>
            </div>
            <table class="w-full text-left table-compact">
                <thead>
                    <tr>
                        <th>Loan No</th>
                        <th>Borrower</th>
                        <th>Maturity Date</th>
                        <th>Days Overdue</th>
                        <th>Principal Balance</th>
                        <th>Interest Rate</th>
                        <th>Pledged Collateral</th>
                        <th>Valuation</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($overdueLoans as $l)
                        @php
                            $daysOver = \Carbon\Carbon::today()->diffInDays($l->maturity_date);
                        @endphp
                        <tr>
                            <td class="font-mono font-semibold text-rose-700">{{ $l->loan_no }}</td>
                            <td>
                                <div class="font-medium text-slate-900">{{ $l->customer->name ?? '' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $l->customer->mobile ?? '' }}</div>
                            </td>
                            <td class="text-rose-600 font-semibold">{{ $l->maturity_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-rose-100 text-rose-800 font-bold">{{ $daysOver }} days</span>
                            </td>
                            <td class="font-bold text-slate-900">₹{{ number_format($l->current_balance_principal, 2) }}</td>
                            <td>{{ $l->interest_rate }}% pm</td>
                            <td>{{ number_format($l->total_net_weight, 3) }} g</td>
                            <td>₹{{ number_format($l->total_valuation_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-slate-400 text-xs">No overdue loans found. All loans are within maturity.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Tab 5: Closed Loans Report -->
    @if($activeTab === 'closed_loans')
        <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
            <div class="px-3 py-2 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="font-bold text-xs text-slate-800">Settled & Closed Loans ({{ $closedLoans->count() }})</span>
            </div>
            <table class="w-full text-left table-compact">
                <thead>
                    <tr>
                        <th>Loan No</th>
                        <th>Borrower</th>
                        <th>Issue Date</th>
                        <th>Settled Date</th>
                        <th>Original Principal</th>
                        <th>Total Interest Earned</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($closedLoans as $l)
                        <tr>
                            <td class="font-mono font-semibold text-slate-700">{{ $l->loan_no }}</td>
                            <td>{{ $l->customer->name ?? '' }}</td>
                            <td>{{ $l->issue_date->format('d/m/Y') }}</td>
                            <td>{{ $l->closed_at ? $l->closed_at->format('d/m/Y') : '-' }}</td>
                            <td>₹{{ number_format($l->principal_amount, 2) }}</td>
                            <td class="font-bold text-emerald-700">₹{{ number_format($l->total_interest_paid, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-slate-400 text-xs">No closed loans on record.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Tab 6: Customer Ledger -->
    @if($activeTab === 'customer_ledger')
        <div class="space-y-3">
            <div class="bg-white p-3 rounded border border-slate-200 shadow-2xs flex items-center space-x-3 no-print">
                <label class="text-xs font-semibold text-slate-700">Select Customer:</label>
                <select wire:model.live="selectedCustomerId" class="input-compact max-w-sm font-medium">
                    <option value="">-- Choose Customer to inspect statement --</option>
                    @foreach($allCustomers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->mobile }})</option>
                    @endforeach
                </select>
            </div>

            @if($ledgerCustomer)
                <div class="bg-white p-4 rounded border border-slate-200 space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">{{ $ledgerCustomer->name }}</h3>
                            <p class="text-xs text-slate-500">{{ $ledgerCustomer->mobile }} &bull; {{ $ledgerCustomer->address }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-500 block">Current Total Outstanding:</span>
                            <strong class="text-base font-bold text-slate-900">₹{{ number_format($ledgerCustomer->total_outstanding, 2) }}</strong>
                        </div>
                    </div>

                    <!-- Chronological Statement -->
                    <div>
                        <h4 class="font-bold text-xs uppercase tracking-wider mb-2 text-slate-700">Loan & Repayment Statement</h4>
                        <table class="w-full text-left table-compact border border-slate-200 rounded">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction Type</th>
                                    <th>Ref / Loan No</th>
                                    <th>Debit (Advance)</th>
                                    <th>Credit (Payment)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ledgerLoans as $l)
                                    <tr>
                                        <td>{{ $l->issue_date->format('d/m/Y') }}</td>
                                        <td><span class="badge bg-blue-50 text-blue-700">Loan Disbursed</span></td>
                                        <td class="font-mono text-amber-700">{{ $l->loan_no }}</td>
                                        <td class="font-bold text-slate-900">₹{{ number_format($l->principal_amount, 2) }}</td>
                                        <td class="text-slate-400">-</td>
                                    </tr>
                                @endforeach
                                @foreach($ledgerPayments as $p)
                                    <tr>
                                        <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                                        <td><span class="badge bg-emerald-50 text-emerald-700">Repayment ({{ $p->payment_mode }})</span></td>
                                        <td class="font-mono text-slate-700">{{ $p->payment_no }}</td>
                                        <td class="text-slate-400">-</td>
                                        <td class="font-bold text-emerald-700">₹{{ number_format($p->total_amount_paid, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Tab 7: Vault & Collateral Summary -->
    @if($activeTab === 'vault_summary')
        <div class="space-y-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white p-3 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Total Collateral Items</span>
                    <span class="text-base font-bold text-slate-900">{{ $vaultItems->count() }} items</span>
                </div>
                <div class="bg-white p-3 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Net Pure Gold in Locker</span>
                    <span class="text-base font-bold text-amber-800">{{ number_format($vaultTotalNet, 3) }} g</span>
                </div>
                <div class="bg-white p-3 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Total Collateral Valuation</span>
                    <span class="text-base font-bold text-slate-900">₹{{ number_format($vaultTotalValuation, 2) }}</span>
                </div>
                <div class="bg-white p-3 rounded border border-slate-200">
                    <span class="text-[10px] text-slate-500 uppercase font-semibold block">Portfolio LTV Coverage</span>
                    <span class="text-base font-bold text-emerald-700">
                        {{ $vaultTotalValuation > 0 ? round(($totalOutstanding / $vaultTotalValuation) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Vault Breakdown Table -->
            <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-3 py-2 border-b border-slate-200 bg-slate-50">
                    <span class="font-bold text-xs text-slate-800">All Items Currently Pledged Inside Storage Locker Vault</span>
                </div>
                <table class="w-full text-left table-compact">
                    <thead>
                        <tr>
                            <th>Packet #</th>
                            <th>Description</th>
                            <th>Customer</th>
                            <th>Purity</th>
                            <th>Gross Wt</th>
                            <th>Net Gold Wt</th>
                            <th>Valuation</th>
                            <th>Locker Shelf</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vaultItems as $item)
                            <tr>
                                <td class="font-mono text-amber-700 font-semibold">{{ $item->packet_number }}</td>
                                <td>{{ $item->item_name }} ({{ $item->category }})</td>
                                <td>{{ $item->customer->name ?? '' }}</td>
                                <td>{{ $item->purity }}</td>
                                <td>{{ number_format($item->gross_weight, 3) }} g</td>
                                <td class="font-bold">{{ number_format($item->net_gold_weight, 3) }} g</td>
                                <td>₹{{ number_format($item->valuation_amount, 2) }}</td>
                                <td><span class="badge bg-slate-100 text-slate-700">{{ $item->locker_reference }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
