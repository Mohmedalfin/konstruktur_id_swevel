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

    <div class="w-full px-3 sm:px-6 lg:px-8 mt-6 mb-2 flex flex-col sm:flex-row sm:items-center gap-3">
        <button onclick="goBackToRab()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-800 transition-colors focus:outline-hidden focus:ring-2 focus:ring-primary/20 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </button>
        <div class="hidden sm:block w-px h-5 bg-slate-300"></div>
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

            const idProject   = params.get('id_project')   || sessionStorage.getItem('current_id_project') || null;
            const kategoriId  = params.get('id_kategori')  || sessionStorage.getItem('rab_tambah_ahs_cat')  || null;
            const idParent    = params.get('id_parent')    || null;
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