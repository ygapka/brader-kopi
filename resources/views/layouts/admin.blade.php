<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Brader Cafe</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                            950: '#451a03',
                        },
                        coffee: {
                            50: '#fdf8f6',
                            100: '#f2e8e5',
                            500: '#8d5b4c',
                            600: '#6f4436',
                            700: '#533227',
                            800: '#3c221a',
                            900: '#2c1813',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col antialiased text-slate-800">
    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar Desktop -->
        <aside class="hidden md:flex md:w-64 md:flex-col bg-stone-900 text-stone-200 shadow-xl border-r border-stone-800">
            <!-- Brand -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-stone-800/80 bg-stone-950/40">
                <div class="w-10 h-10 rounded-xl bg-amber-600 flex items-center justify-center text-white text-lg font-bold shadow-md shadow-amber-600/30">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight text-white leading-tight">Brader Cafe</h1>
                    <p class="text-xs text-amber-500 font-medium">Management System</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 flex flex-col justify-between overflow-y-auto px-4 py-6">
                <nav class="space-y-1.5">
                    <p class="px-3 text-[11px] font-semibold tracking-wider text-stone-400 uppercase mb-2">Utama</p>
                    
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <p class="px-3 text-[11px] font-semibold tracking-wider text-stone-400 uppercase mt-6 mb-2">Katalog & Menu</p>

                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <i class="fa-solid fa-tags w-5 text-center {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Kategori Menu</span>
                    </a>

                    <a href="{{ route('admin.menus.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.menus.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <i class="fa-solid fa-utensils w-5 text-center {{ request()->routeIs('admin.menus.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Menu & Varian</span>
                    </a>

                    <p class="px-3 text-[11px] font-semibold tracking-wider text-stone-400 uppercase mt-6 mb-2">Operasional</p>

                    <a href="{{ route('admin.tables.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.tables.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <i class="fa-solid fa-qrcode w-5 text-center {{ request()->routeIs('admin.tables.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Meja & QR Code</span>
                    </a>

                    <a href="{{ route('admin.cashier.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.cashier.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <i class="fa-solid fa-cash-register w-5 text-center {{ request()->routeIs('admin.cashier.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Kasir (Monitor)</span>
                    </a>

                    <a href="{{ route('admin.kitchen.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.kitchen.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <i class="fa-solid fa-fire-burner w-5 text-center {{ request()->routeIs('admin.kitchen.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Dapur / Barista</span>
                    </a>

                    <p class="px-3 text-[11px] font-semibold tracking-wider text-stone-400 uppercase mt-6 mb-2">Laporan</p>

                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                        <i class="fa-solid fa-file-invoice-dollar w-5 text-center {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Laporan Penjualan</span>
                    </a>
                </nav>

                <!-- User profile & Logout -->
                <div class="pt-6 border-t border-stone-800">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-stone-950/60 border border-stone-800/80 mb-3">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-full bg-amber-600/20 text-amber-500 flex items-center justify-center font-bold text-sm shrink-0 border border-amber-500/30">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="truncate">
                                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                <span class="inline-block px-1.5 py-0.5 text-[10px] uppercase font-bold tracking-wider rounded bg-amber-500/20 text-amber-400">
                                    {{ Auth::user()->role ?? 'admin' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl text-sm font-medium text-rose-300 hover:text-rose-100 bg-rose-950/30 hover:bg-rose-900/50 border border-rose-900/40 transition">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar Mobile -->
            <header class="md:hidden flex items-center justify-between px-4 py-3.5 bg-stone-900 text-white border-b border-stone-800 shadow">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-600 flex items-center justify-center text-white font-bold text-sm">
                        <i class="fa-solid fa-mug-hot"></i>
                    </div>
                    <span class="font-bold text-base tracking-tight">Brader Cafe</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="p-2 text-stone-300 hover:text-white">
                        <i class="fa-solid fa-chart-pie"></i>
                    </a>
                    <a href="{{ route('admin.menus.index') }}" class="p-2 text-stone-300 hover:text-white">
                        <i class="fa-solid fa-utensils"></i>
                    </a>
                    <a href="{{ route('admin.tables.index') }}" class="p-2 text-stone-300 hover:text-white">
                        <i class="fa-solid fa-qrcode"></i>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-rose-400 hover:text-rose-300">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    <!-- Flash Message Alert -->
                    @if(session('success'))
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm animate-fade-in">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                            <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm animate-fade-in">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                            <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                            <div class="flex items-center gap-2 font-semibold text-sm mb-1 text-rose-900">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                Terjadi kesalahan pengisian form:
                            </div>
                            <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Content Header & Body -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
