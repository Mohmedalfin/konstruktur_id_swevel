<div class="w-full px-3 sm:px-6 lg:px-8 py-4 md:py-8">
    <div class="max-w-[90rem] mx-auto space-y-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-6">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/50">
                        <i class="fas fa-users-cog text-sm"></i>
                    </span>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">Kelola & Undang Akun Tim</h3>
                        <p class="text-[11px] text-slate-400">Undang anggota tim baru secara aman tanpa mengirimkan password mentah.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Form: Invite Member -->
                <div class="lg:col-span-1 space-y-4 bg-slate-50/50 border border-slate-200/60 p-5 rounded-2xl">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-paper-plane text-amber-500"></i>
                        Undang Pengguna
                    </h4>
                    
                    <div class="space-y-3.5">
                        <!-- Role Selection -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Role Akses <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none border-e border-slate-200/60 pr-2.5">
                                    <i class="fas fa-user-shield text-slate-400 group-hover:text-primary transition-colors text-[10px]"></i>
                                </div>
                                <select id="subaccount-role" class="w-full pl-11 pr-10 py-2.5 rounded-lg border border-slate-200 text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary bg-white cursor-pointer hover:border-slate-300 transition-all duration-200 appearance-none">
                                    <option value="" selected disabled class="text-slate-400">Pilih role akses tim...</option>
                                    <option value="Gudang" class="py-2 text-slate-800 font-medium">Gudang (Kelola Stok & Logistik)</option>
                                    <option value="Purchasing" class="py-2 text-slate-800 font-medium">Purchasing (Kelola Pembelian & Vendor)</option>
                                </select>
                                <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-slate-400 text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Alamat Email Penerima <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none border-e border-slate-200/60 pr-2.5">
                                    <i class="fas fa-envelope text-slate-400 group-hover:text-primary transition-colors text-[10px]"></i>
                                </div>
                                <input id="subaccount-email" type="email" class="w-full pl-11 pr-4 py-2.5 rounded-lg border border-slate-200 text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary bg-white hover:border-slate-300 transition-all" placeholder="email@domain.com" required />
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Sistem akan membuat tautan aktivasi unik untuk email ini.</p>
                        </div>

                        <!-- Submit Invite -->
                        <button id="subaccount-submit" type="button" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary-hover shadow-sm transition-all duration-200 cursor-pointer mt-2">
                            <i class="fas fa-paper-plane text-[10px]"></i>
                            <span>Kirim Undangan</span>
                        </button>
                    </div>
                </div>

                <!-- Right Table: Active Members & Pending Invites -->
                <div class="lg:col-span-2 space-y-4">
                    
                    <!-- Tabs Header -->
                    <div class="flex border-b border-slate-200">
                        <button id="tab-active" class="py-2.5 px-4 text-xs font-bold border-b-2 border-primary text-primary focus:outline-none flex items-center gap-2">
                            <i class="fas fa-circle-check text-[10px]"></i>
                            Akun Tim Aktif
                        </button>
                        <button id="tab-pending" class="py-2.5 px-4 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none flex items-center gap-2">
                            <i class="fas fa-clock text-[10px]"></i>
                            Undangan Tertunda
                            <span id="pending-count" class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200">0</span>
                        </button>
                    </div>

                    <!-- Panel 1: Active Accounts -->
                    <div id="panel-active" class="block border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="bg-slate-50 text-slate-500 border-b border-slate-200/80">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Role</th>
                                        <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Nama Lengkap</th>
                                        <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Username</th>
                                        <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Email</th>
                                        <th class="text-right px-4 py-3 font-bold uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="subaccount-list" class="divide-y divide-slate-100">
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-medium">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Panel 2: Pending Invitations -->
                    <div id="panel-pending" class="hidden border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="bg-slate-50 text-slate-500 border-b border-slate-200/80">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Email Tujuan</th>
                                        <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Role Calon Akun</th>
                                        <th class="text-left px-4 py-3 font-bold uppercase tracking-wider">Batas Waktu</th>
                                        <th class="text-right px-4 py-3 font-bold uppercase tracking-wider">Aksi / Link</th>
                                    </tr>
                                </thead>
                                <tbody id="invite-list" class="divide-y divide-slate-100">
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-medium">Memuat data undangan...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
