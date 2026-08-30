<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\AuditLog;
use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['nisn'], // siswa login pakai NISN
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => 'siswa', // dipaksa dari server, TIDAK menerima role dari client
                'is_active' => true,
            ]);

            SiswaProfile::create([
                'user_id' => $user->id,
                'nisn' => $validated['nisn'],
                'kelas' => $validated['kelas'],
                'jurusan' => $validated['jurusan'],
                'angkatan' => $validated['angkatan'],
            ]);

            return $user;
        });

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            'description' => "Akun siswa baru terdaftar: {$user->username}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. Silakan login.',
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak aktif. Hubungi admin perpustakaan.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => "User login: {$user->username} ({$user->role})",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->role,
                ],
                'token' => $token,
            ],
        ]);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'description' => "User logout: {$user->username}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
            ],
        ]);
    }
}
