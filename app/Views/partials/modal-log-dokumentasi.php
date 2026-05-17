<div id="modal-log-dokumentasi" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-7 ease-out transition-all sm:max-w-3xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center justify-center">
        <div class="flex flex-col bg-white border shadow-xl rounded-xl pointer-events-auto w-full overflow-hidden">
            <div class="bg-[#0f172a] px-4 py-3 md:px-6 md:py-4 flex items-center justify-between rounded-t-xl border-b border-slate-800">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 md:w-10 md:h-10 rounded border border-green-500/30 flex items-center justify-center bg-transparent text-green-500">
                        <i class="fas fa-images text-base md:text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-base md:text-lg">Log Dokumentasi</h2>
                        <p class="text-slate-400 text-[10px] md:text-xs mt-0.5">Riwayat progres dan galeri foto</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" data-hs-overlay="#modal-log-dokumentasi">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5 md:p-8 overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-h-[80vh]">
                <div class="mb-8">
                    <div class="flex-1">
                            <label class="text-md font-semibold text-[#1e293b]">Filter Tanggal</label>
                            <p class="text-xs text-slate-500 border-b border-slate-300 pb-2 mb-2">Urutkan log berdasarkan tanggal</p>

                            <div class="flex flex-wrap items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-1.5 shadow-sm w-full sm:max-w-max">
                                <div class="flex items-center gap-2 flex-1 sm:flex-initial">
                                    <i class="fas fa-calendar-alt text-slate-400 text-[10px]"></i>
                                    <input type="date" id="log-filter-start" class="border-none focus:ring-0 text-[11px] sm:text-xs font-semibold text-slate-600 p-0 bg-transparent cursor-pointer w-full sm:w-[100px]" title="Tanggal Mulai">
                                </div>
                                <span class="text-slate-400 text-[9px] font-bold uppercase mx-auto sm:mx-0">s/d</span>
                                <div class="flex items-center gap-2 flex-1 sm:flex-initial">
                                    <input type="date" id="log-filter-end" class="border-none focus:ring-0 text-[11px] sm:text-xs font-semibold text-slate-600 p-0 bg-transparent cursor-pointer w-full sm:w-[100px]" title="Tanggal Akhir">
                                </div>
                                <button type="button" id="log-filter-clear" class="hidden w-5 h-5 items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-red-500 transition-colors ml-1 focus:outline-none" title="Hapus Filter">
                                    <i class="fas fa-times text-[10px]"></i>
                                </button>
                            </div>
                    </div>
                </div>

                <div class="relative pl-6 sm:pl-8" id="log-timeline-container">
                    <div class="absolute left-[9px] top-2 bottom-0 w-[3px] bg-[#0f172a] rounded-full"></div>

                    <div class="relative mb-8 timeline-item" data-date="17-03-2026">
                        <div class="absolute -left-[32px] top-1.5 w-5 h-5 rounded-full bg-[#f59e0b] border-[4px] border-white shadow-sm z-10"></div>
                        
                        <h3 class="text-md font-semibold text-[#1e293b] mb-4">17-03-2026</h3>

                        <div class="space-y-3">
                            <div class="bg-[#e2e8f0]/60 rounded-lg p-5 border border-[#cbd5e1]/50">
                                <div class="flex items-center gap-2.5 mb-3">
                                    <h4 class="font-semibold text-[#1e293b] text-sm">Daftar Pekerjaan</h4>
                                </div>
                                <ul class="space-y-1.5 ml-7 list-disc text-[#1e293b] text-[13px]">
                                    <li>Pembuatan gudang semen dan peralatan (40 m2)</li>
                                    <li>Buangan tanah galian (30 m2)</li>
                                </ul>
                            </div>

                            <div class="bg-[#e2e8f0]/60 rounded-lg p-5 border border-[#cbd5e1]/50">
                                <div class="flex items-center gap-2.5 mb-3">
                                    <h4 class="font-semibold text-[#1e293b] text-sm">Dokumentasi</h4>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <div class="w-24 h-24 rounded-lg overflow-hidden border border-white shadow-sm">
                                        <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&q=80&w=300" class="w-full h-full object-cover">
                                    </div>
                                    <div class="w-24 h-24 rounded-lg overflow-hidden border border-white shadow-sm">
                                        <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&q=80&w=300" class="w-full h-full object-cover">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative mb-6 timeline-item" data-date="16-03-2026">
                        <div class="absolute -left-[32px] top-1.5 w-5 h-5 rounded-full bg-[#f59e0b] border-[4px] border-white shadow-sm z-10"></div>
                        
                        <h3 class="text-md font-semibold text-[#1e293b] mb-4">16-03-2026</h3>

                        <div class="bg-[#e2e8f0]/60 rounded-lg p-5 border border-[#cbd5e1]/50 flex items-center justify-center min-h-[80px]">
                            <p class="text-slate-500 font-semibold text-sm">Sama seperti di atas...</p>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
