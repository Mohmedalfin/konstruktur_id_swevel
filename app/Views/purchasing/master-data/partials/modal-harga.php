<!-- Tambah Harga Modal -->
<div id="modalTambahHarga" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 relative flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex justify-between items-center p-5 border-b border-gray-200">
            <h3 class="text-[17px] font-bold text-[#1e293b]">Tambah Harga Supplier</h3>
            <button type="button" class="text-gray-400 hover:text-gray-700 transition-colors size-8 flex justify-center items-center rounded-lg hover:bg-gray-100" onclick="closeTambahModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 overflow-y-auto">
            <form id="formTambahHarga">
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Supplier</label>
                        <select name="supplier_id" id="tambah_supplier_id" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border">
                            <option value="" disabled selected>Pilih supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>"><?= esc($supplier['nama_supplier']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Material</label>
                        <select name="material_id" id="tambah_material_id" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border" onchange="autoFillMaterialDetails(this, 'tambah')">
                            <option value="" disabled selected>Pilih material</option>
                            <?php foreach ($materials as $material): ?>
                                <option value="<?= $material['id'] ?>"><?= esc($material['nama_material']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Spesifikasi</label>
                            <input type="text" id="tambah_spesifikasi" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] bg-gray-50 border" placeholder="Otomatis terisi" readonly>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Satuan</label>
                            <input type="text" id="tambah_satuan" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] bg-gray-50 border" placeholder="Otomatis terisi" readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Harga Satuan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3">
                                <span class="text-gray-500 text-[13px] font-bold">Rp</span>
                            </div>
                            <input type="text" name="harga" id="tambah_harga" class="py-2 px-3 ps-9 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border" placeholder="65.000">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-end items-center gap-x-2 p-5 border-t border-gray-200">
            <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-[13px] font-bold rounded-lg bg-[#ef4444] text-white hover:bg-red-600 transition-colors" onclick="closeTambahModal()">
                Batal
            </button>
            <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-[13px] font-bold rounded-lg bg-[#2563eb] text-white hover:bg-blue-700 transition-colors" onclick="submitTambahHarga()">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>

<!-- Edit Harga Modal -->
<div id="modalEditHarga" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 relative flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex justify-between items-center p-5 border-b border-gray-200">
            <h3 class="text-[17px] font-bold text-[#1e293b]">Edit Harga Supplier</h3>
            <button type="button" class="text-gray-400 hover:text-gray-700 transition-colors size-8 flex justify-center items-center rounded-lg hover:bg-gray-100" onclick="closeEditModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 overflow-y-auto">
            <form id="formEditHarga">
                <input type="hidden" name="id" id="edit_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Supplier</label>
                        <select name="supplier_id" id="edit_supplier_id" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border">
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>"><?= esc($supplier['nama_supplier']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Material</label>
                        <select name="material_id" id="edit_material_id" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border" onchange="autoFillMaterialDetails(this, 'edit')">
                            <?php foreach ($materials as $material): ?>
                                <option value="<?= $material['id'] ?>"><?= esc($material['nama_material']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Spesifikasi</label>
                            <input type="text" id="edit_spesifikasi" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] bg-gray-50 border" readonly>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Satuan</label>
                            <input type="text" id="edit_satuan" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] bg-gray-50 border" readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Harga Satuan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3">
                                <span class="text-gray-500 text-[13px] font-bold">Rp</span>
                            </div>
                            <input type="text" name="harga" id="edit_harga" class="py-2 px-3 ps-9 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-end items-center gap-x-2 p-5 border-t border-gray-200">
            <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-[13px] font-bold rounded-lg bg-[#ef4444] text-white hover:bg-red-600 transition-colors" onclick="closeEditModal()">
                Batal
            </button>
            <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-[13px] font-bold rounded-lg bg-[#2563eb] text-white hover:bg-blue-700 transition-colors" onclick="submitEditHarga()">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>
