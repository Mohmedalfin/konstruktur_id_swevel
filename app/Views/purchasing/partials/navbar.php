<?php
$userName = session()->get('nama_pengguna') ?? session()->get('nama') ?? 'Pengguna';
$userRole = session()->get('kategori_akun') ?? session()->get('role') ?? 'Purchasing';
$activeNav = $activeNav ?? 'dashboard';
?>

<!-- Spacer to prevent layout shift from fixed header -->
<div class="h-[60px] w-full"></div>

<header id="purchasing-header" class="fixed top-0 inset-x-0 z-50 flex justify-between items-center pr-4 pl-6 h-[60px] border-b border-gray-800 bg-[#111827] transition-all duration-300">
    <!-- Logo -->
    <div class="flex items-center gap-2">
        <img src="<?= base_url('assets/images/logoKonstruktor.png') ?>" alt="Kontraktor.id Logo" class="h-6 w-auto object-contain">
        <span class="text-[#f59e0b] text-lg font-bold font-primary tracking-wide">Kontraktor.id</span>
    </div>
    
    <!-- Nav Links & Utilities -->
    <div class="flex items-stretch h-full gap-2">
        
        <!-- Main Navigation -->
        <div class="flex items-stretch h-full border-r border-gray-800 pr-4 mr-2">
            <a href="<?= base_url('purchasing/dashboard') ?>" class="flex items-center <?= $activeNav === 'dashboard' ? 'px-8 bg-white text-gray-900 text-[13px] font-bold' : 'px-6 text-gray-300 text-[13px] font-semibold hover:text-white transition-colors' ?>">Dashboard</a>
            <a href="<?= base_url('purchasing/purchase-request') ?>" class="flex items-center <?= $activeNav === 'purchase-request' ? 'px-8 bg-white text-gray-900 text-[13px] font-bold' : 'px-6 text-gray-300 text-[13px] font-semibold hover:text-white transition-colors' ?>">Purchase Request</a>
            <a href="<?= base_url('purchasing/po-tracking') ?>" class="flex items-center <?= $activeNav === 'po-tracking' ? 'px-8 bg-white text-gray-900 text-[13px] font-bold' : 'px-6 text-gray-300 text-[13px] font-semibold hover:text-white transition-colors' ?>">PO Tracking</a>
            <a href="<?= base_url('purchasing/master-data') ?>" class="flex items-center <?= $activeNav === 'master-data' ? 'px-8 bg-white text-gray-900 text-[13px] font-bold' : 'px-6 text-gray-300 text-[13px] font-semibold hover:text-white transition-colors' ?>">Master Data</a>
        </div>

        <!-- Dropdown Notifikasi -->
        <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] md:[--adaptive:adaptive] [--is-collapse:true] md:[--is-collapse:false] flex items-center px-2">
            <button id="hs-header-notification-dropdown" type="button"
                class="hs-dropdown-toggle relative flex justify-center items-center h-9 w-9 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors focus:outline-none"
                aria-haspopup="menu" aria-expanded="false" aria-label="Notifikasi">
                <div class="shrink-0 relative">
                    <i class="fa-regular fa-bell text-[1.1rem]"></i>
                    <span class="notif-badge-count absolute top-0 right-0 inline-flex items-center justify-center w-3.5 h-3.5 text-[9px] font-bold text-white bg-red-500 border border-primary rounded-full -mt-1 -mr-1.5 hidden">0</span>
                </div>
            </button>

            <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 relative w-full md:w-80 hidden z-10 top-full ps-7 md:ps-0 md:bg-white md:border md:border-gray-200 md:shadow-md md:rounded-xl before:absolute before:-top-4 before:start-0 before:w-full before:h-5"
                role="menu" aria-orientation="vertical" aria-labelledby="hs-header-notification-dropdown">
                <div class="p-3 border-b border-gray-100 flex justify-between items-center bg-white md:rounded-t-xl">
                    <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
                    <span class="notif-header-count text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full font-semibold">0 Baru</span>
                </div>
                <div class="notif-dropdown-list max-h-72 overflow-y-auto bg-white [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
                    <div class="p-4 text-center text-gray-500">
                        <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-2 text-gray-300"></i>
                        <p class="text-xs">Memuat notifikasi...</p>
                    </div>
                </div>
                <div class="p-2 border-t border-gray-100 text-center bg-gray-50 md:rounded-b-xl">
                    <a class="text-xs font-bold text-primary hover:text-primary/80 flex items-center justify-center gap-1 transition-colors" href="<?= base_url('notifikasi') ?>">
                        Lihat Semua Notifikasi <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] md:[--adaptive:adaptive] [--is-collapse:true] md:[--is-collapse:false] flex items-center">
            <button id="hs-purchasing-user" type="button" class="hs-dropdown-toggle flex items-center gap-3 px-3 h-10 hover:bg-gray-800 rounded-lg transition-colors focus:outline-none" aria-haspopup="menu" aria-expanded="false">
                <div class="w-8 h-8 rounded-full bg-gray-700 border border-gray-600 flex items-center justify-center overflow-hidden shrink-0">
                    <i class="text-gray-300 fa-solid fa-user text-sm"></i>
                </div>
                <div class="leading-tight text-left hidden sm:block">
                    <div class="text-[13px] font-semibold text-white"><?= esc($userName) ?></div>
                    <div class="text-[11px] text-gray-400"><?= esc($userRole) ?></div>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 hs-dropdown-open:rotate-180 transition-transform duration-300 ml-1"></i>
            </button>

            <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 relative w-full md:w-52 hidden z-10 top-full ps-7 md:ps-0 md:bg-white md:border md:border-gray-200 md:shadow-md md:rounded-xl before:absolute before:-top-4 before:start-0 before:w-full before:h-5 md:after:hidden after:absolute after:top-1 after:start-4.5 after:h-[calc(100%-4px)] after:border-s after:border-white/20" role="menu" aria-orientation="vertical" aria-labelledby="hs-purchasing-user">
                <div class="py-2 px-1">
                    <a class="px-3 py-2.5 flex items-center gap-3 text-[13px] font-medium text-gray-700 hover:bg-gray-100 rounded-lg mx-1 transition-colors" href="<?= base_url('profile') ?>">
                        <i class="fa-regular fa-id-badge w-4 text-center text-gray-400"></i> Profile
                    </a>
                    <div class="my-1 border-t border-gray-100"></div>
                    <a class="px-3 py-2.5 flex items-center gap-3 text-[13px] font-medium text-red-600 hover:bg-red-50 rounded-lg mx-1 transition-colors" href="<?= base_url('logout') ?>">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {


        /* ── Floating navbar on scroll ──────────────────────────────── */
        const header = document.getElementById('purchasing-header');
        if (header) {
            const floatAdd = [
                'top-3',          
                'mx-3',           
                'sm:mx-6',
                'lg:mx-10',
                'rounded-2xl',    
                'shadow-xl',      
                'border',
                'border-gray-700',
            ];
            // Classes removed when floating (revert defaults)
            const floatRemove = [
                'top-0',
                'border-b',
                'border-gray-800'
            ];

            let ticking = false;
            let floating = false;

            function applyFloatState() {
                const shouldFloat = window.scrollY > 20;
                if (shouldFloat === floating) return; 
                floating = shouldFloat;

                if (shouldFloat) {
                    header.classList.add(...floatAdd);
                    header.classList.remove(...floatRemove);
                } else {
                    header.classList.remove(...floatAdd);
                    header.classList.add(...floatRemove);
                }
            }

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    requestAnimationFrame(function () {
                        applyFloatState();
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });

            applyFloatState();
        }
    });
</script>
