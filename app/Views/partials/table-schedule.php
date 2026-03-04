<?php
$tableVisible = $tableVisible ?? true;
?>

<section id="schedule-table-wrapper" class="<?= $tableVisible ? '' : 'hidden' ?> mx-auto px-4 md:px-6 pb-10 mt-6">
    <div class="rounded-2xl bg-white shadow border border-gray-100 overflow-hidden">

        <div class="p-4 md:p-6 bg-white">
            <!-- Toolbar -->
            <div class="flex items-center gap-3 mb-4">
                <button id="btnFilterKategori"
                    class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-xs md:text-sm font-semibold text-white hover:opacity-90 active:scale-[0.99] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707l-6.414 6.414A1 1 0 0014 13.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 018 17.999v-4.586a1 1 0 00-.293-.707L1.293 6.707A1 1 0 011 6V4z" />
                    </svg>
                    Filter Kategori
                </button>

                <button id="btnExport"
                    class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-xs md:text-sm font-semibold text-white hover:opacity-90 active:scale-[0.99] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-8m0 0l-3 3m3-3l3 3M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
                    </svg>
                    Eksport File
                </button>

                <!-- (opsional) toggle tampilan gantt hide/show kayak figma 3 -->
                <button id="btnToggleGantt"
                    class="ml-auto inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs md:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Toggle Gantt
                </button>
            </div>

            <!-- Table scroll -->
            <div class="overflow-auto rounded-xl border border-slate-200">
                <div class="min-w-[1300px]">
                    <!-- JS akan render header bulan/minggu di sini -->
                    <div id="schedule-header"></div>

                    <!-- JS render body rows di sini -->
                    <div id="schedule-body"></div>
                </div>
            </div>
        </div>
    </div>
</section>