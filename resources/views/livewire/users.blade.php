<div class="space-y-4">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1 border-b border-slate-200">
        <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight">Staff & User Management</h2>
            <p class="text-xs text-slate-500">Manage administrator and operator access credentials, role authorizations, and staff accounts</p>
        </div>
        <div class="flex items-center space-x-2">
            <button wire:click="openCreateModal" class="btn-primary">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Add User Account
            </button>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-2xs flex items-center justify-between text-xs">
        <div class="relative w-full max-w-sm">
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, email, mobile..."
                   class="input-compact pl-8">
        </div>
        @if($search)
            <button wire:click="$set('search', '')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        @endif
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left table-compact">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email Address</th>
                        <th>Mobile</th>
                        <th>System Role</th>
                        <th>Account Status</th>
                        <th>Created At</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="font-semibold text-slate-900 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[11px] font-bold text-slate-700">
                                        {{ substr($user->name, 0, 1) }}
                                    </span>
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="font-mono text-slate-700 text-xs">{{ $user->email }}</td>
                            <td>{{ $user->mobile ?? '-' }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-amber-50 text-amber-800 border border-amber-200 font-bold uppercase">
                                        Administrator
                                    </span>
                                @else
                                    <span class="badge bg-slate-100 text-slate-700 border border-slate-200 font-semibold uppercase">
                                        Staff Operator
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                                @else
                                    <span class="badge bg-rose-50 text-rose-700 border border-rose-200">Suspended</span>
                                @endif
                            </td>
                            <td class="text-slate-500 text-[11px]">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</td>
                            <td class="text-right space-x-1">
                                <button wire:click="openEditModal({{ $user->id }})" class="px-2 py-1 bg-slate-50 hover:bg-slate-100 text-amber-700 border border-slate-200 rounded text-[11px] font-medium transition cursor-pointer">
                                    Edit
                                </button>
                                @if($user->id !== auth()->id())
                                    <button wire:click="confirmDelete({{ $user->id }})" class="px-2 py-1 bg-slate-50 hover:bg-rose-50 text-rose-600 border border-slate-200 rounded text-[11px] font-medium transition cursor-pointer">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-slate-400 text-xs">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create User Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-md overflow-hidden p-4 text-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <h3 class="font-bold text-slate-900 text-sm">Add New User Account</h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="createUser" class="space-y-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="name" class="input-compact" placeholder="Staff Name">
                        @error('name') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" wire:model="email" class="input-compact" placeholder="staff@example.com">
                        @error('email') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Mobile</label>
                            <input type="text" wire:model="mobile" class="input-compact" placeholder="98XXXXXXXX">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">System Role <span class="text-rose-500">*</span></label>
                            <select wire:model="role" class="input-compact font-semibold">
                                <option value="staff">Staff Operator</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Password <span class="text-rose-500">*</span></label>
                            <input type="password" wire:model="password" class="input-compact" placeholder="••••••••">
                            @error('password') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Confirm Password <span class="text-rose-500">*</span></label>
                            <input type="password" wire:model="password_confirmation" class="input-compact" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 pt-1">
                        <label class="flex items-center space-x-2 font-medium text-slate-700 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Account is active & permitted to log in</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-2 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit User Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-md overflow-hidden p-4 text-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <h3 class="font-bold text-slate-900 text-sm">Edit User Account</h3>
                    <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="updateUser" class="space-y-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="name" class="input-compact">
                        @error('name') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" wire:model="email" class="input-compact">
                        @error('email') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Mobile</label>
                            <input type="text" wire:model="mobile" class="input-compact">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">System Role</label>
                            <select wire:model="role" class="input-compact font-semibold">
                                <option value="staff">Staff Operator</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 bg-slate-50 p-2.5 rounded border border-slate-200">
                        <div class="col-span-2 text-[10px] text-slate-500">Leave password blank if keeping current password.</div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">New Password</label>
                            <input type="password" wire:model="password" class="input-compact" placeholder="••••••••">
                            @error('password') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Confirm Password</label>
                            <input type="password" wire:model="password_confirmation" class="input-compact" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 pt-1">
                        <label class="flex items-center space-x-2 font-medium text-slate-700 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Account is active</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-2 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showEditModal', false)" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal && $selectedUser)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg border border-slate-200 shadow-xl w-full max-w-sm overflow-hidden p-4 text-xs space-y-3">
                <h3 class="font-bold text-slate-900 text-sm">Delete User Account</h3>
                <p class="text-slate-600">
                    Are you sure you want to delete user <strong>{{ $selectedUser->name }}</strong> ({{ $selectedUser->email }})?
                </p>
                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-200">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">Cancel</button>
                    <button wire:click="deleteUser" class="btn-danger">Delete User</button>
                </div>
            </div>
        </div>
    @endif
</div>
