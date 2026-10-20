<!DOCTYPE html>
<html lang="id" class="h-full bg-cream-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Brader Cafe</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        cream: {
                            50: '#FDFBF7',
                            100: '#FAF6F0',
                            200: '#F4ECE1',
                            300: '#EAE0D0',
                            400: '#DEC9B0',
                        },
                        espresso: {
                            950: '#180E08',
                            900: '#261810',
                            850: '#311F16',
                            800: '#3D2619',
                            700: '#543725',
                            600: '#6F4B34',
                            500: '#8E6347',
                            400: '#B28768',
                            300: '#D5B5A4',
                        },
                        amber: {
                            50: '#FFFBEB',
                            100: '#FEF3C7',
                            200: '#FDE68A',
                            300: '#FCD34D',
                            400: '#FBBF24',
                            500: '#F59E0B',
                            600: '#D97706',
                            700: '#B45309',
                            800: '#92400E',
                            900: '#78350F',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF6F0;
            color: #261810;
        }

        /* Hide scrollbar completely while maintaining scroll functionality */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }
        .no-scrollbar {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
        }

        /* Sleek modern scrollbars for other parts of the application */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #DEC9B0;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #B28768;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col antialiased text-espresso-900 bg-cream-100">
    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar Desktop -->
        <aside class="hidden md:flex md:w-64 md:flex-col bg-espresso-950 text-cream-100 shadow-2xl border-r border-espresso-850">
            <!-- Brand Header -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-espresso-850 bg-espresso-950">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-white text-lg font-bold shadow-md shadow-amber-600/30">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight text-white leading-tight">Brader Cafe</h1>
                    <p class="text-[11px] text-amber-500 font-semibold tracking-wider uppercase">Management Hub</p>
                </div>
            </div>

            <!-- Navigation Links (Scrollbar hidden) -->
            <div class="flex-1 flex flex-col justify-between overflow-y-auto no-scrollbar px-4 py-5">
                <nav class="space-y-1.5">
                    @if(Auth::user()?->role === 'admin')
                    <p class="px-3 text-[10px] font-extrabold tracking-wider text-espresso-400 uppercase mb-2">Utama</p>
                    
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-cream-300 hover:bg-espresso-850 hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie w-4 text-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Dashboard Utama</span>
                    </a>
                    @endif

                    <p class="px-3 text-[10px] font-extrabold tracking-wider text-espresso-400 uppercase {{ Auth::user()?->role === 'admin' ? 'mt-5' : '' }} mb-2">
                        {{ Auth::user()?->role === 'kasir' ? 'Stasiun Kasir' : 'Operasional' }}
                    </p>

                    <a href="{{ route('admin.cashier.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.cashier.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-cream-300 hover:bg-espresso-850 hover:text-white' }}">
                        <i class="fa-solid fa-cash-register w-4 text-center {{ request()->routeIs('admin.cashier.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>{{ Auth::user()?->role === 'kasir' ? 'Kasir & Pembayaran' : 'Kasir (Monitor)' }}</span>
                    </a>

                    <a href="{{ route('admin.kitchen.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.kitchen.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-cream-300 hover:bg-espresso-850 hover:text-white' }}">
                        <i class="fa-solid fa-fire-burner w-4 text-center {{ request()->routeIs('admin.kitchen.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>KDS Barista (Dapur)</span>
                    </a>

                    <a href="{{ route('admin.tables.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.tables.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-cream-300 hover:bg-espresso-850 hover:text-white' }}">
                        <i class="fa-solid fa-qrcode w-4 text-center {{ request()->routeIs('admin.tables.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Meja & QR Code</span>
                    </a>

                    <p class="px-3 text-[10px] font-extrabold tracking-wider text-espresso-400 uppercase mt-5 mb-2">Katalog & Menu</p>

                    @if(Auth::user()?->role === 'admin')
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-cream-300 hover:bg-espresso-850 hover:text-white' }}">
                        <i class="fa-solid fa-tags w-4 text-center {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Kategori Menu</span>
                    </a>
                    @endif

                    <a href="{{ route('admin.menus.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.menus.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-cream-300 hover:bg-espresso-850 hover:text-white' }}">
                        <i class="fa-solid fa-utensils w-4 text-center {{ request()->routeIs('admin.menus.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>{{ Auth::user()?->role === 'kasir' ? 'Katalog Menu & Stok' : 'Menu & Varian' }}</span>
                    </a>

                    @if(Auth::user()?->role === 'admin')
                    <p class="px-3 text-[10px] font-extrabold tracking-wider text-espresso-400 uppercase mt-5 mb-2">Laporan</p>

                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-cream-300 hover:bg-espresso-850 hover:text-white' }}">
                        <i class="fa-solid fa-file-invoice-dollar w-4 text-center {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-amber-500' }}"></i>
                        <span>Laporan Penjualan</span>
                    </a>
                    @endif
                </nav>

                <!-- User profile & Logout -->
                <div class="pt-5 border-t border-espresso-850">
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-espresso-900 border border-espresso-850 mb-3">
                        <div class="flex items-center gap-2.5 overflow-hidden">
                            <div class="w-8 h-8 rounded-xl bg-amber-600/20 text-amber-500 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-500/30">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="truncate">
                                <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                <span class="inline-block px-1.5 py-0.2 text-[9px] uppercase font-extrabold tracking-wider rounded bg-amber-500/20 text-amber-400">
                                    {{ Auth::user()->role ?? 'admin' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-bold text-rose-300 hover:text-rose-100 bg-rose-950/30 hover:bg-rose-900/50 border border-rose-900/40 transition cursor-pointer">
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
            <header class="md:hidden flex items-center justify-between px-4 py-3 bg-espresso-950 text-white border-b border-espresso-850 shadow-md">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-600 flex items-center justify-center text-white font-bold text-xs">
                        <i class="fa-solid fa-mug-hot"></i>
                    </div>
                    <span class="font-black text-sm tracking-tight text-white">Brader Cafe</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs">
                    @if(Auth::user()?->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="p-2 text-cream-300 hover:text-white" title="Dashboard">
                        <i class="fa-solid fa-chart-pie"></i>
                    </a>
                    @endif
                    <a href="{{ route('admin.cashier.index') }}" class="p-2 {{ request()->routeIs('admin.cashier.*') ? 'text-amber-400' : 'text-cream-300 hover:text-white' }}" title="Kasir">
                        <i class="fa-solid fa-cash-register"></i>
                    </a>
                    <a href="{{ route('admin.kitchen.index') }}" class="p-2 text-amber-400 hover:text-amber-300" title="KDS Barista">
                        <i class="fa-solid fa-fire-burner"></i>
                    </a>
                    <a href="{{ route('admin.tables.index') }}" class="p-2 text-cream-300 hover:text-white" title="Meja">
                        <i class="fa-solid fa-qrcode"></i>
                    </a>
                    <a href="{{ route('admin.menus.index') }}" class="p-2 text-cream-300 hover:text-white" title="Menu">
                        <i class="fa-solid fa-utensils"></i>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-rose-400 hover:text-rose-300" title="Keluar">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 overflow-y-auto bg-cream-100 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    <!-- Flash Message Alert -->
                    @if(session('success'))
                        <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-900 shadow-xs animate-fade-in text-xs font-semibold">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                            <div class="flex-1">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flex items-center gap-3 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-900 shadow-xs animate-fade-in text-xs font-semibold">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                            <div class="flex-1">{{ session('error') }}</div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-900 shadow-xs">
                            <div class="flex items-center gap-2 font-bold text-xs mb-1">
                                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                                Terjadi kesalahan pengisian form:
                            </div>
                            <ul class="list-disc list-inside text-[11px] space-y-0.5 text-rose-800">
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
