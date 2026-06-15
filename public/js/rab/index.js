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
    kategoriModalConfirm,
    kategoriManualAdd,
    kategoriManualInput,
    resetDataBtn
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

import { initImport, refreshImportCategories } from './components/import.js';
import { initTemplate } from './components/template.js';
import { bindSearch } from './hooks/search.js';
import { bindFormatPenomoran } from './components/format.js';
import { toast } from '../shared/ui/toast.js';
import { confirmAction } from '../shared/ui/confirm.js';

function applySourcePermission(data) {
    if (!tambahKategoriBtn) return;
    const isEditable = (data?.sumber_data || 'manual') === 'manual';
    tambahKategoriBtn.classList.toggle('hidden', !isEditable);

    const mobKategoriBtn = document.getElementById('mobile-tambah-kategori-btn');
    if (mobKategoriBtn) {
        mobKategoriBtn.classList.toggle('hidden', !isEditable);
    }
}

function initMobileActionMenu() {
    const mobileBtn = document.getElementById('mobileActionBtn');
    const mobileMenu = document.getElementById('mobileActionMenu');

    if (!mobileBtn || !mobileMenu) return;

    mobileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        mobileMenu.classList.toggle('hidden');
    });

    mobileMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    document.addEventListener('click', () => {
        mobileMenu.classList.add('hidden');
    });

    // Delegasikan klik ke button utama desktop agar logic tidak perlu diduplikasi
    const bindings = {
        'mobile-tambah-kategori-btn': 'tambah-kategori-btn',
        'mobile-format-penomoran-btn': 'format-penomoran-btn',
        'mobile-boq-import-btn': 'boq-import-btn',
        'mobile-reset-rap-btn': 'reset-rap-btn'
    };

    Object.entries(bindings).forEach(([mobId, deskId]) => {
        const mobEl = document.getElementById(mobId);
        const deskEl = document.getElementById(deskId);
        if (mobEl && deskEl) {
            mobEl.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                mobileMenu.classList.add('hidden');
                deskEl.click();
            });
        }
    });
}

