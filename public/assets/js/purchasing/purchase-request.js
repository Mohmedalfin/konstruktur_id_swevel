const modalDetail = document.getElementById('modalDetailPR');
const modalCreatePO = document.getElementById('modalCreatePO');
const modalSuccess = document.getElementById('modalSuccessPO');
let currentPrId = null;

// Helper to format Rupiah
function formatRupiah(angka) {
    let parsed = parseFloat(angka);
    if (isNaN(parsed)) parsed = 0;
    let number_string = Math.round(parsed).toString(),
        sisa = number_string.length % 3,
        rupiah = number_string.substr(0, sisa),
        ribuan = number_string.substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    return 'Rp ' + rupiah;
}

function openDetailModal(id) {
    currentPrId = id;
    
    fetch(`/purchasing/purchase-request/detail/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            renderDetailData(data.data);
            modalDetail.classList.remove('hidden');
            modalDetail.classList.add('flex');
        } else {
            window.Toast.show('Data tidak ditemukan', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        window.Toast.show('Terjadi kesalahan sistem', 'error');
    });
}

function closeDetailModal() {
    modalDetail.classList.add('hidden');
    modalDetail.classList.remove('flex');
    currentPrId = null;
}

function renderDetailData(data) {
    const pr = data.pr;
    const items = data.items;
    
    document.getElementById('detail_pr_number').textContent = 'Nomor PR: ' + pr.pr_number;
    
    const statusContainer = document.getElementById('detail_status_container');
    if (pr.status === 'diproses') {
        statusContainer.innerHTML = `<span class="bg-[#eff6ff] text-[#2563eb] px-3 py-1.5 rounded text-[12px] font-black uppercase tracking-wide flex inline-flex items-center gap-2"><span class="w-2 h-2 bg-blue-500 rounded-full"></span> DIPROSES</span>`;
    } else if (pr.status === 'parsial') {
        statusContainer.innerHTML = `<span class="bg-[#fef08a] text-[#854d0e] px-3 py-1.5 rounded text-[12px] font-black uppercase tracking-wide flex inline-flex items-center gap-2"><span class="w-2 h-2 bg-yellow-500 rounded-full"></span> PARSIAL</span>`;
    } else {
        statusContainer.innerHTML = `<span class="bg-[#bbf7d0] text-[#166534] px-3 py-1.5 rounded text-[12px] font-black uppercase tracking-wide flex inline-flex items-center gap-2"><span class="w-2 h-2 bg-green-500 rounded-full"></span> SELESAI</span>`;
    }

    const tbody = document.getElementById('detail_items_body');
    tbody.innerHTML = '';
    
    let hasPending = false;

    items.forEach((item, index) => {
        if (item.status === 'pending') hasPending = true;
        
        const statusHtml = item.status === 'ordered' 
            ? `<span class="bg-[#dcfce3] text-[#166534] px-3 py-1 rounded text-[11px] font-bold inline-flex items-center gap-1 border border-[#86efac]"><i class="fa-solid fa-check"></i> Ordered</span>`
            : `<span class="bg-gray-100 text-gray-600 px-3 py-1 rounded text-[11px] font-bold inline-flex items-center gap-1 border border-gray-300"><i class="fa-regular fa-clock"></i> Pending</span>`;
            
        let displayVolume = parseFloat(item.volume) || 0;
        let displaySatuan = item.satuan_kemasan || item.satuan;
        let konversiFaktor = parseFloat(item.konversi_faktor) || 1;
        
        if (item.satuan_kemasan && konversiFaktor > 0) {
            displayVolume = displayVolume / konversiFaktor;
        }

        const tr = document.createElement('tr');
        tr.className = index % 2 === 0 ? 'bg-[#f8fafc]' : 'bg-[#e2e8f0]';
        tr.innerHTML = `
            <td class="px-3 py-3 text-[13px] font-bold text-center text-[#1e293b] border-r border-gray-300">${index + 1}</td>
            <td class="px-3 py-3 text-[13px] font-semibold text-[#1e293b] border-r border-gray-300">${item.nama_material}</td>
            <td class="px-3 py-3 text-[13px] font-bold text-center text-[#1e293b] border-r border-gray-300">${displayVolume}</td>
            <td class="px-3 py-3 text-[13px] font-bold text-center text-[#1e293b] border-r border-gray-300">${displaySatuan}</td>
            <td class="px-3 py-3 text-[13px] font-bold text-center text-[#1e293b] border-r border-gray-300">${item.spesifikasi || '-'}</td>
            <td class="px-3 py-3 text-center border-r border-gray-300">${statusHtml}</td>
            <td class="px-3 py-3 text-[13px] font-bold text-center text-[#334155]">${item.po_number || '-'}</td>
        `;
        tbody.appendChild(tr);
    });

    const footer = document.getElementById('detail_footer');
    if (hasPending) {
        footer.style.display = 'flex';
    } else {
        footer.style.display = 'none';
    }
}

function openCreatePOModal() {
    // Hide Detail, show Loading/Fetch
    modalDetail.classList.add('hidden');
    modalDetail.classList.remove('flex');
    
    fetch(`/purchasing/purchase-request/pending/${currentPrId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            renderCreatePOData(data.data);
            modalCreatePO.classList.remove('hidden');
            modalCreatePO.classList.add('flex');
        } else {
            window.Toast.show('Data tidak ditemukan', 'error');
            // Re-open detail
            modalDetail.classList.remove('hidden');
            modalDetail.classList.add('flex');
        }
    })
    .catch(err => {
        console.error(err);
        window.Toast.show('Terjadi kesalahan sistem', 'error');
    });
}

