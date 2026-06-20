<!-- Tambah Harga Modal -->
<div id="modalTambahHarga" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 relative flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="bg-[#0f172a] px-5 py-4 flex items-center justify-between border-b border-slate-800 rounded-t-xl">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded border border-blue-500/30 flex items-center justify-center bg-transparent text-blue-500">
                    <i class="fas fa-tags text-base"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-base">Tambah Harga Supplier</h2>
                </div>
            </div>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" onclick="closeTambahModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto bg-slate-50/30">
            <form id="formTambahHarga">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Supplier</label>
                        <select name="supplier_id" id="tambah_supplier_id" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm">
                            <option value="" disabled selected>Pilih supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>"><?= esc($supplier['nama_supplier']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Material</label>
                        <select name="material_id" id="tambah_material_id" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" onchange="autoFillMaterialDetails(this, 'tambah')">
                            <option value="" disabled selected>Pilih material</option>
                            <?php foreach ($materials as $material): ?>
                                <option value="<?= $material['id'] ?>"><?= esc($material['nama_barang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Spesifikasi</label>
                            <input type="text" id="tambah_spesifikasi" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg shadow-sm" placeholder="Otomatis terisi" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Satuan</label>
                            <input type="text" id="tambah_satuan" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg shadow-sm" placeholder="Otomatis terisi" readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Harga Satuan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3">
                                <span class="text-gray-500 text-sm font-bold">Rp</span>
                            </div>
                            <input type="text" name="harga" id="tambah_harga" class="w-full px-3 py-2 ps-9 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="65.000">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-white rounded-b-xl">
            <button type="button" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors focus:outline-none" onclick="closeTambahModal()">
                Batal
            </button>
            <button type="button" class="px-5 py-2.5 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors focus:outline-none shadow-sm inline-flex items-center gap-2" onclick="submitTambahHarga()">
                <i class="fas fa-save"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>
</div>

<!-- Edit Harga Modal -->
<div id="modalEditHarga" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 relative flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="bg-[#0f172a] px-5 py-4 flex items-center justify-between border-b border-slate-800 rounded-t-xl">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded border border-blue-500/30 flex items-center justify-center bg-transparent text-blue-500">
                    <i class="fas fa-edit text-base"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-base">Edit Harga Supplier</h2>
                </div>
            </div>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto bg-slate-50/30">
            <form id="formEditHarga">
                <input type="hidden" name="id" id="edit_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Supplier</label>
                        <select name="supplier_id" id="edit_supplier_id" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm">
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>"><?= esc($supplier['nama_supplier']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Material</label>
                        <select name="material_id" id="edit_material_id" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" onchange="autoFillMaterialDetails(this, 'edit')">
                            <?php foreach ($materials as $material): ?>
                                <option value="<?= $material['id'] ?>"><?= esc($material['nama_barang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Spesifikasi</label>
                            <input type="text" id="edit_spesifikasi" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg shadow-sm" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Satuan</label>
                            <input type="text" id="edit_satuan" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-slate-100 border border-slate-300 rounded-lg shadow-sm" readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Harga Satuan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3">
                                <span class="text-gray-500 text-sm font-bold">Rp</span>
                            </div>
                            <input type="text" name="harga" id="edit_harga" class="w-full px-3 py-2 ps-9 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-white rounded-b-xl">
            <button type="button" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors focus:outline-none" onclick="closeEditModal()">
                Batal
            </button>
            <button type="button" class="px-5 py-2.5 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors focus:outline-none shadow-sm inline-flex items-center gap-2" onclick="submitEditHarga()">
                <i class="fas fa-save"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>
</div>
