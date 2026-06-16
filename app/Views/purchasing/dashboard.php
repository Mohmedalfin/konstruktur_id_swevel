<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { background-color: #f3f4f6; }
        .nav-item {
            color: #d1d5db;
            font-size: 13px;
            font-weight: 600;
            padding: 0 24px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .nav-item:hover { color: white; }
        .nav-active {
            background-color: white;
            color: #111827;
            font-weight: bold;
            font-size: 13px;
            padding: 0 24px;
            display: flex;
            align-items: center;
        }
        .badge-parsial {
            background-color: #fef08a; /* yellow-200 */
            color: #854d0e; /* yellow-800 */
            border-radius: 9999px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-parsial::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #eab308;
            border-radius: 50%;
        }
        .badge-diproses {
            background-color: #eff6ff; /* blue-50 */
            color: #2563eb; /* blue-600 */
            border-radius: 9999px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-diproses::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #3b82f6;
            border-radius: 50%;
        }
        .badge-selesai {
            background-color: #bbf7d0; /* green-200 */
            color: #166534; /* green-800 */
            border-radius: 9999px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-selesai::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #22c55e;
            border-radius: 50%;
        }
        
        .kpi-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .master-data-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.25rem;
            border: 2px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
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
</head>

<body class="font-sans antialiased text-sm">

    <!-- Top Navigation & Header Container -->
    <div class="bg-[#111827] w-full shadow-md">
        <!-- Navbar -->
        <?= view('purchasing/partials/navbar', ['activeNav' => 'dashboard']) ?>

        <!-- Title -->
        <div class="py-12 flex justify-center items-center relative overflow-hidden bg-cover bg-center bg-no-repeat" style="background-image: url('<?= base_url('assets/images/BackgroundTopBar.png') ?>');">
            <div class="absolute inset-0 bg-[#111827]/80"></div>
            <h1 class="relative z-10 text-white text-4xl font-bold tracking-widest uppercase">DASHBOARD</h1>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 mt-6 pb-12 space-y-6">
        
        <!-- TOP KPI CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Card 1 -->
            <div class="kpi-card relative">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-sm font-bold text-[#1e293b]">Total PR Masuk</h3>
                    <i class="fa-solid fa-money-check-dollar text-[#1e293b] text-xl"></i>
                </div>
                <div class="text-3xl font-black text-[#1e293b] mb-2"><?= $pr['total'] ?></div>
                <div class="text-sm font-semibold text-gray-500">
                    <?= $pr['menunggu'] ?> Menunggu
                </div>
            </div>

            <!-- Card 2 -->
            <div class="kpi-card relative">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-sm font-bold text-[#1e293b]">Total PO Aktif</h3>
                    <i class="fa-solid fa-file-invoice text-[#1e293b] text-xl"></i>
                </div>
                <div class="text-3xl font-black text-[#1e293b] mb-2"><?= $po['total'] ?></div>
                <div class="flex items-center gap-2">
                    <span class="bg-[#e0f2fe] text-[#0284c7] px-2 py-1 rounded text-[11px] font-bold"><?= $po['diproses'] ?> Diproses</span>
                    <span class="bg-[#dcfce3] text-[#166534] px-2 py-1 rounded text-[11px] font-bold"><?= $po['selesai_tiba'] ?> Selesai Tiba</span>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="kpi-card relative">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-sm font-bold text-[#1e293b]">Nilai PO Bulan Ini</h3>
                    <i class="fa-solid fa-coins text-[#1e293b] text-xl"></i>
                </div>
                <div class="text-2xl font-black text-[#1e293b] mb-2">Rp <?= number_format($po['nilai_bulan_ini'], 0, ',', '.') ?></div>
                <div class="text-sm font-bold <?= $po['persentase_kenaikan'] >= 0 ? 'text-green-500' : 'text-red-500' ?> flex items-center gap-1">
                    <i class="fa-solid <?= $po['persentase_kenaikan'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' ?>"></i> <?= $po['persentase_kenaikan'] >= 0 ? '+' : '' ?><?= $po['persentase_kenaikan'] ?>% dari bulan lalu
                </div>
            </div>

            <!-- Card 4 -->
            <div class="kpi-card relative">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-sm font-bold text-[#1e293b]">PR Belum Diproses</h3>
                    <i class="fa-regular fa-clock text-[#1e293b] text-xl"></i>
                </div>
                <div class="text-3xl font-black text-[#1e293b] mb-2"><?= $pr['menunggu'] ?></div>
                <div class="text-sm font-bold text-red-500 bg-red-100 rounded-full px-3 py-1 inline-block w-max">
                    Perlu perhatian
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
                            <div class="flex items-center"><div class="legend-color bg-[#0ea5e9]"></div> Diproses</div>
                            <span><?= $pr['diproses'] ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="flex items-center"><div class="legend-color bg-[#cbd5e1]"></div> Menunggu</div>
                            <span><?= $pr['menunggu'] ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="flex items-center"><div class="legend-color bg-[#f59e0b]"></div> Parsial</div>
                            <span><?= $pr['parsial'] ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="flex items-center"><div class="legend-color bg-[#22c55e]"></div> Selesai</div>
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
                            <div class="flex items-center"><div class="legend-color bg-[#f59e0b]"></div> Diproses</div>
                            <span><?= $po['diproses'] ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="flex items-center"><div class="legend-color bg-[#0ea5e9]"></div> Pengiriman</div>
                            <span><?= $po['pengiriman'] ?></span>
                        </div>
                        <div class="legend-item">
                            <div class="flex items-center whitespace-normal leading-tight"><div class="legend-color bg-[#22c55e]"></div> <span>Selesai Tiba</span></div>
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
                <div class="master-data-card">
                    <div class="bg-[#f1f5f9] p-4 rounded-lg">
                        <i class="fa-solid fa-store text-3xl text-[#1e293b]"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-[#1e293b]"><?= number_format($master_data['supplier'], 0, ',', '.') ?></div>
                        <div class="text-sm font-bold text-gray-600">Supplier Terdaftar</div>
                    </div>
                </div>

                <!-- Data 2 -->
                <div class="master-data-card">
                    <div class="bg-[#f1f5f9] p-4 rounded-lg">
                        <i class="fa-solid fa-boxes-stacked text-3xl text-[#1e293b]"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-[#1e293b]"><?= number_format($master_data['material'], 0, ',', '.') ?></div>
                        <div class="text-sm font-bold text-gray-600">Material Terdaftar</div>
                    </div>
                </div>

                <!-- Data 3 -->
                <div class="master-data-card">
                    <div class="bg-[#f1f5f9] p-4 rounded-lg">
                        <i class="fa-solid fa-tags text-3xl text-[#1e293b]"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-[#1e293b]"><?= number_format($master_data['harga'], 0, ',', '.') ?></div>
                        <div class="text-sm font-bold text-gray-600">Data Harga Tercatat</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                            '#0ea5e9', // Diproses (Blue)
                            '#cbd5e1', // Menunggu (Gray)
                            '#f59e0b', // Parsial (Orange)
                            '#22c55e'  // Selesai (Green)
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
                            '#0ea5e9', // Pengiriman (Blue)
                            '#22c55e'  // Selesai Tiba (Green)
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
</body>
</html>
