<?= $this->extend('gudang/layouts/main') ?>

<?= $this->section('content') ?>
<div class="w-full max-w-[85rem] mx-auto py-4">

    <!-- STATS CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Total Item</p>
                    <h3 id="stat-total" class="text-2xl font-black text-slate-800 leading-none">7</h3>
                </div>
            </div>
        </div>

        <!-- Stok Aman -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Stok Aman</p>
                    <h3 id="stat-aman" class="text-2xl font-black text-slate-800 leading-none">3</h3>
                </div>
            </div>
        </div>

        <!-- Stok Kritis -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Stok Kritis</p>
                    <h3 id="stat-kritis" class="text-2xl font-black text-slate-800 leading-none">3</h3>
                </div>
            </div>
        </div>

        <!-- Stok Kosong -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Stok Kosong</p>
                    <h3 id="stat-kosong" class="text-2xl font-black text-slate-800 leading-none">1</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTIONS & FILTERS -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search -->
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400"></i>
                </div>
                <input type="text" id="search-item" class="w-full pl-10 pr-4 py-2.5 text-sm font-medium text-slate-700 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:bg-white transition-colors" placeholder="Cari Kode atau Nama Item...">
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex flex-wrap items-center gap-2" id="filter-kategori">
                    <button data-kategori="all" class="px-4 py-2 text-xs font-bold rounded-lg shadow-sm border transition-all focus:outline-none bg-slate-800 text-white border-slate-800 hover:bg-slate-700">Semua</button>
                    <button data-kategori="bahan" class="px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">Bahan</button>
                    <button data-kategori="alat" class="px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">Alat</button>
                </div>
                <div class="hidden sm:block w-px h-6 bg-slate-200"></div>
                <div class="flex flex-wrap items-center gap-2" id="filter-status">
                    <button data-status="all" class="px-4 py-2 text-xs font-bold rounded-lg shadow-sm border transition-all focus:outline-none bg-slate-800 text-white border-slate-800 hover:bg-slate-700">Semua Status</button>
                    <button data-status="aman" class="px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">Aman</button>
                    <button data-status="kritis" class="px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">Kritis</button>
                    <button data-status="kosong" class="px-4 py-2 text-xs font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none">Kosong</button>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE CONTAINER -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-base text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Kode Item</th>
                        <th class="px-4 py-3 whitespace-nowrap min-w-[200px]">Nama Item</th>
                        <th class="px-4 py-3 whitespace-nowrap text-center">Kategori</th>
                        <th class="px-4 py-3 whitespace-nowrap text-center">Satuan</th>
                        <th class="px-4 py-3 whitespace-nowrap text-center">Batas Min</th>
                        <th class="px-4 py-3 whitespace-nowrap text-center">Stok Aktual</th>
                        <th class="px-4 py-3 whitespace-nowrap text-center">Status</th>
                        <th class="px-4 py-3 whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="stok-table-body" class="divide-y divide-slate-100 text-slate-700">
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                            <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
                            <p class="text-sm font-semibold">Memuat data stok...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL EDIT BATAS MINIMUM STOK -->
<div id="modal-edit-minimum" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-0 ease-out transition-all sm:max-w-lg sm:w-full sm:mx-auto min-h-full flex items-center justify-center p-4">
        <div class="flex flex-col bg-white border border-slate-200 shadow-2xl rounded-2xl pointer-events-auto w-full overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-[#1e293b] text-white px-6 py-4 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-600/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-base">Edit Detail & Batas Minimum</h2>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" data-hs-overlay="#modal-edit-minimum">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body (Form) -->
            <div class="p-6">
                <form id="form-edit-minimum" class="space-y-4">
                    <input type="hidden" id="edit-id-barang" name="id_barang">
                    
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Barang</label>
                        <input type="text" id="edit-nama-barang" class="w-full px-3 py-2 text-sm text-slate-500 bg-slate-100 border border-slate-200 rounded-lg focus:outline-none cursor-not-allowed" readonly>
                    </div>

                    <div class="bg-blue-50/60 border border-blue-100 text-blue-800 rounded-xl p-3 flex items-start gap-2.5">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 shrink-0"></i>
                        <p class="text-xs leading-normal">
                            Atur <strong>Batas Min</strong> untuk peringatan stok kritis. Isi <strong>Satuan Kemasan</strong> & <strong>Isi per Kemasan</strong> jika gudang menggunakan unit pack/kemasan khusus (cth: 1 Sak = 50 Kg).
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="edit-stok-minimum" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Batas Min (Satuan Dasar)</label>
                            <input type="number" step="0.01" min="0" id="edit-stok-minimum" class="w-full px-3 py-2 text-sm font-bold text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                        </div>
                        <div class="space-y-2">
                            <label for="edit-satuan-kemasan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Satuan Kemasan (Opsional)</label>
                            <input type="text" id="edit-satuan-kemasan" name="satuan_kemasan" placeholder="Cth: Sak, Pail, Box" class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="space-y-2">
                            <label for="edit-konversi-faktor" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Isi per Kemasan</label>
                            <input type="number" step="0.0001" min="0" id="edit-konversi-faktor" name="konversi_faktor" placeholder="Cth: 50 (Artinya 1 Sak = 50 kg)" class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="space-y-2">
                            <label for="edit-satuan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Satuan RAP (Dasar)</label>
                            <input type="text" id="edit-satuan" name="satuan" class="w-full px-3 py-2 text-sm font-bold text-slate-700 bg-slate-100 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-not-allowed" readonly>
                        </div>
                    </div>

                    <!-- Footer Save Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 mt-6 border-t border-slate-100">
                        <button type="button" data-hs-overlay="#modal-edit-minimum" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors focus:outline-none">
                            Batal
                        </button>
                        <button type="submit" id="btn-save-minimum" class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors focus:outline-none shadow-md inline-flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="module" src="<?= base_url('js/gudang-stok/index.js') ?>"></script>
<?= $this->endSection() ?>
