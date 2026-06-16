<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= esc($topbarTitle ?? 'Gudang') ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/css/output.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <style>
        /* Styling backdrop modal agar semi-transparan dan memperlihatkan halaman belakang */
        .hs-overlay-backdrop {
            background-color: rgba(15, 23, 42, 0.5) !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
        }
    </style>
</head>

<body class="bg-gray-50">

    <?= $this->include('partials/global-loader') ?>
    
    <!-- Navbar Khusus Gudang (Menggantikan Header Utama) -->
    <?= $this->include('gudang/partials/navbar') ?>

    <!-- Banner Topbar (Sama dengan proyek) -->
    <?php echo view('partials/topbar', ['title' => $topbarTitle ?? 'Modul Gudang']); ?>

    <main class="w-full max-w-[85rem] mx-auto px-4 py-6">
        <?= $this->renderSection('content') ?>
    </main>

    <script type="module" src="<?= base_url('js/shared/ui/global-loader.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/lodash/lodash.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/vanilla-calendar-pro/index.js') ?>"></script>

    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script>
        window.addEventListener('load', () => {
            window.HSStaticMethods?.autoInit();
            window.HSDatepicker?.autoInit?.();
        });
    </script>

    <?= $this->renderSection('scripts') ?>

</body>

</html>
