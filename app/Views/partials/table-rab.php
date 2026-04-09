<?php
$tableVisible = isset($tableVisible) && $tableVisible;
$wrapperClass = $tableVisible ? '' : 'hidden';
$isReorderMode = isset($isReorderMode) && $isReorderMode;
?>

<div id="rab-table-wrapper" class="w-full px-3 sm:px-6 lg:px-8 py-4 md:py-8 <?= $wrapperClass ?>">
    <!-- Table Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

        <!-- Search -->
        <div class="relative w-full sm:w-64">
            <input id="rab-search" type="text" placeholder="Cari pekerjaan..."
                class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm border border-table-border rounded-lg bg-white placeholder-table-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-table-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- BOQ Actions -->
        <div class="flex items-center gap-1.5 md:gap-2 shrink-0">
            <?php if ($isReorderMode): ?>
                <!-- Actions moved to atur-urutan.php -->

            <?php else: ?>
                <!-- Atur Urutan -->
                <a href="<?= base_url('menu-rap/atur-urutan?id_project=' . ($idProject ?? '') . '&slug=' . ($slug ?? '')) ?>" title="Atur Urutan Uraian"
                    class="inline-flex items-center gap-1 md:gap-1.5 px-2 py-1.5 md:px-3 md:py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-[10px] md:text-xs font-semibold transition-all duration-150 shadow-sm">
                    <i class="fas fa-list-ol"></i> Atur Urutan Uraian
                </a>

                <!-- Tambah Kategori -->
                <button id="tambah-kategori-btn" type="button" title="Tambah Kategori Pekerjaan"
                    class="hidden inline-flex items-center gap-1 md:gap-1.5 px-2 py-1.5 md:px-3 md:py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] md:text-xs font-semibold transition-all duration-150 shadow-sm">
                    <i class="fas fa-plus"></i> Kategori Pekerjaan
                </button>

                <!-- Import BOQ -->
                <button id="boq-import-btn" type="button" title="Import BOQ dari Excel"
                    class="inline-flex items-center gap-1 md:gap-1.5 px-2 py-1.5 md:px-3 md:py-2 rounded-lg bg-primary hover:bg-primary-hover text-white text-[10px] md:text-xs font-semibold transition-all duration-150 shadow-sm">
                    <i class="fas fa-file-import"></i> Import BOQ
                </button>
                <input id="boq-file-input" type="file" accept=".xlsx,.xls,.csv" class="hidden" />
            <?php endif; ?>
        </div>

    </div>

    <div class="overflow-x-auto rounded-xl shadow-md border border-table-border bg-white pb-4 w-full">
        <table class="w-full text-left min-w-[1400px] border-collapse" id="rab-table">

            <!-- Column widths — locked permanently, never shift on open/close -->
            <colgroup>
                <col style="width: 4.5rem">     <!-- No -->
                <col class="min-w-[300px]">     <!-- Uraian Pekerjaan -->
                <?php if (!$isReorderMode): ?>
                <col style="width: 6rem">       <!-- Volume -->
                <col style="width: 6rem">       <!-- Satuan -->
                <col style="width: 10rem">      <!-- Harga Bahan -->
                <col style="width: 10rem">      <!-- Harga Alat -->
                <col style="width: 10rem">      <!-- Harga Upah -->
                <col style="width: 10rem">      <!-- Sub. Bahan -->
                <col style="width: 10rem">      <!-- Sub. Alat -->
                <col style="width: 10rem">      <!-- Sub. Upah -->
                <col style="width: 10rem">      <!-- Harga Keseluruhan -->
                <col style="width: 7rem">       <!-- Aksi -->
                <?php endif; ?>
            </colgroup>

            <!-- Table Head (static — never changes) -->
            <thead>
                <tr class="bg-primary text-white">
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">No</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Uraian Pekerjaan</th>
                    <?php if (!$isReorderMode): ?>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Volume</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Satuan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Harga Bahan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Harga Alat</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Harga Upah</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Sub. Bahan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Sub. Alat</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Sub. Upah</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Harga Keseluruhan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>

            <!-- Table Body — populated by ajax_rab.js -->
            <tbody id="rab-tbody" class="text-table-body text-[11px] md:text-[13px]">
                <!-- rows injected here -->
            </tbody>

            <!-- Table Footer — updated by ajax_rab.js -->
            <!-- Table Footer — updated by ajax_rab.js -->
            <?php if (!$isReorderMode): ?>
            <tfoot id="rab-tfoot">
                <tr class="bg-table-category text-white">
                    <td colspan="10" class="px-3 md:px-5 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">Jumlah Harga</td>
                    <td id="rab-total-jumlah" class="px-3 md:px-5 py-1.5 md:py-2 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 0</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                </tr>
                <tr class="bg-table-category-hover text-white">
                    <td colspan="10" class="px-3 md:px-5 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">PPN 11%</td>
                    <td id="rab-total-ppn" class="px-3 md:px-5 py-1.5 md:py-2 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 0</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                </tr>
                <tr class="bg-table-category text-white">
                    <td colspan="10" class="px-3 md:px-5 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">Total Harga</td>
                    <td id="rab-total-final" class="px-3 md:px-5 py-1.5 md:py-2 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 0</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>

    <?php if ($isReorderMode): ?>
    <!-- ── Simpan Bar ───────────────────────────────────────────────── -->
    <div class="mt-4 flex justify-end">
        <button id="save-reorder-btn" type="button"
            class="pointer inline-flex items-center gap-2 bg-primary hover:bg-primary-hover active:scale-95 text-white px-8 py-2.5 rounded-lg text-xs font-semibold tracking-wide shadow-md transition-all duration-150 focus:outline-none">
            <i class="fas fa-save"></i> Simpan Urutan
        </button>
    </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════════════════════
         MODAL — Preview Import BOQ Excel (Direct Data Swipe)
    ════════════════════════════════════════════════════════════════ -->
    <div id="import-rab-modal-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[95vw] h-[85vh] flex flex-col overflow-hidden">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-table-border bg-primary text-white rounded-t-2xl shrink-0">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-bold tracking-wide">Pemetaan Data Excel (<span id="import-file-name" class="font-medium text-blue-200"></span>)</h3>
                        <p class="text-[11px] text-white/70 mt-0.5">Pilih <span class="bg-white/20 px-1 rounded mx-0.5 text-white underline font-medium">Dropdown Kolom Sistem</span> pada masing-masing header Excel untuk mencocokkan data.</p>
                        <p class="text-[11px] text-white/70 mt-0.5">Urutan Kolom : URAIAN, VOLUME, SATUAN</p>
                    </div>
                </div>
                <button id="import-rab-modal-close" type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 transition-colors focus:            <!-- Workspace Step 1: Mapping -->
            <div id="import-step-1" class="flex-1 overflow-auto bg-slate-50 p-4">
                <div class="rounded-xl shadow-sm border border-table-border bg-white overflow-hidden h-full flex flex-col">
                    <div class="overflow-auto flex-1 pb-4">
                        <table class="table-auto min-w-max md:w-full text-left border-collapse" id="import-rab-modal-table">
                            <thead class="sticky top-0 bg-slate-100 z-10 shadow-sm border-b border-table-border" id="import-rab-modal-thead">
                                <tr><th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-table-subtle">Memuat Struktur Tabel...</th></tr>
                            </thead>
                            <tbody id="import-rab-modal-tbody" class="text-[11px] md:text-[13px] text-table-body">
                                <tr><td class="text-center py-20 text-table-subtle text-xs italic">Menunggu file Excel...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Workspace Step 2: Studio Organisir -->
            <div id="import-step-2" class="hidden flex-1 overflow-hidden bg-slate-50 flex flex-col">
                <!-- Toolbar -->
                <div class="px-6 py-3 bg-white border-b border-table-border flex items-center justify-between shadow-sm shrink-0">
                    <div class="flex items-center gap-2">
                        <button id="import-organize-as-cat" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 text-xs font-bold transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            Jadikan Kategori
                        </button>
                        <button id="import-organize-as-item" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-bold transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            Jadikan Item
                        </button>
                        <div class="w-px h-6 bg-slate-200 mx-1"></div>
                        <button id="import-organize-indent-in" type="button" title="Jadikan Sub (Indentasi Masuk)" class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                        </button>
                        <button id="import-organize-indent-out" type="button" title="Naikkan Level (Indentasi Keluar)" class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-auto p-4 lg:p-6">
                    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-table-border overflow-hidden">
                        <div id="import-organize-list" class="divide-y divide-slate-100 select-none">
                            <!-- Injected by import.js -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex flex-col-reverse md:flex-row md:items-center md:justify-between gap-2 px-4 md:px-6 py-3 md:py-4 border-t border-table-border bg-white shrink-0 rounded-b-2xl shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20">
                <div class="flex items-center gap-4">
                    <p id="import-rab-modal-count" class="text-[10px] md:text-xs text-table-subtle font-medium text-center md:text-left">
                        Pilih file Excel untuk memulai
                    </p>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button id="import-rab-modal-back" type="button" class="hidden whitespace-nowrap px-4 py-1.5 md:py-2 rounded-lg border border-table-border bg-white hover:bg-slate-50 text-table-body text-[10px] md:text-xs font-medium transition-all focus:outline-none active:scale-95">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Kembali
                    </button>
                    <button id="import-rab-modal-cancel" type="button" class="whitespace-nowrap px-4 py-1.5 md:py-2 rounded-lg border border-table-border bg-white hover:bg-slate-50 text-table-body text-[10px] md:text-xs font-medium transition-all focus:outline-none active:scale-95">
                        Batal
                    </button>
                    <button id="import-rab-modal-next" type="button" class="hidden whitespace-nowrap px-4 md:px-6 py-1.5 md:py-2 rounded-lg bg-primary hover:bg-primary-hover text-white text-[10px] md:text-xs font-bold tracking-wide shadow-sm transition-all duration-150 focus:outline-none active:scale-95 disabled:opacity-50">
                        Lanjut Organisir <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button id="import-rab-modal-confirm" type="button" class="hidden whitespace-nowrap px-4 md:px-6 py-1.5 md:py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] md:text-xs font-bold tracking-wide shadow-sm transition-all duration-150 focus:outline-none active:scale-95 disabled:opacity-50">
                        Simpan ke RAB <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </div>
            </div>impan ke RAB<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════
         MODAL — Pilih Kategori Pekerjaan
    ════════════════════════════════════════════════════════════════ -->
    <div id="kategori-modal-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col overflow-hidden">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-table-border bg-primary text-white rounded-t-2xl shrink-0">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-bold tracking-wide">Pilih Kategori Pekerjaan</h3>
                        <p class="text-[11px] text-white/60">Centang kategori yang ingin ditambahkan ke RAB</p>
                    </div>
                </div>
                <button id="kategori-modal-close" type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 transition-colors focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body: List Kategori -->
            <div class="flex-1 overflow-y-auto p-5 bg-slate-50 flex flex-col gap-4">
                
                <!-- Input Kategori Manual -->
                <div class="flex items-center gap-2 pb-4 border-b border-slate-200">
                    <input type="text" id="kategori-manual-input" placeholder="Atau ketik kategori baru di sini..." 
                        class="flex-1 text-xs px-4 py-2.5 rounded-lg border border-table-border focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-slate-400">
                    <button type="button" id="kategori-manual-add" 
                        class="px-4 py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold shadow-sm transition-all focus:outline-none active:scale-95 whitespace-nowrap">
                        Tambah
                    </button>
                </div>

                <ul id="kategori-modal-list" class="space-y-2">
                    <!-- injected by JS -->
                </ul>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-table-border bg-white shrink-0 rounded-b-2xl shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <p id="kategori-modal-info" class="text-xs text-table-subtle font-medium">0 kategori dipilih</p>
                <div class="flex items-center gap-2">
                    <button id="kategori-modal-cancel" type="button" class="px-4 py-2 rounded-lg border border-table-border bg-white hover:bg-slate-50 text-table-body text-xs font-medium transition-all focus:outline-none active:scale-95">Batal</button>
                    <button id="kategori-modal-confirm" type="button" class="px-5 py-2 rounded-lg bg-primary hover:bg-primary-hover text-white text-xs font-semibold tracking-wide shadow-sm transition-all duration-150 focus:outline-none active:scale-95">Tambahkan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════
         MODAL — Prompt Import BOQ (Sudah Punya Excel atau Belum)
    ════════════════════════════════════════════════════════════════ -->
    <div id="import-prompt-modal-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm flex flex-col overflow-hidden transform scale-95 transition-transform duration-300" id="import-prompt-modal-content">
            <!-- Modal Body -->
            <div class="px-6 py-8 text-center flex flex-col items-center">
                <!-- Icon container -->
                <div class="w-16 h-16 bg-blue-50 text-primary rounded-full flex items-center justify-center mb-5 rotate-3 shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-slate-800 mb-2">Import BOQ Excel</h3>
                <p class="text-[13px] text-slate-600 mb-1 leading-relaxed">Apakah Anda sudah memiliki file Excel untuk di-import?</p>
                <p class="text-xs text-slate-400 mb-8 leading-relaxed px-2">Jika belum, silakan unduh template Excel yang telah kami siapkan.</p>

                <div class="flex flex-col w-full gap-3">
                    <button type="button" id="import-prompt-modal-excel" class="group relative w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary-hover text-white text-sm font-bold tracking-wide shadow-md shadow-primary/20 transition-all duration-200 active:scale-[0.98] overflow-hidden flex items-center justify-center gap-2">
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out"></div>
                        <svg class="w-4 h-4 z-10 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span class="z-10 relative">Sudah, Import Excel</span>
                    </button>

                    <button type="button" id="import-prompt-modal-template" class="w-full py-3 px-4 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-bold tracking-wide transition-all duration-200 active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Belum, Unduh Template
                    </button>

                    <button type="button" id="import-prompt-modal-cancel" class="mt-4 text-[11px] font-semibold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest focus:outline-none">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>