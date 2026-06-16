<?php
$userName = session()->get('nama_pengguna') ?? session()->get('nama') ?? 'Pengguna';
$userRole = session()->get('kategori_akun') ?? session()->get('role') ?? 'Kontraktor';
?>

<!-- ========== HEADER ========== -->
<header
    class="flex flex-wrap md:justify-start md:flex-nowrap z-50 bg-navbar border-b border-navbar-line sticky top-0 transition-all duration-500 ease-in-out">
    <nav
        class="relative max-w-[85rem] w-full mx-auto md:flex md:items-center md:justify-between md:gap-3 py-2 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center gap-x-1">
            <a class="flex-none font-semibold text-xl text-foreground focus:outline-hidden focus:opacity-80" href="#"
                aria-label="Brand">
                <div class="flex items-center gap-2">
                    <img src="<?= base_url('assets/images/logoKonstruktor.png') ?>" alt="Kontraktor.id Logo"
                        class="h-7 md:h-8 w-auto object-contain">
                    <span
                        class="text-white text-lg md:text-md font-semibold font-primary tracking-wide">Kontraktor.id</span>
                </div>
            </a>

            <button type="button"
                class="md:hidden relative size-9 flex justify-center items-center font-medium text-sm rounded-lg bg-transparent border border-transparent text-white focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none"
                id="hs-header-base-collapse" aria-expanded="false" aria-controls="hs-header-base"
                aria-label="Toggle navigation">
                <!-- Hamburger icon -->
                <svg id="nav-icon-hamburger" class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="3" x2="21" y1="6" y2="6" />
                    <line x1="3" x2="21" y1="12" y2="12" />
                    <line x1="3" x2="21" y1="18" y2="18" />
                </svg>
                <!-- X icon -->
                <svg id="nav-icon-close" class="size-4 hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
                <span class="sr-only">Toggle navigation</span>
            </button>
        </div>

        <!-- Collapse -->
        <div id="hs-header-base" class="hidden overflow-hidden md:overflow-visible transition-all duration-300 basis-full grow md:block"
            aria-labelledby="hs-header-base-collapse">
            <div
                class="overflow-hidden md:overflow-visible overflow-y-auto md:overflow-y-visible max-h-[75vh] md:max-h-none [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-none [&::-webkit-scrollbar-track]:bg-scrollbar-track [&::-webkit-scrollbar-thumb]:bg-scrollbar-thumb">
                <div class="py-2 md:py-0 flex flex-col md:flex-row md:items-stretch gap-0.5 md:gap-0">
                    <div class="grow">
                        <div class="flex flex-col md:flex-row md:justify-end md:items-stretch gap-0 md:gap-0">
                            <a class="px-4 h-14 md:py-0 md:w-28 md:justify-center flex items-center gap-3 text-sm <?= is_nav_active('dashboard') ? 'bg-white text-primary font-semibold' : 'text-navbar-foreground hover:bg-navbar-hover focus:bg-navbar-focus' ?> md:rounded-none focus:outline-hidden"
                                href="<?= base_url('dashboard') ?>" <?= is_nav_active('dashboard') ? 'aria-current="page"' : '' ?>>
                                <svg class="shrink-0 size-4 block md:hidden" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                                    <path
                                        d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                </svg>
                                Dashboard
                            </a>

                            <a class="px-4 h-14 md:py-0 md:w-28 md:justify-center flex items-center gap-3 text-sm <?= is_nav_active('proyek') ? 'bg-white text-primary font-semibold' : 'text-navbar-foreground hover:bg-navbar-hover focus:bg-navbar-focus' ?> md:rounded-none focus:outline-hidden"
                                href="<?= base_url('proyek') ?>" <?= is_nav_active('proyek') ? 'aria-current="page"' : '' ?>>
                                Proyek
                            </a>

                            <!-- Notification Dropdown -->
                            <div class="hs-dropdown [--strategy:absolute] [--adaptive:none] flex items-center px-2 relative md:border-l md:border-white/10 md:ml-2 md:pl-4">
                                <button id="hs-header-notification-dropdown" type="button" class="hs-dropdown-toggle relative flex justify-center items-center h-9 w-9 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors focus:outline-none" aria-haspopup="menu" aria-expanded="false" aria-label="Notifikasi">
                                    <i class="fa-regular fa-bell text-[1.1rem]"></i>
                                    <span class="absolute top-1.5 right-1.5 inline-flex items-center justify-center w-2.5 h-2.5 font-bold text-white bg-red-500 border border-navbar rounded-full"></span>
                                </button>

                                <div class="hs-dropdown-menu transition-[opacity,margin] duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 absolute right-0 md:right-auto md:left-auto md:translate-x-[-70%] w-80 hidden z-50 top-full mt-2 bg-white border border-gray-200 shadow-md rounded-xl" role="menu" aria-orientation="vertical" aria-labelledby="hs-header-notification-dropdown">
                                    <div class="p-3 border-b border-gray-100 flex justify-between items-center bg-white rounded-t-xl">
                                        <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
                                        <span class="text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full font-semibold">Baru</span>
                                    </div>
                                    <div class="max-h-72 overflow-y-auto bg-white [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
                                        <a class="p-3 flex items-start gap-3 hover:bg-gray-50 bg-blue-50/20 border-b border-gray-100 transition-colors" href="#">
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
                                        <a class="text-xs font-bold text-[#3b82f6] hover:text-[#2563eb] flex items-center justify-center gap-1 transition-colors" href="#">
                                            Lihat Semua Notifikasi <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- User Dropdown -->
                            <div class="hs-dropdown [--strategy:absolute] [--adaptive:none] flex items-center relative">
                                <button id="hs-header-base-dropdown" type="button" class="hs-dropdown-toggle flex items-center gap-3 px-3 h-10 hover:bg-white/10 rounded-lg transition-colors focus:outline-none" aria-haspopup="menu" aria-expanded="false">
                                    <div class="w-8 h-8 rounded-full bg-gray-700 border border-gray-600 flex items-center justify-center overflow-hidden shrink-0">
                                        <i class="text-gray-300 fa-solid fa-user text-sm"></i>
                                    </div>
                                    <div class="leading-tight text-left hidden sm:block">
                                        <div class="text-[13px] font-semibold text-white"><?= esc($userName) ?></div>
                                        <div class="text-[11px] text-gray-400"><?= esc($userRole) ?></div>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 hs-dropdown-open:rotate-180 transition-transform duration-300 ml-1"></i>
                                </button>

                                <div class="hs-dropdown-menu transition-[opacity,margin] duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 absolute right-0 w-52 hidden z-50 top-full mt-2 bg-white border border-gray-200 shadow-md rounded-xl" role="menu" aria-orientation="vertical" aria-labelledby="hs-header-base-dropdown">
                                    <div class="py-2 px-1">
                                        <a class="px-3 py-2.5 flex items-center gap-3 text-[13px] font-medium text-gray-700 hover:bg-gray-100 rounded-lg mx-1 transition-colors" href="#">
                                            <i class="fa-regular fa-id-badge w-4 text-center text-gray-400"></i> Profile
                                        </a>
                                        <div class="my-1 border-t border-gray-100"></div>
                                        <a class="px-3 py-2.5 flex items-center gap-3 text-[13px] font-medium text-red-600 hover:bg-red-50 rounded-lg mx-1 transition-colors" href="<?= base_url('auth/logout') ?>">
                                            <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
<!-- ========== END HEADER ========== -->

<script src="<?= base_url('assets/js/partials/header.js') ?>"></script>