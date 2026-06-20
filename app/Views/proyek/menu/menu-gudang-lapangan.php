<?php
$idProject = $idProject ?? null;
$slug      = $slug ?? '';
$proyek    = $proyek ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gudang Lapangan – <?= esc($proyek['nama_proyek'] ?? 'Proyek') ?> - Kontraktor.id</title>
    <meta name="description" content="Pantau dan kelola stok material fisik di lapangan proyek secara real-time.">
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
</head>
<body class="bg-gray-50 min-h-screen">

    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'GUDANG LAPANGAN', 'subtitle' => '']); ?>

    <!-- ======== Breadcrumb ======== -->
    <div class="w-full px-3 sm:px-6 lg:px-8 mt-6 mb-2 flex flex-col sm:flex-row sm:items-center gap-3">
        <a href="<?= base_url('proyek/' . esc($slug) . '/realisasi') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div class="hidden sm:block w-px h-5 bg-slate-300"></div>
        <nav class="flex items-center text-sm font-medium text-slate-500">
            <a href="<?= base_url('proyek/' . esc($slug) . '/realisasi') ?>" class="hover:text-primary transition-colors focus:outline-none">Menu Realisasi</a>
            <svg class="w-3 h-3 mx-2 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 font-semibold">Gudang Lapangan</span>
        </nav>
    </div>

    <div class="w-full px-3 sm:px-6 lg:px-8 py-4 md:py-8">

        <!-- ======== Tab Navigation ======== -->
        <div class="inline-flex w-full sm:w-auto bg-slate-200/80 p-1 rounded-xl mb-6 shadow-inner">
            <button id="tab-stok"
                class="flex-1 sm:flex-none px-3 sm:px-5 py-2 md:py-2.5 text-[11px] sm:text-sm font-bold bg-white text-[#1e293b] rounded-lg shadow-sm focus:outline-none transition-all whitespace-nowrap">
                Stok Lapangan
            </button>
            <button id="tab-kartu"
                class="flex-1 sm:flex-none px-3 sm:px-5 py-2 md:py-2.5 text-[11px] sm:text-sm font-semibold text-slate-500 hover:text-[#1e293b] rounded-lg focus:outline-none transition-all whitespace-nowrap">
                Kartu Stok
            </button>
        </div>

        <!-- ===================== SECTION: STOK LAPANGAN ===================== -->
        <div id="section-stok" class="block transition-all">

            <!-- Header & Actions -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Persediaan Material di Lapangan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Stok fisik yang sudah diterima dari permintaan dan belum terpakai</p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                    <!-- Search -->
                    <div class="relative w-full sm:w-[320px] shrink-0">
                        <input id="stok-search" type="text" placeholder="Cari material..."
                            class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm border rounded-lg bg-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all" />
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <!-- Filter Kategori -->
                        <div class="relative shrink-0 flex-1 sm:flex-none custom-dropdown-container">
                            <select id="stok-filter-kategori" class="hidden">
                                <option value="">Filter Kategori</option>
                                <option value="Bahan">Bahan</option>
                                <option value="Alat">Alat</option>
                            </select>
                            
                            <button type="button" class="custom-dropdown-btn relative py-2 pl-4 pr-3 flex items-center justify-between w-full sm:w-auto min-w-[140px] cursor-pointer bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm focus:outline-none transition-all">
                                <span class="custom-dropdown-label truncate mr-2 pointer-events-none">Filter Kategori</span>
                                <span class="custom-dropdown-icon shrink-0">
                                    <svg class="w-3.5 h-3.5 opacity-70 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </span>
                            </button>
                            
                            <div class="custom-dropdown-menu hidden absolute z-[60] mt-2 w-full min-w-[140px] p-1.5 space-y-0.5 bg-slate-900 border border-slate-700 rounded-xl shadow-xl overflow-hidden overflow-y-auto left-0"></div>
                        </div>
                        <!-- Refresh -->
                        <button id="btn-refresh-stok"
                            class="inline-flex items-center justify-center w-8 h-8 bg-slate-900 hover:bg-slate-800 text-white rounded-lg transition-colors focus:outline-none shadow-sm shrink-0 group"
                            title="Refresh">
                            <svg class="w-3.5 h-3.5 text-white group-hover:rotate-180 transition-transform duration-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/>
                                <path d="M21 3v5h-5"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Strip -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" id="stok-stats-strip">
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">
                            <i class="fas fa-cubes text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Total Jenis Item</p>
                            <h3 id="stat-total-jenis" class="text-2xl font-black text-slate-800 leading-none">–</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Stok Cukup</p>
                            <h3 id="stat-stok-cukup" class="text-2xl font-black text-slate-800 leading-none">–</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Stok Menipis</p>
                            <h3 id="stat-stok-menipis" class="text-2xl font-black text-slate-800 leading-none">–</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-times-circle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Stok Habis</p>
                            <h3 id="stat-stok-habis" class="text-2xl font-black text-slate-800 leading-none">–</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stok Table -->
            <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100">
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kode</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Material</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider hidden md:table-cell">Kategori</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Merk / Spesifikasi</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Stok Aktual</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center hidden sm:table-cell">Status</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="stok-table-body" class="divide-y divide-slate-100">
                            <!-- Diisi oleh JS -->
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center">
                                            <i class="fas fa-circle-notch fa-spin text-slate-400"></i>
                                        </div>
                                        <p class="text-sm text-slate-500 font-medium">Memuat data stok lapangan…</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Empty state -->
                <div id="stok-empty-state" class="hidden py-24 text-center px-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-5 ring-8 ring-slate-50/50">
                        <i class="fas fa-box-open text-3xl text-slate-300"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-2">Belum Ada Stok Material</h3>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">Stok fisik akan terisi otomatis setelah permintaan material berstatus disetujui dan diterima di lapangan.</p>
                </div>
            </div>
        </div>

        <!-- ===================== SECTION: KARTU STOK ===================== -->
        <div id="section-kartu" class="hidden transition-all">

            <!-- Header & Actions -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Kartu Mutasi Stok Lapangan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Riwayat lengkap setiap pergerakan material masuk dan keluar di lapangan</p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                    <!-- Date Picker -->
                    <div class="hidden md:flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-1.5 shadow-sm shrink-0">
                        <i class="fas fa-calendar-alt text-slate-400 text-xs"></i>
                        <input type="date" id="kartu-filter-start" class="border-none focus:ring-0 text-xs font-semibold text-slate-600 p-0 bg-transparent cursor-pointer w-[110px]" title="Tanggal Mulai">
                        <span class="text-slate-400 text-[10px] font-bold uppercase">s/d</span>
                        <input type="date" id="kartu-filter-end" class="border-none focus:ring-0 text-xs font-semibold text-slate-600 p-0 bg-transparent cursor-pointer w-[110px]" title="Tanggal Selesai">
                        <button type="button" id="kartu-filter-clear-date" class="hidden w-5 h-5 items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-red-500 transition-colors ml-1 focus:outline-none" title="Hapus Filter">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        <!-- Filter Tipe -->
                        <div class="relative shrink-0 custom-dropdown-container">
                            <select id="kartu-filter-tipe" class="hidden">
                                <option value="">Semua Tipe</option>
                                <option value="masuk">Masuk</option>
                                <option value="keluar">Keluar</option>
                            </select>

                            <button type="button" class="custom-dropdown-btn relative py-2 pl-4 pr-3 flex items-center justify-between w-full sm:w-auto min-w-[140px] cursor-pointer bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm focus:outline-none transition-all">
                                <span class="custom-dropdown-label truncate mr-2 pointer-events-none">Semua Tipe</span>
                                <span class="custom-dropdown-icon shrink-0">
                                    <svg class="w-3.5 h-3.5 opacity-70 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </span>
                            </button>
                            
                            <div class="custom-dropdown-menu hidden absolute z-[60] mt-2 w-full min-w-[140px] p-1.5 space-y-0.5 bg-slate-900 border border-slate-700 rounded-xl shadow-xl overflow-hidden overflow-y-auto left-0"></div>
                        </div>
                        <!-- Filter Sumber -->
                        <div class="relative shrink-0 custom-dropdown-container">
                            <select id="kartu-filter-sumber" class="hidden">
                                <option value="">Semua Sumber</option>
                                <option value="permintaan">Penerimaan Permintaan</option>
                                <option value="pemakaian">Pemakaian Realisasi</option>
                                <option value="retur_ke_central">Retur ke Central</option>
                                <option value="mutasi_masuk">Mutasi Masuk</option>
                                <option value="mutasi_keluar">Mutasi Keluar</option>
                                <option value="batal_permintaan">Batal Permintaan</option>
                            </select>

                            <button type="button" class="custom-dropdown-btn relative py-2 pl-4 pr-3 flex items-center justify-between w-full sm:w-auto min-w-[160px] cursor-pointer bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm focus:outline-none transition-all">
                                <span class="custom-dropdown-label truncate mr-2 pointer-events-none">Semua Sumber</span>
                                <span class="custom-dropdown-icon shrink-0">
                                    <svg class="w-3.5 h-3.5 opacity-70 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </span>
                            </button>
                            
                            <div class="custom-dropdown-menu hidden absolute z-[60] mt-2 w-full min-w-[160px] p-1.5 space-y-0.5 bg-slate-900 border border-slate-700 rounded-xl shadow-xl overflow-hidden overflow-y-auto left-0"></div>
                        </div>
                        <!-- Refresh -->
                        <button id="btn-refresh-kartu"
                            class="inline-flex items-center justify-center w-8 h-8 bg-slate-900 hover:bg-slate-800 text-white rounded-lg transition-colors focus:outline-none shadow-sm shrink-0 group"
                            title="Refresh">
                            <svg class="w-3.5 h-3.5 text-white group-hover:rotate-180 transition-transform duration-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/>
                                <path d="M21 3v5h-5"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kartu Table -->
            <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100">
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Material</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Tipe</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Jumlah</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center hidden sm:table-cell">Sisa Stok</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider hidden md:table-cell">Sumber</th>
                                <th class="px-5 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="kartu-table-body" class="divide-y divide-slate-100">
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center">
                                            <i class="fas fa-circle-notch fa-spin text-slate-400"></i>
                                        </div>
                                        <p class="text-sm text-slate-500 font-medium">Memuat riwayat mutasi…</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Empty state -->
                <div id="kartu-empty-state" class="hidden py-24 text-center px-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-5 ring-8 ring-slate-50/50">
                        <i class="fas fa-receipt text-3xl text-slate-300"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-2">Belum Ada Riwayat Mutasi</h3>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">Riwayat transaksi akan otomatis tercatat ketika ada pergerakan barang (penerimaan, pemakaian, atau retur).</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ======== MODAL: Retur ke Central ======== -->
    <div id="modal-retur" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-retur-title">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Backdrop -->
            <div id="modal-retur-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

            <!-- Panel -->
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all overflow-hidden">
                <!-- Header -->
                <div class="bg-[#1e293b] text-white px-6 py-4 flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20">
                            <i class="fas fa-right-left"></i>
                        </div>
                        <div>
                            <h2 id="modal-retur-title" class="text-white font-bold text-base">Retur ke Gudang Central</h2>
                            <p class="text-[11px] text-slate-300 mt-0.5">Kembalikan material sisa dari lapangan</p>
                        </div>
                    </div>
                    <button id="btn-close-retur" type="button"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Body -->
                <form id="form-retur" class="p-5 space-y-4">
                    <input type="hidden" id="retur-id-barang" name="id_barang">

                    <!-- Info Barang -->
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Material</p>
                        <p id="retur-nama-barang" class="text-sm font-bold text-slate-800">–</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Stok tersedia: <span id="retur-stok-tersedia" class="font-bold text-emerald-600">–</span></p>
                    </div>

                    <!-- Jumlah -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wider">
                            Jumlah Retur <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="retur-jumlah" name="jumlah" min="0.001" step="0.001" placeholder="0"
                                class="flex-1 px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <span id="retur-satuan" class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-2.5 rounded-lg border border-slate-200 min-w-[60px] text-center">–</span>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wider">
                            Keterangan <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea id="retur-keterangan" name="keterangan" rows="2" placeholder="Contoh: Sisa semen dari pekerjaan plesteran..."
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"></textarea>
                    </div>

                    <!-- Alert Info -->
                    <div class="flex items-start gap-2.5 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <i class="fas fa-triangle-exclamation text-amber-500 text-xs mt-0.5 shrink-0"></i>
                        <p class="text-[11px] text-amber-700 leading-relaxed font-medium">
                            Material akan dikurangi dari stok lapangan dan dikembalikan ke <strong>Gudang Central</strong>. Tindakan ini akan dicatat di kartu stok.
                        </p>
                    </div>
                </form>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-2 p-5 border-t border-gray-100">
                    <button type="button" id="btn-cancel-retur"
                        class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" form="form-retur" id="btn-submit-retur"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all focus:outline-none disabled:opacity-60 disabled:cursor-not-allowed">
                        <i class="fas fa-right-left text-[10px]"></i>
                        Proses Retur
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======== MODAL: Kartu per Barang ======== -->
    <div id="modal-kartu-barang" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div id="modal-kartu-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl transform transition-all overflow-hidden">
                <div class="bg-[#1e293b] text-white px-6 py-4 flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20">
                            <i class="fas fa-scroll"></i>
                        </div>
                        <div>
                            <h2 id="modal-kartu-title" class="text-white font-bold text-base">Kartu Stok Material</h2>
                            <p class="text-[11px] text-slate-300 mt-0.5" id="modal-kartu-subtitle">Riwayat mutasi per barang</p>
                        </div>
                    </div>
                    <button id="btn-close-kartu-barang" type="button"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <div class="p-6 min-h-[50vh] max-h-[70vh] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead>
                            <tr class="bg-slate-50 rounded-lg">
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Sisa</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="kartu-barang-body" class="divide-y divide-gray-50">
                            <tr><td colspan="5" class="py-8 text-center text-slate-400">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-gl" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2 pointer-events-none"></div>

    <script>
        window.GUDANG_LAPANGAN = {
            idProject: <?= json_encode($idProject) ?>,
            slug: <?= json_encode($slug) ?>,
            apiBase: '<?= base_url('api/gudang-lapangan') ?>',
        };
        window.manualLoader = true;
    </script>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>

    <script type="module" src="<?= base_url('js/gudang-lapangan/index.js') ?>"></script>
</body>
</html>
