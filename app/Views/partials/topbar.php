<?php
$pageTitle = isset($title) ? $title : 'Project Management';
$tagline = 'Optimalkan alur kerja dan pencapaian tim.';

$titleLower = strtolower($pageTitle);

if (strpos($titleLower, 'permintaan') !== false) {
    $tagline = 'Kelola dan pantau seluruh permohonan material masuk dari lapangan.';
} elseif (strpos($titleLower, 'pengadaan') !== false) {
    $tagline = 'Buat dan kelola permohonan pembelian (Purchase Request) material secara efisien.';
} elseif (strpos($titleLower, 'stok') !== false) {
    $tagline = 'Pantau ketersediaan dan status batas minimum setiap item di gudang.';
} elseif (strpos($titleLower, 'gudang lapangan') !== false || strpos($titleLower, 'site inventory') !== false) {
    $tagline = 'Pantau stok material fisik di lapangan proyek dan kelola retur ke gudang pusat.';
} elseif (strpos($titleLower, 'master') !== false || strpos($titleLower, 'barang') !== false) {
    $tagline = 'Kelola data referensi master barang, kategori, dan satuan.';
} elseif (strpos($titleLower, 'rab') !== false) {
    $tagline = 'Susun dan kelola Rencana Anggaran Biaya proyek dengan akurat.';
} elseif (strpos($titleLower, 'proyek') !== false) {
    $tagline = 'Pantau progress, kelola tim, dan pastikan proyek berjalan sesuai rencana.';
}

// Gunakan UPPERCASE agar konsisten dan tegas
$formattedTitle = strtoupper($pageTitle);
?>
<header class="relative text-white py-6 md:py-8 bg-cover bg-center bg-no-repeat overflow-hidden" style="background-image: url('<?= base_url('assets/images/BackgroundTopBar.png') ?>');">
    
    <div class="absolute inset-0 bg-primary/75"></div>

    <div class="relative max-w-[90rem] mx-auto px-4 group text-center">
        <h1 class="text-lg md:text-xl lg:text-2xl font-bold tracking-widest">
            <?= esc($formattedTitle) ?>
        </h1>
        <p class="text-xs md:text-sm text-white/80 max-w-xl mx-auto leading-relaxed font-light transition-all duration-500 opacity-70 group-hover:opacity-100 mt-2">
            <?= esc($tagline) ?>
        </p>
    </div>
</header>