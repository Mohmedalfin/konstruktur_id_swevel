<style>
    #modal-real-sdm .rounded-table-container table tr:last-child td:first-child { border-bottom-left-radius: 0.75rem; }
    #modal-real-sdm .rounded-table-container table tr:last-child td:last-child { border-bottom-right-radius: 0.75rem; }

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
    
    #modal-real-sdm [role="tablist"] button.active {
        border-bottom-color: #16a34a !important; /* green-600 */
        color: #16a34a !important; /* green-600 */
        background-color: transparent !important;
    }
    #modal-real-sdm [role="tablist"] button.active i {
        color: #16a34a !important; /* green-600 */
    }
</style>

<div id="modal-real-sdm" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-0 ease-out transition-all sm:max-w-6xl sm:w-full sm:mx-auto min-h-full flex items-center justify-center p-4">
        <div class="flex flex-col bg-[#f8f9fa] border shadow-xl rounded-xl pointer-events-auto w-full overflow-hidden">
            
            <div class="bg-[#0f172a] px-4 py-3 md:px-6 md:py-4 flex items-center justify-between rounded-t-xl border-b border-slate-800">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 md:w-10 md:h-10 rounded border border-orange-500/30 flex items-center justify-center bg-transparent text-orange-500">
                        <i class="fas fa-box text-base md:text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-base md:text-lg">Tambah Realisasi SDM</h2>
                        <p class="text-slate-400 text-[10px] md:text-xs mt-0.5">Catat pemakaian Alat, Bahan, dan Tenaga Kerja</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" data-hs-overlay="#modal-real-sdm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5 md:p-10 overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-h-[85vh]">
                <div class="mb-4">
                    <div class="flex flex-col mb-4 mt-2">
                        <label class="text-md font-semibold text-[#1e293b]">Tanggal</label>
                        <p class="text-xs text-slate-500 mb-2">Tanggal pelaksanaan pemakaian hari ini</p>

                        <div class="flex-1">
                            <div class="relative">
                                <input type="date" id="real-sdm-tanggal" value="<?= date('Y-m-d') ?>" class="py-2.5 px-4 block w-full md:w-[270px] border border-gray-300 bg-[#f8f9fa] text-sm focus:border-primary focus:ring-1 focus:ring-primary font-medium text-gray-700 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="text-md font-semibold text-[#1e293b]">Input Penggunaan</label>
                        <p class="text-xs text-slate-500 mb-4">Pilih tab kategori untuk menambahkan bahan, alat, atau tenaga kerja</p>

                        <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
                            <nav class="flex w-full border-b border-slate-200 bg-slate-50 rounded-t-xl" aria-label="Tabs" role="tablist">
                                <button type="button" class="flex-1 py-3 px-2 sm:px-4 inline-flex justify-center items-center gap-2 border-b-2 border-transparent text-xs sm:text-sm font-bold text-slate-500 hover:text-slate-700 active" id="tab-sdm-bahan" data-hs-tab="#content-sdm-bahan" aria-controls="content-sdm-bahan" role="tab">
                                    <i class="fas fa-box"></i> Bahan
                                </button>
                                <button type="button" class="flex-1 py-3 px-2 sm:px-4 inline-flex justify-center items-center gap-2 border-b-2 border-transparent text-xs sm:text-sm font-bold text-slate-500 hover:text-slate-700" id="tab-sdm-alat" data-hs-tab="#content-sdm-alat" aria-controls="content-sdm-alat" role="tab">
                                    <i class="fas fa-tools"></i> Alat
                                </button>
                                <button type="button" class="flex-1 py-3 px-2 sm:px-4 inline-flex justify-center items-center gap-2 border-b-2 border-transparent text-xs sm:text-sm font-bold text-slate-500 hover:text-slate-700" id="tab-sdm-upah" data-hs-tab="#content-sdm-upah" aria-controls="content-sdm-upah" role="tab">
                                    <i class="fas fa-hard-hat"></i> Tenaga
                                </button>
                            </nav>

                            <div class="p-5 lg:p-6">
                                <?php
                                $categories = [
                                    ['id' => 'bahan', 'color' => 'orange', 'label' => 'Bahan'],
                                    ['id' => 'alat', 'color' => 'blue', 'label' => 'Alat'],
                                    ['id' => 'upah', 'color' => 'red', 'label' => 'Tenaga']
                                ];
                                foreach ($categories as $idx => $cat): 
                                    $active = $idx === 0 ? 'active' : 'hidden';
                                ?>
                                <div id="content-sdm-<?= $cat['id'] ?>" class="<?= $active ?>" role="tabpanel" aria-labelledby="tab-sdm-<?= $cat['id'] ?>">
                                    <div class="flex flex-col md:flex-row gap-6">
                                        <div class="flex-1 flex flex-col gap-5">
                                            <div class="relative w-full">
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Pilih <?= $cat['label'] ?></label>
                                                <div id="container-select-<?= $cat['id'] ?>" class="relative z-[60]">
                                                    <!-- Dropdown JS will render here -->
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-start">
                                                <div class="space-y-1.5 flex justify-between sm:block border-b border-slate-50 sm:border-none pb-2 sm:pb-0">
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Satuan</label>
                                                    <div class="sm:py-2.5">
                                                        <div id="real-<?= $cat['id'] ?>-satuan-display" class="text-sm sm:text-[15px] font-semibold text-slate-700 leading-none">-</div>
                                                    </div>
                                                    <input type="hidden" id="real-<?= $cat['id'] ?>-satuan">
                                                </div>
                                                <div class="space-y-1.5 flex justify-between sm:block border-b border-slate-50 sm:border-none pb-2 sm:pb-0">
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sisa Stok</label>
                                                    <div class="sm:py-2.5">
                                                        <div id="real-<?= $cat['id'] ?>-sisa-display" class="text-sm sm:text-[15px] font-bold text-<?= $cat['color'] ?>-500 leading-none">0</div>
                                                    </div>
                                                    <input type="hidden" id="real-<?= $cat['id'] ?>-sisa">
                                                </div>
                                                <div class="space-y-1.5">
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Qty (Input)</label>
                                                    <input type="number" id="real-<?= $cat['id'] ?>-qty-actual" step="0.01" min="0" placeholder="0.00" class="py-2 px-3 sm:py-2.5 sm:px-3 block w-full border border-gray-300 bg-white text-sm text-center font-bold text-slate-800 focus:border-<?= $cat['color'] ?>-500 focus:ring-1 focus:ring-<?= $cat['color'] ?>-500 rounded-lg shadow-sm">
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-2">
                                                <div class="space-y-1 flex justify-between sm:block border-b border-slate-50 sm:border-none pb-2 sm:pb-0">
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Spesifikasi</label>
                                                    <div id="real-<?= $cat['id'] ?>-spek-mobile" class="sm:hidden text-[11px] text-slate-600 font-medium">-</div>
                                                    <input type="text" id="real-<?= $cat['id'] ?>-spek" readonly class="hidden sm:block py-2 px-3 w-full border-transparent bg-slate-50 text-xs text-slate-500 rounded-lg cursor-default">
                                                </div>
                                                <div class="space-y-1 flex justify-between sm:block">
                                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Merk</label>
                                                    <div id="real-<?= $cat['id'] ?>-merk-mobile" class="sm:hidden text-[11px] text-slate-600 font-medium">-</div>
                                                    <input type="text" id="real-<?= $cat['id'] ?>-merk" readonly class="hidden sm:block py-2 px-3 w-full border-transparent bg-slate-50 text-xs text-slate-500 rounded-lg cursor-default">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex-[0.8] flex flex-col mt-4 md:mt-0">
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Keterangan</label>
                                            <textarea id="real-<?= $cat['id'] ?>-keterangan" placeholder="Keterangan..." class="flex-1 py-3 px-4 block w-full border border-gray-300 bg-white text-sm placeholder-gray-400 focus:border-<?= $cat['color'] ?>-500 focus:ring-1 focus:ring-<?= $cat['color'] ?>-500 rounded-lg resize-none min-h-[80px] md:min-h-[120px] mb-4"></textarea>
                                            
                                            <button type="button" id="btn-add-<?= $cat['id'] ?>" class="w-full py-3 px-4 inline-flex justify-center items-center gap-2 rounded-lg border border-transparent font-bold bg-green-600 text-white hover:bg-green-700 transition-all text-sm shadow-sm">
                                                <i class="fas fa-plus"></i> Tambahkan ke Daftar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="text-sm font-bold text-[#1e293b] flex items-center gap-2 mb-2">
                            <i class="fas fa-list-ul text-slate-500"></i> 
                            Daftar Penggunaan (Siap Disimpan)
                        </label>
                        <div class="w-full bg-white border border-slate-200 overflow-x-auto rounded-xl shadow-sm">
                            <div class="min-w-[850px] md:min-w-0">
                            <table class="w-full text-left border-collapse">
                                <thead class="rounded-t-xl overflow-hidden">
                                    <tr class="bg-[#0f172a] text-white border-b border-slate-800">
                                        <th class="px-4 py-3 text-center text-xs font-bold w-12 first:rounded-tl-xl">No</th>
                                        <th class="px-4 py-3 text-xs font-bold w-24">Kategori</th>
                                        <th class="px-4 py-3 text-xs font-bold">Nama Item</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold w-20">Satuan</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold w-28">Qty Pakai</th>
                                        <th class="px-4 py-3 text-xs font-bold min-w-[200px]">Keterangan</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold w-20 last:rounded-tr-xl">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="batch-sdm-progress-tbody" class="divide-y divide-slate-100">
                                    <tr id="batch-sdm-empty-row">
                                        <td colspan="7" class="px-4 py-10 text-center text-slate-400 italic text-sm">
                                            Belum ada item resource yang ditambahkan ke daftar simpan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dokumentasi -->
                <div class="mb-10 mt-8 border-t border-slate-200 pt-8">
                    <div class="flex items-end justify-between mb-4">
                        <div>
                            <h3 class="text-md font-semibold text-[#1e293b] mb-1">Dokumentasi (Opsional)</h3>
                            <p class="text-xs text-slate-500 font-medium">Unggah foto material/alat/pekerja saat digunakan</p>
                        </div>
                        <label for="upload-foto-sdm-input" class="inline-flex items-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-[#1e293b] text-xs md:text-sm font-bold rounded-lg cursor-pointer transition-colors">
                            <input type="file" id="upload-foto-sdm-input" class="hidden" accept="image/*" multiple>
                            <i class="fas fa-cloud-upload-alt text-slate-500"></i> <span class="hidden sm:inline">Pilih Foto</span><span class="sm:hidden">Foto</span>
                        </label>
                    </div>

                    <div class="w-full bg-[#f8f9fa] border border-[#e2e8f0] rounded-xl p-4 min-h-[160px] flex flex-col justify-center">
                        <div id="foto-preview-sdm-container" class="flex flex-col gap-3 empty:hidden w-full">
                        </div>
                        
                        <div id="foto-empty-state-sdm" class="py-6 flex flex-col items-center justify-center text-center">
                            <i class="fas fa-images text-4xl text-slate-300 mb-3"></i>
                            <p class="text-sm font-semibold text-slate-500">Belum ada foto dokumentasi</p>
                            <p class="text-xs text-slate-400 mt-1">Maks. 5MB per file (Format: JPG, PNG)</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4 mt-6">
                    <button type="button" class="order-2 sm:order-1 w-full sm:w-auto px-8 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-bold rounded-xl transition-all" data-hs-overlay="#modal-real-sdm">
                        Batal
                    </button>
                    <button type="button" id="btn-save-realisasi-sdm" class="order-1 sm:order-2 w-full sm:w-auto px-8 py-2.5 bg-[#0f172a] hover:bg-slate-800 text-white text-sm font-bold rounded-xl flex items-center justify-center gap-2 shadow-lg transition-all">
                        Simpan Realisasi SDM
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
