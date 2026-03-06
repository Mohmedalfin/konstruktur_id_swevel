<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'App') ?></title>


    <link rel="stylesheet" href="<?= base_url('assets/css/output.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/vanilla-calendar-pro/style/layout.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/vanilla-calendar-pro/style/index.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link href="<?= base_url('assets/css/preloader.css') ?>" rel="stylesheet">

</head>

<body class="bg-gray-50">
    <div id="page-loader" class="fixed inset-0 flex flex-col items-center justify-center bg-white z-50">
        <svg class="loader-container">
            <rect class="loader-boxes"></rect>
        </svg><br>
        <b class="text-orange-500 text-lg">Kontraktor.id</b>
    </div>

    <?= $this->include('partials/header') ?>
    <?php echo view('partials/topbar', ['title' => 'Daftar Project', 'subtitle' => '']); ?>


    <main class="container mx-auto px-4 py-6">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <!-- Vendor JS (WAJIB untuk Advanced Datepicker) -->
    <script src="<?= base_url('assets/vendor/lodash/lodash.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/vanilla-calendar-pro/index.js') ?>"></script>

    <!-- ✅ PRELINE CORE -->
    <script src="<?= base_url('assets/vendor/preline/dist/index.js') ?>"></script>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script>
        const MIN_LOADER_TIME = 500; // 2000ms = 2 detik (ubah kalau mau lebih lama)

        const startTime = Date.now();

        window.addEventListener('load', () => {
            window.HSStaticMethods?.autoInit();
            // biasanya cukup autoInit saja, tapi aman:
            window.HSDatepicker?.autoInit?.();
            const loader = document.getElementById("page-loader");
            const elapsed = Date.now() - startTime;
            const remaining = Math.max(0, MIN_LOADER_TIME - elapsed);

            setTimeout(() => {
                loader.classList.add("hidden");

                setTimeout(() => {
                    loader.remove();
                }, 400); // sesuai transition CSS
            }, remaining);
        });
    </script>
    <!-- PRELINE JS (wajib) -->

    <?= $this->renderSection('scripts') ?>

</body>

</html>