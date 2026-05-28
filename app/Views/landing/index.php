<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontraktor.id - Control Your Project Margin</title>
    <!-- Tailwind CSS -->
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/input.css') ?>" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- FontAwesome (if available or standard link) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        /* Extra custom styles if needed */
        body {
            font-family: 'Montserrat', sans-serif;
        }

        /* Swiper Customizations */
        .swiper-pagination-bullet {
            background-color: #6b7280 !important;
            /* gray-500 */
            opacity: 1 !important;
            width: 10px !important;
            height: 10px !important;
            margin: 0 6px !important;
        }

        .swiper-pagination-bullet-active {
            background-color: #0f1831 !important;
            /* brand-dark */
        }
    </style>
</head>

<body class="bg-brand-cream text-brand-dark antialiased overflow-x-hidden">

    <!-- HERO SECTION -->
    <section id="beranda" class="relative w-full h-screen min-h-[600px] flex flex-col bg-brand-dark bg-cover bg-center bg-no-repeat"
        style="background-image: url('<?= base_url('assets/images/bg-header.png') ?>');">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-brand-dark/70"></div>

        <!-- Navbar -->
        <header id="main-nav" class="fixed top-0 left-0 right-0 z-50 w-full transition-all duration-300">
            <div id="nav-container"
                class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between transition-all duration-300">
                <div class="flex items-center gap-2">
                    <img src="<?= base_url('assets/images/logoKonstruktor.png') ?>" alt="Logo" class="h-8 w-auto">
                    <span class="text-[#F59E0B] font-bold text-xl tracking-wide">Kontraktor.id</span>
                </div>

                <div class="hidden md:flex items-center gap-8 ml-auto">
                    <nav class="flex gap-8 text-white font-medium text-sm">
                        <a href="#beranda" class="group relative hover:text-[#F59E0B] transition-colors duration-300">
                            Beranda
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#F59E0B] transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <a href="#solusi" class="group relative hover:text-[#F59E0B] transition-colors duration-300">
                            Solusi
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#F59E0B] transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <a href="#testimoni" class="group relative hover:text-[#F59E0B] transition-colors duration-300">
                            Testimoni
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#F59E0B] transition-all duration-300 group-hover:w-full"></span>
                        </a>
                        <a href="#kontak" class="group relative hover:text-[#F59E0B] transition-colors duration-300">
                            Kontak
                            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#F59E0B] transition-all duration-300 group-hover:w-full"></span>
                        </a>
                    </nav>

                    <div class="flex items-center gap-4">
                        <a href="<?= base_url('login') ?>"
                            class="border border-[#F59E0B] text-white hover:bg-[#F59E0B] font-semibold text-sm px-8 py-2 rounded-full transition-colors">Login</a>
                        <a href="<?= base_url('Register') ?>"
                            class="bg-[#F59E0B] text-white font-semibold text-sm px-8 py-2 rounded-full hover:bg-yellow-600 transition-colors">SignUp</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Content -->
        <div class="relative z-10 flex-1 flex flex-col items-center justify-center text-center px-6">
            <h1 data-aos="fade-up" class="text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight">Control Your Project Margin
            </h1>
            <p data-aos="fade-up" data-aos-delay="200" class="text-lg md:text-xl text-gray-200 mb-10 max-w-2xl font-medium">
                Manage RAB, RAP, schedule, and real costs in one integrated system.
            </p>
            <div data-aos="zoom-in" data-aos-delay="400">
                <a href="<?= base_url('Register') ?>"
                    class="inline-block bg-[#F59E0B] text-white font-bold text-xl px-10 py-3 rounded-full hover:bg-yellow-600 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 duration-200">
                    Start Free Trial
                </a>
            </div>
        </div>

        <!-- Bottom border -->
        <div class="absolute bottom-0 left-0 w-full h-3 bg-[#F59E0B] z-10"></div>
    </section>


    <!-- MASALAH UMUM SECTION -->
    <section class="py-20 px-6 relative overflow-hidden" style="background-color: #f5f0e8;">
        <!-- Decorative city silhouette left -->
        <div class="absolute left-0 bottom-0 h-full w-64 opacity-20 pointer-events-none hidden lg:block">
            <svg viewBox="0 0 200 400" class="h-full w-full" xmlns="http://www.w3.org/2000/svg"
                preserveAspectRatio="xMinYMax meet">
                <rect x="10" y="300" width="30" height="100" fill="#4a5568" />
                <rect x="15" y="280" width="20" height="20" fill="#4a5568" />
                <rect x="50" y="250" width="40" height="150" fill="#4a5568" />
                <rect x="55" y="235" width="30" height="20" fill="#4a5568" />
                <rect x="60" y="220" width="20" height="15" fill="#4a5568" />
                <rect x="100" y="270" width="35" height="130" fill="#4a5568" />
                <rect x="105" y="255" width="25" height="18" fill="#4a5568" />
                <rect x="145" y="310" width="25" height="90" fill="#4a5568" />
                <rect x="150" y="300" width="15" height="12" fill="#4a5568" />
                <rect x="5" y="330" width="200" height="5" fill="#4a5568" />
            </svg>
        </div>
        <!-- Decorative city silhouette right -->
        <div class="absolute right-0 bottom-0 h-full w-64 opacity-20 pointer-events-none hidden lg:block">
            <svg viewBox="0 0 200 400" class="h-full w-full" xmlns="http://www.w3.org/2000/svg"
                preserveAspectRatio="xMaxYMax meet">
                <rect x="160" y="300" width="30" height="100" fill="#4a5568" />
                <rect x="165" y="280" width="20" height="20" fill="#4a5568" />
                <rect x="110" y="250" width="40" height="150" fill="#4a5568" />
                <rect x="115" y="235" width="30" height="20" fill="#4a5568" />
                <rect x="120" y="220" width="20" height="15" fill="#4a5568" />
                <rect x="65" y="270" width="35" height="130" fill="#4a5568" />
                <rect x="70" y="255" width="25" height="18" fill="#4a5568" />
                <rect x="25" y="310" width="25" height="90" fill="#4a5568" />
                <rect x="30" y="300" width="15" height="12" fill="#4a5568" />
                <rect x="0" y="330" width="200" height="5" fill="#4a5568" />
            </svg>
        </div>

        <div class="max-w-6xl mx-auto text-center relative z-10">
            <h2 data-aos="fade-up" class="text-3xl md:text-4xl font-bold text-brand-dark mb-12">Masalah Umum dalam Proyek Konstruksi</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 max-w-4xl mx-auto">
                <!-- Card 1 -->
                <div data-aos="fade-up" data-aos-delay="100"
                    class="bg-[#F59E0B] text-white p-8 rounded-xl shadow-lg flex flex-col items-center text-center transform transition duration-300 hover:scale-105">
                    <div class="text-5xl mb-4 opacity-90"><i class="fa-regular fa-file-lines"></i></div>
                    <h3 class="text-xl font-bold mb-3">RAP Tidak Konsisten Dijadikan Baseline Biaya</h3>
                    <p class="text-sm opacity-90 leading-relaxed font-medium">RAP telah disusun sebagai acuan biaya
                        internal, namun dalam pelaksanaan sering tidak digunakan secara disiplin sebagai baseline
                        pengendalian.</p>
                </div>

                <!-- Card 2 -->
                <div data-aos="fade-up" data-aos-delay="200"
                    class="bg-[#F59E0B] text-white p-8 rounded-xl shadow-lg flex flex-col items-center text-center transform transition duration-300 hover:scale-105">
                    <div class="text-5xl mb-4 opacity-90"><i class="fa-solid fa-arrow-trend-down"></i></div>
                    <h3 class="text-xl font-bold mb-3">Deviasi Biaya Tidak Terdeteksi Sejak Dini</h3>
                    <p class="text-sm opacity-90 leading-relaxed font-medium">Selisih kuantitas, harga satuan, atau
                        produktivitas tenaga kerja tidak dimonitor secara berkala sehingga pembengkakan biaya baru
                        terlihat di akhir proyek.</p>
                </div>

                <!-- Card 3 -->
                <div data-aos="fade-up" data-aos-delay="300"
                    class="bg-[#F59E0B] text-white p-8 rounded-xl shadow-lg flex flex-col items-center text-center transform transition duration-300 hover:scale-105">
                    <div class="text-5xl mb-4 opacity-90"><i class="fa-regular fa-calendar-days"></i></div>
                    <h3 class="text-xl font-bold mb-3">Progress Fisik Tidak Selaras dengan Progress Keuangan</h3>
                    <p class="text-sm opacity-90 leading-relaxed font-medium">Pencapaian progres pekerjaan tidak selalu
                        diikuti dengan evaluasi biaya aktual terhadap anggaran, sehingga performa proyek sulit diukur
                        secara menyeluruh.</p>
                </div>

                <!-- Card 4 -->
                <div data-aos="fade-up" data-aos-delay="400"
                    class="bg-[#F59E0B] text-white p-8 rounded-xl shadow-lg flex flex-col items-center text-center transform transition duration-300 hover:scale-105">
                    <div class="text-5xl mb-4 opacity-90"><i class="fa-regular fa-folder-open"></i></div>
                    <h3 class="text-xl font-bold mb-3">Data Anggaran dan Realisasi Tidak Terintegrasi</h3>
                    <p class="text-sm opacity-90 leading-relaxed font-medium">RAB, RAP, laporan pembelian, dan laporan
                        realisasi tersimpan terpisah sehingga menyulitkan proses kontrol, audit, dan evaluasi kinerja
                        proyek.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- SOLUSI SECTION -->
    <section id="solusi" class="py-20 px-6 bg-primary text-white">
        <div class="max-w-6xl mx-auto">

            <h2 data-aos="fade-up" class="text-3xl md:text-4xl font-bold text-center mb-16">Temukan Solusi dari<br>Permasalahanmu di
                Kontraktor.id</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
                <div data-aos="fade-right" class="rounded-xl overflow-hidden shadow-2xl bg-black">
                    <video controls poster="<?= base_url('assets/images/hero-landingPage.jpeg') ?>" class="w-full h-auto aspect-video object-cover">
                        <source src="<?= base_url('assets/images/about_estimator.mp4') ?>" type="video/mp4">
                        Browser Anda tidak mendukung tag video.
                    </video>
                </div>

                <div data-aos="fade-left" class="flex flex-col justify-center">
                    <h3 class="text-2xl font-bold mb-4">Apa itu Kontraktor.id?</h3>
                    <p class="text-gray-300 leading-relaxed text-sm md:text-base">
                        Kontraktor.id adalah sistem pengendalian biaya proyek konstruksi yang menghubungkan proses
                        estimasi, perencanaan anggaran internal, hingga realisasi lapangan dalam satu ekosistem digital.
                    </p>
                </div>
            </div>

            <div class="text-center">
                <h3 data-aos="fade-up" class="text-2xl md:text-3xl font-bold mb-10">Kenapa Menggunakan<br>Kontraktor.id?</h3>

                <div class="flex flex-wrap justify-center gap-4 max-w-4xl mx-auto">
                    <div data-aos="zoom-in-up" data-aos-delay="100"
                        class="bg-white text-text-primary px-6 py-4 rounded-xl flex items-center gap-3 shadow-lg font-bold text-sm">
                        <i class="fa-solid fa-shield-halved text-xl"></i>
                        Margin Proyek Lebih Terkontrol
                    </div>
                    <div data-aos="zoom-in-up" data-aos-delay="200"
                        class="bg-white text-text-primary px-6 py-4 rounded-xl flex items-center gap-3 shadow-lg font-bold text-sm">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                        Kontrol Biaya Lebih Disiplin
                    </div>
                    <div data-aos="zoom-in-up" data-aos-delay="300"
                        class="bg-white text-text-primary px-6 py-4 rounded-xl flex items-center gap-3 shadow-lg font-bold text-sm">
                        <i class="fa-solid fa-chart-column text-xl"></i>
                        Pengambilan Keputusan Berbasis Data
                    </div>
                    <div data-aos="zoom-in-up" data-aos-delay="400"
                        class="bg-white text-text-primary px-6 py-4 rounded-xl flex items-center gap-3 shadow-lg font-bold text-sm mt-0 md:mt-2">
                        <i class="fa-solid fa-file-circle-check text-xl"></i>
                        Evaluasi Proyek Lebih Mudah
                    </div>
                    <div data-aos="zoom-in-up" data-aos-delay="500"
                        class="bg-white text-text-primary px-6 py-4 rounded-xl flex items-center gap-3 shadow-lg font-bold text-sm mt-0 md:mt-2">
                        <i class="fa-solid fa-eye text-xl"></i>
                        Transparansi dan Akuntabilitas Lebih Baik
                    </div>
                </div>
            </div>

        </div>
    </section>


    <!-- TESTIMONI SECTION -->
    <section id="testimoni" class="relative py-24 bg-cover bg-center bg-no-repeat bg-fixed"
        style="background-image: url('<?= base_url('assets/images/bg-testimoni.jpeg') ?>');">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-white/80 backdrop-blur-sm"></div>

        <div class="relative z-10 max-w-4xl mx-auto text-center px-6">
            <h2 data-aos="fade-up" class="text-3xl md:text-4xl font-extrabold text-brand-dark mb-2">Testimoni</h2>
            <p data-aos="fade-up" data-aos-delay="100" class="text-gray-600 mb-12 font-medium">Apa kata mereka yang telah menggunakan Kontraktor.id?</p>

            <div data-aos="zoom-in-up" data-aos-delay="300" class="swiper testimoniSwiper">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide">
                        <div class="flex flex-col items-center px-8 pb-16">
                            <div
                                class="w-24 h-24 rounded-full border-4 border-brand-dark overflow-hidden mb-6 shadow-lg bg-gray-200">
                                <img src="<?= base_url('assets/images/testimoni-1.jpg') ?>" alt="Usama Fadlillah"
                                    class="w-full h-full object-cover">
                            </div>
                            <h4 class="text-xl font-bold text-brand-dark">Usama Fadlillah, S.T</h4>
                            <p class="text-sm font-semibold text-gray-500 mb-6">Project Manager PT KALIMANTAN SEJAHTERA
                            </p>
                            <p class="text-gray-700 italic max-w-2xl mx-auto leading-relaxed">
                                "Sebelumnya kami mengontrol RAP dan realisasi secara terpisah menggunakan beberapa file.
                                Dengan
                                sistem ini, kami bisa langsung melihat deviasi biaya per item pekerjaan tanpa menunggu
                                akhir proyek.
                                Kontrol margin jadi jauh lebih terukur."
                            </p>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="swiper-slide">
                        <div class="flex flex-col items-center px-8 pb-16">
                            <div
                                class="w-24 h-24 rounded-full border-4 border-brand-dark overflow-hidden mb-6 shadow-lg bg-gray-200">
                                <img src="<?= base_url('assets/images/testimoni-2.jpg') ?>" alt="Budi Santoso"
                                    class="w-full h-full object-cover">
                            </div>
                            <h4 class="text-xl font-bold text-brand-dark">Attar Hidayatullah, S.T</h4>
                            <p class="text-sm font-semibold text-gray-500 mb-6">Direktur Operasional PT JAMBI MAKMUR</p>
                            <p class="text-gray-700 italic max-w-2xl mx-auto leading-relaxed">
                                "Aplikasi ini sangat membantu tim kami di lapangan. Monitoring progres dan biaya bisa
                                dilakukan
                                secara real-time. Efisiensi waktu yang didapat sangat signifikan dan komunikasi tim jadi
                                lebih lancar."
                            </p>
                        </div>
                    </div>

                    <!-- Slide 3 -->
                    <div class="swiper-slide">
                        <div class="flex flex-col items-center px-8 pb-16">
                            <div
                                class="w-24 h-24 rounded-full border-4 border-brand-dark overflow-hidden mb-6 shadow-lg bg-gray-200">
                                <img src="<?= base_url('assets/images/testimoni-3.jpg') ?>" alt="Andi Wijaya"
                                    class="w-full h-full object-cover">
                            </div>
                            <h4 class="text-xl font-bold text-brand-dark">Muhammad Alfin, S.T, M.T</h4>
                            <p class="text-sm font-semibold text-gray-500 mb-6">Site Manager PT KUNINGAN BERHASIL</p>
                            <p class="text-gray-700 italic max-w-2xl mx-auto leading-relaxed">
                                "Laporan harian dan mingguan yang biasanya menyita waktu berjam-jam, kini bisa
                                diselesaikan
                                dengan beberapa klik. Sangat direkomendasikan untuk proyek skala menengah hingga besar."
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="flex flex-col items-center px-8 pb-16">
                            <div
                                class="w-24 h-24 rounded-full border-4 border-brand-dark overflow-hidden mb-6 shadow-lg bg-gray-200">
                                <img src="<?= base_url('assets/images/testimoni-4.jpg') ?>" alt="Andi Wijaya"
                                    class="w-full h-full object-cover">
                            </div>
                            <h4 class="text-xl font-bold text-brand-dark">Muhammad Gagah, S.T</h4>
                            <p class="text-sm font-semibold text-gray-500 mb-6">Site Manager PT KLATEN JAYA</p>
                            <p class="text-gray-700 italic max-w-2xl mx-auto leading-relaxed">
                                "GACORRR KANGG, APLIKASI INI GACORRR POLLLL"
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>


    <!-- FOOTER -->
    <footer id="kontak" class="bg-[#0b1120] text-gray-300 pt-16 pb-6 border-t border-gray-800">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">

            <!-- Left -->
            <div>
                <h3 class="text-white text-xl font-bold mb-6">Hubungi Kami</h3>
                <p class="text-sm mb-6 max-w-md leading-relaxed text-gray-400">
                    Kami sangat senang bisa berkomunikasi dengan Anda. Pastikan selalu terhubung dengan kami untuk
                    mendapatkan informasi terbaru.
                </p>

                <div class="flex flex-col gap-4 mb-8">
                    <a href="mailto:supports@kontraktor.id" class="group flex items-center gap-3 text-sm text-gray-400 hover:text-[#F59E0B] transition-colors duration-300">
                        <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center group-hover:bg-[#F59E0B] group-hover:text-white transition-all duration-300 group-hover:scale-110">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <span class="group-hover:translate-x-1 transition-transform duration-300">supports@kontraktor.id</span>
                    </a>
                    <a href="tel:+62274511067" class="group flex items-center gap-3 text-sm text-gray-400 hover:text-[#F59E0B] transition-colors duration-300">
                        <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center group-hover:bg-[#F59E0B] group-hover:text-white transition-all duration-300 group-hover:scale-110">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <span class="group-hover:translate-x-1 transition-transform duration-300">+62274-511067</span>
                    </a>
                </div>

                <div class="flex gap-4">
                    <a href="#"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-[#F59E0B] hover:text-white hover:-translate-y-1 hover:shadow-lg hover:shadow-[#F59E0B]/30 transition-all duration-300">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-[#F59E0B] hover:text-white hover:-translate-y-1 hover:shadow-lg hover:shadow-[#F59E0B]/30 transition-all duration-300">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-[#F59E0B] hover:text-white hover:-translate-y-1 hover:shadow-lg hover:shadow-[#F59E0B]/30 transition-all duration-300">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- Right -->
            <div class="md:pl-12">
                <h3 class="text-white text-xl font-bold mb-6">Tentang Kami</h3>
                <ul class="flex flex-col gap-4 text-sm text-gray-400">
                    <li>
                        <a href="#" class="group flex items-center gap-3 hover:text-[#F59E0B] transition-colors duration-300">
                            <i class="fa-solid fa-chart-pie w-5 text-center group-hover:scale-110 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-1 transition-transform duration-300">Ringkasan Eksekutif</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center gap-3 hover:text-[#F59E0B] transition-colors duration-300">
                            <i class="fa-solid fa-book-open w-5 text-center group-hover:scale-110 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-1 transition-transform duration-300">Cara Penggunaan</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center gap-3 hover:text-[#F59E0B] transition-colors duration-300">
                            <i class="fa-solid fa-circle-question w-5 text-center group-hover:scale-110 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-1 transition-transform duration-300">FAQ</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center gap-3 hover:text-[#F59E0B] transition-colors duration-300">
                            <i class="fa-solid fa-user-shield w-5 text-center group-hover:scale-110 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-1 transition-transform duration-300">Kebijakan Privasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center gap-3 hover:text-[#F59E0B] transition-colors duration-300">
                            <i class="fa-solid fa-scale-balanced w-5 text-center group-hover:scale-110 transition-transform duration-300"></i>
                            <span class="group-hover:translate-x-1 transition-transform duration-300">Peraturan & Ketentuan</span>
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <div class="border-t border-gray-800/80 pt-6 text-center text-xs text-gray-500">
            &copy; 2026. Kontraktor Indonesia
        </div>
    </footer>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        // Initialize Swiper
        var swiper = new Swiper(".testimoniSwiper", {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            }
        });

        // Navbar Scroll Effect
        const nav = document.getElementById('main-nav');
        const navContainer = document.getElementById('nav-container');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                // Floating state
                nav.classList.add('pt-4', 'px-4');
                navContainer.classList.add('bg-[#0f1831]', 'shadow-2xl', 'rounded-2xl', 'border', 'border-gray-800', 'py-3');
                navContainer.classList.remove('py-5');
            } else {
                // Top transparent state
                nav.classList.remove('pt-4', 'px-4');
                navContainer.classList.remove('bg-[#0f1831]', 'shadow-2xl', 'rounded-2xl', 'border', 'border-gray-800', 'py-3');
                navContainer.classList.add('py-5');
            }
        });

        // Smooth scrolling without changing URL hash
        document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
        });
    </script>
</body>

</html>