<?php
$wrapperClass = $wrapperClass ?? 'w-full';
?>

<div class="<?= $wrapperClass ?> px-3 sm:px-6 lg:px-8 py-4 md:py-8">

    <!-- ── Header Info (Branding Blue) ──────────────────────────────── -->
    <div class="bg-navbar text-white px-5 py-3.5 rounded-t-xl text-sm shadow-sm flex items-center justify-between border-b border-navbar-line">
        <div class="flex items-center gap-3">
            <div class="p-1.5 bg-blue-500/10 rounded-lg text-blue-400">
                <i class="fas fa-file-invoice text-sm"></i>
            </div>
            <div class="flex flex-col md:flex-row md:items-center md:gap-2">
                <span class="text-white font-bold tracking-tight">Rincian AHS:</span>
                <span id="ahs-item-label" class="text-white/80 font-medium truncate italic text-xs md:text-sm">—</span>
            </div>
        </div>
        <div class="text-[10px] md:text-xs font-semibold opacity-80 uppercase tracking-widest hidden sm:block">
            Sumber: <span id="ahs-source-label" class="text-blue-400 font-bold">PUPR</span>
        </div>
    </div>

    <!-- ── Summary Section (Centered Price & Right Search) ───────────── -->
    <div class="bg-white border-x border-table-border py-6 px-4 md:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
        
        <!-- Left Spacer (to keep price centered) -->
        <div class="hidden md:block w-64"></div>

        <!-- Center: Price Highlight -->
        <div class="flex flex-col items-center gap-2">
            <p class="text-[10px] md:text-xs font-bold text-table-subtle uppercase tracking-[0.2em]">Harga Satuan</p>
            <div class="bg-[#eef2ff] border border-blue-200 px-10 py-2.5 rounded-xl shadow-[0_0_20px_rgba(59,130,246,0.1)] transition-transform hover:scale-105 duration-300">
                <span id="ahs-total-keseluruhan" class="text-2xl md:text-3xl font-black text-blue-700 tabular-nums tracking-tighter">Rp 0</span>
            </div>
        </div>

        <!-- Right: Search -->
        <div class="w-full md:w-64 flex flex-col gap-1.5 self-end">
            <label class="text-[10px] font-bold text-table-subtle uppercase tracking-wider ml-1">Cari Data:</label>
            <div class="relative">
                <input type="text" id="ahs-table-search" placeholder="Masukkan kata kunci..."
                    class="w-full pl-9 pr-4 py-2.5 border border-table-border rounded-xl text-xs focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none bg-slate-50/50 shadow-inner"/>
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-table-subtle text-xs"></i>
            </div>
        </div>
    </div>

    <!-- ── Toolbar ─────────────────────────────────────────────────── -->
    <div class="bg-slate-50/80 border border-table-border p-3 flex flex-wrap items-center justify-center gap-2 mb-6 rounded-b-xl shadow-sm backdrop-blur-sm">
        <button id="ahs-from-db-btn" type="button"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-slate-800 hover:bg-black text-white text-[11px] font-bold transition-all duration-150 focus:outline-none active:scale-95 shadow-md">
            <i class="fas fa-database text-xs text-blue-400"></i>
            DAFTAR AHS
        </button>
        <span class="w-px h-8 bg-slate-300 mx-2 hidden sm:block"></span>
        <div class="flex items-center gap-2 bg-white p-1 rounded-lg border border-slate-200 shadow-inner">
            <button id="ahs-add-bahan-btn" type="button" class="group flex items-center gap-1.5 px-3 py-2 rounded-md hover:bg-blue-50 text-blue-600 text-[10px] font-bold transition-all">
                <i class="fas fa-plus-circle text-blue-400 group-hover:text-blue-600"></i> BAHAN
            </button>
            <button id="ahs-add-upah-btn" type="button" class="group flex items-center gap-1.5 px-3 py-2 rounded-md hover:bg-indigo-50 text-indigo-600 text-[10px] font-bold transition-all">
                <i class="fas fa-plus-circle text-indigo-400 group-hover:text-indigo-600"></i> UPAH
            </button>
            <button id="ahs-add-alat-btn" type="button" class="group flex items-center gap-1.5 px-3 py-2 rounded-md hover:bg-cyan-50 text-cyan-600 text-[10px] font-bold transition-all">
                <i class="fas fa-plus-circle text-cyan-400 group-hover:text-cyan-600"></i> ALAT
            </button>
        </div>
    </div>

    <!-- ── Table Container ──────────────────────────────────────────── -->
    <div class="overflow-x-auto rounded-xl shadow-lg border border-table-border bg-white">
        <table class="w-full text-left border-collapse min-w-[1300px]" id="ahs-table">

            <colgroup>
                <col style="width: 4rem">  <!-- No -->
                <col style="min-width: 25rem"> <!-- Uraian -->
                <col style="width: 8rem">  <!-- Koefisien -->
                <col style="width: 6rem">  <!-- Satuan -->
                <col style="width: 10rem"> <!-- Harga Dasar -->
                <col style="width: 10rem"> <!-- Harga Satuan (Total) -->
                <col style="width: 8rem">  <!-- Aksi -->
                <col style="width: 10rem"> <!-- Merk -->
                <col style="width: 10rem"> <!-- Spesifikasi -->
            </colgroup>

            <thead>
                <tr class="bg-primary text-white">
                    <th class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">No.</th>
                    <th class="px-3 md:px-4 py-3 md:py-3.5 text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">Uraian Pekerjaan</th>
                    <th class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">Koefisien</th>
                    <th class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">Satuan</th>
                    <th class="px-3 md:px-4 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">Harga Dasar</th>
                    <th class="px-3 md:px-4 py-3.5 text-right text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">Harga Satuan</th>
                    <th class="px-3 md:px-4 py-3.5 text-center text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    <th class="px-3 md:px-4 py-3.5 text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">Merk</th>
                    <th class="px-3 md:px-4 py-3.5 text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">Spesifikasi</th>
                </tr>
            </thead>

            <tbody id="ahs-tbody" class="text-table-body text-[11px] md:text-[13px]">
                <!-- rows injected by javascript -->
            </tbody>

        </table>
    </div>

        </table>
    </div>

    <!-- ── Simpan Bar ───────────────────────────────────────────────── -->
    <div class="mt-8 mb-10 flex justify-center">
        <button id="ahs-simpan-btn" type="button"
            class="pointer inline-flex items-center gap-2 bg-primary hover:bg-primary-hover active:scale-95 text-white px-10 py-2.5 rounded-lg text-xs font-bold tracking-widest shadow-md transition-all duration-150 focus:outline-none uppercase">
            <i class="fas fa-check-circle"></i>
            Selesai
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


        <div class="flex-1 overflow-auto">
            <table class="w-full text-left border-collapse table-fixed min-w-[1200px]" id="ahs-modal-table">
                <colgroup>
                    <col style="width: 3.25rem">  <!-- Checkbox -->
                    <col style="width: 6rem">     <!-- ID Item -->
                    <col style="width: 18rem">    <!-- Uraian -->
                    <col style="width: 8rem">     <!-- Merk -->
                    <col style="width: 10rem">    <!-- Spesifikasi -->
                    <col style="width: 5.5rem">   <!-- Satuan -->
                    <col style="width: 8rem">     <!-- Harga Satuan -->
                    <col style="width: 14rem">    <!-- Sumber -->
                </colgroup>
                <thead class="sticky top-0 bg-slate-100/90 backdrop-blur-sm z-10 shadow-sm">
                    <tr>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-table-subtle">
                            <input id="ahs-modal-check-all" type="checkbox" class="w-3.5 h-3.5 rounded accent-primary cursor-pointer"/>
                        </th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-table-subtle">ID Item</th>
                        <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-table-subtle">Uraian</th>
                        <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-table-subtle">Merk</th>
                        <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-table-subtle">Spesifikasi</th>
                        <th class="px-4 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-table-subtle">Satuan</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-table-subtle">Harga Satuan</th>
                        <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wider text-table-subtle">Sumber</th>
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