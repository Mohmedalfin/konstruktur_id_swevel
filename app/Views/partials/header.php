<!-- ========== HEADER ========== -->
<header class="flex flex-wrap  md:justify-start md:flex-nowrap z-50 w-full bg-navbar border-b border-navbar-line">
    <nav class="relative max-w-[85rem] w-full mx-auto md:flex md:items-center md:justify-between md:gap-3 py-2 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center gap-x-1">
            <a class="flex-none font-semibold text-xl text-foreground focus:outline-hidden focus:opacity-80" href="#" aria-label="Brand">
                <div class="flex items-center gap-2">
                    <img src="<?= base_url('assets/images/logoKonstruktor.png') ?>" alt="Kontraktor.id Logo"
                        class="h-7 md:h-8 w-auto object-contain">
                    <span class="text-white text-lg md:text-md font-semibold font-primary tracking-wide">Kontraktor.id</span>
                </div>
            </a>

            <!-- Collapse Button -->
            <button type="button" class="hs-collapse-toggle md:hidden relative size-9 flex justify-center items-center font-medium text-sm rounded-lg bg-transparent border border-transparent text-white focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none" id="hs-header-base-collapse" aria-expanded="false" aria-controls="hs-header-base" aria-label="Toggle navigation" data-hs-collapse="#hs-header-base">
                <svg class="hs-collapse-open:hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" x2="21" y1="6" y2="6" />
                    <line x1="3" x2="21" y1="12" y2="12" />
                    <line x1="3" x2="21" y1="18" y2="18" />
                </svg>
                <svg class="hs-collapse-open:block shrink-0 hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
                <span class="sr-only">Toggle navigation</span>
            </button>
            <!-- End Collapse Button -->
        </div>

        <!-- Collapse -->
        <div id="hs-header-base" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow md:block " aria-labelledby="hs-header-base-collapse">
            <div class="overflow-hidden overflow-y-auto max-h-[75vh] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-none [&::-webkit-scrollbar-track]:bg-scrollbar-track [&::-webkit-scrollbar-thumb]:bg-scrollbar-thumb">
                <div class="py-2 md:py-0 flex flex-col md:flex-row md:items-stretch gap-0.5 md:gap-0">
                    <div class="grow">
                        <div class="flex flex-col md:flex-row md:justify-end md:items-stretch gap-0.5 md:gap-0">
                            <a class="px-3 py-2 md:py-0 md:w-28 md:justify-center flex items-center text-sm bg-white text-primary md:rounded-none focus:outline-hidden" href="#" aria-current="page">
                                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                                    <path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                </svg>
                                Dashboard
                            </a>

                            <!-- Dropdown -->
                            <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] md:[--adaptive:adaptive] [--is-collapse:true] md:[--is-collapse:false]">
                                <button id="hs-header-base-dropdown" type="button" class="hs-dropdown-toggle w-full h-14 p-2 md:justify-center flex items-center text-sm text-navbar-nav-foreground hover:bg-navbar-nav-hover focus:outline-hidden focus:bg-navbar-nav-focus gap-3" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                                    <i class="fa-solid fa-user text-xl"></i>

                                    <div class="leading-tight text-left">
                                        <div class="text-sm font-semibold">Alfin Maulana</div>
                                        <div class="text-xs text-white/70">Kontraktor</div>
                                    </div>
                                    <svg class="hs-dropdown-open:-rotate-180 md:hs-dropdown-open:rotate-0 duration-300 shrink-0 size-4 ms-auto md:ms-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </button>

                                <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 relative w-full md:w-52 hidden z-10 top-full ps-7 md:ps-0 md:bg-dropdown md:border md:border-dropdown-line md md:shadow-md before:absolute before:-top-4 before:start-0 before:w-full before:h-5 md:after:hidden after:absolute after:top-1 after:start-4.5 after:h-[calc(100%-4px)] after:border-s after:border-navbar-nav-list-divider" role="menu" aria-orientation="vertical" aria-labelledby="hs-header-base-dropdown">
                                    <div class="py-1 md:px-1 space-y-0.5">
                                        <a class="p-2 md:px-3 flex items-center text-sm text-dropdown-item-foreground hover:bg-dropdown-item-hover focus:outline-hidden focus:bg-dropdown-item-focus gap-3" href="#">
                                            <i class="fa-regular fa-id-badge w-4"></i>
                                            Profile
                                        </a>

                                        <!-- <div class="hs-dropdown [--strategy:static] md:[--strategy:absolute] [--adaptive:none] md:[--trigger:hover] [--is-collapse:true] md:[--is-collapse:false] relative">
                                            <button id="hs-header-base-dropdown-sub" type="button" class="hs-dropdown-toggle w-full flex justify-between items-center text-sm text-dropdown-item-foreground p-2 md:px-3 hover:bg-dropdown-item-hover focus:outline-hidden focus:bg-dropdown-item-focus">
                                                Sub Menu
                                                <svg class="hs-dropdown-open:-rotate-180 md:hs-dropdown-open:-rotate-90 md:-rotate-90 ms-auto shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m6 9 6 6 6-6" />
                                                </svg>
                                            </button>

                                            <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 relative md:w-48 hidden z-10 md:mt-2 md:mx-2.5! md:top-0 md:end-full ps-7 md:ps-0 md:bg-dropdown md:border md:border-dropdown-line md md:shadow-md before:hidden md:before:block before:absolute before:-end-5 before:top-0 before:h-full before:w-5 md:after:hidden after:absolute after:top-1 after:start-4.5 after:h-[calc(100%-4px)] after:border-s after:border-navbar-nav-list-divider" role="menu" aria-orientation="vertical" aria-labelledby="hs-header-base-dropdown-sub">
                                                <div class="p-1 space-y-1">
                                                    <a class="p-2 md:px-3 flex items-center text-sm text-dropdown-item-foreground hover:bg-dropdown-item-hover focus:outline-hidden focus:bg-dropdown-item-focus" href="#">
                                                        About
                                                    </a>

                                                    <a class="p-2 md:px-3 flex items-center text-sm text-dropdown-item-foreground hover:bg-dropdown-item-hover focus:outline-hidden focus:bg-dropdown-item-focus" href="#">
                                                        Downloads
                                                    </a>

                                                    <a class="p-2 md:px-3 flex items-center text-sm text-dropdown-item-foreground hover:bg-dropdown-item-hover focus:outline-hidden focus:bg-dropdown-item-focus" href="#">
                                                        Team Account
                                                    </a>
                                                </div>
                                            </div>
                                        </div> -->

                                        <a class="p-2 md:px-3 flex items-center text-sm text-dropdown-item-foreground hover:bg-dropdown-item-hover focus:outline-hidden focus:bg-dropdown-item-focus gap-3" href="#">
                                            <i class="fa-solid fa-list-check w-4"></i>
                                            Daftar Proyek
                                        </a>

                                        <a class="p-2 md:px-3 flex items-center text-sm text-dropdown-item-foreground hover:bg-dropdown-item-hover focus:outline-hidden focus:bg-dropdown-item-focus gap-3 text-red-600" href="#">
                                            <i class="fa-solid fa-right-from-bracket w-4"></i>
                                            Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- End Dropdown -->
                        </div>
                    </div>

                    <!-- Button Group -->
                    <!-- <div class=" flex flex-wrap items-center gap-x-1.5">
            <a class="py-[7px] px-2.5 inline-flex items-center font-medium text-sm rounded-lg bg-layer border border-layer-line text-layer-foreground shadow-2xs hover:bg-layer-hover disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-layer-focus" href="#">
              Sign in
            </a>
            <a class="py-2 px-2.5 inline-flex items-center font-medium text-sm rounded-lg bg-primary text-primary-foreground hover:bg-primary-hover focus:outline-hidden focus:bg-primary-focus disabled:opacity-50 disabled:pointer-events-none" href="#">
              Get started
            </a>
          </div> -->
                    <!-- End Button Group -->
                </div>
            </div>
        </div>
        <!-- End Collapse -->
    </nav>
</header>
<!-- ========== END HEADER ========== -->