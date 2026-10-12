@extends('layouts.admin')

@section('title', 'Daftar Menu & Varian')

@section('content')
<!-- Header & Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Daftar Menu & Varian</h2>
        <p class="text-sm text-slate-500">Kelola item produk, foto, harga, dan opsi varian racikan.</p>
    </div>
    <a href="{{ route('admin.menus.create') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-600/20 transition self-start sm:self-auto">
        <i class="fa-solid fa-plus"></i>
        <span>Tambah Menu Baru</span>
    </a>
</div>

<!-- Filter & Search Toolbar -->
<div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
        <a href="{{ route('admin.menus.index') }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ !request('category_id') ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            Semua (Semua Kategori)
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('admin.menus.index', ['category_id' => $cat->id, 'search' => request('search')]) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ request('category_id') == $cat->id ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    <!-- Search input -->
    <form action="{{ route('admin.menus.index') }}" method="GET" class="w-full md:w-72 relative">
        @if(request('category_id'))
            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
        @endif
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu..."
               class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-slate-900 text-xs focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
    </form>
</div>

<!-- Menus Table -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-5">Foto</th>
                    <th class="py-3.5 px-5">Menu</th>
                    <th class="py-3.5 px-5">Kategori</th>
                    <th class="py-3.5 px-5">Harga</th>
                    <th class="py-3.5 px-5">Varian Opsi</th>
                    <th class="py-3.5 px-5 text-center">Status</th>
                    <th class="py-3.5 px-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($menus as $menu)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-5 w-16">
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}"
                                     class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600 text-lg">
                                    <i class="fa-solid fa-mug-saucer"></i>
                                </div>
                            @endif
                        </td>
                        <td class="py-3.5 px-5">
                            <p class="font-bold text-slate-900">{{ $menu->name }}</p>
                            @if($menu->description)
                                <p class="text-xs text-slate-500 line-clamp-1 max-w-xs mt-0.5">{{ $menu->description }}</p>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ $menu->category->name ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-5 whitespace-nowrap">
                            <span class="font-bold text-slate-900">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                        </td>
                        <td class="py-3.5 px-5">
                            @if($menu->variants->count() > 0)
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @foreach($menu->variants as $variant)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200/60">
                                            <span>{{ $variant->name }}</span>
                                            @if($variant->extra_price > 0)
                                                <span class="text-[10px] text-amber-600 font-bold">+{{ number_format($variant->extra_price, 0, ',', '.') }}</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-slate-400 italic">Tanpa varian</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-center whitespace-nowrap">
                            @if($menu->is_available)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Tersedia
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Habis
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-right whitespace-nowrap space-x-1.5">
                            <a href="{{ route('admin.menus.edit', $menu) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-amber-50 hover:text-amber-700 text-slate-700 transition">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Edit</span>
                            </a>
                            <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Hapus menu {{ addslashes($menu->name) }} beserta variannya?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 transition">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-mug-saucer text-3xl mb-2 block text-slate-300"></i>
                            Tidak ada menu ditemukan. Silakan tambahkan menu baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($menus->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $menus->links() }}
        </div>
    @endif
</div>
@endsection
