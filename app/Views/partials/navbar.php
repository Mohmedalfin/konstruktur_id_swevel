<!-- ========== HEADER ========== -->
<header
  class="flex flex-wrap md:justify-start md:flex-nowrap z-50 bg-navbar border-b border-navbar-line sticky top-0 transition-all duration-500 ease-in-out">
  <nav
    class="relative max-w-[85rem] w-full mx-auto md:flex md:items-center md:justify-between md:gap-3 py-2 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center gap-x-1">
      <a class="flex-none font-semibold text-xl text-foreground focus:outline-hidden focus:opacity-80"
        href="<?= base_url('proyek') ?>" aria-label="Brand">
        <div class="flex items-center gap-2">
          <img src="<?= base_url('assets/images/logoKonstruktor.png') ?>" alt="Kontraktor.id Logo"
            class="h-7 md:h-8 w-auto object-contain">
          <span class="text-white text-lg md:text-md font-semibold font-primary tracking-wide">Kontraktor.id</span>
        </div>
      </a>

      <!-- Collapse Button -->
      <button type="button"
        class="hs-collapse-toggle md:hidden relative size-9 flex justify-center items-center font-medium text-sm rounded-lg bg-transparent border border-transparent text-white focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none"
        id="hs-header-base-collapse" aria-expanded="false" aria-controls="hs-header-base" aria-label="Toggle navigation"
        data-hs-collapse="#hs-header-base">
        <!-- Hamburger icon: visible by default, hidden when menu is open -->
        <svg id="nav-icon-hamburger" class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round">
          <line x1="3" x2="21" y1="6" y2="6" />
          <line x1="3" x2="21" y1="12" y2="12" />
          <line x1="3" x2="21" y1="18" y2="18" />
        </svg>
        <!-- X icon: hidden by default, visible when menu is open -->
        <svg id="nav-icon-close" class="size-4 hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round">
          <path d="M18 6 6 18" />
          <path d="m6 6 12 12" />
        </svg>
        <span class="sr-only">Toggle navigation</span>
      </button>
      <!-- End Collapse Button -->
    </div>

    <!-- Collapse -->
    <div id="hs-header-base"
      class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow md:block "
      aria-labelledby="hs-header-base-collapse">
      <div
        class="overflow-hidden overflow-y-auto max-h-[75vh] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-none [&::-webkit-scrollbar-track]:bg-scrollbar-track [&::-webkit-scrollbar-thumb]:bg-scrollbar-thumb">
        <div class="py-2 md:py-0 flex flex-col md:flex-row md:items-stretch gap-0.5 md:gap-0">
          <div class="grow">
            <div class="flex flex-col md:flex-row md:justify-end md:items-stretch gap-0.5 md:gap-0">
              <a class="<?= get_nav_class('dashboard') ?>" href="<?= base_url('dashboard') ?>" data-nav-path="dashboard"
                <?= is_nav_active('dashboard') ? 'aria-current="page"' : '' ?>>
                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24"
                  height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                  <path
                    d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                </svg>
                Dashboard
              </a>

              <a class="<?= get_nav_class('menu-rap') ?>" href="<?= base_url('proyek') ?>" data-nav-path="menu-rap"
                <?= is_nav_active('menu-rap') ? 'aria-current="page"' : '' ?>>
                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24"
                  height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                  <circle cx="12" cy="7" r="4" />
                </svg>
                RAP
              </a>

              <a class="<?= get_nav_class('schedule') ?>" href="<?= base_url('schedule') ?>" data-nav-path="schedule"
                <?= is_nav_active('schedule') ? 'aria-current="page"' : '' ?>>
                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24"
                  height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 12h.01" />
                  <path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                  <path d="M22 13a18.15 18.15 0 0 1-20 0" />
                  <rect width="20" height="14" x="2" y="6" rx="2" />
                </svg>
                Schedule
              </a>

              <a class="<?= get_nav_class('realisasi') ?>" href="<?= base_url('realisasi') ?>" data-nav-path="realisasi"
                <?= is_nav_active('realisasi') ? 'aria-current="page"' : '' ?>>
                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24"
                  height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 12h.01" />
                  <path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                  <path d="M22 13a18.15 18.15 0 0 1-20 0" />
                  <rect width="20" height="14" x="2" y="6" rx="2" />
                </svg>
                Realisasi
              </a>

              <a class="<?= get_nav_class('monitoring') ?>" href="<?= base_url('monitoring') ?>"
                data-nav-path="monitoring" <?= is_nav_active('monitoring') ? 'aria-current="page"' : '' ?>>
                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24"
                  height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path
                    d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2" />
                  <path d="M18 14h-8" />
                  <path d="M15 18h-5" />
                  <path d="M10 6h8v4h-8V6Z" />
                </svg>
                Monitoring
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Collapse -->
  </nav>
</header>
<!-- ========== END HEADER ========== -->

<script src="<?= base_url('assets/js/navbar.js') ?>"></script>