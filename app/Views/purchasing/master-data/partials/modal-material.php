<!-- Tambah Material Modal -->
<div id="modalTambahMaterial" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 relative flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex justify-between items-center p-5 border-b border-gray-200">
            <h3 class="text-[17px] font-bold text-[#1e293b]">Tambah Material</h3>
            <button type="button" class="text-gray-400 hover:text-gray-700 transition-colors size-8 flex justify-center items-center rounded-lg hover:bg-gray-100" onclick="closeTambahModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 overflow-y-auto">
            <form id="formTambahMaterial">
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Nama Material</label>
                        <input type="text" name="nama_material" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border" placeholder="Masukkan nama material">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Kategori</label>
                            <select name="kategori" id="tambah_kategori" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border">
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="Struktur">Struktur</option>
                                <option value="Finishing">Finishing</option>
                                <option value="Atap">Atap</option>
                                <option value="MEP">MEP</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Satuan</label>
                            <input type="text" name="satuan" id="tambah_satuan" list="datalist-satuan" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border" placeholder="Ketik atau pilih satuan">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Spesifikasi</label>
                        <input type="text" name="spesifikasi" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border" placeholder="Contoh: SNI, Standard, 3 inch...">
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-end items-center gap-x-2 p-5 border-t border-gray-200">
            <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-[13px] font-bold rounded-lg bg-[#ef4444] text-white hover:bg-red-600 transition-colors" onclick="closeTambahModal()">
                Batal
            </button>
            <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-[13px] font-bold rounded-lg bg-[#2563eb] text-white hover:bg-blue-700 transition-colors" onclick="submitTambahMaterial()">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>

<!-- Edit Material Modal -->
<div id="modalEditMaterial" class="fixed inset-0 z-50 hidden bg-black/50 items-center justify-center font-sans">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 relative flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex justify-between items-center p-5 border-b border-gray-200">
            <h3 class="text-[17px] font-bold text-[#1e293b]">Edit Material</h3>
            <button type="button" class="text-gray-400 hover:text-gray-700 transition-colors size-8 flex justify-center items-center rounded-lg hover:bg-gray-100" onclick="closeEditModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 overflow-y-auto">
            <form id="formEditMaterial">
                <input type="hidden" name="id" id="edit_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Nama Material</label>
                        <input type="text" name="nama_material" id="edit_nama_material" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Kategori</label>
                            <select name="kategori" id="edit_kategori" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border">
                                <option value="Struktur">Struktur</option>
                                <option value="Finishing">Finishing</option>
                                <option value="Atap">Atap</option>
                                <option value="MEP">MEP</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Satuan</label>
                            <input type="text" name="satuan" id="edit_satuan" list="datalist-satuan" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border" placeholder="Ketik atau pilih satuan">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-bold text-[#1e293b] mb-1.5">Spesifikasi</label>
                        <input type="text" name="spesifikasi" id="edit_spesifikasi" class="py-2 px-3 block w-full border-gray-300 rounded-md text-[13px] focus:border-blue-500 focus:ring-blue-500 border">
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-end items-center gap-x-2 p-5 border-t border-gray-200">
            <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-[13px] font-bold rounded-lg bg-[#ef4444] text-white hover:bg-red-600 transition-colors" onclick="closeEditModal()">
                Batal
            </button>
            <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-[13px] font-bold rounded-lg bg-[#2563eb] text-white hover:bg-blue-700 transition-colors" onclick="submitEditMaterial()">
                Simpan Perubahan
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
