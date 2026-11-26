<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-slate-200">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Gold Pledge Management & Vault Inventory</h2>
            <p class="text-xs text-slate-500">Track pledged jewellery, locker packets, purity valuation and release statuses</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('loans') }}" class="btn-primary">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Pledge In New Loan
            </a>
        </div>
    </div>

    <!-- Vault Collateral Position Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3 rounded border border-slate-200 shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Pledged Items</span>
            <span class="text-lg font-bold text-slate-800">{{ $items->total() }} items</span>
            <span class="text-[10px] text-slate-400 block mt-0.5">In records</span>
        </div>
        <div class="bg-white p-3 rounded border border-slate-200 shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Net Gold Weight</span>
            <span class="text-lg font-bold text-amber-800">{{ number_format($totalNetGrams, 3) }} g</span>
            <span class="text-[10px] text-slate-400 block mt-0.5">Pure gold weight</span>
        </div>
        <div class="bg-white p-3 rounded border border-slate-200 shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Vault Collateral Valuation</span>
            <span class="text-lg font-bold text-slate-900">₹{{ number_format($totalValuation, 2) }}</span>
            <span class="text-[10px] text-slate-400 block mt-0.5">Based on market purity rates</span>
        </div>
        <div class="bg-white p-3 rounded border border-slate-200 shadow-2xs">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Vault Custody Status</span>
            <span class="text-sm font-bold text-emerald-700 flex items-center gap-1 mt-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Secured in Vault Lockers
            </span>
            <span class="text-[10px] text-slate-400 block mt-0.5">Sealed in safety packets</span>
        </div>
    </div>

    <!-- Search & Filters Toolbar -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-2xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center space-x-2 flex-1 min-w-[260px]">
            <div class="relative w-full max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search item, packet no, locker, customer, loan..."
                       class="input-compact pl-8">
            </div>
            @if($search)
                <button wire:click="$set('search', '')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>

        <div class="flex items-center space-x-2 flex-wrap">
            <div>
                <select wire:model.live="categoryFilter" class="input-compact w-28">
                    <option value="">All Categories</option>
                    <option value="Ring">Ring</option>
                    <option value="Chain">Chain</option>
                    <option value="Bangle">Bangle</option>
                    <option value="Necklace">Necklace</option>
                    <option value="Coin">Coin</option>
                    <option value="Earring">Earring</option>
                    <option value="Bracelet">Bracelet</option>
                    <option value="Mangalsutra">Mangalsutra</option>
                    <option value="Pendant">Pendant</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <select wire:model.live="purityFilter" class="input-compact w-24">
                    <option value="">All Purity</option>
                    <option value="24K">24K</option>
                    <option value="22K">22K</option>
                    <option value="20K">20K</option>
                    <option value="18K">18K</option>
                </select>
            </div>
            <div>
                <select wire:model.live="statusFilter" class="input-compact w-28">
                    <option value="">All Status</option>
                    <option value="Pledged">Pledged</option>
                    <option value="Released">Released</option>
                    <option value="Auctioned">Auctioned</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Pledges Table -->
    <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-compact">
                <thead>
                    <tr>
                        <th>Packet / Locker</th>
                        <th>Jewellery Description</th>
                        <th>Category</th>
                        <th>Customer / Loan</th>
                        <th>Gross Wt</th>
                        <th>Stone Wt</th>
                        <th>Net Gold Wt</th>
                        <th>Purity</th>
                        <th>Valuation</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <div class="font-bold text-amber-700 font-mono text-xs">{{ $item->packet_number ?? 'UNASSIGNED' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $item->locker_reference ?? 'No Locker Assigned' }}</div>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-900">{{ $item->item_name }}</div>
                                @if($item->remarks)
                                    <div class="text-[10px] text-slate-400 italic">{{ $item->remarks }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-slate-100 text-slate-700 border border-slate-200">{{ $item->category }}</span>
                            </td>
                            <td>
                                @if($item->customer)
                                    <div class="font-medium text-slate-900">{{ $item->customer->name }}</div>
                                    <div class="text-[10px] text-slate-400">
                                        @if($item->loan)
                                            <a href="{{ route('loans', ['search' => $item->loan->loan_no]) }}" class="text-amber-700 hover:underline">
                                                Loan: {{ $item->loan->loan_no }}
                                            </a>
                                        @else
                                            <span>Standalone Pledge</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="text-slate-700">{{ number_format($item->gross_weight, 3) }} g</td>
                            <td class="text-slate-500">{{ number_format($item->stone_weight, 3) }} g</td>
                            <td class="font-bold text-amber-800">{{ number_format($item->net_gold_weight, 3) }} g</td>
                            <td>
                                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold {{ $item->purity === '24K' ? 'bg-amber-100 text-amber-900' : ($item->purity === '22K' ? 'bg-yellow-50 text-yellow-800 border border-yellow-200' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $item->purity }}
                                </span>
                            </td>
                            <td class="font-bold text-slate-900">₹{{ number_format($item->valuation_amount, 2) }}</td>
                            <td>
                                @if($item->status === 'Pledged')
                                    <span class="badge bg-amber-50 text-amber-800 border border-amber-200">Pledged</span>
                                @elseif($item->status === 'Released')
                                    <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">Released</span>
                                @else
                                    <span class="badge bg-rose-50 text-rose-700 border border-rose-200">Auctioned</span>
                                @endif
                            </td>
                            <td class="text-right space-x-1">
                                <button wire:click="printTag({{ $item->id }})" class="px-2 py-1 bg-slate-50 hover:bg-slate-100 text-amber-700 border border-slate-200 rounded text-[11px] font-medium transition cursor-pointer" title="Print Vault Tag">
                                    Tag
                                </button>
                                <button wire:click="openEditModal({{ $item->id }})" class="px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded text-[11px] font-medium transition cursor-pointer" title="Edit Item Details">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-6 text-slate-400 text-xs">
                                No jewellery items found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $items->links() }}
        </div>
    </div>

    <!-- Edit Item Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-lg overflow-hidden my-6">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-slate-900 text-sm">Edit Pledged Jewellery Details</h3>
                    <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form wire:submit.prevent="updateItem" class="p-4 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Item Description <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="item_name" class="input-compact" placeholder="e.g. 22K Gold Bangle Set (2 Pcs)">
                            @error('item_name') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Category</label>
                            <select wire:model="category" class="input-compact">
                                <option value="Ring">Ring</option>
                                <option value="Chain">Chain</option>
                                <option value="Bangle">Bangle</option>
                                <option value="Necklace">Necklace</option>
                                <option value="Coin">Coin</option>
                                <option value="Earring">Earring</option>
                                <option value="Bracelet">Bracelet</option>
                                <option value="Mangalsutra">Mangalsutra</option>
                                <option value="Pendant">Pendant</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Purity</label>
                            <select wire:model.live="purity" class="input-compact">
                                <option value="24K">24K (99.9%)</option>
                                <option value="22K">22K (91.6%)</option>
                                <option value="20K">20K (83.3%)</option>
                                <option value="18K">18K (75.0%)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Gross Wt (g) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.001" wire:model.live="gross_weight" class="input-compact">
                            @error('gross_weight') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Stone Wt (g)</label>
                            <input type="number" step="0.001" wire:model.live="stone_weight" class="input-compact">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Net Gold Wt (g)</label>
                            <input type="text" readonly value="{{ $net_gold_weight }}" class="input-compact bg-amber-50 font-bold text-amber-900 font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Gold Rate/g (₹)</label>
                            <input type="number" step="1" wire:model.live="gold_rate_per_gram" class="input-compact">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Valuation (₹)</label>
                            <input type="text" readonly value="{{ number_format(floatval($valuation_amount), 2) }}" class="input-compact bg-slate-100 font-bold text-slate-900 font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Packet Number</label>
                            <input type="text" wire:model="packet_number" class="input-compact uppercase font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Locker / Storage Ref</label>
                            <input type="text" wire:model="locker_reference" class="input-compact" placeholder="e.g. Locker A - Tray 2">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Item Status</label>
                            <select wire:model="status" class="input-compact">
                                <option value="Pledged">Pledged</option>
                                <option value="Released">Released</option>
                                <option value="Auctioned">Auctioned</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Remarks</label>
                            <input type="text" wire:model="remarks" class="input-compact">
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showEditModal', false)" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Printable Jewellery Vault Tag Modal -->
    @if($showTagModal && $selectedItem)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-md overflow-hidden my-6">
                <div class="no-print px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-slate-900 text-sm">Vault Pledge Tag</h3>
                    <div class="flex items-center space-x-2">
                        <button onclick="window.print()" class="btn-primary py-1 text-xs">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print Tag
                        </button>
                        <button wire:click="$set('showTagModal', false)" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Printable Tag Box -->
                <div class="p-6">
                    <div class="border-2 border-dashed border-slate-400 rounded-lg p-4 bg-amber-50/20 text-xs">
                        <div class="text-center border-b border-slate-300 pb-2 mb-3">
                            <div class="font-bold text-sm text-slate-900 uppercase tracking-tight">
                                {{ \App\Models\BusinessSetting::get('shop_name', 'thrumble.smallloan') }}
                            </div>
                            <div class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">VAULT SECURITY PLEDGE TAG</div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mb-2 pb-2 border-b border-slate-200">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">Packet Number</span>
                                <span class="font-bold text-base text-amber-800 font-mono">{{ $selectedItem->packet_number ?? 'UNASSIGNED' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-500 uppercase block">Locker Reference</span>
                                <span class="font-bold text-sm text-slate-900 font-mono">{{ $selectedItem->locker_reference ?? 'Tray 1' }}</span>
                            </div>
                        </div>

                        <div class="space-y-1.5 mb-3 text-slate-800">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Item Description:</span>
                                <strong class="text-right">{{ $selectedItem->item_name }} ({{ $selectedItem->category }})</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Customer Name:</span>
                                <strong>{{ $selectedItem->customer->name ?? 'N/A' }}</strong>
                            </div>
                            @if($selectedItem->loan)
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Loan Number:</span>
                                    <strong class="font-mono text-amber-700">{{ $selectedItem->loan->loan_no }}</strong>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-slate-500">Gross Wt / Stone Wt:</span>
                                <span>{{ $selectedItem->gross_weight }}g / {{ $selectedItem->stone_weight }}g</span>
                            </div>
                            <div class="flex justify-between text-sm bg-white p-1 rounded border border-slate-200">
                                <span class="font-semibold text-slate-700">Net Gold Weight:</span>
                                <strong class="text-amber-800">{{ $selectedItem->net_gold_weight }} g ({{ $selectedItem->purity }})</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Assessed Valuation:</span>
                                <strong class="text-slate-900">₹{{ number_format($selectedItem->valuation_amount, 2) }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pledge Date:</span>
                                <span>{{ $selectedItem->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-300 flex justify-between text-[10px] text-slate-500">
                            <span>Status: <strong>{{ $selectedItem->status }}</strong></span>
                            <span>thrumble.smallloan Vault Safe</span>
                        </div>
                    </div>
                </div>

                <div class="no-print px-4 py-2.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end">
                    <button wire:click="$set('showTagModal', false)" class="btn-secondary">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
