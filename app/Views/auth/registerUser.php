<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontraktor.id - Register</title>
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
</head>
<body class="flex items-center justify-center min-h-screen p-4 sm:p-8 bg-cover bg-center bg-no-repeat" 
      style="background-image: url('<?= base_url('assets/images/BackgroundLogin.png') ?>');">

    <main class="flex flex-col md:flex-row w-full max-w-[1150px] max-h-[90vh] md:h-[650px] bg-landing-1 shadow-2xl rounded-sm overflow-hidden z-10">
        
        <section class="w-full h-48 md:h-auto md:w-1/2 relative flex-shrink-0">
            <img src="<?= base_url('assets/images/loginImage.jpeg') ?>" 
                 alt="Construction" class="absolute inset-0 w-full h-full object-cover">
        </section>

        <section class="w-full md:w-1/2 p-6 sm:p-8 lg:p-12 overflow-y-auto bg-[#FEFDF8]/90 backdrop-blur-sm [&::-webkit-scrollbar]:w-[5px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-gray-400 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
            <div class="flex flex-col items-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <img src="<?= base_url('assets/images/logoKonstruktor.png') ?>" alt="Kontraktor.id Logo"
                        class="h-7 md:h-8 w-auto object-contain">
                    <span class="text-yellow-500 text-lg md:text-xl font-semibold font-primary tracking-wide">Kontraktor.id</span>
                </div>

                <h1 class="text-[18px] md:block text-2xl lg:text-[20px] font-semibold font-primary text-brand-dark text-center mb-10">
                    Buat Akun Kontraktor.id
                </h1>
                
                <form action="#" class="w-full space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <div id="field-nama" class="border-b border-gray-300 transition-colors">
                                <label for="nama_lengkap" class="block text-sm font-medium text-brand-dark mb-2">Nama Lengkap</label>
                                <input type="text" id="nama_lengkap" name="nama_lengkap" class="w-full py-1 bg-transparent focus:outline-none">
                            </div>
                            <span id="nama_lengkap-error" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <div id="field-email" class="border-b border-gray-300 transition-colors">
                                <label for="email" class="block text-sm font-medium text-brand-dark mb-2">Email</label>
                                <input type="email" id="reg_email" name="email" class="w-full py-1 bg-transparent focus:outline-none">
                            </div>
                            <span id="reg_email-error" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <div id="field-nohp" class="border-b border-gray-300 transition-colors">
                                <label for="no_hp" class="block text-sm font-medium text-brand-dark mb-2">No. HP</label>
                                <input type="text" id="no_hp" name="no_hp" class="w-full py-1 bg-transparent focus:outline-none">
                            </div>
                            <span id="no_hp-error" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        
                        <div class="md:col-span-2">
                            <div id="field-perusahaan" class="border-b border-gray-300 transition-colors">
                                <label for="nama_perusahaan" class="block text-sm font-medium text-brand-dark mb-2">Nama Perusahaan</label>
                                <input type="text" id="nama_perusahaan" name="nama_perusahaan" class="w-full py-1 bg-transparent focus:outline-none">
                            </div>
                            <span id="nama_perusahaan-error" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>

                        <div class="md:col-span-2">
                            <div id="field-domisili" class="border-b border-gray-300 transition-colors">
                                <label for="domisili" class="block text-sm font-medium text-brand-dark mb-2">Domisili Perusahaan</label>
                                <el-select id="domisili" name="domisili" class="block w-full">
                                    <button type="button" class="grid w-full cursor-default grid-cols-1 py-1 text-left text-brand-dark bg-transparent focus:outline-none sm:text-sm/6">
                                        <el-selectedcontent class="col-start-1 row-start-1 flex items-center gap-3 pr-5">
                                            <span class="block truncate text-sm text-gray-400">Pilih Domisili Perusahaan</span>
                                        </el-selectedcontent>
                                        <svg class="col-start-1 row-start-1 size-4 self-center justify-self-end text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <el-options anchor="bottom start" popover class="max-h-56 w-(--button-width) overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 [--anchor-gap:--spacing(1)] data-leave:transition data-leave:transition-discrete data-leave:duration-100 data-leave:ease-in data-closed:data-leave:opacity-0 sm:text-sm">
                                        <el-option value="kontraktor" class="group/option relative block cursor-default py-2 pr-9 pl-3 text-brand-dark select-none focus:bg-primary focus:text-white focus:outline-hidden">
                                            <div class="flex items-center">
                                                <span class="block truncate font-normal group-aria-selected/option:font-semibold">Yogyakarta</span>
                                            </div>
                                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary group-not-aria-selected/option:hidden group-focus/option:text-white in-[el-selectedcontent]:hidden">
                                                <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                                    <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </el-option>
                                    </el-options>
                                </el-select>
                            </div>
                            <span id="domisili-error" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        
                        <div class="md:col-span-2">
                            <div id="field-posisi" class="border-b border-gray-300 transition-colors">
                            <label for="posisi" class="block text-sm font-medium text-brand-dark mb-2">Posisi Pekerjaan</label>
                            <el-select id="posisi" name="posisi" class="block w-full">
                                <button type="button" class="grid w-full cursor-default grid-cols-1 py-1 text-left text-brand-dark bg-transparent focus:outline-none sm:text-sm/6">
                                    <el-selectedcontent class="col-start-1 row-start-1 flex items-center gap-3 pr-5">
                                        <span class="block truncate text-sm text-gray-400">Pilih Posisi Pekerjaan</span>
                                    </el-selectedcontent>
                                    <svg class="col-start-1 row-start-1 size-4 self-center justify-self-end text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <el-options anchor="bottom start" popover class="max-h-56 w-(--button-width) overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 [--anchor-gap:--spacing(1)] data-leave:transition data-leave:transition-discrete data-leave:duration-100 data-leave:ease-in data-closed:data-leave:opacity-0 sm:text-sm">
                                    <el-option value="kontraktor" class="group/option relative block cursor-default py-2 pr-9 pl-3 text-brand-dark select-none focus:bg-primary focus:text-white focus:outline-hidden">
                                        <div class="flex items-center">
                                            <span class="block truncate font-normal group-aria-selected/option:font-semibold">Kontraktor</span>
                                        </div>
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary group-not-aria-selected/option:hidden group-focus/option:text-white in-[el-selectedcontent]:hidden">
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                                <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </el-option>
                                    <el-option value="purchasing" class="group/option relative block cursor-default py-2 pr-9 pl-3 text-brand-dark select-none focus:bg-primary focus:text-white focus:outline-hidden">
                                        <div class="flex items-center">
                                            <span class="block truncate font-normal group-aria-selected/option:font-semibold">Purchasing</span>
                                        </div>
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary group-not-aria-selected/option:hidden group-focus/option:text-white in-[el-selectedcontent]:hidden">
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                                <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </el-option>
                                    <el-option value="gudang" class="group/option relative block cursor-default py-2 pr-9 pl-3 text-brand-dark select-none focus:bg-primary focus:text-white focus:outline-hidden">
                                        <div class="flex items-center">
                                            <span class="block truncate font-normal group-aria-selected/option:font-semibold">Gudang</span>
                                        </div>
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary group-not-aria-selected/option:hidden group-focus/option:text-white in-[el-selectedcontent]:hidden">
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                                <path d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" fill-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </el-option>
                                </el-options>
                            </el-select>
                            </div>
                            <span id="posisi-error" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        
                        <div>
                            <div id="field-password" class="relative border-b border-gray-300 transition-colors">
                                <label for="password" class="block text-sm font-medium text-brand-dark mb-2">Password</label>
                                <input type="password" id="password" name="password" class="w-full pr-8 py-1 bg-transparent focus:outline-none">
                                <button type="button" id="togglePassword" class="toggle-pass absolute right-0 bottom-1 text-brand-dark hover:text-gray-500 transition-colors focus:outline-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <span id="password-error" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <div id="field-confirm" class="relative border-b border-gray-300 transition-colors">
                                <label for="confirmPassword" class="block text-sm font-medium text-brand-dark mb-2">Konfirmasi Password</label>
                                <input type="password" id="confirmPassword" name="confirm_password" class="w-full pr-8 py-1 bg-transparent focus:outline-none">
                                <button type="button" id="toggleConfirmPassword" class="toggle-pass absolute right-0 bottom-1 text-brand-dark hover:text-gray-500 transition-colors focus:outline-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <span id="confirmPassword-error" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>

                       
                    </div>
                    <div class="flex items-center justify-between mb-8 text-xs sm:text-sm">
                        <label class="flex items-center cursor-pointer text-brand-dark text-xs">
                            <input type="checkbox" class="w-4 h-4 mr-2 rounded accent-secondary cursor-pointer">
                            Saya Menyetujui Syarat dan Ketentuan pada Kontraktor.id
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-primary text-white font-semibold py-3 rounded-md hover:bg-opacity-90 transition-all">
                        Buat Akun
                    </button>
                </form>

            </div>
        </section>
    </main>

    <script src="<?= base_url('assets/js/loginUI.js') ?>"></script>
    <script src="./node_modules/preline/dist/preline.js"></script>
    <script src="<?= base_url('assets/js/notification/registerValidation.js') ?>"></script>
</body>
</html>