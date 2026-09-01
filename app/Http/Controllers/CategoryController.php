<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('books')->orderBy('name')->get();

        return view('master.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:categories,name'],
        ]);

        $category = Category::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_category',
            'description' => "Menambah kategori: {$category->name}",
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:categories,name,' . $category->id],
        ]);

        $category->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_category',
            'description' => "Mengubah kategori menjadi: {$category->name}",
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh buku.');
        }

        $name = $category->name;
        $category->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_category',
            'description' => "Menghapus kategori: {$name}",
        ]);

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
