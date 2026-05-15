<div class="bg-white rounded-xl border border-slate-100 shadow-[0_4px_20px_-5px_rgba(0,0,0,0.05)] overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500 delay-200 h-full flex flex-col">
    <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-white">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i class="fas fa-list-check text-sm"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">Ringkasan Pekerjaan</h3>
        </div>
        <a href="<?= base_url('proyek/'.$slug.'/realisasi') ?>" class="text-[11px] font-bold text-primary hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
            Lihat Detail <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    
    <div class="p-0 overflow-auto flex-1 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar-thumb]:bg-slate-200 [&::-webkit-scrollbar-track]:bg-slate-50">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead class="sticky top-0 z-10">
                <tr class="bg-slate-50/95 backdrop-blur-sm shadow-sm">
                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Kategori Pekerjaan</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 text-center w-24">Bobot</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 text-center w-24">Planned</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 text-center w-24">Actual</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-summary-body" class="divide-y divide-slate-50">
                <!-- Skeleton rows for loading state -->
                <?php for($i=0; $i<4; $i++): ?>
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-4 py-3"><div class="h-4 w-40 bg-slate-100 animate-pulse rounded"></div></td>
                    <td class="px-4 py-3"><div class="h-4 w-10 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
                    <td class="px-4 py-3"><div class="h-4 w-10 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
                    <td class="px-4 py-3"><div class="h-4 w-10 bg-slate-100 animate-pulse rounded mx-auto"></div></td>
                    <td class="px-4 py-3 flex justify-center"><div class="h-6 w-20 bg-slate-100 animate-pulse rounded-md"></div></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>
