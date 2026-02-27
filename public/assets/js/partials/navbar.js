/**
 * navbar.js
 * Highlights the active navigation link based on the current URL.
 * Works as a progressive enhancement alongside the PHP active-state logic.
 */
(function () {
    const currentPath = window.location.pathname.replace(/\/$/, '');

    const navLinks = document.querySelectorAll('header nav a[data-nav-path]');

    const activeClasses = ['bg-white', 'text-gray-900', 'font-semibold'];
    const inactiveClasses = ['text-navbar-nav-foreground', 'hover:bg-navbar-nav-hover', 'focus:bg-navbar-nav-focus'];

    navLinks.forEach(function (link) {
        const navPath = '/' + link.dataset.navPath.replace(/^\//, '');
        const isRoot = navPath === '/' || navPath === '';

        let isActive = false;
        if (isRoot) {
            isActive = currentPath === '' || currentPath === '/';
        } else {
            isActive = currentPath === navPath || currentPath.startsWith(navPath + '/');
        }

        if (isActive) {
            link.setAttribute('aria-current', 'page');
            activeClasses.forEach(function (cls) { link.classList.add(cls); });
            inactiveClasses.forEach(function (cls) { link.classList.remove(cls); });
        } else {
            link.removeAttribute('aria-current');
            inactiveClasses.forEach(function (cls) { link.classList.add(cls); });
            activeClasses.forEach(function (cls) { link.classList.remove(cls); });
        }
    });

    /* ── Hamburger ↔ X icon swap ───────────────────────────────── */
    const toggleBtn = document.getElementById('hs-header-base-collapse');
    if (toggleBtn) {
        const iconHamburger = document.getElementById('nav-icon-hamburger');
        const iconClose = document.getElementById('nav-icon-close');

        toggleBtn.addEventListener('click', function () {
            const isCurrentlyOpen = toggleBtn.getAttribute('aria-expanded') === 'true';
            if (iconHamburger) iconHamburger.classList.toggle('hidden', !isCurrentlyOpen);
            if (iconClose) iconClose.classList.toggle('hidden', isCurrentlyOpen);
        });
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