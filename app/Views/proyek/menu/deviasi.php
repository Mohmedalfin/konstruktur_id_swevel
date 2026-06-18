<?php
$requestSource = isset($_GET['source']) ? $_GET['source'] : '';

if ($requestSource === 'purchasing') {
    $layout = 'purchasing/layouts/main';
    $backUrl = base_url('purchasing/purchase-request');
} elseif ($requestSource === 'gudang' || in_array(strtolower($userRole ?? ''), ['gudang', 'logistik'])) {
    $layout = 'gudang/layouts/main';
    $backUrl = base_url('gudang/permintaan');
} else {
    $layout = 'layouts/app';
    $backUrl = base_url('permintaan');
}
?>
<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
<div class="w-full max-w-[85rem] mx-auto py-4 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Laporan Deviasi & Margin Profit</h2>
            <p class="text-sm text-slate-500">Monitoring pengeluaran barang/alat yang melebihi kuota RAP.</p>
        </div>
        <a href="<?= $backUrl ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i> Kembali ke Permintaan
        </a>
    </div>

    <!-- SUMMARY CARD -->
    <div class="bg-white border border-red-200 shadow-sm rounded-xl p-4 md:p-5 mb-6">
        <div class="flex items-center gap-x-4">
            <div class="p-3 bg-red-100 rounded-lg text-red-600">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Estimasi Pemotongan Margin Profit</p>
                <h3 id="total-kerugian" class="text-2xl font-bold text-red-600 mt-1">Rp 0</h3>
            </div>
        </div>
    </div>

    <!-- TABLE SECTION -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col">
        <!-- HEADER (Outside overflow) -->
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <h3 class="text-sm font-bold text-slate-700">Rincian Item Over-Limit</h3>
            <div class="flex gap-2">
                <!-- Custom Searchable Dropdown for Projects -->
                <div class="relative w-64" id="project-filter-container">
                    <!-- Hidden input to store actual value -->
                    <input type="hidden" id="filter-proyek" value="">
                    
                    <!-- Trigger Button -->
                    <button type="button" id="project-filter-trigger" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-700 text-left focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 flex justify-between items-center shadow-sm transition-colors cursor-pointer relative z-10">
                        <span id="project-filter-display" class="truncate">Semua Proyek</span>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] ml-2 transition-transform duration-200" id="project-filter-icon"></i>
                    </button>
                    
                    <!-- Dropdown Panel -->
                    <div id="project-filter-panel" class="hidden absolute right-0 z-50 w-64 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden flex flex-col">
                        <!-- Search Input -->
                        <div class="p-2 border-b border-slate-100 bg-slate-50">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-slate-400 text-[10px]"></i>
                                </div>
                                <input type="text" id="project-search-input" class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-md text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Ketik nama proyek..." autocomplete="off">
                            </div>
                        </div>
                        
                        <!-- Results List -->
                        <div id="project-filter-dropdown" class="max-h-52 overflow-y-auto py-1">
                            <div class="px-3 py-2 text-xs font-bold bg-blue-50 text-blue-600 hover:bg-slate-50 cursor-pointer project-item transition-colors" data-value="" data-name="Semua Proyek">
                                Semua Proyek
                            </div>
                            <?php foreach ($projects as $p): ?>
                                <div class="px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 cursor-pointer project-item transition-colors" data-value="<?= $p['id_project'] ?>" data-name="<?= esc($p['nama_proyek']) ?>">
                                    <?= esc($p['nama_proyek']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE WRAPPER -->
        <div class="overflow-x-auto rounded-b-xl">
            <div class="min-w-full inline-block align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-start text-xs font-bold text-slate-500 uppercase">Tanggal & Dokumen</th>
                            <th scope="col" class="px-6 py-3 text-start text-xs font-bold text-slate-500 uppercase">Proyek & Item</th>
                            <th scope="col" class="px-6 py-3 text-end text-xs font-bold text-slate-500 uppercase">Over Limit</th>
                            <th scope="col" class="px-6 py-3 text-end text-xs font-bold text-slate-500 uppercase">Harga Satuan</th>
                            <th scope="col" class="px-6 py-3 text-end text-xs font-bold text-slate-500 uppercase">Potongan Margin</th>
                            <th scope="col" class="px-6 py-3 text-start text-xs font-bold text-slate-500 uppercase">Justifikasi</th>
                        </tr>
                    </thead>
                    <tbody id="deviasi-tbody" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                <p class="text-sm font-semibold">Memuat data deviasi...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const DeviasiUI = {
        init() {
            this.tbody = document.getElementById('deviasi-tbody');
            this.totalKerugianEl = document.getElementById('total-kerugian');
            this.filterProyek = document.getElementById('filter-proyek');

            this.initDropdown();
            this.loadData();
        },

        initDropdown() {
            this.container = document.getElementById('project-filter-container');
            this.trigger = document.getElementById('project-filter-trigger');
            this.panel = document.getElementById('project-filter-panel');
            this.display = document.getElementById('project-filter-display');
            this.icon = document.getElementById('project-filter-icon');
            this.searchInput = document.getElementById('project-search-input');
            this.items = document.querySelectorAll('.project-item');
            
            // Toggle dropdown
            this.trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = this.panel.classList.contains('hidden');
                if (isHidden) {
                    this.panel.classList.remove('hidden');
                    this.icon.classList.add('rotate-180');
                    setTimeout(() => this.searchInput.focus(), 100);
                } else {
                    this.closeDropdown();
                }
            });

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!this.container.contains(e.target)) {
                    this.closeDropdown();
                }
            });

            // Search logic
            this.searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                this.items.forEach(item => {
                    const name = item.getAttribute('data-name').toLowerCase();
                    if (name.includes(term)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Select item
            this.items.forEach(item => {
                item.addEventListener('click', () => {
                    const value = item.getAttribute('data-value');
                    const name = item.getAttribute('data-name');
                    
                    this.filterProyek.value = value;
                    this.display.innerText = name;
                    
                    // Reset styling
                    this.items.forEach(i => {
                        i.classList.remove('font-bold', 'bg-blue-50', 'text-blue-600');
                        i.classList.add('text-slate-700');
                    });
                    
                    // Add active styling
                    item.classList.remove('text-slate-700');
                    item.classList.add('font-bold', 'bg-blue-50', 'text-blue-600');
                    
                    this.closeDropdown();
                    this.loadData();
                });
            });
        },

        closeDropdown() {
            this.panel.classList.add('hidden');
            this.icon.classList.remove('rotate-180');
        },
        
        async loadData() {
            this.tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-slate-400"><i class="fas fa-spinner fa-spin text-2xl mb-2"></i><p class="text-sm font-semibold">Memuat data deviasi...</p></td></tr>`;
            
            try {
                let url = '<?= base_url('api/permintaan/deviasi-data') ?>';
                const projectId = this.filterProyek.value;
                if(projectId) {
                    url += `?id_project=${projectId}`;
                }

                const res = await fetch(url);
                const json = await res.json();
                
                if(json.status === 'success') {
                    this.renderData(json.data);
                } else {
                    throw new Error(json.message);
                }
            } catch(e) {
                this.tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-red-500 font-semibold"><i class="fas fa-exclamation-triangle mb-2 text-2xl"></i><p>${e.message}</p></td></tr>`;
            }
        },

        renderData(data) {
            const formatRp = (num) => 'Rp ' + Number(num).toLocaleString('id-ID');
            const formatDate = (dateStr) => {
                if(!dateStr) return '-';
                const d = new Date(dateStr);
                return d.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'});
            };

            this.totalKerugianEl.innerText = formatRp(data.total_kerugian);

            if(!data.items || data.items.length === 0) {
                this.tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-slate-400 font-semibold"><i class="fas fa-check-circle text-2xl text-emerald-400 mb-2"></i><p>Tidak ada pengeluaran over-limit.</p></td></tr>`;
                return;
            }

            let html = '';
            data.items.forEach(item => {
                const statusBadge = item.status === 'selesai' 
                    ? `<span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">Selesai</span>`
                    : `<span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">${item.status}</span>`;

                html += `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-800">${item.nomor_permintaan}</span>
                                <span class="text-[11px] text-slate-500">${formatDate(item.tanggal_permintaan)}</span>
                                <div class="mt-1">${statusBadge}</div>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">${item.nama_proyek}</span>
                                <span class="text-xs font-bold text-slate-800">${item.nama_barang}</span>
                                <span class="text-[11px] text-slate-500">${item.satuan}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-end">
                            <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-lg text-xs font-bold bg-red-100 text-red-700">
                                <i class="fas fa-arrow-up text-[10px]"></i> ${Number(item.jumlah_over_limit).toLocaleString('id-ID')}
                            </span>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-end text-xs text-slate-600 font-semibold">
                            ${formatRp(item.harga_satuan)}
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-end text-xs font-bold text-red-600">
                            ${formatRp(item.kerugian_margin)}
                        </td>
                        <td class="px-6 py-3 max-w-[200px]">
                            <p class="text-[11px] text-slate-600 bg-amber-50 border border-amber-100 rounded-md p-2 italic break-words">
                                "${item.justifikasi_over_limit || 'Tidak ada justifikasi'}"
                            </p>
                        </td>
                    </tr>
                `;
            });
            this.tbody.innerHTML = html;
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        DeviasiUI.init();
    });
</script>
<?= $this->endSection() ?>
