class ToastManager {
    constructor() {
        this.containerId = 'global-toast-container';
        this.initContainer();
    }

    initContainer() {
        if (document.getElementById(this.containerId)) return;
        
        const container = document.createElement('div');
        container.id = this.containerId;
        container.className = 'fixed top-4 left-1/2 -translate-x-1/2 sm:top-6 sm:left-auto sm:right-4 sm:translate-x-0 z-[9999] flex flex-col gap-2 p-3 sm:p-4 pointer-events-none w-[calc(100%-2rem)] sm:w-full max-w-sm';
        document.body.appendChild(container);
    }

    show(message, type = 'success', duration = 3000) {
        this.initContainer();
        const container = document.getElementById(this.containerId);

        const styles = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
            error:   'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-amber-50 border-amber-200 text-amber-800',
            info:    'bg-blue-50 border-blue-200 text-blue-800'
        };

        const icons = {
            success: `<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`,
            error:   `<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>`,
            warning: `<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
            info:    `<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
        };

        const cls  = styles[type] || styles.info;
        const icon = icons[type]  || icons.info;

        const toast = document.createElement('div');
        toast.className = `flex flex-row items-center gap-3 p-4 rounded-xl shadow-lg border ${cls} pointer-events-auto transition-all duration-300 transform translate-x-full opacity-0`;
        
        toast.innerHTML = `
            <div class="shrink-0">${icon}</div>
            <div class="flex-1 text-sm font-medium">${message}</div>
            <button class="shrink-0 p-1 rounded-lg hover:bg-black/5 transition-colors focus:outline-none focus:ring-2 focus:ring-black/10 toast-close-btn">
                <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        `;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            });
        });

        const closeBtn = toast.querySelector('.toast-close-btn');
        const dismiss = () => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300); 
        };
        closeBtn.addEventListener('click', dismiss);

        if (duration > 0) {
            setTimeout(dismiss, duration);
        }
    }
}

export const toast = new ToastManager();

window.Toast = toast;
