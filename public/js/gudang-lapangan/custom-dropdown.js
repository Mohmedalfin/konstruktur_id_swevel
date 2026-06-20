export function initCustomDropdowns() {
    document.querySelectorAll('.custom-dropdown-container').forEach(container => {
        const select = container.querySelector('select');
        if (!select) return;

        const btn = container.querySelector('.custom-dropdown-btn');
        const label = container.querySelector('.custom-dropdown-label');
        const iconContainer = container.querySelector('.custom-dropdown-icon');
        const menu = container.querySelector('.custom-dropdown-menu');
        
        if (!btn || !label || !iconContainer || !menu) return;

        const defaultText = select.options.length > 0 ? select.options[0].text : 'Filter';
        
        // Build menu options based on <select> options
        let optionsHtml = '';
        Array.from(select.options).forEach((opt, idx) => {
            if (idx === 0) return; // Skip the placeholder option
            optionsHtml += `
                <button type="button" class="custom-dropdown-option flex justify-between items-center w-full py-2 px-3 text-xs text-slate-300 font-medium cursor-pointer hover:bg-slate-800 hover:text-white rounded-lg focus:outline-none focus:bg-slate-800 transition-colors" data-value="${opt.value}">
                    <span class="truncate pointer-events-none">${opt.text}</span>
                    <svg class="w-3.5 h-3.5 text-white hidden check-icon pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </button>
            `;
        });
        menu.innerHTML = optionsHtml;

        function updateUI(isOpen) {
            const hasValue = select.value !== "";
            const optionBtns = menu.querySelectorAll('.custom-dropdown-option');
            
            // Update Label text and color
            if (hasValue) {
                const selectedOpt = select.options[select.selectedIndex];
                label.textContent = selectedOpt ? selectedOpt.text : defaultText;
                label.classList.add('text-white');
                label.classList.remove('text-slate-400');
            } else {
                label.textContent = defaultText;
                label.classList.remove('text-white');
                label.classList.add('text-slate-400');
            }

            // Update Icon (Chevron or X)
            if (hasValue) {
                iconContainer.innerHTML = `
                    <svg class="w-4 h-4 opacity-100 text-white clear-filter-btn cursor-pointer hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                `;
            } else {
                iconContainer.innerHTML = `
                    <svg class="w-3.5 h-3.5 opacity-70 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                `;
            }

            // Update Menu Active States
            optionBtns.forEach(ob => {
                const val = ob.dataset.value;
                const isSelected = val === select.value;
                if (isSelected) {
                    ob.classList.add('bg-blue-600', 'text-white');
                    ob.classList.remove('text-slate-300');
                    ob.querySelector('.check-icon').classList.remove('hidden');
                } else {
                    ob.classList.remove('bg-blue-600', 'text-white');
                    ob.classList.add('text-slate-300');
                    ob.querySelector('.check-icon').classList.add('hidden');
                }
            });
        }

        function toggleMenu() {
            const isHidden = menu.classList.contains('hidden');
            // Close all other dropdown menus on the page first
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });
            // Update icons for all other dropdowns
            document.querySelectorAll('.custom-dropdown-container').forEach(c => {
                if (c !== container) {
                    const sel = c.querySelector('select');
                    const iconCont = c.querySelector('.custom-dropdown-icon');
                    if (sel && iconCont && sel.value === "") {
                        iconCont.innerHTML = `<svg class="w-3.5 h-3.5 opacity-70 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>`;
                    }
                }
            });

            if (isHidden) {
                menu.classList.remove('hidden');
                updateUI(true);
            } else {
                closeMenu();
            }
        }

        function closeMenu() {
            menu.classList.add('hidden');
            updateUI(false);
        }

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (e.target.closest('.clear-filter-btn')) {
                select.value = "";
                select.dispatchEvent(new Event('change', { bubbles: true }));
                closeMenu();
                return;
            }
            toggleMenu();
        });

        menu.addEventListener('click', (e) => {
            e.stopPropagation();
            const optionBtn = e.target.closest('.custom-dropdown-option');
            if (!optionBtn) return;
            
            select.value = optionBtn.dataset.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            closeMenu();
        });

        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                closeMenu();
            }
        });
        
        // Listen to external select changes
        select.addEventListener('change', () => {
            updateUI(menu.classList.contains('hidden') ? false : true);
        });

        // Initial setup
        updateUI(false);
    });
}
