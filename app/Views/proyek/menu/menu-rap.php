<?php
// Read GET params passed from dashboard.php links
$rabId = $idProject ?? null;
$rabMode = $rabId ? 'readonly' : null;

// Sanitize mode
if (!in_array($rabMode, ['readonly', 'new'], true)) {
    $rabMode = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAP - Kontraktor.id</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css" />
</head>

<body class="bg-gray-50 min-h-screen">
    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'RAP Rencana', 'subtitle' => '']); ?>

    <?php echo view('partials/table-rab', ['tableVisible' => true]); ?>

    <!-- Pass init state to ajax_rab.js -->
    <!-- <script>
        window.RAB_INIT = {
            mode:              <?= $rabMode ? json_encode($rabMode) : 'null' ?>,
            id:                <?= $rabId ? json_encode($rabId) : 'null' ?>,
            idProject:         <?= $rabId ? json_encode($rabId) : 'null' ?>,
            slug:              <?= isset($slug) ? json_encode($slug) : 'null' ?>,
            rincianAhsUrl:     <?= json_encode(base_url('menu-rap/rincian-ahs')) ?>,
            tambahAhsUrl:      <?= json_encode(base_url('menu-rap/tambah-ahs')) ?>,
            apiRapUrl:         <?= json_encode(base_url('api/rap')) ?>,
            apiKategoriUrl:    <?= json_encode(base_url('api/rap/kategori')) ?>,
            apiKategoriMaster: <?= json_encode(base_url('api/rap/kategori-master')) ?>,
            apiPekerjaanUrl:   <?= json_encode(base_url('api/rap/pekerjaan')) ?>
        };
    </script> -->
    <script>
        window.RAB_INIT = {
            mode: 'readonly',
            id: <?= json_encode($idProject) ?>,
            idProject: <?= json_encode($idProject) ?>,
            slug: <?= json_encode($slug) ?>,
            rincianAhsUrl: <?= json_encode(base_url('menu-rap/rincian-ahs')) ?>,
            tambahAhsUrl: <?= json_encode(base_url('menu-rap/tambah-ahs')) ?>,
            apiRapUrl: <?= json_encode(base_url('api/rap')) ?>,
            apiKategoriUrl: <?= json_encode(base_url('api/rap/kategori')) ?>,
            apiKategoriMaster: <?= json_encode(base_url('api/rap/kategori-master')) ?>,
            apiPekerjaanUrl: <?= json_encode(base_url('api/rap/pekerjaan')) ?>
        };

        // Persist the current project slug so the navbar can restore it
        if (window.RAB_INIT.slug) {
            localStorage.setItem('lastProjectSlug', window.RAB_INIT.slug);
        }
    </script>

    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>

    <script src="<?= base_url('assets/js/vendor/exceljs.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
    <script type="module" src="<?= base_url('js/rab/index.js?v=' . time()) ?>"></script>
</body>

</html>
