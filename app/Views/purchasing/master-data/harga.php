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
        <!-- ACTIONS & FILTERS -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4 items-center">
                <div class="flex items-center gap-2 relative w-64 md:w-80">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                        <i class="fa-solid fa-search text-slate-400 text-xs"></i>
                    </div>
                    <input type="text" id="searchHarga" class="py-2 px-4 ps-8 block w-full border-slate-300 rounded-lg text-xs font-medium focus:border-blue-500 focus:ring-blue-500 border placeholder-slate-400 shadow-sm" placeholder="Cari material atau suppli...">
                </div>
                
                <div class="bg-gray-100/80 p-1 rounded-lg inline-flex items-center border border-gray-200/60 shadow-sm backdrop-blur-sm">
                    <a href="?group=none" class="px-4 py-1.5 rounded-md text-[13px] font-bold transition-all duration-200 flex items-center gap-1.5 <?= $group == 'none' ? 'bg-white text-[#111827] shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-200/50' ?>">
                        <i class="fa-solid fa-list text-[11px] <?= $group == 'none' ? 'text-blue-600' : '' ?>"></i> Semua
                    </a>
                    <a href="?group=supplier" class="px-4 py-1.5 rounded-md text-[13px] font-bold transition-all duration-200 flex items-center gap-1.5 <?= $group == 'supplier' ? 'bg-white text-[#111827] shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-200/50' ?>">
                        <i class="fa-solid fa-store text-[11px] <?= $group == 'supplier' ? 'text-amber-500' : '' ?>"></i> Supplier
                    </a>
                    <a href="?group=material" class="px-4 py-1.5 rounded-md text-[13px] font-bold transition-all duration-200 flex items-center gap-1.5 <?= $group == 'material' ? 'bg-white text-[#111827] shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-200/50' ?>">
                        <i class="fa-solid fa-cube text-[11px] <?= $group == 'material' ? 'text-teal-500' : '' ?>"></i> Material
                    </a>
                </div>
            </div>
            
            <button type="button" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-semibold text-sm py-2 px-5 rounded-lg transition-all shadow-sm focus:ring-2 focus:ring-primary/20" onclick="openTambahModal()">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Harga
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="<?= base_url('purchasing/master-data') ?>" class="px-6 py-2.5 bg-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50 border-b-2 border-transparent rounded-t-lg text-sm font-semibold flex items-center gap-2 transition-all">
                <i class="fa-solid fa-store"></i> Supplier
            </a>
            <a href="<?= base_url('purchasing/master-data/material') ?>" class="px-6 py-2.5 bg-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50 border-b-2 border-transparent rounded-t-lg text-sm font-semibold flex items-center gap-2 transition-all">
                <i class="fa-solid fa-cube"></i> Material
            </a>
            <a href="<?= base_url('purchasing/master-data/harga') ?>" class="px-6 py-2.5 bg-white text-[#111827] border-b-2 border-primary shadow-sm rounded-t-lg text-sm font-bold flex items-center gap-2 transition-all">
                <i class="fa-solid fa-tags text-primary"></i> Harga
            </a>
        </div>

        <!-- Card Body (Table Container) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-[#111827] text-white border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Material</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Supplier</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Spesifikasi</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Satuan</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Harga Satuan</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="hargaTableBody">
                        <?php if (empty($hargas)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center bg-white">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-tags text-2xl text-slate-300"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada data harga material</p>
                                    <p class="text-xs mt-1 text-slate-400">Silakan tambah data harga baru.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($hargasGrouped as $groupName => $items): ?>
                                <?php if ($group !== 'none'): ?>
                                    <tr class="bg-slate-50">
                                        <td colspan="7" class="px-6 py-3 text-left text-xs font-black text-slate-800 uppercase tracking-wider border-y border-gray-200">
                                            <?= $group == 'supplier' ? '<i class="fa-solid fa-store mr-2 text-slate-400"></i>' : '<i class="fa-solid fa-cube mr-2 text-slate-400"></i>' ?> <?= esc($groupName) ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php $no = 1; foreach ($items as $harga): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors group <?= $group !== 'none' ? 'searchable-row' : '' ?>">
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-500"><?= $no++ ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800 material-name"><?= esc($harga['nama_material']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-800 supplier-name"><?= esc($harga['nama_supplier']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800"><?= esc($harga['spesifikasi']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800"><?= esc($harga['satuan_kemasan'] ?: $harga['satuan']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800">Rp <?= number_format($harga['harga'], 0, ',', '.') ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-[13px]">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" class="btn-action-edit inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-lg transition-all focus:outline-none shadow-sm" onclick='openEditModal(<?= json_encode($harga) ?>)'>
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-action-delete inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-red-600 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 rounded-lg transition-all focus:outline-none shadow-sm" onclick="deleteHarga(<?= $harga['id'] ?>)">
                                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Modals -->
    <?php echo view('purchasing/master-data/partials/modal-harga'); ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        window.materialsData = <?= json_encode($materials) ?>;
    </script>
    <script src="<?= base_url('assets/js/purchasing/master-data-harga.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
