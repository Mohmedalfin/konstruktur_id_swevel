<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4 animate-in fade-in zoom-in duration-500 delay-100 h-full">
    <!-- Card 1: Nilai Kontrak -->
    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] relative overflow-hidden group hover:shadow-lg transition-all duration-300 flex flex-col h-full">
        <div class="absolute top-0 right-0 w-24 h-24 bg-slate-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="mb-2 relative z-10 flex justify-between items-start">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nilai Kontrak (RAB)</h3>
            <div class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400">
                <i class="fas fa-file-signature text-xs"></i>
            </div>
        </div>
        <div class="flex-1 flex items-center relative z-10">
            <h2 class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight" id="val-nilai-kontrak">
                <div class="h-6 w-3/4 bg-slate-100 animate-pulse rounded-md"></div>
            </h2>
        </div>
        <div class="mt-2 min-h-[20px] relative z-10">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Kontrak Disetujui</p>
        </div>
    </div>

    <!-- Card 2: Nilai RAP -->
    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] relative overflow-hidden group hover:shadow-lg transition-all duration-300 flex flex-col h-full">
        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="mb-2 relative z-10 flex justify-between items-start">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nilai RAP</h3>
            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500/50 shadow-sm">
                <i class="fas fa-coins text-xs"></i>
            </div>
        </div>
        <div class="flex-1 flex items-center relative z-10">
            <h2 class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight" id="val-nilai-rap">
                <div class="h-6 w-3/4 bg-slate-100 animate-pulse rounded-md"></div>
            </h2>
        </div>
        <div class="mt-2 min-h-[20px] relative z-10 opacity-0 transition-opacity duration-300" id="container-margin">
            <div class="flex items-center gap-1.5">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Margin</span>
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-600" id="val-margin-pct">
                    <i class="fas fa-caret-up"></i> <span id="text-margin-pct">0%</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Card 3: Realisasi Biaya -->
    <div class="bg-gradient-to-br from-[#0f172a] to-slate-800 rounded-xl p-4 border border-slate-700 shadow-xl relative overflow-hidden group hover:shadow-2xl transition-all duration-300 flex flex-col h-full text-white">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110 duration-500"></div>
        <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-primary/20 to-transparent mix-blend-overlay"></div>
        <div class="mb-2 relative z-10 flex justify-between items-start">
            <h3 class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Realisasi Biaya</h3>
            <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-white backdrop-blur-sm border border-white/10">
                <i class="fas fa-money-bill-wave text-xs"></i>
            </div>
        </div>
        <div class="flex-1 flex items-center relative z-10">
            <h2 class="text-xl lg:text-2xl font-black text-white tracking-tight drop-shadow-sm" id="val-realisasi">
                <div class="h-6 w-3/4 bg-slate-700 animate-pulse rounded-md"></div>
            </h2>
        </div>
        <div class="mt-2 min-h-[20px] relative z-10">
            <div class="w-full bg-slate-900/50 rounded-full h-1 mb-1 overflow-hidden shadow-inner">
                <div class="bg-gradient-to-r from-blue-400 to-indigo-400 h-1 rounded-full transition-all duration-1000 w-0 relative" id="bar-serapan"></div>
            </div>
            <div class="flex justify-between items-center">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Serapan <span class="text-white font-black" id="val-serapan-pct">0%</span></p>
            </div>
        </div>
    </div>

    <!-- Card 4: Target Selesai -->
    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] relative overflow-hidden group hover:shadow-lg transition-all duration-300 flex flex-col h-full">
        <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="mb-2 relative z-10 flex justify-between items-start">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Target Selesai</h3>
            <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500/50 shadow-sm">
                <i class="fas fa-calendar-check text-xs"></i>
            </div>
        </div>
        <div class="flex-1 flex items-center relative z-10">
            <h2 class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight" id="val-target-date">
                <div class="h-6 w-3/4 bg-slate-100 animate-pulse rounded-md"></div>
            </h2>
        </div>
        <div class="mt-2 min-h-[20px] relative z-10">
            <p class="text-[10px] font-bold uppercase tracking-wider" id="val-hari-lagi">
                <span class="animate-pulse bg-slate-100 text-transparent rounded w-20 inline-block">00 hari</span>
            </p>
        </div>
    </div>
</div>
