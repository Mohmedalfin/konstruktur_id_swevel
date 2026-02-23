<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kontraktor.id</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen p-4 sm:p-8 relative bg-cover bg-center bg-no-repeat" 
      style="background-image: url('<?= base_url('assets/images/BackgroundLogin.png') ?>');">

    <div class="absolute inset-0 bg-brand-bg opacity-90"></div>    
    <main class="flex flex-col md:flex-row w-full max-w-[1000px] h-auto md:h-[600px] bg-brand-cream shadow-2xl rounded-sm overflow-hidden z-10">
        
        <section class="w-full h-48 md:h-auto md:w-1/2 relative flex-shrink-0">
            <img src="<?= base_url('assets/images/loginImage.jpeg') ?>" 
                 alt="Construction Site" 
                 class="absolute inset-0 w-full h-full object-cover">
        </section>

        <section class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-[#FDFBF7]">
            
            <div class="flex items-center justify-center gap-2 mb-8">
                <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3L2 12h3v8h14v-8h3L12 3zm0 2.83l5 4.5V18H7v-7.67l5-4.5z"/>
                    <path d="M11 11h2v4h-2z"/>
                </svg>
                <span class="text-yellow-500 text-xl font-bold tracking-wide">Kontraktor.id</span>
            </div>

            <h1 class="hidden md:block text-2xl lg:text-[26px] font-bold text-brand-dark text-center mb-10">
                Masuk ke Akun Kontraktor.id
            </h1>

            <form action="#" method="POST" class="flex flex-col">
                
                <div class="mb-6 relative">
                    <label for="email" class="block text-sm font-medium text-brand-dark mb-2">Email</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full border-b border-gray-300 bg-transparent py-2 text-brand-dark focus:outline-none focus:border-brand-dark transition-colors"
                           placeholder=" ">
                </div>

                <div class="mb-6 relative">
                    <label for="password" class="block text-sm font-medium text-brand-dark mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required 
                               class="w-full border-b border-gray-300 bg-transparent py-2 pr-10 text-brand-dark focus:outline-none focus:border-brand-dark transition-colors">
                        <button type="button" id="togglePassword" class="absolute right-0 top-1/2 -translate-y-1/2 text-brand-dark hover:text-gray-500 transition-colors focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-8 text-xs sm:text-sm">
                    <label class="flex items-center cursor-pointer text-brand-dark font-medium">
                        <input type="checkbox" class="w-4 h-4 mr-2 border-gray-300 rounded text-brand-dark focus:ring-brand-dark">
                        Ingat Saya
                    </label>
                    <a href="#" class="text-brand-dark font-bold hover:underline">Lupa Password?</a>
                </div>

                <button type="submit" class="w-full bg-brand-dark text-white font-semibold py-3 px-4 rounded-md hover:bg-[#0f1831] transition-colors focus:ring-4 focus:ring-blue-900 focus:outline-none">
                    Masuk
                </button>

            </form>

            <p class="text-center text-sm text-gray-500 mt-6 font-medium">
                Belum mempunyai akun? <a href="#" class="text-brand-dark font-bold hover:underline">Daftar</a>
            </p>

        </section>
    </main>

    <script src="<?= base_url('assets/js/loginUI.js') ?>"></script>
</body>
</html>