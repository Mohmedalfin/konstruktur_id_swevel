<?php
$wrapperClass = $wrapperClass ?? 'w-full';
?>

<div class="<?= $wrapperClass ?> px-3 sm:px-6 lg:px-8 py-4 md:py-8">

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

        <!-- Title -->
        <h2 class="text-sm md:text-base font-bold text-table-strong truncate">
            Rincian AHS — Pembuatan gudang semen dan peralatan
        </h2>

        <!-- Search -->
        <div class="relative w-full sm:w-64 shrink-0">
            <input id="ahs-search" type="text" placeholder="Cari bahan / upah / alat…"
                class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm border border-table-border rounded-lg bg-white placeholder-table-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-table-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-xl shadow-md border border-table-border bg-white">
        <table class="w-full text-left border-collapse min-w-[860px]" id="ahs-table">

            <colgroup>
                <col style="width: 3.25rem">    
                <col>                            
                <col style="width: 6.5rem">     
                <col style="width: 5.5rem">      
                <col style="width: 10rem">       
                <col style="width: 10rem">       
                <col style="width: 6.5rem">      
                <col style="width: 7.5rem">      
            </colgroup>

            <!-- ── Head ── -->
            <thead>
                <tr class="bg-primary text-white">
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center    text-[10px] md:text-xs font-semibold uppercase tracking-wider">No</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5               text-[10px] md:text-xs font-semibold uppercase tracking-wider">Uraian</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center    text-[10px] md:text-xs font-semibold uppercase tracking-wider">Koefisien</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center    text-[10px] md:text-xs font-semibold uppercase tracking-wider">Satuan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right     text-[10px] md:text-xs font-semibold uppercase tracking-wider">Harga Dasar</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right     text-[10px] md:text-xs font-semibold uppercase tracking-wider">Harga Satuan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center    text-[10px] md:text-xs font-semibold uppercase tracking-wider">Merk</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center    text-[10px] md:text-xs font-semibold uppercase tracking-wider">Spesifikasi</th>
                </tr>
            </thead>

            <!-- ── Body — populated by ajax_ahs.js ── -->
            <tbody id="ahs-tbody" class="text-table-body text-[11px] md:text-[13px]">
                <!-- rows injected here -->
            </tbody>

            <!-- ── Footer totals ── -->
            <tfoot id="ahs-tfoot">
                <tr class="bg-table-category text-white">
                    <td colspan="5" class="px-3 md:px-5 py-2 md:py-2.5 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">Total Keseluruhan RAB</td>
                    <td id="ahs-total-rab" class="px-3 md:px-5 py-2 md:py-2.5 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 2,819,589.63</td>
                    <td colspan="2" class="px-3 md:px-5 py-2 md:py-2.5"></td>
                </tr>
                <tr class="bg-table-category-hover text-white">
                    <td colspan="5" class="px-3 md:px-5 py-2 md:py-2.5 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">Total Keseluruhan RAP Rencana</td>
                    <td id="ahs-total-rap" class="px-3 md:px-5 py-2 md:py-2.5 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap opacity-70">Rp 0</td>
                    <td colspan="2" class="px-3 md:px-5 py-2 md:py-2.5"></td>
                </tr>
            </tfoot>

        </table>
    </div>

    <!-- Save Bar -->
    <div class="mt-4 flex justify-end">
        <button class="bg-primary hover:bg-primary-hover active:scale-95 text-white px-8 py-2 rounded-lg text-xs md:text-sm font-semibold tracking-wide shadow-md transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/40">
            Simpan
        </button>
    </div>

</div>