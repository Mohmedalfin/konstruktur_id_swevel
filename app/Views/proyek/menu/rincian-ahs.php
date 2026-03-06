<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian AHS</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/preloader.css') ?>" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">
    <div id="page-loader" class="fixed inset-0 flex flex-col items-center justify-center bg-white z-50">
        <svg class="loader-container">
            <rect class="loader-boxes"></rect>
        </svg><br>
        <b class="text-orange-500 text-lg">Kontraktor.id</b>
    </div>
    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'Rincian AHS', 'subtitle' => '']); ?>

    <?php echo view('partials/table-ahs', ['wrapperClass' => 'w-full']); ?>


    <script src="<?= base_url('ajax/ajax_ahs.js') ?>"></script>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
    <script src="<?= base_url('assets/js/partials/navbar.js') ?>"></script>
    <script>
        const MIN_LOADER_TIME = 500; // 2000ms = 2 detik (ubah kalau mau lebih lama)

        const startTime = Date.now();

        window.addEventListener("load", function() {
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
</body>

</html>