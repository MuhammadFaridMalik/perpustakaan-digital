<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load('siswaProfile');

        return response()->json([
            'success' => true,
            'data' => $this->formatProfile($user),
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => $this->formatProfile($user->fresh('siswaProfile')),
        ]);
    }

    private function formatProfile($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'academic' => $user->siswaProfile ? [
                'nisn' => $user->siswaProfile->nisn,
                'kelas' => $user->siswaProfile->kelas,
                'jurusan' => $user->siswaProfile->jurusan,
                'angkatan' => $user->siswaProfile->angkatan,
            ] : null,
        ];
    }
}
