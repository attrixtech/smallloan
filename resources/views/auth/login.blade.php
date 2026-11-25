@extends('layouts.guest')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
    <!-- Header -->
    <div class="p-6 text-center border-b border-slate-100 bg-slate-50/70">
        <div class="w-12 h-12 rounded-lg bg-amber-500 text-white flex items-center justify-center font-bold text-xl mx-auto mb-2 shadow-xs">
            GL
        </div>
        <h1 class="text-base font-bold text-slate-800">{{ \App\Models\BusinessSetting::get('shop_name', 'WeBotApp Gold Loan & Girvi') }}</h1>
        <p class="text-xs text-slate-500 mt-0.5">Compact & Modern Gold Loan Management System</p>
    </div>

    <!-- Login Form -->
    <div class="p-6">
        @if($errors->any())
            <div class="mb-4 p-2.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" required
                       class="form-input text-xs"
                       value="{{ old('email', 'admin@webotapp.com') }}"
                       placeholder="user@example.com">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                       class="form-input text-xs"
                       value="admin123"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-1.5 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-amber-600 border-slate-300 focus:ring-amber-500">
                    <span>Remember me</span>
                </label>
                <span class="text-slate-400">v1.0.0</span>
            </div>

            <button type="submit" class="w-full btn-primary py-2 text-xs font-semibold mt-2">
                Sign In
            </button>
        </form>

        <!-- Quick 1-Click Demo Login Shortcuts -->
        <div class="mt-5 pt-4 border-t border-slate-100">
            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-center mb-2">
                Quick Demo Access
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button type="button"
                        onclick="document.getElementById('email').value='admin@webotapp.com'; document.getElementById('password').value='password123'; this.form.submit();"
                        class="px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded text-[11px] font-medium text-center transition">
                    👤 Login as Admin
                </button>
                <button type="button"
                        onclick="document.getElementById('email').value='staff@webotapp.com'; document.getElementById('password').value='password123'; this.form.submit();"
                        class="px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded text-[11px] font-medium text-center transition">
                    👥 Login as Staff
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
