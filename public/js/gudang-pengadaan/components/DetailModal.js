import { api } from '../core/api.js';

export class DetailModal {
    constructor() {
        this.modal = document.getElementById('modal-detail-pengadaan');
        this.overlay = document.getElementById('modal-detail-overlay');
        this.panel = document.getElementById('modal-detail-panel');
        
        this.elPrNumber = document.getElementById('detail-pr-number');
        this.elPrDate = document.getElementById('detail-pr-date');
        this.elKeterangan = document.getElementById('detail-keterangan');
        this.tbody = document.getElementById('detail-items-tbody');
        this.stepperProgress = document.getElementById('stepper-progress');
        this.stepItems = document.querySelectorAll('.step-item');
        this.rejectedAlert = document.getElementById('detail-rejected-alert');
        
        this.initEvents();
    }

    initEvents() {
        document.getElementById('btn-close-detail-modal')?.addEventListener('click', () => this.close());
        document.getElementById('btn-close-detail-footer')?.addEventListener('click', () => this.close());
    }

    async open(prId) {
        this.modal.classList.remove('hidden');
        // Trigger reflow
        void this.modal.offsetWidth;
        
        this.overlay.classList.remove('opacity-0');
        this.overlay.classList.add('opacity-100');
        
        this.panel.classList.remove('opacity-0', 'scale-95');
        this.panel.classList.add('opacity-100', 'scale-100');

        this.setLoadingState();

        try {
            const result = await api.getDetail(prId);
            this.renderData(result.data);
        } catch (error) {
            console.error('Error fetching detail:', error);
            alert('Gagal memuat detail pengadaan.');
            this.close();
        }
    }

