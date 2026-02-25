/**
 * RAB Table Accordion Controller
 * Smooth expand/collapse with icon transitions
 */

/**
 * Toggles the visibility of table sub-rows with smooth CSS transitions.
 * @param {string} categoryId - The unique identifier for the category
 */
function toggleAccordion(categoryId) {
    const expandableContents = document.querySelectorAll(`.subrow-${categoryId} .expand-content`);
    if (expandableContents.length === 0) return;

    const isClosed = expandableContents[0].classList.contains('max-h-0');

    expandableContents.forEach(content => {
        if (isClosed) {
            // OPENING
            content.classList.remove('max-h-0', 'opacity-0', 'py-0');
            content.classList.add('max-h-[60px]', 'opacity-100', 'py-1.5', 'md:py-2');
        } else {
            // CLOSING
            content.classList.remove('max-h-[60px]', 'opacity-100', 'py-1.5', 'md:py-2');
            content.classList.add('max-h-0', 'opacity-0', 'py-0');
        }
    });

    // Update icons
    const iconElement = document.getElementById(`icon-${categoryId}`);
    const chevronElement = document.getElementById(`chevron-${categoryId}`);

    if (iconElement) {
        if (!isClosed) {
            // Closed state: Plus icon
            iconElement.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>`;
        } else {
            // Open state: Minus icon
            iconElement.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>`;
        }
    }

    if (chevronElement) {
        if (!isClosed) {
            // Closed: chevron down + rotate
            chevronElement.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>`;
            chevronElement.classList.add('rotate-0');
            chevronElement.classList.remove('-rotate-180');
        } else {
            // Open: chevron up
            chevronElement.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>`;
            chevronElement.classList.add('-rotate-180');
            chevronElement.classList.remove('rotate-0');
        }
    }

    // Update aria-expanded on the category row
    const categoryRow = document.querySelector(`.rab-category[onclick*="${categoryId}"]`);
    if (categoryRow) {
        categoryRow.setAttribute('aria-expanded', isClosed ? 'true' : 'false');
    }
}

// Keyboard accessibility: allow Enter/Space to toggle accordion
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.rab-category').forEach(row => {
        row.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                row.click();
            }
        });
    });
});