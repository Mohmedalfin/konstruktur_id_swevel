<!-- Tambah Material Modal -->
<div id="modalTambahMaterial" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 relative flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="bg-[#0f172a] px-5 py-4 flex items-center justify-between border-b border-slate-800 rounded-t-xl">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded border border-blue-500/30 flex items-center justify-center bg-transparent text-blue-500">
                    <i class="fas fa-cube text-base"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-base">Tambah Material</h2>
                </div>
            </div>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" onclick="closeTambahModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto bg-slate-50/30">
            <form id="formTambahMaterial">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Material</label>
                        <input type="text" name="nama_material" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="Masukkan nama material">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori</label>
                            <select name="kategori" id="tambah_kategori" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm">
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="Struktur">Struktur</option>
                                <option value="Finishing">Finishing</option>
                                <option value="Atap">Atap</option>
                                <option value="MEP">MEP</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Satuan</label>
                            <input type="text" name="satuan" id="tambah_satuan" list="datalist-satuan" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="Ketik atau pilih satuan">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Spesifikasi</label>
                        <input type="text" name="spesifikasi" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="Contoh: SNI, Standard, 3 inch...">
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-white rounded-b-xl">
            <button type="button" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors focus:outline-none" onclick="closeTambahModal()">
                Batal
            </button>
            <button type="button" class="px-5 py-2.5 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors focus:outline-none shadow-sm inline-flex items-center gap-2" onclick="submitTambahMaterial()">
                <i class="fas fa-save"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>
</div>

<!-- Edit Material Modal -->
<div id="modalEditMaterial" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 relative flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="bg-[#0f172a] px-5 py-4 flex items-center justify-between border-b border-slate-800 rounded-t-xl">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded border border-blue-500/30 flex items-center justify-center bg-transparent text-blue-500">
                    <i class="fas fa-edit text-base"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-base">Edit Material</h2>
                </div>
            </div>
            <button type="button" class="w-8 h-8 flex items-center justify-center rounded bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto bg-slate-50/30">
            <form id="formEditMaterial">
                <input type="hidden" name="id" id="edit_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Material</label>
                        <input type="text" name="nama_material" id="edit_nama_material" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori</label>
                            <select name="kategori" id="edit_kategori" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm">
                                <option value="Struktur">Struktur</option>
                                <option value="Finishing">Finishing</option>
                                <option value="Atap">Atap</option>
                                <option value="MEP">MEP</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Satuan</label>
                            <input type="text" name="satuan" id="edit_satuan" list="datalist-satuan" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm" placeholder="Ketik atau pilih satuan">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Spesifikasi</label>
                        <input type="text" name="spesifikasi" id="edit_spesifikasi" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors shadow-sm">
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-white rounded-b-xl">
            <button type="button" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors focus:outline-none" onclick="closeEditModal()">
                Batal
            </button>
            <button type="button" class="px-5 py-2.5 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors focus:outline-none shadow-sm inline-flex items-center gap-2" onclick="submitEditMaterial()">
                <i class="fas fa-save"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>
</div>

<datalist id="datalist-satuan">
    <!-- Volume / Dimensi -->
    <option value="m">Meter (m)</option>
    <option value="m2">Meter Persegi (m²)</option>
    <option value="m3">Meter Kubik (m³)</option>
    <option value="cm">Sentimeter (cm)</option>
    <option value="mm">Milimeter (mm)</option>
    <option value="km">Kilometer (km)</option>
    <!-- Berat -->
    <option value="kg">Kilogram (kg)</option>
    <option value="ton">Ton</option>
    <option value="gr">Gram (gr)</option>
    <!-- Satuan Umum / Satuan Kerja -->
    <option value="bh">Buah (bh)</option>
    <option value="unit">Unit</option>
    <option value="set">Set</option>
    <option value="ls">Lump Sum (ls)</option>
    <option value="ttk">Titik (ttk)</option>
    <option value="btg">Batang (btg)</option>
    <option value="lbr">Lembar (lbr)</option>
    <option value="mtr">Meter Lari (m')</option>
    <!-- Waktu & Tenaga -->
    <option value="org/hr">Orang/Hari (OH)</option>
    <option value="jam">Jam</option>
    <option value="hari">Hari</option>
    <option value="bln">Bulan</option>
    <option value="mgg">Minggu</option>
    <!-- Kemasan -->
    <option value="zak">Zak</option>
    <option value="gln">Galon (gln)</option>
    <option value="klg">Kaleng (klg)</option>
    <option value="btl">Botol (btl)</option>
    <option value="ktk">Kotak (ktk)</option>
    <option value="rol">Rol</option>
    <option value="dus">Dus</option>
    <!-- Lainnya -->
    <option value="rit">Ritase (rit)</option>
    <option value="pax">Pax</option>
    <option value="liter">Liter (L)</option>
    <option value="sak">Sak</option>
</datalist>
