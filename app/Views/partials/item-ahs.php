<?php
$wrapperClass = $wrapperClass ?? 'w-full';
?>

<div class="<?= $wrapperClass ?> px-3 sm:px-6 lg:px-8 py-4 md:py-8">

    <!-- ── Context Banner ────────────────────────────────────────────── -->
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

    <!-- ── Toolbar ────────────────────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">

        <!-- Left: Nama Pekerjaan Input + Tambah Sendiri -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Add custom item button -->
            <button id="tambah-ahs-custom-btn"
                class="inline-flex items-center gap-2 bg-table-category hover:bg-table-category/90 active:scale-95 text-white px-4 py-2 rounded-lg text-xs font-semibold tracking-wide shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/40">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pekerjaan Sendiri
            </button>
        </div>
    </div>

    <!-- ── Filter: Nama Pekerjaan + Sumber ────────────────────────────── -->
    <div class="bg-white border border-table-border rounded-xl p-4 mb-5 shadow-sm">
        <div class="flex flex-col gap-3">

            <!-- Nama Pekerjaan -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="tambah-ahs-nama" class="text-xs font-semibold text-table-body shrink-0 w-36">Nama Pekerjaan</label>
                <input id="tambah-ahs-nama" type="text" placeholder="Ketik Nama Pekerjaan"
                    class="flex-1 bg-white border border-table-border rounded-lg px-3 py-2 text-xs text-table-medium placeholder-table-subtle focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all shadow-sm"/>
            </div>

            <!-- Sumber checkboxes — label aligns with "Nama Pekerjaan" label above -->
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

    <!-- ── Result Count ────────────────────────────────────────────────── -->
    <p id="tambah-ahs-count" class="text-[11px] text-table-subtle mb-3 font-medium tracking-wide"></p>

    <!-- ── Table Container ───────────────────────────────────────────── -->
    <div class="overflow-x-auto rounded-xl shadow-md border border-table-border bg-white scrollbar-thin">
        <table class="w-full text-left border-collapse min-w-[700px]" id="tambah-ahs-table">

            <colgroup>
                <col style="width: 3.25rem">   <!-- No -->
                <col>                           <!-- Nama Pekerjaan -->
                <col style="width: 6rem">       <!-- Satuan -->
                <col style="width: 12rem">      <!-- Sumber + Harga -->
                <col style="width: 5.5rem">     <!-- Aksi -->
            </colgroup>

            <!-- ── Head ─────────────────────────────────────────────── -->
            <thead>
                <tr class="bg-table-category text-white">
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">No</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-[10px] md:text-xs font-semibold uppercase tracking-wider">Nama Pekerjaan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">Satuan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-right text-[10px] md:text-xs font-semibold uppercase tracking-wider">Harga Satuan</th>
                    <th scope="col" class="px-3 md:px-5 py-3 md:py-3.5 text-center text-[10px] md:text-xs font-semibold uppercase tracking-wider">Pilih</th>
                </tr>
            </thead>

            <!-- ── Body — populated by ajax_tambah_ahs.js ─────────── -->
            <tbody id="tambah-ahs-tbody" class="text-table-body text-[11px] md:text-[13px]">
                <!-- rows injected here -->
            </tbody>

        </table>
    </div>

    <!-- ── Pagination Info ───────────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-3">
        <p id="tambah-ahs-pagination-info" class="text-[11px] text-table-subtle font-medium tracking-wide"></p>
        <div class="flex items-center gap-1.5" id="tambah-ahs-pagination-btns"></div>
    </div>

    <!-- ── Action Bar ────────────────────────────────────────────────── -->
    <div class="mt-5 flex items-center justify-between gap-3">
        <p id="tambah-ahs-selected-count" class="text-xs text-table-subtle font-medium">
            Belum ada item dipilih
        </p>
        <button id="tambah-ahs-submit-btn"
            class="bg-primary hover:bg-primary/90 active:scale-95 disabled:opacity-50 disabled:pointer-events-none text-white px-8 py-2.5 rounded-lg text-xs md:text-sm font-semibold tracking-wide shadow-md transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/40"
            disabled>
            Tambah ke AHS
        </button>
    </div>

</div>

<script src="<?= base_url('ajax/ajax_tambah_ahs.js') ?>"></script>