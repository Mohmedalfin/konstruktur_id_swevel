<div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-in fade-in slide-in-from-bottom-4 duration-500 delay-500">
    
    <!-- Status Cost -->
    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] relative overflow-hidden group h-full min-h-[120px] md:min-h-[160px] flex items-center" id="container-status-cost">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="flex items-center gap-4 md:gap-6 relative z-10 w-full h-full">
            <!-- Left: Large Circle Icon -->
            <div class="w-16 h-16 md:w-24 md:h-24 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center shrink-0 transition-all duration-500" id="icon-bg-cost">
                <i class="fas fa-file-invoice-dollar text-2xl md:text-4xl" id="icon-main-cost"></i>
            </div>
            
            <!-- Middle: Text Content -->
            <div class="flex-1">
                <p class="text-xs md:text-sm font-bold text-slate-500 mb-0.5">Status Cost</p>
                <h4 class="text-xl md:text-3xl font-bold leading-none mb-2" id="text-status-cost">
                    <div class="h-6 md:h-8 w-24 md:w-32 bg-slate-100 rounded animate-pulse"></div>
                </h4>
                <div class="inline-flex items-center px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-slate-100/80 border border-slate-200/50 opacity-0 transition-opacity duration-500" id="container-cpi">
                    <span class="text-[10px] md:text-xs font-bold text-slate-500 mr-2">CPI</span>
                    <span class="text-xs md:text-sm font-black" id="val-cpi">0.00</span>
                </div>
            </div>

            <!-- Right: Small Warning Icon -->
            <div class="absolute top-0 right-0 p-2 md:p-4">
                <div id="icon-big-cost" class="text-lg md:text-2xl opacity-0 transition-all duration-500"></div>
            </div>
        </div>
    </div>

    <!-- Status Schedule -->
    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] relative overflow-hidden group h-full min-h-[120px] md:min-h-[160px] flex items-center" id="container-status-schedule">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="flex items-center gap-4 md:gap-6 relative z-10 w-full h-full">
            <!-- Left: Large Circle Icon -->
            <div class="w-16 h-16 md:w-24 md:h-24 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center shrink-0 transition-all duration-500" id="icon-bg-schedule">
                <i class="fas fa-calendar-alt text-2xl md:text-4xl" id="icon-main-schedule"></i>
            </div>
            
            <!-- Middle: Text Content -->
            <div class="flex-1">
                <p class="text-xs md:text-sm font-bold text-slate-500 mb-0.5">Status Schedule</p>
                <h4 class="text-xl md:text-3xl font-bold leading-none mb-2" id="text-status-schedule">
                    <div class="h-6 md:h-8 w-24 md:w-32 bg-slate-100 rounded animate-pulse"></div>
                </h4>
                <div class="inline-flex items-center px-2 py-0.5 md:px-3 md:py-1 rounded-full bg-slate-100/80 border border-slate-200/50 opacity-0 transition-opacity duration-500" id="container-spi">
                    <span class="text-[10px] md:text-xs font-bold text-slate-500 mr-2">SPI</span>
                    <span class="text-xs md:text-sm font-black" id="val-spi">0.00</span>
                </div>
            </div>

            <!-- Right: Small Warning Icon -->
            <div class="absolute top-0 right-0 p-2 md:p-4">
                <div id="icon-big-schedule" class="text-lg md:text-2xl opacity-0 transition-all duration-500"></div>
            </div>
        </div>
    </div>

    <!-- Overall Status -->
    <div class="bg-slate-800 rounded-xl p-4 shadow-lg text-white relative overflow-hidden transition-all group h-full min-h-[120px] md:min-h-[160px] flex items-center" id="container-status-overall">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-50"></div>
        <div class="flex items-center gap-4 md:gap-6 relative z-10 w-full h-full">
            <!-- Left: Large Circle Icon -->
            <div class="w-16 h-16 md:w-24 md:h-24 rounded-full bg-white/10 flex items-center justify-center shrink-0 backdrop-blur-sm border border-white/10 relative z-10 transition-all duration-500" id="icon-bg-overall">
                <i class="fas fa-circle-notch fa-spin text-2xl md:text-4xl text-white/50" id="icon-status-overall"></i>
            </div>
            
            <!-- Middle: Text Content -->
            <div class="flex-1">
                <p class="text-xs md:text-sm font-bold text-white/50 mb-0.5">Overall Status</p>
                <h4 class="text-xl md:text-3xl font-bold tracking-wide leading-none mb-2" id="text-status-overall">Calculating...</h4>
                <p class="text-xs md:text-sm font-medium text-white/70 line-clamp-1" id="desc-status-overall">Evaluating project data.</p>
            </div>
        </div>
    </div>

</div>
