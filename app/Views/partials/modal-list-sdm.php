<div id="modal-list-sdm" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-0 ease-out transition-all sm:max-w-4xl sm:w-full sm:mx-auto min-h-full flex items-center justify-center p-4">
        <div class="flex flex-col bg-white shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] rounded-2xl pointer-events-auto w-full overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-[#1e293b] text-white px-5 py-4 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-600/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20">
                        <i class="fas fa-clipboard-list text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-base sm:text-lg">Daftar Kebutuhan Proyek</h2>
                        <p class="text-slate-400 text-xs mt-0.5">List seluruh kebutuhan Bahan, Alat, dan Tenaga</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" data-hs-overlay="#modal-list-sdm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-4 md:p-5 overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-h-[85vh]">
                
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                        <input type="text" id="search-list-sdm" class="py-2.5 ps-11 pe-4 block w-full border border-slate-200 rounded-xl text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white transition-colors" placeholder="Cari kebutuhan (contoh: Semen, Paku)...">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4" aria-label="Tabs" role="tablist">
                    <button type="button" class="group flex items-center justify-between p-3 rounded-xl border-2 border-slate-100 bg-white hover:border-slate-200 [&.active]:border-indigo-600 [&.active]:bg-indigo-50/30 text-left focus:outline-none transition-all active" id="tab-list-bahan" data-hs-tab="#content-list-bahan" aria-controls="content-list-bahan" role="tab">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0">
                                <i class="fas fa-box text-xs"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-700 group-[.active]:text-indigo-700 text-sm">Bahan</h3>
                                <p class="text-[10px] text-slate-500 font-medium leading-tight">Daftar kebutuhan bahan</p>
                            </div>
                        </div>
                        <div class="bg-slate-100 text-slate-500 group-[.active]:bg-indigo-100 group-[.active]:text-indigo-700 text-[11px] font-bold px-2 py-0.5 rounded-full badge-bahan">0</div>
                    </button>
                    <button type="button" class="group flex items-center justify-between p-3 rounded-xl border-2 border-slate-100 bg-white hover:border-slate-200 [&.active]:border-indigo-600 [&.active]:bg-indigo-50/30 text-left focus:outline-none transition-all" id="tab-list-alat" data-hs-tab="#content-list-alat" aria-controls="content-list-alat" role="tab">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                <i class="fas fa-tools text-xs"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-700 group-[.active]:text-indigo-700 text-sm">Alat</h3>
                                <p class="text-[10px] text-slate-500 font-medium leading-tight">Daftar kebutuhan alat</p>
                            </div>
                        </div>
                        <div class="bg-slate-100 text-slate-500 group-[.active]:bg-indigo-100 group-[.active]:text-indigo-700 text-[11px] font-bold px-2 py-0.5 rounded-full badge-alat">0</div>
                    </button>
                    <button type="button" class="group flex items-center justify-between p-3 rounded-xl border-2 border-slate-100 bg-white hover:border-slate-200 [&.active]:border-indigo-600 [&.active]:bg-indigo-50/30 text-left focus:outline-none transition-all" id="tab-list-upah" data-hs-tab="#content-list-upah" aria-controls="content-list-upah" role="tab">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                <i class="fas fa-hard-hat text-xs"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-700 group-[.active]:text-indigo-700 text-sm">Tenaga</h3>
                                <p class="text-[10px] text-slate-500 font-medium leading-tight">Daftar kebutuhan tenaga</p>
                            </div>
                        </div>
                        <div class="bg-slate-100 text-slate-500 group-[.active]:bg-indigo-100 group-[.active]:text-indigo-700 text-[11px] font-bold px-2 py-0.5 rounded-full badge-upah">0</div>
                    </button>
                </div>

                <!-- Content Area -->
                <div class="border border-slate-200 rounded-xl bg-white flex flex-col overflow-hidden h-[250px]">
                    <div class="grid grid-cols-12 gap-4 px-5 py-2.5 bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-widest shrink-0">
                        <div class="col-span-8">ITEM</div>
                        <div class="col-span-2 text-left">KEBUTUHAN</div>
                        <div class="col-span-2 text-left">SISA</div>
                    </div>

                    <div class="p-0 overflow-y-auto flex-1">
                        <div id="content-list-bahan" class="active" role="tabpanel" aria-labelledby="tab-list-bahan">
                            <div class="hs-accordion-group" id="accordion-list-bahan">
                            </div>
                            <div id="empty-list-bahan" class="hidden py-10 text-center">
                                <div class="inline-flex w-12 h-12 rounded-full bg-slate-50 items-center justify-center mb-2">
                                    <i class="fas fa-box-open text-xl text-slate-300"></i>
                                </div>
                                <h3 class="text-slate-600 font-bold mb-1">Tidak Ada Bahan</h3>
                                <p class="text-[11px] text-slate-400">Bahan tidak ditemukan atau belum ditambahkan</p>
                            </div>
                        </div>

                        <div id="content-list-alat" class="hidden" role="tabpanel" aria-labelledby="tab-list-alat">
                            <div class="hs-accordion-group" id="accordion-list-alat">
                            </div>
                            <div id="empty-list-alat" class="hidden py-10 text-center">
                                <div class="inline-flex w-12 h-12 rounded-full bg-slate-50 items-center justify-center mb-2">
                                    <i class="fas fa-tools text-xl text-slate-300"></i>
                                </div>
                                <h3 class="text-slate-600 font-bold mb-1">Tidak Ada Alat</h3>
                                <p class="text-[11px] text-slate-400">Alat tidak ditemukan atau belum ditambahkan</p>
                            </div>
                        </div>

                        <div id="content-list-upah" class="hidden" role="tabpanel" aria-labelledby="tab-list-upah">
                            <div class="hs-accordion-group" id="accordion-list-upah">
                            </div>
                            <div id="empty-list-upah" class="hidden py-10 text-center">
                                <div class="inline-flex w-12 h-12 rounded-full bg-slate-50 items-center justify-center mb-2">
                                    <i class="fas fa-hard-hat text-xl text-slate-300"></i>
                                </div>
                                <h3 class="text-slate-600 font-bold mb-1">Tidak Ada Tenaga</h3>
                                <p class="text-[11px] text-slate-400">Tenaga tidak ditemukan atau belum ditambahkan</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="px-5 py-3 border-t border-slate-100 bg-white flex items-center justify-between">
                <div class="text-sm text-slate-500">
                    Total <span class="font-bold text-slate-700" id="total-items-text">0 item</span>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" class="px-5 py-2.5 text-sm font-bold text-indigo-600 bg-white border border-indigo-200 hover:border-indigo-600 hover:bg-indigo-50 rounded-xl transition-all focus:outline-none flex items-center gap-2">
                        <i class="fas fa-file-export"></i> Export
                    </button>
                    <button type="button" class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-md focus:outline-none" data-hs-overlay="#modal-list-sdm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    #modal-list-sdm [role="tablist"] button.active {
        border-bottom-color: #4f46e5 !important; 
        color: #4f46e5 !important;
        background-color: transparent !important;
    }
</style>
