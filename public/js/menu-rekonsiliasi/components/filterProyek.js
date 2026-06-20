export const FilterProyek = {
    init: () => {
        const filterNama = document.getElementById('filter-nama');
        const clearNama = document.getElementById('clear-nama');

        const filterLokasi = document.getElementById('filter-lokasi');
        const clearLokasi = document.getElementById('clear-lokasi');

        const filterTahun = document.getElementById('filter-tahun');
        const clearTahun = document.getElementById('clear-tahun');

        const emptyState = document.getElementById('empty-state');

        function applyFilters() {
            const namaVal = filterNama ? filterNama.value.toLowerCase() : '';

            const isLokasiSelected = filterLokasi && filterLokasi.value !== "";
            const lokasiVal = isLokasiSelected ? filterLokasi.options[filterLokasi.selectedIndex]?.text.toLowerCase() : "";

            const isTahunSelected = filterTahun && filterTahun.value !== "";
            const tahunVal = isTahunSelected ? filterTahun.value.toLowerCase() : "";

            // Toggle clear buttons
            if (clearNama) clearNama.classList.toggle('hidden', namaVal === '');
            if (clearLokasi) clearLokasi.classList.toggle('hidden', !isLokasiSelected);
            if (clearTahun) clearTahun.classList.toggle('hidden', !isTahunSelected);

            const cards = document.querySelectorAll('.proyek-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cNama = card.getAttribute('data-nama') || '';
                const cLokasi = card.getAttribute('data-lokasi') || '';
                const cTahun = card.getAttribute('data-tahun') || '';

                const matchNama = !namaVal || cNama.includes(namaVal);
                const matchLokasi = !isLokasiSelected || cLokasi.includes(lokasiVal);
                const matchTahun = !tahunVal || cTahun.includes(tahunVal);

                if (matchNama && matchLokasi && matchTahun) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (emptyState) {
                if (visibleCount === 0 && cards.length > 0) {
                    emptyState.classList.remove('hidden');
                } else {
                    emptyState.classList.add('hidden');
                }
            }
        }

        function resetPrelineSelect(selectId) {
            const el = document.getElementById(selectId);
            if (el) {
                el.value = '';
                el.dispatchEvent(new Event('change', { bubbles: true }));

                const wrap = el.closest('.relative');
                if (wrap) {
                    const titleSpan = wrap.querySelector('[data-title]');
                    const placeholder = JSON.parse(el.getAttribute('data-hs-select') || '{}').placeholder || 'Semua';
                    if (titleSpan) titleSpan.innerHTML = '<span class="text-gray-500">' + placeholder + '</span>';

                    const dropdown = wrap.querySelector('.hs-select-dropdown');
                    if (dropdown) {
                        dropdown.querySelectorAll('.hs-selected').forEach(n => {
                            n.classList.remove('hs-selected');
                            n.classList.remove('bg-gray-100');
                            const check = n.querySelector('svg');
                            if (check && check.parentElement) check.parentElement.classList.add('hidden');
                        });
                    }
                }
                applyFilters();
            }
        }

        if (filterNama) {
            filterNama.addEventListener('input', applyFilters);
            if (clearNama) {
                clearNama.addEventListener('click', () => {
                    filterNama.value = '';
                    applyFilters();
                });
            }
        }

        if (filterTahun) {
            filterTahun.addEventListener('change', applyFilters);
            if (clearTahun) {
                clearTahun.addEventListener('click', (e) => {
                    e.stopPropagation();
                    resetPrelineSelect('filter-tahun');
                });
            }
        }

        if (filterLokasi) {
            filterLokasi.addEventListener('change', applyFilters);
            if (clearLokasi) {
                clearLokasi.addEventListener('click', (e) => {
                    e.stopPropagation();
                    resetPrelineSelect('filter-lokasi');
                });
            }
        }
    }
};
