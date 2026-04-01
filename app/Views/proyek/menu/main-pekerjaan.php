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
    <?php echo view('partials/topbar', ['title' => 'Tambah Pekerjaan', 'subtitle' => '']); ?>

    <div class="w-full px-3 sm:px-6 lg:px-8 mt-6 mb-2">
        <nav class="flex items-center text-sm font-medium text-table-subtle">
            <button onclick="goBackToRab()" class="hover:text-primary transition-colors focus:outline-none cursor-pointer">Menu RAP</button>
            <svg class="w-3 h-3 mx-2 shrink-0 text-table-border" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-table-strong">Tambah Pekerjaan</span>
        </nav>
    </div>

    <?php echo view('partials/item-ahs'); ?>


    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('assets/js/partials/navbar.js') ?>"></script>
    <script>
        (function () {
            const params = new URLSearchParams(window.location.search);

            const idProject   = params.get('id')           || sessionStorage.getItem('current_id_project') || null;
            const kategoriId  = params.get('kategori_id')  || sessionStorage.getItem('rab_tambah_ahs_cat')  || null;
            const slug        = params.get('slug')          || localStorage.getItem('lastProjectSlug')       || null;

            window.RAB_INIT = { idProject };

            // Persist for submit.js
            if (idProject)  sessionStorage.setItem('current_id_project', idProject);
            if (kategoriId) sessionStorage.setItem('rab_tambah_ahs_cat',  kategoriId);

            // Build the return URL so after submit we land back on the right project
            if (slug) {
                sessionStorage.setItem('rab_return_url', `/proyek/${slug}`);
                localStorage.setItem('lastProjectSlug', slug);
            }

            // Show the kategori name in the context banner
            const label = document.getElementById('tambah-ahs-pekerjaan-label');
            if (label) {
                const nama = params.get('kategori_nama');
                if (nama) label.textContent = decodeURIComponent(nama.replace(/\+/g, ' '));
            }
        })();
    </script>
    <script>
        window.addEventListener('load', function () {
            window.HSStaticMethods?.autoInit();
        });
    </script>
</body>
</html>