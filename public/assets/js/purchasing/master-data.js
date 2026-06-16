const modalSupplier = document.getElementById('modal-supplier');
const formSupplier = document.getElementById('form-supplier');
const modalTitle = document.getElementById('modal-supplier-title');

function openTambahModal() {
    formSupplier.reset();
    document.getElementById('supplier_id').value = '';
    modalTitle.textContent = 'Tambah Supplier';
    HSOverlay.open(modalSupplier);
}

function openEditModal(supplier) {
    formSupplier.reset();
    document.getElementById('supplier_id').value = supplier.id;
    document.getElementById('nama_supplier').value = supplier.nama_supplier;
    document.getElementById('telepon').value = supplier.telepon || '';
    document.getElementById('email').value = supplier.email || '';
    document.getElementById('alamat').value = supplier.alamat || '';
    document.getElementById('npwp').value = supplier.npwp || '';
    document.getElementById('rekening_bank').value = supplier.rekening_bank || '';
    
    modalTitle.textContent = 'Edit Supplier';
    HSOverlay.open(modalSupplier);
}

function saveSupplier(e) {
    e.preventDefault();
    
    const id = document.getElementById('supplier_id').value;
    const isEdit = id !== '';
    const url = isEdit ? `/purchasing/master-data/update/${id}` : '/purchasing/master-data/store';
    const method = isEdit ? 'PUT' : 'POST';
    
    const formData = new FormData(formSupplier);
    const data = Object.fromEntries(formData.entries());
    
    // Disable button
    const btnSave = document.getElementById('btn-save-supplier');
    const originalText = btnSave.innerHTML;
    btnSave.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    btnSave.disabled = true;

    // Use fetch or jQuery ajax. Since jQuery is included, let's use it for simplicity with PUT override if needed.
    // Fetch is cleaner.
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: res.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: res.message || 'Terjadi kesalahan'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan sistem'
        });
    })
    .finally(() => {
        btnSave.innerHTML = originalText;
        btnSave.disabled = false;
    });
}

function deleteSupplier(id) {
    Swal.fire({
        title: 'Hapus Supplier?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/purchasing/master-data/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Dihapus!',
                        text: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
            });
        }
    })
}

// Search functionality
document.getElementById('searchSupplier').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#supplierTableBody tr');
    
    rows.forEach(row => {
        // Assume column 1 is Nama Supplier
        let nama = row.cells[1]?.textContent.toLowerCase();
        if (nama) {
            if (nama.indexOf(filter) > -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});
