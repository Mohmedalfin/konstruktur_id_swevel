<?php
$wrapperClass = $wrapperClass ?? 'w-full';
?>

<div class="<?= $wrapperClass ?> px-3 sm:px-6 lg:px-8 py-4 md:py-8">

    <!-- ── Header Info ─────────────────────────────────────────────── -->
    <div class="bg-[#0f172a] text-white px-5 py-3.5 rounded-t-xl flex items-center justify-between gap-4 border-b border-slate-800">
        <div class="flex items-center gap-3 min-w-0">
            <!-- Dot indicator -->
            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></span>
            <div class="flex flex-col md:flex-row md:items-center md:gap-2 min-w-0">
                <span class="text-white font-bold tracking-tight shrink-0 text-sm">Rincian AHS:</span>
                <span id="ahs-item-label" class="text-slate-400 font-medium truncate italic text-xs md:text-sm">—</span>
            </div>
        </div>
        <div class="shrink-0 hidden sm:flex items-center gap-1.5 text-[11px] font-semibold tracking-widest uppercase">
            <span class="text-slate-500">Sumber:</span>
            <span id="ahs-source-label" class="text-blue-400 font-bold">—</span>
        </div>
    </div>

    <!-- ── Summary Section ────────────────────────────────────────── -->
    <div class="bg-white border-x border-b border-slate-200 px-6 py-5 flex flex-col md:flex-row items-center justify-between gap-5">

        <!-- Left Spacer -->
        <div class="hidden md:block w-64"></div>

        <!-- Center: Price Card -->
        <div class="flex items-center gap-4 bg-slate-50 border border-slate-200 rounded-xl px-6 py-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.18em] mb-0.5">Harga Satuan</p>
                <span id="ahs-total-keseluruhan" class="text-2xl font-black text-blue-600 tabular-nums tracking-tight">Rp 0</span>
            </div>
        </div>

        <!-- Right: Search -->
        <div class="w-full md:w-64 flex flex-col gap-1.5">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-0.5">Cari Data:</label>
            <div class="relative">
                <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" id="ahs-table-search" placeholder="Masukkan kata kunci..."
                    class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-xs focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition-all outline-none bg-white shadow-sm text-slate-700" />
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
    <div class="overflow-x-auto rounded-none shadow-sm border-y border-slate-200 bg-white">
        <table class="w-full text-left border-collapse min-w-[1500px]" id="ahs-table">
            <colgroup>
                <col style="width: 3.5rem"> <!-- No -->
                <col style="min-width: 20rem"> <!-- Uraian -->
                <col style="width: 8rem"> <!-- Koefisien -->
                <col style="width: 6rem"> <!-- Satuan -->
                <col style="width: 12rem"> <!-- Harga Dasar -->
                <col style="width: 14rem"> <!-- Harga Satuan (Total) -->
                <col style="width: 7rem"> <!-- Aksi -->
                <col style="min-width: 13rem"> <!-- Merk -->
                <col style="min-width: 13rem"> <!-- Spesifikasi -->
                <col style="min-width: 16rem"> <!-- Sumber / Regulasi -->
            </colgroup>

            <thead>
                <tr class="bg-primary text-white">
                    <th
                        class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                        No.</th>
                    <th
                        class="px-3 md:px-4 py-3 md:py-3.5 text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                        Uraian Pekerjaan</th>
                    <th
                        class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                        Koefisien</th>
                    <th
                        class="px-3 md:px-4 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                        Satuan</th>
                    <th
                        class="px-3 md:px-4 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                        Harga Dasar</th>
                    <th
                        class="px-3 md:px-4 py-3.5 text-right text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                        Harga Satuan</th>
                    <th
                        class="px-3 md:px-4 py-3.5 text-center text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                        Aksi</th>
                    <th
                        class="px-3 md:px-4 py-3.5 text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                        Merk</th>
                    <th
                        class="px-3 md:px-4 py-3.5 text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                        Spesifikasi</th>
                    <th
                        class="px-3 md:px-4 py-3.5 text-[10px] md:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                        Sumber / Regulasi
                        <span class="ml-1 text-amber-300 text-[9px] font-normal normal-case">(→ SHBJ)</span>
                    </th>
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
        class="cursor-pointer inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover active:scale-95 text-white px-10 py-3 rounded-full text-xs font-bold tracking-widest shadow-md transition-all duration-150 focus:outline-none uppercase">
        <span class="leading-none mt-px">Simpan</span>
    </button>
