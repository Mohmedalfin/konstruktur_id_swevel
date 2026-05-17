<div id="modal-category-detail" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-7 ease-out transition-all sm:max-w-[900px] sm:w-full sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center justify-center p-4">
        <div class="flex flex-col bg-slate-50 border shadow-2xl rounded-2xl overflow-hidden relative pointer-events-auto w-full">         
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 rounded-bl-[100%] pointer-events-none"></div>

            <div class="flex justify-between items-center py-5 px-8 border-b border-slate-200 bg-white relative z-10">
                <div class="flex-1 min-w-0 mr-4">
                    <h3 class="text-base font-black text-slate-800 tracking-tight truncate" id="modal-cat-title">
                        Detail Kategori: <span class="animate-pulse bg-slate-100 text-transparent rounded inline-block w-40">Loading</span>
                    </h3>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    <div class="hidden items-center px-4 py-2 rounded-full text-[10px] font-black shadow-sm border whitespace-nowrap" id="modal-cat-spi-pill">
                        <span id="modal-cat-spi-text">Slight Delay (SPI 0.93)</span>
                    </div>

                    <button type="button" class="w-10 h-10 flex justify-center items-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-all duration-300 border border-transparent hover:border-slate-100 active:scale-95" data-hs-overlay="#modal-category-detail">
                        <span class="sr-only">Tutup</span>
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 relative z-10 flex flex-col gap-5">               
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-sm font-black text-slate-800">Progress Kategori</h4>
                            <div class="w-6 h-6 rounded-full bg-slate-50 flex items-center justify-center text-slate-600">
                                <i class="fas fa-chart-pie text-[10px]"></i>
                            </div>
                        </div>
                        
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-bold text-slate-400 w-12 uppercase tracking-wider">Target</span>
                                <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-rose-500 rounded-full transition-all duration-700" id="modal-cat-bar-target" style="width: 0%"></div>
                                </div>
                                <span class="text-[10px] font-black text-slate-600 w-8 text-right" id="modal-cat-text-target">0%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-bold text-slate-400 w-12 uppercase tracking-wider">Aktual</span>
                                <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full transition-all duration-700" id="modal-cat-bar-actual" style="width: 0%"></div>
                                </div>
                                <span class="text-[10px] font-black text-slate-600 w-8 text-right" id="modal-cat-text-actual">0%</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-[10px] font-black" id="modal-cat-deviasi-pill">
                                <i class="fas fa-exclamation-circle"></i>
                                <span id="modal-cat-deviasi-text">Deviasi 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-sm font-black text-slate-800">Biaya Kategori</h4>
                                <div class="w-6 h-6 rounded-full bg-slate-50 flex items-center justify-center text-slate-600">
                                    <i class="fas fa-coins text-[10px]"></i>
                                </div>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Realisasi Kategori</p>
                            <h2 class="text-xl font-black text-slate-800 tracking-tight mb-2 truncate" id="modal-cat-ac">
                                Rp 0
                            </h2>
                        </div>
                        <div class="pt-3 border-t border-slate-100">
                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-[10px] font-black" id="modal-cat-cpi-pill">
                                <i class="fas fa-check-circle"></i>
                                <span id="modal-cat-cpi-text">On Budget (CPI 1.00)</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-all flex flex-col justify-center min-h-[145px]">
                        <div class="absolute top-0 left-0 right-0 p-4 pb-0">
                            <div class="flex justify-between items-start">
                                <h4 class="text-sm font-black text-slate-800">Timeline Kategori</h4>
                                <div class="w-6 h-6 rounded-full bg-slate-50 flex items-center justify-center text-slate-600">
                                    <i class="fas fa-calendar-alt text-[10px]"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col mt-4">
                            <h2 class="text-lg font-black text-slate-800 tracking-tight mb-3" id="modal-cat-week-text">
                                Minggu -- dari --
                            </h2>
                            <div class="w-full">
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden mb-4">
                                    <div class="h-full bg-blue-500 rounded-full transition-all duration-700" id="modal-cat-time-bar" style="width: 0%"></div>
                                </div>
                                <div class="flex justify-between items-center text-[10px] font-bold text-slate-500">
                                    <span id="modal-cat-start-date">-</span>
                                    <span id="modal-cat-finish-date">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 

                <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden flex flex-col">
                    <div class="overflow-x-auto [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:bg-slate-200 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-slate-50 h-[250px] overflow-y-auto">
                        <table class="w-full text-left border-separate border-spacing-0 min-w-[700px]">
                            <thead class="sticky top-0 z-20">
                                <tr class="bg-slate-50/95 backdrop-blur-md">
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center w-12 border-b border-slate-200 shadow-[0_1px_0_0_rgba(226,232,240,1)]">No</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-200 shadow-[0_1px_0_0_rgba(226,232,240,1)]">Uraian Pekerjaan</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center border-b border-slate-200 shadow-[0_1px_0_0_rgba(226,232,240,1)]">Volume</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center border-b border-slate-200 shadow-[0_1px_0_0_rgba(226,232,240,1)]">Volume Tercapai</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center border-b border-slate-200 shadow-[0_1px_0_0_rgba(226,232,240,1)]">Bobot(%)</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center w-28 border-b border-slate-200 shadow-[0_1px_0_0_rgba(226,232,240,1)]">Progress</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center w-28 border-b border-slate-200 shadow-[0_1px_0_0_rgba(226,232,240,1)]">Status</th>
                                </tr>
                            </thead>
                            <tbody id="modal-cat-table-body" class="bg-white">
                                <?php for($i=0; $i<4; $i++): ?>
                                <tr class="h-[50px] border-b border-slate-50 last:border-0">
                                    <td class="px-4 py-2 text-center border-b border-slate-100/50"><div class="h-3 w-4 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
                                    <td class="px-4 py-2 border-b border-slate-100/50"><div class="h-3 w-32 bg-slate-100 animate-pulse rounded"></div></td>
                                    <td class="px-4 py-2 border-b border-slate-100/50"><div class="h-3 w-12 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
                                    <td class="px-4 py-2 border-b border-slate-100/50"><div class="h-3 w-12 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
                                    <td class="px-4 py-2 border-b border-slate-100/50"><div class="h-3 w-8 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
                                    <td class="px-4 py-2 border-b border-slate-100/50"><div class="h-3 w-16 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
                                    <td class="px-4 py-2 border-b border-slate-100/50"><div class="h-4 w-16 bg-slate-100 animate-pulse rounded-full mx-auto"></div></td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
