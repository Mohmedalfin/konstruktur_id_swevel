<?php
$wrapperClass = $wrapperClass ?? 'w-full';
?>

<div class="<?= $wrapperClass ?> px-3 sm:px-6 lg:px-8 py-4 md:py-8">

    <div class="flex items-center gap-3 bg-primary text-white px-5 py-3 rounded-xl text-sm shadow-sm mb-6">
        <svg class="w-4 h-4 shrink-0 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <span class="text-white/70 text-xs uppercase tracking-widest font-semibold">Pekerjaan</span>
        <span class="w-px h-4 bg-white/20 shrink-0"></span>
        <span id="tambah-ahs-pekerjaan-label" class="text-secondary font-bold tracking-wide truncate">
            PEKERJAAN PERSIAPAN
        </span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">

        <div class="flex flex-wrap items-center gap-2">
            <button id="tambah-ahs-custom-btn"
                class="inline-flex items-center gap-2 bg-table-category hover:bg-table-category/90 active:scale-95 text-white px-4 py-2 rounded-lg text-xs font-semibold tracking-wide shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/40">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pekerjaan Sendiri
            </button>
        </div>
    </div>

    <div class="bg-white border border-table-border rounded-xl p-4 mb-5 shadow-sm">
        <div class="flex flex-col gap-3">

            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="tambah-ahs-nama" class="text-xs font-semibold text-table-body shrink-0 w-36">Nama Pekerjaan</label>
                <input id="tambah-ahs-nama" type="text" placeholder="Ketik Nama Pekerjaan"
                    class="flex-1 bg-white border border-table-border rounded-lg px-3 py-2 text-xs text-table-medium placeholder-table-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-sm"/>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <span class="text-xs font-semibold text-table-body shrink-0 w-36">Sumber</span>
                <div class="flex flex-wrap gap-x-5 gap-y-2">
                    <?php
                    $sources = ['Proyek Terkini', 'SNI', 'Empiris', 'PUPR', 'Estimator.id'];
                    foreach ($sources as $src):
                        $id = 'src-' . strtolower(str_replace([' ', '.'], '-', $src));
                    ?>
                    <label for="<?= $id ?>" class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" id="<?= $id ?>" value="<?= $src ?>"
                            class="tambah-ahs-source w-3.5 h-3.5 rounded border-table-border text-primary accent-primary cursor-pointer"/>
                        <span class="text-xs text-table-subtle group-hover:text-table-body transition-colors"><?= $src ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

    <p id="tambah-ahs-count" class="text-[11px] text-table-subtle mb-3 font-medium tracking-wide"></p>

    <div class="overflow-x-auto rounded-xl shadow-md border border-table-border bg-white scrollbar-thin">
        <table class="w-full text-left border-collapse min-w-[700px]" id="tambah-ahs-table">

            <colgroup>
                <col style="width: 3.25rem">  
                <col style="width: 45%">
                <col style="width: 6rem">
                <col style="width: 14rem">
                <col style="width: 9rem"> 
            </colgroup>

            <thead>
                <tr class="bg-table-category text-white">
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">No</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-[10px] md:text-xs font-semibold uppercase tracking-wider">Nama Pekerjaan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">Satuan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">Sumber</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">Pilih</th>
                </tr>
            </thead>

            <tbody id="tambah-ahs-tbody" class="text-table-body text-[11px] md:text-[13px]">            </tbody>

        </table>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-3">
        <p id="tambah-ahs-pagination-info" class="text-[11px] text-table-subtle font-medium tracking-wide"></p>
        <div class="flex items-center gap-1.5" id="tambah-ahs-pagination-btns"></div>
    </div>

    <div class="mt-5 flex items-center justify-between gap-3">
        <p id="tambah-ahs-selected-count" class="text-xs text-table-subtle font-medium">
            Belum ada item dipilih
        </p>
        <button id="tambah-ahs-submit-btn"
            class="bg-primary hover:bg-primary/90 active:scale-95 disabled:opacity-50 disabled:pointer-events-none text-white px-8 py-2.5 rounded-lg text-xs md:text-sm font-semibold tracking-wide shadow-md transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/40"
            disabled>
            Tambah ke RAB
        </button>
    </div>
</div>

<script type="module" src="<?= base_url('js/pekerjaan/index.js') ?>"></script>

<datalist id="datalist-satuan">
    <!-- Volume / Dimensi -->
    <option value="m">Meter (m)</option>
    <option value="m2">Meter Persegi (m²)</option>
    <option value="m3">Meter Kubik (m³)</option>
    <option value="cm">Sentimeter (cm)</option>
    <option value="mm">Milimeter (mm)</option>
    <option value="km">Kilometer (km)</option>
    <!-- Berat -->
    <option value="kg">Kilogram (kg)</option>
    <option value="ton">Ton</option>
    <option value="gr">Gram (gr)</option>
    <!-- Satuan Umum / Satuan Kerja -->
    <option value="bh">Buah (bh)</option>
    <option value="unit">Unit</option>
    <option value="set">Set</option>
    <option value="ls">Lump Sum (ls)</option>
    <option value="ttk">Titik (ttk)</option>
    <option value="btg">Batang (btg)</option>
    <option value="lbr">Lembar (lbr)</option>
    <option value="mtr">Meter Lari (m')</option>
    <!-- Waktu & Tenaga -->
    <option value="org/hr">Orang/Hari (OH)</option>
    <option value="jam">Jam</option>
    <option value="hari">Hari</option>
    <option value="bln">Bulan</option>
    <option value="mgg">Minggu</option>
    <!-- Kemasan -->
    <option value="zak">Zak</option>
    <option value="gln">Galon (gln)</option>
    <option value="klg">Kaleng (klg)</option>
    <option value="btl">Botol (btl)</option>
    <option value="ktk">Kotak (ktk)</option>
    <option value="rol">Rol</option>
    <option value="dus">Dus</option>
    <!-- Lainnya -->
    <option value="rit">Ritase (rit)</option>
    <option value="pax">Pax</option>
    <option value="liter">Liter (L)</option>
</datalist>