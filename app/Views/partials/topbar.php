<header class="bg-primary text-white py-4">
    <h1 class="text-center text-4xl font-bold">
        <?= isset($title) ? $title : 'title ga kebaca' ?>
    </h1>
    <?php if (!empty($subtitle)) : ?>
        <p class="text-center mt-2 opacity-80">
            <?= esc($subtitle) ?>
        </p>
    <?php endif ?>
</header>