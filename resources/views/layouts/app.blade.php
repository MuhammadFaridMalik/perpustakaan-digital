<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin') — Perpustakaan Sekolah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-bg text-text antialiased">
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

        <!-- Sidebar desktop -->
        <aside class="hidden lg:flex lg:flex-col lg:w-60 border-r border-border bg-surface">
            <div class="px-6 py-5 border-b border-border">
                <p class="text-sm text-muted">Perpustakaan Sekolah</p>
                <p class="font-semibold text-primary">Panel Admin</p>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1">
                @php $user = auth()->user(); @endphp
                <a href="{{ route('dashboard') }}"
                   class="block px-3 py-2 rounded text-sm {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'text-text hover:bg-bg' }}">
                    Dashboard
                </a>
                <a href="{{ route('admins.index') }}"
                   class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admins.*') ? 'bg-primary text-white' : 'text-text hover:bg-bg' }}">
                    Manajemen Admin
                </a>
                <a href="{{ route('audit-logs.index') }}"
                   class="block px-3 py-2 rounded text-sm {{ request()->routeIs('audit-logs.*') ? 'bg-primary text-white' : 'text-text hover:bg-bg' }}">
                    Audit Log
                </a>
                @if ($user->role === 'super_admin')
                    <a href="{{ route('settings.index') }}"
                       class="block px-3 py-2 rounded text-sm {{ request()->routeIs('settings.*') ? 'bg-primary text-white' : 'text-text hover:bg-bg' }}">
                        Pengaturan Sistem
                    </a>
                @endif
            </nav>
            <div class="px-3 py-4 border-t border-border">
                <p class="px-3 text-sm font-medium">{{ $user->name }}</p>
                <p class="px-3 text-xs text-muted mb-2 capitalize">{{ str_replace('_', ' ', $user->role) }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-3 py-2 rounded text-sm text-danger hover:bg-bg">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Sidebar mobile -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
            <div class="absolute inset-0 bg-black/40" @click="sidebarOpen = false"></div>
            <aside class="relative flex flex-col w-64 h-full bg-surface">
                <div class="px-6 py-5 border-b border-border flex items-center justify-between">
                    <p class="font-semibold text-primary">Panel Admin</p>
                    <button @click="sidebarOpen = false" class="text-muted">✕</button>
                </div>
                <nav class="flex-1 px-3 py-4 space-y-1">
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded text-sm hover:bg-bg">Dashboard</a>
                    <a href="{{ route('admins.index') }}" class="block px-3 py-2 rounded text-sm hover:bg-bg">Manajemen Admin</a>
                    <a href="{{ route('audit-logs.index') }}" class="block px-3 py-2 rounded text-sm hover:bg-bg">Audit Log</a>
                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('settings.index') }}" class="block px-3 py-2 rounded text-sm hover:bg-bg">Pengaturan Sistem</a>
                    @endif
                </nav>
            </aside>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="lg:hidden flex items-center justify-between px-4 py-3 border-b border-border bg-surface">
                <button @click="sidebarOpen = true" class="text-primary font-medium">☰ Menu</button>
                <span class="text-sm text-muted">Panel Admin</span>
            </header>

            <main class="flex-1 px-4 py-6 lg:px-10 lg:py-8">
                @if (session('success'))
                    <div class="mb-6 rounded border border-primary/20 bg-primary/5 px-4 py-3 text-sm text-primary">
                        {{ session('success') }}
                    </div>
                @endif

                <h1 class="text-2xl font-semibold mb-6">@yield('title')</h1>

                @yield('content')
            </main>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>