<header class="relative text-white py-4 md:py-8 lg:py-10 bg-cover bg-center bg-no-repeat" style="background-image: url('<?= base_url('assets/images/BackgroundTopBar.png') ?>');">
    <!-- Dark overlay for text readability -->
    <div class="absolute inset-0 bg-primary/60"></div>
    <div class="relative max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-center text-xl md:text-2xl lg:text-3xl font-bold tracking-tight">
            <?= isset($title) ? $title : 'title ga kebaca' ?>
        </h1>
        <?php if (!empty($subtitle)) : ?>
            <p class="text-center mt-1.5 text-xs md:text-sm text-white/70">
                <?= esc($subtitle) ?>
            </p>
        <?php endif ?>
    </div>
</header>