<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Perusahaan - Kontraktor.id</title>
    <link rel="preload" href="<?= base_url('assets/css/output.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php echo view('partials/global-loader'); ?>
    <?php if (strtolower(session()->get('kategori_akun') ?? session()->get('role') ?? '') === 'gudang') : ?>
        <?php echo view('gudang/partials/navbar'); ?>
    <?php elseif (strtolower(session()->get('kategori_akun') ?? session()->get('role') ?? '') === 'purchasing') : ?>
        <?php echo view('purchasing/partials/navbar'); ?>
    <?php else : ?>
        <?php echo view('partials/header'); ?>
    <?php endif; ?>
    <?php echo view('partials/topbar', [
        'title' => 'PROFILE PERUSAHAAN',
        'subtitle' => 'Perbarui informasi dan identitas perusahaan Anda.'
    ]); ?>

    <main class="w-full grow">
        <?php echo view('partials/table-profile'); ?>
    </main>

    <script>
        window.manualLoader = true;
        window.PROFILE_INIT = {
            fetchUrl: <?= json_encode(base_url('profile/data')) ?>,
            updateUrl: <?= json_encode(base_url('profile/update')) ?>
        };
    </script>
    <script type="module" src="<?= base_url('js/shared/ui/global-loader.js') ?>"></script>
    <script src="<?= base_url('assets/js/preline.js') ?>" defer></script>
    <script type="module" src="<?= base_url('js/profile/index.js') ?>"></script>
</body>
</html>