function closeCreatePOModal() {
    modalCreatePO.classList.add('hidden');
    modalCreatePO.classList.remove('flex');
    // Re-open Detail
    modalDetail.classList.remove('hidden');
    modalDetail.classList.add('flex');
}

function renderCreatePOData(data) {
    const pr = data.pr;
    const items = data.items;
    
    document.getElementById('create_po_pr_number').textContent = 'Nomor PR: ' + pr.pr_number;
    
    const tbody = document.getElementById('create_po_items_body');
    tbody.innerHTML = '';
    
    items.forEach((item, index) => {
        let options = '<option value="" disabled selected>Pilih Supplier</option>';
        item.available_suppliers.forEach(sup => {
            options += `<option value="${sup.supplier_id}" data-harga="${sup.harga}">${sup.nama_supplier} - ${formatRupiah(sup.harga)}</option>`;
        });

        let displayVolume = parseFloat(item.volume) || 0;
        let displaySatuan = item.satuan_kemasan || item.satuan;
        let konversiFaktor = parseFloat(item.konversi_faktor) || 1;
        
        if (item.satuan_kemasan && konversiFaktor > 0) {
            displayVolume = displayVolume / konversiFaktor;
        }

        const tr = document.createElement('tr');
        tr.className = index % 2 === 0 ? 'bg-[#f8fafc]' : 'bg-[#e2e8f0]';
        tr.innerHTML = `
            <td class="px-3 py-3 text-center border-r border-gray-300">
                <input type="checkbox" class="item-checkbox rounded border-gray-400 text-blue-600 focus:ring-blue-500 cursor-pointer w-5 h-5" value="${item.id}" data-material="${item.id_barang || item.material_id}" data-volume="${item.volume}">
            </td>
            <td class="px-3 py-3 text-[13px] font-semibold text-[#1e293b] border-r border-gray-300">${item.nama_material}</td>
            <td class="px-3 py-3 text-[13px] font-bold text-center text-[#1e293b] border-r border-gray-300">${displayVolume}</td>
            <td class="px-3 py-3 text-[13px] font-bold text-center text-[#1e293b] border-r border-gray-300">${displaySatuan}</td>
            <td class="px-3 py-3 text-[13px] font-bold text-center text-[#1e293b] border-r border-gray-300">${item.spesifikasi || '-'}</td>
            <td class="px-3 py-2 text-center">
                <select class="supplier-select py-1.5 px-3 block w-full border-gray-300 rounded text-[13px] focus:border-blue-500 focus:ring-blue-500 bg-white">
                    ${options}
                </select>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Check All functionality
    const checkAll = document.getElementById('checkAllItems');
    checkAll.checked = false;
    checkAll.onclick = function() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    };
}

function submitCreatePO() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    if (checkboxes.length === 0) {
        window.Toast.show('Pilih minimal satu item untuk dibuatkan PO.', 'warning');
        return;
    }

    const selections = [];
    let valid = true;

    checkboxes.forEach(cb => {
        const tr = cb.closest('tr');
        const select = tr.querySelector('.supplier-select');
        const supplierId = select.value;
        const harga = select.options[select.selectedIndex]?.dataset.harga;

        if (!supplierId) {
            valid = false;
            tr.classList.add('bg-red-100');
        } else {
            tr.classList.remove('bg-red-100');
            selections.push({
                pr_item_id: cb.value,
                material_id: cb.dataset.material,
                volume: cb.dataset.volume,
                supplier_id: supplierId,
                harga: harga
            });
        }
    });

    if (!valid) {
        window.Toast.show('Harap pilih supplier untuk semua item yang dicentang.', 'warning');
        return;
    }

    window.confirmAction('Buat PO?', `Anda akan membuat PO untuk ${selections.length} item. Lanjutkan?`, 'Ya, Buat PO').then((isConfirmed) => {
        if (isConfirmed) {
            // POST to backend
            fetch('/purchasing/purchase-request/generate-po', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    pr_id: currentPrId,
                    selections: selections
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    showSuccessModal(data.created_pos);
                    window.Toast.show('PO berhasil dibuat!', 'success');
                } else {
                    window.Toast.show(data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                window.Toast.show('Terjadi kesalahan sistem', 'error');
            });
        }
    });
}

function showSuccessModal(pos) {
    modalCreatePO.classList.add('hidden');
    modalCreatePO.classList.remove('flex');
    
    const list = document.getElementById('success_po_list');
    list.innerHTML = '';

    pos.forEach(po => {
        list.innerHTML += `
            <div class="border border-gray-200 rounded-lg p-4 text-left shadow-sm flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="text-[#2563eb] font-bold text-[15px]"><i class="fa-solid fa-file-invoice text-xl mr-2"></i> ${po.po_number}</span>
                    <span class="bg-[#eff6ff] text-[#1e40af] px-3 py-1 rounded text-[11px] font-bold text-right">${po.items_desc}</span>
                </div>
                <div class="text-[#334155] font-bold text-[14px]">
                    ${po.supplier_name}
                </div>
            </div>
        `;
    });

    modalSuccess.classList.remove('hidden');
    modalSuccess.classList.add('flex');
}

// Search
// Filtering Logic
const searchInput = document.getElementById('searchPR');
const monthInput = document.getElementById('filter-month');
const statusButtons = document.querySelectorAll('.filter-btn');
let currentStatus = 'all';

function filterTable() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const monthTerm = monthInput ? monthInput.value : '';
    const rows = document.querySelectorAll('#prTableBody tr.table-row');
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        let prNum = row.cells[1]?.textContent.toLowerCase() || '';
        let rowStatus = row.dataset.status || '';
        let rowDate = row.dataset.date || '';
        
        let matchSearch = prNum.includes(searchTerm);
        let matchMonth = monthTerm === '' || rowDate === monthTerm;
        let matchStatus = currentStatus === 'all';
        
        if (!matchStatus) {
            if (currentStatus === 'pending') {
                matchStatus = ['pending', 'draft', 'menunggu'].includes(rowStatus.toLowerCase());
            } else if (currentStatus === 'diproses') {
                matchStatus = ['diproses', 'ordered', 'parsial'].includes(rowStatus.toLowerCase());
            } else if (currentStatus === 'selesai') {
                matchStatus = rowStatus.toLowerCase() === 'selesai';
            }
        }
        
        if (matchSearch && matchMonth && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    const emptyStateRow = document.getElementById('empty-state-row');
    if (emptyStateRow) {
        if (visibleCount === 0 && rows.length > 0) {
            emptyStateRow.style.display = '';
        } else {
            emptyStateRow.style.display = 'none';
        }
    }
}

if (searchInput) searchInput.addEventListener('keyup', filterTable);
if (monthInput) monthInput.addEventListener('change', filterTable);

statusButtons.forEach(btn => {
    btn.addEventListener('click', function() {
        // Update active class
        statusButtons.forEach(b => {
            b.classList.remove('bg-slate-800', 'text-white', 'border-slate-800');
            b.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
        });
        this.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');
        this.classList.add('bg-slate-800', 'text-white', 'border-slate-800');
        
        currentStatus = this.dataset.status;
        filterTable();
    });
});
