/**
 * hooks/pending.js
 * Stateful logic for injecting pending AHS items (from sessionStorage) into the
 * RAB table after returning from the Tambah AHS page, and restoring category rows.
 *
 * UPDATED: pending items are now persisted to the database (POST /api/rap)
 * before being rendered, so data survives page refresh.
 */

import { state, tbody }      from '../core/state.js';
import { fmt, escHtml }      from '../../shared/utils.js';
import { updateTotals }      from '../components/render.js';
import { appendCategoryRow } from '../components/categories.js';
import { confirmDelete }     from '../../shared/ui/confirm.js';
import { toast }             from '../../shared/ui/toast.js';
import { batchSaveRap }      from '../core/rap-data.js';
import { getProjectSlug }    from '../core/data.js';

export async function injectPendingItems() {
    let groups = [];
    try {
        const raw = sessionStorage.getItem('rab_pending_items');
        if (raw) groups = JSON.parse(raw);
    } catch (_) { return; }
    
    if (!groups || groups.length === 0) return;

    const slug = getProjectSlug();
    if (!slug) return;

    // Flatten semua item dari semua group
    const batchItems = [];
    groups.forEach(group => {
        (group.items || []).forEach(item => {
            batchItems.push({
                id_pekerjaan: item.id    || 0,
                db_id:        item.db_id || null,
                id_kategori:  group.catDbId || null,
                sumber:       item.sumber  || 'estimator',
                nama:         item.nama    || '',
                satuan:       item.satuan  || '',
            });
        });
    });

    if (batchItems.length > 0) {
        try {
            await batchSaveRap(slug, batchItems);
            toast.show(`${batchItems.length} pekerjaan ditambahkan ke RAP`, 'success', 2000);
            sessionStorage.removeItem('rab_pending_items');
        } catch (err) {
            console.error('[pending.js] Gagal simpan ke DB:', err);
            toast.show('Gagal menyimpan pekerjaan ke database', 'warning', 3500);
        }
    }
}
