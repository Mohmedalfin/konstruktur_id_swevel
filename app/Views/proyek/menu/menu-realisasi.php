<?php
$idProject = $idProject ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realisasi</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
</head>
    <body class="bg-gray-50 min-h-screen">
        <?php echo view('partials/navbar'); ?>
        <?php echo view('partials/topbar', ['title' => 'REALISASI', 'subtitle' => '']); ?>

        <div class="w-full px-3 sm:px-6 lg:px-8 py-4 md:py-8">
            <div class="inline-flex w-full sm:w-auto bg-slate-200/80 p-1 rounded-xl mb-6 shadow-inner">
                <button id="tab-pekerjaan" class="flex-1 sm:flex-none px-3 sm:px-6 py-2 md:py-2.5 text-[11px] sm:text-sm font-bold bg-white text-[#1e293b] rounded-lg shadow-sm focus:outline-none transition-all whitespace-nowrap">
                    Realisasi Pekerjaan
                </button>
                <button id="tab-sdm" class="flex-1 sm:flex-none px-3 sm:px-6 py-2 md:py-2.5 text-[11px] sm:text-sm font-semibold text-slate-500 hover:text-[#1e293b] rounded-lg focus:outline-none transition-all whitespace-nowrap">
                    Realisasi SDM
                </button>
            </div>
            
            <div id="section-pekerjaan" class="block transition-all">
                <div class="flex items-center justify-between md:justify-end gap-2 mb-4">
                    <div class="relative md:hidden z-[80]">
                        <button id="mobileActionBtn" type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-slate-600 shadow-sm focus:outline-none">
                            <i class="fas fa-ellipsis-v text-sm"></i>
                        </button>
                        <div id="mobileActionMenu" class="hidden absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 p-2 z-[90] animate-in fade-in zoom-in duration-200">
                            <button type="button" data-hs-overlay="#modal-log-dokumentasi" class="flex items-center gap-3 w-full px-3 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg transition-colors text-left focus:outline-none">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-image text-xs"></i>
                                </div>
                                <span>Log Dokumentasi</span>
                            </button>
                            <div class="h-px bg-slate-100 my-1"></div>
                            
                            <!-- Filter Kategori Dropdown -->
                            <div class="relative">
                                <button id="mobileCategoryBtn" type="button"
                                    class="w-full inline-flex items-center gap-3 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg transition-colors text-left focus:outline-none">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-filter text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider leading-none mb-0.5">Filter Kategori</p>
                                        <span id="mobileFilterLabel" class="truncate block text-slate-700 font-semibold leading-tight">Semua Kategori</span>
                                    </div>
                                    <span class="dropdown-icon ms-auto">
                                        <svg class="w-3.5 h-3.5 opacity-70 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </span>
                                </button>
                                <div id="mobileCategoryMenu"
                                    class="hidden bg-slate-50 rounded-lg border border-slate-200 overflow-y-auto max-h-[180px] mt-1 z-50 p-2 space-y-1">
                                    <div class="flex items-center gap-2 py-1 px-1 border-b border-slate-200 mb-1">
                                        <input type="checkbox" id="mobile-category-all" class="w-4 h-4 border-gray-300 rounded focus:ring-slate-800 accent-slate-800 cursor-pointer" value="all" checked>
                                        <label for="mobile-category-all" class="text-xs font-semibold text-slate-600 cursor-pointer">Pilih Semua</label>
                                    </div>
                                    <div id="mobile-category-checkbox-list" class="space-y-1">
                                    <?php if (isset($categories) && !empty($categories)): ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <div class="flex items-center gap-2 py-1 px-1">
                                                <input type="checkbox" class="mobile-category-checkbox w-4 h-4 border-gray-300 rounded focus:ring-slate-800 accent-slate-800 cursor-pointer" value="<?= esc($cat['nama_kategori']) ?>">
                                                <label class="text-xs font-semibold text-slate-600 truncate cursor-pointer"><?= esc($cat['nama_kategori']) ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-[10px] text-slate-400 italic">Tidak ada kategori</span>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="hidden md:flex items-center gap-2">
                            <button type="button" data-hs-overlay="#modal-log-dokumentasi" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-[#0f172a] hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-all focus:outline-none">
                                <i class="fas fa-image text-[10px]"></i> Log Dokumentasi
                            </button>

                            <div class="relative z-[40]">
                                <button id="categoryDropdownBtn" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-[#0f172a] hover:bg-slate-800 text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none shadow-sm min-w-[140px] justify-between">
                                    <span id="selectedCategory">Filter Kategori</span>
                                    <span class="dropdown-icon">
                                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </span>
                                </button>

                                <div id="categoryDropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 overflow-y-auto max-h-[220px] z-[60] [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                                    <div class="px-3 py-2 border-b border-gray-100">
                                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 cursor-pointer hover:bg-gray-50 px-1 py-1 rounded">
                                            <input type="checkbox" id="category-checkbox-all" class="w-4 h-4 border-gray-300 rounded focus:ring-slate-800 accent-slate-800 cursor-pointer" value="all" checked>
                                            <span>Pilih Semua</span>
                                        </label>
                                    </div>
                                    <div id="category-checkbox-list" class="py-1">
                                    <?php if (isset($categories) && !empty($categories)): ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <label class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
                                                <input type="checkbox" class="category-checkbox w-4 h-4 border-gray-300 rounded focus:ring-slate-800 accent-slate-800 cursor-pointer" value="<?= esc($cat['nama_kategori']) ?>">
                                                <span class="truncate"><?= esc($cat['nama_kategori']) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="block px-4 py-2 text-sm text-gray-400 italic">Tidak ada kategori</span>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" data-hs-overlay="#modal-tambah-realisasi" class="inline-flex items-center gap-1.5 px-3 py-2 md:px-4 md:py-2 rounded-lg bg-[#0f172a] hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-all focus:outline-none">
                            <i class="fas fa-plus"></i>
                            <span class="hidden sm:inline">Tambah Progress</span>
                            <span class="sm:hidden">Tambah</span>
                        </button>
                    </div>
                </div>
                <?php echo view('partials/table-realisasi-pekerjaan'); ?>
            </div>

            <div id="section-sdm" class="hidden transition-all">
                <div class="flex items-center justify-between gap-2 mb-4">
                    <div class="hidden md:flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-1.5 shadow-sm ml-auto">
                        <i class="fas fa-calendar-alt text-slate-400 text-xs"></i>
                        <input type="date" id="sdm-filter-start" class="border-none focus:ring-0 text-xs font-semibold text-slate-600 p-0 bg-transparent cursor-pointer w-[110px]" title="Tanggal Mulai">
                        <span class="text-slate-400 text-[10px] font-bold uppercase">s/d</span>
                        <input type="date" id="sdm-filter-end" class="border-none focus:ring-0 text-xs font-semibold text-slate-600 p-0 bg-transparent cursor-pointer w-[110px]" title="Tanggal Akhir">
                        <button type="button" id="sdm-filter-clear" class="hidden w-5 h-5 items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-red-500 transition-colors ml-1 focus:outline-none" title="Hapus Filter">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </div>

                    <div class="relative md:hidden">
                        <button id="mobileActionBtnSDM" type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-slate-600 shadow-sm focus:outline-none">
                            <i class="fas fa-ellipsis-v text-sm"></i>
                        </button>
                        <div id="mobileActionMenuSDM" class="hidden absolute left-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 p-4 z-[70] animate-in fade-in zoom-in duration-200">
                            <div class="flex flex-col gap-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Tanggal Mulai</label>
                                    <input type="date" id="mobile-sdm-start" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-600 text-xs font-semibold rounded-lg focus:outline-none focus:border-primary">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Tanggal Akhir</label>
                                    <input type="date" id="mobile-sdm-end" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-slate-600 text-xs font-semibold rounded-lg focus:outline-none focus:border-primary">
                                </div>
                                <button type="button" id="mobile-sdm-clear" class="w-full py-2 text-xs font-bold text-red-500 bg-red-50 rounded-lg hover:bg-red-100 transition-colors hidden">
                                    Hapus Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" data-hs-overlay="#modal-list-sdm" class="inline-flex items-center gap-1.5 px-3 py-2 md:px-4 md:py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all focus:outline-none ml-auto md:ml-0">
                        <i class="fas fa-list"></i>
                        <span class="hidden sm:inline">Daftar Kebutuhan</span>
                        <span class="sm:hidden">Daftar</span>
                    </button>
                    <button type="button" data-hs-overlay="#modal-real-sdm" class="inline-flex items-center gap-1.5 px-3 py-2 md:px-4 md:py-2 rounded-lg bg-[#0f172a] hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-all focus:outline-none">
                        <i class="fas fa-plus"></i>
                        <span class="hidden sm:inline">Tambah Penggunaan</span>
                        <span class="sm:hidden">Tambah</span>
                    </button>
                </div>

                <?php echo view('partials/table-realisasi-sdm'); ?>
            </div>
        </div>

        <?php echo view('partials/modal-real-pekerjaan'); ?>
        <?php echo view('partials/modal-real-sdm'); ?>
        <?php echo view('partials/modal-list-sdm'); ?>
        <?php echo view('partials/modal-log-dokumentasi'); ?>

        <script>
            window.REALISASI_INIT = {
                idProject: <?= json_encode($idProject) ?>,
                slug: <?= json_encode($slug ?? '') ?>,
                progressData: <?= json_encode($progressData ?? []) ?>
            };

            if (window.REALISASI_INIT.slug) {
                localStorage.setItem('lastProjectSlug', window.REALISASI_INIT.slug);
            }
            window.manualLoader = true;
        </script>
        <script src="<?= base_url('assets/js/preline.js') ?>"></script>
        <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
        <script src="<?= base_url('assets/js/partials/navbar.js') ?>"></script>
        <script type="module" src="<?= base_url('js/realisasi/index.js') ?>"></script>
    </body>
</html>
