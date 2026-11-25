<div class="space-y-4">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-slate-200">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">System Configuration & Business Settings</h2>
            <p class="text-xs text-slate-500">Configure store branding, receipt templates, daily gold market rates and profile security</p>
        </div>
    </div>

    <!-- Navigation Tabs Toolbar -->
    <div class="bg-white p-1.5 rounded border border-slate-200 shadow-2xs flex flex-wrap items-center gap-1.5 text-xs">
        <button wire:click="$set('activeTab', 'profile')"
                class="px-3 py-1.5 rounded font-medium transition cursor-pointer {{ $activeTab === 'profile' ? 'bg-amber-600 text-white font-semibold' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
            My Profile
        </button>
        <button wire:click="$set('activeTab', 'security')"
                class="px-3 py-1.5 rounded font-medium transition cursor-pointer {{ $activeTab === 'security' ? 'bg-amber-600 text-white font-semibold' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
            Security & Password
        </button>
        <button wire:click="$set('activeTab', 'business')"
                class="px-3 py-1.5 rounded font-medium transition cursor-pointer {{ $activeTab === 'business' ? 'bg-amber-600 text-white font-semibold' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
            Business Settings & Receipts
        </button>
        <button wire:click="$set('activeTab', 'rates')"
                class="px-3 py-1.5 rounded font-medium transition cursor-pointer {{ $activeTab === 'rates' ? 'bg-amber-600 text-white font-semibold' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
            Daily Gold Rates
        </button>
    </div>

    <!-- TAB 1: My Profile -->
    @if($activeTab === 'profile')
        <div class="bg-white rounded border border-slate-200 shadow-2xs p-4 max-w-xl">
            <h3 class="font-bold text-slate-900 text-sm mb-3">User Profile Information</h3>
            <form wire:submit.prevent="updateProfile" class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Your Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="name" class="input-compact">
                    @error('name') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" wire:model="email" class="input-compact">
                    @error('email') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mobile Contact</label>
                    <input type="text" wire:model="mobile" class="input-compact">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary">Save Profile Changes</button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 2: Security & Password -->
    @if($activeTab === 'security')
        <div class="bg-white rounded border border-slate-200 shadow-2xs p-4 max-w-xl">
            <h3 class="font-bold text-slate-900 text-sm mb-3">Change Account Password</h3>
            <form wire:submit.prevent="updatePassword" class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Current Password <span class="text-rose-500">*</span></label>
                    <input type="password" wire:model="current_password" class="input-compact" placeholder="••••••••">
                    @error('current_password') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">New Password <span class="text-rose-500">*</span></label>
                    <input type="password" wire:model="new_password" class="input-compact" placeholder="Minimum 6 characters">
                    @error('new_password') <span class="text-[10px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Confirm New Password <span class="text-rose-500">*</span></label>
                    <input type="password" wire:model="new_password_confirmation" class="input-compact" placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 3: Business Settings & Receipts -->
    @if($activeTab === 'business')
        <div class="bg-white rounded border border-slate-200 shadow-2xs p-4 max-w-2xl">
            <h3 class="font-bold text-slate-900 text-sm mb-3">Store Details & Default Loan Parameters</h3>
            <form wire:submit.prevent="updateBusinessSettings" class="space-y-3 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Store / Shop Name <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="shop_name" class="input-compact font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Contact Phone <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="phone_number" class="input-compact">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Store Address <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="shop_address" class="input-compact">
                </div>

                <!-- Receipt Customization -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-2.5 rounded border border-slate-200">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Receipt Header Note</label>
                        <input type="text" wire:model="receipt_header" class="input-compact" placeholder="Tagline or license #">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Receipt Footer Note</label>
                        <input type="text" wire:model="receipt_footer" class="input-compact" placeholder="Terms summary">
                    </div>
                </div>

                <!-- Loan Defaults -->
                <div class="bg-amber-50/50 p-3 rounded border border-amber-200/60 space-y-2">
                    <span class="text-[10px] font-bold text-amber-900 uppercase tracking-wider block">Default Loan Configuration</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Default Interest Rate (% pm)</label>
                            <input type="number" step="0.05" wire:model="default_interest_rate" class="input-compact font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Default Calculation Method</label>
                            <select wire:model="default_interest_method" class="input-compact font-medium">
                                <option value="monthly">Monthly Interest</option>
                                <option value="daily">Daily Interest</option>
                                <option value="annual">Annual Interest</option>
                                <option value="flat">Flat Interest</option>
                                <option value="reducing_balance">Reducing Balance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Default Tenure (Months)</label>
                            <input type="number" wire:model="default_loan_tenure_months" class="input-compact font-bold">
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary">Save Business Settings</button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 4: Daily Gold Valuation Rates -->
    @if($activeTab === 'rates')
        <div class="bg-white rounded border border-slate-200 shadow-2xs p-4 max-w-xl">
            <h3 class="font-bold text-slate-900 text-sm mb-1">Today's Market Gold Valuation Rates</h3>
            <p class="text-xs text-slate-500 mb-3">These rates are automatically applied when calculating jewellery collateral valuations during loan origination</p>

            <form wire:submit.prevent="updateGoldRates" class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-amber-50/50 p-2.5 rounded border border-amber-200">
                        <label class="block font-bold text-amber-900 mb-1">24K Pure Gold (99.9%) ₹/g</label>
                        <input type="number" step="1" wire:model="gold_rate_24k" class="input-compact font-bold text-slate-900">
                    </div>
                    <div class="bg-amber-50/50 p-2.5 rounded border border-amber-200">
                        <label class="block font-bold text-amber-900 mb-1">22K Standard Gold (91.6%) ₹/g</label>
                        <input type="number" step="1" wire:model="gold_rate_22k" class="input-compact font-bold text-slate-900">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 p-2.5 rounded border border-slate-200">
                        <label class="block font-bold text-slate-700 mb-1">20K Gold (83.3%) ₹/g</label>
                        <input type="number" step="1" wire:model="gold_rate_20k" class="input-compact font-bold text-slate-900">
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded border border-slate-200">
                        <label class="block font-bold text-slate-700 mb-1">18K Gold (75.0%) ₹/g</label>
                        <input type="number" step="1" wire:model="gold_rate_18k" class="input-compact font-bold text-slate-900">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary">Update Gold Rates</button>
                </div>
            </form>
        </div>
    @endif
</div>
