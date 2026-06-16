<div class="w-full px-3 sm:px-6 lg:px-8 py-4 md:py-8">
    <form id="profile-form" class="max-w-[90rem] mx-auto space-y-4" enctype="multipart/form-data">
        <div class="relative overflow-hidden bg-white/90 bg-gradient-to-br from-white via-white to-slate-50 border border-slate-200/80 rounded-2xl shadow-[0_10px_30px_rgba(15,23,42,0.08)] p-4 sm:p-6">
            <div class="pointer-events-none absolute -top-20 -right-20 h-40 w-40 rounded-full bg-slate-100/70"></div>
            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-slate-900 text-yellow-500 shadow-inner overflow-hidden">
                        <img id="profile-photo-preview" src="" alt="Foto profile" class="hidden absolute inset-0 w-full h-full object-cover">
                        <div id="profile-photo-fallback" class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-city text-3xl"></i>
                        </div>
                    </div>

                    <div>
                        <h2 id="profile-company-name" class="text-lg sm:text-xl font-bold text-slate-800">Memuat profile...</h2>
                        <div class="flex flex-wrap items-center gap-3 text-[11px] sm:text-xs text-slate-500 mt-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-briefcase text-slate-400"></i>
                                <span>Kategori Akun</span>
                                <span id="profile-category-badge" class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 font-semibold">-</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-circle text-[6px] text-emerald-500"></i>
                                <span>Status</span>
                                <span id="profile-status-badge" class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-semibold">-</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="far fa-calendar-alt text-slate-400"></i>
                                <span>Bergabung</span>
                                <span id="profile-joined-at" class="font-semibold text-slate-700">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 ">
                    <button id="profile-edit-toggle" type="button"
                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-900 text-white text-xs font-semibold shadow-sm hover:bg-slate-800 transition-colors">
                        <i class="fas fa-pen text-[11px]"></i>
                        Edit Profile
                    </button>
                    <button id="profile-cancel-edit" type="button" hidden
                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-times text-[11px]"></i>
                        Batalkan
                    </button>
                    <button id="profile-save" type="submit" hidden
                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary-hover shadow-sm transition-colors">
                        <i class="fas fa-save text-[11px]"></i>
                        Simpan Perubahan
                    </button>
                </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="relative overflow-hidden bg-white/90 bg-gradient-to-br from-white via-white to-slate-50 border border-slate-200/80 rounded-2xl shadow-[0_10px_30px_rgba(15,23,42,0.08)] p-4 sm:p-6">
                <div class="pointer-events-none absolute -top-20 -right-20 h-40 w-40 rounded-full bg-emerald-50/70"></div>
                <div class="pointer-events-none absolute top-4 right-4 hidden sm:flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-md ring-1 ring-slate-200/70 text-emerald-500">
                    <i class="fas fa-building text-[11px]"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">Informasi Perusahaan</h3>
                    </div>

                    <div class="space-y-3">
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Nama Perusahaan</span>
                        <div class="text-[12px] text-slate-800">
                            <span data-profile-text="perusahaan" class="profile-view font-semibold">-</span>
                            <input type="text" name="perusahaan" data-profile-input="perusahaan" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                        </div>
                    </div>

                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Profil</span>
                        <div class="text-[12px] text-slate-700 leading-relaxed">
                            <p data-profile-text="profil" class="profile-view">-</p>
                            <textarea rows="3" name="profil" data-profile-input="profil" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Alamat</span>
                        <div class="text-[12px] text-slate-700 leading-relaxed">
                            <p data-profile-text="alamat" class="profile-view">-</p>
                            <textarea rows="3" name="alamat" data-profile-input="alamat" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Wilayah</span>
                        <div class="text-[12px] text-slate-700">
                            <span data-profile-text="wilayah_label" class="profile-view text-primary font-semibold">-</span>
                            <input type="text" name="id_wilayah" data-profile-input="id_wilayah" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                        </div>
                    </div>

                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Website</span>
                        <div class="text-[12px] text-slate-700">
                            <a id="profile-website-link" href="#" target="_blank" rel="noopener noreferrer" class="profile-view inline-flex items-center gap-2 text-primary font-semibold hover:underline">
                                <span data-profile-text="website_label">-</span>
                                <i class="fas fa-external-link-alt text-[10px]"></i>
                            </a>
                            <input type="text" name="website" data-profile-input="website" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-white/90 bg-gradient-to-br from-white via-white to-slate-50 border border-slate-200/80 rounded-2xl shadow-[0_10px_30px_rgba(15,23,42,0.08)] p-4 sm:p-6">
                <div class="pointer-events-none absolute -top-20 -right-20 h-40 w-40 rounded-full bg-blue-50/70"></div>
                <div class="pointer-events-none absolute top-4 right-4 hidden sm:flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-md ring-1 ring-slate-200/70 text-blue-500">
                    <i class="fas fa-phone text-[11px]"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">Kontak Perusahaan</h3>
                    </div>

                    <div class="space-y-3">
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Email</span>
                        <div class="text-[12px] text-slate-700">
                            <span data-profile-text="email" class="profile-view text-primary font-semibold">-</span>
                            <input type="email" name="email" data-profile-input="email" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                        </div>
                    </div>
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">No. Telepon</span>
                        <div class="text-[12px] text-slate-700">
                            <span data-profile-text="no_telp" class="profile-view font-semibold">-</span>
                            <input type="text" name="no_telp" data-profile-input="no_telp" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                        </div>
                    </div>
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">No. WhatsApp</span>
                        <div class="text-[12px] text-slate-700">
                            <span data-profile-text="no_wa" class="profile-view font-semibold">-</span>
                            <input type="text" name="no_wa" data-profile-input="no_wa" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-white/90 bg-gradient-to-br from-white via-white to-slate-50 border border-slate-200/80 rounded-2xl shadow-[0_10px_30px_rgba(15,23,42,0.08)] p-4 sm:p-6">
                <div class="pointer-events-none absolute -top-20 -right-20 h-40 w-40 rounded-full bg-purple-50/70"></div>
                <div class="pointer-events-none absolute top-4 right-4 hidden sm:flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-md ring-1 ring-slate-200/70 text-purple-500">
                    <i class="fas fa-user-circle text-[11px]"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">Informasi Akun</h3>
                    </div>

                    <div class="space-y-3">
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Nama Pengguna</span>
                        <div class="text-[12px] text-slate-700">
                            <span data-profile-text="nama_pengguna" class="profile-view font-semibold">-</span>
                            <input type="text" name="nama_pengguna" data-profile-input="nama_pengguna" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                        </div>
                    </div>
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Username</span>
                        <div class="text-[12px] text-slate-700">
                            <span data-profile-text="username" class="profile-view font-semibold">-</span>
                            <input type="text" name="username" data-profile-input="username" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                        </div>
                    </div>
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Kategori Akun</span>
                        <div class="text-[12px] text-slate-700">
                            <span data-profile-text="kategori_akun" class="profile-view text-indigo-600 font-semibold">-</span>
                            <input type="text" name="kategori_akun" data-profile-input="kategori_akun" hidden
                                class="profile-edit w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                        </div>
                    </div>
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Akun Induk</span>
                        <div class="text-[12px] text-slate-700">
                            <span data-profile-text="parent_id">-</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Status Akun</span>
                        <div class="text-[12px] text-slate-700">
                            <span id="profile-status-inline" class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span data-profile-text="status">-</span>
                            </span>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="relative overflow-hidden bg-white/90 bg-gradient-to-br from-white via-white to-slate-50 border border-slate-200/80 rounded-2xl shadow-[0_10px_30px_rgba(15,23,42,0.08)] p-4 sm:p-6">
                <div class="pointer-events-none absolute -top-20 -right-20 h-40 w-40 rounded-full bg-orange-50/70"></div>
                <div class="pointer-events-none absolute top-4 right-4 hidden sm:flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-md ring-1 ring-slate-200/70 text-orange-500">
                    <i class="fas fa-calendar-alt text-[11px]"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">Informasi Pendaftaran</h3>
                    </div>

                    <div class="space-y-3">
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Tanggal Daftar</span>
                        <div data-profile-text="tgl_daftar_formatted" class="text-[12px] text-slate-700 font-semibold">-</div>
                    </div>
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <span class="text-[11px] font-semibold text-slate-500">Jam Daftar</span>
                        <div data-profile-text="jam_daftar_formatted" class="text-[12px] text-slate-700 font-semibold">-</div>
                    </div>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-white/90 bg-gradient-to-br from-white via-white to-slate-50 border border-slate-200/80 rounded-2xl shadow-[0_10px_30px_rgba(15,23,42,0.08)] p-4 sm:p-6 lg:col-span-2">
                <div class="pointer-events-none absolute -top-20 -right-20 h-40 w-40 rounded-full bg-slate-100/70"></div>
                <div class="pointer-events-none absolute top-4 right-4 hidden sm:flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-md ring-1 ring-slate-200/70 text-slate-500">
                    <i class="fas fa-shield-alt text-[11px]"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wide">Keamanan Akun</h3>
                    </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                        <div class="flex-1 space-y-3">
                            <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                                <span class="text-[11px] font-semibold text-slate-500">Password</span>
                                <div class="text-[12px] text-slate-700 font-semibold">
                                    <span class="profile-password-view">............</span>
                                    <div class="profile-password-edit space-y-2" hidden>
                                        <input id="profile-password-input" type="password" name="password" autocomplete="new-password"
                                            placeholder="Masukkan password baru"
                                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                                        <input id="profile-password-confirmation" type="password" autocomplete="new-password"
                                            placeholder="Konfirmasi password baru"
                                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-white" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 bg-blue-50 text-blue-700 text-[11px] px-3 py-2 rounded-lg border border-blue-100">
                                <i class="fas fa-info-circle mt-0.5"></i>
                                <span>Untuk menjaga keamanan akun, jangan bagikan password Anda kepada siapa pun.</span>
                            </div>

                            <div class="flex flex-wrap items-center justify-end gap-2 pt-2">
                                <button id="profile-password-toggle" type="button"
                                    class="cursor-pointer profile-password-view inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                    <i class="fas fa-key text-[11px]"></i>
                                    Ubah Password
                                </button>
                                <button id="profile-password-cancel" type="button" hidden
                                    class="cursor-pointer profile-password-edit inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                    <i class="fas fa-times text-[11px]"></i>
                                    Batalkan
                                </button>
                                <button id="profile-password-save" type="button" hidden
                                    class="cursor-pointer profile-password-edit inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary-hover shadow-sm transition-colors">
                                    <i class="fas fa-save text-[11px]"></i>
                                    Simpan Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
            <a href="<?= base_url('dashboard') ?>"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-left text-[11px]"></i>
                Kembali ke Dashboard
            </a>
            <label data-profile-edit-only class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 shadow-sm transition-colors cursor-pointer" hidden>
                <i class="fas fa-upload text-[11px]"></i>
                Upload Logo/Foto
                <input id="profile-photo-input" type="file" name="foto" accept=".jpg,.jpeg,.png" class="hidden">
            </label>
        </div>

    </form>
</div>
