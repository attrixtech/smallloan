<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Customers;
use App\Livewire\Dashboard;
use App\Livewire\GoldRelease;
use App\Livewire\Loans;
use App\Livewire\Payments;
use App\Livewire\Pledges;
use App\Livewire\Renewals;
use App\Livewire\Reports;
use App\Livewire\Settings;
use App\Livewire\Users;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected ERP Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/customers', Customers::class)->name('customers');
    Route::get('/pledges', Pledges::class)->name('pledges');
    Route::get('/loans', Loans::class)->name('loans');
    Route::get('/payments', Payments::class)->name('payments');
    Route::get('/renewals', Renewals::class)->name('renewals');
    Route::get('/gold-release', GoldRelease::class)->name('gold-release');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/users', Users::class)->name('users');
    Route::get('/settings', Settings::class)->name('settings');
});
