<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar') ?>
<header class="bg-primary text-white py-5">
    <h1 class="text-center text-3xl md:text-4xl font-bold tracking-wide">PROYEK BARU</h1>
</header>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="mx-auto max-w-6xl">
    <div class="rounded-lg bg-white p-6 shadow-md ring-1 ring-black/5 md:p-8">

        <div class="mb-6 text-center">
            <h2 class="text-lg font-extrabold tracking-wide text-text-primary">LENGKAPI PROFIL PROYEK</h2>
        </div>

        <form action="<?= base_url('proyek/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr]">

                <!-- LEFT -->
                <div class="space-y-5">

                    <!-- Upload Foto -->
                    <div class="overflow-hidden border border-gray-200">
                        <label class="block cursor-pointer">
                            <input
                                id="foto_proyek"
                                type="file"
                                name="foto_proyek"
                                accept="image/png,image/jpeg"
                                class="hidden" />

                            <!-- Frame biru -->
                            <div class="relative h-64 bg-primary text-white">

                                <!-- Default image / preview -->
                                <img
                                    id="fotoPreview"
                                    src="<?= base_url('assets/images/BackgroundLogin.png') ?>"
                                    alt="Preview"
                                    class="absolute inset-0 h-full w-full object-cover opacity-25" />

                                <!-- Konten tengah -->
                                <div class="relative z-10 flex h-full flex-col items-center justify-center text-center px-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20">
                                        <i class="fa-solid fa-cloud-arrow-up text-3xl"></i>
                                    </div>

                                    <div class="mt-4 text-2xl font-extrabold tracking-wide">
                                        Unggah Foto
                                    </div>

                                    <!-- Teks format masuk ke frame biru -->
                                    <div class="mt-2 text-sm font-medium text-white/80">
                                        JPG/PNG, max 2MB
                                    </div>
                                </div>

                                <!-- Optional: overlay gelap biar mirip gambar 2 -->
                                <div class="absolute inset-0 bg-black/20"></div>
                            </div>
                        </label>
                    </div>

                    <!-- Tambah Dokumen -->
                    <input
                        id="dokumenInput"
                        type="file"
                        name="dokumen[]"
                        class="hidden"
                        multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" />

                    <button
                        type="button"
                        id="btnTambahDokumen"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-primary px-4 py-2 text-white shadow-md transition hover:bg-primary/90">
                        <i class="fa-solid fa-file-circle-plus"></i>
                        <span class="text-sm font-semibold">Tambah Dokumen</span>
                    </button>

                    <!-- Daftar Dokumen -->
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <div class="bg-gray-100 px-4 py-2 text-sm font-semibold text-text-primary text-center">
                            Daftar Dokumen
                        </div>

                        <div id="dokumenEmpty" class="flex items-center justify-center p-6">
                            <div class="text-center text-gray-400">
                                <i class="fa-regular fa-file-lines text-5xl text-primary"></i>
                                <div class="mt-3 text-sm text-primary">Belum ada dokumen</div>
                            </div>
                        </div>

                        <!-- list -->
                        <div id="dokumenList" class="hidden divide-y divide-gray-200"></div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="space-y-6">

                    <!-- Profil Proyek -->
                    <div class="space-y-4">

                        <div>
                            <label class="mb-1 block text-md font-semibold text-text-primary">Nama Proyek</label>
                            <input name="nama_proyek" type="text" placeholder="Masukkan Nama Proyek"
                                class="w-full  border border-gray-300 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="mb-1 block text-md font-semibold text-text-primary">Lokasi Proyek</label>
                            <input name="lokasi_proyek" type="text" placeholder="Masukkan Lokasi Proyek"
                                class="w-full  border border-gray-300 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="mb-1 block text-md font-semibold text-text-primary">Jenis Proyek</label>
                            <input name="jenis_proyek" type="text" placeholder="Masukkan Jenis Proyek"
                                class="w-full  border border-gray-300 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>

                        <!-- DATEPICKER ROW (SIMPLE - PRELINE) -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            <!-- Tanggal Mulai -->
                            <div>
                                <label class="mb-1 block text-md font-semibold text-text-primary">Tanggal Mulai</label>

                                <input
                                    name="tanggal_mulai"
                                    class="hs-datepicker py-3 px-4 block w-full bg-white border border-gray-300 sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    type="text"
                                    placeholder="YYYY.MM.DD"
                                    autocomplete="off"
                                    onkeydown="return false"
                                    data-hs-datepicker='{
                                      "type": "default",
                                      "applyUtilityClasses": true,
                                      "dateMax": "2050-12-31",
                                      "mode": "custom-select",
                                      "templates": {
                                        "arrowPrev": "<button data-vc-arrow=\"prev\"><svg class=\"shrink-0 size-4\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m15 18-6-6 6-6\"></path></svg></button>",
                                        "arrowNext": "<button data-vc-arrow=\"next\"><svg class=\"shrink-0 size-4\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m9 18 6-6-6-6\"></path></svg></button>"
                                      }
                                    }'>
                            </div>

                            <!-- Estimasi Selesai -->
                            <div>
                                <label class="mb-1 block text-md font-semibold text-text-primary">Estimasi Selesai</label>

                                <input
                                    name="estimasi_selesai"
                                    class="hs-datepicker py-3 px-4 block w-full bg-white border border-gray-300 sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    type="text"
                                    placeholder="YYYY.MM.DD"
                                    autocomplete="off"
                                    onkeydown="return false"
                                    data-hs-datepicker='{
                                      "type": "default",
                                      "applyUtilityClasses": true,
                                      "dateMax": "2050-12-31",
                                      "mode": "custom-select",
                                      "templates": {
                                        "arrowPrev": "<button data-vc-arrow=\"prev\"><svg class=\"shrink-0 size-4\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m15 18-6-6 6-6\"></path></svg></button>",
                                        "arrowNext": "<button data-vc-arrow=\"next\"><svg class=\"shrink-0 size-4\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m9 18 6-6-6-6\"></path></svg></button>"
                                      }
                                    }'>
                            </div>

                        </div>
                    </div>

                    <div class="h-px bg-gray-200"></div>

                    <!-- Informasi Owner -->
                    <div>
                        <h3 class="mb-3 text-sm font-bold text-text-primary">Informasi Owner</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-md font-semibold text-text-primary">Nama Owner / Klien</label>
                                <input name="nama_owner" type="text" placeholder="Masukkan Nama Owner / Klien"
                                    class="w-full  border border-gray-300 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>

                            <div>
                                <label class="mb-1 block text-md font-semibold text-text-primary">Perusahaan</label>
                                <input name="perusahaan" type="text" placeholder="Masukkan Nama Perusahaan"
                                    class="w-full  border border-gray-300 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>

                            <div>
                                <label class="mb-1 block text-md font-semibold text-text-primary">Nomor Kontrak</label>
                                <input name="nomor_kontrak" type="text" placeholder="Masukkan Nomor Kontrak"
                                    class="w-full  border border-gray-300 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>

                            <div>
                                <label class="mb-1 block text-md font-semibold text-text-primary">Keterangan Lain</label>
                                <input name="keterangan_lain" type="text" placeholder="Masukkan Keterangan Lain"
                                    class="w-full  border border-gray-300 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <a href="<?= base_url('proyek') ?>"
                                class="inline-flex items-center justify-center rounded-full border border-gray-300 bg-white px-6 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 gap-2">
                                <i class="fa-solid fa-arrow-rotate-left"></i>
                                Batal
                            </a>

                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-full bg-primary px-6 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-primary/90 gap-2">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Simpan
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // =========================
        // 1) UPLOAD FOTO (preview)
        // =========================
        const fotoInput = document.getElementById('foto_proyek');
        const fotoPreview = document.getElementById('fotoPreview');

        if (fotoInput && fotoPreview) {
            fotoInput.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (!file) return;

                // validasi ukuran max 2MB
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran foto maksimal 2MB');
                    fotoInput.value = '';
                    return;
                }

                // validasi tipe
                if (!['image/jpeg', 'image/png'].includes(file.type)) {
                    alert('Format foto harus JPG atau PNG');
                    fotoInput.value = '';
                    return;
                }

                const url = URL.createObjectURL(file);
                fotoPreview.src = url;

                // bikin preview lebih jelas
                fotoPreview.classList.remove('opacity-25');
                fotoPreview.classList.add('opacity-60');
            });
        }

        // =========================
        // 2) TAMBAH DOKUMEN (list)
        // =========================
        const btnDok = document.getElementById('btnTambahDokumen');
        const dokInput = document.getElementById('dokumenInput');
        const dokEmpty = document.getElementById('dokumenEmpty');
        const dokList = document.getElementById('dokumenList');

        // simpan file-file yang dipilih agar bisa remove
        let selectedFiles = [];

        function formatSize(bytes) {
            const mb = bytes / (1024 * 1024);
            return mb >= 1 ? `${mb.toFixed(2)} MB` : `${(bytes / 1024).toFixed(0)} KB`;
        }

        function syncInputFiles() {
            if (!dokInput) return;
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            dokInput.files = dt.files;
        }

        function renderDokumen() {
            if (!dokEmpty || !dokList) return;

            if (!selectedFiles.length) {
                dokEmpty.classList.remove('hidden');
                dokList.classList.add('hidden');
                dokList.innerHTML = '';
                syncInputFiles();
                return;
            }

            dokEmpty.classList.add('hidden');
            dokList.classList.remove('hidden');

            dokList.innerHTML = selectedFiles.map((f, idx) => `
        <div class="flex items-center justify-between gap-3 px-4 py-3">
          <div class="min-w-0">
            <div class="truncate text-sm font-semibold text-slate-800">${f.name}</div>
            <div class="text-xs text-slate-500">${formatSize(f.size)}</div>
          </div>

          <button type="button"
            data-remove="${idx}"
            class="shrink-0 rounded-full border border-gray-300 bg-white px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">
            Hapus
          </button>
        </div>
      `).join('');

            syncInputFiles();
        }

        if (btnDok && dokInput) {
            btnDok.addEventListener('click', () => dokInput.click());

            dokInput.addEventListener('change', () => {
                const files = Array.from(dokInput.files || []);
                if (!files.length) return;

                // aturan (ubah sesuai kebutuhan)
                const MAX_FILES = 10;
                const MAX_SIZE = 10 * 1024 * 1024; // 10MB per file

                for (const f of files) {
                    if (f.size > MAX_SIZE) {
                        alert(`File "${f.name}" terlalu besar. Maksimal 10MB.`);
                        continue;
                    }
                    selectedFiles.push(f);
                }

                if (selectedFiles.length > MAX_FILES) {
                    selectedFiles = selectedFiles.slice(0, MAX_FILES);
                    alert(`Maksimal ${MAX_FILES} dokumen.`);
                }

                renderDokumen();
            });
        }

        if (dokList) {
            dokList.addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-remove]');
                if (!btn) return;

                const idx = Number(btn.dataset.remove);
                selectedFiles.splice(idx, 1);
                renderDokumen();
            });
        }

        // render awal
        renderDokumen();
    });
</script>
<?= $this->endSection() ?>