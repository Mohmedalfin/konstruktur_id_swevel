<?= $this->extend('purchasing/layouts/main') ?>

<?= $this->section('styles') ?>
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
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
        <!-- ACTIONS & FILTERS -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-2 relative w-64 md:w-80">
                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                    <i class="fa-solid fa-search text-slate-400 text-xs"></i>
                </div>
                <input type="text" id="searchSupplier" class="py-2 px-4 ps-8 block w-full border-slate-300 rounded-lg text-xs font-medium focus:border-blue-500 focus:ring-blue-500 border placeholder-slate-400 shadow-sm" placeholder="Cari nama supplier...">
            </div>
            
            <button type="button" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-semibold text-sm py-2 px-5 rounded-lg transition-all shadow-sm focus:ring-2 focus:ring-primary/20" onclick="openTambahModal()">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Supplier
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="<?= base_url('purchasing/master-data') ?>" class="px-6 py-2.5 bg-white text-[#111827] border-b-2 border-primary shadow-sm rounded-t-lg text-sm font-bold flex items-center gap-2 transition-all">
                <i class="fa-solid fa-store text-primary"></i> Supplier
            </a>
            <a href="<?= base_url('purchasing/master-data/material') ?>" class="px-6 py-2.5 bg-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50 border-b-2 border-transparent rounded-t-lg text-sm font-semibold flex items-center gap-2 transition-all">
                <i class="fa-solid fa-cube"></i> Material
            </a>
            <a href="<?= base_url('purchasing/master-data/harga') ?>" class="px-6 py-2.5 bg-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50 border-b-2 border-transparent rounded-t-lg text-sm font-semibold flex items-center gap-2 transition-all">
                <i class="fa-solid fa-tags"></i> Harga
            </a>
        </div>

        <!-- Card Body (Table Container) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-[#111827] text-white border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Nama Supplier</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Telepon</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">NPWP</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Rekening Bank</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="supplierTableBody">
                        <?php if (empty($suppliers)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center bg-white">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-truck text-2xl text-slate-300"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada data supplier</p>
                                    <p class="text-xs mt-1 text-slate-400">Silakan tambah supplier baru.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($suppliers as $supplier): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-500"><?= $no++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800"><?= esc($supplier['nama_supplier']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800"><?= esc($supplier['telepon']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800"><?= esc($supplier['email']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800"><?= esc($supplier['npwp']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800"><?= esc($supplier['rekening_bank']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-[13px]">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" class="btn-action-edit inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-lg transition-all focus:outline-none shadow-sm" onclick='openEditModal(<?= json_encode($supplier) ?>)'>
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                            <button type="button" class="btn-action-delete inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-red-600 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 rounded-lg transition-all focus:outline-none shadow-sm" onclick="deleteSupplier(<?= $supplier['id'] ?>)">
                                                <i class="fa-solid fa-trash-can"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Modals -->
    <?php echo view('purchasing/master-data/partials/modal-supplier'); ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/purchasing/master-data.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
