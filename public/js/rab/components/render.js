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

    const grandTotal = categories.reduce((sum, cat) => {
        const items = cat.items || [];
        return sum + items.reduce((s, i) => s + Number(i.hargaKeseluruhan || 0), 0);
    }, 0);

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

    categories.forEach(cat => {
        const items = cat.items || [];
        const catTotal = items.reduce((s, i) => s + Number(i.hargaKeseluruhan || 0), 0);
        const isOpen = !state.collapsed[cat.id];
        const subClass = isOpen ? '' : 'hidden';
        const isReorderMode = window.RAB_INIT && window.RAB_INIT.isReorderMode;

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
    updateTotals(grandTotal);
    updateHierarchicalNumbers(); // initial numbering
    bindCategoryToggle();
    bindReadonlyDropdowns();
    bindCategoryActionButtons();
    bindDeleteCategoryButtons();
    bindSubItemButtons();

    // Bind save button for reorder mode
    const saveBtn = document.getElementById('save-reorder-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
            if (!window.reorderedDataCache || Object.keys(window.reorderedDataCache).length === 0) {
                Swal.fire('Info', 'Tidak ada perubahan urutan yang perlu disimpan.', 'info');
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
                if(pContainer) pContainer.style.paddingLeft = (newDepth * 1.5) + 'rem';
                
                const uraianContainer = item.querySelector('td:nth-child(2) > div');
                if(uraianContainer) uraianContainer.style.paddingLeft = (newDepth * 1.5) + 'rem';
                
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

            const noStr = counters.slice(0, depth + 1).join('.');
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
        
        // CSS for indenting based on mode
        const containerPadding = isReorderMode ? `padding-left: ${indent}rem` : ``;
        const noIconPadding = isReorderMode ? `min-width:30px;` : `padding-left: ${(depth * 0.5) + 1.5}rem`;
        const textPadding = `padding-left: ${indent}rem`; // Always indent Uraian proportionally

        html += `
            <tr class="sortable-item subrow-${catId} ${subClass} bg-table-row border-b border-table-border hover:bg-white transition-colors duration-150" 
                data-cat="${catId}" 
                data-parent-id="${item.id_parent || ''}"
                data-id-rap-detail="${item.id_rap_detail || ''}"
                data-depth="${depth}">
                <td class="px-1 md:px-2 py-2 md:py-2.5 text-center text-table-subtle no-cell w-[100px]" style="border-right: ${isReorderMode ? '1px solid #e5e7eb' : 'none'}">
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
                <td class="px-3 md:px-5 py-2 md:py-2.5 font-medium text-table-medium min-w-[250px] lg:min-w-[350px] whitespace-normal leading-relaxed">
                    <div style="${textPadding}" class="flex items-start gap-2">
                        ${depth > 0 ? `<span class="text-slate-300">└─</span>` : ''}
                        <span>${escHtml(item.uraian || '-')}</span>
                    </div>
                </td>
                ${!isReorderMode ? `
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-center tabular-nums">${volume}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-center text-table-subtle">${escHtml(item.satuan || '')}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(hargaBahan)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(hargaAlat)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap">${fmt(hargaUpah)}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(hargaBahan * (volume || 1))}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(hargaAlat * (volume || 1))}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums whitespace-nowrap font-medium text-table-medium">${fmt(hargaUpah * (volume || 1))}</td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-right tabular-nums font-semibold text-table-strong whitespace-nowrap">
                    ${fmt(hargaKeseluruhan)}
                </td>
                <td class="px-3 md:px-5 py-2 md:py-2.5 text-center">
                    <div class="inline-flex items-center gap-1.5">
                        ${isEditable && isAddDeleteAllowed ? `
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

                        <button
                            type="button"
                            class="readonly-item-detail inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white hover:bg-slate-50 border border-table-border text-table-subtle hover:text-table-body transition-colors focus:outline-none"
                            data-url="${(window.RAB_INIT && window.RAB_INIT.rincianAhsUrl) || '/menu-rap/rincian-ahs'}"
                            data-id-rap-detail="${item.id_rap_detail || ''}"
                            title="Input Rincian AHS">
                            <svg class="w-3.5 h-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>

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
        btn.addEventListener('click', function() {
            const idParent = this.dataset.idRapDetail;
            const idKategori = this.dataset.cat;
            const idProject = window.RAB_INIT?.idProject || window.RAB_INIT?.id;
            const slug = window.RAB_INIT?.slug || new URLSearchParams(window.location.search).get('slug');
            const url = (window.RAB_INIT && window.RAB_INIT.tambahPekerjaanUrl) || `/menu-rap/tambah-pekerjaan`;

            window.location.href = `${url}?id_project=${idProject}&id_kategori=${idKategori}&id_parent=${idParent}&slug=${slug}`;
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

export function updateTotals(total) {
    const safeTotal = Number(total || 0);
    const ppn = safeTotal * 0.11;
    const grand = safeTotal + ppn;

    if (totalJumlah) totalJumlah.textContent = fmt(safeTotal);
    if (totalPpn) totalPpn.textContent = fmt(ppn);
    if (totalFinal) totalFinal.textContent = fmt(grand);
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

            if (!idRapDetail) {
                window.location.href = baseUrl;
                return;
            }

            const separator = baseUrl.includes('?') ? '&' : '?';
            window.location.href = `${baseUrl}${separator}id_rap_detail=${encodeURIComponent(idRapDetail)}`;
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