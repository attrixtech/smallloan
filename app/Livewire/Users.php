<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Users extends Component
{
    public $search = '';

    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public $userId;
    public $name = '';
    public $email = '';
    public $mobile = '';
    public $role = 'staff';
    public $is_active = true;
    public $password = '';
    public $password_confirmation = '';

    public $selectedUser = null;

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['userId', 'name', 'email', 'mobile', 'password', 'password_confirmation']);
        $this->role = 'staff';
        $this->is_active = true;
        $this->showCreateModal = true;
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string|max:20',
            'role' => 'required|in:admin,staff',
            'password' => 'required|min:6|same:password_confirmation',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'password' => Hash::make($this->password),
        ]);

        $this->showCreateModal = false;
        session()->flash('success', "User account for {$this->name} created successfully!");
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->mobile = $user->mobile;
        $this->role = $user->role;
        $this->is_active = (bool) $user->is_active;
        $this->password = '';
        $this->password_confirmation = '';

        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'mobile' => 'nullable|string|max:20',
            'role' => 'required|in:admin,staff',
            'password' => 'nullable|min:6|same:password_confirmation',
        ]);

        $user = User::findOrFail($this->userId);
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->showEditModal = false;
        session()->flash('success', "User account updated successfully!");
    }

    public function confirmDelete($id)
    {
        if ($id == auth()->id()) {
            session()->flash('error', 'You cannot delete your own account!');
            return;
        }

        $this->selectedUser = User::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if (!$this->selectedUser) return;

        if ($this->selectedUser->id == auth()->id()) {
            session()->flash('error', 'You cannot delete your own account!');
            $this->showDeleteModal = false;
            return;
        }

        $name = $this->selectedUser->name;
        $this->selectedUser->delete();
        $this->showDeleteModal = false;
        session()->flash('success', "User account {$name} deleted successfully.");
    }

    public function render()
    {
        $query = User::query();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('mobile', 'like', "%{$this->search}%");
            });
        }

        $users = $query->orderBy('id', 'asc')->get();

        return view('livewire.users', array_merge(get_object_vars($this), [
            'users' => $users,
        ]))->layout('layouts.app', ['title' => 'User Management']);
    }
}
