<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMemberRequest;
use App\Models\AuditLog;
use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = User::where('role', 'siswa')
            ->with('siswaProfile')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', '%' . $request->search . '%')
                       ->orWhereHas('siswaProfile', fn ($p) => $p->where('nisn', 'like', '%' . $request->search . '%'));
                });
            })
            ->when($request->filled('kelas'), function ($q) use ($request) {
                $q->whereHas('siswaProfile', fn ($p) => $p->where('kelas', $request->kelas));
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('is_active', $request->status === 'aktif');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $daftarKelas = SiswaProfile::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('members.index', compact('members', 'daftarKelas'));
    }

    public function show(User $member)
    {
        abort_if($member->role !== 'siswa', 404);
        $member->load('siswaProfile');

        return view('members.show', compact('member'));
    }

    public function edit(User $member)
    {
        abort_if($member->role !== 'siswa', 404);
        $member->load('siswaProfile');

        return view('members.edit', compact('member'));
    }

    public function update(UpdateMemberRequest $request, User $member)
    {
        abort_if($member->role !== 'siswa', 404);

        $validated = $request->validated();

        $member->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'username' => $validated['nisn'], // username selalu disinkronkan dengan NISN
        ]);

        $member->siswaProfile()->updateOrCreate(
            ['user_id' => $member->id],
            [
                'nisn' => $validated['nisn'],
                'kelas' => $validated['kelas'],
                'jurusan' => $validated['jurusan'],
                'angkatan' => $validated['angkatan'],
            ]
        );

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_member',
            'description' => "Mengubah data anggota: {$member->name} (NISN: {$validated['nisn']})",
        ]);

        return redirect()->route('members.show', $member)->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function toggleActive(User $member)
    {
        abort_if($member->role !== 'siswa', 404);

        $member->is_active = ! $member->is_active;
        $member->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $member->is_active ? 'activate_member' : 'deactivate_member',
            'description' => ($member->is_active ? 'Mengaktifkan' : 'Menonaktifkan') . " akun anggota: {$member->name}",
        ]);

        $message = $member->is_active ? 'Akun anggota diaktifkan kembali.' : 'Akun anggota dinonaktifkan.';

        return redirect()->route('members.show', $member)->with('success', $message);
    }
}
