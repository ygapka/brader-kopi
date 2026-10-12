@extends('layouts.customer')

@section('title', 'Katalog Menu')

@section('content')
<div x-data="orderCart()" x-init="initCart()">
    
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative m-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Kategori & Menu -->
    <div class="px-4 py-4 space-y-8">
        @foreach($categories as $category)
            @if($category->menus->count() > 0)
            <div id="category-{{ $category->id }}">
                <h2 class="text-lg font-bold text-gray-800 mb-4">{{ $category->name }}</h2>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($category->menus as $menu)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col cursor-pointer"
                         @click="openMenu(allMenus[{{ $menu->id }}])">
                         
                        <div class="h-32 bg-gray-200 w-full relative">
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="absolute bottom-2 right-2 bg-orange-500 text-white p-1.5 rounded-full shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                        </div>
                        
                        <div class="p-3 flex flex-col flex-grow">
                            <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 leading-snug">{{ $menu->name }}</h3>
                            <div class="mt-auto pt-2">
                                <span class="text-orange-600 font-bold text-sm">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>

    <!-- Floating Cart Button -->
    <div x-show="cartCount > 0" x-transition 
         class="fixed bottom-4 left-0 right-0 max-w-md mx-auto px-4 z-40">
        <button @click="isCartModalOpen = true" 
                class="w-full bg-orange-600 hover:bg-orange-700 text-white rounded-2xl p-4 shadow-lg flex items-center justify-between transition-colors">
            <div class="flex items-center gap-3">
                <div class="bg-orange-500 rounded-full w-8 h-8 flex items-center justify-center font-bold">
                    <span x-text="cartCount"></span>
                </div>
                <span class="font-semibold text-lg">Keranjang</span>
            </div>
            <div class="font-bold text-lg" x-text="formatRp(cartTotal)"></div>
        </button>
    </div>

    <!-- Modal Menu Detail -->
    <div x-show="isMenuModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-0 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="isMenuModalOpen = false">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <div class="relative z-10 inline-block align-bottom bg-white rounded-t-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full"
                 x-show="isMenuModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-full">
                 
                <template x-if="activeMenu">
                    <div>
                        <!-- Image -->
                        <div class="h-48 bg-gray-200 relative w-full">
                            <template x-if="activeMenu.image">
                                <img :src="'/storage/' + activeMenu.image" class="w-full h-full object-cover">
                            </template>
                            <button @click="isMenuModalOpen = false" class="absolute top-4 right-4 bg-white/80 p-2 rounded-full text-gray-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="p-5">
                            <h2 class="text-xl font-bold text-gray-900 mb-1" x-text="activeMenu.name"></h2>
                            <p class="text-gray-500 text-sm mb-4" x-text="activeMenu.description"></p>
                            
                            <!-- Variants -->
                            <template x-if="activeMenu.variants && activeMenu.variants.length > 0">
                                <div class="mb-5">
                                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Pilih Varian</h3>
                                    <div class="space-y-2">
                                        <template x-for="variant in activeMenu.variants" :key="variant.id">
                                            <label class="flex items-center justify-between p-3 border rounded-lg cursor-pointer"
                                                   :class="selectedVariant === variant.name ? 'border-orange-500 bg-orange-50' : 'border-gray-200'">
                                                <div class="flex items-center">
                                                    <input type="radio" name="variant" :value="variant.name" x-model="selectedVariant" class="text-orange-600 focus:ring-orange-500 w-4 h-4">
                                                    <span class="ml-3 text-gray-700" x-text="variant.name"></span>
                                                </div>
                                                <span class="text-gray-500 text-sm" x-show="variant.extra_price > 0" x-text="'+ ' + formatRp(variant.extra_price)"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Notes -->
                            <div class="mb-5">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2">Catatan Tambahan</h3>
                                <textarea x-model="notes" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm p-3 border" placeholder="Contoh: Jangan pakai sayur, pedas sedang..."></textarea>
                            </div>
                            
                            <!-- Quantity & Add -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button @click="if(quantity > 1) quantity--" class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-l-lg focus:outline-none">-</button>
                                    <span class="px-4 py-2 text-gray-800 font-medium min-w-[3rem] text-center" x-text="quantity"></span>
                                    <button @click="quantity++" class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-r-lg focus:outline-none">+</button>
                                </div>
                                <button @click="addToCart()" class="bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold shadow-sm hover:bg-orange-700 transition-colors flex-grow ml-4 flex justify-between items-center">
                                    <span>Tambah</span>
                                    <span x-text="formatRp(activeMenuPrice * quantity)"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Modal Checkout -->
    <div x-show="isCartModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-0 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="isCartModalOpen = false">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <div class="relative z-10 inline-block align-bottom bg-white rounded-t-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full min-h-[70vh] flex flex-col"
                 x-show="isCartModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-full">
                 
                 <div class="p-4 border-b flex justify-between items-center bg-white sticky top-0">
                     <h2 class="text-xl font-bold text-gray-900">Keranjang Pesanan</h2>
                     <button @click="isCartModalOpen = false" class="text-gray-500 p-2">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                     </button>
                 </div>
                 
                 <div class="p-4 flex-grow overflow-y-auto">
                     <template x-if="items.length === 0">
                         <div class="text-center py-10 text-gray-500">
                             Keranjang masih kosong.
                         </div>
                     </template>
                     
                     <div class="space-y-4">
                         <template x-for="(item, index) in items" :key="index">
                             <div class="flex justify-between items-start pb-4 border-b border-gray-100">
                                 <div class="flex-grow pr-4">
                                     <h4 class="font-semibold text-gray-900" x-text="item.name"></h4>
                                     <p class="text-sm text-gray-500" x-show="item.variant_name" x-text="'Varian: ' + item.variant_name"></p>
                                     <p class="text-sm text-gray-400 italic" x-show="item.notes" x-text="'Catatan: ' + item.notes"></p>
                                     <div class="text-orange-600 font-medium mt-1" x-text="formatRp(item.price)"></div>
                                 </div>
                                 <div class="flex flex-col items-end gap-2">
                                     <div class="flex items-center border border-gray-300 rounded-md">
                                         <button type="button" @click="updateQty(index, -1)" class="px-2 py-1 text-gray-600">-</button>
                                         <span class="px-2 py-1 text-sm font-medium w-8 text-center" x-text="item.quantity"></span>
                                         <button type="button" @click="updateQty(index, 1)" class="px-2 py-1 text-gray-600">+</button>
                                     </div>
                                 </div>
                             </div>
                         </template>
                     </div>
                 </div>
                 
                 <!-- Checkout Form -->
                 <div class="p-4 bg-gray-50 border-t" x-show="items.length > 0">
                     <form action="{{ route('order.checkout', $token) }}" method="POST" id="checkout-form">
                         @csrf
                         <input type="hidden" name="items" :value="JSON.stringify(items)">
                         
                         <div class="space-y-3 mb-4">
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemesan <span class="text-red-500">*</span></label>
                                 <input type="text" name="customer_name" required class="w-full border-gray-300 rounded-lg p-2.5 text-sm border focus:ring-orange-500 focus:border-orange-500" placeholder="Masukkan nama Anda" x-model="customerName">
                             </div>
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                                 <input type="text" name="customer_whatsapp" required class="w-full border-gray-300 rounded-lg p-2.5 text-sm border focus:ring-orange-500 focus:border-orange-500" placeholder="081234567890" x-model="customerWhatsapp">
                             </div>
                         </div>
                         
                         <div class="flex justify-between items-center mb-4">
                             <span class="font-medium text-gray-600">Total Pembayaran</span>
                             <span class="text-xl font-bold text-orange-600" x-text="formatRp(cartTotal)"></span>
                         </div>
                         
                         <button type="button" @click="submitCheckout()" class="w-full bg-orange-600 text-white font-bold py-3.5 rounded-xl hover:bg-orange-700 transition-colors">
                             Pesan Sekarang
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
            
            initCart() {
                const savedCart = localStorage.getItem('brader_cart_{{ $table->id }}');
                if (savedCart) {
                    try {
                        this.items = JSON.parse(savedCart);
                    } catch (e) {
                        this.items = [];
                    }
                }
                
                // Watch for changes and save to localStorage
                this.$watch('items', value => {
                    localStorage.setItem('brader_cart_{{ $table->id }}', JSON.stringify(value));
                });
            },
            
            formatRp(angka) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
            },
            
            openMenu(menu) {
                this.activeMenu = menu;
                this.quantity = 1;
                this.notes = '';
                // Default select first variant if exists
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
                    alert('Mohon lengkapi Nama dan Nomor WhatsApp.');
                    return;
                }
                
                // Clear localStorage on submit so cart is empty next time
                localStorage.removeItem('brader_cart_{{ $table->id }}');
                
                document.getElementById('checkout-form').submit();
            }
        }
    }
</script>
@endpush

