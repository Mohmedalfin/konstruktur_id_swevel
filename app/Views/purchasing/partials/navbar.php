<?php
$fullName = trim(session()->get('nama_pengguna') ?? session()->get('nama') ?? 'User');
$userRole = ucfirst((string) (session()->get('kategori_akun') ?? session()->get('role') ?? 'Purchasing'));

$words = preg_split('/\\s+/', $fullName);
$wordLimit = 2;
if (count($words) > $wordLimit) {
    $userName = implode(' ', array_slice($words, 0, $wordLimit)) . '...';
} else {
    $userName = $fullName;
}

$firstSegment = service('uri')->getSegment(1);
$isProfilePage = $firstSegment === 'profile';

$accountTriggerClass = $isProfilePage
    ? 'hs-dropdown-toggle w-full h-[72px] p-2 md:w-auto md:px-4 md:justify-center flex items-center gap-3 text-sm bg-white text-slate-800 hover:bg-white/95 focus:outline-hidden focus:bg-white transition-colors duration-200'
    : 'hs-dropdown-toggle w-full h-[72px] p-2 md:w-auto md:px-4 md:justify-center flex items-center gap-3 text-sm text-navbar-foreground hover:bg-navbar-hover focus:outline-hidden focus:bg-navbar-focus transition-colors duration-200';

$accountIconWrapperClass = $isProfilePage
    ? 'w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center'
    : 'w-8 h-8 rounded-full bg-white/20 flex items-center justify-center ring-1 ring-white/30';

$accountIconClass = $isProfilePage
    ? 'text-slate-700 fa-solid fa-user text-sm'
    : 'text-white fa-solid fa-user text-sm';

$accountNameClass = $isProfilePage
    ? 'text-sm font-medium text-slate-900'
    : 'text-sm font-medium text-white';

$accountChevronClass = $isProfilePage
    ? 'text-slate-800 hs-dropdown-open:-rotate-180 duration-300 shrink-0 size-4 ms-auto md:ms-1 group-hover:translate-y-0.5'
    : 'text-white hs-dropdown-open:-rotate-180 duration-300 shrink-0 size-4 ms-auto md:ms-1 group-hover:translate-y-0.5';
