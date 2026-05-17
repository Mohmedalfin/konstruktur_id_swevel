<header class="relative text-white py-6 md:py-8 bg-cover bg-center bg-no-repeat overflow-hidden" style="background-image: url('<?= base_url('assets/images/BackgroundTopBar.png') ?>');">
    
    <div class="absolute inset-0 bg-primary/75"></div>

    <div class="relative max-w-[90rem] mx-auto px-4 group text-center">
        <h1 class="text-lg md:text-xl lg:text-2xl font-bold tracking-tight">
            <?= isset($title) ? $title : 'Project Management' ?>
        </h1>
        <p class="text-xs md:text-sm text-white/80 max-w-xl mx-auto leading-relaxed font-light transition-all duration-500 opacity-60 group-hover:opacity-100">
            Optimalkan alur kerja dan pencapaian tim.
        </p>
    </div>
</header>