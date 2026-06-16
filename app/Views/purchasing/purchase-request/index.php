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
            border-radius: 4px;
            padding: 2px 12px;
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
            border-radius: 4px;
            padding: 2px 12px;
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
            border-radius: 4px;
            padding: 2px 12px;
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
</head>

<body class="font-sans antialiased text-sm">

    <!-- Top Navigation & Header Container -->
    <div class="bg-[#111827] w-full shadow-md">
        <!-- Navbar -->
        <?= view('purchasing/partials/navbar', ['activeNav' => 'purchase-request']) ?>

        <!-- Title -->
        <div class="py-12 flex justify-center items-center relative overflow-hidden bg-cover bg-center bg-no-repeat" style="background-image: url('<?= base_url('assets/images/BackgroundTopBar.png') ?>');">
            <div class="absolute inset-0 bg-[#111827]/80"></div>
            <h1 class="relative z-10 text-white text-4xl font-bold tracking-widest uppercase">PURCHASE REQUEST</h1>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 mt-6 pb-12">
        
        <!-- Card Body -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
            
            <!-- Toolbar -->
            <div class="flex gap-4 items-center mb-6">
                <!-- Search -->
                <div class="relative w-80">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                        <i class="fa-solid fa-search text-gray-500"></i>
                    </div>
                    <input type="text" id="searchPR" class="py-2 px-4 ps-10 block w-full border-gray-300 rounded-lg text-[13px] font-medium focus:border-blue-500 focus:ring-blue-500 border placeholder-gray-400" placeholder="Cari No. PR">
                </div>
                <!-- Date Filter -->
                <div class="relative w-64">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                        <i class="fa-regular fa-calendar text-gray-500"></i>
                    </div>
                    <input type="text" id="dateFilter" class="py-2 px-4 ps-10 block w-full border-gray-300 rounded-lg text-[13px] font-medium focus:border-blue-500 focus:ring-blue-500 border placeholder-gray-400" placeholder="Date Filters">
                </div>
            </div>

            <!-- Table -->
            <div class="border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-[#111827] text-white">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center text-[13px] font-bold tracking-wide w-16">No</th>
                            <th scope="col" class="px-4 py-3 text-center text-[13px] font-bold tracking-wide">Nomor PR</th>
                            <th scope="col" class="px-4 py-3 text-center text-[13px] font-bold tracking-wide">Tgl Permintaan</th>
                            <th scope="col" class="px-4 py-3 text-center text-[13px] font-bold tracking-wide">Total Item</th>
                            <th scope="col" class="px-4 py-3 text-center text-[13px] font-bold tracking-wide">Status PR</th>
                            <th scope="col" class="px-4 py-3 text-center text-[13px] font-bold tracking-wide w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300 bg-white text-[#1e293b]" id="prTableBody">
                        <?php if (empty($prs)): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-[13px] font-semibold text-gray-500">Belum ada data PR.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($prs as $pr): 
                                // Format tanggal to indonesian style manually: 10 Mei 2026
                                $date = new DateTime($pr['request_date']);
                                $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                                $formattedDate = $date->format('j') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y');
                            ?>
                                <tr class="<?= $no % 2 == 0 ? 'bg-[#cbd5e1]' : 'bg-[#f1f5f9]' ?> hover:bg-gray-200 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-[13px] font-bold text-center text-[#1e293b]"><?= $no++ ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-[13px] font-bold text-center text-[#1e293b]"><?= esc($pr['pr_number']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-[13px] font-bold text-center text-[#1e293b]"><?= $formattedDate ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-[13px] font-bold text-center text-[#1e293b]"><?= esc($pr['total_items']) ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <?php if ($pr['status'] == 'diproses'): ?>
                                            <span class="badge-diproses">Diproses</span>
                                        <?php elseif ($pr['status'] == 'parsial'): ?>
                                            <span class="badge-parsial">Parsial</span>
                                        <?php else: ?>
                                            <span class="badge-selesai">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <button type="button" class="btn-details" onclick="openDetailModal(<?= $pr['id'] ?>)">Details</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modals -->
    <?php echo view('purchasing/purchase-request/partials/modal-detail'); ?>
    <?php echo view('purchasing/purchase-request/partials/modal-create-po'); ?>
    <?php echo view('purchasing/purchase-request/partials/modal-success'); ?>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="<?= base_url('assets/js/purchasing/purchase-request.js?v=' . time()) ?>"></script>
</body>

</html>
