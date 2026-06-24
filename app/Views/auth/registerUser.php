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
                
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-700 border border-red-400 text-sm w-full">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('validation_errors')) : ?>
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-700 border border-red-400 text-sm w-full">
                        <ul class="list-disc pl-4">
                        <?php foreach(session()->getFlashdata('validation_errors') as $err): ?>
                            <li><?= $err ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= site_url('auth/registerProcess') ?>" method="POST" class="w-full space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <div id="field-nama" class="border-b border-gray-300 transition-colors">
                                <label for="nama_lengkap" class="block text-sm font-medium text-brand-dark mb-2">Nama Lengkap</label>
                                <input type="text" id="nama_lengkap" name="nama_pengguna" value="<?= old('nama_pengguna') ?>" class="w-full py-1 bg-transparent focus:outline-none">
                            </div>
                            <span id="nama_lengkap-error" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div>
                            <div id="field-email" class="border-b border-gray-300 transition-colors">
                                <label for="email" class="block text-sm font-medium text-brand-dark mb-2">Email</label>
                                <input type="email" id="reg_email" name="email" value="<?= old('email') ?>" class="w-full py-1 bg-transparent focus:outline-none">
                            </div>
                            <span id="reg_email-error" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div class="md:col-span-2">
                            <div id="field-username" class="border-b border-gray-300 transition-colors">
                                <label for="username" class="block text-sm font-medium text-brand-dark mb-2">Username</label>
                                <input type="text" id="username" name="username" value="<?= old('username') ?>" class="w-full py-1 bg-transparent focus:outline-none">
                            </div>
                            <span id="username-error" class="text-red-500 text-xs mt-1 hidden"></span>
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
                                <select name="id_wilayah" id="domisili" data-hs-select='{
                                  "hasSearch": true,
                                  "searchPlaceholder": "Cari Kabupaten/Kota...",
                                  "searchClasses": "block w-full text-sm bg-white border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500",
                                  "searchWrapperClasses": "bg-white p-2 -mx-1 sticky top-0",
                                  "placeholder": "Pilih Kabupaten/Kota",
                                  "toggleTag": "<button type=\"button\" aria-expanded=\"false\"></button>",
                                  "toggleClasses": "relative py-2.5 ps-3 pe-9 flex w-full cursor-pointer bg-transparent border-b border-gray-300 text-start text-sm focus:outline-none focus:border-brand-dark transition-colors",
                                  "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 shadow-xl overflow-hidden overflow-y-auto",
                                  "optionClasses": "hs-selected:bg-gray-100 py-2 px-3 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-50",
                                  "optionTemplate": "<div class=\"flex justify-between items-center w-full\"><span data-title></span><span class=\"hidden hs-selected:block\"><svg class=\"size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 16 16\" fill=\"currentColor\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div>",
                                  "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"size-4 text-gray-500\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 20 20\" fill=\"currentColor\"><path fill-rule=\"evenodd\" d=\"M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06Z\" clip-rule=\"evenodd\"/></svg></div>"
                                }' class="hidden">
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?= $city['id'] ?>"><?= esc($city['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <span id="domisili-error" class="text-red-500 text-xs mt-1 hidden"></span>
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
    <script src="<?= base_url('assets/js/preline.js') ?>"></script>
    <script>
        window.addEventListener('load', () => {
            window.HSStaticMethods?.autoInit();
        });
    </script>
    <script src="<?= base_url('assets/js/notification/registerValidation.js') ?>"></script>
</body>
</html>