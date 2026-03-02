<header class="relative text-white py-6 md:py-8 bg-cover bg-center bg-no-repeat overflow-hidden" style="background-image: url('<?= base_url('assets/images/BackgroundTopBar.png') ?>');">
    
    <div class="absolute inset-0 bg-primary/75"></div>

    <div class="absolute inset-0 pointer-events-none">
        <i class="fa-solid fa-layer-group absolute top-4 left-[8%] text-lg opacity-40 animate-bounce"></i>
        <i class="fa-solid fa-list-check absolute bottom-6 left-[18%] text-xl opacity-30 animate-[pulse_3s_infinite]"></i>
        <i class="fa-solid fa-clock-rotate-left absolute top-1/2 right-[12%] text-lg opacity-40 animate-[spin_8s_linear_infinite]"></i>
        <i class="fa-solid fa-chart-pie absolute bottom-4 right-[8%] text-xl opacity-30 animate-[bounce_5s_infinite]"></i>
    </div>

    <div class="relative max-w-[90rem] mx-auto px-4 group text-center">
        <h1 class="text-lg md:text-xl lg:text-2xl font-bold tracking-tight transition-all duration-500 group-hover:tracking-[0.2em]">
            <?= isset($title) ? $title : 'Project Management' ?>
        </h1>
        <p class="text-xs md:text-sm text-white/80 max-w-xl mx-auto leading-relaxed font-light transition-all duration-500 opacity-60 group-hover:opacity-100">
            Optimalkan alur kerja dan pencapaian tim.
        </p>
    </div>
</header>