<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Panel Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-bg text-text antialiased flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <p class="text-sm text-muted">Perpustakaan Sekolah</p>
            <h1 class="text-xl font-semibold text-primary">Masuk ke Panel Admin</h1>
        </div>

        <div class="bg-surface border border-border rounded p-6">
            @if ($errors->any())
                <div class="mb-4 rounded border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1" for="username">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                           class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" for="password">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>
                <button type="submit"
                        class="w-full bg-primary hover:bg-primary-hover text-white rounded py-2 text-sm font-medium transition-colors">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>
