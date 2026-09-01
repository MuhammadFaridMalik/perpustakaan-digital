<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Rack;
use Illuminate\Http\Request;

class RackController extends Controller
{
    public function index()
    {
        $racks = Rack::withCount('books')->orderBy('code')->get();

        return view('master.racks', compact('racks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:racks,code'],
            'description' => ['nullable', 'string', 'max:100'],
        ]);

        $rack = Rack::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_rack',
            'description' => "Menambah rak: {$rack->code}",
        ]);

        return back()->with('success', 'Rak berhasil ditambahkan.');
    }

    public function update(Request $request, Rack $rack)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:racks,code,' . $rack->id],
            'description' => ['nullable', 'string', 'max:100'],
        ]);

        $rack->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_rack',
            'description' => "Mengubah rak menjadi: {$rack->code}",
        ]);

        return back()->with('success', 'Rak berhasil diperbarui.');
    }

    public function destroy(Rack $rack)
    {
        if ($rack->books()->exists()) {
            return back()->with('error', 'Rak tidak bisa dihapus karena masih dipakai oleh buku.');
        }

        $code = $rack->code;
        $rack->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_rack',
            'description' => "Menghapus rak: {$code}",
        ]);

        return back()->with('success', 'Rak berhasil dihapus.');
    }
}
