<!DOCTYPE html>
<html lang="id" class="h-full bg-stone-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $table->table_number }} - Brader Cafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 text-white selection:bg-amber-500 selection:text-white">
    <div class="w-full max-w-sm text-center">
        <div class="w-16 h-16 rounded-2xl bg-amber-600 text-white flex items-center justify-center mx-auto text-3xl font-extrabold shadow-xl shadow-amber-600/30 mb-4 ring-8 ring-stone-800">
            <i class="fa-solid fa-mug-hot"></i>
        </div>
        <h1 class="text-2xl font-bold tracking-tight">Selamat Datang di Brader Cafe</h1>
        <div class="inline-block px-4 py-1.5 rounded-full bg-amber-500/20 text-amber-400 font-bold text-sm mt-3 border border-amber-500/30">
            {{ $table->table_number }}
        </div>

        <div class="mt-6 p-6 rounded-2xl bg-stone-800/80 border border-stone-700/80 shadow-2xl text-left space-y-3">
            <div class="flex items-center gap-2 text-emerald-400 text-xs font-semibold">
                <i class="fa-solid fa-circle-check"></i>
                <span>QR Code Valid & Meja Aktif</span>
            </div>
            <p class="text-xs text-stone-300 leading-relaxed">
                Anda terhubung ke <strong>{{ $table->table_number }}</strong> dengan token sesi <code>{{ $table->token }}</code>.
            </p>
            <div class="p-3 rounded-xl bg-stone-900 border border-stone-700/60 text-xs text-stone-400">
                <i class="fa-solid fa-mobile-screen-button text-amber-500 mr-1.5"></i>
                Halaman katalog menu mobile & keranjang checkout Midtrans akan siap diintegrasikan pada <strong>Tahap 3</strong>.
            </div>
        </div>

        <p class="text-[11px] text-stone-500 mt-6">Brader Cafe &bull; Self-Ordering System</p>
    </div>
</body>
</html>
