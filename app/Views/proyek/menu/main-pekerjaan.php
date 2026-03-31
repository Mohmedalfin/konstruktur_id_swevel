<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah AHS</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/fontawesome/css/all.min.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">

    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'Tambah Pekerjaan', 'subtitle' => '']); ?>

    <div class="w-full px-3 sm:px-6 lg:px-8 mt-6 mb-2">
        <nav class="flex items-center text-sm font-medium text-table-subtle">
            <button onclick="goBackToRab()" class="hover:text-primary transition-colors focus:outline-none cursor-pointer">Menu RAP</button>
            <svg class="w-3 h-3 mx-2 shrink-0 text-table-border" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-table-strong">Tambah Pekerjaan</span>
        </nav>
    </div>

    <?php echo view('partials/table-pekerjaan'); ?>


    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('assets/js/partials/navbar.js') ?>"></script>
    <script>
        window.addEventListener('load', function () {
            window.HSStaticMethods?.autoInit();
        });
    </script>
</body>
</html>