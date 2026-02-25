<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Rab & Rab</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'Menu Rab & Rab', 'subtitle' => '']); ?>
    <?php echo view('partials/table-rab'); ?>

    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('assets/js/menu-rab.js') ?>"></script>
</body>
</html>