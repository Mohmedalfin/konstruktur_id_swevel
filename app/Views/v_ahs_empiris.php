<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .table-header { background-color: #198754; color: white; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h4 class="mb-3">Menampilkan 20 data AHS Empiris (CI4)</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr class="table-header text-center">
                    <th width="50">No.</th>
                    <th>Nama Pekerjaan</th>
                    <th>Satuan</th>
                    <th>Keterangan (Proyek)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($ahs_data)): ?>
                    <?php $no = 1; foreach($ahs_data as $row): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= esc($row->uraian_pekerjaan); ?></td>
                        <td class="text-center"><?= esc($row->satuan_pekerjaan); ?></td>
                        <td><?= esc($row->keterangan); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Data tidak ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>