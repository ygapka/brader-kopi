<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>@yield('title', 'Self-Order') - Brader Cafe</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS CDN with Café Color Palette -->
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
                            950: '#190E08',
                            900: '#261810',
                            850: '#311F16',
                            800: '#3D2619',
                            700: '#543725',
                            600: '#6F4B34',
                            500: '#8E6347',
                            400: '#B28768',
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
            -webkit-tap-highlight-color: transparent;
        }
        /* Hide scrollbar for category tabs */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-cream-100 text-espresso-900 antialiased font-sans pb-28 selection:bg-amber-200 selection:text-espresso-950">
    
    <!-- App Header -->
    <header class="sticky top-0 z-40 bg-cream-50/95 backdrop-blur-md border-b border-cream-200/80 shadow-[0_2px_10px_rgba(38,24,16,0.03)]">
        <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-600 to-espresso-850 flex items-center justify-center text-white text-base shadow-sm shadow-amber-600/30">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
                <div>
                    <h1 class="text-base font-extrabold text-espresso-950 tracking-tight leading-none flex items-center gap-1.5">
                        Brader Cafe
                    </h1>
                    <p class="text-[10px] font-semibold text-amber-700 tracking-wider uppercase mt-0.5">Artisan Coffee & Eatery</p>
                </div>
            </div>

            @if(isset($table))
            <div class="flex items-center gap-1.5 bg-amber-500/10 border border-amber-500/30 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full shadow-xs">
                <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                <span>Meja {{ $table->table_number }}</span>
            </div>
            @endif
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-md mx-auto relative min-h-[calc(100vh-65px)]">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
