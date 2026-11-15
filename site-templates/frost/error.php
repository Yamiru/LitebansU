<?php $h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); ?>
<div class="narrow center">
    <p class="badge-icon"><i class="fas fa-exclamation-triangle" style="margin:0"></i></p>
    <h1 style="border:0"><?= $h($title ?? 'Error') ?></h1>
    <p class="muted"><?= $h($message ?? 'An error occurred') ?></p>
    <a class="btn primary" href="<?= $h(url()) ?>"><i class="fas fa-home"></i><?= $h($lang->get('nav.home')) ?></a>
</div>
