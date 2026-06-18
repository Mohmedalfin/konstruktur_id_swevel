
<div id="modal-buat-permintaan" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="mt-0 ease-out transition-all sm:max-w-6xl sm:w-full sm:mx-auto min-h-full flex items-center justify-center p-4">
        <div class="flex flex-col bg-white border border-slate-200 shadow-2xl rounded-2xl pointer-events-auto w-full overflow-hidden max-h-[calc(100vh-1.5rem)] sm:max-h-[calc(100vh-2.5rem)]">
            
            <!-- Modal Header -->
            <div class="bg-[#1e293b] text-white px-6 py-4 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-600/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-base sm:text-lg">Buat Permintaan Baru</h2>
                        <p class="text-slate-400 text-xs mt-0.5">Isi data di bawah ini untuk membuat permintaan baru ke gudang</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none" data-hs-overlay="#modal-buat-permintaan">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body (Form) -->
            <div class="p-6 overflow-y-auto flex-1">
                <form id="permintaan-form" class="space-y-6">
                    
                    <!-- Project Blocks Container -->
                    <div id="project-blocks-container" class="space-y-6">
                    </div>

                    <!-- Add Project Button -->
                    <button type="button" id="btn-add-project" class="w-full py-3 border-2 border-dashed border-slate-300 hover:border-slate-400 text-slate-600 hover:text-slate-800 rounded-xl flex items-center justify-center gap-2 font-bold text-xs sm:text-sm transition-all focus:outline-none bg-slate-50 hover:bg-slate-100 shadow-inner">
                        <i class="fas fa-building text-xs"></i>
                        <span>Tambah Proyek Lain</span>
                    </button>

                    <!-- Over-Limit Justification Block (Hidden by default) -->
                    <div id="over-limit-container" class="hidden bg-red-50 border border-red-200 rounded-xl p-4 space-y-2">
                        <label for="justifikasi_over_limit" class="block text-xs font-bold text-red-700 uppercase tracking-wider">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Justifikasi Over-Limit RAP <span class="text-red-500">*</span>
                        </label>
                        <textarea id="justifikasi_over_limit" rows="3" placeholder="Wajib diisi: Jelaskan alasan mengapa permintaan melebihi kuota RAP..." class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-red-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder-red-300 transition-colors"></textarea>
                        <p class="text-[10px] sm:text-xs text-red-600 mt-1"><i class="fas fa-info-circle mr-1"></i>Terdapat item yang melebihi sisa volume RAP. Anda wajib menyertakan alasan justifikasi.</p>
                    </div>

                    <!-- Global Notes Block -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2">
                        <label for="catatan_umum" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Catatan untuk gudang (opsional)
                        </label>
                        <textarea id="catatan_umum" rows="3" placeholder="Contoh : Mohon dikirim sebelum tanggal 20 Juni 2026..." class="w-full px-3 py-2 text-sm text-slate-700 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-slate-400"></textarea>
                    </div>

                    <!-- Footer Save Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" data-hs-overlay="#modal-buat-permintaan" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors focus:outline-none">
                            Batal
                        </button>
                        <button type="submit" id="btn-submit-form" class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors focus:outline-none shadow-md inline-flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            <span>Simpan Permintaan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
