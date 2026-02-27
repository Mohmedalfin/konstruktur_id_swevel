<?php
// Read GET params passed from dashboard.php links
$rabId   = isset($_GET['id'])   ? (int) $_GET['id']     : null;
$rabMode = isset($_GET['mode']) ? $_GET['mode']          : ($rabId ? 'readonly' : null);

// Sanitize mode
if (! in_array($rabMode, ['readonly', 'new'], true)) {
    $rabMode = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu RAB &amp; RAP</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'RAB & RAP', 'subtitle' => '']); ?>

    <?php echo view('partials/table-rab', ['tableVisible' => true]); ?>

    <!-- Pass init state to ajax_rab.js -->
    <script>
        window.RAB_INIT = {
            mode: <?= $rabMode ? json_encode($rabMode) : 'null' ?>,
            id:   <?= $rabId   ? json_encode($rabId)   : 'null' ?>
        };
    </script>

    <script src="<?= base_url('ajax/ajax_rab.js') ?>"></script>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
    <script src="<?= base_url('assets/js/partials/navbar.js') ?>"></script>
</body>
</html>