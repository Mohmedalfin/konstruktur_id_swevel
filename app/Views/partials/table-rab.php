<?php
$tableVisible = isset($tableVisible) && $tableVisible;
$wrapperClass = $tableVisible ? '' : 'hidden';
?>

<div id="rab-table-wrapper" class="w-full px-3 sm:px-6 lg:px-8 py-4 md:py-8 <?= $wrapperClass ?>">
    <!-- Table Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

        <!-- Search -->
        <div class="relative w-full sm:w-100">
            <input id="rab-search" type="text" placeholder="Cari pekerjaan..."
                class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm border border-table-border rounded-lg bg-white placeholder-table-subtle focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-table-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- BOQ Actions -->
        <div class="flex items-center gap-1.5 md:gap-2 shrink-0">

            <!-- Tambah Kategori (only visible in editable mode) -->
            <button id="tambah-kategori-btn" type="button" title="Tambah Kategori Pekerjaan"
                class="hidden inline-flex items-center gap-1 md:gap-1.5 px-2 py-1.5 md:px-3 md:py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] md:text-xs font-semibold transition-all duration-150 focus:outline-none active:scale-95 shadow-sm">
                <svg class="w-3 h-3 md:w-3.5 md:h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Kategori Pekerjaan
            </button>

            <!-- BOQ Actions Dropdown -->
            <div class="hs-dropdown relative inline-flex">
                <button type="button" title="Aksi BOQ" class="hs-dropdown-toggle inline-flex items-center justify-center w-8 h-8 md:w-9 md:h-9 rounded-lg border border-table-border bg-white hover:bg-slate-50 text-slate-700 transition-all duration-150 focus:outline-none active:scale-95 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                </button>

                <div class="hs-dropdown-menu hidden z-50 mt-2 w-48 overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10 end-0" role="menu">
                    <button id="boq-download-template-btn" type="button" class="w-full text-left flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-xs text-slate-700 hover:bg-slate-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Template
                    </button>
                    <button id="boq-import-btn" type="button" class="w-full text-left flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-xs text-slate-700 hover:bg-slate-50 focus:outline-none transition-colors">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Excel BOQ
                    </button>
                </div>
            </div>

            <!-- Hidden file input -->
            <input id="boq-file-input" type="file" accept=".xlsx,.xls,.csv" class="hidden" />

        </div>

    </div>

    <div class="overflow-x-auto rounded-xl shadow-md border border-table-border bg-white pb-4 w-full">
        <table class="w-full text-left min-w-[1400px] border-collapse" id="rab-table">

            <!-- Column widths — locked permanently, never shift on open/close -->
            <colgroup>
                <col style="width: 3.5rem">     <!-- No -->
                <col class="min-w-[300px]">     <!-- Uraian Pekerjaan (flexible/min-width to force scroll) -->
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
            </colgroup>

            <!-- Table Head (static — never changes) -->
            <thead>
                <tr class="bg-primary text-white">
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">No</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-[10px] md:text-xs font-semibold uppercase tracking-wider whitespace-nowrap">Uraian Pekerjaan</th>
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
                </tr>
            </thead>

            <!-- Table Body — populated by ajax_rab.js -->
            <tbody id="rab-tbody" class="text-table-body text-[11px] md:text-[13px]">
                <!-- rows injected here -->
            </tbody>

            <!-- Table Footer — updated by ajax_rab.js -->
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
        </table>
    </div>

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
                <button id="import-rab-modal-close" type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 transition-colors focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Single Table Workspace -->
            <div id="import-step-preview" class="flex-1 overflow-auto bg-slate-50 p-4">
                <div class="rounded-xl shadow-sm border border-table-border bg-white overflow-hidden h-full flex flex-col">
                    <div class="overflow-auto flex-1 pb-4">
                        <table class="table-auto min-w-max md:w-full text-left border-collapse" id="import-rab-modal-table">
                            <!-- Injected dynamically by JS including colgroups and JS-powered swiper headers -->
                            <thead class="sticky top-0 bg-slate-100 z-10 shadow-sm border-b border-table-border" id="import-rab-modal-thead">
                                <tr>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-table-subtle">Memuat Struktur Tabel...</th>
                                </tr>
                            </thead>
                            <tbody id="import-rab-modal-tbody" class="text-[11px] md:text-[13px] text-table-body">
                                <tr>
                                    <td class="text-center py-20 text-table-subtle text-xs italic">
                                        Menunggu file Excel...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex flex-col-reverse md:flex-row md:items-center md:justify-between gap-2 px-4 md:px-6 py-3 md:py-4 border-t border-table-border bg-white shrink-0 rounded-b-2xl shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-20">
                <p id="import-rab-modal-count" class="text-[10px] md:text-xs text-table-subtle font-medium text-center md:text-left">
                    Pilih file Excel untuk memulai
                </p>
                <div class="flex items-center justify-end gap-2">
                    <button id="import-rab-modal-cancel" type="button" class="whitespace-nowrap px-4 py-1.5 md:py-2 rounded-lg border border-table-border bg-white hover:bg-slate-50 text-table-body text-[10px] md:text-xs font-medium transition-all focus:outline-none active:scale-95">
                        Batal
                    </button>
                    <button id="import-rab-modal-confirm" type="button" class="hidden whitespace-nowrap px-4 md:px-6 py-1.5 md:py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] md:text-xs font-bold tracking-wide shadow-sm transition-all duration-150 focus:outline-none active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                        <div class="flex items-center gap-2">Simpan ke RAB<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
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
</div>