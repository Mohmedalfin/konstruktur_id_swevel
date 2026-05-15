export const GlobalLoader = {
    show: function() {
        const loader = document.getElementById('global-page-loader');
        if (loader) {
            loader.classList.remove('opacity-0', 'pointer-events-none');
            window._loaderStartTime = Date.now();
        }
    },

    hide: function() {
        const loader = document.getElementById('global-page-loader');
        if (!loader) return;

        const elapsedTime = window._loaderStartTime ? Date.now() - window._loaderStartTime : 0;
        const minDuration = 400;
        const delay = Math.max(0, minDuration - elapsedTime);

        setTimeout(() => {
            loader.classList.add('opacity-0');
            setTimeout(() => {
                loader.classList.add('pointer-events-none');
            }, 500);

        }, delay);
    },

    isActive: function() {
        const loader = document.getElementById('global-page-loader');
        return loader ? !loader.classList.contains('opacity-0') : false;
    }
};

window.GlobalLoader = GlobalLoader;
window.showLoader = GlobalLoader.show;
window.hideLoader = GlobalLoader.hide;
window.isLoaderActive = GlobalLoader.isActive;

function attemptAutoHide() {
    if (!window.manualLoader) {
        GlobalLoader.hide();
    }
}

document.addEventListener('click', function (e) {
    const link = e.target.closest('a[href]');
    if (!link) return;

    const href = link.getAttribute('href');
    if (!href) return;

    const isExternal = link.target === '_blank' || link.hostname !== window.location.hostname;
    const isSpecial  = href.startsWith('#') || href.startsWith('javascript:')
                    || href.startsWith('mailto:') || href.startsWith('tel:');
    const isDownload = link.hasAttribute('download');
    const isModifier = e.ctrlKey || e.metaKey || e.shiftKey || e.altKey;

    if (isExternal || isSpecial || isDownload || isModifier) return;

    GlobalLoader.show();
});

if (document.readyState === 'complete') {
    attemptAutoHide();
} else {
    window.addEventListener('load', attemptAutoHide);
}