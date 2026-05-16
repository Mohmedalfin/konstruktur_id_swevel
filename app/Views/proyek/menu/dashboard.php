<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<style>
    /* ── Design tokens ───────────────────────────── */
    :root {
        --db-card:      #ffffff;
        --db-primary:   #1a2e4a;
        --db-accent:    #f59e0b;
        --db-blue:      #3b82f6;
        --db-red:       #ef4444;
        --db-green:     #10b981;
        --db-text:      #1e293b;
        --db-muted:     #64748b;
        --db-border:    #e2e8f0;
        --db-radius:    12px;
        --db-shadow:    0 2px 12px rgba(0,0,0,.07);
    }

    /* ── Wrapper ─────────────────────────────────── */
    .db-wrap { max-width: 1200px; margin: 0 auto; padding: 24px 0px 48px; }

    /* ── Stat cards ──────────────────────────────── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media(max-width:900px){ .stat-grid{ grid-template-columns: repeat(2,1fr); } }
    @media(max-width:500px){ .stat-grid{ grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--db-card);
        border-radius: var(--db-radius);
        box-shadow: var(--db-shadow);
        padding: 20px 20px 16px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        position: relative;
        min-width: 0;
    }
    .stat-card .stat-icon {
        position: absolute;
        top: 18px; right: 18px;
        font-size: 1.3rem;
        color: var(--db-muted);
        opacity: .5;
    }
    .stat-label {
        font-size: .75rem;
        color: var(--db-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .stat-value {
        font-size: 1.45rem;
        font-weight: 800;
        color: var(--db-text);
        line-height: 1.2;
    }
    .stat-sub {
        font-size: .72rem;
        color: var(--db-muted);
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
    }
    .stat-sub.up   { color: #10b981; }
    .stat-sub.down { color: #ef4444; }

    /* ── Charts row ──────────────────────────────── */
    .chart-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }
    @media(max-width:1024px){ .chart-row{ grid-template-columns:1fr; } }

    .db-card {
        background: var(--db-card);
        border-radius: var(--db-radius);
        box-shadow: var(--db-shadow);
        padding: 20px;
        min-width: 0;
    }
    .db-card-title {
        font-size: .9rem;
        font-weight: 700;
        color: var(--db-text);
        margin-bottom: 16px;
    }

    /* ── Donut area ──────────────────────────────── */
    .health-inner {
        display: flex;
        align-items: stretch;
        gap: 20px;
    }
    .health-left {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        border-right: 1px solid var(--db-border);
        padding-right: 20px;
    }
    .donut-wrap { position: relative; width: 140px; flex-shrink: 0; }
    .donut-center {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%,-50%);
        text-align: center;
        pointer-events: none;
    }
    .donut-center .dc-num  { font-size: 1.4rem; font-weight: 800; line-height: 1; }
    .donut-center .dc-lbl  { font-size: .6rem; color: var(--db-muted); font-weight: 600; }

    .health-right {
        flex: 1.2;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    @media(max-width: 640px) {
        .health-inner { flex-direction: column; gap: 16px; }
        .health-left { 
            border-right: none; 
            border-bottom: 1px solid var(--db-border); 
            padding-right: 0; 
            padding-bottom: 16px; 
            justify-content: center;
        }
    }
    .legend-row { display: flex; align-items: center; gap: 7px; margin-bottom: 6px; font-size: .77rem; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .legend-num { margin-left: auto; font-weight: 700; }

    /* ── Status bars ─────────────────────────────── */
    .status-section { margin-top: 12px; }
    .status-lbl { font-size: .72rem; font-weight: 600; color: var(--db-muted); margin-bottom: 5px; }
    .bar-track {
        display: flex;
        height: 20px;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 10px;
    }
    .bar-seg {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .65rem;
        font-weight: 700;
        color: #fff;
        transition: width .4s;
    }

    /* ── Table ───────────────────────────────────── */
    .db-table-wrap {
        background: var(--db-card);
        border-radius: var(--db-radius);
        box-shadow: var(--db-shadow);
        overflow: hidden;
    }
    .db-table-head {
        padding: 14px 20px;
        font-size: .9rem;
        font-weight: 700;
        border-bottom: 1px solid var(--db-border);
    }
    table.dash-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
    .dash-table thead th {
        background: #f8fafc;
        padding: 10px 14px;
        text-align: left;
        font-weight: 700;
        color: var(--db-muted);
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        border-bottom: 1px solid var(--db-border);
    }
    .dash-table tbody tr { border-bottom: 1px solid var(--db-border); transition: background .15s; }
    .dash-table tbody tr:last-child { border-bottom: none; }
    .dash-table tbody tr:hover { background: #f8fafc; }
    .dash-table td { padding: 10px 14px; vertical-align: middle; }

    /* ── Status badges ───────────────────────────── */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: .7rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge-ontime    { background: #dcfce7; color: #166534; }
    .badge-slight    { background: #fef9c3; color: #854d0e; }
    .badge-early     { background: #dbeafe; color: #1e40af; }
    .badge-delayed   { background: #fee2e2; color: #991b1b; }
    .badge-onbudget  { background: #dcfce7; color: #166534; }
    .badge-under     { background: #dbeafe; color: #1e40af; }
    .badge-slightover{ background: #fef9c3; color: #854d0e; }
    .badge-overrun   { background: #fee2e2; color: #991b1b; }
    .badge-healthy   { background: #dcfce7; color: #166534; }
    .badge-warning   { background: #fef9c3; color: #854d0e; }
    .badge-critical  { background: #fee2e2; color: #991b1b; }

    /* ── Empty state ─────────────────────────────── */
    .empty-dash {
        text-align: center;
        padding: 48px 20px;
        color: var(--db-muted);
    }
    .empty-dash i { font-size: 3rem; margin-bottom: 12px; opacity: .3; }
</style>

<!-- ── Main wrapper ─────────────────────────────────────────── -->
<div class="db-wrap">

    <?php
    // ── Prepare values ───────────────────────────────────────
    $totalAktif   = (int)   ($totalProyekAktif  ?? 0);
    $totalProyek  = (int)   ($totalProyek       ?? 0);
    $nilaiKontrak = (float) ($totalNilaiKontrak ?? 0);
    $serapan      = (float) ($totalRap          ?? 0);
    $pctSerapan   = (float) ($pctSerapan        ?? 0);
    $rataProgres  = (float) ($rataProgres       ?? 0);

    $hCritical = (int) ($healthCounts['critical'] ?? 0);
    $hWarning  = (int) ($healthCounts['warning']  ?? 0);
    $hHealthy  = (int) ($healthCounts['healthy']  ?? 0);
    $hTotal    = $hCritical + $hWarning + $hHealthy;

    $cashFlowData = $cashFlow ?? [];
    $daftar       = $daftarProyek ?? [];

    if (!function_exists('fmtRp')) {
        function fmtRp(float $n): string {
            return 'Rp ' . number_format($n, 0, ',', '.');
        }
    }
    ?>

    <!-- ── Stat cards ──────────────────────────────────────── -->
    <div class="stat-grid">

        <!-- Total Proyek Aktif -->
        <div class="stat-card">
            <i class="fa-solid fa-folder-open stat-icon"></i>
            <span class="stat-label">Total Proyek Aktif</span>
            <span class="stat-value"><?= $totalAktif ?> Proyek</span>
            <span class="stat-sub">
                <i class="fa-solid fa-layer-group"></i>
                <?= $totalProyek ?> total proyek terdaftar
            </span>
        </div>

        <!-- Total Serapan Anggaran -->
        <div class="stat-card">
            <i class="fa-solid fa-chart-line stat-icon"></i>
            <span class="stat-label">Total Serapan Anggaran</span>
            <span class="stat-value"><?= fmtRp($serapan) ?></span>
            <span class="stat-sub">
                <?= $pctSerapan ?>% dari Total Kontrak
            </span>
        </div>

        <!-- Rata-rata Progres -->
        <div class="stat-card">
            <i class="fa-solid fa-circle-half-stroke stat-icon"></i>
            <span class="stat-label">Rata-rata Progres</span>
            <span class="stat-value"><?= $rataProgres ?>%</span>
            <span class="stat-sub">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Data realisasi belum tersedia
            </span>
        </div>

        <!-- Total Nilai Kontrak -->
        <div class="stat-card">
            <i class="fa-solid fa-file-contract stat-icon"></i>
            <span class="stat-label">Total Nilai Kontrak</span>
            <span class="stat-value" style="font-size:1.2rem"><?= fmtRp($nilaiKontrak) ?></span>
            <span class="stat-sub">
                <i class="fa-solid fa-handshake"></i>
                Akumulasi harga deal proyek
            </span>
        </div>

    </div><!-- /.stat-grid -->

    <!-- ── Charts row ──────────────────────────────────────── -->
    <div class="chart-row">

        <!-- Project Health Overview -->
        <div class="db-card">
            <div class="db-card-title">Project Health Overview</div>

            <?php if ($hTotal === 0): ?>
                <div class="empty-dash">
                    <i class="fa-solid fa-chart-pie"></i>
                    <p>Belum ada data proyek.</p>
                </div>
            <?php else: ?>
            <div class="health-inner">

                <!-- Donut & Legend Wrap -->
                <div class="health-left">
                    <div class="donut-wrap">
                        <canvas id="healthDonut" width="140" height="140"></canvas>
                        <div class="donut-center">
                            <div class="dc-num"><?= $hTotal ?></div>
                            <div class="dc-lbl">Total<br>Proyek</div>
                        </div>
                    </div>
                    <div class="legend-wrap" style="flex: 1;">
                        <div class="legend-row">
                            <span class="legend-dot" style="background:#ef4444"></span>
                            <span>Critical</span>
                            <span class="legend-num" style="color:#ef4444"><?= $hCritical ?></span>
                        </div>
                        <div class="legend-row">
                            <span class="legend-dot" style="background:#f59e0b"></span>
                            <span>Warning</span>
                            <span class="legend-num" style="color:#f59e0b"><?= $hWarning ?></span>
                        </div>
                        <div class="legend-row">
                            <span class="legend-dot" style="background:#10b981"></span>
                            <span>Healthy</span>
                            <span class="legend-num" style="color:#10b981"><?= $hHealthy ?></span>
                        </div>
                    </div>
                </div>

                <!-- Status bars -->
                <div class="health-right">
                    <div class="status-section" style="margin-top: 0;">
                        <div class="status-lbl">Schedule Status</div>
                        <?php
                        $onTime  = max(0, $hHealthy);
                        $delayed = max(0, $hCritical + $hWarning);
                        $barTotal = max(1, $onTime + $delayed);
                        $pOn  = round($onTime / $barTotal * 100);
                        $pDel = 100 - $pOn;
                        ?>
                        <div class="bar-track">
                            <div class="bar-seg" style="width:<?= $pOn ?>%;background:#10b981">
                                <?= $onTime > 0 ? $onTime : '' ?>
                            </div>
                            <div class="bar-seg" style="width:<?= $pDel ?>%;background:#ef4444">
                                <?= $delayed > 0 ? $delayed : '' ?>
                            </div>
                        </div>
                        <div style="display:flex;gap:12px;font-size:.65rem;color:var(--db-muted);margin-bottom:10px">
                            <span><span style="color:#10b981">■</span> On Time</span>
                            <span><span style="color:#ef4444">■</span> Delayed</span>
                        </div>

                        <div class="status-lbl">Cost Status</div>
                        <?php
                        $onBudget = max(0, $hHealthy);
                        $overrun  = max(0, $hCritical);
                        $bTotal   = max(1, $onBudget + $overrun);
                        $pBud  = round($onBudget / $bTotal * 100);
                        $pOver = 100 - $pBud;
                        ?>
                        <div class="bar-track">
                            <div class="bar-seg" style="width:<?= $pBud ?>%;background:#3b82f6">
                                <?= $onBudget > 0 ? $onBudget : '' ?>
                            </div>
                            <div class="bar-seg" style="width:<?= $pOver ?>%;background:#f59e0b">
                                <?= $overrun > 0 ? $overrun : '' ?>
                            </div>
                        </div>
                        <div style="display:flex;gap:12px;font-size:.65rem;color:var(--db-muted)">
                            <span><span style="color:#3b82f6">■</span> On Budget</span>
                            <span><span style="color:#f59e0b">■</span> Overrun</span>
                        </div>
                    </div>
                </div>

            </div>
            <?php endif; ?>
        </div>

        <!-- Cash Flow Portfolio -->
        <div class="db-card">
            <div class="db-card-title">Cash Flow Portfolio</div>
            <?php if (empty($cashFlowData)): ?>
                <div class="empty-dash">
                    <i class="fa-solid fa-chart-bar"></i>
                    <p>Belum ada data cash flow.</p>
                </div>
            <?php else: ?>
            <div style="position: relative; width: 100%;">
                <canvas id="cashFlowChart" style="max-height:220px"></canvas>
            </div>
            <?php endif; ?>
        </div>

    </div><!-- /.chart-row -->

    <!-- ── Daftar Proyek Table ──────────────────────────────── -->
    <div class="db-table-wrap">
        <div class="db-table-head">Daftar Proyek</div>
        <?php if (empty($daftar)): ?>
            <div class="empty-dash">
                <i class="fa-solid fa-folder-open"></i>
                <p>Belum ada proyek yang terdaftar. <a href="<?= base_url('proyek/create') ?>" style="color:var(--db-blue);font-weight:700">Buat proyek baru →</a></p>
            </div>
        <?php else: ?>
        <div style="overflow-x:auto; padding: 0 20px 20px 20px;">
            <table class="dash-table" id="dash-proyek-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Proyek</th>
                        <th>Lokasi</th>
                        <th>Status Schedule</th>
                        <th>Status Cost</th>
                        <th>Overall Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftar as $row): ?>
                    <tr>
                        <td style="color:var(--db-muted);font-weight:600"><?= $row['no'] ?></td>
                        <td>
                            <a href="<?= base_url('proyek/' . $row['slug']) ?>"
                               style="font-weight:600;color:var(--db-text);text-decoration:none"
                               class="hover:underline">
                                <?= esc($row['nama']) ?>
                            </a>
                        </td>
                        <td style="color:var(--db-muted)"><?= esc($row['lokasi']) ?></td>
                        <td><span class="badge <?= $row['jadwalStatus']['class'] ?>"><?= $row['jadwalStatus']['label'] ?></span></td>
                        <td><span class="badge <?= $row['costStatus']['class'] ?>"><?= $row['costStatus']['label'] ?></span></td>
                        <td><span class="badge <?= $row['overall']['class'] ?>"><?= $row['overall']['label'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div><!-- /.db-wrap -->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
/* ── Donut Chart ───────────────────────────────────── */
(function () {
    const ctx = document.getElementById('healthDonut');
    if (!ctx) return;

    const critical = <?= $hCritical ?>;
    const warning  = <?= $hWarning  ?>;
    const healthy  = <?= $hHealthy  ?>;
    const total    = critical + warning + healthy;

    if (total === 0) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Critical', 'Warning', 'Healthy'],
            datasets: [{
                data: [critical, warning, healthy],
                backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            cutout: '68%',
            plugins: { legend: { display: false }, tooltip: { callbacks: {
                label: ctx => ` ${ctx.label}: ${ctx.raw} proyek`
            }}},
            animation: { animateRotate: true, duration: 800 }
        }
    });
})();

/* ── Cash Flow Bar Chart ───────────────────────────── */
(function () {
    const ctx = document.getElementById('cashFlowChart');
    if (!ctx) return;

    const labels  = <?= json_encode(array_column($cashFlowData, 'label'))  ?>;
    const planned = <?= json_encode(array_column($cashFlowData, 'planned')) ?>;
    const actual  = <?= json_encode(array_column($cashFlowData, 'actual'))  ?>;

    const hasData = planned.some(v => v > 0) || actual.some(v => v > 0);
    if (!hasData) {
        ctx.closest('.db-card').innerHTML += '<p style="text-align:center;color:#94a3b8;font-size:.8rem;margin-top:12px">Belum ada data RAP bulan ini.</p>';
        return;
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Planned Cost',
                    data: planned,
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                    borderSkipped: false,
                },
                {
                    label: 'Actual Cost',
                    data: actual,
                    backgroundColor: '#ef4444',
                    borderRadius: 4,
                    borderSkipped: false,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, boxWidth: 12, padding: 16 }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: Rp ${Number(ctx.raw).toLocaleString('id-ID')}`
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        font: { size: 10 },
                        callback: v => {
                            if (v >= 1_000_000_000) return 'Rp ' + (v/1_000_000_000).toFixed(1) + 'M';
                            if (v >= 1_000_000)     return 'Rp ' + (v/1_000_000).toFixed(1) + 'jt';
                            if (v >= 1_000)         return 'Rp ' + (v/1_000).toFixed(0) + 'rb';
                            return 'Rp ' + v;
                        }
                    }
                }
            },
            animation: { duration: 800 }
        }
    });
})();
</script>
<?= $this->endSection() ?>