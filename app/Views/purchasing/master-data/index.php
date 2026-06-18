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
        <!-- Tabs -->
        <div class="flex">
            <a href="<?= base_url('purchasing/master-data') ?>" class="tab-active px-8 py-3 rounded-tl-xl rounded-tr-xl text-[15px] flex items-center gap-2 shadow-[0_-2px_10px_rgba(0,0,0,0.05)] z-10 relative">
                <i class="fa-solid fa-store"></i> Supplier
            </a>
            <a href="<?= base_url('purchasing/master-data/material') ?>" class="tab-inactive px-8 py-3 rounded-tl-xl rounded-tr-xl text-[15px] flex items-center gap-2 -ml-3 z-0 relative">
                <i class="fa-solid fa-cube"></i> Material
            </a>
            <a href="<?= base_url('purchasing/master-data/harga') ?>" class="tab-inactive px-8 py-3 rounded-tl-xl rounded-tr-xl text-[15px] flex items-center gap-2 -ml-3 z-0 relative">
                <i class="fa-solid fa-tags"></i> Harga
            </a>
        </div>

        <!-- Card Body -->
        <div class="bg-white rounded-b-xl rounded-tr-xl shadow-md p-6 border border-gray-200">
            
            <!-- Toolbar -->
            <div class="flex justify-between items-center mb-5">
                <div class="relative w-80">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                        <i class="fa-solid fa-search text-gray-500"></i>
                    </div>
                    <input type="text" id="searchSupplier" class="py-2.5 px-4 ps-10 block w-full border-gray-400 rounded-lg text-[13px] font-medium focus:border-blue-500 focus:ring-blue-500 border placeholder-gray-500" placeholder="Cari nama supplier...">
                </div>
                
                <button type="button" class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-bold rounded-lg bg-[#111827] text-white hover:bg-[#0f172a] transition-colors" onclick="openTambahModal()">
                    <i class="fa-solid fa-plus"></i> Tambah Supplier
                </button>
            </div>

            <!-- Table -->
            <div class="border border-gray-300 rounded-lg overflow-hidden">
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
