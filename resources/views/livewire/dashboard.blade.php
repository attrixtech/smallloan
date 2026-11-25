<div class="space-y-4">
    <!-- Top Action / Greeting Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-lg font-bold text-slate-800 tracking-tight">Financial Dashboard</h1>
            <p class="text-xs text-slate-500">Live summary of active pledges, outstanding credit, and recent ledger activities.</p>
        </div>
        <div class="mt-2 sm:mt-0 flex items-center space-x-2">
            <a href="{{ route('loans') }}?action=create" class="btn-primary">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Gold Loan
            </a>
            <a href="{{ route('payments') }}?action=create" class="btn-secondary">
                <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Receive Payment
            </a>
            <a href="{{ route('customers') }}?action=create" class="btn-secondary">
                <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                New Customer
            </a>
        </div>
    </div>

    <!-- 6 Compact Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Active Loans -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Active Loans</div>
            <div class="mt-1 text-lg font-bold text-slate-900">{{ $activeLoansCount }}</div>
            <div class="mt-1 text-[11px] text-emerald-600 flex items-center">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span>
                In good standing
            </div>
        </div>

        <!-- Outstanding Principal -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Outstanding Principal</div>
            <div class="mt-1 text-lg font-bold text-slate-900">₹{{ number_format($totalOutstandingPrincipal, 2) }}</div>
            <div class="mt-1 text-[11px] text-slate-500">Live portfolio principal</div>
        </div>

        <!-- Total Interest Collected -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Interest Collected</div>
            <div class="mt-1 text-lg font-bold text-emerald-700">₹{{ number_format($totalInterestCollected, 2) }}</div>
            <div class="mt-1 text-[11px] text-slate-500">Total profit earned</div>
        </div>

        <!-- Total Customers -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Total Customers</div>
            <div class="mt-1 text-lg font-bold text-slate-900">{{ $totalCustomers }}</div>
            <div class="mt-1 text-[11px] text-slate-500">Registered borrowers</div>
        </div>

        <!-- Loans Due Today -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Due Today</div>
            <div class="mt-1 text-lg font-bold {{ $dueTodayLoansCount > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                {{ $dueTodayLoansCount }}
            </div>
            <div class="mt-1 text-[11px] text-slate-500">Maturity date today</div>
        </div>

        <!-- Overdue Loans -->
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Overdue Loans</div>
            <div class="mt-1 text-lg font-bold {{ $overdueLoansCount > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                {{ $overdueLoansCount }}
            </div>
            <div class="mt-1 text-[11px] text-rose-600">Requires follow-up</div>
        </div>
    </div>

    <!-- Middle Split: Urgent Attention / Due Soon & Recent Payments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Attention Needed: Due Today or Expiring Soon -->
        <div class="bg-white rounded border border-slate-200 shadow-xs">
            <div class="px-3 py-2 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                <span class="font-semibold text-xs text-slate-800 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Maturity Alerts (Due Today & Next 7 Days)
                </span>
                <a href="{{ route('loans') }}?status=Overdue" class="text-[11px] text-amber-700 hover:underline">View Overdue</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-compact">
                    <thead>
                        <tr>
                            <th>Loan No</th>
                            <th>Customer</th>
                            <th>Principal</th>
                            <th>Maturity</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dueSoonLoans as $loan)
                            <tr>
                                <td class="font-semibold text-slate-800">
                                    <a href="{{ route('loans') }}?search={{ $loan->loan_no }}" class="text-amber-700 hover:underline">
                                        {{ $loan->loan_no }}
                                    </a>
                                </td>
                                <td>{{ $loan->customer->name ?? 'N/A' }}</td>
                                <td>₹{{ number_format($loan->current_balance_principal, 2) }}</td>
                                <td class="{{ Carbon\Carbon::parse($loan->maturity_date)->isPast() ? 'text-rose-600 font-semibold' : 'text-slate-600' }}">
                                    {{ Carbon\Carbon::parse($loan->maturity_date)->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge {{ $loan->status === 'Overdue' ? 'badge-overdue' : 'badge-active' }}">
                                        {{ $loan->status }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('payments') }}?loan_id={{ $loan->id }}" class="text-xs text-emerald-700 hover:underline font-medium">Pay</a>
                                    <span class="text-slate-300 mx-1">|</span>
                                    <a href="{{ route('renewals') }}?loan_id={{ $loan->id }}" class="text-xs text-amber-700 hover:underline font-medium">Renew</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-xs text-slate-400">No loans due for maturity this week.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Payments Received -->
        <div class="bg-white rounded border border-slate-200 shadow-xs">
            <div class="px-3 py-2 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                <span class="font-semibold text-xs text-slate-800 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Recent Payments Collected
                </span>
                <a href="{{ route('payments') }}" class="text-[11px] text-amber-700 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-compact">
                    <thead>
                        <tr>
                            <th>Receipt</th>
                            <th>Customer</th>
                            <th>Mode</th>
                            <th>Interest</th>
                            <th>Principal</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $pay)
                            <tr>
                                <td class="font-medium text-slate-800">{{ $pay->payment_no }}</td>
                                <td>{{ $pay->customer->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="uppercase text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                                        {{ $pay->payment_mode }}
                                    </span>
                                </td>
                                <td class="text-emerald-700">₹{{ number_format($pay->interest_amount, 2) }}</td>
                                <td class="text-slate-700">₹{{ number_format($pay->principal_amount, 2) }}</td>
                                <td class="text-right font-bold text-slate-900">₹{{ number_format($pay->total_amount_paid, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-xs text-slate-400">No payments recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bottom: Recent Loan Bookings -->
    <div class="bg-white rounded border border-slate-200 shadow-xs">
        <div class="px-3 py-2 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
            <span class="font-semibold text-xs text-slate-800 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Recent Loan Bookings
            </span>
            <a href="{{ route('loans') }}" class="text-[11px] text-amber-700 hover:underline">View All Loans</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-compact">
                <thead>
                    <tr>
                        <th>Loan No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Gold Net Wt</th>
                        <th>Valuation</th>
                        <th>Principal Disbursed</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLoans as $loan)
                        <tr>
                            <td class="font-bold text-amber-700">
                                <a href="{{ route('loans') }}?search={{ $loan->loan_no }}" class="hover:underline">{{ $loan->loan_no }}</a>
                            </td>
                            <td>{{ Carbon\Carbon::parse($loan->issue_date)->format('d M Y') }}</td>
                            <td class="font-medium text-slate-800">{{ $loan->customer->name ?? 'N/A' }}</td>
                            <td>{{ number_format($loan->total_net_weight, 3) }} g</td>
                            <td>₹{{ number_format($loan->total_valuation_amount, 2) }}</td>
                            <td class="font-semibold text-slate-900">₹{{ number_format($loan->principal_amount, 2) }}</td>
                            <td>{{ $loan->interest_rate }}% pm</td>
                            <td>
                                <span class="badge {{ $loan->status === 'Active' ? 'badge-active' : ($loan->status === 'Overdue' ? 'badge-overdue' : 'badge-closed') }}">
                                    {{ $loan->status }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('loans') }}?view_id={{ $loan->id }}" class="text-xs text-amber-700 hover:underline font-medium">Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-xs text-slate-400">No loans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
