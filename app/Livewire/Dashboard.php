<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $today = Carbon::today()->toDateString();

        $activeLoansCount = Loan::where('status', 'Active')->count();
        $overdueLoansCount = Loan::where('status', 'Overdue')->count();
        $dueTodayLoansCount = Loan::where('status', 'Active')->whereDate('maturity_date', $today)->count();
        $totalCustomers = Customer::count();

        $totalOutstandingPrincipal = Loan::whereIn('status', ['Active', 'Overdue'])->sum('current_balance_principal');
        $totalInterestCollected = Payment::sum('interest_amount');
        $totalPrincipalCollected = Payment::sum('principal_amount');

        $recentLoans = Loan::with('customer')->latest('created_at')->take(5)->get();
        $recentPayments = Payment::with(['customer', 'loan'])->latest('created_at')->take(5)->get();

        $dueSoonLoans = Loan::with('customer')
            ->whereIn('status', ['Active', 'Overdue'])
            ->whereDate('maturity_date', '<=', Carbon::today()->addDays(7))
            ->orderBy('maturity_date', 'asc')
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'activeLoansCount' => $activeLoansCount,
            'overdueLoansCount' => $overdueLoansCount,
            'dueTodayLoansCount' => $dueTodayLoansCount,
            'totalCustomers' => $totalCustomers,
            'totalOutstandingPrincipal' => $totalOutstandingPrincipal,
            'totalInterestCollected' => $totalInterestCollected,
            'totalPrincipalCollected' => $totalPrincipalCollected,
            'recentLoans' => $recentLoans,
            'recentPayments' => $recentPayments,
            'dueSoonLoans' => $dueSoonLoans,
        ])->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
