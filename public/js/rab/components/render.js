/**
 * components/render.js
 * All rendering functions: loading spinner, readonly table, editable empty state,
 * totals, table visibility, editable mode toggle, and bind helpers for readonly mode.
 */

import {
    state,
    tbody,
    wrapper,
    searchInput,
    tambahKategoriBtn,
    totalJumlah,
    totalPpn,
    totalFinal
} from '../core/state.js';
import { fmt, escHtml } from '../../shared/utils.js';
import { fetchRabData } from '../core/data.js';
import { confirmAction } from '../../shared/ui/confirm.js';

export function renderLoading() {
    tbody.innerHTML = `
        <tr>
            <td colspan="12" class="text-center py-10 text-table-subtle text-xs tracking-wide">
                <svg class="animate-spin w-5 h-5 mx-auto mb-2 text-table-muted" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                Memuat data...
            </td>
        </tr>
    `;
    updateTotals(0);
}

export function renderReadonly(data) {
    const categories = data?.categories || [];
    const isReorderMode = window.RAB_INIT && window.RAB_INIT.isReorderMode;
    const isProjectManual = (data?.sumber_data || 'manual') === 'manual';
    const isEditable = isReorderMode ? true : isProjectManual;

    // Specifically lock down structure for BOQ/Estimator source
    const isAddDeleteAllowed = isProjectManual;

    // Helper: sum only leaf nodes recursively (items without children)
    function sumLeaves(items) {
        return items.reduce((s, i) => {
            if (i.children && i.children.length > 0) {
                return s + sumLeaves(i.children);
            }
            return s + Number(i.hargaKeseluruhan || 0);
        }, 0);
    }
    function sumLeavesBahan(items) {
        return items.reduce((s, i) => {
            if (i.children && i.children.length > 0) return s + sumLeavesBahan(i.children);
            return s + (Number(i.hargaBahan || 0) * Number(i.volume || 0));
        }, 0);
    }
    function sumLeavesUpah(items) {
        return items.reduce((s, i) => {
            if (i.children && i.children.length > 0) return s + sumLeavesUpah(i.children);
            return s + (Number(i.hargaUpah || 0) * Number(i.volume || 0));
        }, 0);
    }
    function sumLeavesAlat(items) {
        return items.reduce((s, i) => {
            if (i.children && i.children.length > 0) return s + sumLeavesAlat(i.children);
            return s + (Number(i.hargaAlat || 0) * Number(i.volume || 0));
        }, 0);
    }

    const grandTotal = categories.reduce((sum, cat) => sum + sumLeaves(cat.items || []), 0);
    const grandBahan = categories.reduce((sum, cat) => sum + sumLeavesBahan(cat.items || []), 0);
    const grandUpah  = categories.reduce((sum, cat) => sum + sumLeavesUpah(cat.items || []), 0);
    const grandAlat  = categories.reduce((sum, cat) => sum + sumLeavesAlat(cat.items || []), 0);

    if (categories.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="12" class="text-center py-10 text-table-subtle text-xs">
                    Tidak ada data pekerjaan.
                </td>
            </tr>
        `;
        updateTotals(0);
        return;
    }

    let html = '';

    categories.forEach((cat, catIndex) => {
        const items = cat.items || [];
        const catTotal = sumLeaves(items);
        const isOpen = !state.collapsed[cat.id];
        const subClass = isOpen ? '' : 'hidden';
        const isReorderMode = window.RAB_INIT && window.RAB_INIT.isReorderMode;
        
        const isImported = state.sumber_data === 'boq' || state.sumber_data === 'import';
        let catNomor = formatNomor(-1, [catIndex + 1]);
        let catNomorStr = '';
        
        if (isImported) {
            if (cat.nomor_custom === null || cat.nomor_custom === undefined || cat.nomor_custom === '') {
                catNomor = '';
            } else {
                catNomor = cat.nomor_custom;
                catNomorStr = escHtml(catNomor); // Custom nomor dari Excel, tidak perlu tambah titik
            }
        } else {
            catNomorStr = catNomor ? escHtml(catNomor) + '.' : ''; // Sistem punya, tambah titik
        }

        html += `
            <tr class="rab-category bg-table-category text-white hover:bg-table-category-hover cursor-pointer select-none transition-colors duration-200"
                data-cat="${cat.id}" role="button" tabindex="0">
                <td class="w-12 md:w-14 px-3 md:px-5 py-2.5 md:py-3 text-center">
                    <div class="relative flex items-center justify-center w-5 h-5 mx-auto">
                        <svg class="cat-icon-minus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 ${isOpen ? '' : 'hidden'}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg class="cat-icon-plus absolute w-4 h-4 md:w-5 md:h-5 opacity-90 transition-opacity duration-200 ${isOpen ? 'hidden' : ''}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </td>

                <td colspan="${isReorderMode ? 1 : 9}" class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-[10px] md:text-xs uppercase tracking-widest">
                    <span class="flex items-center gap-2">
                        <span class="w-1 h-3.5 md:h-4 bg-secondary rounded-full"></span>
                        <span class="cat-nomor mr-1">${catNomorStr}</span>
                        ${escHtml(cat.name || 'Tanpa Kategori')}
                    </span>
                </td>

                ${!isReorderMode ? `
                <td class="px-3 md:px-5 py-2.5 md:py-3 text-right text-[10px] md:text-xs tabular-nums opacity-80">
                    ${fmt(catTotal)}
                </td>

                <td class="px-2 md:px-3 py-2.5 md:py-3 text-center">
                    ${isAddDeleteAllowed ? `
                        <div class="inline-flex items-center gap-1">
                            <button
                                type="button"
                                class="add-subitem-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/20 hover:bg-white/30 text-white transition-colors duration-150 focus:outline-none"
                                data-cat="${cat.id}"
                                data-catname="${escHtml(cat.name || '')}"
                                title="Tambah Pekerjaan">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="delete-category-btn inline-flex items-center justify-center w-6 h-6 rounded-md bg-white/20 hover:bg-red-500/30 text-white transition-colors duration-150 focus:outline-none"
                                data-cat="${cat.id}"
                                data-catname="${escHtml(cat.name || '')}"
                                title="Hapus Kategori">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    ` : ''}
                </td>
                ` : ''}
            </tr>
        `;

        if (items.length === 0) {
            html += `
                <tr class="subrow-${cat.id} ${subClass} bg-table-row border-b border-table-border">
                    <td colspan="12" class="px-5 py-3 text-center text-table-subtle text-xs italic">
                        Belum ada item pekerjaan.
                    </td>
                </tr>
            `;
        } else {
            html += renderItemRows(items, cat.id, subClass, isEditable, '', 0, isAddDeleteAllowed);
        }
    });

    tbody.innerHTML = html;
    updateTotals(grandTotal, grandBahan, grandUpah, grandAlat);
    updateHierarchicalNumbers(); // initial numbering
    bindCategoryToggle();
    bindReadonlyDropdowns();
    bindCategoryActionButtons();
    bindDeleteCategoryButtons();
    bindSubItemButtons();
    bindVolumeInputs();

    // Bind save button for reorder mode
    const saveBtn = document.getElementById('save-reorder-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
            if (!window.reorderedDataCache || Object.keys(window.reorderedDataCache).length === 0) {
                Swal.fire({
                    title: 'Info', 
                    text: 'Tidak ada perubahan urutan yang perlu disimpan.', 
                    icon: 'info',
                    scrollbarPadding: false
                });
                return;
            }

            const allReordered = [];
            for (const catId in window.reorderedDataCache) {
                allReordered.push(...window.reorderedDataCache[catId]);
            }

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            await saveOrderToBackend(allReordered);

            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Urutan';

            // Clear cache after save
            window.reorderedDataCache = {};
        });
    }

    try {
        window.HSStaticMethods?.autoInit(['dropdown']);
    } catch (_) { }

    if (isEditable && isReorderMode && typeof window.Sortable !== 'undefined') {
        if (window.rabSortableInstance) {
            window.rabSortableInstance.destroy();
        }
        window.rabSortableInstance = new window.Sortable(tbody, {
            animation: 150,
            handle: '.drag-handle',
            draggable: '.sortable-item',
            ghostClass: 'sortable-ghost',
            forceFallback: true,
            fallbackOnBody: true,
            onStart: function (evt) {
                const item = evt.item;
                const startDepth = parseInt(item.dataset.depth || '0', 10);

                const children = [];
                let next = item.nextElementSibling;
                while (next && next.classList.contains('sortable-item')) {
                    const nextDepth = parseInt(next.dataset.depth || '0', 10);
                    if (nextDepth > startDepth) {
                        children.push(next);
                        next = next.nextElementSibling;
                    } else {
                        break;
                    }
                }
                item._dragChildren = children;

                // Hide children so they don't interfere with DOM positions during drag
                children.forEach(c => c.style.display = 'none');

                // Track dragging for indent/outdent
                item._originalDepth = startDepth;
                item._currentDepth = startDepth;
                item._dragStartX = null;
                item._lastPrevSibling = null; // Track where the ghost is to reset anchor

                const handleDragMove = (e) => {
                    const clientX = e.touches ? e.touches[0].clientX : e.clientX;

                    const ghostEl = document.querySelector('.sortable-ghost');
                    if (!ghostEl) return;

                    // Find actual previous sibling of ghost
                    let prev = ghostEl.previousElementSibling;
                    while (prev && (prev.classList.contains('sortable-drag') || prev.style.display === 'none')) {
                        prev = prev.previousElementSibling;
                    }

                    // If the ghost moved vertically to a new slot, RESET the horizontal anchor!
                    // This completely ignores "arc" dragging where users sweep the cursor diagonally.
                    if (item._lastPrevSibling !== prev) {
                        item._lastPrevSibling = prev;
                        item._dragStartX = clientX; // Reset base X to current mouse position
                        item._currentDepth = startDepth; // Reset to original depth assumption for the new slot
                    }

                    if (item._dragStartX === null) {
                        item._dragStartX = clientX;
                    }

                    const deltaX = clientX - item._dragStartX;
                    const deltaDepth = Math.round(deltaX / 30);

                    let prevDepth = 0;
                    if (prev && prev.classList.contains('sortable-item')) {
                        prevDepth = parseInt(prev.dataset.depth || '0', 10);
                    }

                    // Calculate "Family Height" (deepest child's relative depth from parent)
                    let familyHeight = 0;
                    if (item._dragChildren && item._dragChildren.length > 0) {
                        item._dragChildren.forEach(child => {
                            const relDepth = parseInt(child.dataset.depth || '0', 10) - startDepth;
                            familyHeight = Math.max(familyHeight, relDepth);
                        });
                    }

                    // Max absolute depth is 2 (level 3: 1.1.1)
                    // The parent's max depth is (2 - familyHeight) to ensure the deepest child stays <= 2
                    // AND at most previous sibling's depth + 1
                    const maxAllowedForFamily = 2 - familyHeight;
                    const maxDepth = Math.min(maxAllowedForFamily, prevDepth + 1);

                    let targetDepth = startDepth + deltaDepth;
                    let clampedDepth = Math.max(0, Math.min(targetDepth, maxDepth));

                    // Visual feedback colors
                    let indicatorClass = '';
                    let bgColor = '';

                    if (targetDepth > clampedDepth) { // Wants to indent but can't
                        bgColor = 'rgba(239, 68, 68, 0.15)'; // red-500 light
                        indicatorClass = 'border-l-4 border-red-500';
                    } else if (clampedDepth > startDepth) { // Indenting (Dimasukkin) -> Green
                        bgColor = 'rgba(34, 197, 94, 0.15)'; // emerald-500 light
                        indicatorClass = 'border-l-4 border-emerald-500';
                    } else if (clampedDepth < startDepth) { // Outdenting (Dikeluarkan) -> Yellow
                        bgColor = 'rgba(234, 179, 8, 0.15)'; // yellow-500 light
                        indicatorClass = 'border-l-4 border-yellow-500';
                    } else { // Neutral
                        bgColor = '';
                        indicatorClass = 'border-l-4 border-primary';
                    }

                    Array.from(ghostEl.children).forEach(td => {
                        td.style.backgroundColor = bgColor;
                    });

                    const pContainer = ghostEl.querySelector('td:nth-child(1) > div');
                    if (pContainer) pContainer.style.paddingLeft = (clampedDepth * 1.5) + 'rem';

                    const textContainer = ghostEl.querySelector('td:nth-child(2) > div');
                    if (textContainer) textContainer.style.paddingLeft = (clampedDepth * 1.5) + 'rem';

                    ghostEl.className = ghostEl.className.replace(/border-l-4 border-[a-z]+-500/g, '');
                    ghostEl.classList.add('border-l-4');
                    if (indicatorClass) {
                        ghostEl.className += ' ' + indicatorClass.replace('border-l-4', '').trim();
                    }

                    item._currentDepth = clampedDepth;
                };

                document.addEventListener('mousemove', handleDragMove);
                document.addEventListener('touchmove', handleDragMove);
                item._cleanupDragMove = () => {
                    document.removeEventListener('mousemove', handleDragMove);
                    document.removeEventListener('touchmove', handleDragMove);
                };
            },
            onMove: function (evt) {
                const related = evt.related;

                // Allow moving to any sortable item regardless of category
                if (related && related.classList.contains('sortable-item')) {
                    return true;
                }
                return false;
            },
            onEnd: function (evt) {
                const item = evt.item;
                const children = item._dragChildren || [];
                const originalDepth = item._originalDepth;
                const newDepth = item._currentDepth;

                if (item._cleanupDragMove) item._cleanupDragMove();

                const depthDelta = newDepth - originalDepth;

                // PREVENT FAMILY SPLITTING
                // If we dropped into the middle of another parent's descendants while asking for a shallower depth,
                // auto-shift the parent down to the end of that interrupted family cluster.
                let nextNode = item.nextElementSibling;
                while (nextNode && !children.includes(nextNode) && nextNode.classList.contains('sortable-item')) {
                    let sibDepth = parseInt(nextNode.dataset.depth || '0', 10);
                    if (sibDepth > newDepth) {
                        nextNode.after(item);
                        nextNode = item.nextElementSibling;
                    } else {
                        break;
                    }
                }

                // Apply new depth to the dragged item
                item.dataset.depth = newDepth;
                const pContainer = item.querySelector('td:nth-child(1) > div');
                if (pContainer) pContainer.style.paddingLeft = (newDepth * 1.5) + 'rem';

                const uraianContainer = item.querySelector('td:nth-child(2) > div');
                if (uraianContainer) uraianContainer.style.paddingLeft = (newDepth * 1.5) + 'rem';

                // Move children back after the parent in its new position and adjust their depths
                let current = item;
                children.forEach(c => {
                    c.style.display = ''; // Restore visibility

                    const chStartDepth = parseInt(c.dataset.depth || '0', 10);
                    const chNewDepth = chStartDepth + depthDelta;
                    c.dataset.depth = chNewDepth;

                    const cContainer = c.querySelector('td:nth-child(1) > div');
                    if (cContainer) cContainer.style.paddingLeft = (chNewDepth * 1.5) + 'rem';

                    const cUraian = c.querySelector('td:nth-child(2) > div');
                    if (cUraian) cUraian.style.paddingLeft = (chNewDepth * 1.5) + 'rem';

                    current.after(c);
                    current = c;
                });

                delete item._dragChildren;
                delete item._cleanupDragMove;

                if (evt.oldIndex === evt.newIndex && newDepth === originalDepth) return;

                // Full table sweep to handle cross-category reordering
                const allRows = Array.from(tbody.children);
                let currentCatId = null;
                const countersByParent = {};
                const currentParents = { 0: null };
                const finalReorderedData = {};

                allRows.forEach(row => {
                    if (row.classList.contains('rab-category')) {
                        currentCatId = row.dataset.cat;
                        finalReorderedData[currentCatId] = [];
                        return;
                    }

                    if (row.classList.contains('sortable-item')) {
                        const depth = parseInt(row.dataset.depth || '0', 10);
                        const idParent = depth > 0 ? currentParents[depth - 1] : null;

                        // Link to new category and update classes
                        if (row.dataset.cat !== currentCatId) {
                            row.className = row.className.replace(/subrow-\d+/, `subrow-${currentCatId}`);
                            row.dataset.cat = currentCatId;
                        }

                        currentParents[depth] = row.dataset.idRapDetail;
                        row.dataset.parentId = idParent || '';

                        const pIdKey = idParent || 'root';
                        if (!countersByParent[pIdKey]) countersByParent[pIdKey] = 0;
                        countersByParent[pIdKey]++;

                        finalReorderedData[currentCatId].push({
                            id_rap_detail: row.dataset.idRapDetail,
                            id_kategori: currentCatId,
                            urutan: countersByParent[pIdKey],
                            id_parent: idParent
                        });
                    }
                });

                updateHierarchicalNumbers();
                window.reorderedDataCache = finalReorderedData;
            }
        });
    }
}

/**
 * Recalculate and update the display numbers (1, 1.1, etc.) in the No column
 * for all items in the table, respecting the current DOM order and depth.
 */
export function formatNomor(depth, counters) {
    let format = state.format_penomoran || {};
    if (typeof format === 'string') {
        try { format = JSON.parse(format); } catch(e) { format = {}; }
    }
    
    const rule = format[String(depth)] || (depth === -1 ? 'A' : (depth === 0 ? '1' : '1.1'));
    const currIndex = counters[counters.length - 1];
    
    if (rule === 'A') return numberToAlpha(currIndex, false);
    if (rule === 'a') return numberToAlpha(currIndex, true);
    if (rule === 'I') return numberToRoman(currIndex, false);
    if (rule === 'i') return numberToRoman(currIndex, true);
    if (rule === '-') return '-';
    if (rule === '1.1') return counters.join('.');
    
    return currIndex.toString();
}

function numberToAlpha(num, lowercase = false) {
    let result = '';
    while (num > 0) {
        num--;
        result = String.fromCharCode((lowercase ? 97 : 65) + (num % 26)) + result;
        num = Math.floor(num / 26);
    }
    return result;
}

function numberToRoman(num, lowercase = false) {
    const lookup = {M:1000,CM:900,D:500,CD:400,C:100,XC:90,L:50,XL:40,X:10,IX:9,V:5,IV:4,I:1};
    let roman = '';
    for ( let i in lookup ) {
        while ( num >= lookup[i] ) {
            roman += i;
            num -= lookup[i];
        }
    }
    return lowercase ? roman.toLowerCase() : roman;
}

function updateHierarchicalNumbers() {
    const categories = Array.from(tbody.querySelectorAll('.rab-category'));
    categories.forEach(catRow => {
        const catId = catRow.dataset.cat;
        const itemRows = Array.from(tbody.querySelectorAll(`.subrow-${catId}.sortable-item`));

        let counters = [0];
        let prevDepth = 0;

        itemRows.forEach(row => {
            const depth = parseInt(row.dataset.depth || '0', 10);

            if (depth > prevDepth) {
                counters[depth] = 1;
            } else if (depth < prevDepth) {
                counters = counters.slice(0, depth + 1);
                counters[depth]++;
            } else {
                counters[depth]++;
            }
            prevDepth = depth;

            // Failsafe initialization
            for (let i = 0; i <= depth; i++) {
                if (!counters[i]) counters[i] = 1;
            }

            let noStr = '';
            const isImported = state.sumber_data === 'boq' || state.sumber_data === 'import';
            const rowSumber = row.dataset.sumber || 'manual';
            const rowNomorCustom = row.dataset.nomorCustom;
            
            if (isImported && rowSumber === 'boq') {
                if (rowNomorCustom === "null" || rowNomorCustom === "undefined" || !rowNomorCustom) {
                    noStr = '';
                } else {
                    noStr = rowNomorCustom;
                }
            } else {
                noStr = formatNomor(depth, counters.slice(0, depth + 1));
                if (noStr !== '-' && !noStr.includes('.')) {
                    noStr += '.';
                }
            }

            const span = row.querySelector('.no-cell span');
            if (span) span.textContent = noStr;
        });
    });
}

function renderItemRows(items, catId, subClass, isEditable, prefix = '', depth = 0, isAddDeleteAllowed = true) {
    let html = '';
    const isReorderMode = window.RAB_INIT && window.RAB_INIT.isReorderMode;
    items.forEach((item, index) => {
        const volume = Number(item.volume || 0);
        const hargaBahan = Number(item.hargaBahan || 0);
        const hargaAlat = Number(item.hargaAlat || 0);
        const hargaUpah = Number(item.hargaUpah || 0);
        const hargaKeseluruhan = Number(item.hargaKeseluruhan || 0);

        const currentNo = prefix ? `${prefix}.${index + 1}` : `${index + 1}`;
        const indent = depth * 1.5; // rem
        const hasChildren = item.children && item.children.length > 0;
        const uraianColspan = hasChildren && !isReorderMode ? 10 : 1;
        const fontClass = hasChildren ? 'font-bold text-slate-700' : 'font-medium text-table-medium';
        const bgClass = hasChildren ? 'bg-slate-50/50' : '';

        // CSS for indenting based on mode
        const containerPadding = isReorderMode ? `padding-left: ${indent}rem` : ``;
        const noIconPadding = isReorderMode ? `min-width:30px;` : `padding-left: ${(depth * 0.5) + 1.5}rem`;
        const textPadding = `padding-left: ${indent}rem`; // Always indent Uraian proportionally

        html += `
            <tr class="sortable-item subrow-${catId} ${subClass} bg-table-row border-b border-table-border hover:bg-white transition-colors duration-150" 
                data-cat="${catId}" 
                data-parent-id="${item.id_parent || ''}"
                data-id-rap-detail="${item.id_rap_detail || ''}"
                data-sumber="${item.sumber || 'manual'}"
                data-nomor-custom="${item.nomor_custom !== null && item.nomor_custom !== undefined ? escHtml(item.nomor_custom) : 'null'}"
                data-depth="${depth}">
                <td class="px-1 md:px-2 py-2 md:py-2.5 text-center text-table-subtle no-cell w-[100px] ${bgClass}" style="border-right: ${isReorderMode ? '1px solid #e5e7eb' : 'none'}">
                    <div class="flex items-center justify-start h-full transition-all duration-150" style="${containerPadding}">
                        ${isReorderMode ? `
                        <!-- Icon stays on the far left, fixed width -->
                        <div class="w-6 md:w-8 flex-shrink-0 flex items-center justify-center">
                            ${isEditable ? `
                                <div class="cursor-grab hover:text-primary active:cursor-grabbing text-slate-300 transition-colors drag-handle active:scale-95 p-1" title="Tahan dan geser untuk memindahkan urutan">
                                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                    </svg>
                                </div>
                            ` : ''}
                        </div>
                        ` : ''}
                        <!-- Indented number -->
                        <div class="flex-1 text-left" style="${noIconPadding}">
                            <span class="text-[10px] md:text-xs tabular-nums font-medium whitespace-nowrap">${currentNo}</span>
                        </div>
                    </div>
                </td>
                <td colspan="${uraianColspan}" class="px-3 md:px-5 py-2 md:py-2.5 ${fontClass} min-w-[250px] lg:min-w-[350px] whitespace-normal leading-relaxed border-l border-table-border ${bgClass}">
                    <div style="${textPadding}" class="flex items-start gap-2">
                        ${depth > 0 ? `<span class="text-slate-300 font-normal">└─</span>` : ''}
                        <span>${escHtml(item.uraian || '')}</span>
                    </div>
                </td>
                ${!isReorderMode ? `
                ${!hasChildren ? `
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-center tabular-nums border-l border-table-border group relative w-[120px]">
                    ${isEditable ? `
                        <div class="flex items-center justify-center gap-2 volume-display-container">
                            <span class="volume-text">${volume}</span>
                            <button type="button" class="edit-volume-btn opacity-0 group-hover:opacity-100 text-slate-400 hover:text-primary transition-opacity p-1 focus:outline-none" title="Edit Volume">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                        </div>
                        <div class="hidden volume-edit-container items-center justify-center gap-1">
                            <input type="number" min="0" step="0.01" class="volume-input w-14 px-1.5 py-1 text-center text-[10px] md:text-xs border border-table-border rounded focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all" data-id-rap-detail="${item.id_rap_detail || ''}" value="${volume}">
                            <div class="flex items-center flex-col gap-0.5">
                                <button type="button" class="save-volume-btn flex items-center justify-center w-5 h-5 rounded bg-primary text-white hover:bg-primary-hover focus:outline-none transition-colors shadow-sm" title="Simpan">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button type="button" class="cancel-volume-btn flex items-center justify-center w-5 h-5 rounded bg-slate-200 text-slate-600 hover:bg-slate-300 focus:outline-none transition-colors shadow-sm" title="Batal">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    ` : volume}
                </td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle border-l border-table-border">${escHtml(item.satuan || '')}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap border-l border-table-border">${fmt(hargaBahan)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap border-l border-table-border">${fmt(hargaUpah)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap border-l border-table-border">${fmt(hargaAlat)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium border-l border-table-border">${fmt(hargaBahan * (volume || 1))}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium border-l border-table-border">${fmt(hargaUpah * (volume || 1))}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium border-l border-table-border">${fmt(hargaAlat * (volume || 1))}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong whitespace-nowrap border-l border-table-border ${bgClass}">
                    ${fmt(hargaKeseluruhan)}
                </td>
                ` : ''}
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-center border-l border-table-border ${bgClass}">
                    <div class="inline-flex items-center gap-1.5">
                        ${isEditable && isAddDeleteAllowed && depth < 2 ? `
                            <button
                                type="button"
                                class="add-nested-item-btn inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border border-emerald-200 transition-colors focus:outline-none"
                                data-id-rap-detail="${item.id_rap_detail || ''}"
                                data-cat="${catId}"
                                title="Tambah Sub Pekerjaan">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        ` : ''}

                        ${isEditable && isAddDeleteAllowed && hasChildren ? `
                            <button
                                type="button"
                                class="copy-pekerjaan-btn inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 transition-colors focus:outline-none"
                                data-id-rap-detail="${item.id_rap_detail || ''}"
                                title="Copy Pekerjaan">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        ` : ''}

                        ${!hasChildren ? `
                        <button
                            type="button"
                            class="readonly-item-detail inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white hover:bg-slate-50 border border-table-border text-table-subtle hover:text-table-body transition-colors focus:outline-none"
                            data-url="${(window.RAB_INIT && window.RAB_INIT.rincianAhsUrl) || '/menu-rap/rincian-ahs'}"
                            data-id-rap-detail="${item.id_rap_detail || ''}"
                            data-uraian="${escHtml(item.uraian || '')}"
                            data-keterangan="${escHtml(item.keterangan || '')}"
                            title="Input Rincian AHS">
                            <svg class="w-3.5 h-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        ` : ''}

                        ${isEditable && isAddDeleteAllowed ? `
                            <button
                                type="button"
                                class="readonly-item-delete inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white hover:bg-red-50 border border-table-border text-red-500 transition-colors focus:outline-none"
                                data-id-rap-detail="${item.id_rap_detail || ''}"
                                title="Hapus pekerjaan">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        ` : ''}
                    </div>
                </td>
                ` : ''}
            </tr>
        `;

        if (hasChildren) {
            html += renderItemRows(item.children, catId, subClass, isEditable, currentNo, depth + 1, isAddDeleteAllowed);
        }
    });
    return html;
}

function bindSubItemButtons() {
    tbody.querySelectorAll('.add-nested-item-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const idParent = this.dataset.idRapDetail;
            const idKategori = this.dataset.cat;
            const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
            const slug = window.RAB_INIT?.slug || new URLSearchParams(window.location.search).get('slug');
            const url = (window.RAB_INIT && window.RAB_INIT.tambahPekerjaanUrl) || `/menu-rap/tambah-pekerjaan`;

            window.location.href = `${url}?id_project=${idProject}&id_kategori=${idKategori}&id_parent=${idParent}&slug=${slug}`;
        });
    });

    tbody.querySelectorAll('.copy-pekerjaan-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const idRapDetail = this.dataset.idRapDetail;
            if (!idRapDetail) return;

            try {
                if (window.renderLoading) window.renderLoading();
                const res = await fetch(`/api/rap/pekerjaan/copy`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ id_rap_detail: idRapDetail })
                });

                const json = await res.json();
                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal mengcopy pekerjaan');
                }

                if (window.Toast) window.Toast.show('Pekerjaan berhasil diduplikat', 'success');

                // Refresh data
                const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
                if (idProject && window.fetchRabData) {
                    const data = await window.fetchRabData(idProject);
                    if (window.state) {
                        window.state.activeCategories = (data.categories || []).map(cat => ({
                            id: String(cat.id),
                            nama: cat.name
                        }));
                    }
                    if (window.renderReadonly) {
                        window.renderReadonly(data);
                    } else {
                        window.location.reload();
                    }
                } else {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
                if (window.Toast) window.Toast.show(err.message, 'error');
                else alert(err.message);

                // fallback reload on error if stuck loading
                if (!window.fetchRabData) window.location.reload();
            }
        });
    });
}

