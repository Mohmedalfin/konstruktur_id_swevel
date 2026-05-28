/**
 * header.js
 * Handles: user-profile dropdown, hamburger toggle, floating scroll effect.
 * Loaded by partials/header.php.
 */
(function () {

    /* ── Dropdowns (User Profile & Notifications) ──────────────────────────────────── */
    const profileBtn  = document.getElementById('hs-header-base-dropdown');
    const profileMenu = profileBtn ? profileBtn.closest('.hs-dropdown')?.querySelector('.hs-dropdown-menu') : null;

    const notifBtn  = document.getElementById('hs-header-notification-dropdown');
    const notifMenu = notifBtn ? notifBtn.closest('.hs-dropdown')?.querySelector('.hs-dropdown-menu') : null;

    function toggleDropdown(btn, menu, e) {
        if (!btn || !menu) return;
        e.stopPropagation();
        const isOpen = btn.getAttribute('aria-expanded') === 'true';

        // Close other dropdowns
        if (profileBtn && profileBtn !== btn) {
            if(profileMenu) { profileMenu.classList.add('hidden', 'opacity-0'); profileMenu.classList.remove('opacity-100'); }
            profileBtn.setAttribute('aria-expanded', 'false');
        }
        if (notifBtn && notifBtn !== btn) {
            if(notifMenu) { notifMenu.classList.add('hidden', 'opacity-0'); notifMenu.classList.remove('opacity-100'); }
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
            'top-3',          
            'mx-3',           
            'sm:mx-6',
            'lg:mx-10',
            'rounded-2xl',    
            'shadow-xl',      
            'border',
            'border-white/10',
        ];
        const floatRemove = ['top-0', 'border-b', 'border-navbar-line'];

        let ticking = false, floating = false;

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
                requestAnimationFrame(function () { applyFloatState(); ticking = false; });
                ticking = true;
            }
        }, { passive: true });

        applyFloatState();
    }
})();
