<div class="space-y-4">
    <!-- Top Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-slate-200">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Gold Release & Jewellery Handover</h2>
            <p class="text-xs text-slate-500">Secure handover of pledged collateral upon settlement, accidental release prevention & handover certificates</p>
        </div>
        <div class="flex items-center space-x-2">
            <button wire:click="openCreateModal" class="btn-primary">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                Release Gold Jewellery
            </button>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-2xs flex items-center justify-between text-xs">
        <div class="relative w-full max-w-sm">
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by release no, recipient, loan no..."
                   class="input-compact pl-8">
        </div>
        @if($search)
            <button wire:click="$set('search', '')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        @endif
    </div>

    <!-- Gold Releases Table -->
    <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-compact">
                <thead>
                    <tr>
                        <th>Release #</th>
                        <th>Release Date</th>
                        <th>Loan Contract</th>
                        <th>Borrower</th>
                        <th>Handed Over To</th>
                        <th>Relation</th>
                        <th>Items</th>
                        <th>Net Weight</th>
                        <th>Authorization</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($releases as $rel)
                        <tr>
                            <td class="font-semibold text-slate-800 font-mono">
                                <a href="javascript:void(0)" wire:click="printRelease({{ $rel->id }})" class="text-amber-700 hover:underline">
                                    {{ $rel->release_no }}
                                </a>
                            </td>
                            <td class="text-slate-600 text-[11px]">{{ $rel->release_date->format('d/m/Y') }}</td>
                            <td class="font-semibold text-amber-700 font-mono">{{ $rel->loan->loan_no ?? '-' }}</td>
                            <td>
                                <div class="font-medium text-slate-900">{{ $rel->customer->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $rel->customer->mobile ?? '' }}</div>
                            </td>
                            <td class="font-semibold text-slate-800">{{ $rel->handed_over_to }}</td>
                            <td>
                                <span class="badge uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $rel->recipient_relation }}
                                </span>
                            </td>
                            <td class="text-slate-700">{{ $rel->items_count }} items</td>
                            <td class="font-bold text-amber-800">{{ number_format($rel->total_net_weight, 3) }} g</td>
                            <td>
                                @if($rel->is_authorized_override)
                                    <span class="badge bg-rose-50 text-rose-700 border border-rose-200">Admin Override</span>
                                @else
                                    <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">Settled Release</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <button wire:click="printRelease({{ $rel->id }})" class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded text-[11px] font-medium transition cursor-pointer">
                                    Certificate
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-6 text-slate-400 text-xs">
                                No gold release records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $releases->links() }}
        </div>
    </div>

    <!-- Modal: Release Gold Jewellery -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-2xl overflow-hidden my-6">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Release Pledged Gold Jewellery</h3>
                        <p class="text-[11px] text-slate-500">Certificate No: <strong class="text-amber-800 font-mono">{{ $release_no }}</strong></p>
                    </div>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="processRelease" class="p-4 space-y-4 max-h-[80vh] overflow-y-auto text-xs">
                    <!-- Loan Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Select Loan Contract <span class="text-rose-500">*</span></label>
                            <select wire:model.live="loan_id" class="input-compact font-medium">
                                <option value="">-- Choose Loan Contract --</option>
                                @foreach($eligibleLoans as $el)
                                    <option value="{{ $el->id }}">
                                        {{ $el->loan_no }} - {{ $el->customer->name ?? '' }} [{{ $el->status }} - Bal: ₹{{ number_format($el->current_balance_principal, 0) }}]
                                    </option>
                                @endforeach
                            </select>
                            @error('loan_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Handover Date <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="release_date" class="input-compact">
                        </div>
                    </div>

                    <!-- Selected Loan & Safeguard Warning -->
                    @if($selectedLoan)
                        @php
                            $isSettled = ($selectedLoan->status === 'Closed' || floatval($selectedLoan->current_balance_principal) <= 0);
                        @endphp

                        @if(!$isSettled)
                            <div class="p-3 bg-rose-50 border border-rose-200 rounded space-y-2">
                                <div class="flex items-center gap-2 text-rose-800 font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>ACCIDENTAL RELEASE SAFEGUARD TRIGGERED</span>
                                </div>
                                <p class="text-rose-700 text-[11px]">
                                    This loan is NOT yet settled! Outstanding Principal Balance: <strong>₹{{ number_format($selectedLoan->current_balance_principal, 2) }}</strong>.
                                    Releasing gold prior to full settlement requires authorized administrative override.
                                </p>
                                <div class="pt-1">
                                    <label class="flex items-center space-x-2 text-rose-900 font-semibold cursor-pointer">
                                        <input type="checkbox" wire:model.live="is_authorized_override" class="rounded text-rose-600 focus:ring-rose-500">
                                        <span>Confirm Authorized Override to Release Collateral</span>
                                    </label>
                                </div>
                                @if($is_authorized_override)
                                    <div>
                                        <label class="block text-[11px] font-semibold text-rose-900 mb-1">Authorized Override Justification <span class="text-rose-500">*</span></label>
                                        <input type="text" wire:model="override_reason" class="input-compact border-rose-300" placeholder="e.g. Approved by Director / Substitution of collateral">
                                        @error('override_reason') <span class="text-[10px] text-rose-600">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                                @error('is_authorized_override') <span class="text-[10px] text-rose-600 block">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded text-emerald-800 text-xs flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Verified: Loan is fully settled and cleared. Safe to release jewellery to borrower.</span>
                            </div>
                        @endif

                        <!-- Collateral Items To Be Released -->
                        <div class="border border-slate-200 rounded p-2.5 bg-slate-50 space-y-1.5">
                            <span class="font-bold text-slate-800 block text-[11px]">Items in Vault to be Handed Over ({{ $selectedLoan->pledgedItems->count() }} items &bull; {{ number_format($selectedLoan->total_net_weight, 3) }} g)</span>
                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                @foreach($selectedLoan->pledgedItems as $pi)
                                    <div class="bg-white p-1.5 rounded border border-slate-200 flex justify-between">
                                        <span>{{ $pi->item_name }} ({{ $pi->purity }})</span>
                                        <strong>{{ number_format($pi->net_gold_weight, 3) }} g</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Recipient Details -->
                    <div class="bg-slate-50 p-3 rounded border border-slate-200 space-y-3">
                        <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block">Handover Recipient Verification</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Handed Over To (Name) <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="handed_over_to" class="input-compact" placeholder="Recipient full name">
                                @error('handed_over_to') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Recipient Relationship <span class="text-rose-500">*</span></label>
                                <select wire:model="recipient_relation" class="input-compact">
                                    <option value="self">Borrower Self</option>
                                    <option value="spouse">Spouse</option>
                                    <option value="nominee">Nominee</option>
                                    <option value="authorized_rep">Authorized Representative</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Recipient ID Proof</label>
                                <input type="text" wire:model="recipient_id_proof" class="input-compact" placeholder="Aadhaar / Voter / Driving License">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Signature / Handover Note</label>
                                <input type="text" wire:model="recipient_signature_note" class="input-compact" placeholder="Acknowledged in person with original receipt">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Confirm Handover & Release</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Printable Gold Release Certificate Modal -->
    @if($showPrintModal && $selectedRelease)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-2xl overflow-hidden my-6">
                <div class="px-4 py-2 border-b border-slate-200 flex items-center justify-between bg-slate-50 no-print">
                    <span class="font-bold text-xs text-slate-800">Printable Gold Release Certificate</span>
                    <button wire:click="$set('showPrintModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Printable Area -->
                <div class="p-6 bg-white text-slate-900 text-xs font-sans space-y-4 print-receipt-container" id="printableGoldRelease">
                    <!-- Shop Header -->
                    <div class="text-center pb-3 border-b-2 border-slate-900">
                        <h2 class="text-base font-extrabold uppercase tracking-wide text-slate-900">
                            {{ \App\Models\BusinessSetting::get('shop_name', 'WeBotApp Gold Loan & Girvi') }}
                        </h2>
                        <p class="text-[11px] text-slate-600">{{ \App\Models\BusinessSetting::get('shop_address', 'Main Road, Zaveri Bazaar, Mumbai') }}</p>
                        <p class="text-[11px] text-slate-600">Phone: {{ \App\Models\BusinessSetting::get('phone_number', '+91 98765 43210') }}</p>
                        <div class="inline-block px-3 py-0.5 mt-2 rounded bg-slate-100 text-[11px] font-bold tracking-wider uppercase border border-slate-300">
                            Certificate of Gold Jewellery Release & Handover
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <div>
                            <p><strong>Release Certificate No:</strong> <span class="font-mono font-bold">{{ $selectedRelease->release_no }}</span></p>
                            <p><strong>Loan Contract Ref:</strong> <span class="font-mono font-bold">{{ $selectedRelease->loan->loan_no ?? '-' }}</span></p>
                        </div>
                        <div class="text-right">
                            <p><strong>Handover Date:</strong> {{ $selectedRelease->release_date->format('d/m/Y') }}</p>
                            <p><strong>Status:</strong> <span class="font-semibold text-emerald-700">Released from Vault</span></p>
                        </div>
                    </div>

                    <!-- Borrower & Recipient Info -->
                    <div class="grid grid-cols-2 gap-4 border border-slate-300 p-3 rounded">
                        <div>
                            <span class="font-bold block mb-1 text-slate-700">Borrower Information</span>
                            <p><strong>Name:</strong> {{ $selectedRelease->customer->name ?? '' }}</p>
                            <p><strong>Mobile:</strong> {{ $selectedRelease->customer->mobile ?? '' }}</p>
                            <p><strong>Address:</strong> {{ $selectedRelease->customer->address ?? '' }}</p>
                        </div>
                        <div>
                            <span class="font-bold block mb-1 text-slate-700">Recipient Details</span>
                            <p><strong>Handed Over To:</strong> <span class="font-bold">{{ $selectedRelease->handed_over_to }}</span></p>
                            <p><strong>Relationship:</strong> {{ strtoupper($selectedRelease->recipient_relation) }}</p>
                            <p><strong>ID Verification:</strong> {{ $selectedRelease->recipient_id_proof ?? 'Verified' }}</p>
                        </div>
                    </div>

                    <!-- Released Items Table -->
                    <div>
                        <h4 class="font-bold text-xs uppercase tracking-wider mb-1">Released Jewellery Items (Vault Return)</h4>
                        <table class="w-full text-left border border-slate-300 text-[11px]">
                            <thead class="bg-slate-100 border-b border-slate-300">
                                <tr>
                                    <th class="p-1.5 border-r border-slate-300">Packet</th>
                                    <th class="p-1.5 border-r border-slate-300">Item Description</th>
                                    <th class="p-1.5 border-r border-slate-300">Purity</th>
                                    <th class="p-1.5 border-r border-slate-300">Gross Wt</th>
                                    <th class="p-1.5 border-r border-slate-300">Stone Wt</th>
                                    <th class="p-1.5">Net Gold Wt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedRelease->loan->pledgedItems as $item)
                                    <tr class="border-b border-slate-200">
                                        <td class="p-1.5 border-r border-slate-200 font-mono">{{ $item->packet_number }}</td>
                                        <td class="p-1.5 border-r border-slate-200">{{ $item->item_name }}</td>
                                        <td class="p-1.5 border-r border-slate-200">{{ $item->purity }}</td>
                                        <td class="p-1.5 border-r border-slate-200">{{ number_format($item->gross_weight, 3) }} g</td>
                                        <td class="p-1.5 border-r border-slate-200">{{ number_format($item->stone_weight, 3) }} g</td>
                                        <td class="p-1.5 font-bold">{{ number_format($item->net_gold_weight, 3) }} g</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 font-bold border-t border-slate-300">
                                <tr>
                                    <td colspan="5" class="p-1.5 text-right border-r border-slate-300">TOTAL NET GOLD RELEASED:</td>
                                    <td class="p-1.5 text-amber-900">{{ number_format($selectedRelease->total_net_weight, 3) }} grams</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Handover Declaration -->
                    <div class="text-[10px] text-slate-600 leading-tight space-y-1 border border-slate-200 p-2.5 rounded bg-slate-50">
                        <p class="font-bold text-slate-800">ACKNOWLEDGEMENT & DISCHARGE RECEIPT:</p>
                        <p>I hereby acknowledge that I have received the above listed gold jewellery articles in full, intact condition without damage. I confirm that all claims relating to Loan {{ $selectedRelease->loan->loan_no ?? '' }} stand fully resolved and discharged.</p>
                    </div>

                    <!-- Signatures -->
                    <div class="grid grid-cols-2 gap-8 pt-8">
                        <div class="text-center">
                            <div class="border-t border-slate-400 pt-1">
                                <span class="font-semibold text-[11px]">Recipient's Signature</span>
                                <div class="text-[9px] text-slate-400">Received articles in good order</div>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="border-t border-slate-400 pt-1">
                                <span class="font-semibold text-[11px]">Authorized Vault Custodian</span>
                                <div class="text-[9px] text-slate-400">{{ $selectedRelease->processor->name ?? 'Store Manager' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 border-t border-slate-200 flex items-center justify-end space-x-2 bg-slate-50 no-print">
                    <button type="button" wire:click="$set('showPrintModal', false)" class="btn-secondary">Close</button>
                    <button type="button" onclick="window.print()" class="btn-primary">Print Certificate</button>
                </div>
            </div>
        </div>
    @endif
</div>
