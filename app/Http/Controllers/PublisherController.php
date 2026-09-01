<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function index()
    {
        $publishers = Publisher::withCount('books')->orderBy('name')->get();

        return view('master.publishers', compact('publishers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $publisher = Publisher::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_publisher',
            'description' => "Menambah penerbit: {$publisher->name}",
        ]);

        return back()->with('success', 'Penerbit berhasil ditambahkan.');
    }

    public function update(Request $request, Publisher $publisher)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $publisher->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_publisher',
            'description' => "Mengubah penerbit menjadi: {$publisher->name}",
        ]);

        return back()->with('success', 'Penerbit berhasil diperbarui.');
    }

    public function destroy(Publisher $publisher)
    {
        if ($publisher->books()->exists()) {
            return back()->with('error', 'Penerbit tidak bisa dihapus karena masih dipakai oleh buku.');
        }

        $name = $publisher->name;
        $publisher->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_publisher',
            'description' => "Menghapus penerbit: {$name}",
        ]);

        return back()->with('success', 'Penerbit berhasil dihapus.');
    }
}
