

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