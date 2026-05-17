<?php
$idProject = $idProject ?? null;
$slug = $slug ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Dashboard</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> 
</head>
<body class="bg-slate-50/50 min-h-screen text-slate-800 font-sans">
    <script>window.manualLoader = true;</script>
    <?php echo view('partials/navbar'); ?>
    <?php echo view('partials/topbar', ['title' => 'DASHBOARD', 'subtitle' => 'Project Overview & Analytics']); ?>

    <main class="w-full px-4 sm:px-6 lg:px-8 py-6 lg:py-8 max-w-[1600px] mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6 lg:mb-8">
            <div class="animate-in fade-in slide-in-from-left-4 duration-500">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Project Overview</span>
                </div>
                <h1 class="text-lg lg:text-2xl font-black text-slate-800 tracking-tight" id="dash-project-name">
                    <?= esc($project_name ?? '') ?>
                </h1>
            </div>
            
            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-slate-100 shadow-sm text-xs font-semibold text-slate-500">
                <i class="fas fa-clock text-slate-400"></i>
                <span id="dash-current-time">Memuat waktu...</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5 mb-4 lg:mb-5">
            <div class="h-[380px]">
                <?php echo view('partials/dashboard-overview-cards'); ?>
            </div>
            <div class="h-[380px]">
                <?php echo view('partials/dashboard-charts', ['type' => 'progress_only']); ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5 mb-4 lg:mb-5">
            <div class="h-[380px]">
                <?php echo view('partials/dashboard-work-sumary'); ?>
            </div>
            <div class="h-[380px]">
                <?php echo view('partials/dashboard-charts', ['type' => 'cost_only']); ?>
            </div>
        </div>

        <div class="w-full">
            <?php echo view('partials/dashboard-status-cards'); ?>
        </div>

    </main>

    <?php echo view('partials/dashboard-category-detail'); ?>

    <script>
        window.DASHBOARD_INIT = {
            idProject: <?= json_encode($idProject) ?>,
            slug: <?= json_encode($slug ?? '') ?>
        };
        if (window.DASHBOARD_INIT.slug) {
            localStorage.setItem('lastProjectSlug', window.DASHBOARD_INIT.slug);
        }

        setInterval(() => {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('dash-current-time').textContent = now.toLocaleDateString('id-ID', options) + ' WIB';
        }, 1000);
    </script>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('node_modules/preline/dist/preline.js') ?>"></script>
    <script src="<?= base_url('assets/js/partials/navbar.js') ?>"></script>
    <script type="module" src="<?= base_url('js/dashboard/index.js') ?>"></script>
</body>
</html>