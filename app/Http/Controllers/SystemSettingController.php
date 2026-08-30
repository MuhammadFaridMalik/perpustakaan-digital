<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('key')->get();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'values' => ['required', 'array'],
            'values.*' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($validated['values'] as $key => $value) {
            SystemSetting::where('key', $key)->update(['value' => $value]);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_settings',
            'description' => 'Mengubah pengaturan sistem.',
        ]);

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