function bindVolumeInputs() {
    // Bind Edit Buttons
    tbody.querySelectorAll('.edit-volume-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const td = this.closest('td');
            const displayContainer = td.querySelector('.volume-display-container');
            const editContainer = td.querySelector('.volume-edit-container');
            const input = editContainer.querySelector('.volume-input');

            displayContainer.classList.add('hidden');
            displayContainer.classList.remove('flex');
            editContainer.classList.remove('hidden');
            editContainer.classList.add('flex');
            
            input.focus();
            input.select();
        });
    });

    // Replace blur saving with explicit buttons
    tbody.querySelectorAll('.volume-edit-container').forEach(container => {
        const input = container.querySelector('.volume-input');
        const saveBtn = container.querySelector('.save-volume-btn');
        const cancelBtn = container.querySelector('.cancel-volume-btn');
        const displayContainer = container.closest('td').querySelector('.volume-display-container');
        
        let lastValue = input.value;

        const resetView = () => {
            container.classList.add('hidden');
            container.classList.remove('flex');
            displayContainer.classList.remove('hidden');
            displayContainer.classList.add('flex');
            input.value = lastValue; // Revert input to original
        };

        const saveVolume = async () => {
            const idRapDetail = input.dataset.idRapDetail;
            const newVolume = parseFloat(input.value);

            if (isNaN(newVolume) || newVolume < 0) {
                resetView();
                return;
            }

            if (input.value === lastValue) {
                resetView();
                return;
            }

            try {
                if (window.renderLoading) window.renderLoading();
                const res = await fetch(`/api/rap/pekerjaan/${idRapDetail}/volume`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ volume: newVolume })
                });

                const json = await res.json();
                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal mengubah volume');
                }

                if (window.Toast) window.Toast.show('Volume berhasil diubah', 'success');

                // Refresh data
                const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
                if (idProject && window.fetchRabData) {
                    const data = await window.fetchRabData(idProject);
                    if (window.state) {
                        window.state.activeCategories = (data.categories || []).map(cat => ({
                            id: String(cat.id),
                            nama: cat.name
                        }));
                    }
                    if (window.renderReadonly) {
                        window.renderReadonly(data);
                    } else {
                        window.location.reload();
                    }
                } else {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
                if (window.Toast) window.Toast.show(err.message, 'error');
                else alert(err.message);
                
                resetView();
                if (!window.fetchRabData) window.location.reload();
            }
        };

        saveBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            saveVolume();
        });

        cancelBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            resetView();
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveVolume();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                resetView();
            }
        });
    });
}

