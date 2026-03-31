/**
 * tambah-ahs/hooks/submit.js
 * Submit hook — saves selected items to sessionStorage and redirects back to RAB.
 */

import { state, submitBtn } from '../core/state.js';
import { toast } from '../../shared/ui/toast.js';

export function bindSubmit() {
    if (!submitBtn) return;

    submitBtn.addEventListener('click', function () {
        const items = Object.values(state.selected);
        if (items.length === 0) return;

        // Ambil konteks kategori dari sessionStorage (disimpan saat klik + di RAB)
        let catId   = '';
        let catName = '';
        let catDbId = null;  // db_id integer dari tabel kategori_pekerjaan
        try {
            catId   = sessionStorage.getItem('rab_tambah_ahs_cat')     || '';
            catName = sessionStorage.getItem('rab_tambah_ahs_catname') || '';
            catDbId = sessionStorage.getItem('rab_tambah_ahs_dbid')    || null;
            if (catDbId) catDbId = parseInt(catDbId, 10) || null;
        } catch (_) {}

        try {
            let existing = [];
            try {
                const raw = sessionStorage.getItem('rab_pending_items');
                if (raw) existing = JSON.parse(raw);
            } catch (_) {}

            // Replace existing group for this category (avoid duplicates)
            existing = existing.filter(g => g.catId !== catId);
            existing.push({ catId, catName, catDbId, items });
            sessionStorage.setItem('rab_pending_items', JSON.stringify(existing));
        } catch (_) {}

        // Visual feedback → redirect
        submitBtn.textContent = 'Menambahkan…';
        submitBtn.disabled    = true;
        toast.show(`Berhasil menambahkan ${items.length} pekerjaan ke RAB`, 'success', 2500);

        setTimeout(function () {
            let rabUrl = '';
            try { rabUrl = sessionStorage.getItem('rab_return_url') || ''; } catch (_) {}
            if (!rabUrl) {
                try {
                    const ref = document.referrer;
                    if (ref && new URL(ref).origin === window.location.origin) rabUrl = ref;
                } catch (_) {}
            }
            window.location.href = rabUrl || '/menu-rap?mode=new';
        }, 1200);
    });
}
