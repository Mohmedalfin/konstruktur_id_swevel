<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= esc($title ?? 'Purchasing') ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/css/output.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <?= $this->renderSection('styles') ?>
    <style>
        body { background-color: #f8fafc; }
        .hs-overlay-backdrop {
            background-color: rgba(15, 23, 42, 0.5) !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
        }
    </style>
</head>

<body class="font-sans antialiased text-sm text-slate-800">

    <?= $this->include('partials/global-loader') ?>
    
    <?= view('purchasing/partials/navbar', ['activeNav' => $activeNav ?? '']) ?>
    <?= view('partials/topbar', ['title' => $title ?? 'Purchasing']) ?>

    <main class="w-full max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-12 space-y-6">
        <?= $this->renderSection('content') ?>
    </main>

    <script type="module" src="<?= base_url('js/shared/ui/global-loader.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('js/shared/notification-poll.js') ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.addEventListener('load', () => {
            window.HSStaticMethods?.autoInit();
        });
    </script>

    <?= $this->renderSection('scripts') ?>

</body>
</html>
