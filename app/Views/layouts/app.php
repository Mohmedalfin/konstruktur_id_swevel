<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'App') ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/css/output.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
</head>

<body class="bg-gray-50">

    <?= $this->include('partials/header') ?>
    <?= $this->renderSection('topbar') ?>

    <main class="container mx-auto px-4 py-6">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <!-- Vendor JS (WAJIB untuk Advanced Datepicker) -->
    <script src="<?= base_url('assets/vendor/lodash/lodash.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/vanilla-calendar-pro/index.js') ?>"></script>

    <!-- ✅ PRELINE CORE (INI YANG KAMU KURANG) -->
    <script src="<?= base_url('assets/vendor/preline/dist/index.js') ?>"></script>

    <script>
        window.addEventListener('load', () => {
            window.HSStaticMethods?.autoInit();
            // biasanya cukup autoInit saja, tapi aman:
            window.HSDatepicker?.autoInit?.();
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>