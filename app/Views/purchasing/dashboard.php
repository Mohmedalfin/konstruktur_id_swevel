<?= $this->extend('purchasing/layouts/main') ?>

<?= $this->section('styles') ?>
    <style>
        .kpi-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .kpi-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .master-data-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .chart-container {
            position: relative;
            height: 130px;
            width: 130px;
            margin: 0 auto;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .legend-color {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            margin-right: 6px;
            flex-shrink: 0;
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        
        <!-- TOP KPI CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Card 1 -->
            <div class="kpi-card group">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-blue-50 transition-transform duration-500 group-hover:scale-110"></div>
                <div class="relative flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-money-check-dollar text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total PR Masuk</p>
                        <h3 class="text-3xl font-black text-[#1e293b] mb-2"><?= $pr['total'] ?></h3>
                        <p class="text-[11px] font-semibold text-gray-500">
                            <?= $pr['menunggu'] ?> Menunggu
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="kpi-card group">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-indigo-50 transition-transform duration-500 group-hover:scale-110"></div>
                <div class="relative flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file-invoice text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total PO Aktif</p>
                        <h3 class="text-3xl font-black text-[#1e293b] mb-2"><?= $po['total'] ?></h3>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="bg-[#e0f2fe] text-[#0284c7] px-2 py-0.5 rounded text-[10px] font-bold"><?= $po['diproses'] ?> Diproses</span>
                            <span class="bg-[#dcfce3] text-[#166534] px-2 py-0.5 rounded text-[10px] font-bold"><?= $po['selesai_tiba'] ?> Selesai</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="kpi-card group">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-emerald-50 transition-transform duration-500 group-hover:scale-110"></div>
                <div class="relative flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-coins text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nilai PO Bulan Ini</p>
                        <h3 class="text-xl font-black text-[#1e293b] mb-2">Rp <?= number_format($po['nilai_bulan_ini'], 0, ',', '.') ?></h3>
                        <p class="text-[11px] font-bold <?= $po['persentase_kenaikan'] >= 0 ? 'text-emerald-500' : 'text-rose-500' ?> flex items-center gap-1">
                            <i class="fa-solid <?= $po['persentase_kenaikan'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' ?>"></i> <?= $po['persentase_kenaikan'] >= 0 ? '+' : '' ?><?= $po['persentase_kenaikan'] ?>% dari bulan lalu
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="kpi-card group">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-amber-50 transition-transform duration-500 group-hover:scale-110"></div>
                <div class="relative flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-clock text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">PR Belum Diproses</p>
                        <h3 class="text-3xl font-black text-[#1e293b] mb-2"><?= $pr['menunggu'] ?></h3>
                        <p class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-100 rounded-full px-2 py-0.5 inline-block">
                            Perlu perhatian
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHARTS AND TABLE -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            
            <!-- Chart 1 -->
            <div class="bg-white rounded-xl shadow-md p-5 border border-gray-200 flex flex-col h-full">
                <h3 class="text-md font-bold text-[#1e293b] mb-4">Status Purchase Request</h3>
                <div class="flex-1 grid grid-cols-12 items-center gap-2">
                    <div class="col-span-6 md:col-span-5 relative">
                        <div class="chart-container">
                            <canvas id="prStatusChart"></canvas>
                            <!-- Center Text -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-1">
                                <span class="text-[10px] font-bold text-gray-800">Total PR</span>
                                <span class="text-xl font-black text-gray-900">24</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-6 md:col-span-7 flex flex-col justify-center">
                        <div class="legend-item">
                            <div class="flex items-center"><div class="legend-color bg-blue-500"></div> Diproses</div>
                            <span><?= $pr['diproses'] ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="flex items-center"><div class="legend-color bg-purple-500"></div> Menunggu</div>
                            <span><?= $pr['menunggu'] ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="flex items-center"><div class="legend-color bg-amber-500"></div> Parsial</div>
                            <span><?= $pr['parsial'] ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="flex items-center"><div class="legend-color bg-emerald-500"></div> Selesai</div>
                            <span><?= $pr['selesai'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart 2 -->
            <div class="bg-white rounded-xl shadow-md p-5 border border-gray-200 flex flex-col h-full">
                <h3 class="text-md font-bold text-[#1e293b] mb-4">Status PO Tracking</h3>
                <div class="flex-1 grid grid-cols-12 items-center gap-2">
                    <div class="col-span-6 md:col-span-5 relative">
                        <div class="chart-container">
                            <canvas id="poStatusChart"></canvas>
                            <!-- Center Text -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-1">
                                <span class="text-[10px] font-bold text-gray-800">Total PO</span>
                                <span class="text-xl font-black text-gray-900">6</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-6 md:col-span-7 flex flex-col justify-center">
                        <div class="legend-item">
                            <div class="flex items-center"><div class="legend-color bg-amber-500"></div> Diproses</div>
                            <span><?= $po['diproses'] ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="flex items-center"><div class="legend-color bg-blue-500"></div> Pengiriman</div>
                            <span><?= $po['pengiriman'] ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="flex items-center whitespace-normal leading-tight"><div class="legend-color bg-emerald-500"></div> <span>Selesai Tiba</span></div>
                            <span class="ml-1"><?= $po['selesai_tiba'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200 h-full">
                <h3 class="text-md font-bold text-[#1e293b] mb-4">PR Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#e2e8f0]">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-bold text-[#1e293b] tracking-wider">Nomor PR</th>
                                <th scope="col" class="px-3 py-2 text-center text-xs font-bold text-[#1e293b] tracking-wider">Tgl Permintaan</th>
                                <th scope="col" class="px-3 py-2 text-center text-xs font-bold text-[#1e293b] tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($pr['terbaru'])): ?>
                                <tr>
                                    <td colspan="3" class="px-3 py-4 text-center text-xs font-semibold text-gray-500">Belum ada data PR.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pr['terbaru'] as $item): 
                                    $date = new DateTime($item['request_date']);
                                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                                    $formattedDate = $date->format('j') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y');
                                ?>
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs font-bold text-[#1e293b]"><?= esc($item['pr_number']) ?></td>
                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-center text-gray-600 font-medium"><?= $formattedDate ?></td>
                                    <td class="px-3 py-2 whitespace-nowrap text-center">
                                        <?php if (strtolower($item['status']) == 'diproses'): ?>
                                            <span class="badge-diproses">Diproses</span>
                                        <?php elseif (strtolower($item['status']) == 'parsial'): ?>
                                            <span class="badge-parsial">Parsial</span>
                                        <?php elseif (strtolower($item['status']) == 'selesai'): ?>
                                            <span class="badge-selesai">Selesai</span>
                                        <?php else: ?>
                                            <span class="bg-gray-200 text-gray-800 rounded-full px-2 py-1 text-[11px] font-bold">Menunggu</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- BOTTOM MASTER DATA SUMMARY -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
            <h3 class="text-md font-bold text-[#1e293b] mb-4">Ringkasan Master Data</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Data 1 -->
                <div class="master-data-card group relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-blue-50 opacity-50 transition-transform duration-500 group-hover:scale-125 z-0"></div>
                    <div class="bg-blue-100 p-4 rounded-xl z-10 relative transition-transform duration-300 group-hover:rotate-6">
                        <i class="fa-solid fa-store text-3xl text-blue-600 drop-shadow-sm"></i>
                    </div>
                    <div class="z-10 relative">
                        <div class="text-3xl font-black text-slate-800"><?= number_format($master_data['supplier'], 0, ',', '.') ?></div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Supplier Terdaftar</div>
                    </div>
                </div>

                <!-- Data 2 -->
                <div class="master-data-card group relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-emerald-50 opacity-50 transition-transform duration-500 group-hover:scale-125 z-0"></div>
                    <div class="bg-emerald-100 p-4 rounded-xl z-10 relative transition-transform duration-300 group-hover:rotate-6">
                        <i class="fa-solid fa-boxes-stacked text-3xl text-emerald-600 drop-shadow-sm"></i>
                    </div>
                    <div class="z-10 relative">
                        <div class="text-3xl font-black text-slate-800"><?= number_format($master_data['material'], 0, ',', '.') ?></div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Material Terdaftar</div>
                    </div>
                </div>

                <!-- Data 3 -->
                <div class="master-data-card group relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-amber-50 opacity-50 transition-transform duration-500 group-hover:scale-125 z-0"></div>
                    <div class="bg-amber-100 p-4 rounded-xl z-10 relative transition-transform duration-300 group-hover:rotate-6">
                        <i class="fa-solid fa-tags text-3xl text-amber-600 drop-shadow-sm"></i>
                    </div>
                    <div class="z-10 relative">
                        <div class="text-3xl font-black text-slate-800"><?= number_format($master_data['harga'], 0, ',', '.') ?></div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Data Harga Tercatat</div>
                    </div>
                </div>
            </div>
        </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Chart.js Configuration
            Chart.defaults.font.family = 'sans-serif';
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(17, 24, 39, 0.9)';
            
            const prCtx = document.getElementById('prStatusChart').getContext('2d');
            const prChart = new Chart(prCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Diproses', 'Menunggu', 'Parsial', 'Selesai'],
                    datasets: [{
                        data: [<?= $pr['diproses'] ?>, <?= $pr['menunggu'] ?>, <?= $pr['parsial'] ?>, <?= $pr['selesai'] ?>],
                        backgroundColor: [
                            '#3b82f6', // Diproses (Blue)
                            '#8b5cf6', // Menunggu (Purple)
                            '#f59e0b', // Parsial (Orange)
                            '#10b981'  // Selesai (Green)
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            const poCtx = document.getElementById('poStatusChart').getContext('2d');
            const poChart = new Chart(poCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Diproses', 'Pengiriman', 'Selesai Tiba'],
                    datasets: [{
                        data: [<?= $po['diproses'] ?>, <?= $po['pengiriman'] ?>, <?= $po['selesai_tiba'] ?>],
                        backgroundColor: [
                            '#f59e0b', // Diproses (Orange)
                            '#3b82f6', // Pengiriman (Blue)
                            '#10b981'  // Selesai Tiba (Green)
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
<?= $this->endSection() ?>
