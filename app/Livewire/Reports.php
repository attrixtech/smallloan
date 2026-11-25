<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\PledgedItem;
use Carbon\Carbon;
use Livewire\Component;

class Reports extends Component
{
    public string $activeTab = 'daily';
    public string $dailyDate = '';
    public int $monthlyMonth = 1;
    public int $monthlyYear = 2026;
    public ?int $selectedCustomerId = null;

    protected $queryString = ['activeTab'];

    public function mount()
    {
        $this->dailyDate = date('Y-m-d');
        $this->monthlyMonth = (int) date('m');
        $this->monthlyYear = (int) date('Y');
    }

    public function exportCsv()
    {
        $filename = "report-{$this->activeTab}-" . date('Ymd-His') . ".csv";

        return response()->streamDownload(function() {
            $handle = fopen('php://output', 'w');

            if ($this->activeTab === 'daily') {
                fputcsv($handle, ['Receipt No', 'Date', 'Loan No', 'Customer', 'Mode', 'Interest', 'Principal', 'Total Collected']);
                $payments = Payment::with(['loan', 'customer'])->whereDate('payment_date', $this->dailyDate)->get();
                foreach ($payments as $p) {
                    fputcsv($handle, [
                        $p->payment_no,
                        $p->payment_date->format('Y-m-d'),
                        $p->loan->loan_no ?? '',
                        $p->customer->name ?? '',
                        strtoupper($p->payment_mode),
                        $p->interest_amount,
                        $p->principal_amount,
                        $p->total_amount_paid,
                    ]);
                }
            } elseif ($this->activeTab === 'monthly') {
                fputcsv($handle, ['Receipt No', 'Date', 'Loan No', 'Customer', 'Mode', 'Interest', 'Principal', 'Total Collected']);
                $payments = Payment::with(['loan', 'customer'])
                    ->whereYear('payment_date', $this->monthlyYear)
                    ->whereMonth('payment_date', $this->monthlyMonth)
                    ->get();
                foreach ($payments as $p) {
                    fputcsv($handle, [
                        $p->payment_no,
                        $p->payment_date->format('Y-m-d'),
                        $p->loan->loan_no ?? '',
                        $p->customer->name ?? '',
                        strtoupper($p->payment_mode),
                        $p->interest_amount,
                        $p->principal_amount,
                        $p->total_amount_paid,
                    ]);
                }
            } elseif ($this->activeTab === 'active_loans') {
                fputcsv($handle, ['Loan No', 'Customer', 'Mobile', 'Issue Date', 'Maturity', 'Principal', 'Balance', 'Net Gold Wt (g)', 'Valuation']);
                $loans = Loan::with('customer')->where('status', 'Active')->get();
                foreach ($loans as $l) {
                    fputcsv($handle, [
                        $l->loan_no,
                        $l->customer->name ?? '',
                        $l->customer->mobile ?? '',
                        $l->issue_date->format('Y-m-d'),
                        $l->maturity_date->format('Y-m-d'),
                        $l->principal_amount,
                        $l->current_balance_principal,
                        $l->total_net_weight,
                        $l->total_valuation_amount,
                    ]);
                }
            } elseif ($this->activeTab === 'overdue_loans') {
                fputcsv($handle, ['Loan No', 'Customer', 'Mobile', 'Maturity', 'Days Overdue', 'Balance', 'Net Gold (g)']);
                $loans = Loan::with('customer')->where('status', 'Overdue')->get();
                foreach ($loans as $l) {
                    $days = Carbon::today()->diffInDays(Carbon::parse($l->maturity_date));
                    fputcsv($handle, [
                        $l->loan_no,
                        $l->customer->name ?? '',
                        $l->customer->mobile ?? '',
                        $l->maturity_date->format('Y-m-d'),
                        $days,
                        $l->current_balance_principal,
                        $l->total_net_weight,
                    ]);
                }
            } elseif ($this->activeTab === 'closed_loans') {
                fputcsv($handle, ['Loan No', 'Customer', 'Issue Date', 'Closed Date', 'Principal', 'Interest Paid']);
                $loans = Loan::with('customer')->where('status', 'Closed')->get();
                foreach ($loans as $l) {
                    fputcsv($handle, [
                        $l->loan_no,
                        $l->customer->name ?? '',
                        $l->issue_date->format('Y-m-d'),
                        $l->closed_at ? $l->closed_at->format('Y-m-d') : '',
                        $l->principal_amount,
                        $l->total_interest_paid,
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function render()
    {
        // Daily
        $dailyPayments = Payment::with(['customer', 'loan'])
            ->whereDate('payment_date', $this->dailyDate)
            ->orderBy('id', 'desc')
            ->get();

        $dailyTotalCollected = (float) $dailyPayments->sum('total_amount_paid');
        $dailyTotalInterest = (float) $dailyPayments->sum('interest_amount');
        $dailyTotalPrincipal = (float) $dailyPayments->sum('principal_amount');
        $dailyCash = (float) $dailyPayments->where('payment_mode', 'cash')->sum('total_amount_paid');
        $dailyUpi = (float) $dailyPayments->whereIn('payment_mode', ['upi', 'bank_transfer', 'cheque'])->sum('total_amount_paid');

        // Monthly
        $monthlyPayments = Payment::with(['customer', 'loan'])
            ->whereYear('payment_date', $this->monthlyYear)
            ->whereMonth('payment_date', $this->monthlyMonth)
            ->orderBy('id', 'desc')
            ->get();

        $monthlyTotalCollected = (float) $monthlyPayments->sum('total_amount_paid');
        $monthlyTotalInterest = (float) $monthlyPayments->sum('interest_amount');
        $monthlyTotalPrincipal = (float) $monthlyPayments->sum('principal_amount');

        // Loans Lists
        $activeLoansList = Loan::with('customer')->where('status', 'Active')->orderBy('id', 'desc')->get();
        $overdueLoansList = Loan::with('customer')->where('status', 'Overdue')->orderBy('maturity_date', 'asc')->get();
        $closedLoansList = Loan::with('customer')->where('status', 'Closed')->orderBy('closed_at', 'desc')->get();

        // Customer Ledger
        $customersList = Customer::orderBy('name')->get();
        $ledgerCustomer = null;
        $ledgerLoans = collect();
        $ledgerPayments = collect();
        if ($this->selectedCustomerId) {
            $ledgerCustomer = Customer::with(['loans', 'payments'])->find($this->selectedCustomerId);
            if ($ledgerCustomer) {
                $ledgerLoans = $ledgerCustomer->loans()->orderBy('issue_date', 'asc')->get();
                $ledgerPayments = $ledgerCustomer->payments()->orderBy('payment_date', 'asc')->get();
            }
        }

        // Vault Summary
        $vaultItems = PledgedItem::where('status', 'Pledged')->with(['customer', 'loan'])->get();
        $vaultTotalNet = (float) $vaultItems->sum('net_gold_weight');
        $vaultTotalValuation = (float) $vaultItems->sum('valuation_amount');
        $totalOutstanding = (float) Loan::whereIn('status', ['Active', 'Overdue'])->sum('current_balance_principal');

        return view('livewire.reports', array_merge(get_object_vars($this), [
            'dailyPayments' => $dailyPayments,
            'dailyTotalCollected' => $dailyTotalCollected,
            'dailyTotalInterest' => $dailyTotalInterest,
            'dailyTotalPrincipal' => $dailyTotalPrincipal,
            'dailyCash' => $dailyCash,
            'dailyUpi' => $dailyUpi,

            'monthlyPayments' => $monthlyPayments,
            'monthlyTotalCollected' => $monthlyTotalCollected,
            'monthlyTotalInterest' => $monthlyTotalInterest,
            'monthlyTotalPrincipal' => $monthlyTotalPrincipal,

            'activeLoansList' => $activeLoansList,
            'activeLoans' => $activeLoansList,
            'overdueLoansList' => $overdueLoansList,
            'overdueLoans' => $overdueLoansList,
            'closedLoansList' => $closedLoansList,
            'closedLoans' => $closedLoansList,

            'customersList' => $customersList,
            'allCustomers' => $customersList,
            'ledgerCustomer' => $ledgerCustomer,
            'ledgerLoans' => $ledgerLoans,
            'ledgerPayments' => $ledgerPayments,

            'vaultItems' => $vaultItems,
            'vaultTotalNet' => $vaultTotalNet,
            'vaultTotalValuation' => $vaultTotalValuation,
            'totalOutstanding' => $totalOutstanding,
        ]))->layout('layouts.app', ['title' => 'Financial Reports']);
    }
}
