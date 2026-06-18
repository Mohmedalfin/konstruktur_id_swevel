<?= $this->extend('purchasing/layouts/main') ?>

<?= $this->section('styles') ?>
    <style>
        .btn-details {
            background-color: #2563eb; /* blue-600 */
            color: white;
            border-radius: 4px;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .btn-details:hover {
            background-color: #1d4ed8;
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
        <!-- STATS CARDS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-file-invoice text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Permintaan</p>
                        <h3 id="stat-total" class="text-xl font-bold text-slate-800 mt-0.5"><?= $stats['total'] ?></h3>
                    </div>
                </div>
            </div>

            <!-- Menunggu -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Menunggu</p>
                        <h3 id="stat-pending" class="text-xl font-bold text-slate-800 mt-0.5"><?= $stats['menunggu'] ?></h3>
                    </div>
                </div>
            </div>

            <!-- Diproses -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-cog text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Diproses</p>
                        <h3 id="stat-proses" class="text-xl font-bold text-slate-800 mt-0.5"><?= $stats['diproses'] + $stats['parsial'] ?></h3>
                    </div>
                </div>
            </div>

            <!-- Selesai -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Selesai</p>
                        <h3 id="stat-kirim" class="text-xl font-bold text-slate-800 mt-0.5"><?= $stats['selesai'] ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTIONS & FILTERS -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 relative w-48">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                        <i class="fa-solid fa-search text-gray-500 text-xs"></i>
                    </div>
                    <input type="text" id="searchPR" class="py-1.5 px-4 ps-8 block w-full border-slate-300 rounded-lg text-xs font-medium focus:border-blue-500 focus:ring-blue-500 border placeholder-slate-400 shadow-sm" placeholder="Cari No. PR...">
                </div>
                <div class="flex items-center gap-2">
                    <input type="month" id="filter-month" class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:border-blue-500 cursor-pointer" value="<?= date('Y-m') ?>">
                </div>
                <div class="hidden sm:block w-px h-6 bg-slate-200"></div>
                <div class="flex flex-wrap items-center gap-2" id="status-filters">
                    <button data-status="all" class="filter-btn px-4 py-1.5 text-[11px] font-bold rounded-lg shadow-sm border transition-all focus:outline-none bg-slate-800 text-white border-slate-800 uppercase tracking-wider">
                        Semua
                    </button>
                    <button data-status="pending" class="filter-btn px-4 py-1.5 text-[11px] font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none uppercase tracking-wider">
                        Menunggu
                    </button>
                    <button data-status="diproses" class="filter-btn px-4 py-1.5 text-[11px] font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none uppercase tracking-wider">
                        Diproses
                    </button>
                    <button data-status="selesai" class="filter-btn px-4 py-1.5 text-[11px] font-bold rounded-lg shadow-sm border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-all focus:outline-none uppercase tracking-wider">
                        Selesai
                    </button>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center gap-2 mt-3 sm:mt-0">
                <a href="<?= base_url('permintaan/deviasi?source=purchasing') ?>" class="inline-flex items-center justify-center gap-2 px-4 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-bold shadow-sm transition-all focus:outline-none cursor-pointer">
                    <i class="fas fa-chart-line text-[10px]"></i>
                    <span>Laporan Deviasi</span>
                </a>
            </div>
        </div>

        <!-- Card Body -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

            <!-- Table -->
            <div class="border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-[#111827] text-white border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Nomor PR</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Tgl Permintaan</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Keterangan</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Total Item</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Status PR</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="prTableBody">
                        <?php if (empty($prs)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center bg-white">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-box-open text-2xl text-slate-300"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada data Purchase Request</p>
                                    <p class="text-xs mt-1 text-slate-400">Tidak ada data yang sesuai dengan filter saat ini.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <!-- Empty State for JS Filter -->
                            <tr id="empty-state-row" style="display: none;">
                                <td colspan="7" class="px-6 py-12 text-center bg-white">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-box-open text-2xl text-slate-300"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada data Purchase Request</p>
                                    <p class="text-xs mt-1 text-slate-400">Tidak ada data yang sesuai dengan filter saat ini.</p>
                                </td>
                            </tr>
                            <?php $no = 1; foreach ($prs as $pr): 
                                // Format tanggal to indonesian style manually: 10 Mei 2026
                                $date = new DateTime($pr['request_date']);
                                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                                $formattedDate = $date->format('j') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y');
                            ?>
                                <tr class="table-row hover:bg-slate-50/80 transition-colors" data-status="<?= esc($pr['status']) ?>" data-date="<?= date('Y-m', strtotime($pr['request_date'])) ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-500"><?= $no++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-center text-slate-800"><?= esc($pr['pr_number']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-center text-slate-600"><?= $formattedDate ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-center text-slate-500 truncate max-w-[200px]"><?= esc($pr['keterangan'] ?? '-') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-center text-slate-800"><?= esc($pr['total_items']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php if ($pr['status'] == 'pending' || $pr['status'] == 'draft'): ?>
                                            <span class="badge-pending">Pending</span>
                                        <?php elseif ($pr['status'] == 'diproses' || $pr['status'] == 'ordered'): ?>
                                            <span class="badge-diproses">Diproses</span>
                                        <?php elseif ($pr['status'] == 'parsial'): ?>
                                            <span class="badge-parsial">Parsial</span>
                                        <?php else: ?>
                                            <span class="badge-selesai">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button type="button" class="btn-details" onclick="openDetailModal(<?= $pr['id'] ?>)">Details</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    <!-- Modals -->
    <?php echo view('purchasing/purchase-request/partials/modal-detail'); ?>
    <?php echo view('purchasing/purchase-request/partials/modal-create-po'); ?>
    <?php echo view('purchasing/purchase-request/partials/modal-success'); ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/purchasing/purchase-request.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
