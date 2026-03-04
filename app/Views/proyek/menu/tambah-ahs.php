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
    <?php echo view('partials/topbar', ['title' => 'Tambah AHS', 'subtitle' => '']); ?>

    <?php echo view('partials/item-ahs'); ?>

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