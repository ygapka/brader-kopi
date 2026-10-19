@extends('layouts.admin')

@section('title', 'Tambah Menu Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Menu Baru</h2>
            <p class="text-sm text-slate-500">Lengkapi informasi produk, foto, dan opsi varian racikan.</p>
        </div>
        <a href="{{ route('admin.menus.index') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 text-sm font-semibold shadow-sm transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    <!-- Form Card -->
    <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" 
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-8">
        @csrf

        <!-- Informasi Utama -->
        <div>
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-info-circle text-amber-600"></i>
                Informasi Produk
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kategori Menu <span class="text-rose-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Menu <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Caramel Macchiato"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label for="price" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Harga Pokok (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">Rp</span>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" required min="0" step="1" placeholder="25000"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Status Ketersediaan
                    </label>
                    <div class="flex items-center gap-4 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_available" value="1" {{ old('is_available', '1') == '1' ? 'checked' : '' }}
                                   class="text-amber-600 focus:ring-amber-500">
                            <span class="text-sm font-medium text-slate-700">Tersedia (Ready)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_available" value="0" {{ old('is_available') === '0' ? 'checked' : '' }}
                                   class="text-amber-600 focus:ring-amber-500">
                            <span class="text-sm font-medium text-rose-600">Habis (Sold Out)</span>
                        </label>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Deskripsi Singkat Menu
                    </label>
                    <textarea id="description" name="description" rows="3" placeholder="Jelaskan aroma, rasa, dan komposisi menu..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Upload Foto Menu -->
        <div>
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-image text-amber-600"></i>
                Foto / Gambar Menu
            </h3>

            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- Preview box -->
                <div id="imagePreviewContainer" class="w-32 h-32 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0">
                    <div id="previewPlaceholder" class="text-center text-slate-400 p-2">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl mb-1 block"></i>
                        <span class="text-[11px]">Belum ada foto</span>
                    </div>
                    <img id="imagePreview" src="#" alt="Preview Foto" class="w-full h-full object-cover hidden">
                </div>

                <div class="flex-1 space-y-2">
                    <label for="image" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Pilih File Gambar (Maks 2MB, JPG/PNG/WebP)
                    </label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                           onchange="handleImagePreview(event)"
                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                    <p class="text-[11px] text-slate-400">Gambar akan otomatis disimpan ke folder storage publik.</p>
                </div>
            </div>
        </div>

        <!-- Kelola Varian Menu -->
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-amber-600"></i>
                        Opsi Varian Racikan (Opsional)
                    </h3>
                    <p class="text-xs text-slate-400">Contoh: Less Sugar (Rp 0), Extra Shot (Rp 4.000), Oat Milk (Rp 6.000)</p>
                </div>
                <button type="button" onclick="addVariantRow()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Varian</span>
                </button>
            </div>

            <div id="variantsContainer" class="space-y-3">
                <!-- Baris varian ditambahkan via JS -->
            </div>
        </div>

        <!-- Tombol Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.menus.index') }}" 
               class="px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-semibold transition">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-600/20 transition">
                Simpan & Publikasikan Menu
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let variantIndex = 0;

    function addVariantRow(name = '', price = 0) {
        const container = document.getElementById('variantsContainer');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80 animate-fade-in';
        row.id = `variant_row_${variantIndex}`;
        row.innerHTML = `
            <div class="flex-1">
                <input type="text" name="variants[${variantIndex}][name]" value="${name}" placeholder="Nama varian (cth: Less Sugar)" required
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-900 focus:outline-none focus:border-amber-500">
            </div>
            <div class="w-44">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400 font-bold text-[11px]">+Rp</span>
                    <input type="number" name="variants[${variantIndex}][extra_price]" value="${price}" min="0" step="1" placeholder="0"
                           class="w-full pl-9 pr-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-900 focus:outline-none focus:border-amber-500">
                </div>
            </div>
            <button type="button" onclick="removeVariantRow('${row.id}')"
                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus varian">
                <i class="fa-solid fa-trash-can text-sm"></i>
            </button>
        `;
        container.appendChild(row);
        variantIndex++;
    }

    function removeVariantRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) row.remove();
    }

    function handleImagePreview(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('previewPlaceholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
