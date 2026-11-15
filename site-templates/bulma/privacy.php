<?php $h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); ?>
<div class="box content">
    <h1><?= $h($title) ?></h1>
    <?= $privacyHtml ?>
</div>
