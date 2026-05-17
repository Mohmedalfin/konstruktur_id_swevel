<?php $type = $type ?? 'all'; ?>

<?php if ($type === 'all' || $type === 'progress_only'): ?>
<div class="bg-white rounded-xl border border-slate-100 shadow-[0_4px_20px_-5px_rgba(0,0,0,0.05)] p-4 animate-in fade-in slide-in-from-right-8 duration-500 delay-300 relative overflow-hidden group h-full flex flex-col">
    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-[100px] -mr-10 -mt-10 opacity-50 pointer-events-none transition-transform group-hover:scale-110"></div>
    <div class="flex justify-between items-center mb-6 relative">
        <div class="flex items-center gap-2.5">
            <div class="w-1 h-5 bg-emerald-500 rounded-full"></div>
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Progress Chart</h3>
</div>
    </div>
    <div id="chart-progress" class="w-full flex-1 flex items-center justify-center relative z-10 min-h-[220px]">
        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center">
                <i class="fas fa-circle-notch fa-spin text-slate-300 text-xl"></i>
            </div>
            <span class="text-[11px] text-slate-400 font-bold uppercase tracking-widest">Memuat S-Curve...</span>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($type === 'all' || $type === 'cost_only'): ?>
<div class="bg-white rounded-xl border border-slate-100 shadow-[0_4px_20px_-5px_rgba(0,0,0,0.05)] p-4 animate-in fade-in slide-in-from-right-8 duration-500 delay-400 relative overflow-hidden group h-full flex flex-col">
    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-bl-[100px] -mr-10 -mt-10 opacity-50 pointer-events-none transition-transform group-hover:scale-110"></div>
    <div class="flex justify-between items-center mb-6 relative">
        <div class="flex items-center gap-2.5">
            <div class="w-1 h-5 bg-purple-500 rounded-full"></div>
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Cost Chart</h3>
        </div>
        <button class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 hover:text-purple-600 hover:bg-purple-50 transition-colors flex items-center justify-center">
            <i class="fas fa-expand-arrows-alt text-xs"></i>
        </button>
    </div>
    <div id="chart-cost" class="w-full flex-1 flex items-center justify-center relative z-10 min-h-[220px]">
        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center">
                <i class="fas fa-circle-notch fa-spin text-slate-300 text-xl"></i>
            </div>
            <span class="text-[11px] text-slate-400 font-bold uppercase tracking-widest">Memuat Grafik...</span>
        </div>
    </div>
</div>
<?php endif; ?>
