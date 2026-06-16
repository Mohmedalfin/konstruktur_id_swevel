// Modal Elements
const modalTambah = document.getElementById('modalTambahMaterial');
const modalEdit = document.getElementById('modalEditMaterial');
const formTambah = document.getElementById('formTambahMaterial');
const formEdit = document.getElementById('formEditMaterial');

document.addEventListener('DOMContentLoaded', function() {
    $('#tambah_kategori').select2({
        dropdownParent: $('#modalTambahMaterial'),
        width: '100%',
        placeholder: 'Pilih Kategori'
    });

    $('#edit_kategori').select2({
        dropdownParent: $('#modalEditMaterial'),
        width: '100%'
    });
});

// Open Modals
function openTambahModal() {
    formTambah.reset();
    $('#tambah_kategori').val('').trigger('change');
    document.getElementById('tambah_satuan').value = '';
    modalTambah.classList.remove('hidden');
    modalTambah.classList.add('flex');
}

function openEditModal(material) {
    formEdit.reset();
    document.getElementById('edit_id').value = material.id;
    document.getElementById('edit_nama_material').value = material.nama_material;
    $('#edit_kategori').val(material.kategori).trigger('change');
    document.getElementById('edit_satuan').value = material.satuan;
    document.getElementById('edit_spesifikasi').value = material.spesifikasi || '';
    
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
function submitTambahMaterial() {
    const formData = new FormData(formTambah);
    const data = Object.fromEntries(formData.entries());
    
    // Validasi basic
    if (!data.nama_material || !data.kategori || !data.satuan) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Nama, Kategori, dan Satuan wajib diisi!'
        });
        return;
    }

    fetch('/purchasing/master-data/material/store', {
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
            Swal.fire('Gagal!', res.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
    });
}

function submitEditMaterial() {
    const formData = new FormData(formEdit);
    const data = Object.fromEntries(formData.entries());
    const id = data.id;

    // Validasi basic
    if (!data.nama_material || !data.kategori || !data.satuan) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Nama, Kategori, dan Satuan wajib diisi!'
        });
        return;
    }

    fetch(`/purchasing/master-data/material/update/${id}`, {
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
            Swal.fire('Gagal!', res.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
    });
}

function deleteMaterial(id) {
    Swal.fire({
        title: 'Hapus Material?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/purchasing/master-data/material/delete/${id}`, {
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
document.getElementById('searchMaterial').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#materialTableBody tr');
    
    rows.forEach(row => {
        // Kolom 1 adalah Nama Material (index 1 karena No index 0)
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
