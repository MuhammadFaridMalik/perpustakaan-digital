<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminManagementController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->orderBy('name')
            ->paginate(10);

        return view('admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admins.create');
    }

    public function store(StoreAdminRequest $request)
    {
        $validated = $request->validated();

        $admin = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'is_active' => true,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_admin',
            'description' => "Membuat akun admin baru: {$admin->username}",
        ]);

        return redirect()->route('admins.index')->with('success', 'Akun admin berhasil dibuat.');
    }

    public function edit(User $admin)
    {
        abort_if($admin->role !== 'admin', 404);

        return view('admins.edit', compact('admin'));
    }

    public function update(UpdateAdminRequest $request, User $admin)
    {
        abort_if($admin->role !== 'admin', 404);

        $validated = $request->validated();

        $admin->name = $validated['name'];
        $admin->username = $validated['username'];
        $admin->email = $validated['email'] ?? null;

        if (! empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_admin',
            'description' => "Mengubah data admin: {$admin->username}",
        ]);

        return redirect()->route('admins.index')->with('success', 'Data admin berhasil diperbarui.');
    }

    public function toggleActive(User $admin)
    {
        abort_if($admin->role !== 'admin', 404);

        $admin->is_active = ! $admin->is_active;
        $admin->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $admin->is_active ? 'activate_admin' : 'deactivate_admin',
            'description' => ($admin->is_active ? 'Mengaktifkan' : 'Menonaktifkan') . " akun admin: {$admin->username}",
        ]);

        $message = $admin->is_active ? 'Akun admin diaktifkan kembali.' : 'Akun admin dinonaktifkan.';

        return redirect()->route('admins.index')->with('success', $message);
    }
}
