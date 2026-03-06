<?php
$tableVisible = isset($tableVisible) && $tableVisible;
$wrapperClass = $tableVisible ? '' : 'hidden';
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
        <div class="flex items-center gap-2 shrink-0">

            <!-- Download Template -->
            <button id="boq-download-template-btn" type="button"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-amber-400 hover:bg-amber-500 text-black text-xs font-semibold transition-all duration-150 focus:outline-none active:scale-95 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Template
            </button>

            <!-- Import BOQ -->
            <button id="boq-import-btn" type="button"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-primary hover:bg-primary-hover text-white text-xs font-semibold transition-all duration-150 focus:outline-none active:scale-95 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import BOQ
            </button>

            <!-- Hidden file input -->
            <input id="boq-file-input" type="file" accept=".xlsx,.xls,.csv" class="hidden" />

        </div>

    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-xl shadow-md border border-table-border bg-white">
        <table class="w-full text-left min-w-[800px] border-collapse" id="rab-table">

            <!-- Column widths — locked permanently, never shift on open/close -->
            <colgroup>
                <col style="width: 3.5rem">     <!-- No -->
                <col>                           <!-- Uraian Pekerjaan (flexible) -->
                <col style="width: 5rem">       <!-- Volume -->
                <col style="width: 5rem">       <!-- Satuan -->
                <col style="width: 9rem">       <!-- Harga Bahan -->
                <col style="width: 9rem">       <!-- Harga Alat -->
                <col style="width: 9rem">       <!-- Harga Upah -->
                <col style="width: 9rem">       <!-- Harga Keseluruhan -->
                <col style="width: 7rem">       <!-- Aksi -->
            </colgroup>

            <!-- Table Head (static — never changes) -->
            <thead>
                <tr class="bg-primary text-white">
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider w-12 md:w-14">No</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-[10px] md:text-xs font-semibold uppercase tracking-wider">Uraian Pekerjaan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider w-16 md:w-20">Volume</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider w-16 md:w-20">Satuan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider">Harga Bahan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider">Harga Alat</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider">Harga Upah</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider">Harga Keseluruhan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider w-20 md:w-24">Aksi</th>
                </tr>
            </thead>

            <!-- Table Body — populated by ajax_rab.js -->
            <tbody id="rab-tbody" class="text-table-body text-[11px] md:text-[13px]">
                <!-- rows injected here -->
            </tbody>

            <!-- Table Footer — updated by ajax_rab.js -->
            <tfoot id="rab-tfoot">
                <tr class="bg-table-category text-white">
                    <td colspan="7" class="px-3 md:px-5 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">Jumlah Harga</td>
                    <td id="rab-total-jumlah" class="px-3 md:px-5 py-1.5 md:py-2 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 0</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                </tr>
                <tr class="bg-table-category-hover text-white">
                    <td colspan="7" class="px-3 md:px-5 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">PPN 11%</td>
                    <td id="rab-total-ppn" class="px-3 md:px-5 py-1.5 md:py-2 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 0</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                </tr>
                <tr class="bg-table-category text-white">
                    <td colspan="7" class="px-3 md:px-5 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">Total Harga</td>
                    <td id="rab-total-final" class="px-3 md:px-5 py-1.5 md:py-2 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 0</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>