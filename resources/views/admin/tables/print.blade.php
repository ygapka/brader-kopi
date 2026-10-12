<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code - {{ $table->table_number }} - Brader Cafe</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .print-card {
                border: 2px solid #000 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-4 selection:bg-amber-500 selection:text-white">

    <!-- Floating Top Toolbar (No-print) -->
    <div class="no-print fixed top-5 inset-x-0 flex justify-center z-50">
        <div class="bg-stone-900/90 backdrop-blur-md px-5 py-2.5 rounded-2xl shadow-xl flex items-center gap-3 border border-stone-700">
            <button onclick="window.print()" 
                    class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs flex items-center gap-2 shadow-md transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Lembar Meja (Print)</span>
            </button>
            <a href="{{ route('admin.tables.download_qr', $table) }}" 
               class="px-4 py-2 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-200 font-bold text-xs flex items-center gap-2 transition">
                <i class="fa-solid fa-download"></i>
                <span>Download SVG</span>
            </a>
            <a href="{{ route('admin.tables.index') }}" 
               class="px-3 py-2 rounded-xl text-stone-400 hover:text-white text-xs font-semibold transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Printable Table Stand Card -->
    <div class="print-card w-full max-w-sm bg-white rounded-3xl border-2 border-stone-800 p-8 text-center shadow-xl relative overflow-hidden mt-12 md:mt-0">
        <!-- Accent Header Ribbon -->
        <div class="w-16 h-16 rounded-2xl bg-amber-600 text-white flex items-center justify-center mx-auto text-3xl font-extrabold shadow-lg shadow-amber-600/25 mb-4">
            <i class="fa-solid fa-mug-hot"></i>
        </div>

        <h1 class="text-2xl font-black tracking-tight text-stone-900 uppercase">Brader Cafe</h1>
        <p class="text-xs tracking-widest text-amber-600 uppercase font-bold mt-0.5 mb-5">Coffee & Eatery</p>

        <!-- Big Table Badge -->
        <div class="inline-block px-6 py-2 rounded-2xl bg-stone-900 text-white font-extrabold text-xl tracking-wide uppercase mb-6 shadow-sm">
            {{ $table->table_number }}
        </div>

        <!-- Crisp QR Code SVG -->
        <div class="p-4 bg-white rounded-2xl border-2 border-stone-900 inline-block mx-auto mb-5 shadow-inner">
            <div class="w-60 h-60 flex items-center justify-center">
                {!! $qrCodeSvg !!}
            </div>
        </div>

        <!-- Instructions -->
        <div class="space-y-1.5 px-2">
            <h3 class="font-bold text-stone-900 text-sm flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-camera text-amber-600"></i>
                Scan QR untuk Pesan & Bayar
            </h3>
            <p class="text-xs text-stone-500 leading-relaxed">
                Arahkan kamera HP Anda ke QR Code di atas untuk memilih menu kopi favorit dan melakukan pembayaran mandiri.
            </p>
        </div>

        <!-- Footer / URL fallback -->
        <div class="mt-6 pt-4 border-t border-dashed border-stone-300">
            <p class="text-[10px] text-stone-400 font-mono break-all">
                {{ $orderUrl }}
            </p>
        </div>
    </div>

</body>
</html>
