<!-- Modal Supplier -->
<div id="modal-supplier" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-7 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center justify-center">
        <div class="flex flex-col bg-white border shadow-xl rounded-xl pointer-events-auto w-full">
            <div class="flex justify-between items-center py-3 px-4 border-b">
                <h3 id="modal-supplier-title" class="font-bold text-gray-800 text-[#1e3a8a]">
                    Tambah Supplier
                </h3>
                <button type="button" class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none" data-hs-overlay="#modal-supplier">
                    <span class="sr-only">Close</span>
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            
            <form id="form-supplier" onsubmit="saveSupplier(event)">
                <div class="p-4 overflow-y-auto">
                    <input type="hidden" id="supplier_id" name="id">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="nama_supplier" class="block text-sm font-bold mb-2 text-[#1e3a8a]">Nama Supplier</label>
                            <input type="text" id="nama_supplier" name="nama_supplier" class="py-2.5 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 border" placeholder="Masukkan nama supplier" required>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="telepon" class="block text-sm font-bold mb-2 text-[#1e3a8a]">No Telepon</label>
                                <input type="text" id="telepon" name="telepon" class="py-2.5 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 border" placeholder="08xx-xxxx-xxxx">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold mb-2 text-[#1e3a8a]">Email</label>
                                <input type="email" id="email" name="email" class="py-2.5 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 border" placeholder="supplier@email.com">
                            </div>
                        </div>

                        <div>
                            <label for="alamat" class="block text-sm font-bold mb-2 text-[#1e3a8a]">Alamat</label>
                            <input type="text" id="alamat" name="alamat" class="py-2.5 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 border" placeholder="Alamat lengkap supplier">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="npwp" class="block text-sm font-bold mb-2 text-[#1e3a8a]">NPWP</label>
                                <input type="text" id="npwp" name="npwp" class="py-2.5 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 border" placeholder="xx.xxx.xxx.x-xxx">
                            </div>
                            <div>
                                <label for="rekening_bank" class="block text-sm font-bold mb-2 text-[#1e3a8a]">Rekening Bank</label>
                                <input type="text" id="rekening_bank" name="rekening_bank" class="py-2.5 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 border" placeholder="Nama Bank - No. Rekening">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t">
                    <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-500 text-white hover:bg-red-600 focus:outline-none focus:bg-red-600 transition-colors" data-hs-overlay="#modal-supplier">
                        Batal
                    </button>
                    <button type="submit" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-[#1e3a8a] text-white hover:bg-blue-800 focus:outline-none focus:bg-blue-800 transition-colors" id="btn-save-supplier">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
