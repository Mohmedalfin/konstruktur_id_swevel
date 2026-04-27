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

    <!-- ── Toolbar (Hidden) ─────────────────────────────────────────────────── -->
    <div id="ahs-toolbar-old" class="hidden">
        <button id="ahs-from-db-btn"></button>
        <button id="ahs-add-bahan-btn"></button>
        <button id="ahs-add-upah-btn"></button>
        <button id="ahs-add-alat-btn"></button>
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
    class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300 flex flex-col" style="background:#fff">

    <div id="ahs-modal-content" class="w-full h-full flex flex-col transform -translate-y-full transition-transform duration-500 ease-out overflow-hidden">

        <!-- Body: Left Filter + Right Table -->
        <div class="flex flex-1 overflow-hidden">

            <!-- ── Left Panel: Filter ───────────────────────── -->
            <div class="w-56 shrink-0 flex flex-col bg-white border-r border-gray-200 overflow-y-auto">

                <!-- Filter Header -->
                <div class="bg-brand-dark text-white px-4 py-3 flex items-center gap-2 shrink-0">
                    <i class="fas fa-filter text-xs"></i>
                    <span class="text-xs font-bold uppercase tracking-wider">Filter Material</span>
                </div>

                <div class="p-3 flex flex-col gap-4">
                    <!-- Search Nama -->
                    <div>
                        <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block mb-1" id="ahs-filter-label-nama">Nama Bahan</label>
                        <input id="ahs-modal-search" type="text" placeholder="Ketik Nama Bahan"
                            class="w-full px-2.5 py-1.5 border border-gray-200 text-xs focus:outline-none focus:border-brand-dark bg-white"/>
                    </div>

                    <!-- Filter Tipe (hidden buttons kept for JS compatibility) -->
                    <div class="hidden">
                        <button data-filter="all"   class="ahs-modal-filter-btn">Semua</button>
                        <button data-filter="bahan" class="ahs-modal-filter-btn">Bahan</button>
                        <button data-filter="alat"  class="ahs-modal-filter-btn">Alat</button>
                        <button data-filter="upah"  class="ahs-modal-filter-btn">Upah</button>
                    </div>
                </div>
            </div>

            <!-- ── Right Panel: Tabs + Table ────────────────── -->
            <div class="flex-1 flex flex-col overflow-hidden">

                <!-- Tab Bar -->
                <div class="flex border-b border-gray-200 bg-white shrink-0">
                    <button class="ahs-source-tab px-5 py-3 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-brand-dark transition-colors whitespace-nowrap focus:outline-none" data-source="proyek">Proyek Terkini</button>
                    <button class="ahs-source-tab px-5 py-3 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-brand-dark transition-colors whitespace-nowrap focus:outline-none" data-source="suplier">Suplier</button>
                    <button class="ahs-source-tab px-5 py-3 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-brand-dark transition-colors whitespace-nowrap focus:outline-none" data-source="shbj">SHBJ</button>
                    <button class="ahs-source-tab px-5 py-3 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-brand-dark transition-colors whitespace-nowrap focus:outline-none" data-source="ikkbps">IKK BPS</button>
                    <button class="ahs-source-tab px-5 py-3 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-brand-dark transition-colors whitespace-nowrap focus:outline-none" data-source="estimatorid">Estimator.id</button>
                    <button class="ahs-source-tab px-5 py-3 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-brand-dark transition-colors whitespace-nowrap focus:outline-none" data-source="survey">Survey</button>
                </div>

                <!-- Table -->
                <div class="flex-1 overflow-auto">
                    <table class="w-full text-left border-collapse" id="ahs-modal-table">
                        <colgroup>
                            <col style="width: 3rem">   <!-- No -->
                            <col>                       <!-- Nama Bahan -->
                            <col style="width: 6rem">   <!-- Satuan -->
                            <col style="width: 9rem">   <!-- Harga Dasar -->
                            <col style="width: 8rem">   <!-- Merk -->
                            <col style="width: 10rem">  <!-- Spesifikasi -->
                            <col style="width: 10rem">  <!-- Sumber -->
                            <col style="width: 3rem">   <!-- Checkbox -->
                        </colgroup>
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-brand-dark text-white">
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">No.</th>
                                <th class="px-3 py-2.5 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                                    Nama Bahan
                                    <i class="fas fa-sort text-[8px] opacity-60 ml-1"></i>
                                </th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    Satuan <i class="fas fa-sort text-[8px] opacity-60 ml-0.5"></i>
                                </th>
                                <th class="px-3 py-2.5 text-right text-[10px] font-bold uppercase tracking-wider">
                                    Harga Dasar <i class="fas fa-sort text-[8px] opacity-60 ml-0.5"></i>
                                </th>
                                <th class="px-3 py-2.5 text-[10px] font-bold uppercase tracking-wider">
                                    Merk <i class="fas fa-sort text-[8px] opacity-60 ml-0.5"></i>
                                </th>
                                <th class="px-3 py-2.5 text-[10px] font-bold uppercase tracking-wider">
                                    Spesifikasi <i class="fas fa-sort text-[8px] opacity-60 ml-0.5"></i>
                                </th>
                                <th class="px-3 py-2.5 text-[10px] font-bold uppercase tracking-wider">
                                    Sumber <i class="fas fa-sort text-[8px] opacity-60 ml-0.5"></i>
                                </th>
                                <th class="px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider">
                                    <input id="ahs-modal-check-all" type="checkbox" class="w-3.5 h-3.5 accent-brand-dark cursor-pointer"/>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="ahs-modal-tbody" class="text-[12px] text-slate-700">
                            <!-- injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── Footer ───────────────────────────────────────── -->
        <div class="shrink-0 flex items-center justify-center gap-3 py-3 border-t border-gray-200 bg-slate-50">
            <p id="ahs-modal-selected-count" class="text-xs text-table-subtle font-medium absolute left-6"></p>
            <button id="ahs-modal-confirm" type="button" disabled
                class="px-6 py-2 rounded-full bg-brand-dark hover:bg-brand-dark/90 text-white text-xs font-bold tracking-wide shadow-sm transition-all duration-150 focus:outline-none active:scale-95 disabled:opacity-40 disabled:pointer-events-none">
                <i class="fas fa-check mr-1.5"></i> Pilih
            </button>
            <button id="ahs-modal-cancel" type="button"
                class="px-6 py-2 rounded-full border border-brand-dark text-brand-dark text-xs font-bold tracking-wide transition-all focus:outline-none active:scale-95 hover:bg-brand-dark/5">
                <i class="fas fa-times mr-1.5"></i> BATAL
            </button>
            <!-- Hidden close button for JS compat -->
            <button id="ahs-modal-close" class="hidden"></button>
        </div>

    </div>
</div>
