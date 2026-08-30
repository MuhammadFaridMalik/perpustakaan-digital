@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="max-w-lg">
        <p class="text-sm text-muted mb-6">Pengaturan ini berlaku untuk semua transaksi peminjaman di masa mendatang.</p>

        <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            @foreach ($settings as $setting)
                <div>
                    <label class="block text-sm font-medium mb-1">{{ $setting->description }}</label>
                    <input type="number" name="values[{{ $setting->key }}]" value="{{ old('values.' . $setting->key, $setting->value) }}" required min="0"
                           class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>
            @endforeach

            <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded transition-colors">
                Simpan Pengaturan
            </button>
        </form>
    </div>
@endsection