?>
<!-- ========== PURCHASING HEADER ========== -->
<header class="flex flex-wrap md:justify-start md:flex-nowrap z-50 bg-navbar border-b border-navbar-line sticky top-0 transition-all duration-200 ease-in-out">
  <nav class="relative max-w-[85rem] w-full mx-auto md:flex md:items-center md:justify-between md:gap-3 px-4 sm:px-6 lg:px-8">
    
    <div class="flex justify-between items-center gap-x-1 h-[72px]">
      <a class="flex-none font-semibold text-xl text-foreground focus:outline-hidden focus:opacity-80" href="<?= base_url('purchasing') ?>" aria-label="Brand">
        <div class="flex items-center gap-2">
          <img src="<?= base_url('assets/images/logoKonstruktor.png') ?>" alt="Kontraktor.id Logo"
            class="h-7 md:h-8 w-auto object-contain">
          <span class="text-white text-lg md:text-md font-semibold font-primary tracking-wide">Kontraktor.id</span>
        </div>
      </a>

      <button type="button"
        class="hs-collapse-toggle md:hidden relative size-9 flex justify-center items-center font-medium text-sm rounded-lg bg-transparent border border-transparent text-white focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none"
        id="hs-header-base-collapse" aria-expanded="false" aria-controls="hs-header-base" aria-label="Toggle navigation"
        data-hs-collapse="#hs-header-base">
        <svg id="nav-icon-hamburger" class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>
        <svg id="nav-icon-close" class="size-4 hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        <span class="sr-only">Toggle navigation</span>
      </button>
    </div>

    <div id="hs-header-base"
      class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow md:block"
      aria-labelledby="hs-header-base-collapse">
      <div class="overflow-hidden overflow-y-auto max-h-[75vh] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-none [&::-webkit-scrollbar-track]:bg-scrollbar-track [&::-webkit-scrollbar-thumb]:bg-scrollbar-thumb">
        <div class="py-2 md:py-0 flex flex-col md:flex-row md:items-stretch gap-0.5 md:gap-0">
          <div class="grow">
            <div class="flex flex-col md:flex-row md:justify-end md:items-stretch gap-0.5 md:gap-0">

              <!-- Menu Navigasi Purchasing -->
              <a class="<?= get_nav_class('purchasing/dashboard') ?>" href="<?= base_url('purchasing/dashboard') ?>"
                <?= is_nav_active('purchasing/dashboard') ? 'aria-current="page"' : '' ?>>
                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" /><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" /></svg>
                Dashboard
              </a>

              <a class="<?= get_nav_class('purchasing/purchase-request') ?>" href="<?= base_url('purchasing/purchase-request') ?>"
                <?= is_nav_active('purchasing/purchase-request') ? 'aria-current="page"' : '' ?>>
                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Request
              </a>

              <a class="<?= get_nav_class('purchasing/po-tracking') ?>" href="<?= base_url('purchasing/po-tracking') ?>"
                <?= is_nav_active('purchasing/po-tracking') ? 'aria-current="page"' : '' ?>>
                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                PO Tracking
              </a>

              <a class="<?= get_nav_class('purchasing/master-data') ?>" href="<?= base_url('purchasing/master-data') ?>"
                <?= is_nav_active('purchasing/master-data') ? 'aria-current="page"' : '' ?>>
                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                Master Data
              </a>

              <!-- Dropdown Notifikasi -->
              <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] md:[--adaptive:adaptive] [--is-collapse:true] md:[--is-collapse:false]">
                  <button id="hs-header-notification-dropdown" type="button"
                      class="hs-dropdown-toggle relative w-full h-[72px] p-2 md:w-auto md:px-4 md:justify-center flex items-center gap-3 text-sm text-navbar-foreground hover:bg-navbar-hover focus:outline-none focus:bg-navbar-focus transition-colors duration-200"
                      aria-haspopup="menu" aria-expanded="false" aria-label="Notifikasi">
                      <div class="shrink-0 relative">
                          <i class="fa-regular fa-bell text-white text-[1.1rem]"></i>
                          <span class="notif-badge-count absolute top-0 right-0 inline-flex items-center justify-center w-3.5 h-3.5 text-[9px] font-bold text-white bg-red-500 border border-primary rounded-full -mt-1 -mr-1.5 hidden">0</span>
                      </div>
                      <span class="md:hidden">Notifikasi</span>
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

              <!-- Dropdown (User Profile) -->
              <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] md:[--adaptive:adaptive] [--is-collapse:true] md:[--is-collapse:false]">
                  <button id="hs-header-base-dropdown" type="button"
                      class="<?= esc($accountTriggerClass) ?>"
                      aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">

                      <div class="<?= esc($accountIconWrapperClass) ?> shrink-0">
                          <i class="<?= esc($accountIconClass) ?>"></i>
                      </div>

                      <div class="leading-tight text-left">
                          <div class="<?= esc($accountNameClass) ?>" title="<?= esc($fullName) ?>"><?= esc($userName) ?></div>
                          <div class="text-xs text-secondary opacity-80"><?= esc($userRole) ?></div>
                      </div>

                      <svg class="<?= esc($accountChevronClass) ?>" 
                          xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="m6 9 6 6 6-6"/>
                      </svg>
                  </button>

                  <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 relative w-full md:w-52 hidden z-10 top-full ps-7 md:ps-0 md:bg-white md:border md:border-gray-200 md:shadow-md before:absolute before:-top-4 before:start-0 before:w-full before:h-5 md:after:hidden after:absolute after:top-1 after:start-4.5 after:h-[calc(100%-4px)] after:border-s after:border-white/20"
                      role="menu" aria-orientation="vertical" aria-labelledby="hs-header-base-dropdown">
                      <div class="py-1 md:px-1 space-y-0.5">
                          <a class="p-2 md:px-3 flex items-center text-sm text-slate-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 rounded-lg gap-3" href="<?= base_url('profile') ?>">
                              <i class="fa-regular fa-id-badge w-4"></i>
                              Profile
                          </a>
                          <a class="p-2 md:px-3 flex items-center text-sm text-red-600 hover:bg-red-50 focus:outline-none focus:bg-red-50 rounded-lg gap-3" href="<?= base_url('logout') ?>">
                              <i class="fa-solid fa-right-from-bracket w-4"></i>
                              Logout
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
<script src="<?= base_url('assets/js/partials/header.js') ?>" defer></script>
<script src="<?= base_url('js/shared/notification-poll.js') ?>" defer></script>
