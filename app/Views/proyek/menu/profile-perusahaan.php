<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile Perusahaan</title>
    <link rel="preload" href="<?= base_url('assets/css/output.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php echo view('partials/global-loader'); ?>
    <?php echo view('partials/header'); ?>
    <?php echo view('partials/topbar', [
        'title' => 'EDIT PROFILE PERUSAHAAN',
        'subtitle' => 'Perbarui informasi dan identitas perusahaan Anda.'
    ]); ?>

    <main class="w-full grow">
        <?php echo view('partials/table-profile'); ?>
    </main>

    <script>
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
