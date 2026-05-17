/**
 * rab/search.js
 * Real-time search/filter for the RAB table.
 */

import { state, tbody, searchInput } from './state.js';

export function bindSearch() {
    if (!searchInput) return;

    searchInput.addEventListener('input', function (e) {
        const term       = e.target.value.toLowerCase().trim();
        const categories = tbody.querySelectorAll('.rab-category');

        categories.forEach(catRow => {
            const catId   = catRow.dataset.cat;
            if (!catId) return;

            const catText      = catRow.textContent.toLowerCase();
            const items        = tbody.querySelectorAll(`.subrow-${catId}, .subrow-item-${catId}, .subrow-placeholder-${catId}`);
            let catMatch       = term === '' || catText.includes(term);
            let anyItemMatch   = false;

            items.forEach(itemRow => {
                const itemText  = itemRow.textContent.toLowerCase();
                const itemMatch = term === '' || itemText.includes(term);

                if (itemMatch || catMatch) {
                    itemRow.style.display = '';
                    if (term !== '') itemRow.classList.remove('hidden');
                    anyItemMatch = true;
                } else {
                    itemRow.style.display = 'none';
                }
            });

            if (catMatch || anyItemMatch) {
                catRow.style.display = '';
                if (term !== '') {
                    const plus    = catRow.querySelector('.cat-icon-plus, .edit-cat-icon-plus');
                    const minus   = catRow.querySelector('.cat-icon-minus, .edit-cat-icon-minus');
                    const chevron = catRow.querySelector('.cat-chevron');
                    if (plus)    plus.classList.add('hidden');
                    if (minus)   minus.classList.remove('hidden');
                    if (chevron) chevron.classList.remove('rotate-180');
                }
            } else {
                catRow.style.display = 'none';
            }

            if (term === '') {
                items.forEach(itemRow => {
                    itemRow.style.display = '';
                    if (state.mode === 'readonly') {
                        const isHidden = state.collapsed[catId];
                        itemRow.classList.toggle('hidden', !!isHidden);
                        const plus    = catRow.querySelector('.cat-icon-plus');
                        const minus   = catRow.querySelector('.cat-icon-minus');
                        const chevron = catRow.querySelector('.cat-chevron');
                        if (plus)    plus.classList.toggle('hidden', !isHidden);
                        if (minus)   minus.classList.toggle('hidden', !!isHidden);
                        if (chevron) chevron.classList.toggle('rotate-180', !!isHidden);
                    }
                });
            }
        });
    });
}
