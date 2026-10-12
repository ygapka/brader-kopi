@extends('layouts.admin')

@section('title', 'Kategori Menu')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Kategori Menu</h2>
        <p class="text-sm text-slate-500">Kelompokkan produk kafe untuk memudahkan navigasi menu pelanggan.</p>
    </div>
    <button type="button" onclick="openCreateModal()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-600/20 transition">
        <i class="fa-solid fa-plus"></i>
        <span>Tambah Kategori Baru</span>
    </button>
</div>

<!-- Table Container -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-5">#</th>
                    <th class="py-3.5 px-5">Nama Kategori</th>
                    <th class="py-3.5 px-5">Slug URL</th>
                    <th class="py-3.5 px-5 text-center">Jumlah Menu</th>
                    <th class="py-3.5 px-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-4 px-5 text-slate-400 font-medium">
                            {{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}
                        </td>
                        <td class="py-4 px-5 font-semibold text-slate-900">
                            {{ $category->name }}
                        </td>
                        <td class="py-4 px-5 font-mono text-xs text-slate-500">
                            {{ $category->slug }}
                        </td>
                        <td class="py-4 px-5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                {{ $category->menus_count }} item
                            </span>
                        </td>
                        <td class="py-4 px-5 text-right space-x-1.5 whitespace-nowrap">
                            <button type="button" 
                                    onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->slug) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-amber-50 hover:text-amber-700 text-slate-700 transition">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Edit</span>
                            </button>

                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ addslashes($category->name) }}?');">
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
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-tags text-3xl mb-2 block text-slate-300"></i>
                            Belum ada kategori yang ditambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $categories->links() }}
        </div>
    @endif
</div>

<!-- Modal Tambah Kategori -->
<div id="createModal" class="fixed inset-0 z-50 hidden bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-base">Tambah Kategori Baru</h3>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="create_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="create_name" name="name" required placeholder="Contoh: Signature Coffee"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="create_slug" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Slug URL (Opsional)
                </label>
                <input type="text" id="create_slug" name="slug" placeholder="Biarkan kosong untuk auto-generate"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" 
                        class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-semibold transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-600/20 transition">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="editModal" class="fixed inset-0 z-50 hidden bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-base">Edit Kategori</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="edit_name" name="name" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="edit_slug" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Slug URL
                </label>
                <input type="text" id="edit_slug" name="slug"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" 
                        class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-semibold transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-600/20 transition">
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(id, name, slug) {
        document.getElementById('editForm').action = '/admin/categories/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_slug').value = slug;
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
