<div class="w-full px-3 sm:px-6 lg:px-8 py-4 md:py-8">
    <div class="max-w-[90rem] mx-auto space-y-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 text-slate-600">
                        <i class="fas fa-users text-sm"></i>
                    </span>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">Tambah Akun Tim</h3>
                        <p class="text-[11px] text-slate-400">Akun utama dapat menambahkan akun untuk sistem Gudang dan Purchasing.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-1 space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1.5">Role Akses <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <!-- Left Icon with divider -->
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none border-e border-slate-100 pr-2.5">
                                <i class="fas fa-user-shield text-slate-400 group-hover:text-primary group-focus-within:text-primary transition-colors text-[10px]"></i>
                            </div>
                            <!-- Premium Select Input -->
                            <select id="subaccount-role" class="w-full pl-11 pr-10 py-2.5 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary focus:bg-white bg-slate-50/40 appearance-none cursor-pointer hover:bg-white hover:border-slate-300 transition-all duration-200 font-medium">
                                <option value="" selected disabled class="text-slate-400">Pilih role akses tim...</option>
                                <option value="Gudang" class="py-2 text-slate-800">Gudang (Kelola Stok & Logistik)</option>
                                <option value="Purchasing" class="py-2 text-slate-800">Purchasing (Kelola Pembelian & Vendor)</option>
                            </select>
                            <!-- Right Chevron with Rotation -->
                            <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-slate-400 group-hover:text-primary group-focus-within:text-primary transition-all duration-200 text-[9px] transform group-focus-within:rotate-180"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Pengguna <span class="text-red-500">*</span></label>
                        <input id="subaccount-name" type="text" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" placeholder="Nama pengguna" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Username <span class="text-red-500">*</span></label>
                        <input id="subaccount-username" type="text" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" placeholder="username" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Email</label>
                        <input id="subaccount-email" type="email" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" placeholder="email@domain.com" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Password <span class="text-red-500">*</span></label>
                        <input id="subaccount-password" type="password" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" placeholder="Password sementara" />
                        <p class="text-[10px] text-slate-400 mt-1">Password akan di-hash otomatis oleh sistem.</p>
                    </div>

                    <button id="subaccount-submit" type="button" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary-hover shadow-sm transition-colors">
                        <i class="fas fa-user-plus text-[11px]"></i>
                        Tambahkan Akun
                    </button>
                </div>

                <div class="lg:col-span-2 lg:mt-5">
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
                        <div class="flex items-center justify-between px-4 py-1 bg-primary text-white">
                            <h4 class="text-xs font-bold uppercase tracking-wide">Daftar Akun Tim</h4>
                            <button id="subaccount-refresh" type="button" class="inline-flex items-center gap-2 px-1.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                <i class="fas fa-rotate text-[11px]"></i>
                                Refresh
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="bg-white text-slate-600 border-b border-slate-200">
                                    <tr>
                                        <th class="text-left px-4 py-2.5 font-bold">Role</th>
                                        <th class="text-left px-4 py-2.5 font-bold">Nama</th>
                                        <th class="text-left px-4 py-2.5 font-bold">Username</th>
                                        <th class="text-left px-4 py-2.5 font-bold">Email</th>
                                        <th class="text-right px-4 py-2.5 font-bold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="subaccount-list" class="divide-y divide-slate-100">
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-slate-400">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p class="text-[10px] text-slate-400 mt-2 px-1">Catatan: Relasi akun tim disimpan via field <code>parent_id</code>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