async function saveOrderToBackend(reorderedItems) {
    if (!reorderedItems.length) return;
    try {
        const res = await fetch('/api/rap/reorder', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ items: reorderedItems })
        });
        const json = await res.json();

        if (!res.ok || json.status !== 'success') {
            throw new Error(json.message || 'Gagal menyimpan urutan');
        }
    } catch (err) {
        console.error('Gagal reorder:', err);
        if (window.Toast) {
            window.Toast.show(err.message || 'Gagal menyimpan urutan', 'error');
        } else {
            alert(err.message || 'Gagal menyimpan urutan');
        }
    }
}

export function renderEditable() {
    tbody.innerHTML = `
        <tr id="rab-tbody-empty">
            <td colspan="12" class="text-center py-14 text-table-subtle text-xs">
                <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Belum ada kategori pekerjaan. Klik <strong>+ Kategori Pekerjaan</strong> untuk memulai.
            </td>
        </tr>
    `;
    updateTotals(0);
}

export function bindCategoryActionButtons() {
    tbody.querySelectorAll('.add-subitem-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();

            const idKategori = Number(btn.dataset.cat || 0);
            const namaKategori = btn.dataset.catname || 'kategori';
            const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
            const slug = window.RAB_INIT?.slug || new URLSearchParams(window.location.search).get('slug');

            if (!idKategori) {
                alert('Kategori tidak ditemukan.');
                return;
            }

            if (!slug && !idProject) {
                alert('Project tidak ditemukan.');
                return;
            }

            const params = new URLSearchParams();
            if (slug) params.set('slug', slug);
            if (idProject) params.set('id_project', idProject);
            params.set('id_kategori', idKategori);
            params.set('kategori_nama', namaKategori);

            window.location.href = `/menu-rap/tambah-pekerjaan?${params.toString()}`;
        });
    });
}

