<div class="max-w-[90rem] mx-auto px-3 sm:px-6 lg:px-8 py-4 md:py-8">

    <!-- Search Bar (right-aligned) -->
    <div class="flex justify-end mb-4">
        <div class="relative w-full sm:w-64">
            <input type="text" placeholder="Cari pekerjaan..."
                class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>  
        </div>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-xl shadow-md border border-gray-200/80 bg-white">
        <table class="w-full text-left min-w-[800px] border-collapse" id="rab-table">

            <!-- Table Head -->
            <thead>
                <tr class="bg-primary text-white">
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider w-12 md:w-14">No</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-[10px] md:text-xs font-semibold uppercase tracking-wider">Uraian Pekerjaan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider w-16 md:w-20">Volume</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider w-16 md:w-20">Satuan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider">Harga Dasar</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider">Harga</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider w-12 md:w-16">%</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider w-20 md:w-24">Aksi</th>
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="text-gray-700 text-[11px] md:text-[13px]">

                <!-- ======== Category: Pekerjaan Persiapan ======== -->
                <tr class="rab-category bg-[#3b5278] text-white hover:bg-[#344a6b] cursor-pointer select-none transition-colors duration-200"
                    onclick="toggleAccordion('persiapan')" role="button" aria-expanded="true" tabindex="0">
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                        <svg id="icon-persiapan" class="w-4 h-4 md:w-5 md:h-5 mx-auto opacity-80 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </td>
                    <td colspan="6" class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-[10px] md:text-xs uppercase tracking-widest">
                        <span class="flex items-center gap-2">
                            <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                            Pekerjaan Persiapan
                        </span>
                    </td>
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                        <svg id="chevron-persiapan" class="w-3.5 h-3.5 md:w-4 md:h-4 mx-auto opacity-60 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                    </td>
                </tr>

                <!-- Sub-row 1 -->
                <tr class="subrow-persiapan bg-gray-50/70 border-b border-gray-200/60 transition-colors duration-150">
                    <td class="p-0 text-center text-gray-500">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out">1</div>
                    </td>
                    <td class="p-0">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out font-medium text-gray-800">Pembuatan gudang semen dan peralatan</div>
                    </td>
                    <td class="p-0 text-center">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out tabular-nums">1.00</div>
                    </td>
                    <td class="p-0 text-center text-gray-500">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out">m²</div>
                    </td>
                    <td class="p-0 text-right">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out tabular-nums">Rp 32.621,60</div>
                    </td>
                    <td class="p-0 text-right">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out tabular-nums font-semibold text-gray-900">Rp 32.621,60</div>
                    </td>
                    <td class="p-0 text-center">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out text-gray-400">0.00%</div>
                    </td>
                    <td class="p-0 text-center">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out">
                            <button class="bg-primary hover:bg-primary-hover active:scale-95 text-white px-2.5 md:px-3.5 py-1 rounded-md text-[10px] md:text-xs font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                Detail
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Sub-row 2 -->
                <tr class="subrow-persiapan bg-gray-50/70 border-b border-gray-200/60 transition-colors duration-150">
                    <td class="p-0 text-center text-gray-500">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out ">2</div>
                    </td>
                    <td class="p-0">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out  font-medium text-gray-800">Buangan tanah galian</div>
                    </td>
                    <td class="p-0 text-center">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out  tabular-nums">1.00</div>
                    </td>
                    <td class="p-0 text-center text-gray-500">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out ">m²</div>
                    </td>
                    <td class="p-0 text-right">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out  tabular-nums">Rp 32.621,60</div>
                    </td>
                    <td class="p-0 text-right">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out  tabular-nums font-semibold text-gray-900">Rp 32.621,60</div>
                    </td>
                    <td class="p-0 text-center">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out  text-gray-400">0.00%</div>
                    </td>
                    <td class="p-0 text-center">
                        <div class="expand-content max-h-[60px] opacity-100 py-1.5 md:py-2 px-3 md:px-5 overflow-hidden transition-all duration-300 ease-in-out ">
                            <button class="bg-primary hover:bg-primary-hover active:scale-95 text-white px-2.5 md:px-3.5 py-1 rounded-md text-[10px] md:text-xs font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                Detail
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>

            <!-- Table Footer: Totals -->
            <tfoot>
                <!-- Jumlah Harga -->
                <tr class="bg-[#3b5278] text-white">
                    <td colspan="5" class="px-3 md:px-5 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">Jumlah Harga</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 0,00</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">0.00 %</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                </tr>
                <!-- PPN -->
                <tr class="bg-[#344a6b] text-white">
                    <td colspan="5" class="px-3 md:px-5 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">PPN 0.00 %</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 0,00</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                </tr>
                <!-- Total Harga -->
                <tr class="bg-[#3b5278] text-white">
                    <td colspan="5" class="px-3 md:px-5 py-1.5 md:py-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wide whitespace-nowrap">Total Harga</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2 text-right text-[10px] md:text-xs font-bold tabular-nums whitespace-nowrap">Rp 0,00</td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                    <td class="px-3 md:px-5 py-1.5 md:py-2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>