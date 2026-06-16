<?php
$wrapperClass = $wrapperClass ?? 'w-full';
?>

<div class="<?= $wrapperClass ?> px-3 sm:px-6 lg:px-8 py-4 md:py-8">

    <!-- ── Header Info (Branding Blue) ──────────────────────────────── -->
    <div
        class="bg-navbar text-white px-5 py-3.5 rounded-t-xl text-sm shadow-sm flex items-center justify-between border-b border-navbar-line">
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
    <div
        class="bg-white border-x border-table-border py-6 px-4 md:px-8 flex flex-col md:flex-row items-center justify-between gap-6">

        <!-- Left Spacer (to keep price centered) -->
        <div class="hidden md:block w-64"></div>

        <!-- Center: Price Highlight -->
        <div class="flex flex-col items-center gap-2">
            <p class="text-[10px] md:text-xs font-bold text-table-subtle uppercase tracking-[0.2em]">Harga Satuan</p>
            <div
                class="bg-[#eef2ff] border border-blue-200 px-10 py-2.5 rounded-xl shadow-[0_0_20px_rgba(59,130,246,0.1)] transition-transform hover:scale-105 duration-300">
                <span id="ahs-total-keseluruhan"
                    class="text-2xl md:text-3xl font-black text-blue-700 tabular-nums tracking-tighter">Rp 0</span>
            </div>
        </div>

        <!-- Right: Search -->
        <div class="w-full md:w-64 flex flex-col gap-1.5 self-end">
            <label class="text-[10px] font-bold text-table-subtle uppercase tracking-wider ml-1">Cari Data:</label>
            <div class="relative">
                <input type="text" id="ahs-table-search" placeholder="Masukkan kata kunci..."
                    class="w-full pl-9 pr-4 py-2.5 border border-table-border rounded-xl text-xs focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none bg-slate-50/50 shadow-inner" />
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
                <col style="width: 4rem"> <!-- No -->
                <col style="min-width: 22rem"> <!-- Uraian -->
                <col style="width: 8rem"> <!-- Koefisien -->
                <col style="width: 6rem"> <!-- Satuan -->
                <col style="width: 10rem"> <!-- Harga Dasar -->
                <col style="width: 10rem"> <!-- Harga Satuan (Total) -->
                <col style="width: 8rem"> <!-- Aksi -->
                <col style="width: 9rem"> <!-- Merk -->
                <col style="width: 9rem"> <!-- Spesifikasi -->
                <col style="min-width: 14rem"> <!-- Sumber / Regulasi -->
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
        <div class="shrink-0 bg-white px-6 py-4 flex items-center justify-between border-b border-slate-200 z-20">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-50 border border-blue-100 rounded-xl text-blue-600 shadow-sm">
                    <i class="fas fa-database text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-800 tracking-tight">Database AHS</h2>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Pilih bahan, upah, dan alat dari referensi
                        harga terpadu.</p>
                </div>
            </div>
            <button onclick="document.getElementById('ahs-modal-close').click()"
                class="text-slate-400 hover:text-red-500 hover:bg-red-50 p-2.5 rounded-full transition-colors focus:outline-none">
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
                                class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-inner" />
                            <i
                                class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
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
                            <col> <!-- Nama Bahan -->
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
                                    class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest border-b border-slate-700">
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
                    class="px-5 py-2.5 rounded-xl border-2 border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50 text-xs font-bold tracking-widest uppercase transition-all focus:outline-none active:scale-95 shadow-sm">
                    Batal
                </button>
                <button id="ahs-modal-confirm" type="button" disabled
                    class="px-8 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black tracking-widest uppercase shadow-[0_4px_14px_rgba(37,99,235,0.39)] hover:shadow-[0_6px_20px_rgba(37,99,235,0.23)] transition-all duration-200 focus:outline-none active:scale-95 disabled:opacity-50 disabled:shadow-none disabled:cursor-not-allowed flex items-center gap-2">
                    <i class="fas fa-check-circle text-sm"></i> Pilih Item
                </button>
                <!-- Hidden close button for JS compat -->
                <button id="ahs-modal-close" class="hidden"></button>
            </div>
        </div>
    </div>
</div>
<datalist id="datalist-satuan">
    <!-- Volume / Dimensi -->
    <option value="m">Meter (m)</option>
    <option value="m2">Meter Persegi (m²)</option>
    <option value="m3">Meter Kubik (m³)</option>
    <option value="cm">Sentimeter (cm)</option>
    <option value="mm">Milimeter (mm)</option>
    <option value="km">Kilometer (km)</option>
    <!-- Berat -->
    <option value="kg">Kilogram (kg)</option>
    <option value="ton">Ton</option>
    <option value="gr">Gram (gr)</option>
    <!-- Satuan Umum / Satuan Kerja -->
    <option value="bh">Buah (bh)</option>
    <option value="unit">Unit</option>
    <option value="set">Set</option>
    <option value="ls">Lump Sum (ls)</option>
    <option value="ttk">Titik (ttk)</option>
    <option value="btg">Batang (btg)</option>
    <option value="lbr">Lembar (lbr)</option>
    <option value="mtr">Meter Lari (m')</option>
    <!-- Waktu & Tenaga -->
    <option value="org/hr">Orang/Hari (OH)</option>
    <option value="jam">Jam</option>
    <option value="hari">Hari</option>
    <option value="bln">Bulan</option>
    <option value="mgg">Minggu</option>
    <!-- Kemasan -->
    <option value="zak">Zak</option>
    <option value="gln">Galon (gln)</option>
    <option value="klg">Kaleng (klg)</option>
    <option value="btl">Botol (btl)</option>
    <option value="ktk">Kotak (ktk)</option>
    <option value="rol">Rol</option>
    <option value="dus">Dus</option>
    <!-- Lainnya -->
    <option value="rit">Ritase (rit)</option>
    <option value="pax">Pax</option>
    <option value="liter">Liter (L)</option>
</datalist>