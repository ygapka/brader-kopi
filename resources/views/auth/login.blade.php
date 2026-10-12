<!DOCTYPE html>
<html lang="id" class="h-full bg-stone-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - Brader Cafe</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 selection:bg-amber-500 selection:text-white">
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-amber-600 items-center justify-center text-white text-3xl font-bold shadow-xl shadow-amber-600/30 mb-4 ring-8 ring-stone-800">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Brader Cafe</h1>
            <p class="text-stone-400 text-sm mt-1">Sistem Manajemen & Self-Order QR</p>
        </div>

        <!-- Login Card -->
        <div class="bg-stone-850 bg-stone-800/80 backdrop-blur-md rounded-2xl border border-stone-700/80 p-8 shadow-2xl">
            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                    <div class="flex items-center gap-2 font-semibold mb-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Gagal Masuk:
                    </div>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('login.attempt') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-stone-300 mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-500">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email', 'admin@bradercafe.com') }}" required autofocus
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-stone-900 border border-stone-700 text-white placeholder-stone-500 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                               placeholder="nama@bradercafe.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-stone-300 mb-1.5">
                        Kata Sandi (Password)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-500">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" id="password" name="password" value="password" required
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-stone-900 border border-stone-700 text-white placeholder-stone-500 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-stone-300 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-stone-900 border-stone-700 text-amber-600 focus:ring-amber-500 focus:ring-offset-stone-800">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-semibold text-sm shadow-lg shadow-amber-600/30 transition transform active:scale-[0.99] flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <!-- Quick Demo Credentials Fill -->
            <div class="mt-6 pt-6 border-t border-stone-700/60">
                <p class="text-center text-xs text-stone-400 font-medium mb-3">Akun Default Database Seeder:</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button" onclick="fillCreds('admin@bradercafe.com', 'password')"
                            class="px-2.5 py-2 rounded-lg bg-stone-900 hover:bg-stone-700/80 border border-stone-700 text-stone-300 text-left transition flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-amber-400">Admin</p>
                            <p class="text-[10px] text-stone-500">Full Access</p>
                        </div>
                        <i class="fa-solid fa-arrow-turn-down text-stone-500"></i>
                    </button>
                    <button type="button" onclick="fillCreds('kasir@bradercafe.com', 'password')"
                            class="px-2.5 py-2 rounded-lg bg-stone-900 hover:bg-stone-700/80 border border-stone-700 text-stone-300 text-left transition flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-stone-200">Kasir</p>
                            <p class="text-[10px] text-stone-500">Order & POS</p>
                        </div>
                        <i class="fa-solid fa-arrow-turn-down text-stone-500"></i>
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-stone-500 mt-6">
            &copy; {{ date('Y') }} Brader Cafe &bull; Laravel 11 System
        </p>
    </div>

    <script>
        function fillCreds(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
