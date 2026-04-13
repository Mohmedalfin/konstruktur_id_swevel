<div class="w-full px-3 sm:px-6 lg:px-8 py-4 md:py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        
        <!-- Search -->
        <div class="relative w-full sm:w-64">
            <input id="schedule-search" type="text" placeholder="Cari pekerjaan..."
                class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-1.5 md:gap-2 shrink-0">
            <!-- Filter -->
            <button id="schedule-filter-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-hover text-white text-[10px] md:text-xs font-semibold rounded-lg transition-colors focus:outline-none shadow-sm">
                Filter Kategori
                <svg class="w-4 h-4 ml-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Export -->
            <button id="schedule-export-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-hover text-white text-[10px] md:text-xs font-semibold rounded-lg transition-colors focus:outline-none shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Eksport File
            </button>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl shadow-md border border-slate-200 bg-white pb-4 w-full relative" id="schedule-table-container">
        <table class="w-full text-left border-collapse" style="min-width: max-content;" id="schedule-table">
            <thead id="schedule-thead">
                <tr><th class="p-4 text-center text-sm text-slate-500">Memuat Jadwal...</th></tr>
            </thead>

            <tbody id="schedule-tbody" class="text-[12px] text-slate-700">
                <!-- Data akan dirender oleh ajax_schedule.js -->
            </tbody>
        </table>
    </div>
</div>
