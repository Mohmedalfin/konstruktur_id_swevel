<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= esc($title ?? 'Purchasing') ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/css/output.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <?= $this->renderSection('styles') ?>
    <style>
        body { background-color: #f8fafc; }
        .hs-overlay-backdrop {
            background-color: rgba(15, 23, 42, 0.5) !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
        }
        .nav-item {
            color: #d1d5db;
            font-size: 13px;
            font-weight: 600;
            padding: 0 24px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .nav-item:hover { color: white; }
        .nav-active {
            background-color: white;
            color: #111827;
            font-weight: bold;
            font-size: 13px;
            padding: 0 24px;
            display: flex;
            align-items: center;
        }
        
        .badge-pending {
            background-color: #f3f4f6; /* gray-100 */
            color: #4b5563; /* gray-600 */
            border-radius: 4px;
            padding: 2px 12px;
            font-size: 11px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-pending::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #9ca3af;
            border-radius: 50%;
        }
        .badge-parsial {
            background-color: #fef08a; /* yellow-200 */
            color: #854d0e; /* yellow-800 */
            border-radius: 4px;
            padding: 2px 12px;
            font-size: 11px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-parsial::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #eab308;
            border-radius: 50%;
        }
        .badge-diproses {
            background-color: #eff6ff; /* blue-50 */
            color: #2563eb; /* blue-600 */
            border-radius: 4px;
            padding: 2px 12px;
            font-size: 11px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-diproses::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #3b82f6;
            border-radius: 50%;
        }
        .badge-selesai {
            background-color: #bbf7d0; /* green-200 */
            color: #166534; /* green-800 */
            border-radius: 4px;
            padding: 2px 12px;
            font-size: 11px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-selesai::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #22c55e;
            border-radius: 50%;
        }
        .badge-ditolak {
            background-color: #fecdd3; /* rose-200 */
            color: #9f1239; /* rose-800 */
            border-radius: 4px;
            padding: 2px 12px;
            font-size: 11px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-ditolak::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #e11d48;
            border-radius: 50%;
        }
    </style>
</head>

<body class="font-sans antialiased text-sm text-slate-800">

    <?= $this->include('partials/global-loader') ?>
    
    <div class="bg-[#111827] w-full shadow-md z-50 relative">
        <?= view('purchasing/partials/navbar', ['activeNav' => $activeNav ?? '']) ?>
        <?= view('partials/topbar', ['title' => $title ?? 'Purchasing']) ?>
    </div>

    <main class="w-full max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-12 space-y-6">
        <?= $this->renderSection('content') ?>
    </main>

    <script type="module" src="<?= base_url('js/shared/ui/global-loader.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script src="<?= base_url('js/shared/notification-poll.js') ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.addEventListener('load', () => {
            window.HSStaticMethods?.autoInit();
        });
    </script>

    <?= $this->renderSection('scripts') ?>

</body>
</html>
