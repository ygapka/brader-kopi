@extends('layouts.admin')

@section('title', 'Manajemen Meja & QR Code')

@section('content')
<!-- Header & Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Meja & QR Code</h2>
        <p class="text-sm text-slate-500">Generate token unik, pantau status meja, cetak kartu meja, dan download QR Code.</p>
    </div>
    <button type="button" onclick="openCreateTableModal()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-600/20 transition self-start sm:self-auto">
        <i class="fa-solid fa-plus"></i>
        <span>Tambah Meja Baru</span>
    </button>
</div>

<!-- Table Card List -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-5">#</th>
                    <th class="py-3.5 px-5">Nomor Meja</th>
                    <th class="py-3.5 px-5">Token Unik</th>
                    <th class="py-3.5 px-5 text-center">QR Code</th>
                    <th class="py-3.5 px-5 text-center">Status</th>
                    <th class="py-3.5 px-5 text-center">Pesanan Terkait</th>
                    <th class="py-3.5 px-5 text-right">Aksi & QR Tools</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tables as $table)
                    @php
                        $orderUrl = url('/order/' . $table->token);
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-4 px-5 text-slate-400 font-medium">
                            {{ $loop->iteration + ($tables->currentPage() - 1) * $tables->perPage() }}
                        </td>
                        <td class="py-4 px-5 font-bold text-slate-900">
                            <span class="inline-flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs border border-amber-200">
                                    <i class="fa-solid fa-chair"></i>
                                </span>
                                {{ $table->table_number }}
                            </span>
                        </td>
                        <td class="py-4 px-5 font-mono text-xs text-slate-500">
                            <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200">
                                {{ $table->token }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            <!-- Mini QR Code preview thumbnail -->
                            <div class="inline-block p-1 bg-white border border-slate-200 rounded-lg shadow-2xs cursor-pointer hover:border-amber-500 transition"
                                 onclick="openQrModal('{{ $table->table_number }}', '{{ $orderUrl }}')"
                                 title="Klik untuk memperbesar QR Code">
                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(40)->margin(0)->generate($orderUrl) !!}
                            </div>
                        </td>
                        <td class="py-4 px-5 text-center whitespace-nowrap">
                            @if($table->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-center text-xs font-semibold text-slate-600">
                            {{ $table->orders_count }} transaksi
                        </td>
                        <td class="py-4 px-5 text-right whitespace-nowrap space-x-1">
                            <!-- Tombol Cetak QR Code -->
                            <a href="{{ route('admin.tables.print', $table) }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition"
                               title="Cetak Tampilan Kartu QR Code Meja">
                                <i class="fa-solid fa-print"></i>
                                <span>Cetak</span>
                            </a>

                            <!-- Tombol Download QR Code SVG -->
                            <a href="{{ route('admin.tables.download_qr', $table) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 transition"
                               title="Download File Vektor QR Code (.svg)">
                                <i class="fa-solid fa-download"></i>
                                <span>Download</span>
                            </a>

                            <!-- Tombol Edit Meja -->
                            <button type="button" 
                                    onclick="openEditTableModal({{ $table->id }}, '{{ addslashes($table->table_number) }}', {{ $table->is_active ? 1 : 0 }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                    title="Edit Meja">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            <!-- Tombol Hapus Meja -->
                            <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Hapus meja {{ addslashes($table->table_number) }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 transition"
                                        title="Hapus Meja">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-chair text-3xl mb-2 block text-slate-300"></i>
                            Belum ada meja terdaftar. Silakan tambahkan meja baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tables->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $tables->links() }}
        </div>
    @endif
</div>

<!-- Modal Tambah Meja Baru -->
<div id="createTableModal" class="fixed inset-0 z-50 hidden bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-base">Tambah Meja Baru</h3>
            <button type="button" onclick="closeCreateTableModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.tables.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="create_table_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nomor / Nama Meja <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="create_table_number" name="table_number" required placeholder="Contoh: Meja 11, Outdoor 02, VIP"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>

            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/60 text-xs text-amber-800 flex items-start gap-2.5">
                <i class="fa-solid fa-wand-magic-sparkles text-amber-600 mt-0.5 text-sm"></i>
                <p>Sistem akan <strong>secara otomatis men-generate token unik acak</strong> dan QR Code yang mengarah ke URL pemesanan mandiri <code>/order/{token}</code>.</p>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="create_is_active" name="is_active" value="1" checked class="rounded text-amber-600 focus:ring-amber-500">
                <label for="create_is_active" class="text-sm font-medium text-slate-700 cursor-pointer">
                    Status meja langsung aktif
                </label>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCreateTableModal()" 
                        class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-semibold transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-600/20 transition">
                    Simpan Meja
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Meja -->
<div id="editTableModal" class="fixed inset-0 z-50 hidden bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-base">Edit Data Meja</h3>
            <button type="button" onclick="closeEditTableModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="editTableForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_table_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nomor / Nama Meja <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="edit_table_number" name="table_number" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="rounded text-amber-600 focus:ring-amber-500">
                <label for="edit_is_active" class="text-sm font-medium text-slate-700 cursor-pointer">
                    Status meja aktif (Bisa digunakan pesan)
                </label>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <label class="flex items-start gap-2 cursor-pointer text-xs text-slate-700">
                    <input type="checkbox" name="regenerate_token" value="1" class="rounded text-amber-600 focus:ring-amber-500 mt-0.5">
                    <span><strong>Generate Token Baru</strong> (Gunakan jika ingin mengganti QR Code lama yang sudah tidak berlaku).</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditTableModal()" 
                        class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-semibold transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-600/20 transition">
                    Perbarui Meja
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pratinjau QR Code -->
<div id="qrPreviewModal" class="fixed inset-0 z-50 hidden bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-sm overflow-hidden text-center animate-fade-in p-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 id="modalQrTableTitle" class="font-bold text-slate-900 text-base">QR Code Meja</h3>
            <button type="button" onclick="closeQrModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 inline-block mb-4">
            <div id="modalQrSvgContainer"></div>
        </div>

        <p class="text-xs text-slate-500 font-mono break-all px-2" id="modalQrUrlText"></p>

        <div class="mt-5 pt-4 border-t border-slate-100">
            <button type="button" onclick="closeQrModal()"
                    class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                Tutup Pratinjau
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openCreateTableModal() {
        document.getElementById('createTableModal').classList.remove('hidden');
    }
    function closeCreateTableModal() {
        document.getElementById('createTableModal').classList.add('hidden');
    }

    function openEditTableModal(id, number, isActive) {
        document.getElementById('editTableForm').action = '/admin/tables/' + id;
        document.getElementById('edit_table_number').value = number;
        document.getElementById('edit_is_active').checked = (isActive === 1);
        document.getElementById('editTableModal').classList.remove('hidden');
    }
    function closeEditTableModal() {
        document.getElementById('editTableModal').classList.add('hidden');
    }

    function openQrModal(tableNumber, orderUrl) {
        document.getElementById('modalQrTableTitle').innerText = 'QR Code ' + tableNumber;
        document.getElementById('modalQrUrlText').innerText = orderUrl;
        
        // Generate SVG via image tag or direct url
        const container = document.getElementById('modalQrSvgContainer');
        container.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(orderUrl)}" alt="QR Code" class="w-48 h-48 mx-auto">`;
        
        document.getElementById('qrPreviewModal').classList.remove('hidden');
    }
    function closeQrModal() {
        document.getElementById('qrPreviewModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
