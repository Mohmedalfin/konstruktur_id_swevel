import {
    kategoriModalOverlay,
    kategoriModalList,
    kategoriModalConfirm
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

        kategoriModalList.innerHTML = categories.map(cat => `
            <li>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-primary hover:bg-primary/5 cursor-pointer transition-all">
                    <input
                        type="checkbox"
                        class="kategori-checkbox w-4 h-4 accent-primary rounded"
                        value="${cat.id}"
                        data-id="${cat.id}"
                        data-nama="${cat.nama}"
                    >
                    <span class="text-sm text-slate-700">${cat.nama}</span>
                </label>
            </li>
        `).join('');

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