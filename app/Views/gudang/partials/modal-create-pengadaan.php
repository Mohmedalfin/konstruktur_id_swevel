<!-- Modal Form Create Pengadaan -->
<div id="modal-create-pengadaan" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" id="modal-create-overlay"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle w-full max-w-4xl border border-slate-100 opacity-0 scale-95" id="modal-create-panel">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-6 flex items-center justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-indigo-500/10 blur-2xl"></div>
                <div class="absolute bottom-0 left-1/4 w-24 h-24 rounded-full bg-blue-500/10 blur-xl"></div>
                
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-xl border border-indigo-500/30 text-indigo-400 flex items-center justify-center shrink-0 bg-gradient-to-br from-indigo-500/10 to-indigo-600/5 shadow-[0_0_15px_rgba(99,102,241,0.15)]">
                        <i class="fas fa-plus-circle text-2xl drop-shadow-md"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg sm:text-xl tracking-wide drop-shadow-sm" id="modal-create-title">
                            Buat Pengajuan Pengadaan
                        </h3>
                        <p class="text-xs text-slate-400 mt-1" id="modal-create-subtitle">Masukkan item yang ingin diajukan ke Purchasing</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-slate-300 hover:bg-white/20 hover:text-white transition-all duration-200 focus:outline-none shrink-0 relative z-10 backdrop-blur-sm" id="btn-close-create-modal">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="px-6 py-6 max-h-[70vh] overflow-y-auto custom-scrollbar bg-white">
                <form id="form-create-pengadaan" onsubmit="event.preventDefault();">
                    
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Keterangan / Catatan Pengajuan</label>
                        <textarea id="create-keterangan" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-slate-400" placeholder="Contoh: Pengadaan rutin bulan Juni, harap segera diproses..."></textarea>
                    </div>

                    <!-- Mode Info Alert -->
                    <div id="smart-mode-alert" class="hidden mb-5 bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-3 items-start text-sm">
                        <i class="fas fa-magic text-amber-500 mt-0.5"></i>
                        <div>
                            <span class="font-bold text-amber-800">Smart Auto-Fill Aktif:</span>
                            <span class="text-amber-700">Daftar di bawah ini adalah barang yang stok aktualnya sudah mencapai batas minimum. Silakan sesuaikan volume yang ingin diajukan.</span>
                        </div>
                    </div>

                    <!-- Pencarian & Tambah Barang (Mode Manual) -->
                    <div id="manual-search-section" class="mb-5 bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Cari & Tambah Barang</label>
                        <div class="relative flex flex-col sm:flex-row gap-3">
                            <!-- Custom Searchable Dropdown -->
                            <div class="relative flex-1" id="custom-select-container">
                                <!-- Trigger Button -->
                                <button type="button" id="custom-select-trigger" class="w-full h-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-sm text-left focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 flex justify-between items-center shadow-sm transition-colors">
                                    <span id="custom-select-display" class="text-slate-400 truncate">Pilih barang untuk ditambahkan ke daftar...</span>
                                    <i class="fas fa-search text-slate-400 text-xs ml-2 transition-transform duration-200" id="custom-select-icon"></i>
                                </button>
                                
                                <!-- Dropdown Panel -->
                                <div id="custom-select-panel" class="hidden absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden flex flex-col">
                                    <!-- Search Input -->
                                    <div class="p-2 border-b border-slate-100 bg-slate-50">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-search text-slate-400 text-xs"></i>
                                            </div>
                                            <input type="text" id="input-global-search" class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-md text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="Ketik nama barang untuk mencari..." autocomplete="off">
                                        </div>
                                    </div>
                                    
                                    <!-- Results List -->
                                    <div id="global-search-dropdown" class="max-h-52 overflow-y-auto py-1">
                                        <div class="px-4 py-8 text-sm text-slate-400 text-center flex flex-col items-center">
                                            <i class="fas fa-search text-2xl mb-2 text-slate-200"></i>
                                            Ketik nama barang untuk memulai pencarian
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" id="btn-add-selected-item" class="px-5 py-2.5 bg-slate-200 text-slate-500 font-semibold text-sm rounded-lg transition-all cursor-not-allowed flex items-center justify-center gap-2 whitespace-nowrap" disabled>
                                <i class="fas fa-plus"></i> Tambah ke List
                            </button>
                        </div>
                    </div>

                    <div class="mb-3 flex justify-between items-end">
                        <h4 class="text-sm font-bold text-slate-800">Daftar Item Pengajuan</h4>
                        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md" id="item-count-badge">0 Item</span>
                    </div>

                    <!-- Items Container -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="overflow-x-auto min-h-[250px]">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 w-10 text-center">#</th>
                                        <th scope="col" class="px-4 py-3 min-w-[250px]">Nama Barang</th>
                                        <th scope="col" class="px-4 py-3 w-32 text-center">Stok Gudang</th>
                                        <th scope="col" class="px-4 py-3 w-32">Volume Ajuan</th>
                                        <th scope="col" class="px-4 py-3 w-20 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="pengadaan-items-tbody" class="divide-y divide-slate-100 bg-white">
                                    <!-- Dynamic rows will be added here via JS -->
                                    <tr id="empty-state-row">
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                            <i class="fas fa-box-open text-3xl mb-3 text-slate-300"></i>
                                            <p class="text-sm font-semibold text-slate-500">Belum ada item.</p>
                                            <p class="text-xs mt-1">Cari dan tambah barang menggunakan form pencarian di atas.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3 rounded-b-2xl">
                <button type="button" id="btn-cancel-create" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-200 bg-slate-100 rounded-xl transition-all focus:outline-none">
                    Batal
                </button>
                <button type="button" id="btn-submit-create" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-600/30 rounded-xl transition-all focus:outline-none flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Kirim Pengajuan
                </button>
            </div>
            
        </div>
    </div>
</div>

<!-- Template for new row -->
<template id="row-item-template">
    <tr class="item-row transition-colors hover:bg-slate-50 group">
        <td class="px-4 py-3 text-center text-slate-500 font-medium row-number">1</td>
        <td class="px-4 py-3">
            <input type="hidden" class="input-id-barang" name="id_barang[]" required>
            <div class="flex items-center gap-2">
                <span class="nama-text font-semibold text-slate-700 whitespace-normal"></span>
                <span class="satuan-badge text-[10px] font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full shrink-0"></span>
            </div>
        </td>
        <td class="px-4 py-3 text-center">
            <div class="flex flex-col">
                <span class="text-xs font-bold text-slate-700 display-stok-aktual">-</span>
                <span class="text-[10px] font-semibold text-slate-400 display-stok-min">Min: -</span>
            </div>
        </td>
        <td class="px-4 py-3">
            <div class="flex items-center gap-2">
                <input type="number" name="volume[]" class="input-volume w-24 px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-sm text-center font-bold text-indigo-700 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all" min="0.01" step="0.01" placeholder="0" required>
            </div>
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" class="btn-remove-row w-8 h-8 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition-all focus:outline-none" title="Hapus baris">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
</template>
