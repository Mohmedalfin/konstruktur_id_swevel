import {
    kategoriModalOverlay,
    kategoriModalList,
    kategoriModalConfirm,
    state
} from '../core/state.js';

import { fetchKategoriMaster } from '../core/data.js';

export async function openKategoriModal() {
    if (!kategoriModalOverlay || !kategoriModalList) return;

    kategoriModalOverlay.classList.remove('hidden');
    kategoriModalOverlay.classList.add('flex');

    kategoriModalList.innerHTML = `
        <li class="text-xs text-slate-500 px-2 py-3">Memuat kategori...</li>
    `;

    try {
        const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;

        if (!idProject) {
            throw new Error('ID project tidak ditemukan');
        }

        const categories = await fetchKategoriMaster(idProject);

        if (!categories.length) {
            kategoriModalList.innerHTML = `
                <li class="text-xs text-slate-500 px-2 py-3">Belum ada kategori tersedia.</li>
            `;
            updateModalInfo();
            return;
        }

        const activeIds = (state.activeCategories || []).map(c => String(c.id));

        kategoriModalList.innerHTML = categories.map(cat => {
            const isActive = activeIds.includes(String(cat.id));
            const disabledAttr = isActive ? 'disabled checked' : '';
            const bgClass = isActive ? 'bg-slate-50 opacity-60 cursor-not-allowed border-slate-200' : 'border-slate-200 hover:border-primary hover:bg-primary/5 cursor-pointer';
            const textClass = isActive ? 'text-slate-400' : 'text-slate-700';

            return `
            <li>
                <label class="flex items-center gap-3 p-3 rounded-xl border ${bgClass} transition-all">
                    <input
                        type="checkbox"
                        class="kategori-checkbox w-4 h-4 accent-primary rounded disabled:opacity-50"
                        value="${cat.id}"
                        data-id="${cat.id}"
                        data-nama="${cat.nama}"
                        ${disabledAttr}
                    >
                    <span class="text-sm ${textClass}">${cat.nama}</span>
                    ${isActive ? `<span class="ml-auto text-[10px] bg-emerald-50 text-emerald-600 border border-emerald-200 px-2 py-0.5 rounded-full font-medium">Terpilih</span>` : ''}
                </label>
            </li>
        `}).join('');

        updateModalInfo();
    } catch (err) {
        console.error(err);
        kategoriModalList.innerHTML = `
            <li class="text-xs text-red-500 px-2 py-3">Gagal mengambil kategori master.</li>
        `;
    }
}

export function closeKategoriModal() {
    if (!kategoriModalOverlay) return;
    kategoriModalOverlay.classList.add('hidden');
    kategoriModalOverlay.classList.remove('flex');
}

export function updateModalInfo() {
    if (!kategoriModalList || !kategoriModalConfirm) return;

    const checked = kategoriModalList.querySelectorAll('.kategori-checkbox:checked').length;
    kategoriModalConfirm.disabled = checked === 0;
}

export function appendCategoryRow(cat) {
    // placeholder kalau dipakai file lain
    return cat;
}