<?php
// Read GET params passed from dashboard.php links
$rabId   = $idProject ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesuaiakn Urutan Uraian Pekerjaan</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/sweetalert2/sweetalert2.min.css') ?>">
</head>
<body class="bg-gray-50 min-h-screen">
    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'Sesuaikan urutan uraian pekerjaan', 'subtitle' => '']); ?>

    <div class="w-full px-3 sm:px-6 lg:px-8 mt-6 mb-2 flex flex-col sm:flex-row sm:items-center gap-3">
        <a href="<?= base_url('proyek/' . ($slug ?? '')) ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-800 transition-colors focus:outline-hidden focus:ring-2 focus:ring-primary/20 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div class="hidden sm:block w-px h-5 bg-slate-300"></div>
        <nav class="flex items-center text-sm font-medium text-table-subtle">
            <a href="<?= base_url('proyek/' . ($slug ?? '')) ?>" class="hover:text-primary transition-colors focus:outline-none">Menu RAP</a>
            <svg class="w-3 h-3 mx-2 shrink-0 text-table-border" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-table-strong">Atur Urutan Uraian</span>
        </nav>
    </div>

    <?php echo view('partials/table-rab', ['tableVisible' => true, 'isReorderMode' => true, 'idProject' => $idProject ?? null, 'slug' => $slug ?? null]); ?>

    <script>
        window.RAB_INIT = {
            mode: 'reorder', // custom mode for this page
            id: <?= json_encode($idProject) ?>,
            idProject: <?= json_encode($idProject) ?>,
            slug: <?= json_encode($slug) ?>,
            apiRapUrl: <?= json_encode(base_url('api/rap')) ?>,
            isReorderMode: true
        };
    </script>
    <script src="<?= base_url('assets/sweetalert2/sweetalert2.all.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <script type="module" src="<?= base_url('js/rab/api/pekerjaan.js') ?>"></script>
    <script type="module" src="<?= base_url('js/rab/components/render.js') ?>"></script>
    <script type="module" src="<?= base_url('js/rab/core/data.js') ?>"></script>
    <script type="module" src="<?= base_url('js/rab/core/state.js') ?>"></script>
    <script type="module">
        import { fetchRabData } from '<?= base_url('js/rab/core/data.js') ?>';
        import { renderLoading, renderReadonly } from '<?= base_url('js/rab/components/render.js') ?>';
        
        document.addEventListener('DOMContentLoaded', async () => {
            if (window.RAB_INIT && window.RAB_INIT.id) {
                try {
                    renderLoading();
                    const data = await fetchRabData(window.RAB_INIT.id);
                    renderReadonly(data);
                } catch (e) {
                    Swal.fire('Error', e.message || 'Gagal memuat data', 'error');
                    const tbody = document.getElementById('rab-tbody');
                    if(tbody) tbody.innerHTML = `<tr><td colspan="10" class="text-center text-red-500 py-4">Gagal memuat data</td></tr>`;
                }
            }
        });
    </script>
</body>
</html>
