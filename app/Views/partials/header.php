<!-- app/Views/partials/header.php -->
<header class="bg-bg-nav text-white shadow-sm">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid h-16 grid-cols-[auto_1fr_auto] items-center gap-6">

            <!-- Left: Logo -->
            <a href="<?= base_url('/') ?>" class="flex items-center gap-3">
                <!-- placeholder logo (ganti nanti) -->
                <div class="h-9 w-9 rounded-md bg-white/10"></div>
                <span class="text-lg font-semibold tracking-wide">Kontraktor.id</span>
            </a>

            <!-- Center: Nav -->
            <div class="flex">
                <a
                    href="<?= base_url('proyek') ?>"
                    class="inline-flex items-center justify-center rounded-md bg-white px-10 py-2 text-sm font-semibold text-text-primary shadow-md hover:bg-gray-100">
                    Proyek
                </a>
            </div>

            <!-- Right: User -->
            <div class="flex items-center gap-3 whitespace-nowrap">
                <i class="fa-solid fa-user text-xl"></i>
                <div class="leading-tight">
                    <div class="font-semibold">Alfin Maulana</div>
                    <div class="text-sm text-white/70">Admin Project</div>
                </div>
            </div>

        </div>
    </div>
</header>