</div>

</div>

<div id="ahs-modal-overlay"
    class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300 flex flex-col items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 md:p-8">

    <div id="ahs-modal-content"
        class="w-full max-w-[1400px] h-full flex flex-col transform scale-95 opacity-0 transition-all duration-300 ease-out overflow-hidden bg-white rounded-2xl shadow-2xl ring-1 ring-black/5">

        <!-- ── Modal Header ───────────────────────────────────── -->
        <div class="bg-[#0f172a] px-5 py-4 flex items-center justify-between border-b border-slate-800 rounded-t-2xl z-20">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center border border-blue-500/30">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7c0-2 1-3 3-3h10c2 0 3 1 3 3M4 7h16M8 11h8M8 15h5"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-white text-lg tracking-wide">Database AHS</h2>
                    <p class="text-slate-400 text-xs mt-0.5">Pilih bahan, upah, dan alat dari referensi harga terpadu.</p>
                </div>
            </div>
            <button onclick="document.getElementById('ahs-modal-close').click()"
                class="w-8 h-8 flex justify-center items-center rounded-lg text-slate-400 hover:bg-white/10 hover:text-white transition-colors focus:outline-none">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body: Left Filter + Right Table -->
        <div class="flex flex-1 overflow-hidden bg-slate-50">

            <!-- ── Left Panel: Filter ───────────────────────── -->
            <div
                class="w-64 shrink-0 flex flex-col bg-white border-r border-slate-200 overflow-y-auto z-10 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">

                <div class="p-5 flex flex-col gap-6">
                    <!-- Search Nama -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-2"
                            id="ahs-filter-label-nama">Pencarian Material</label>
                        <div class="relative">
                            <input id="ahs-modal-search" type="text" placeholder="Ketik kata kunci..."
                                class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 transition-all shadow-sm" />
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Filter Tipe (hidden buttons kept for JS compatibility) -->
                    <div class="hidden">
                        <button data-filter="all" class="ahs-modal-filter-btn">Semua</button>
                        <button data-filter="bahan" class="ahs-modal-filter-btn">Bahan</button>
                        <button data-filter="alat" class="ahs-modal-filter-btn">Alat</button>
                        <button data-filter="upah" class="ahs-modal-filter-btn">Upah</button>
                    </div>
                </div>
            </div>

            <!-- ── Right Panel: Tabs + Table ────────────────── -->
            <div class="flex-1 flex flex-col overflow-hidden bg-white">

                <!-- Tab Bar -->
                <div class="flex overflow-x-auto border-b border-slate-200 bg-white shrink-0 scrollbar-hide">
                    <button
                        class="ahs-source-tab px-5 py-3.5 text-[11px] font-bold border-b-[3px] border-transparent text-slate-500 hover:text-blue-600 hover:bg-blue-50/50 transition-all whitespace-nowrap focus:outline-none uppercase tracking-wider"
                        data-source="proyek">Proyek Terkini</button>
                    <button
                        class="ahs-source-tab px-5 py-3.5 text-[11px] font-bold border-b-[3px] border-transparent text-slate-500 hover:text-blue-600 hover:bg-blue-50/50 transition-all whitespace-nowrap focus:outline-none uppercase tracking-wider"
                        data-source="suplier">Suplier</button>
                    <button
                        class="ahs-source-tab px-5 py-3.5 text-[11px] font-bold border-b-[3px] border-transparent text-slate-500 hover:text-blue-600 hover:bg-blue-50/50 transition-all whitespace-nowrap focus:outline-none uppercase tracking-wider"
                        data-source="shbj">SHBJ</button>
                    <button
                        class="ahs-source-tab px-5 py-3.5 text-[11px] font-bold border-b-[3px] border-transparent text-slate-500 hover:text-blue-600 hover:bg-blue-50/50 transition-all whitespace-nowrap focus:outline-none uppercase tracking-wider"
                        data-source="ikkbps">IKK BPS</button>
                    <button
                        class="ahs-source-tab px-5 py-3.5 text-[11px] font-bold border-b-[3px] border-transparent text-slate-500 hover:text-blue-600 hover:bg-blue-50/50 transition-all whitespace-nowrap focus:outline-none uppercase tracking-wider"
                        data-source="estimatorid">Estimator.id</button>
                    <button
                        class="ahs-source-tab px-5 py-3.5 text-[11px] font-bold border-b-[3px] border-transparent text-slate-500 hover:text-blue-600 hover:bg-blue-50/50 transition-all whitespace-nowrap focus:outline-none uppercase tracking-wider"
                        data-source="survey">Survey</button>
                </div>

                <!-- Table -->
                <div class="flex-1 overflow-auto bg-slate-50/30">
                    <table class="w-full text-left border-collapse" id="ahs-modal-table">
                        <colgroup>
                            <col style="width: 3.5rem"> <!-- No -->
                            <col style="width: 7rem"> <!-- Kategori -->
                            <col style="min-width: 15rem"> <!-- Nama Bahan -->
                            <col style="width: 6rem"> <!-- Satuan -->
                            <col style="width: 9rem"> <!-- Harga Dasar -->
                            <col style="width: 8rem"> <!-- Merk -->
                            <col style="width: 10rem"> <!-- Spesifikasi -->
                            <col style="width: 11rem"> <!-- Sumber -->
                            <col style="width: 3.5rem"> <!-- Checkbox -->
                        </colgroup>
                        <thead class="sticky top-0 z-10 shadow-sm">
                            <tr class="bg-slate-800 text-white">
                                <th
                                    class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest border-b border-slate-700">
                                    No.</th>
                                <th
                                    class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest border-b border-slate-700">
                                    Kategori
                                </th>
                                <th
                                    class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest border-b border-slate-700 min-w-[200px]">
                                    Nama Bahan
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest border-b border-slate-700">
                                    Satuan
                                </th>
                                <th
                                    class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-widest border-b border-slate-700">
                                    Harga Dasar
                                </th>
                                <th
                                    class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest border-b border-slate-700">
                                    Merk
                                </th>
                                <th
                                    class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest border-b border-slate-700">
                                    Spesifikasi
                                </th>
                                <th
                                    class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest border-b border-slate-700">
                                    Sumber
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest border-b border-slate-700">
                                    <input id="ahs-modal-check-all" type="checkbox"
                                        class="w-4 h-4 rounded border-slate-500 bg-slate-700/50 text-blue-500 focus:ring-blue-500 cursor-pointer transition-colors" />
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
        <div
            class="shrink-0 flex items-center justify-between px-6 py-4 border-t border-slate-200 bg-white rounded-b-2xl">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse hidden" id="ahs-modal-loading-indicator">
                </div>
                <p id="ahs-modal-selected-count"
                    class="text-xs text-slate-500 font-bold tracking-wide uppercase bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button id="ahs-modal-cancel" type="button"
                    class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition-all">
                    Batal
                </button>
                <button id="ahs-modal-confirm" type="button" disabled
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl flex items-center justify-center gap-2 shadow-md transition-all disabled:opacity-50 disabled:shadow-none disabled:cursor-not-allowed">
                    <i class="fas fa-check-circle text-sm"></i> Pilih Item
                </button>
                <!-- Hidden close button for JS compat -->
                <button id="ahs-modal-close" class="hidden"></button>
            </div>
        </div>
    </div>
</div>