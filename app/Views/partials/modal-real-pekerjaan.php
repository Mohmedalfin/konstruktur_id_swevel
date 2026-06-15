<style>
    #modal-tambah-realisasi .rounded-table-container table tr:last-child td:first-child { border-bottom-left-radius: 0.75rem; }
    #modal-tambah-realisasi .rounded-table-container table tr:last-child td:last-child { border-bottom-right-radius: 0.75rem; }

    [data-hs-select-dropdown] [data-value=""][data-disabled] span,
    .hs-select-dropdown [aria-disabled="true"] span {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.08em !important;
        text-transform: uppercase !important;
        cursor: default !important;
        pointer-events: none !important;
        border-radius: 6px !important;
        display: block;
    }
    
    #real-task-select + div .hs-select-option[data-is-category="true"],
    [id^="hs-select-"] div [aria-disabled="true"] {
        background: #f1f5f9 !important;
        color: #64748b !important;
        font-weight: 700 !important;
        font-size: 10px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.07em !important;
        cursor: default !important;
        pointer-events: none !important;
        padding: 6px 12px !important;
        border-radius: 6px;
        margin-top: 4px;
    }
</style>

<div id="modal-tambah-realisasi" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-0 ease-out transition-all sm:max-w-6xl sm:w-full sm:mx-auto min-h-full flex items-center justify-center p-4">
        <div class="flex flex-col bg-[#f8f9fa] border shadow-xl rounded-xl pointer-events-auto w-full overflow-hidden">
            
            <div class="bg-[#0f172a] px-4 py-3 md:px-6 md:py-4 flex items-center justify-between rounded-t-xl border-b border-slate-800">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 md:w-10 md:h-10 rounded border border-green-500/30 flex items-center justify-center bg-transparent text-green-500">
                        <i class="fas fa-clipboard-list text-base md:text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-base md:text-lg">Tambah Realisasi Pekerjaan</h2>
                        <p class="text-slate-400 text-[10px] md:text-xs mt-0.5">Catat progress fisik pekerjaan di lapangan</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" data-hs-overlay="#modal-tambah-realisasi">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5 md:p-10 overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-h-[85vh]">
                <div class="mb-4">
                    <div class="flex flex-col mb-4 mt-2">
                        <label class="text-md font-semibold text-[#1e293b]">Tanggal</label>
                        <p class="text-xs text-slate-500 mb-2">Tanggal pelaksanaan pekerjaan hari ini</p>

                        <div class="flex-1">
                            <div class="relative">
                                <input type="date" id="real-tanggal" value="<?= date('Y-m-d') ?>" class="py-2.5 px-4 block w-full md:w-[270px] border border-gray-300 bg-[#f8f9fa] text-sm focus:border-primary focus:ring-1 focus:ring-primary font-medium text-gray-700 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-md font-semibold text-[#1e293b]">Progress Pekerjaan</label>
                        <p class="text-xs text-slate-500 mb-2">Progress pekerjaan hari ini</p>

                        <div class="bg-white border border-[#e2e8f0] rounded-xl overflow-visible mb-6 shadow-sm relative z-10">
                            <div class="flex flex-col lg:flex-row">
                                <div class="flex-1 p-4 md:p-6 bg-white rounded-xl">
                                    <div class="flex flex-col gap-5">
                                        <div class="relative w-full">
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Pilih Item Pekerjaan</label>
                                            <select id="real-task-select" data-hs-select='{
                                                "hasSearch": true,
                                                "searchPlaceholder": "Cari pekerjaan...",
                                                "searchClasses": "block w-full text-sm border-gray-200 rounded-md focus:border-primary focus:ring-primary py-2 px-3",
                                                "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0",
                                                "placeholder": "Pilih Pekerjaan...",
                                                "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                                                "toggleClasses": "relative py-3 px-4 flex items-center w-full cursor-pointer bg-white border border-gray-300 rounded-lg text-start text-sm hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-primary font-semibold text-slate-700 transition-all",
                                                "dropdownClasses": "mt-2 z-[60] w-full max-h-[300px] p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden overflow-y-auto",
                                                "optionClasses": "py-2.5 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 hs-selected:bg-blue-50 hs-selected:text-blue-600 hs-selected:font-semibold",
                                                "extraMarkup": "<div class=\"absolute top-1/2 end-4 -translate-y-1/2\"><i class=\"fas fa-chevron-down text-slate-400 text-[10px]\"></i></div>"
                                            }' class="hidden">
                                                <option value="">Pilih Pekerjaan</option>
                                            </select>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-start">
                                            <div class="space-y-1.5 flex justify-between sm:block border-b border-slate-50 sm:border-none pb-2 sm:pb-0">
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Satuan</label>
                                                <div class="sm:py-2.5">
                                                    <div id="real-satuan-display" class="text-sm sm:text-[15px] font-semibold text-[#22c55e] leading-none">-</div>
                                                </div>
                                                <input type="hidden" id="real-satuan">
                                            </div>
                                            <div class="space-y-1.5 flex justify-between sm:block border-b border-slate-50 sm:border-none pb-2 sm:pb-0">
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sisa Target</label>
                                                <div class="sm:py-2.5">
                                                    <div id="real-vol-sisa-display" class="text-sm sm:text-[15px] font-semibold text-[#22c55e] leading-none">0</div>
                                                </div>
                                                <input type="hidden" id="real-vol-target">
                                            </div>
                                            <div class="space-y-1.5">
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Realisasi (Input)</label>
                                                <input type="number" id="real-vol-actual" step="0.01" min="0" placeholder="0.00" class="py-2 px-3 sm:py-2.5 sm:px-3 block w-full border border-gray-300 bg-white text-sm text-center font-bold text-slate-800 focus:border-primary focus:ring-1 focus:ring-primary rounded-lg shadow-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-[1.2] p-4 md:p-6 bg-white flex flex-col">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Keterangan / Catatan Lapangan</label>
                                    <textarea id="real-keterangan" placeholder="Tuliskan detail pelaksanaan, kendala, atau informasi penting lainnya di sini..." class="flex-1 py-3 px-4 block w-full border border-gray-300 bg-white text-sm placeholder-gray-400 focus:border-primary focus:ring-1 focus:ring-primary rounded-lg resize-none min-h-[100px] md:min-h-[120px]"></textarea>
                                    
                                    <!-- Mobile Add Button -->
                                    <button type="button" id="btn-add-progress-mobile" class="md:hidden mt-4 w-full py-3 px-4 inline-flex justify-center items-center gap-2 rounded-lg bg-green-500 text-white font-bold text-sm shadow-sm transition-all focus:outline-none">
                                        <i class="fas fa-plus"></i> Tambahkan ke Daftar
                                    </button>
                                </div>

                                <div class="hidden md:flex w-16 bg-slate-50/30 items-start justify-center p-4 pt-8 rounded-tr-xl rounded-br-xl">
                                    <button type="button" id="btn-add-progress" class="flex-none w-8 h-8 inline-flex justify-center items-center rounded-lg bg-green-500 hover:bg-green-600 text-white transition-all shadow-md hover:shadow-green-100 active:scale-95 group" title="Tambahkan ke daftar">
                                        <i class="fas fa-plus text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="text-sm font-bold text-[#1e293b] flex items-center gap-2 mb-2">
                                <i class="fas fa-list-ul text-primary"></i> 
                                Daftar Item Ditambahkan
                            </label>
                            <div class="w-full bg-white border border-[#e2e8f0] overflow-x-auto rounded-xl shadow-sm">
                                <div class="min-w-[800px] md:min-w-0">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-[#0f172a] text-white">
                                                <th class="px-4 py-3 text-center text-xs font-semibold w-12">No</th>
                                                <th class="px-4 py-3 text-xs font-semibold">Uraian Pekerjaan</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold w-24">Satuan</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold w-28">Sisa Target</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold w-32">Vol. Input</th>
                                                <th class="px-4 py-3 text-xs font-semibold min-w-[200px]">Keterangan</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold w-20">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="batch-progress-tbody" class="divide-y divide-slate-100">
                                            <tr id="batch-empty-row">
                                                <td colspan="7" class="px-4 py-10 text-center text-slate-400 italic text-sm">
                                                    Belum ada item yang ditambahkan ke daftar simpan.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>                
                </div>

                <!-- Dokumentasi -->
                <div class="mb-10">
                    <div class="flex items-end justify-between mb-4">
                        <div>
                        <label class="text-md font-semibold text-[#1e293b]">Progress Pekerjaan</label>
                        <p class="text-xs text-slate-500 mb-2">Progress pekerjaan hari ini</p>

                        </div>
                        <label for="upload-foto-input" class="inline-flex items-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-[#1e293b] text-xs md:text-sm font-bold rounded-lg cursor-pointer transition-colors">
                            <input type="file" id="upload-foto-input" class="hidden" accept="image/*" multiple>
                            <i class="fas fa-cloud-upload-alt text-primary"></i> <span class="hidden sm:inline">Pilih Foto</span><span class="sm:hidden">Foto</span>
                        </label>
                    </div>

                    <div class="w-full bg-[#f8f9fa] border border-[#e2e8f0] rounded-xl p-4 min-h-[160px] flex flex-col justify-center">
                        <div id="foto-preview-container" class="flex flex-col gap-3 empty:hidden w-full">
                        </div>
                        
                        <div id="foto-empty-state" class="py-6 flex flex-col items-center justify-center text-center">
                            <i class="fas fa-images text-4xl text-slate-300 mb-3"></i>
                            <p class="text-sm font-semibold text-slate-500">Belum ada foto dokumentasi</p>
                            <p class="text-xs text-slate-400 mt-1">Maks. 5MB per file (Format: JPG, PNG)</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 mt-6">
                    <button type="button" class="order-2 sm:order-1 w-full sm:w-auto px-8 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-bold rounded-xl transition-all" data-hs-overlay="#modal-tambah-realisasi">
                        Batal
                    </button>
                    <button type="button" id="btn-save-realisasi" class="order-1 sm:order-2 w-full sm:w-auto px-8 py-2.5 bg-[#22c55e] hover:bg-green-600 text-white text-sm font-bold rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-green-100 transition-all">
                        Simpan Progres
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
