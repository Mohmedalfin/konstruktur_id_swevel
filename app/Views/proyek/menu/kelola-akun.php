<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun Tim - Kontraktor.id</title>
    <link rel="preload" href="<?= base_url('assets/css/output.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php echo view('partials/global-loader'); ?>
    <?php echo view('partials/header'); ?>
    <?php echo view('partials/topbar', [
        'title' => 'KELOLA AKUN TIM',
        'subtitle' => 'Tambahkan akun untuk sistem Gudang dan Purchasing.'
    ]); ?>

    <main class="w-full grow">
        <?php echo view('partials/team-accounts'); ?>
    </main>

    <script>
        window.TEAM_ACCOUNTS_INIT = {
            listUrl: <?= json_encode(base_url('kelola-akun/data')) ?>,
            createUrl: <?= json_encode(base_url('kelola-akun/create')) ?>,
            deleteUrl: <?= json_encode(base_url('kelola-akun/delete')) ?>
        };
    </script>
    <script type="module" src="<?= base_url('js/shared/ui/global-loader.js') ?>"></script>
    <script src="<?= base_url('assets/js/preline.js') ?>" defer></script>
    <script type="module" src="<?= base_url('js/team-accounts/index.js') ?>"></script>
</body>
</html>
