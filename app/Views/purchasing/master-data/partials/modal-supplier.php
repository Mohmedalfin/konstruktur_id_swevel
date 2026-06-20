<!-- Modal Supplier -->
<div id="modal-supplier" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-7 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center justify-center">
        <div class="flex flex-col bg-white border shadow-xl rounded-xl pointer-events-auto w-full overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-[#0f172a] px-5 py-4 flex items-center justify-between border-b border-slate-800 rounded-t-xl">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded border border-blue-500/30 flex items-center justify-center bg-transparent text-blue-500">
                        <i class="fas fa-store text-base"></i>
                    </div>
                    <div>
                        <h2 id="modal-supplier-title" class="text-white font-bold text-base">Tambah Supplier</h2>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" data-hs-overlay="#modal-supplier">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="form-supplier" onsubmit="saveSupplier(event)">
                <div class="p-6 overflow-y-auto bg-slate-50/30">
                    <input type="hidden" id="supplier_id" name="id">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="nama_supplier" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Supplier</label>
                            <input type="text" id="nama_supplier" name="nama_supplier" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="Masukkan nama supplier" required>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="telepon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">No Telepon</label>
                                <input type="text" id="telepon" name="telepon" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="08xx-xxxx-xxxx">
                            </div>
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email</label>
                                <input type="email" id="email" name="email" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="supplier@email.com">
                            </div>
                        </div>

                        <div>
                            <label for="alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat</label>
                            <input type="text" id="alamat" name="alamat" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="Alamat lengkap supplier">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="npwp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NPWP</label>
                                <input type="text" id="npwp" name="npwp" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="xx.xxx.xxx.x-xxx">
                            </div>
                            <div>
                                <label for="rekening_bank" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rekening Bank</label>
                                <input type="text" id="rekening_bank" name="rekening_bank" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="Nama Bank - No. Rekening">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-white rounded-b-xl">
                    <button type="button" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors focus:outline-none" data-hs-overlay="#modal-supplier">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors focus:outline-none shadow-sm inline-flex items-center gap-2" id="btn-save-supplier">
                        <i class="fas fa-save"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
