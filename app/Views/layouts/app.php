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

    <!-- PRELINE JS (wajib) -->
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>

    <!-- Auto init (wajib) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.HSStaticMethods) window.HSStaticMethods.autoInit();
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>