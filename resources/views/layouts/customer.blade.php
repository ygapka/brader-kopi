<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Self-Order') - Brader Kopi</title>
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans pb-24">
    
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900">
                ☕ Brader Kopi
            </h1>
            @if(isset($table))
            <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                Meja {{ $table->table_number }}
            </span>
            @endif
        </div>
    </header>

    <main class="max-w-md mx-auto relative min-h-screen">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>

