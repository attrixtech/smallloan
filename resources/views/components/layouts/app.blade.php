<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'WeBotApp' }} - Gold Loan Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#f1f5f9] text-slate-800 antialiased font-sans text-[13px] selection:bg-amber-100 selection:text-amber-900 min-h-screen flex flex-col">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="no-print w-60 bg-white border-r border-slate-200 flex flex-col shrink-0 z-20">
            <!-- Brand -->
            <div class="h-14 px-4 flex items-center justify-between border-b border-slate-200 bg-slate-50/50">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-semibold text-slate-800 tracking-tight">
                    <div class="w-8 h-8 rounded bg-amber-500 flex items-center justify-center text-white shadow-xs font-bold text-sm">
                        GL
                    </div>
                    <div>
                        <div class="text-[13px] font-bold leading-none text-slate-900 tracking-tight">WeBotApp Girvi</div>
                        <div class="text-[10px] text-slate-500 font-normal leading-tight mt-0.5">Gold Loan Manager</div>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-2.5 py-3 space-y-1 text-xs">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                        ['route' => 'customers', 'label' => 'Customers', 'icon' => 'users'],
                        ['route' => 'pledges', 'label' => 'Gold Pledges', 'icon' => 'gem'],
                        ['route' => 'loans', 'label' => 'Loans', 'icon' => 'file-text'],
                        ['route' => 'payments', 'label' => 'Payments', 'icon' => 'credit-card'],
                        ['route' => 'renewals', 'label' => 'Renewals', 'icon' => 'refresh-cw'],
                        ['route' => 'gold-release', 'label' => 'Gold Release', 'icon' => 'shield-check'],
                        ['route' => 'reports', 'label' => 'Reports', 'icon' => 'bar-chart-2'],
                        ['route' => 'users', 'label' => 'Users', 'icon' => 'user-check', 'adminOnly' => true],
                        ['route' => 'settings', 'label' => 'Settings', 'icon' => 'settings'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @if(!isset($item['adminOnly']) || (auth()->check() && auth()->user()->role === 'admin'))
                        @php
                            $isActive = request()->routeIs($item['route'].'*');
                        @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded font-medium transition {{ $isActive ? 'bg-amber-50 text-amber-900 border border-amber-200 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            @if($item['icon'] === 'home')
                                <svg class="w-4 h-4 {{ $isActive ? 'text-amber-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            @elseif($item['icon'] === 'users')
                                <svg class="w-4 h-4 {{ $isActive ? 'text-amber-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            @elseif($item['icon'] === 'gem')
                                <svg class="w-4 h-4 {{ $isActive ? 'text-amber-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @elseif($item['icon'] === 'file-text')
                                <svg class="w-4 h-4 {{ $isActive ? 'text-amber-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @elseif($item['icon'] === 'credit-card')
                                <svg class="w-4 h-4 {{ $isActive ? 'text-amber-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            @elseif($item['icon'] === 'refresh-cw')
                                <svg class="w-4 h-4 {{ $isActive ? 'text-amber-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            @elseif($item['icon'] === 'shield-check')
                                <svg class="w-4 h-4 {{ $isActive ? 'text-amber-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            @elseif($item['icon'] === 'bar-chart-2')
                                <svg class="w-4 h-4 {{ $isActive ? 'text-amber-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            @elseif($item['icon'] === 'user-check')
                                <svg class="w-4 h-4 {{ $isActive ? 'text-amber-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            @elseif($item['icon'] === 'settings')
                                <svg class="w-4 h-4 {{ $isActive ? 'text-amber-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @endif
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <!-- Bottom User Profile & Shop info -->
            <div class="p-3 border-t border-slate-200 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                            {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'A' }}
                        </div>
                        <div class="truncate">
                            <div class="font-medium text-xs text-slate-800 truncate">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</div>
                            <div class="text-[10px] text-slate-500 capitalize">{{ auth()->check() ? auth()->user()->role : 'Administrator' }}</div>
                        </div>
                    </div>
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Logout" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-slate-100 transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <header class="no-print h-14 bg-white border-b border-slate-200 px-5 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-4">
                    <div class="text-xs font-semibold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ \App\Models\BusinessSetting::get('shop_name', 'WeBotApp Gold Loan & Girvi') }}</span>
                    </div>

                    <!-- Live Gold Rate Ticker -->
                    <div class="hidden lg:flex items-center gap-2 px-2.5 py-1 bg-amber-50/80 border border-amber-200/80 rounded text-[11px] text-amber-900">
                        <span class="font-bold text-amber-700">Rates Today:</span>
                        <span>24K: ₹{{ number_format(\App\Models\BusinessSetting::get('gold_rate_24k', 7850)) }}/g</span>
                        <span class="text-amber-300">•</span>
                        <span>22K: ₹{{ number_format(\App\Models\BusinessSetting::get('gold_rate_22k', 7250)) }}/g</span>
                        <span class="text-amber-300">•</span>
                        <span>18K: ₹{{ number_format(\App\Models\BusinessSetting::get('gold_rate_18k', 5950)) }}/g</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-[11px] text-slate-500 hidden sm:inline-block">
                        {{ now()->format('l, d M Y') }}
                    </span>

                    <a href="{{ route('loans') }}?action=new" class="btn-primary flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>New Loan</span>
                    </a>

                    <a href="{{ route('payments') }}?action=new" class="btn-secondary flex items-center gap-1.5 border-slate-300">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Receive Payment</span>
                    </a>
                </div>
            </header>

            <!-- Dynamic Body -->
            <main class="flex-1 overflow-y-auto p-5 bg-[#f4f6f8]">
                <!-- Flash Notification Banner -->
                @if(session('success'))
                    <div class="mb-4 px-3.5 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded text-xs flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 px-3.5 py-2 bg-rose-50 border border-rose-200 text-rose-800 rounded text-xs flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800">&times;</button>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
