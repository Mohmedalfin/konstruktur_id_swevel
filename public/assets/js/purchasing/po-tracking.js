const modalDetail = document.getElementById('modalDetailPO');
let currentPoId = null;

// Helper to format Rupiah
function formatRupiah(angka) {
    let number_string = angka.toString().replace(/[^,\d]/g, ''),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah ? 'Rp ' + rupiah : '';
}

// Helper to format Date
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const d = date.getDate();
    const m = months[date.getMonth()];
    const y = date.getFullYear();
    const h = String(date.getHours()).padStart(2, '0');
    const min = String(date.getMinutes()).padStart(2, '0');
    
    return `${d} ${m} ${y} ${h}:${min} WIB`;
}

function openDetailModal(id) {
    currentPoId = id;
    
    // Fetch data
    fetch(`/purchasing/po-tracking/detail/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            renderModalData(data.data);
            modalDetail.classList.remove('hidden');
            modalDetail.classList.add('flex');
        } else {
            Swal.fire('Error', 'Data tidak ditemukan', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
    });
}

function closeDetailModal() {
    modalDetail.classList.add('hidden');
    modalDetail.classList.remove('flex');
    currentPoId = null;
}

function renderModalData(po) {
    document.getElementById('detail_po_number').textContent = po.po_number;
    document.getElementById('detail_supplier_name').textContent = 'Supplier: ' + po.nama_supplier;
    
    // Status Badge
    const statusContainer = document.getElementById('detail_status_container');
    if (po.status === 'diproses') {
        statusContainer.innerHTML = `<span class="bg-[#fef08a] text-[#854d0e] px-3 py-1.5 rounded text-[11px] font-black uppercase tracking-wide">STATUS: DIPROSES</span>`;
    } else if (po.status === 'dalam pengiriman') {
        statusContainer.innerHTML = `<span class="bg-[#eff6ff] text-[#2563eb] px-3 py-1.5 rounded text-[11px] font-black uppercase tracking-wide">STATUS: DALAM PENGIRIMAN</span>`;
    } else {
        statusContainer.innerHTML = `<span class="bg-[#bbf7d0] text-[#166534] px-3 py-1.5 rounded text-[11px] font-black uppercase tracking-wide">STATUS: SELESAI TIBA</span>`;
    }

    // Render Items
    const tbody = document.getElementById('detail_items_body');
    tbody.innerHTML = '';
    po.items.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-2 py-3 text-[13px] font-semibold text-[#1e293b]">${item.nama_material}</td>
            <td class="px-2 py-3 text-[13px] font-bold text-center text-[#1e293b]">${item.volume}</td>
            <td class="px-2 py-3 text-[13px] font-bold text-center text-[#1e293b]">${item.satuan}</td>
            <td class="px-2 py-3 text-[13px] font-bold text-center text-[#1e293b]">${item.spesifikasi || '-'}</td>
            <td class="px-2 py-3 text-[13px] font-bold text-right text-[#1e293b]">${formatRupiah(item.sub_total)}</td>
        `;
        tbody.appendChild(tr);
    });
    
    document.getElementById('detail_total_harga').textContent = formatRupiah(po.total_nilai);

    // Render Stepper & CTA
    renderStepperAndCTA(po);
}

