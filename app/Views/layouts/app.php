<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= esc($title ?? 'App') ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/css/output.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css" />
</head>

<body class="bg-gray-50">

    <?= $this->include('partials/global-loader') ?>
    <?= $this->include('partials/header') ?>
    <?php echo view('partials/topbar', ['title' => $topbarTitle ?? 'Daftar Project', 'subtitle' => '']); ?>


    <main class="container mx-auto px-4 py-6">
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
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>

    <?= $this->renderSection('scripts') ?>

</body>

</html>