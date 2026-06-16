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

        <!-- Notification Dropdown -->
        <div class="hs-dropdown [--strategy:absolute] [--adaptive:none] flex items-center px-2 relative">
            <button id="hs-purchasing-notification" type="button" class="hs-dropdown-toggle relative flex justify-center items-center h-9 w-9 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors focus:outline-none" aria-haspopup="menu" aria-expanded="false" aria-label="Notifikasi">
                <i class="fa-regular fa-bell text-[1.1rem]"></i>
                <span class="absolute top-1.5 right-1.5 inline-flex items-center justify-center w-2.5 h-2.5 font-bold text-white bg-red-500 border border-[#111827] rounded-full"></span>
            </button>

            <div class="hs-dropdown-menu transition-[opacity,margin] duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 absolute right-0 w-80 hidden z-50 top-full mt-2 bg-white border border-gray-200 shadow-md rounded-xl" role="menu" aria-orientation="vertical" aria-labelledby="hs-purchasing-notification">
                <div class="p-3 border-b border-gray-100 flex justify-between items-center bg-white rounded-t-xl">
                    <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
                    <span class="text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full font-semibold">Baru</span>
                </div>
                <div class="max-h-72 overflow-y-auto bg-white [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
                    <a class="p-3 flex items-start gap-3 hover:bg-gray-50 bg-blue-50/20 border-b border-gray-100 transition-colors" href="<?= base_url('purchasing/notification') ?>">
                        <div class="shrink-0 p-2 bg-purple-100 text-purple-600 rounded-full mt-0.5">
                            <i class="fa-solid fa-cart-shopping w-3.5 h-3.5 text-center flex items-center justify-center"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 font-bold">Pengajuan Purchasing</p>
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">PO-2023-001 telah disetujui oleh Direktur dan siap diproses.</p>
                            <p class="text-[10px] text-[#3b82f6] font-semibold mt-1">1 jam yang lalu</p>
                        </div>
                        <div class="shrink-0 w-2 h-2 bg-[#3b82f6] rounded-full mt-2"></div>
                    </a>
                </div>
                <div class="p-2 border-t border-gray-100 text-center bg-gray-50 rounded-b-xl">
                    <a class="text-xs font-bold text-[#3b82f6] hover:text-[#2563eb] flex items-center justify-center gap-1 transition-colors" href="<?= base_url('purchasing/notification') ?>">
                        Lihat Semua Notifikasi <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="hs-dropdown [--strategy:absolute] [--adaptive:none] flex items-center relative">
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

            <div class="hs-dropdown-menu transition-[opacity,margin] duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 absolute right-0 w-52 hidden z-50 top-full mt-2 bg-white border border-gray-200 shadow-md rounded-xl" role="menu" aria-orientation="vertical" aria-labelledby="hs-purchasing-user">
                <div class="py-2 px-1">
                    <a class="px-3 py-2.5 flex items-center gap-3 text-[13px] font-medium text-gray-700 hover:bg-gray-100 rounded-lg mx-1 transition-colors" href="#">
                        <i class="fa-regular fa-id-badge w-4 text-center text-gray-400"></i> Profile
                    </a>
                    <div class="my-1 border-t border-gray-100"></div>
                    <a class="px-3 py-2.5 flex items-center gap-3 text-[13px] font-medium text-red-600 hover:bg-red-50 rounded-lg mx-1 transition-colors" href="#">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const profileBtn = document.getElementById('hs-purchasing-user');
        const profileMenu = profileBtn ? profileBtn.nextElementSibling : null;

        const notifBtn = document.getElementById('hs-purchasing-notification');
        const notifMenu = notifBtn ? notifBtn.nextElementSibling : null;

        function toggleDropdown(btn, menu, e) {
            if (!btn || !menu) return;
            e.stopPropagation();
            const isOpen = btn.getAttribute('aria-expanded') === 'true';

            // Close other dropdowns
            if (profileBtn && profileBtn !== btn && profileMenu) {
                profileMenu.classList.add('hidden', 'opacity-0'); profileMenu.classList.remove('opacity-100');
                profileBtn.setAttribute('aria-expanded', 'false');
            }
            if (notifBtn && notifBtn !== btn && notifMenu) {
                notifMenu.classList.add('hidden', 'opacity-0'); notifMenu.classList.remove('opacity-100');
                notifBtn.setAttribute('aria-expanded', 'false');
            }

            if (isOpen) {
                menu.classList.add('hidden', 'opacity-0');
                menu.classList.remove('opacity-100');
                btn.setAttribute('aria-expanded', 'false');
            } else {
                menu.classList.remove('hidden', 'opacity-0');
                menu.classList.add('opacity-100');
                btn.setAttribute('aria-expanded', 'true');
            }
        }

        if (profileBtn) profileBtn.addEventListener('click', (e) => toggleDropdown(profileBtn, profileMenu, e));
        if (notifBtn) notifBtn.addEventListener('click', (e) => toggleDropdown(notifBtn, notifMenu, e));

        document.addEventListener('click', function () {
            if (profileBtn && profileBtn.getAttribute('aria-expanded') === 'true') {
                if(profileMenu) { profileMenu.classList.add('hidden', 'opacity-0'); profileMenu.classList.remove('opacity-100'); }
                profileBtn.setAttribute('aria-expanded', 'false');
            }
            if (notifBtn && notifBtn.getAttribute('aria-expanded') === 'true') {
                if(notifMenu) { notifMenu.classList.add('hidden', 'opacity-0'); notifMenu.classList.remove('opacity-100'); }
                notifBtn.setAttribute('aria-expanded', 'false');
            }
        });

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
