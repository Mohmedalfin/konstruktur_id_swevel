<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule | Konstruktor.id</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'Schedule', 'subtitle' => '']); ?>

    <!-- Schedule Components -->
    <main class="w-full grow">
        <?php echo view('partials/table-schedule'); ?>
    </main>

    <!-- Preline UI (single, correct path) -->
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <!-- Schedule Module -->
    <script type="module" src="<?= base_url('js/schedule/index.js') ?>"></script>
</body>
</html>