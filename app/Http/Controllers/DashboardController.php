<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAdmin = User::where('role', 'admin')->count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $akunNonaktif = User::where('is_active', false)->count();

        $recentLogs = AuditLog::with('user')
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('dashboard.index', compact('totalAdmin', 'totalSiswa', 'akunNonaktif', 'recentLogs'));
    }
}
