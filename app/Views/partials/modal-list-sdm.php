<div id="modal-list-sdm" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-0 ease-out transition-all sm:max-w-4xl sm:w-full sm:mx-auto min-h-full flex items-center justify-center p-4">
        <div class="flex flex-col bg-[#f8f9fa] border shadow-xl rounded-xl pointer-events-auto w-full overflow-hidden">
            
            <div class="bg-indigo-900 px-4 py-3 md:px-6 md:py-4 flex items-center justify-between rounded-t-xl border-b border-indigo-800">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 md:w-10 md:h-10 rounded border border-indigo-400/30 flex items-center justify-center bg-transparent text-indigo-400">
                        <i class="fas fa-clipboard-list text-base md:text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-base md:text-lg">Daftar Kebutuhan Proyek</h2>
                        <p class="text-indigo-200 text-[10px] md:text-xs mt-0.5">List seluruh kebutuhan Bahan, Alat, dan Tenaga</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-indigo-800/80 text-indigo-200 hover:bg-indigo-700 hover:text-white transition-colors focus:outline-none" data-hs-overlay="#modal-list-sdm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-4 md:p-6 overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden h-[80vh]">
                
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3.5">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                        <input type="text" id="search-list-sdm" class="py-2.5 ps-10 pe-4 block w-full border border-slate-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-white shadow-sm" placeholder="Cari kebutuhan (contoh: Semen, Paku)...">
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
                    <nav class="flex w-full border-b border-slate-200 bg-slate-50 rounded-t-xl" aria-label="Tabs" role="tablist">
                        <button type="button" class="flex-1 py-3 px-2 sm:px-4 inline-flex justify-center items-center gap-2 border-b-2 border-transparent text-xs sm:text-sm font-bold text-slate-500 hover:text-slate-700 active" id="tab-list-bahan" data-hs-tab="#content-list-bahan" aria-controls="content-list-bahan" role="tab">
                            <i class="fas fa-box text-orange-500"></i> Bahan
                        </button>
                        <button type="button" class="flex-1 py-3 px-2 sm:px-4 inline-flex justify-center items-center gap-2 border-b-2 border-transparent text-xs sm:text-sm font-bold text-slate-500 hover:text-slate-700" id="tab-list-alat" data-hs-tab="#content-list-alat" aria-controls="content-list-alat" role="tab">
                            <i class="fas fa-tools text-blue-500"></i> Alat
                        </button>
                        <button type="button" class="flex-1 py-3 px-2 sm:px-4 inline-flex justify-center items-center gap-2 border-b-2 border-transparent text-xs sm:text-sm font-bold text-slate-500 hover:text-slate-700" id="tab-list-upah" data-hs-tab="#content-list-upah" aria-controls="content-list-upah" role="tab">
                            <i class="fas fa-hard-hat text-red-500"></i> Tenaga
                        </button>
                    </nav>

                    <div class="p-0 md:p-0">
                        <div id="content-list-bahan" class="active" role="tabpanel" aria-labelledby="tab-list-bahan">
                            <div class="hs-accordion-group" id="accordion-list-bahan">
                                <!-- JS render -->
                            </div>
                            <div id="empty-list-bahan" class="hidden py-10 flex flex-col items-center justify-center text-center">
                                <i class="fas fa-box-open text-4xl text-slate-300 mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Tidak ada data bahan</p>
                            </div>
                        </div>

                        <div id="content-list-alat" class="hidden" role="tabpanel" aria-labelledby="tab-list-alat">
                            <div class="hs-accordion-group" id="accordion-list-alat">
                                <!-- JS render -->
                            </div>
                            <div id="empty-list-alat" class="hidden py-10 flex flex-col items-center justify-center text-center">
                                <i class="fas fa-toolbox text-4xl text-slate-300 mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Tidak ada data alat</p>
                            </div>
                        </div>

                        <div id="content-list-upah" class="hidden" role="tabpanel" aria-labelledby="tab-list-upah">
                            <div class="hs-accordion-group" id="accordion-list-upah">
                                <!-- JS render -->
                            </div>
                            <div id="empty-list-upah" class="hidden py-10 flex flex-col items-center justify-center text-center">
                                <i class="fas fa-user-hard-hat text-4xl text-slate-300 mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Tidak ada data tenaga</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<style>
    #modal-list-sdm [role="tablist"] button.active {
        border-bottom-color: #4f46e5 !important; /* indigo-600 */
        color: #4f46e5 !important;
        background-color: transparent !important;
    }
</style>
