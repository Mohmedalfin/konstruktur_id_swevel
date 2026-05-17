<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule</title>
    <link rel="preload" href="<?= base_url('assets/css/output.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'Schedule', 'subtitle' => '']); ?>

    <main class="w-full grow">
        <?php echo view('partials/table-schedule'); ?>
    </main>

    <script>
        window.SCHEDULE_INIT = {
            idProject: <?= isset($idProject) ? json_encode($idProject) : 'null' ?>,
            slug: <?= isset($slug) ? json_encode($slug) : 'null' ?>,
            apiScheduleDataUrl: <?= json_encode(base_url('api/schedule/data')) ?>
        };

        if (window.SCHEDULE_INIT.slug) {
            localStorage.setItem('lastProjectSlug', window.SCHEDULE_INIT.slug);
        }
        window.manualLoader = true;
    </script>

    <script src="<?= base_url('assets/js/preline.js') ?>" defer></script>
    <script src="<?= base_url('assets/js/vendor/chart.min.js') ?>"></script>
    <script type="module" src="<?= base_url('js/schedule/index.js') ?>"></script>
</body>
</html>