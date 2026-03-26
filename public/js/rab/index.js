/**
 * rab/index.js
 * Entry point for the RAB feature.
 */

import {
    state,
    wrapper,
    tbody,
    addRabBtn,
    cards,
    tambahKategoriBtn,
    kategoriModalClose,
    kategoriModalCancel,
    kategoriModalList,
    kategoriModalOverlay,
    kategoriModalConfirm
} from './core/state.js';

import { fetchRabData } from './core/data.js';

import {
    renderLoading,
    renderReadonly,
    renderEditable,
    showTable,
    setEditableMode
} from './components/render.js';

import {
    openKategoriModal,
    closeKategoriModal,
    updateModalInfo
} from './components/categories.js';

import { initImport } from './components/import.js';
import { initTemplate } from './components/template.js';
import { bindSearch } from './hooks/search.js';

function applySourcePermission(data) {
    if (!tambahKategoriBtn) return;
    const isEditable = (data?.sumber_data || 'manual') === 'manual';
    tambahKategoriBtn.classList.toggle('hidden', !isEditable);
}

if (!wrapper || !tbody) {
    // not RAP page
} else {
    bindSearch();
    initTemplate();
    initImport();

    if (tambahKategoriBtn) {
        tambahKategoriBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openKategoriModal();
        });
    }

    if (kategoriModalClose) {
        kategoriModalClose.addEventListener('click', closeKategoriModal);
    }

    if (kategoriModalCancel) {
        kategoriModalCancel.addEventListener('click', closeKategoriModal);
    }

    if (kategoriModalList) {
        kategoriModalList.addEventListener('change', updateModalInfo);
    }

    if (kategoriModalOverlay) {
        kategoriModalOverlay.addEventListener('click', function (e) {
            if (e.target === kategoriModalOverlay) closeKategoriModal();
        });
    }

    if (kategoriModalConfirm) {
        kategoriModalConfirm.addEventListener('click', async function () {
            const oldText = kategoriModalConfirm.textContent || 'Tambahkan';

            try {
                if (!kategoriModalList) {
                    closeKategoriModal();
                    return;
                }

                const checked = Array.from(
                    kategoriModalList.querySelectorAll('.kategori-checkbox:not([disabled]):checked')
                );

                if (checked.length === 0) {
                    alert('Pilih minimal 1 kategori.');
                    return;
                }

                const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
                if (!idProject) {
                    alert('ID project tidak ditemukan.');
                    return;
                }

                const kategoriPayload = checked.map(cb => ({
                    nama: cb.dataset.nama
                }));

                kategoriModalConfirm.disabled = true;
                kategoriModalConfirm.textContent = 'Menambahkan...';

                closeKategoriModal();

                const res = await fetch(window.RAB_INIT.apiKategoriUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_project: Number(idProject),
                        kategori: kategoriPayload
                    })
                });

                const json = await res.json();

                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal menyimpan kategori');
                }

                renderLoading();

                const data = await fetchRabData(idProject);

                state.activeCategories = (data.categories || []).map(cat => ({
                    id: String(cat.id),
                    nama: cat.name
                }));

                applySourcePermission(data);
                renderReadonly(data);

                kategoriModalConfirm.disabled = false;
                kategoriModalConfirm.textContent = oldText;
            } catch (err) {
                console.error('Gagal tambah kategori:', err);
                kategoriModalConfirm.disabled = false;
                kategoriModalConfirm.textContent = oldText;
                alert(err.message || 'Terjadi kesalahan saat menambahkan kategori');
            }
        });
    }

    cards.forEach(card => {
        card.addEventListener('click', async function () {
            const id = card.dataset.id;

            state.mode = 'readonly';
            state.currentId = id;
            state.collapsed = {};
            state.activeCategories = [];

            cards.forEach(c => c.classList.remove('ring-2', 'ring-primary'));
            card.classList.add('ring-2', 'ring-primary');

            setEditableMode(false);
            showTable();
            renderLoading();

            const data = await fetchRabData(id);
            applySourcePermission(data);
            renderReadonly(data);
        });
    });

    if (addRabBtn) {
        addRabBtn.addEventListener('click', function () {
            state.mode = 'editable';
            state.currentId = null;
            state.collapsed = {};
            state.activeCategories = [];

            cards.forEach(c => c.classList.remove('ring-2', 'ring-primary'));
            setEditableMode(true);
            showTable();
            renderEditable();
        });
    }

    document.addEventListener('DOMContentLoaded', async function () {
        const init = window.RAB_INIT;
        const idProject = init?.idProject || init?.id;

        if (!init || !idProject) return;

        try {
            state.mode = 'readonly';
            state.currentId = idProject;
            state.collapsed = {};

            setEditableMode(true);
            showTable();
            renderLoading();

            const data = await fetchRabData(idProject);

            state.activeCategories = (data.categories || []).map(cat => ({
                id: String(cat.id),
                nama: cat.name
            }));

            applySourcePermission(data);
            renderReadonly(data);
        } catch (err) {
            console.error('Gagal memuat RAP:', err);
            tbody.innerHTML = `
                <tr>
                    <td colspan="12" class="text-center py-10 text-red-500 text-xs">
                        Gagal memuat data RAP.
                    </td>
                </tr>
            `;
        }
    });
}