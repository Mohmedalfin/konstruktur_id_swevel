<?php

$id     = $card['id']     ?? 0;
$title  = $card['title']  ?? '';
$lokasi = $card['lokasi'] ?? '';
$nilai  = $card['nilai']  ?? null;
$pctVal = $card['pct']    ?? null;
$tgl    = $card['tgl']    ?? '';
$href   = $card['href']   ?? '#';
$status = $card['status'] ?? 'draft';

$isSelesai = $status === 'done';

$pctCls = match (true) {
    $pctVal && str_starts_with($pctVal, '+') => 'text-emerald-600',
    $pctVal && str_starts_with($pctVal, '-') => 'text-red-500',
    default                                   => 'text-table-subtle',
};
?>

<div id="proyek-card-<?= $id ?>" class="group relative flex flex-col overflow-hidden rounded-2xl bg-white border border-table-border shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">

    <a href="<?= esc($href) ?>" class="absolute inset-0 z-10" aria-label="Buka proyek <?= esc($title) ?>"></a>

    <div class="relative h-24 sm:h-36 shrink-0 overflow-hidden">

        <?php
        $fotoPath = !empty($card['foto']) ? base_url($card['foto']) : base_url('assets/images/BackgroundLogin.png');
        ?>
        <img src="<?= $fotoPath ?>"
             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
             alt="Cover <?= esc($title) ?>">

        <div class="absolute inset-0 bg-linear-to-t from-primary/70 via-primary/10 to-transparent"></div>

        <div class="absolute top-2 right-2 z-20">

            <?php if ($isSelesai): ?>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/90 backdrop-blur-sm text-white text-[10px] sm:text-xs font-semibold shadow">
                    <i class="fa-solid fa-circle-check"></i> Selesai
                </span>
            <?php else: ?>
            <div class="hs-dropdown relative inline-flex">

                <button type="button"
                    class="hs-dropdown-toggle inline-flex items-center justify-center w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-white/20 hover:bg-white/40 text-white backdrop-blur-sm transition-colors focus:outline-none">
                    <i class="fa-solid fa-ellipsis text-xs"></i>
                </button>

                <div class="hs-dropdown-menu hidden z-50 mt-2 w-32 sm:w-44 overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/10 end-0" role="menu">
                    <a class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-xs text-slate-700 hover:bg-slate-50" href="#">
                        <i class="fa-solid fa-user-plus w-3.5 sm:w-4 text-primary shrink-0"></i> Undang Tim
                    </a>
                    <a class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-xs text-slate-700 hover:bg-slate-50" href="<?= isset($card['id']) ? base_url('proyek/edit/' . $card['id']) : '#' ?>">
                        <i class="fa-regular fa-pen-to-square w-3.5 sm:w-4 text-amber-500 shrink-0"></i> Edit
                    </a>
                    <a class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-xs text-slate-700 hover:bg-slate-50" href="#">
                        <i class="fa-regular fa-copy w-3.5 sm:w-4 text-primary shrink-0"></i> Duplikat
                    </a>
                    <?php if ($isSelesai): ?>
                        <span class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-xs font-semibold text-emerald-600">
                            <i class="fa-solid fa-circle-check w-3.5 sm:w-4 shrink-0"></i> Sudah Selesai
                        </span>
                    <?php else: ?>
                        <button type="button"
                            class="btn-selesai-proyek w-full flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-xs text-slate-700 hover:bg-emerald-50 text-left"
                            data-id="<?= $id ?>" data-nama="<?= esc($title) ?>">
                            <i class="fa-solid fa-circle-check w-3.5 sm:w-4 text-emerald-500 shrink-0"></i> Selesaikan
                        </button>
                    <?php endif; ?>
                    <div class="border-t border-table-border my-1"></div>
                    <button type="button"
                        class="btn-hapus-proyek w-full flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-xs text-red-500 hover:bg-red-50 text-left"
                        data-id="<?= $id ?>" data-nama="<?= esc($title) ?>">
                        <i class="fa-regular fa-trash-can w-3.5 sm:w-4 shrink-0"></i> Hapus
                    </button>
                </div>

            </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="flex flex-col flex-1 p-2.5 sm:p-4 gap-2 sm:gap-3">

        <h3 class="text-[11px] sm:text-sm font-bold leading-snug text-table-strong line-clamp-2">
            <?= esc($title) ?>
        </h3>

        <div class="flex flex-col gap-1 sm:gap-1.5 text-[11px] sm:text-[12px] text-table-body">

            <div class="hidden sm:flex items-center gap-2">
                <i class="fa-solid fa-location-dot w-3.5 text-primary shrink-0"></i>
                <span class="truncate"><?= esc($lokasi) ?></span>
            </div>

            <div class="flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave w-3.5 text-primary shrink-0"></i>
                <span class="font-semibold tabular-nums">
                    <?= $nilai ?? '<span class="text-table-muted italic">Belum ada nilai</span>' ?>
                </span>
            </div>

            <?php if ($pctVal): ?>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-chart-simple w-3.5 text-primary shrink-0"></i>
                <span class="font-semibold <?= $pctCls ?>"><?= esc($pctVal) ?></span>
            </div>
            <?php endif; ?>

        </div>

    </div>

    <div class="flex items-center justify-between border-t border-table-border px-2.5 sm:px-4 py-2 sm:py-2.5 bg-gray-50/60">
        <span class="flex items-center gap-1 text-[10px] sm:text-[11px] text-table-subtle">
            <i class="fa-regular fa-calendar w-3 text-primary"></i>
            <span class="hidden xs:inline sm:inline"><?= esc($tgl) ?></span>
            <span class="inline sm:hidden"><?= date('d/m', strtotime($tgl)) ?></span>
        </span>
        <?php if ($isSelesai): ?>
            <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] font-semibold text-emerald-600">
                <i class="fa-solid fa-circle-check"></i>
                <span class="hidden sm:inline">Selesai</span>
            </span>
        <?php else: ?>
            <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] font-semibold text-primary group-hover:underline">
                <span class="hidden sm:inline">Lihat Detail</span>
                <svg class="w-3 h-3 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        <?php endif; ?>
    </div>
</div>