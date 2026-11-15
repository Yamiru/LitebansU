<?php $h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); ?>
<div class="columns is-centered">
    <div class="column is-8">
        <div class="box has-text-centered py-6">
            <p class="is-size-1 has-text-danger"><i class="fas fa-exclamation-triangle"></i></p>
            <h1 class="title is-2"><?= $h($title ?? 'Error') ?></h1>
            <p class="subtitle"><?= $h($message ?? 'An error occurred') ?></p>
            <a class="button is-primary" href="<?= $h(url()) ?>"><i class="fas fa-home mr-2"></i><?= $h($lang->get('nav.home')) ?></a>
        </div>
    </div>
</div>
