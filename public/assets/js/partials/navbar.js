(function () {
    const currentPath = window.location.pathname.replace(/\/$/, '');

    const navLinks = document.querySelectorAll('header nav a[data-nav-path]');

    const activeClasses   = ['bg-white', 'text-gray-900', 'font-semibold'];
    const inactiveClasses = ['text-navbar-foreground', 'hover:bg-navbar-hover', 'focus:bg-navbar-focus'];

    navLinks.forEach(function (link) {
        const navPath = '/' + link.dataset.navPath.replace(/^\//, '');
        const isRoot  = navPath === '/' || navPath === '';

        let isActive = false;
        if (isRoot) {
            isActive = currentPath === '' || currentPath === '/';
        } else if (navPath === '/dashboard') {
            isActive = currentPath.includes('/dashboard')
                || (currentPath.startsWith('/proyek/')
                && !currentPath.includes('/schedule')
                && !currentPath.includes('/realisasi')
                && !currentPath.includes('/permintaan')
                && !currentPath.includes('/gudang-lapangan')
                && !currentPath.includes('/rap'));
        } else if (navPath === '/menu-rap') {
            isActive = currentPath.includes('/rap');
        } else if (navPath === '/schedule') {
            isActive = currentPath === navPath
                || currentPath.endsWith('/schedule');
        } else if (navPath === '/realisasi') {
            isActive = currentPath === navPath
                || currentPath.endsWith('/realisasi')
                || currentPath.includes('/gudang-lapangan');
        } else {
            isActive = currentPath === navPath || currentPath.startsWith(navPath + '/');
        }

        if (isActive) {
            link.setAttribute('aria-current', 'page');
            activeClasses.forEach(function (cls)   { link.classList.add(cls); });
            inactiveClasses.forEach(function (cls) { link.classList.remove(cls); });
        } else {
            link.removeAttribute('aria-current');
            inactiveClasses.forEach(function (cls) { link.classList.add(cls); });
            activeClasses.forEach(function (cls)   { link.classList.remove(cls); });
        }
    });

    // --- Restore last project slug into the Dashboard, RAB, RAP, Schedule & Realisasi nav link ---
    const dashboardLink = document.querySelector('header nav a[data-nav-path="dashboard"]');
    const rabLink = document.querySelector('header nav a[data-nav-path="menu-rap"]');
    const scheduleLink = document.querySelector('header nav a[data-nav-path="schedule"]');
    const realisasiLink = document.querySelector('header nav a[data-nav-path="realisasi"]');
    const lastSlug = localStorage.getItem('lastProjectSlug');
    
    if (lastSlug) {
        const baseOrigin = window.location.origin;
        if (dashboardLink) {
            dashboardLink.href = baseOrigin + '/proyek/' + lastSlug;
        }
        if (rabLink) {
            rabLink.href = baseOrigin + '/proyek/' + lastSlug + '/rap';
        }
        if (scheduleLink) {
            scheduleLink.href = baseOrigin + '/proyek/' + lastSlug + '/schedule';
        }
        if (realisasiLink) {
            realisasiLink.href = baseOrigin + '/proyek/' + lastSlug + '/realisasi';
        }
    }

    // Make dropdown button active if its internal links correspond to the current page
    const dropBtn = document.getElementById('hs-header-base-dropdown');
    if (dropBtn) {
        const profilePaths = ['/profile', '/account', '/settings']; 
        let isDropdownActive = profilePaths.some(path => currentPath.startsWith(path));

        if (isDropdownActive) {
            activeClasses.forEach(function (cls)   { dropBtn.classList.add(cls); });
            inactiveClasses.forEach(function (cls) { dropBtn.classList.remove(cls); });
        } else {
            inactiveClasses.forEach(function (cls) { dropBtn.classList.add(cls); });
            activeClasses.forEach(function (cls)   { dropBtn.classList.remove(cls); });
        }
    }

    /* ── Hamburger ↔ X icon swap ───────────────────────────────── */
    const toggleBtn = document.getElementById('hs-header-base-collapse');
    if (toggleBtn) {
        const iconHamburger = document.getElementById('nav-icon-hamburger');
        const iconClose     = document.getElementById('nav-icon-close');
        const navMenu       = document.getElementById('hs-header-base');

        toggleBtn.addEventListener('click', function () {
            const isOpen = toggleBtn.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                toggleBtn.setAttribute('aria-expanded', 'false');
                if (navMenu)       navMenu.classList.add('hidden');
                if (iconHamburger) iconHamburger.classList.remove('hidden');
                if (iconClose)     iconClose.classList.add('hidden');
            } else {
                toggleBtn.setAttribute('aria-expanded', 'true');
                if (navMenu)       navMenu.classList.remove('hidden');
                if (iconHamburger) iconHamburger.classList.add('hidden');
                if (iconClose)     iconClose.classList.remove('hidden');
            }
        });
    }

    /* ── Dropdown Toggle Logic (Profile & Notifikasi) ─────────── */
    const profileBtnEl  = document.getElementById('hs-header-base-dropdown');
    const profileMenuEl = profileBtnEl ? profileBtnEl.closest('.hs-dropdown')?.querySelector('.hs-dropdown-menu') : null;

    const notifBtnEl  = document.getElementById('hs-header-notification-dropdown');
    const notifMenuEl = notifBtnEl ? notifBtnEl.closest('.hs-dropdown')?.querySelector('.hs-dropdown-menu') : null;

    const profileChevronEl = profileBtnEl ? profileBtnEl.querySelector('svg') : null;

    function toggleNavDropdown(btn, menu, e) {
        if (!btn || !menu) return;
        e.stopPropagation();
        const isOpen = btn.getAttribute('aria-expanded') === 'true';

        if (profileBtnEl && profileBtnEl !== btn) {
            if (profileMenuEl) { profileMenuEl.classList.add('hidden', 'opacity-0'); profileMenuEl.classList.remove('opacity-100'); }
            profileBtnEl.setAttribute('aria-expanded', 'false');
            profileBtnEl.classList.remove('hs-dropdown-open');
            if (profileChevronEl) profileChevronEl.classList.remove('-rotate-180');
        }
        if (notifBtnEl && notifBtnEl !== btn) {
            if (notifMenuEl) { notifMenuEl.classList.add('hidden', 'opacity-0'); notifMenuEl.classList.remove('opacity-100'); }
            notifBtnEl.setAttribute('aria-expanded', 'false');
            notifBtnEl.classList.remove('hs-dropdown-open');
        }

        if (isOpen) {
            menu.classList.add('hidden', 'opacity-0');
            menu.classList.remove('opacity-100');
            btn.setAttribute('aria-expanded', 'false');
            btn.classList.remove('hs-dropdown-open');
            if (btn === profileBtnEl && profileChevronEl) profileChevronEl.classList.remove('-rotate-180');
        } else {
            menu.classList.remove('hidden', 'opacity-0');
            menu.classList.add('opacity-100');
            btn.setAttribute('aria-expanded', 'true');
            btn.classList.add('hs-dropdown-open');
            if (btn === profileBtnEl && profileChevronEl) profileChevronEl.classList.add('-rotate-180');
        }
    }

    if (profileBtnEl) profileBtnEl.addEventListener('click', (e) => toggleNavDropdown(profileBtnEl, profileMenuEl, e));
    if (notifBtnEl)   notifBtnEl.addEventListener('click', (e) => toggleNavDropdown(notifBtnEl, notifMenuEl, e));

    document.addEventListener('click', function () {
        if (profileBtnEl && profileBtnEl.getAttribute('aria-expanded') === 'true') {
            if (profileMenuEl) { profileMenuEl.classList.add('hidden', 'opacity-0'); profileMenuEl.classList.remove('opacity-100'); }
            profileBtnEl.setAttribute('aria-expanded', 'false');
            profileBtnEl.classList.remove('hs-dropdown-open');
            if (profileChevronEl) profileChevronEl.classList.remove('-rotate-180');
        }
        if (notifBtnEl && notifBtnEl.getAttribute('aria-expanded') === 'true') {
            if (notifMenuEl) { notifMenuEl.classList.add('hidden', 'opacity-0'); notifMenuEl.classList.remove('opacity-100'); }
            notifBtnEl.setAttribute('aria-expanded', 'false');
            notifBtnEl.classList.remove('hs-dropdown-open');
        }
    });

    /* ── Floating navbar on scroll ──────────────────────────────── */
    const header = document.querySelector('header');
    if (header) {
        const floatAdd = [
            'top-3',          
            'mx-3',           
            'sm:mx-6',
            'lg:mx-10',
            'rounded-2xl',    
            'shadow-xl',      
            'border',
            'border-white/10',
        ];
        // Classes removed when floating (revert defaults)
        const floatRemove = [
            'top-0',
            'border-b',
            'border-navbar-line',
        ];


        let ticking  = false;
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
})();


/**
 * Toggles the visibility of table sub-rows with smooth CSS transitions.
 */
function toggleAccordion(categoryId) {
    const expandableContents = document.querySelectorAll(`.subrow-${categoryId} .expand-content`);
    if (expandableContents.length === 0) return;

    const isClosed = expandableContents[0].classList.contains('max-h-0');

    expandableContents.forEach(content => {
        if (isClosed) {
            // OPENING
            content.classList.remove('max-h-0', 'opacity-0', 'py-0');
            content.classList.add('max-h-[60px]', 'opacity-100', 'py-1.5', 'md:py-2');
        } else {
            // CLOSING
            content.classList.remove('max-h-[60px]', 'opacity-100', 'py-1.5', 'md:py-2');
            content.classList.add('max-h-0', 'opacity-0', 'py-0');
        }
    });

    const iconElement = document.getElementById(`icon-${categoryId}`);
    const chevronElement = document.getElementById(`chevron-${categoryId}`);

    if (iconElement) {
        if (!isClosed) {
            iconElement.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>`;
        } else {
            iconElement.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>`;
        }
    }

    if (chevronElement) {
        if (!isClosed) {
            chevronElement.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>`;
            chevronElement.classList.add('rotate-0');
            chevronElement.classList.remove('-rotate-180');
        } else {
            chevronElement.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>`;
            chevronElement.classList.add('-rotate-180');
            chevronElement.classList.remove('rotate-0');
        }
    }

    // Update aria-expanded on the category row
    const categoryRow = document.querySelector(`.rab-category[onclick*="${categoryId}"]`);
    if (categoryRow) {
        categoryRow.setAttribute('aria-expanded', isClosed ? 'true' : 'false');
    }
}

