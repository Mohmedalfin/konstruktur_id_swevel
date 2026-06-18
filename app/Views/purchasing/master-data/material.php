<?= $this->extend('purchasing/layouts/main') ?>

<?= $this->section('styles') ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .tab-active {
            background-color: white;
            color: #0f172a;
            border-top: 4px solid #f59e0b; /* yellow */
            font-weight: bold;
        }
        .tab-inactive {
            background-color: #d1d5db; /* gray-300 */
            color: #1e293b;
            border-top: 4px solid transparent;
            font-weight: 600;
        }
        .table-row-odd { background-color: #ffffff; }
        .table-row-even { background-color: #cbd5e1; /* slate-300/400 */ }
        .btn-action-edit {
            color: #334155;
            background-color: white;
            border: 2px solid #64748b;
            border-radius: 4px;
        }
        .btn-action-delete {
            color: white;
            background-color: #ef4444; /* red-500 */
            border: 2px solid #ef4444;
            border-radius: 4px;
        }
        /* Select2 Tailwind Premium Overrides */
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            height: 38px;
            display: flex;
            align-items: center;
            font-size: 13px;
            padding-left: 0.5rem;
            outline: none;
            transition: all 0.2s ease-in-out;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b;
            line-height: 38px;
            padding-left: 0;
            padding-right: 20px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 8px;
        }
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }
        
        /* Dropdown */
        .select2-dropdown {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 1060;
            font-size: 13px;
            overflow: hidden;
            margin-top: 4px;
        }
        
        /* Search Box */
        .select2-container--default .select2-search--dropdown {
            padding: 8px;
            background-color: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #cbd5e1;
            border-radius: 0.375rem;
            padding: 6px 12px;
            outline: none;
            transition: all 0.2s;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }
        
        /* Options */
        .select2-container--default .select2-results__option {
            padding: 8px 12px;
            color: #334155;
            transition: background-color 0.15s ease;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
        }
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .select2-results__option {
            border-bottom: 1px solid #f8fafc;
        }
        .select2-results__option:last-child {
            border-bottom: none;
        }
        
        /* Scrollbar */
        .select2-results__options::-webkit-scrollbar {
            width: 6px;
        }
        .select2-results__options::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .select2-results__options::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .select2-results__options::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
        
        <!-- Tabs -->
        <div class="flex">
            <a href="<?= base_url('purchasing/master-data') ?>" class="tab-inactive px-8 py-3 rounded-tl-xl rounded-tr-xl text-[15px] flex items-center gap-2 z-0 relative">
                <i class="fa-solid fa-store"></i> Supplier
            </a>
            <a href="<?= base_url('purchasing/master-data/material') ?>" class="tab-active px-8 py-3 rounded-tl-xl rounded-tr-xl text-[15px] flex items-center gap-2 -ml-3 z-10 relative shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
                <i class="fa-solid fa-cube"></i> Material
            </a>
            <a href="<?= base_url('purchasing/master-data/harga') ?>" class="tab-inactive px-8 py-3 rounded-tl-xl rounded-tr-xl text-[15px] flex items-center gap-2 -ml-3 z-0 relative">
                <i class="fa-solid fa-tags"></i> Harga
            </a>
        </div>

        <!-- Card Body -->
        <div class="bg-white rounded-b-xl rounded-tl-xl rounded-tr-xl shadow-md p-6 border border-gray-200">
            
            <!-- Toolbar -->
            <div class="flex justify-between items-center mb-5">
                <div class="relative w-80">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                        <i class="fa-solid fa-search text-gray-500"></i>
                    </div>
                    <input type="text" id="searchMaterial" class="py-2.5 px-4 ps-10 block w-full border-gray-400 rounded-lg text-[13px] font-medium focus:border-blue-500 focus:ring-blue-500 border placeholder-gray-500" placeholder="Cari nama material...">
                </div>
            </div>

            <!-- Table -->
            <div class="border border-gray-300 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-[#111827] text-white border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Nama Material</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Satuan</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Spesifikasi</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Kategori</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Jumlah Supplier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="materialTableBody">
                        <?php if (empty($materials)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center bg-white">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-cube text-2xl text-slate-300"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada data material</p>
                                    <p class="text-xs mt-1 text-slate-400">Data material diambil dari RAP dan Gudang.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($materials as $material): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-500"><?= $no++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= esc($material['nama_barang']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800"><?= esc($material['satuan']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800"><?= esc($material['spesifikasi']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800"><?= esc($material['jenis_item']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800">0</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    <!-- Modals -->
    <!-- Modal Material Dihapus (Hanya Read-Only) -->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?= base_url('assets/js/purchasing/master-data-material.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
