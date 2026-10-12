<!DOCTYPE html>
<html lang="id" class="h-full bg-stone-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meja Tidak Ditemukan / Nonaktif - Brader Cafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 text-white">
    <div class="w-full max-w-sm text-center">
        <div class="w-16 h-16 rounded-2xl bg-rose-600/20 text-rose-500 border border-rose-500/30 flex items-center justify-center mx-auto text-3xl font-bold mb-4">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h1 class="text-xl font-bold tracking-tight">Meja Tidak Dapat Diakses</h1>
        
        <div class="mt-4 p-5 rounded-2xl bg-stone-800 border border-stone-700 text-xs text-stone-300 leading-relaxed text-center space-y-2">
            @if(!$table)
                <p>QR Code atau token meja yang Anda scan tidak valid atau telah kadaluarsa.</p>
            @else
                <p><strong>{{ $table->table_number }}</strong> saat ini berstatus <strong>Nonaktif</strong>. Silakan hubungi kasir atau staf kafe untuk informasi lebih lanjut.</p>
            @endif
        </div>

        <p class="text-xs text-stone-500 mt-6">&copy; {{ date('Y') }} Brader Cafe</p>
    </div>
</body>
</html>
