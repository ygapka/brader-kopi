@extends('layouts.customer')

@section('title', 'Katalog Menu')

@section('content')
<div x-data="orderCart()" x-init="initCart()" class="relative">
    
    <!-- Flash Error Notification -->
    @if(session('error'))
    <div class="mx-4 mt-3 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center gap-2.5 text-xs font-medium shadow-xs">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Search & Welcome Section -->
    <div class="px-4 pt-4 pb-2">
        <div class="bg-gradient-to-r from-espresso-900 to-espresso-800 rounded-2xl p-4 text-cream-50 shadow-md shadow-espresso-950/15 relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-2 text-amber-400 text-xs font-bold uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-mug-saucer"></i>
                    <span>Selamat Datang</span>
                </div>
                <h2 class="text-lg font-extrabold text-white leading-tight">Pilih Menu Favoritmu</h2>
                <p class="text-xs text-cream-300 mt-1">Pesan langsung dari meja Anda, cepat & praktis.</p>
            </div>
            <div class="absolute -right-4 -bottom-6 w-24 h-24 bg-amber-500/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="absolute right-3 top-3 text-cream-50/10 text-6xl pointer-events-none">
                <i class="fa-solid fa-coffee"></i>
            </div>
        </div>

        <!-- Search Input -->
        <div class="mt-3 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-espresso-400 text-xs"></i>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari kopi, minuman, makanan..." 
                   class="w-full bg-white border border-cream-200 rounded-xl pl-9 pr-8 py-2.5 text-xs text-espresso-900 placeholder:text-espresso-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition shadow-xs">
            <button x-show="searchQuery.length > 0" @click="searchQuery = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-espresso-400 hover:text-espresso-700 text-xs">
                <i class="fa-solid fa-circle-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Category Filter Tabs (Horizontal Scroll) -->
    <div class="sticky top-[57px] z-30 bg-cream-100/95 backdrop-blur-md py-2.5 px-4 border-b border-cream-200/70 shadow-xs">
        <div class="flex gap-2 overflow-x-auto no-scrollbar py-0.5">
            <button type="button"
                    @click="activeCategory = 'all'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
                    :class="activeCategory === 'all' 
                        ? 'bg-espresso-900 text-cream-50 shadow-sm shadow-espresso-950/20' 
                        : 'bg-white text-espresso-700 border border-cream-200/90 hover:bg-cream-200/50'">
                <span>Semua</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full"
                      :class="activeCategory === 'all' ? 'bg-amber-500 text-espresso-950 font-extrabold' : 'bg-cream-200 text-espresso-600'"
                      x-text="totalMenuCount"></span>
            </button>

            @foreach($categories as $category)
            <button type="button"
                    @click="activeCategory = {{ $category->id }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
                    :class="activeCategory === {{ $category->id }} 
                        ? 'bg-espresso-900 text-cream-50 shadow-sm shadow-espresso-950/20' 
                        : 'bg-white text-espresso-700 border border-cream-200/90 hover:bg-cream-200/50'">
                <span>{{ $category->name }}</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full"
                      :class="activeCategory === {{ $category->id }} ? 'bg-amber-500 text-espresso-950 font-extrabold' : 'bg-cream-200 text-espresso-600'">
                    {{ $category->menus->count() }}
                </span>
            </button>
            @endforeach
        </div>
    </div>

    <!-- Menu List Grid -->
    <div class="px-4 py-4 space-y-6">
        @foreach($categories as $category)
            @if($category->menus->count() > 0)
            <div x-show="isCategoryVisible({{ $category->id }})" x-transition.opacity class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold text-espresso-900 tracking-tight flex items-center gap-2">
                        <span class="w-2 h-4 rounded-full bg-amber-600 inline-block"></span>
                        <span>{{ $category->name }}</span>
                    </h2>
                    <span class="text-[11px] font-semibold text-espresso-500">{{ $category->menus->count() }} menu</span>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    @foreach($category->menus as $menu)
                    <div x-show="matchesSearch({{ $menu->id }})"
                         class="group bg-white rounded-2xl border border-cream-200/80 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col h-full relative {{ $menu->is_available ? 'cursor-pointer' : 'opacity-75' }}"
                         @click="{{ $menu->is_available ? 'openMenu(allMenus['.$menu->id.'])' : '' }}">
                        
                        <!-- Thumbnail Image Area -->
                        <div class="aspect-square w-full bg-cream-200/70 relative overflow-hidden">
                            @if($menu->image_url)
                                <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105 {{ !$menu->is_available ? 'grayscale contrast-75' : '' }}">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-espresso-300 gap-1 bg-cream-100">
                                    <i class="fa-solid fa-mug-hot text-2xl"></i>
                                    <span class="text-[9px] uppercase tracking-wider font-semibold text-espresso-400">Brader Cafe</span>
                                </div>
                            @endif

                            <!-- Status Badge: Tersedia vs Habis -->
                            @if(!$menu->is_available)
                                <div class="absolute inset-0 bg-espresso-950/45 backdrop-blur-[1px] flex items-center justify-center p-2">
                                    <span class="bg-rose-900/90 text-rose-100 text-[10px] font-extrabold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 border border-rose-700/50">
                                        <i class="fa-solid fa-ban text-[9px]"></i>
                                        <span>Stok Habis</span>
                                    </span>
                                </div>
                            @else
                                <div class="absolute top-2 left-2">
                                    <span class="bg-cream-50/90 text-emerald-700 text-[9px] font-bold px-2 py-0.5 rounded-md shadow-xs backdrop-blur-xs flex items-center gap-1 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Tersedia</span>
                                    </span>
                                </div>
                            @endif

                            <!-- Quick Add Floating Button -->
                            @if($menu->is_available)
                            <div class="absolute bottom-2 right-2">
                                <button type="button" 
                                        @click.stop="openMenu(allMenus[{{ $menu->id }}])"
                                        class="w-7 h-7 rounded-full bg-amber-600 hover:bg-amber-700 active:scale-90 text-white flex items-center justify-center shadow-md shadow-amber-600/30 transition-transform cursor-pointer">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                            @endif
                        </div>

                        <!-- Content Area -->
                        <div class="p-3 flex flex-col flex-grow justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-espresso-900 line-clamp-2 leading-snug group-hover:text-amber-700 transition-colors">
                                    {{ $menu->name }}
                                </h3>
                                @if($menu->description)
                                    <p class="text-[10px] text-espresso-600/80 line-clamp-1 mt-0.5">{{ $menu->description }}</p>
                                @endif
                            </div>

                            <div class="mt-2.5 pt-1.5 border-t border-cream-200/60 flex items-center justify-between">
                                <span class="text-xs font-extrabold text-amber-700">
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </span>
                                @if($menu->variants->count() > 0)
                                    <span class="text-[9px] text-espresso-500 bg-cream-200/60 px-1.5 py-0.5 rounded font-medium">
                                        +{{ $menu->variants->count() }} Varian
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>

    <!-- Floating Bottom Bar (Keranjang) -->
    <div x-show="cartCount > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-8"
         class="fixed bottom-4 left-0 right-0 max-w-md mx-auto px-4 z-40"
         style="display: none;">
        <button type="button"
                @click="isCartModalOpen = true" 
                class="w-full bg-espresso-950/95 hover:bg-espresso-900 active:scale-[0.99] text-cream-50 rounded-2xl p-3.5 shadow-xl shadow-espresso-950/30 border border-espresso-800 backdrop-blur-md flex items-center justify-between transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-600 flex items-center justify-center font-black text-sm text-white shadow-sm shadow-amber-600/40">
                    <span x-text="cartCount"></span>
                </div>
                <div class="text-left">
                    <div class="font-extrabold text-sm text-white flex items-center gap-1.5">
                        <span>Keranjang Pesanan</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    </div>
                    <div class="text-[11px] text-cream-300">Ketuk untuk lihat rincian & checkout</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="font-black text-sm text-amber-400" x-text="formatRp(cartTotal)"></div>
                <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-xs text-cream-200">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>
        </button>
    </div>

    <!-- Modal Customizer Menu Detail (Bottom Sheet) -->
    <div x-show="isMenuModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-0 pb-0 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-espresso-950/60 backdrop-blur-xs transition-opacity" 
                 aria-hidden="true" 
                 @click="isMenuModalOpen = false"></div>

            <div class="relative z-10 inline-block align-bottom bg-cream-50 rounded-t-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border-t border-cream-200/80"
                 x-show="isMenuModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-full">
                 
                <template x-if="activeMenu">
                    <div>
                        <!-- Header Image -->
                        <div class="h-52 bg-cream-200 relative w-full overflow-hidden">
                            <template x-if="activeMenu.image_url">
                                <img :src="activeMenu.image_url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!activeMenu.image_url">
                                <div class="w-full h-full flex flex-col items-center justify-center text-espresso-300 gap-1 bg-cream-200">
                                    <i class="fa-solid fa-mug-hot text-4xl text-espresso-400"></i>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-espresso-500">Brader Cafe</span>
                                </div>
                            </template>

                            <button @click="isMenuModalOpen = false" 
                                    class="absolute top-4 right-4 w-8 h-8 bg-espresso-900/70 hover:bg-espresso-900 backdrop-blur-md rounded-full text-white flex items-center justify-center transition cursor-pointer">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>
                        
                        <div class="p-5 max-h-[60vh] overflow-y-auto">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div>
                                    <h2 class="text-lg font-extrabold text-espresso-950 leading-tight" x-text="activeMenu.name"></h2>
                                    <p class="text-espresso-600 text-xs mt-1 leading-relaxed" x-text="activeMenu.description"></p>
                                </div>
                                <span class="text-base font-black text-amber-700 shrink-0" x-text="formatRp(activeMenu.price)"></span>
                            </div>
                            
                            <!-- Variants Selector -->
                            <template x-if="activeMenu.variants && activeMenu.variants.length > 0">
                                <div class="mt-4 pt-4 border-t border-cream-200/80">
                                    <label class="block text-xs font-bold text-espresso-900 uppercase tracking-wider mb-2.5">
                                        Pilih Varian
                                    </label>
                                    <div class="space-y-2">
                                        <template x-for="variant in activeMenu.variants" :key="variant.id">
                                            <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition"
                                                   :class="selectedVariant === variant.name 
                                                       ? 'border-amber-600 bg-amber-500/10 text-espresso-950 font-bold' 
                                                       : 'border-cream-300 bg-white text-espresso-800 hover:border-amber-400'">
                                                <div class="flex items-center gap-2.5">
                                                    <input type="radio" 
                                                           name="variant" 
                                                           :value="variant.name" 
                                                           x-model="selectedVariant" 
                                                           class="text-amber-600 focus:ring-amber-500 w-4 h-4 accent-amber-600">
                                                    <span class="text-xs" x-text="variant.name"></span>
                                                </div>
                                                <span class="text-xs text-amber-700 font-semibold" 
                                                      x-show="variant.extra_price > 0" 
                                                      x-text="'+ ' + formatRp(variant.extra_price)"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Special Notes -->
                            <div class="mt-4 pt-4 border-t border-cream-200/80">
                                <label class="block text-xs font-bold text-espresso-900 uppercase tracking-wider mb-1.5">
                                    Catatan Pesanan
                                </label>
                                <textarea x-model="notes" 
                                          rows="2" 
                                          class="w-full bg-white border border-cream-300 rounded-xl p-3 text-xs text-espresso-900 placeholder:text-espresso-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition" 
                                          placeholder="Contoh: Less sugar, extra ice, pisahkan sambal..."></textarea>
                            </div>
                        </div>

                        <!-- Stepper & Add Button Footer -->
                        <div class="p-4 bg-white border-t border-cream-200 flex items-center justify-between gap-3">
                            <div class="flex items-center bg-cream-100 rounded-xl border border-cream-300 p-1">
                                <button type="button" 
                                        @click="if(quantity > 1) quantity--" 
                                        class="w-9 h-9 rounded-lg bg-white text-espresso-800 hover:bg-cream-200 active:scale-95 flex items-center justify-center font-bold text-sm shadow-xs transition">
                                    <i class="fa-solid fa-minus text-xs"></i>
                                </button>
                                <span class="w-10 text-center font-extrabold text-sm text-espresso-950" x-text="quantity"></span>
                                <button type="button" 
                                        @click="quantity++" 
                                        class="w-9 h-9 rounded-lg bg-white text-espresso-800 hover:bg-cream-200 active:scale-95 flex items-center justify-center font-bold text-sm shadow-xs transition">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>

                            <button type="button" 
                                    @click="addToCart()" 
                                    class="flex-1 bg-amber-600 hover:bg-amber-700 active:scale-[0.99] text-white py-3.5 px-4 rounded-xl font-extrabold text-xs shadow-md shadow-amber-600/30 transition-all flex items-center justify-between cursor-pointer">
                                <span>Tambah Pesanan</span>
                                <span x-text="formatRp(activeMenuPrice * quantity)"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Modal Cart & Checkout Drawer (Bottom Sheet) -->
    <div x-show="isCartModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-0 pb-0 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-espresso-950/60 backdrop-blur-xs transition-opacity" 
                 aria-hidden="true" 
                 @click="isCartModalOpen = false"></div>

            <div class="relative z-10 inline-block align-bottom bg-cream-50 rounded-t-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full min-h-[75vh] max-h-[90vh] flex flex-col border-t border-cream-200/80"
                 x-show="isCartModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-full">
                 
                <!-- Drawer Header -->
                <div class="p-4 border-b border-cream-200 bg-white flex justify-between items-center sticky top-0 z-10">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-700 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-espresso-950 leading-none">Keranjang Pesanan</h2>
                            <p class="text-[10px] text-espresso-500 mt-0.5">{{ $table->display_name }} &bull; Periksa pesanan Anda</p>
                        </div>
                    </div>
                    <button @click="isCartModalOpen = false" class="w-8 h-8 rounded-full bg-cream-100 text-espresso-700 hover:bg-cream-200 flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                 
                <!-- Items List -->
                <div class="p-4 flex-grow overflow-y-auto space-y-3">
                    <template x-if="items.length === 0">
                        <div class="text-center py-16 text-espresso-400">
                            <i class="fa-solid fa-mug-saucer text-4xl mb-3 block text-espresso-300"></i>
                            <p class="text-sm font-bold text-espresso-800">Keranjang masih kosong</p>
                            <p class="text-xs text-espresso-500 mt-1">Pilih menu favorit Anda untuk mulai memesan.</p>
                        </div>
                    </template>
                    
                    <template x-for="(item, index) in items" :key="index">
                        <div class="bg-white p-3.5 rounded-2xl border border-cream-200/90 shadow-xs flex items-start justify-between gap-3">
                            <div class="flex-grow">
                                <h4 class="font-extrabold text-xs text-espresso-950" x-text="item.name"></h4>
                                <template x-if="item.variant_name">
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 text-amber-800" x-text="'Varian: ' + item.variant_name"></span>
                                </template>
                                <template x-if="item.notes">
                                    <p class="text-[10px] text-espresso-500 italic mt-1" x-text="'Catatan: ' + item.notes"></p>
                                </template>
                                <div class="text-amber-700 font-extrabold text-xs mt-1.5" x-text="formatRp(item.price)"></div>
                            </div>

                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <div class="flex items-center bg-cream-100 rounded-lg border border-cream-300 p-0.5">
                                    <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded bg-white text-espresso-800 hover:bg-cream-200 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-minus text-[10px]"></i>
                                    </button>
                                    <span class="w-7 text-center font-bold text-xs text-espresso-950" x-text="item.quantity"></span>
                                    <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded bg-white text-espresso-800 hover:bg-cream-200 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                </div>
                                <span class="text-[11px] font-black text-espresso-950" x-text="formatRp(item.price * item.quantity)"></span>
                            </div>
                        </div>
                    </template>
                </div>
                 
                <!-- Customer Details & Checkout Form -->
                <div class="p-4 bg-white border-t border-cream-200" x-show="items.length > 0">
                    <form action="{{ route('order.checkout', $token) }}" method="POST" id="checkout-form">
                        @csrf
                        <input type="hidden" name="items" :value="JSON.stringify(items)">
                        
                        <div class="space-y-3 mb-4 bg-cream-50 p-3.5 rounded-2xl border border-cream-200">
                            <div>
                                <label class="block text-[11px] font-bold text-espresso-900 uppercase tracking-wider mb-1">
                                    Nama Pemesan <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 flex items-center pointer-events-none text-espresso-400 text-xs">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           name="customer_name" 
                                           required 
                                           class="w-full bg-white border border-cream-300 rounded-xl pl-10 pr-3 py-2.5 text-xs text-espresso-900 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition shadow-2xs" 
                                           placeholder="Masukkan nama lengkap Anda" 
                                           x-model="customerName">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-espresso-900 uppercase tracking-wider mb-1">
                                        Nomor Meja
                                    </label>
                                    <div class="flex items-center gap-2 bg-cream-200/70 border border-cream-300 rounded-xl px-3 py-2.5 text-xs font-extrabold text-espresso-900">
                                        <i class="fa-solid fa-chair text-amber-600"></i>
                                        <span>{{ $table->display_name }}</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-espresso-900 uppercase tracking-wider mb-1">
                                        WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <span class="absolute left-3.5 flex items-center pointer-events-none text-emerald-600 text-xs">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </span>
                                        <input type="tel" 
                                               name="customer_whatsapp" 
                                               required 
                                               class="w-full bg-white border border-cream-300 rounded-xl pl-10 pr-3 py-2.5 text-xs text-espresso-900 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition shadow-2xs" 
                                               placeholder="08123456789" 
                                               x-model="customerWhatsapp">
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-espresso-500 italic">
                                * Bukti struk & invoice otomatis dikirimkan ke WhatsApp Anda setelah bayar.
                            </p>
                        </div>
                        
                        <!-- Total Payment -->
                        <div class="flex justify-between items-center mb-3 px-1">
                            <div>
                                <span class="text-xs font-bold text-espresso-700 block">Total Pembayaran</span>
                                <span class="text-[10px] text-espresso-500" x-text="cartCount + ' item pesanan'"></span>
                            </div>
                            <span class="text-lg font-black text-amber-700" x-text="formatRp(cartTotal)"></span>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="button" 
                                @click="submitCheckout()" 
                                class="w-full bg-amber-600 hover:bg-amber-700 active:scale-[0.99] text-white font-extrabold py-3.5 rounded-xl shadow-md shadow-amber-600/30 transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-wider cursor-pointer">
                            <i class="fa-solid fa-lock text-xs"></i>
                            <span>Lanjut ke Pembayaran</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const allMenus = @json($categories->pluck('menus')->flatten()->keyBy('id'));

    function orderCart() {
        return {
            items: [],
            isMenuModalOpen: false,
            isCartModalOpen: false,
            activeMenu: null,
            quantity: 1,
            selectedVariant: '',
            notes: '',
            customerName: '',
            customerWhatsapp: '',
            searchQuery: '',
            activeCategory: 'all',
            
            initCart() {
                const savedCart = localStorage.getItem('brader_cart_{{ $table->id }}');
                if (savedCart) {
                    try {
                        this.items = JSON.parse(savedCart);
                    } catch (e) {
                        this.items = [];
                    }
                }
                
                this.$watch('items', value => {
                    localStorage.setItem('brader_cart_{{ $table->id }}', JSON.stringify(value));
                });
            },
            
            get totalMenuCount() {
                return Object.keys(allMenus).length;
            },
            
            matchesSearch(menuId) {
                const menu = allMenus[menuId];
                if (!menu) return false;
                if (!this.searchQuery) return true;
                const query = this.searchQuery.toLowerCase();
                const name = (menu.name || '').toLowerCase();
                const desc = (menu.description || '').toLowerCase();
                return name.includes(query) || desc.includes(query);
            },
            
            isCategoryVisible(categoryId) {
                if (this.activeCategory === 'all') return true;
                return this.activeCategory === categoryId;
            },
            
            formatRp(angka) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
            },
            
            openMenu(menu) {
                if (!menu || !menu.is_available) return;
                this.activeMenu = menu;
                this.quantity = 1;
                this.notes = '';
                this.selectedVariant = (menu.variants && menu.variants.length > 0) ? menu.variants[0].name : '';
                this.isMenuModalOpen = true;
            },
            
            get activeMenuPrice() {
                if (!this.activeMenu) return 0;
                let price = parseFloat(this.activeMenu.price);
                if (this.selectedVariant && this.activeMenu.variants) {
                    const variant = this.activeMenu.variants.find(v => v.name === this.selectedVariant);
                    if (variant) {
                        price += parseFloat(variant.extra_price);
                    }
                }
                return price;
            },
            
            addToCart() {
                const existingIndex = this.items.findIndex(i => 
                    i.menu_id === this.activeMenu.id && 
                    i.variant_name === this.selectedVariant &&
                    i.notes === this.notes
                );
                
                if (existingIndex >= 0) {
                    this.items[existingIndex].quantity += this.quantity;
                } else {
                    this.items.push({
                        menu_id: this.activeMenu.id,
                        name: this.activeMenu.name,
                        price: this.activeMenuPrice,
                        variant_name: this.selectedVariant,
                        quantity: this.quantity,
                        notes: this.notes
                    });
                }
                
                this.isMenuModalOpen = false;
            },
            
            updateQty(index, change) {
                const newQty = this.items[index].quantity + change;
                if (newQty > 0) {
                    this.items[index].quantity = newQty;
                } else {
                    this.items.splice(index, 1);
                    if (this.items.length === 0) {
                        this.isCartModalOpen = false;
                    }
                }
            },
            
            get cartCount() {
                return this.items.reduce((total, item) => total + item.quantity, 0);
            },
            
            get cartTotal() {
                return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
            },
            
            submitCheckout() {
                if (this.customerName.trim() === '' || this.customerWhatsapp.trim() === '') {
                    alert('Mohon lengkapi Nama Pemesan dan Nomor WhatsApp.');
                    return;
                }
                
                localStorage.removeItem('brader_cart_{{ $table->id }}');
                document.getElementById('checkout-form').submit();
            }
        }
    }
</script>
@endpush
