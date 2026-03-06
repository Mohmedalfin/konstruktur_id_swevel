/**
 * ajax_tambah_ahs.js
 * Renders the "Tambah AHS" item picker table with search, source filter, and pagination.
 *
 * To connect to a real CI4 endpoint, replace fetchTambahAhsData() with:
 *   const res  = await fetch('/api/pekerjaan?q=' + encodeURIComponent(query));
 *   return res.json();   // { total, data: [{id, nama, satuan, harga, sumber}] }
 */
(function () {
    'use strict';

    /* ============================================================
       CONFIG
    ============================================================ */
    const PAGE_SIZE = 10;

    /* ============================================================
       DOM REFERENCES
    ============================================================ */
    const tbody         = document.getElementById('tambah-ahs-tbody');
    const namaInput     = document.getElementById('tambah-ahs-nama');
    const countEl       = document.getElementById('tambah-ahs-count');
    const paginationEl  = document.getElementById('tambah-ahs-pagination-btns');
    const paginationInfo= document.getElementById('tambah-ahs-pagination-info');
    const submitBtn     = document.getElementById('tambah-ahs-submit-btn');
    const selectedCount = document.getElementById('tambah-ahs-selected-count');
    const customBtn     = document.getElementById('tambah-ahs-custom-btn');
    const sourceBoxes   = document.querySelectorAll('.tambah-ahs-source');

    if (!tbody) return;

    /* ============================================================
       STATE
    ============================================================ */
    const state = {
        page:     1,
        query:    '',
        sources:  [],    
        selected: {},   
    };

    /* ============================================================
       DUMMY DATA
       Replace fetchTambahAhsData() with a real fetch() to connect the DB.
    ============================================================ */
    const allData = [
        { id: 1,  nama: 'Pembuatan gudang semen dan peralatan', satuan: 'm²', harga: 32621.60,  sumber: 'Proyek Terkini' },
        { id: 2,  nama: 'Buangan tanah galian',                 satuan: 'm³', harga: 45000.00,  sumber: 'SNI'            },
        { id: 3,  nama: 'Pengurugan sirtu padat',               satuan: 'm²', harga: 125000.00, sumber: 'SNI'            },
        { id: 4,  nama: 'Penggalian tanah biasa',               satuan: 'm³', harga: 38500.00,  sumber: 'PUPR'           },
        { id: 5,  nama: 'Penggalian tanah keras',               satuan: 'm³', harga: 55000.00,  sumber: 'PUPR'           },
        { id: 6,  nama: 'Pembersihan lapangan',                 satuan: 'm²', harga: 12000.00,  sumber: 'Empiris'        },
        { id: 7,  nama: 'Pekerjaan bowplank',                   satuan: 'm\'', harga: 28500.00, sumber: 'Estimator.id'   },
        { id: 8,  nama: 'Pengecoran pondasi beton K-225',       satuan: 'm³', harga: 950000.00, sumber: 'SNI'            },
        { id: 9,  nama: 'Pemasangan besi tulangan D16 ulir',    satuan: 'kg', harga: 14500.00,  sumber: 'SNI'            },
        { id: 10, nama: 'Bekisting kolom struktur',             satuan: 'm²', harga: 125000.00, sumber: 'Proyek Terkini' },
        { id: 11, nama: 'Pasangan dinding bata merah 1:4',      satuan: 'm²', harga: 185000.00, sumber: 'SNI'            },
        { id: 12, nama: 'Plesteran & acian dinding',            satuan: 'm²', harga: 72000.00,  sumber: 'SNI'            },
        { id: 13, nama: 'Pemasangan keramik lantai 40x40 cm',   satuan: 'm²', harga: 145000.00, sumber: 'Estimator.id'   },
        { id: 14, nama: 'Pekerjaan cat dinding interior',       satuan: 'm²', harga: 55000.00,  sumber: 'Empiris'        },
        { id: 15, nama: 'Pekerjaan cat dinding eksterior',      satuan: 'm²', harga: 68000.00,  sumber: 'Empiris'        },
        { id: 16, nama: 'Pemasangan kusen pintu kayu',          satuan: 'bh', harga: 850000.00, sumber: 'Proyek Terkini' },
        { id: 17, nama: 'Pemasangan daun pintu panel kayu',     satuan: 'bh', harga: 1250000.00,sumber: 'Proyek Terkini' },
        { id: 18, nama: 'Pekerjaan instalasi listrik titik',    satuan: 'ttk',harga: 185000.00, sumber: 'PUPR'           },
        { id: 19, nama: 'Pekerjaan instalasi air bersih',       satuan: 'm\'', harga: 95000.00, sumber: 'PUPR'           },
        { id: 20, nama: 'Saluran drainase beton U-30',          satuan: 'm\'', harga: 320000.00,sumber: 'SNI'            },
        { id: 21, nama: 'Pengaspalan jalan t=5 cm',             satuan: 'm²', harga: 235000.00, sumber: 'SNI'            },
        { id: 22, nama: 'Rabatan beton t=7 cm',                 satuan: 'm²', harga: 178000.00, sumber: 'SNI'            },
        { id: 23, nama: 'Pemasangan paving block',              satuan: 'm²', harga: 165000.00, sumber: 'Estimator.id'   },
        { id: 24, nama: 'Pekerjaan plafon gypsum board 9 mm',   satuan: 'm²', harga: 145000.00, sumber: 'Estimator.id'   },
        { id: 25, nama: 'Rangka atap baja ringan',              satuan: 'm²', harga: 225000.00, sumber: 'SNI'            },
    ];

    /**
     * Simulate AJAX fetch.
     * Replace with: const res = await fetch(`/api/pekerjaan?q=...&sumber=...&page=...`);
     */
    function fetchTambahAhsData(query, sources, page) {
        return new Promise(function (resolve) {
            setTimeout(function () {
                let filtered = allData;

                // Filter by search query
                if (query) {
                    const q = query.toLowerCase();
                    filtered = filtered.filter(function (item) {
                        return item.nama.toLowerCase().includes(q)
                            || item.sumber.toLowerCase().includes(q);
                    });
                }

                // Filter by sumber checkboxes
                if (sources.length > 0) {
                    filtered = filtered.filter(function (item) {
                        return sources.includes(item.sumber);
                    });
                }

                const total    = filtered.length;
                const start    = (page - 1) * PAGE_SIZE;
                const pageData = filtered.slice(start, start + PAGE_SIZE);

                resolve({ total: total, page: page, data: pageData });
            }, 200); // simulate 200ms network latency
        });
    }

    /* ============================================================
       FORMAT HELPERS
    ============================================================ */
    const fmt = function (n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID', { minimumFractionDigits: 2 });
    };

    /* ============================================================
       SUMBER BADGE COLOR MAP
    ============================================================ */
    const sumberColor = {
        'Proyek Terkini': 'bg-blue-100 text-blue-700',
        'SNI':            'bg-emerald-100 text-emerald-700',
        'Empiris':        'bg-amber-100 text-amber-700',
        'PUPR':           'bg-violet-100 text-violet-700',
        'Estimator.id':   'bg-rose-100 text-rose-700',
    };

    /* ============================================================
       RENDER — LOADING
    ============================================================ */
    function renderLoading() {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-10 text-table-subtle text-xs tracking-wide">
                    <svg class="animate-spin w-5 h-5 mx-auto mb-2 text-table-muted" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    Memuat data…
                </td>
            </tr>`;
    }

    /* ============================================================
       RENDER — TABLE ROWS
    ============================================================ */
    function renderRows(result) {
        const { total, page, data } = result;
        const start = (page - 1) * PAGE_SIZE + 1;
        const end   = Math.min(page * PAGE_SIZE, total);

        // Update count label
        if (countEl) {
            countEl.textContent = total > 0
                ? `Menampilkan ${start} sampai ${end} dari ${total.toLocaleString('id-ID')} data`
                : 'Tidak ada data yang cocok';
        }

        // Update pagination info
        if (paginationInfo) {
            paginationInfo.textContent = total > 0
                ? `Halaman ${page} dari ${Math.ceil(total / PAGE_SIZE)}`
                : '';
        }

        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-10 text-table-subtle text-xs italic">
                        Tidak ada data pekerjaan yang cocok.
                    </td>
                </tr>`;
            renderPagination(0, 1);
            return;
        }

        let html = '';
        data.forEach(function (item, idx) {
            const rowNum     = start + idx;
            const isChecked  = !!state.selected[item.id];
            const rowBg      = rowNum % 2 === 0 ? 'bg-table-row' : 'bg-white';
            const checkedRow = isChecked ? 'ring-inset ring-2 ring-primary/30 bg-primary/5' : rowBg;
            const badgeCls   = sumberColor[item.sumber] || 'bg-gray-100 text-gray-600';

            html += `
                <tr class="tambah-ahs-row border-b border-table-border/60 hover:bg-primary/5 transition-colors duration-100 ${isChecked ? 'bg-primary/5' : rowBg}"
                    data-id="${item.id}">
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-center text-table-subtle font-medium tabular-nums">${rowNum}</td>
                    <td class="px-3 md:px-5 py-2.5 md:py-3 font-semibold text-table-strong max-w-0 truncate" title="${item.nama}">${item.nama}</td>
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-center text-table-subtle">${item.satuan}</td>
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] md:text-[10px] font-semibold ${badgeCls}">${item.sumber}</span>
                    </td>
                    <td class="px-3 md:px-5 py-2.5 md:py-3 text-center">
                        <input type="checkbox"
                            class="tambah-ahs-checkbox w-4 h-4 rounded border-table-border text-primary accent-primary cursor-pointer"
                            data-id="${item.id}"
                            data-nama="${item.nama}"
                            data-satuan="${item.satuan}"
                            data-harga="${item.harga}"
                            data-sumber="${item.sumber}"
                            ${isChecked ? 'checked' : ''}/>
                    </td>
                </tr>`;
        });

        tbody.innerHTML = html;
        renderPagination(total, page);
        bindCheckboxes();
    }

    /* ============================================================
       RENDER — PAGINATION BUTTONS
    ============================================================ */
    function renderPagination(total, current) {
        if (!paginationEl) return;
        const totalPages = Math.ceil(total / PAGE_SIZE);

        if (totalPages <= 1) {
            paginationEl.innerHTML = '';
            return;
        }

        const btnBase  = 'inline-flex items-center justify-center w-7 h-7 text-xs rounded-md border transition-all duration-150 focus:outline-none';
        const btnActive= `${btnBase} bg-primary text-white border-primary font-bold`;
        const btnIdle  = `${btnBase} bg-white border-table-border text-table-body hover:bg-primary/10 hover:border-primary/30`;
        const btnDisabled = `${btnBase} bg-white border-table-border text-table-muted opacity-50 cursor-not-allowed pointer-events-none`;

        let html = '';

        // Prev button
        html += `<button class="${current === 1 ? btnDisabled : btnIdle}" data-page="${current - 1}" ${current === 1 ? 'disabled' : ''}>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>`;

        // Page numbers (show at most 5 pages around current)
        const delta  = 2;
        let pagNums  = [];
        for (let p = Math.max(1, current - delta); p <= Math.min(totalPages, current + delta); p++) {
            pagNums.push(p);
        }

        if (pagNums[0] > 1) {
            html += `<button class="${btnIdle}" data-page="1">1</button>`;
            if (pagNums[0] > 2) {
                html += `<span class="px-1 text-table-subtle text-xs">…</span>`;
            }
        }

        pagNums.forEach(function (p) {
            html += `<button class="${p === current ? btnActive : btnIdle}" data-page="${p}">${p}</button>`;
        });

        if (pagNums[pagNums.length - 1] < totalPages) {
            if (pagNums[pagNums.length - 1] < totalPages - 1) {
                html += `<span class="px-1 text-table-subtle text-xs">…</span>`;
            }
            html += `<button class="${btnIdle}" data-page="${totalPages}">${totalPages}</button>`;
        }

        // Next button
        html += `<button class="${current === totalPages ? btnDisabled : btnIdle}" data-page="${current + 1}" ${current === totalPages ? 'disabled' : ''}>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>`;

        paginationEl.innerHTML = html;

        // Bind page buttons
        paginationEl.querySelectorAll('button[data-page]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                state.page = parseInt(btn.dataset.page, 10);
                load();
            });
        });
    }

    /* ============================================================
       BIND CHECKBOXES + ROW CLICK
    ============================================================ */
    function bindCheckboxes() {
        // Row click toggles checkbox
        tbody.querySelectorAll('.tambah-ahs-row').forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.tagName === 'INPUT') return; // let checkbox handle natively
                const cb = row.querySelector('.tambah-ahs-checkbox');
                if (cb) { cb.checked = !cb.checked; cb.dispatchEvent(new Event('change')); }
            });
        });

        tbody.querySelectorAll('.tambah-ahs-checkbox').forEach(function (cb) {
            cb.addEventListener('change', function () {
                const id  = cb.dataset.id;
                const row = cb.closest('tr');

                if (cb.checked) {
                    state.selected[id] = {
                        id:     id,
                        nama:   cb.dataset.nama,
                        satuan: cb.dataset.satuan,
                        harga:  parseFloat(cb.dataset.harga),
                        sumber: cb.dataset.sumber,
                    };
                    row.classList.add('bg-primary/5');
                    row.classList.remove('bg-white', 'bg-table-row');
                } else {
                    delete state.selected[id];
                    const rowIdx = Array.from(tbody.querySelectorAll('tr')).indexOf(row);
                    row.classList.remove('bg-primary/5');
                    row.classList.add(rowIdx % 2 === 0 ? 'bg-table-row' : 'bg-white');
                }

                updateSubmitBar();
            });
        });
    }

    /* ============================================================
       UPDATE SUBMIT BAR
    ============================================================ */
    function updateSubmitBar() {
        const count = Object.keys(state.selected).length;
        if (submitBtn) {
            submitBtn.disabled = count === 0;
        }
        if (selectedCount) {
            selectedCount.textContent = count > 0
                ? `${count} item dipilih`
                : 'Belum ada item dipilih';
        }
    }

    /* ============================================================
       LOAD DATA
    ============================================================ */
    async function load() {
        renderLoading();
        const result = await fetchTambahAhsData(state.query, state.sources, state.page);
        renderRows(result);
    }

    /* ============================================================
       SEARCH — debounced
    ============================================================ */
    let debounceTimer = null;
    function debounce(fn, ms) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fn, ms);
    }

    // Nama Pekerjaan filter
    if (namaInput) {
        namaInput.addEventListener('input', function () {
            debounce(function () {
                state.query = namaInput.value.trim();
                state.page  = 1;
                load();
            }, 300);
        });
    }

    /* ============================================================
       SOURCE FILTER
    ============================================================ */
    sourceBoxes.forEach(function (cb) {
        cb.addEventListener('change', function () {
            state.sources = Array.from(sourceBoxes)
                .filter(function (b) { return b.checked; })
                .map(function (b) { return b.value; });
            state.page = 1;
            load();
        });
    });

    /* ============================================================
       TAMBAH SENDIRI — inline custom row
    ============================================================ */
    if (customBtn) {
        customBtn.addEventListener('click', function () {
            // Insert a blank editable row at the top of tbody
            const existingCustom = tbody.querySelector('.tambah-ahs-custom-row');
            if (existingCustom) {
                existingCustom.querySelector('input[data-field="nama"]').focus();
                return;
            }

            const customRow = document.createElement('tr');
            customRow.className = 'tambah-ahs-custom-row bg-primary/5 border-b-2 border-primary/30';
            customRow.innerHTML = `
                <td class="px-3 md:px-5 py-2.5 text-center text-table-subtle">—</td>
                <td class="px-3 md:px-5 py-2.5">
                    <input type="text" data-field="nama" placeholder="Nama pekerjaan…"
                        class="w-full px-2 py-1.5 text-xs border border-table-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-white text-table-strong"/>
                </td>
                <td class="px-3 md:px-5 py-2.5">
                    <input type="text" data-field="satuan" placeholder="m²"
                        class="w-full px-2 py-1.5 text-xs border border-table-border rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-white text-table-medium"/>
                </td>
                <td class="px-3 md:px-5 py-2.5">
                    <input type="text" data-field="sumber" placeholder="Sumber…" value="Manual"
                        class="w-full px-2 py-1.5 text-xs border border-table-border rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-white text-table-medium"/>
                </td>
                <td class="px-3 md:px-5 py-2.5 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <button class="custom-add-confirm inline-flex items-center justify-center w-7 h-7 rounded-lg bg-primary hover:bg-primary/80 text-white transition-colors focus:outline-none" title="Tambahkan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                        <button class="custom-add-cancel inline-flex items-center justify-center w-7 h-7 rounded-lg bg-white hover:bg-red-50 border border-table-border hover:border-red-300 text-table-subtle hover:text-red-500 transition-colors focus:outline-none" title="Batal">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </td>`;

            tbody.insertBefore(customRow, tbody.firstChild);
            customRow.querySelector('input[data-field="nama"]').focus();

            // Cancel
            customRow.querySelector('.custom-add-cancel').addEventListener('click', function () {
                customRow.remove();
            });

            // Confirm — add to selected
            customRow.querySelector('.custom-add-confirm').addEventListener('click', function () {
                const nama   = customRow.querySelector('[data-field="nama"]').value.trim();
                const satuan = customRow.querySelector('[data-field="satuan"]').value.trim() || 'm²';
                const sumber = customRow.querySelector('[data-field="sumber"]').value.trim() || 'Manual';
                const harga  = 0;

                if (!nama) {
                    customRow.querySelector('[data-field="nama"]').focus();
                    return;
                }

                // Generate a temporary negative ID to avoid clashes with real DB IDs
                const tempId = 'custom-' + Date.now();
                state.selected[tempId] = { id: tempId, nama, satuan, harga, sumber };
                customRow.remove();
                updateSubmitBar();
            });
        });
    }

    /* ============================================================
       SUBMIT — "Tambah ke AHS"
       Menyimpan item terpilih ke sessionStorage lalu balik ke RAB.
    ============================================================ */
    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            const items = Object.values(state.selected);
            if (items.length === 0) return;

            // Baca kategori yang memicu panel ini (disimpan oleh ajax_rab.js)
            let catId   = '';
            let catName = '';
            try {
                catId   = sessionStorage.getItem('rab_tambah_ahs_cat')     || '';
                catName = sessionStorage.getItem('rab_tambah_ahs_catname') || '';
            } catch (_) {}

            // Simpan pending items ke sessionStorage agar ajax_rab.js bisa membacanya
            try {
                const payload = {
                    catId:   catId,
                    catName: catName,
                    items:   items
                };
                // Gabungkan dengan pending items yang sudah ada (dari kategori lain)
                let existing = [];
                try {
                    const raw = sessionStorage.getItem('rab_pending_items');
                    if (raw) existing = JSON.parse(raw);
                } catch (_) {}

                // Hapus entry lama untuk catId yang sama (replace)
                existing = existing.filter(function (g) { return g.catId !== catId; });
                existing.push(payload);
                sessionStorage.setItem('rab_pending_items', JSON.stringify(existing));
            } catch (_) {}

            // Visual feedback singkat lalu redirect ke halaman RAB
            submitBtn.textContent = 'Menambahkan…';
            submitBtn.disabled    = true;

            setTimeout(function () {
                // Coba dapatkan URL RAB dari sessionStorage, fallback ke referrer atau default
                let rabUrl = '';
                try { rabUrl = sessionStorage.getItem('rab_return_url') || ''; } catch (_) {}
                if (!rabUrl) {
                    // Gunakan document.referrer jika masih di domain yang sama
                    try {
                        const ref = document.referrer;
                        if (ref && new URL(ref).origin === window.location.origin) {
                            rabUrl = ref;
                        }
                    } catch (_) {}
                }
                if (!rabUrl) rabUrl = '/menu-rap?mode=new';
                window.location.href = rabUrl;
            }, 600);
        });
    }

    /* ============================================================
       AUTO-INIT
    ============================================================ */
    (function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', load);
        } else {
            load();
        }
    })();

})();
