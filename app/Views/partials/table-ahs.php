<?php
$wrapperClass = $wrapperClass ?? 'w-full';
?>

<div class="<?= $wrapperClass ?> px-3 sm:px-6 lg:px-8 py-4 md:py-8">

    <!-- ── Context Banner ──────────────────────────────────────────── -->
    <div class="flex items-center gap-3 bg-primary text-white px-5 py-3 rounded-xl text-sm shadow-sm mb-6">
        <svg class="w-4 h-4 shrink-0 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <span class="text-white/70 text-xs uppercase tracking-widest font-semibold shrink-0">Item BOQ</span>
        <span class="w-px h-4 bg-white/20 shrink-0"></span>
        <span id="ahs-item-label" class="text-secondary font-bold tracking-wide truncate">—</span>
    </div>

    <!-- ── Toolbar ─────────────────────────────────────────────────── -->
    <div class="flex flex-wrap items-center gap-2 mb-5">

            <!-- Dari Daftar AHS (modal) -->
            <button id="ahs-from-db-btn" type="button"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-amber-400 hover:bg-amber-500 text-black text-xs font-semibold transition-all duration-150 focus:outline-none active:scale-95 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                Daftar AHS
            </button>

            <!-- Tambah Bahan -->
            <button id="ahs-add-bahan-btn" type="button"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold transition-all duration-150 focus:outline-none active:scale-95 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Bahan
            </button>

            <!-- Tambah Alat -->
            <button id="ahs-add-alat-btn" type="button"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold transition-all duration-150 focus:outline-none active:scale-95 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Alat
            </button>

            <!-- Tambah Upah -->
            <button id="ahs-add-upah-btn" type="button"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-violet-500 hover:bg-violet-600 text-white text-xs font-semibold transition-all duration-150 focus:outline-none active:scale-95 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Upah
            </button>

    </div>

    <!-- ── Table Container ──────────────────────────────────────────── -->
    <div class="overflow-x-auto rounded-xl shadow-md border border-table-border bg-white">
        <table class="w-full text-left border-collapse min-w-[860px]" id="ahs-table">

            <colgroup>
                <col style="width: 3rem">
                <col style="width: 6rem">
                <col>
                <col style="width: 7rem">
                <col style="width: 6rem">
                <col style="width: 10rem">
                <col style="width: 10rem">
                <col style="width: 4rem">
            </colgroup>

            <thead>
                <tr class="bg-primary text-white">
                    <th scope="col" class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">No</th>
                    <th scope="col" class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">Tipe</th>
                    <th scope="col" class="px-3 md:px-4 py-3 md:py-3.5 text-[10px] md:text-xs font-semibold uppercase tracking-wider">Uraian</th>
                    <th scope="col" class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">Koefisien</th>
                    <th scope="col" class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">Satuan</th>
                    <th scope="col" class="px-3 md:px-4 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider">Harga Satuan</th>
                    <th scope="col" class="px-3 md:px-4 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider">Jumlah Harga</th>
                    <th scope="col" class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody id="ahs-tbody" class="text-table-body text-[11px] md:text-[13px]">
                <!-- rows injected by ajax_ahs.js -->
            </tbody>

            <tfoot id="ahs-tfoot">
                <tr class="bg-emerald-50 border-t border-emerald-200">
                    <td colspan="6" class="px-3 md:px-4 py-1.5 text-right text-[10px] md:text-xs font-semibold text-emerald-700 uppercase tracking-wide whitespace-nowrap">Total Bahan</td>
                    <td id="ahs-total-bahan" class="px-3 md:px-4 py-1.5 text-right text-[10px] md:text-xs font-bold tabular-nums text-emerald-700 whitespace-nowrap">Rp 0</td>
                    <td class="px-3 md:px-4 py-1.5"></td>
                </tr>
                <tr class="bg-blue-50 border-t border-blue-200">
                    <td colspan="6" class="px-3 md:px-4 py-1.5 text-right text-[10px] md:text-xs font-semibold text-blue-700 uppercase tracking-wide whitespace-nowrap">Total Alat</td>
                    <td id="ahs-total-alat" class="px-3 md:px-4 py-1.5 text-right text-[10px] md:text-xs font-bold tabular-nums text-blue-700 whitespace-nowrap">Rp 0</td>
                    <td class="px-3 md:px-4 py-1.5"></td>
                </tr>
                <tr class="bg-violet-50 border-t border-violet-200">
                    <td colspan="6" class="px-3 md:px-4 py-1.5 text-right text-[10px] md:text-xs font-semibold text-violet-700 uppercase tracking-wide whitespace-nowrap">Total Upah</td>
                    <td id="ahs-total-upah" class="px-3 md:px-4 py-1.5 text-right text-[10px] md:text-xs font-bold tabular-nums text-violet-700 whitespace-nowrap">Rp 0</td>
                    <td class="px-3 md:px-4 py-1.5"></td>
                </tr>
                <tr class="bg-table-category text-white">
                    <td colspan="6" class="px-3 md:px-4 py-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">Total Keseluruhan</td>
                    <td id="ahs-total-keseluruhan" class="px-3 md:px-4 py-2 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 0</td>
                    <td class="px-3 md:px-4 py-2"></td>
                </tr>
            </tfoot>

        </table>
    </div>

    <!-- ── Simpan Bar ───────────────────────────────────────────────── -->
    <div class="mt-4 flex justify-end">
        <button id="ahs-simpan-btn" type="button"
            class="pointer inline-flex items-center gap-2 bg-primary hover:bg-primary-hover active:scale-95 text-white px-8 py-2.5 rounded-lg text-xs font-semibold tracking-wide shadow-md transition-all duration-150 focus:outline-none">
            Simpan Rincian AHS
        </button>
    </div>

