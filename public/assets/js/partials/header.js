/**
 * header.js
 * Handles: user-profile dropdown, hamburger toggle, floating scroll effect.
 * Loaded by partials/header.php.
 */
(function () {

    /* ── User Profile Dropdown ──────────────────────────────────── */
    const dropBtn  = document.getElementById('hs-header-base-dropdown');
    const dropMenu = dropBtn ? dropBtn.closest('.hs-dropdown')?.querySelector('.hs-dropdown-menu') : null;

    if (dropBtn && dropMenu) {
        dropBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = dropBtn.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                dropMenu.classList.add('hidden');
                dropMenu.classList.remove('opacity-100');
                dropMenu.classList.add('opacity-0');
                dropBtn.setAttribute('aria-expanded', 'false');
            } else {
                dropMenu.classList.remove('hidden');
                dropMenu.classList.remove('opacity-0');
                dropMenu.classList.add('opacity-100');
                dropBtn.setAttribute('aria-expanded', 'true');
            }
        });

        // Close when clicking outside
        document.addEventListener('click', function () {
            if (dropBtn.getAttribute('aria-expanded') === 'true') {
                dropMenu.classList.add('hidden', 'opacity-0');
                dropMenu.classList.remove('opacity-100');
                dropBtn.setAttribute('aria-expanded', 'false');
            }
        });
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

    /* ── Floating navbar on scroll ──────────────────────────────── */
    const header = document.querySelector('header');
    if (header) {
        const floatAdd = [
            'mt-3', 'mx-3', 'sm:mx-6', 'lg:mx-10',
            'rounded-2xl', 'shadow-xl', 'border', 'border-white/10',
        ];
        const floatRemove = ['border-b', 'border-navbar-line'];

        let ticking = false, floating = false;

        function applyFloatState() {
            const shouldFloat = window.scrollY > 20;
            if (shouldFloat === floating) return;
            floating = shouldFloat;
            if (shouldFloat) {
                floatAdd.forEach(c    => header.classList.add(c));
                floatRemove.forEach(c => header.classList.remove(c));
            } else {
                floatAdd.forEach(c    => header.classList.remove(c));
                floatRemove.forEach(c => header.classList.add(c));
            }
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () { applyFloatState(); ticking = false; });
                ticking = true;
            }
        }, { passive: true });

        applyFloatState();
    }

})();