function renderStepperAndCTA(po) {
    const s1_icon = document.getElementById('step_1_icon');
    const s2_icon = document.getElementById('step_2_icon');
    const s3_icon = document.getElementById('step_3_icon');
    const s4_icon = document.getElementById('step_4_icon');
    
    const s2_title = document.getElementById('step_2_title');
    const s3_title = document.getElementById('step_3_title');
    const s4_title = document.getElementById('step_4_title');

    // Reset styles
    [s1_icon, s2_icon, s3_icon, s4_icon].forEach(el => {
        el.className = 'w-12 h-12 rounded-full flex items-center justify-center text-xl shadow-sm transition-colors';
    });
    [s2_title, s3_title, s4_title].forEach(el => {
        el.className = 'text-[13px] font-bold text-gray-500';
    });

    document.getElementById('step_1_date').textContent = formatDate(po.created_at);
    document.getElementById('step_2_desc').innerHTML = `Estimasi Paling Lambat<br>${formatDate(po.estimasi_tanggal).split(' ')[0]} ${formatDate(po.estimasi_tanggal).split(' ')[1]} ${formatDate(po.estimasi_tanggal).split(' ')[2]}`;

    const ctaBox = document.getElementById('cta_box');
    
    // Step 1 is always green
    s1_icon.classList.add('bg-[#22c55e]', 'text-white');
    
    if (po.status === 'diproses') {
        s2_icon.classList.add('bg-[#eab308]', 'text-white'); // yellow
        s2_icon.innerHTML = '<i class="fa-solid fa-hourglass-half"></i>';
        s2_title.classList.replace('text-gray-500', 'text-[#eab308]');
        
        s3_icon.classList.add('bg-gray-300', 'text-white');
        s4_icon.classList.add('bg-gray-300', 'text-white');
        
        // CTA
        ctaBox.style.display = 'flex';
        document.getElementById('cta_icon').className = 'fa-solid fa-box-open';
        document.getElementById('cta_title').textContent = 'Konfirmasi Pengiriman';
        document.getElementById('cta_desc').textContent = 'Klik tombol di samping jika supplier sudah mengonfirmasi pengiriman barang';
        
        const btn = document.getElementById('cta_btn');
        btn.innerHTML = '<i class="fa-solid fa-truck"></i> <span>Tandai Sedang Dikirim</span>';
        btn.className = 'bg-[#0061ff] hover:bg-blue-700 text-white font-bold text-[13px] py-2.5 px-5 rounded-lg flex items-center gap-2 transition-colors shadow-sm';
        btn.onclick = () => updateStatus('dalam pengiriman', 'Kirim Barang', 'Apakah supplier telah mengirim barang?');

    } else if (po.status === 'dalam pengiriman') {
        s2_icon.classList.add('bg-[#22c55e]', 'text-white'); // green check
        s2_icon.innerHTML = '<i class="fa-solid fa-check"></i>';
        s2_title.classList.replace('text-gray-500', 'text-[#1e293b]');
        document.getElementById('step_2_desc').textContent = '';
        
        s3_icon.classList.add('bg-[#eab308]', 'text-white'); // yellow truck
        s3_title.classList.replace('text-gray-500', 'text-[#eab308]');
        
        s4_icon.classList.add('bg-gray-300', 'text-white');

        // CTA
        ctaBox.style.display = 'flex';
        document.getElementById('cta_icon').className = 'fa-solid fa-box-open';
        document.getElementById('cta_title').textContent = 'Konfirmasi Diterima';
        document.getElementById('cta_desc').textContent = 'Klik tombol di samping jika barang sudah diterima oleh Gudang';
        
        const btn = document.getElementById('cta_btn');
        btn.innerHTML = '<i class="fa-solid fa-location-dot"></i> <span>Tandai Telah Diterima</span>';
        btn.className = 'bg-[#0061ff] hover:bg-blue-700 text-white font-bold text-[13px] py-2.5 px-5 rounded-lg flex items-center gap-2 transition-colors shadow-sm';
        btn.onclick = () => updateStatus('selesai tiba', 'Barang Diterima', 'Apakah barang telah tiba di lokasi proyek?');

    } else if (po.status === 'selesai tiba') {
        s2_icon.classList.add('bg-[#22c55e]', 'text-white'); 
        s2_icon.innerHTML = '<i class="fa-solid fa-check"></i>';
        s2_title.classList.replace('text-gray-500', 'text-[#1e293b]');
        document.getElementById('step_2_desc').textContent = '';
        
        s3_icon.classList.add('bg-[#22c55e]', 'text-white'); 
        s3_title.classList.replace('text-gray-500', 'text-[#1e293b]');
        
        s4_icon.classList.add('bg-[#4ade80]', 'text-white'); // green-400
        s4_title.classList.replace('text-gray-500', 'text-[#4ade80]');
        
        // Hide CTA
        ctaBox.style.display = 'none';
    }
}

function updateStatus(newStatus, title, text) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Konfirmasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/purchasing/po-tracking/status/${currentPoId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            });
        }
    });
}

// Filtering Logic
const searchInput = document.getElementById('searchPO');
const monthInput = document.getElementById('filter-month');
const statusButtons = document.querySelectorAll('.filter-btn');
let currentStatus = 'all';

function filterTable() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const monthTerm = monthInput ? monthInput.value : '';
    const rows = document.querySelectorAll('#poTableBody tr.table-row');
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        let poNum = row.cells[1]?.textContent.toLowerCase() || '';
        let supplier = row.cells[2]?.textContent.toLowerCase() || '';
        let rowStatus = row.dataset.status || '';
        let rowDate = row.dataset.date || '';
        
        let matchSearch = poNum.includes(searchTerm) || supplier.includes(searchTerm);
        let matchMonth = monthTerm === '' || rowDate === monthTerm;
        let matchStatus = currentStatus === 'all';
        
        if (!matchStatus) {
            if (currentStatus === 'diproses') {
                matchStatus = ['diproses', 'proses'].includes(rowStatus.toLowerCase());
            } else if (currentStatus === 'pengiriman') {
                matchStatus = ['pengiriman', 'dalam pengiriman', 'dikirim'].includes(rowStatus.toLowerCase());
            } else if (currentStatus === 'selesai') {
                matchStatus = ['selesai', 'selesai tiba', 'selesai_tiba'].includes(rowStatus.toLowerCase());
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
