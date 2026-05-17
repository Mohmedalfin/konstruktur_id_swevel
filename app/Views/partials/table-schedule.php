<div class="w-full px-3 sm:px-6 lg:px-8 py-4 md:py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">        
        <div class="relative w-full sm:w-100">
            <input id="schedule-search" type="text" placeholder="Cari pekerjaan..."
                class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm border rounded-lg bg-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <div class="flex items-center justify-between gap-2">
            <div class="hidden md:flex items-center gap-2 shrink-0">
                <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm">
                    <i class="fas fa-calendar-alt text-slate-400 text-xs"></i>
                    <input type="date" id="filterStartDate" class="border-none focus:ring-0 text-xs font-semibold text-slate-600 p-0 bg-transparent cursor-pointer w-[110px]" title="Tanggal Mulai">
                    <span class="text-slate-400 text-[10px] font-bold uppercase">s/d</span>
                    <input type="date" id="filterEndDate" class="border-none focus:ring-0 text-xs font-semibold text-slate-600 p-0 bg-transparent cursor-pointer w-[110px]" title="Tanggal Selesai">
                    <button type="button" id="schedule-filter-clear" class="hidden w-5 h-5 items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-red-500 transition-colors ml-1 focus:outline-none" title="Hapus Filter">
                        <i class="fas fa-times text-[10px]"></i>
                    </button>
                </div>

                <div class="relative z-[60]">
                    <button
                        id="viewModeDropdownBtn"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none shadow-sm min-w-[140px] justify-between"
                    >
                        <span id="selectedViewMode">Jadwal (Gantt)</span>

                        <span class="viewmode-dropdown-icon">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </button>

                    <div
                        id="viewModeDropdownMenu"
                        class="hidden absolute left-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-200 overflow-y-auto z-[60]"
                    >
                        <button class="viewmode-option block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 font-semibold text-slate-800" data-value="gantt">Jadwal (Gantt)</button>
                        <button class="viewmode-option block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-slate-600" data-value="s-curve">Kurva S</button>
                    </div>
                </div>

                <div class="flex items-center gap-2 relative z-[60]">
                    <div class="relative">
                        <button
                            id="categoryDropdownBtn"
                            type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none shadow-sm min-w-[120px] justify-between"
                        >
                            <span id="selectedCategory">Filter Kategori</span>

                            <span class="dropdown-icon">
                                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </span>
                        </button>

                        <div
                            id="categoryDropdownMenu"
                            class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 overflow-y-auto max-h-[220px] z-[60] [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
                        >
                            <?php if (isset($categories) && !empty($categories)): ?>
                                <div class="px-3 py-2 border-b border-gray-100">
                                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 cursor-pointer hover:bg-gray-50 px-1 py-1 rounded">
                                        <input type="checkbox" class="category-checkbox-all w-4 h-4 border-gray-300 rounded text-table-category focus:ring-table-category accent-table-category cursor-pointer" value="all">
                                        <span>Pilih Semua</span>
                                    </label>
                                </div>
                                <div class="py-1">
                                <?php foreach ($categories as $cat): ?>
                                    <label class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
                                        <input type="checkbox" class="category-checkbox w-4 h-4 border-gray-300 rounded text-table-category focus:ring-table-category accent-table-category cursor-pointer" value="<?= esc($cat['nama_kategori']) ?>">
                                        <span class="truncate"><?= esc($cat['nama_kategori']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="block px-4 py-2 text-sm text-gray-400 italic">Tidak ada kategori</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <button id="schedule-export-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-semibold rounded-lg transition-colors focus:outline-none shadow-sm">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14"/>
                    </svg>
                    Eksport
                </button>
            </div>

            <div class="relative ml-auto md:hidden">
                <button
                    id="mobileActionBtn"
                    type="button"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary hover:bg-primary-hover text-white shadow-sm"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="5" cy="12" r="2"></circle>
                        <circle cx="12" cy="12" r="2"></circle>
                        <circle cx="19" cy="12" r="2"></circle>
                    </svg>
                </button>

                <div
                    id="mobileActionMenu"
                    class="hidden absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 z-[60] p-3"
                >
                    <div class="flex flex-col gap-2">
                        <div class="relative">
                            <button
                                id="mobileViewModeBtn"
                                type="button"
                                class="w-full inline-flex items-center justify-between px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-semibold rounded-lg"
                            >
                                <span id="mobileSelectedViewMode">Jadwal (Gantt)</span>
                                <span class="mobile-viewmode-dropdown-icon">
                                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </span>
                            </button>
                            <div id="mobileViewModeMenu" class="hidden bg-white rounded-lg border border-gray-200 mt-1 overflow-hidden">
                                <button class="mobile-viewmode-option block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 font-semibold text-slate-800" data-value="gantt">Jadwal (Gantt)</button>
                                <button class="mobile-viewmode-option block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-slate-600" data-value="s-curve">Kurva S</button>
                            </div>
                        </div>

                        <button
                        id="mobileCategoryBtn"
                        type="button"
                        class="w-full inline-flex items-center justify-between px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-semibold rounded-lg"
                    >
                        <span id="mobileSelectedCategory">Filter Kategori</span>

                        <span class="dropdown-icon">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </button>

                    <div
                        id="mobileCategoryMenu"
                        class="hidden bg-white rounded-lg border border-gray-200 overflow-y-auto max-h-[200px] [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
                    >
                        <?php if (isset($categories) && !empty($categories)): ?>
                            <div class="px-3 py-2 border-b border-gray-100">
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 cursor-pointer hover:bg-gray-50 px-1 py-1 rounded">
                                    <input type="checkbox" class="mobile-category-checkbox-all w-4 h-4 border-gray-300 rounded text-table-category focus:ring-table-category accent-table-category cursor-pointer" value="all">
                                    <span>Pilih Semua</span>
                                </label>
                            </div>
                            <div class="py-1">
                            <?php foreach ($categories as $cat): ?>
                                <label class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
                                    <input type="checkbox" class="mobile-category-checkbox w-4 h-4 border-gray-300 rounded text-table-category focus:ring-table-category accent-table-category cursor-pointer" value="<?= esc($cat['nama_kategori']) ?>">
                                    <span class="truncate"><?= esc($cat['nama_kategori']) ?></span>
                                </label>
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="block px-4 py-2 text-sm text-gray-400 italic">Tidak ada kategori</span>
                        <?php endif; ?>
                    </div>

                        <div class="flex flex-col gap-2 px-1 mt-1 mb-2">
                            <label class="text-xs font-semibold text-slate-600">Tanggal Mulai</label>
                            <input type="date" id="mobileFilterStartDate" class="w-full px-3 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                            
                            <label class="text-xs font-semibold text-slate-600 mt-1">Tanggal Selesai</label>
                            <input type="date" id="mobileFilterEndDate" class="w-full px-3 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                        </div>

                        <button id="mobileExportBtn" type="button" class="w-full inline-flex gap-2 px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-semibold rounded-lg">
                            Eksport
                        </button>
                    </div>
                </div>
            </div>
        </div>  
    </div>

    <div class="overflow-auto rounded-xl shadow-md border border-slate-200 bg-white pb-4 w-full relative max-h-[80vh] md:max-h-[calc(100vh-160px)]" id="schedule-table-container">
        <div id="sCurveOverlay" class="absolute pointer-events-none z-[15] hidden" style="opacity: 0.85;">
            <canvas id="sCurveChartOverlay"></canvas>
        </div>

        <div class="relative min-w-full w-max">
            <table class="w-full text-left border-collapse" style="min-width: max-content;" id="schedule-table">
                <thead id="schedule-thead">
                </thead>

                <tbody id="schedule-tbody" class="text-[12px] text-slate-700 relative">
                </tbody>

                <tfoot id="schedule-tfoot">
                </tfoot>
            </table>
        </div>
    </div>
</div>
