<?php

namespace App\Livewire;

use App\Models\BusinessSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Settings extends Component
{
    public $activeTab = 'business';

    // Business Settings
    public $shop_name;
    public $shop_address;
    public $phone_number;
    public $email;
    public $currency = 'INR';
    public $receipt_header;
    public $receipt_footer;
    public $default_interest_rate = 1.50;
    public $default_interest_method = 'monthly';
    public $default_loan_tenure_months = 12;

    // Gold Rates (per gram)
    public $gold_rate_24k = 7450;
    public $gold_rate_22k = 6850;
    public $gold_rate_20k = 6250;
    public $gold_rate_18k = 5600;

    // My Profile
    public $profile_name = '';
    public $profile_email = '';
    public $profile_mobile = '';

    // Security
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public function mount()
    {
        $this->loadBusinessSettings();
        $this->loadProfileSettings();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function loadBusinessSettings()
    {
        $this->shop_name = BusinessSetting::get('shop_name', 'thrumble.smallloan');
        $this->shop_address = BusinessSetting::get('shop_address', 'Shop #12, Zaveri Bazaar, Kalbadevi, Mumbai - 400002');
        $this->phone_number = BusinessSetting::get('phone_number', '+91 98765 43210');
        $this->email = BusinessSetting::get('email', 'contact@thrumble.smallloan');
        $this->currency = BusinessSetting::get('currency', 'INR');
        $this->receipt_header = BusinessSetting::get('receipt_header', 'Trusted Gold Loan & Girvi Financing since 1998');
        $this->receipt_footer = BusinessSetting::get('receipt_footer', 'Subject to Mumbai Jurisdiction. Thank you for your business.');
        $this->default_interest_rate = (float) BusinessSetting::get('default_interest_rate', 1.50);
        $this->default_interest_method = BusinessSetting::get('default_interest_method', 'monthly');
        $this->default_loan_tenure_months = (int) BusinessSetting::get('default_loan_tenure_months', 12);

        $this->gold_rate_24k = (float) BusinessSetting::get('gold_rate_24k', 7450);
        $this->gold_rate_22k = (float) BusinessSetting::get('gold_rate_22k', 6850);
        $this->gold_rate_20k = (float) BusinessSetting::get('gold_rate_20k', 6250);
        $this->gold_rate_18k = (float) BusinessSetting::get('gold_rate_18k', 5600);
    }

    public function loadProfileSettings()
    {
        $user = auth()->user();
        if ($user) {
            $this->profile_name = $user->name;
            $this->profile_email = $user->email;
            $this->profile_mobile = $user->mobile ?? '';
        }
    }

    public function saveBusinessSettings()
    {
        $this->validate([
            'shop_name' => 'required|string|max:150',
            'shop_address' => 'required|string|max:300',
            'phone_number' => 'required|string|max:50',
            'default_interest_rate' => 'required|numeric|min:0.1',
            'default_loan_tenure_months' => 'required|integer|min:1',
        ]);

        BusinessSetting::set('shop_name', $this->shop_name);
        BusinessSetting::set('shop_address', $this->shop_address);
        BusinessSetting::set('phone_number', $this->phone_number);
        BusinessSetting::set('email', $this->email);
        BusinessSetting::set('currency', $this->currency);
        BusinessSetting::set('receipt_header', $this->receipt_header);
        BusinessSetting::set('receipt_footer', $this->receipt_footer);
        BusinessSetting::set('default_interest_rate', $this->default_interest_rate);
        BusinessSetting::set('default_interest_method', $this->default_interest_method);
        BusinessSetting::set('default_loan_tenure_months', $this->default_loan_tenure_months);

        session()->flash('success', 'Business settings updated successfully!');
    }

    public function saveGoldRates()
    {
        $this->validate([
            'gold_rate_24k' => 'required|numeric|min:100',
            'gold_rate_22k' => 'required|numeric|min:100',
            'gold_rate_20k' => 'required|numeric|min:100',
            'gold_rate_18k' => 'required|numeric|min:100',
        ]);

        BusinessSetting::set('gold_rate_24k', $this->gold_rate_24k);
        BusinessSetting::set('gold_rate_22k', $this->gold_rate_22k);
        BusinessSetting::set('gold_rate_20k', $this->gold_rate_20k);
        BusinessSetting::set('gold_rate_18k', $this->gold_rate_18k);

        session()->flash('success', 'Market gold valuation rates updated successfully!');
    }

    public function updateProfile()
    {
        $user = auth()->user();
        $this->validate([
            'profile_name' => 'required|string|max:100',
            'profile_email' => 'required|email|unique:users,email,' . $user->id,
            'profile_mobile' => 'nullable|string|max:20',
        ]);

        $user->name = $this->profile_name;
        $user->email = $this->profile_email;
        $user->mobile = $this->profile_mobile;
        $user->save();

        session()->flash('success', 'Your personal profile has been updated!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password provided is incorrect.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success', 'Password successfully changed!');
    }

    public function render()
    {
        return view('livewire.settings', get_object_vars($this))->layout('layouts.app', ['title' => 'Settings']);
    }
}