if (!wrapper || !tbody) {
    // not RAP page
} else {
    bindSearch();
    initTemplate();
    initImport();
    bindFormatPenomoran();
    initMobileActionMenu();

    if (resetDataBtn) {
        resetDataBtn.addEventListener('click', async function() {
            const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
            if (!idProject) return;

            const ok = await confirmAction(
                'Kosongkan Seluruh RAP?',
                'Semua data kategori, pekerjaan, dan rincian AHS dalam proyek ini akan <strong>dihapus permanen</strong>. Tindakan ini tidak dapat dibatalkan.',
                'Ya, Kosongkan'
            );

            if (!ok) return;

            try {
                resetDataBtn.disabled = true;
                const originalHtml = resetDataBtn.innerHTML;
                resetDataBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

                const res = await fetch(`/api/rap/reset/${idProject}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                const json = await res.json();
                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal reset data');
                }

                toast.show('Data RAP berhasil dikosongkan!', 'success');
                
                // Refresh data
                renderLoading();
                const freshData = await fetchRabData(idProject);
                
                state.activeCategories = (freshData.categories || []).map(cat => ({
                    id: String(cat.id),
                    nama: cat.name
                }));
                state.format_penomoran = freshData.format_penomoran || null;

                applySourcePermission(freshData);
                renderReadonly(freshData);

                resetDataBtn.disabled = false;
                resetDataBtn.innerHTML = originalHtml;
            } catch (err) {
                console.error('Reset error:', err);
                toast.show(err.message || 'Gagal mengosongkan data', 'error');
                resetDataBtn.disabled = false;
            }
        });
    }

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
                state.format_penomoran = data.format_penomoran || null;

                applySourcePermission(data);
                renderReadonly(data);

                toast.show('Kategori berhasil ditambahkan ke RAB!', 'success');

                kategoriModalConfirm.disabled = false;
                kategoriModalConfirm.textContent = oldText;
            } catch (err) {
                console.error('Gagal tambah kategori:', err);
                kategoriModalConfirm.disabled = false;
                kategoriModalConfirm.textContent = oldText;
                toast.show(err.message || 'Terjadi kesalahan saat menambahkan kategori', 'error');
            }
        });
    }

    if (kategoriManualAdd && kategoriManualInput) {
        kategoriManualAdd.addEventListener('click', async function (e) {
            e.preventDefault();
            const val = kategoriManualInput.value.trim();
            if (!val) {
                alert('Silakan ketik nama kategori baru terlebih dahulu.');
                return;
            }

            const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
            if (!idProject) {
                alert('ID project tidak ditemukan.');
                return;
            }

            const oldText = kategoriManualAdd.textContent || 'Tambah';
            kategoriManualAdd.disabled = true;
            kategoriManualAdd.textContent = '...';

            try {
                const res = await fetch(window.RAB_INIT.apiKategoriUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_project: Number(idProject),
                        kategori: [ { nama: val } ],
                        is_master_only: true
                    })
                });

                const json = await res.json();
                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal menyimpan kategori');
                }

                kategoriManualInput.value = '';
                await openKategoriModal();
                refreshImportCategories(); 

                toast.show('Kategori custom berhasil ditambahkan ke daftar!', 'success');

            } catch (err) {
                console.error('Gagal tambah kategori manual:', err);
                toast.show(err.message || 'Terjadi kesalahan', 'error');
            } finally {
                kategoriManualAdd.disabled = false;
                kategoriManualAdd.textContent = oldText;
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
            state.format_penomoran = data.format_penomoran || null;
            state.sumber_data = data.sumber_data || 'manual';
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
            state.sumber_data = 'manual';

            cards.forEach(c => c.classList.remove('ring-2', 'ring-primary'));
            setEditableMode(true);
            showTable();
            renderEditable();
        });
    }

    window.addEventListener('rabDataImported', async function (e) {
        const importedRows = e.detail || [];
        if (importedRows.length === 0) return;

        const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
        if (!idProject) {
            toast.show('ID project tidak ditemukan', 'error');
            return;
        }

        const groups = {};
        importedRows.forEach(row => {
            if (!row.kategori || row.type === 'header') return;
            if (!groups[row.kategori]) groups[row.kategori] = [];
            groups[row.kategori].push(row);
        });

        const kategoriIds = Object.keys(groups);
        if (kategoriIds.length === 0) {
            toast.show('Tidak ada item valid yang bisa diimpor', 'error');
            return;
        }

        try {
            renderLoading();
            let totalSaved = 0;

            for (const katId of kategoriIds) {
                const items = groups[katId];

                const pekerjaan = items.map(item => ({
                    nama:       item.uraian   || '',
                    volume:     item.volume   || 1,
                    satuan:     item.satuan   || '',
                    harga_bahan: 0,
                    harga_alat:  0,
                    harga_upah:  0
                }));

                const res = await fetch('/api/rap/pekerjaan', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_project:  Number(idProject),
                        id_kategori: Number(katId),
                        pekerjaan
                    })
                });

                const json = await res.json();
                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal menyimpan pekerjaan');
                }

                totalSaved += items.length;
            }

            const data = await fetchRabData(idProject);
            state.activeCategories = (data.categories || []).map(cat => ({
                id: String(cat.id),
                nama: cat.name
            }));
            state.format_penomoran = data.format_penomoran || null;
            state.sumber_data = data.sumber_data || 'manual';
            applySourcePermission(data);
            renderReadonly(data);

            toast.show(`${totalSaved} pekerjaan berhasil diimpor ke RAP!`, 'success');

        } catch (err) {
            console.error('Gagal import BOQ:', err);
            const data = await fetchRabData(idProject).catch(() => null);
            if (data) renderReadonly(data);
            toast.show(err.message || 'Terjadi kesalahan saat mengimpor', 'error');
        }
    });

    document.addEventListener('DOMContentLoaded', async function () {
        window.manualLoader = true;
        if (window.showLoader) window.showLoader();

        const init = window.RAB_INIT;
        const idProject = init?.idProject || init?.id;

        if (!init || !idProject) {
            if (window.hideLoader) window.hideLoader();
            return;
        }

        try {
            state.mode = 'readonly';
            state.currentId = idProject;
            state.collapsed = {};

            setEditableMode(true);
            showTable();

            const data = await fetchRabData(idProject);

            state.activeCategories = (data.categories || []).map(cat => ({
                id: String(cat.id),
                nama: cat.name
            }));
            state.format_penomoran = data.format_penomoran || null;
            state.sumber_data = data.sumber_data || 'manual';

            fetch('/api/rap/recalculate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_project: Number(idProject) })
            }).then(async () => {
                const freshData = await fetchRabData(idProject);
                state.activeCategories = (freshData.categories || []).map(cat => ({
                    id: String(cat.id),
                    nama: cat.name
                }));
                state.format_penomoran = freshData.format_penomoran || null;
                state.sumber_data = freshData.sumber_data || 'manual';
                applySourcePermission(freshData);
                renderReadonly(freshData);
            }).catch(() => {/* non-blocking */});

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
        } finally {
            if (window.hideLoader) window.hideLoader();
        }
    });
}