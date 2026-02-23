<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/assets/css/output.css">
</head>

<body>
    <?= $this->include('partials/header') ?>
    <main>
        <div class="container">
            <?= $this->renderSection('content') ?>
        </div>
        <?= $this->include('partials/footer') ?>
    </main>
    <?= $this->renderSection('scripts') ?>
</body>

</html>