// Modal Elements
const modalTambah = document.getElementById('modalTambahHarga');
const modalEdit = document.getElementById('modalEditHarga');
const formTambah = document.getElementById('formTambahHarga');
const formEdit = document.getElementById('formEditHarga');

// Format number to Rupiah
function formatRupiah(angka, prefix) {
    let number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
}

// Attach Event Listeners for Formatting
document.addEventListener('DOMContentLoaded', function() {
    const inputTambah = document.getElementById('tambah_harga');
    if (inputTambah) {
        inputTambah.addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });
    }

    const inputEdit = document.getElementById('edit_harga');
    if (inputEdit) {
        inputEdit.addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    $('#tambah_supplier_id').select2({
        dropdownParent: $('#modalTambahHarga'),
        width: '100%',
        placeholder: 'Pilih supplier'
    });
    $('#tambah_material_id').select2({
        dropdownParent: $('#modalTambahHarga'),
        width: '100%',
        placeholder: 'Pilih material'
    }).on('change', function() {
        autoFillMaterialDetails(this, 'tambah');
    });

    $('#edit_supplier_id').select2({
        dropdownParent: $('#modalEditHarga'),
        width: '100%'
    });
    $('#edit_material_id').select2({
        dropdownParent: $('#modalEditHarga'),
        width: '100%'
    }).on('change', function() {
        autoFillMaterialDetails(this, 'edit');
    });
});

// Helper for Auto-filling material details (Satuan & Spesifikasi)
function autoFillMaterialDetails(selectElement, type) {
    const materialId = selectElement.value;
    const material = window.materialsData.find(m => m.id == materialId);
    
    if (material) {
        if (type === 'tambah') {
            document.getElementById('tambah_spesifikasi').value = material.spesifikasi || '-';
            document.getElementById('tambah_satuan').value = material.satuan || '-';
        } else if (type === 'edit') {
            document.getElementById('edit_spesifikasi').value = material.spesifikasi || '-';
            document.getElementById('edit_satuan').value = material.satuan || '-';
        }
    }
}

// Open Modals
function openTambahModal() {
    formTambah.reset();
    $('#tambah_supplier_id').val('').trigger('change');
    $('#tambah_material_id').val('').trigger('change');
    document.getElementById('tambah_spesifikasi').value = '';
    document.getElementById('tambah_satuan').value = '';
    document.getElementById('tambah_harga').value = '';
    modalTambah.classList.remove('hidden');
    modalTambah.classList.add('flex');
}

function openEditModal(harga) {
    formEdit.reset();
    document.getElementById('edit_id').value = harga.id;
    $('#edit_supplier_id').val(harga.supplier_id).trigger('change');
    $('#edit_material_id').val(harga.material_id).trigger('change');
    document.getElementById('edit_harga').value = formatRupiah(harga.harga.toString());
    
    modalEdit.classList.remove('hidden');
    modalEdit.classList.add('flex');
}

// Close Modals
function closeTambahModal() {
    modalTambah.classList.add('hidden');
    modalTambah.classList.remove('flex');
}

function closeEditModal() {
    modalEdit.classList.add('hidden');
    modalEdit.classList.remove('flex');
}

// Submit Handlers
function submitTambahHarga() {
    const formData = new FormData(formTambah);
    const data = Object.fromEntries(formData.entries());
    
    // Hilangkan titik (ribuan) agar menjadi integer utuh
    if (data.harga) {
        data.harga = data.harga.replace(/\./g, '');
    }
    
    // Validasi basic
    if (!data.supplier_id || !data.material_id || !data.harga) {
        window.Toast.show('Supplier, Material, dan Harga wajib diisi!', 'warning');
        return;
    }

    fetch('/purchasing/master-data/harga/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            window.Toast.show(res.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            window.Toast.show(res.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.Toast.show('Terjadi kesalahan sistem', 'error');
    });
}

function submitEditHarga() {
    const formData = new FormData(formEdit);
    const data = Object.fromEntries(formData.entries());
    const id = data.id;

    // Hilangkan titik (ribuan) agar menjadi integer utuh
    if (data.harga) {
        data.harga = data.harga.replace(/\./g, '');
    }

    // Validasi basic
    if (!data.supplier_id || !data.material_id || !data.harga) {
        window.Toast.show('Supplier, Material, dan Harga wajib diisi!', 'warning');
        return;
    }

    fetch(`/purchasing/master-data/harga/update/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            window.Toast.show(res.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            window.Toast.show(res.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.Toast.show('Terjadi kesalahan sistem', 'error');
    });
}

function deleteHarga(id) {
    window.confirmAction('Hapus Harga?', 'Data yang dihapus tidak dapat dikembalikan!', 'Ya, Hapus').then((isConfirmed) => {
        if (isConfirmed) {
            fetch(`/purchasing/master-data/harga/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    window.Toast.show(res.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    window.Toast.show(res.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.Toast.show('Terjadi kesalahan sistem', 'error');
            });
        }
    })
}

// Search functionality
document.getElementById('searchHarga').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#hargaTableBody tr');
    
    rows.forEach(row => {
        // Kolom 1 adalah Nama Material, Kolom 2 adalah Supplier
        let material = row.cells[1]?.textContent.toLowerCase() || '';
        let supplier = row.cells[2]?.textContent.toLowerCase() || '';
        
        if (material.indexOf(filter) > -1 || supplier.indexOf(filter) > -1) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