</div>


<!-- ═══════════════════════════════════════════════════════════════
     MODAL — Pilih dari Daftar AHS
════════════════════════════════════════════════════════════════ -->
<div id="ahs-modal-overlay"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl h-[600px] flex flex-col overflow-hidden">

        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-table-border bg-primary text-white rounded-t-2xl shrink-0">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <div>
                    <h3 class="text-sm font-bold tracking-wide">Pilih dari Daftar AHS</h3>
                    <p class="text-[11px] text-white/60">Cari dan pilih item AHS untuk ditambahkan</p>
                </div>
            </div>
            <button id="ahs-modal-close" type="button"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 transition-colors focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Search + Filter -->
        <div class="px-6 py-4 border-b border-table-border bg-slate-50 shrink-0">
            <div class="flex flex-col sm:flex-row gap-3">

                <!-- Search -->
                <div class="relative flex-1">
                    <input id="ahs-modal-search" type="text" placeholder="Cari nama bahan / alat / pekerja..."
                        class="w-full pl-9 pr-4 py-2 text-xs border border-table-border rounded-lg bg-white placeholder-table-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"/>
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-table-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Filter Tipe -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <button data-filter="all"   class="ahs-modal-filter-btn active-filter px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-150 focus:outline-none">Semua</button>
                    <button data-filter="bahan" class="ahs-modal-filter-btn px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-150 focus:outline-none">Bahan</button>
                    <button data-filter="alat"  class="ahs-modal-filter-btn px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-150 focus:outline-none">Alat</button>
                    <button data-filter="upah"  class="ahs-modal-filter-btn px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-150 focus:outline-none">Upah</button>
                </div>

            </div>
        </div>

        <!-- Table List AHS (scrollable) -->
        <div class="flex-1 overflow-auto">
            <table class="w-full text-left border-collapse table-fixed min-w-[600px]" id="ahs-modal-table">
                <colgroup>
                    <col style="width: 3rem">     <!-- Checkbox -->
                    <col style="width: 5.5rem">   <!-- Tipe -->
                    <col>                         <!-- Uraian (flexible) -->
                    <col style="width: 5.5rem">   <!-- Satuan -->
                    <col style="width: 8rem">     <!-- Harga Satuan -->
                </colgroup>
                <thead class="sticky top-0 bg-slate-100 z-10">
                    <tr>
                        <th class="px-4 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-table-subtle w-10">
                            <input id="ahs-modal-check-all" type="checkbox" class="w-3.5 h-3.5 rounded accent-primary cursor-pointer"/>
                        </th>
                        <th class="px-4 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-table-subtle w-16">Tipe</th>
                        <th class="px-4 py-2.5 text-[10px] font-semibold uppercase tracking-wider text-table-subtle">Uraian</th>
                        <th class="px-4 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-table-subtle w-16">Satuan</th>
                        <th class="px-4 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-table-subtle w-28">Harga Satuan</th>
                    </tr>
                </thead>
                <tbody id="ahs-modal-tbody" class="text-[11px] md:text-[13px] text-table-body">
                    <!-- injected by JS -->
                </tbody>
            </table>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-table-border bg-slate-50 shrink-0 rounded-b-2xl">
            <p id="ahs-modal-selected-count" class="text-xs text-table-subtle font-medium">
                Belum ada item dipilih
            </p>
            <div class="flex items-center gap-2">
                <button id="ahs-modal-cancel" type="button"
                    class="px-4 py-2 rounded-lg border border-table-border bg-white hover:bg-slate-50 text-table-body text-xs font-medium transition-all focus:outline-none active:scale-95">
                    Batal
                </button>
                <button id="ahs-modal-confirm" type="button" disabled
                    class="px-5 py-2 rounded-lg bg-primary hover:bg-primary-hover text-white text-xs font-semibold tracking-wide shadow-sm transition-all duration-150 focus:outline-none active:scale-95 disabled:opacity-40 disabled:pointer-events-none">
                    Tambahkan AHS
                </button>
            </div>
        </div>

    </div>
</div>