export function bindDeleteCategoryButtons() {
    tbody.querySelectorAll('.delete-category-btn').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.stopPropagation();

            const catId = btn.dataset.cat || '';
            const catName = btn.dataset.catname || 'kategori';

            if (!catId) return;

            const ok = await confirmAction(
                'Hapus Kategori?',
                `Yakin ingin menghapus kategori <strong>"${catName}"</strong>? Semua pekerjaan di kategori ini juga akan ikut terhapus.`,
                'Ya, Hapus'
            );
            if (!ok) return;

            try {
                const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
                if (!idProject) {
                    throw new Error('ID project tidak ditemukan');
                }

                const res = await fetch(`/api/rap/kategori/${encodeURIComponent(catId)}?id_project=${encodeURIComponent(idProject)}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const json = await res.json();

                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal menghapus kategori');
                }

                renderLoading();
                const data = await fetchRabData(idProject);

                state.activeCategories = (data.categories || []).map(cat => ({
                    id: String(cat.id),
                    nama: cat.name
                }));

                renderReadonly(data);

                // Show toast specifically for UI completeness
                if (window.Toast) {
                    window.Toast.show(`Kategori "${catName}" berhasi dihapus dari project`, 'success');
                } else if (typeof toast !== 'undefined' && toast.show) {
                    toast.show(`Kategori "${catName}" berhasi dihapus dari project`, 'success');
                }
            } catch (err) {
                console.error('Gagal hapus kategori:', err);
                if (window.Toast) {
                    window.Toast.show(err.message || 'Terjadi kesalahan saat menghapus kategori', 'error');
                } else {
                    alert(err.message || 'Terjadi kesalahan saat menghapus kategori');
                }
            }
        });
    });
}

export function updateTotals(total, bahan = 0, upah = 0, alat = 0) {
    const safeTotal = Number(total || 0);
    const safeBahan = Number(bahan || 0);
    const safeUpah  = Number(upah || 0);
    const safeAlat  = Number(alat || 0);
    const ppn  = safeTotal * 0.11;
    const grand = safeTotal + ppn;

    if (totalJumlah) totalJumlah.textContent = fmt(safeTotal);
    if (totalPpn)    totalPpn.textContent     = fmt(ppn);
    if (totalFinal)  totalFinal.textContent   = fmt(grand);

    // Update subtotal rows
    const elBahan = document.getElementById('rab-subtotal-bahan');
    const elUpah  = document.getElementById('rab-subtotal-upah');
    const elAlat  = document.getElementById('rab-subtotal-alat');
    if (elBahan) elBahan.textContent = fmt(safeBahan);
    if (elUpah)  elUpah.textContent  = fmt(safeUpah);
    if (elAlat)  elAlat.textContent  = fmt(safeAlat);
}

export function showTable() {
    wrapper.classList.remove('hidden');

    if (searchInput) {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
    }

    setTimeout(() => {
        wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 80);
}

export function setEditableMode(on) {
    if (!tambahKategoriBtn) return;

    if (on) {
        tambahKategoriBtn.classList.remove('hidden');
    } else {
        tambahKategoriBtn.classList.add('hidden');
    }
}

export function bindCategoryToggle() {
    tbody.querySelectorAll('.rab-category[data-cat]').forEach(row => {
        row.addEventListener('click', function () {
            const catId = row.dataset.cat;
            const subRows = tbody.querySelectorAll(`.subrow-${catId}`);
            const minus = row.querySelector('.cat-icon-minus');
            const plus = row.querySelector('.cat-icon-plus');
            const chevron = row.querySelector('.cat-chevron');
            const isHidden = subRows.length > 0 && subRows[0].classList.contains('hidden');

            subRows.forEach(r => r.classList.toggle('hidden', !isHidden));

            if (minus) minus.classList.toggle('hidden', !isHidden);
            if (plus) plus.classList.toggle('hidden', isHidden);
            if (chevron) chevron.classList.toggle('rotate-180', !isHidden);

            state.collapsed[catId] = !isHidden;
        });

        row.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                row.click();
            }
        });
    });
}

export function bindReadonlyDropdowns() {
    tbody.querySelectorAll('.readonly-item-detail').forEach(btn => {
        btn.addEventListener('click', function () {
            const baseUrl = btn.dataset.url || '/menu-rap/rincian-ahs';
            const idRapDetail = btn.dataset.idRapDetail || '';
            const uraian = btn.dataset.uraian || '';
            const keterangan = btn.dataset.keterangan || '';

            let sourceVal = '';
            if (keterangan.toUpperCase().includes('EMPIRIS')) {
                sourceVal = 'EMPIRIS';
            } else if (keterangan.trim() === '') {
                sourceVal = '';
            } else {
                sourceVal = 'PUPR';
            }

            try {
                sessionStorage.setItem('ahs_item_label', uraian);
                sessionStorage.setItem('ahs_item_source', sourceVal);
            } catch (_) { }

            if (!idRapDetail) {
                window.location.href = baseUrl;
                return;
            }

            const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id || '';
            const separator = baseUrl.includes('?') ? '&' : '?';
            const projectPart = idProject ? `&id_project=${encodeURIComponent(idProject)}` : '';
            window.location.href = `${baseUrl}${separator}id_rap_detail=${encodeURIComponent(idRapDetail)}${projectPart}`;
        });
    });

    tbody.querySelectorAll('.readonly-item-delete').forEach(btn => {
        btn.addEventListener('click', async function () {
            const idRapDetail = btn.dataset.idRapDetail || btn.getAttribute('data-id-rap-detail');
            if (!idRapDetail) return;

            const ok = await confirmAction(
                'Hapus Pekerjaan?',
                'Yakin ingin menghapus pekerjaan ini dari RAB?',
                'Ya, Hapus'
            );
            if (!ok) return;

            try {
                const res = await fetch(`/api/rap/pekerjaan/${idRapDetail}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const json = await res.json();

                if (!res.ok || json.status !== 'success') {
                    throw new Error(json.message || 'Gagal menghapus');
                }

                const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
                if (!idProject) {
                    throw new Error('ID project tidak ditemukan');
                }

                renderLoading();
                const data = await fetchRabData(idProject);

                state.activeCategories = (data.categories || []).map(cat => ({
                    id: String(cat.id),
                    nama: cat.name
                }));

                renderReadonly(data);

                if (window.Toast) {
                    window.Toast.show('Pekerjaan berhasil dihapus dari RAB', 'success');
                }
            } catch (err) {
                console.error('Gagal hapus pekerjaan:', err);
                if (window.Toast) {
                    window.Toast.show(err.message || 'Terjadi kesalahan saat menghapus', 'error');
                } else {
                    alert(err.message || 'Terjadi kesalahan saat menghapus');
                }
            }
        });
    });
}