    close() {
        this.overlay.classList.remove('opacity-100');
        this.overlay.classList.add('opacity-0');
        
        this.panel.classList.remove('opacity-100', 'scale-100');
        this.panel.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            this.modal.classList.add('hidden');
        }, 300);
    }

    setLoadingState() {
        this.elPrNumber.textContent = 'Memuat...';
        this.elPrDate.textContent = 'Tanggal: -';
        this.elKeterangan.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        this.tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-16 text-center text-slate-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p class="text-sm">Memuat item...</p>
                </td>
            </tr>
        `;
        // Reset stepper
        this.stepperProgress.classList.remove('scale-x-100', 'scale-y-100');
        this.stepperProgress.classList.add('scale-x-0', 'scale-y-0');
        this.stepperProgress.style.height = '0%';
        this.stepperProgress.style.width = '0%';
        
        this.stepItems.forEach(item => {
            const icon = item.querySelector('.step-icon');
            icon.className = 'step-icon w-8 h-8 rounded-full bg-white border-2 border-slate-300 flex items-center justify-center text-slate-400 text-xs font-bold transition-colors shadow-sm';
        });
        
        this.rejectedAlert.classList.add('hidden');
    }

    renderData(data) {
        if (!data) return;

        const req = data.request || data;
        const items = req.items || data.items || [];

        this.elPrNumber.textContent = req.pr_number || '-';
        this.elPrDate.textContent = `Tanggal: ${req.request_date || '-'}`;
        this.elKeterangan.textContent = req.keterangan || 'Tidak ada keterangan.';

        // Render Stepper
        this.updateStepper(req.status);

        // Render Items
        if (items.length === 0) {
            this.tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                        <i class="fas fa-box-open text-2xl mb-2"></i>
                        <p class="text-sm">Tidak ada item dalam pengajuan ini.</p>
                    </td>
                </tr>
            `;
            return;
        }

        this.tbody.innerHTML = items.map((item, index) => `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-4 py-3 text-center text-slate-500 font-medium">${index + 1}</td>
                <td class="px-4 py-3">
                    <div class="font-semibold text-slate-800">${item.nama_barang || '-'}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Kode: ${item.kode_barang || '-'}</div>
                </td>
                <td class="px-4 py-3 text-center font-bold text-indigo-600">
                    ${item.volume} <span class="text-xs font-normal text-slate-500">${item.satuan_kemasan || item.satuan}</span>
                </td>
                <td class="px-4 py-3 text-sm text-slate-600 max-w-[200px] truncate" title="${item.keterangan || ''}">
                    ${item.keterangan || '<span class="italic text-slate-400">Tidak ada</span>'}
                </td>
                <td class="px-4 py-3 text-center">
                    ${this.getItemStatusBadge(item.status)}
                </td>
            </tr>
        `).join('');
    }

    updateStepper(status) {
        // Base mapping status to step level
        const statusMap = {
            'draft': 1,
            'pending': 1,
            'approved': 2,
            'ordered': 3,
            'completed': 4,
            'rejected': 0
        };

        const currentStep = statusMap[status] ?? 1;

        if (status === 'rejected') {
            this.rejectedAlert.classList.remove('hidden');
            // Hide progress line visually if rejected
            this.stepperProgress.style.width = '0%';
            this.stepperProgress.style.height = '0%';
        } else {
            this.rejectedAlert.classList.add('hidden');
            // Calculate progress line percentage based on steps (1 to 4)
            // If step is 1, 0%
            // If step is 2, 33%
            // If step is 3, 66%
            // If step is 4, 100%
            let percentage = 0;
            if (currentStep === 2) percentage = 33;
            if (currentStep === 3) percentage = 66;
            if (currentStep === 4) percentage = 100;
            
            // For desktop it's width, for mobile it's height. Tailwind handles this via CSS classes usually,
            // but we can just set both for simplicity or use inline styles safely.
            const isMobile = window.innerWidth < 640;
            if (isMobile) {
                this.stepperProgress.style.height = `${percentage}%`;
                this.stepperProgress.style.width = '100%';
                this.stepperProgress.classList.remove('scale-y-0');
                this.stepperProgress.classList.add('scale-y-100');
            } else {
                this.stepperProgress.style.width = `${percentage}%`;
                this.stepperProgress.style.height = '100%';
                this.stepperProgress.classList.remove('scale-x-0');
                this.stepperProgress.classList.add('scale-x-100');
            }
        }

        // Update step icons
        this.stepItems.forEach(item => {
            const stepNum = parseInt(item.getAttribute('data-step'));
            const icon = item.querySelector('.step-icon');
            const title = item.querySelector('.step-title');
            
            if (status === 'rejected') {
                if (stepNum === 1) {
                    icon.className = 'step-icon w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors shadow-sm bg-red-100 border-2 border-red-500 text-red-600';
                    title.className = 'text-sm font-bold text-red-700 step-title';
                } else {
                    icon.className = 'step-icon w-8 h-8 rounded-full bg-white border-2 border-slate-200 flex items-center justify-center text-slate-300 text-xs font-bold transition-colors shadow-sm';
                    title.className = 'text-sm font-bold text-slate-400 step-title';
                }
                return;
            }

            if (stepNum < currentStep) {
                // Completed steps
                icon.className = 'step-icon w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors shadow-sm bg-indigo-500 border-2 border-indigo-500 text-white';
                title.className = 'text-sm font-bold text-indigo-700 step-title';
            } else if (stepNum === currentStep) {
                // Current active step
                icon.className = 'step-icon w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors shadow-sm bg-white border-2 border-indigo-500 text-indigo-600 ring-4 ring-indigo-50';
                title.className = 'text-sm font-bold text-indigo-700 step-title';
            } else {
                // Future steps
                icon.className = 'step-icon w-8 h-8 rounded-full bg-white border-2 border-slate-300 flex items-center justify-center text-slate-400 text-xs font-bold transition-colors shadow-sm';
                title.className = 'text-sm font-bold text-slate-500 step-title';
            }
        });
    }

    getItemStatusBadge(status) {
        switch (status) {
            case 'pending':
                return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">Pending</span>';
            case 'ordered':
                return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-200">Di-PO</span>';
            case 'received':
                return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">Diterima</span>';
            default:
                return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">${status}</span>`;
        }
    }
}
