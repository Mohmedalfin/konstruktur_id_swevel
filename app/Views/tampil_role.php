<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Percobaan Role CI4</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-10">

    <div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow-lg">
        <h1 class="text-xl font-bold mb-4 text-slate-800 border-b pb-2">Daftar Role User</h1>
        
        <ul class="space-y-2">
            <?php foreach ($daftar_role as $r) : ?>
                <li class="flex items-center p-3 bg-slate-50 rounded-lg border border-slate-200 hover:bg-blue-50 transition">
                    <span class="bg-blue-500 text-white w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold mr-3">
                        <?= $r->id_role; ?>
                    </span>
                    <span class="font-medium text-slate-700 uppercase tracking-wide">
                        <?= $r->role; ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="mt-4 text-xs text-slate-400 text-center">
            Total Role: <?= count($daftar_role); ?>
        </p>
    </div>
</body>
</html>