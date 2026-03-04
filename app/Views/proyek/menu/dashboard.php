<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'Dashboard', 'subtitle' => '']); ?>

    <div class="max-w-6xl mx-auto p-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-table-strong">RAB Management</h1>
            <a href="<?= base_url('menu-rap?mode=new') ?>"
                class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition font-medium text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah RAB
            </a>
        </div>

        <!-- RAB CARD LIST -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <a href="<?= base_url('menu-rap?id=1') ?>"
               class="rab-card block bg-white shadow rounded-xl p-4 hover:shadow-md transition-shadow border border-transparent hover:border-primary/30">
                <h3 class="font-semibold text-table-strong">Gedung Klinik</h3>
                <p class="text-sm text-table-subtle mt-1">Kab. Sleman — Tahun 2026</p>
            </a>
            <a href="<?= base_url('menu-rap?id=2') ?>"
               class="rab-card block bg-white shadow rounded-xl p-4 hover:shadow-md transition-shadow border border-transparent hover:border-primary/30">
                <h3 class="font-semibold text-table-strong">Renovasi RSUD Bantul</h3>
                <p class="text-sm text-table-subtle mt-1">Kab. Bantul — Tahun 2026</p>
            </a>
        </div>

    </div>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
    <script src="<?= base_url('assets/js/partials/navbar.js') ?>"></script>
</body>
</html>