<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$items = function (string $key, string $icon, string $color) use ($lang, $h): string {
    $list = $lang->get($key);
    $out = '<ul class="mt-3">';
    foreach ((array)$list as $item) {
        $out .= '<li class="mb-2 is-flex"><i class="fas ' . $icon . ' ' . $color . ' mr-2 mt-1"></i><span>' . $h($item) . '</span></li>';
    }
    return $out . '</ul>';
};
$step = fn(int $n, string $icon, string $title, string $desc, string $extra = ''): string =>
    '<div class="mb-5"><h3 class="title is-5"><span class="tag is-primary is-medium is-rounded mr-2">' . $n . '</span><i class="fas ' . $icon . ' mr-2"></i>' . $h($title) . '</h3><p>' . $h($desc) . '</p>' . $extra . '</div>';
$contacts = [];
if ($config['show_contact_discord'] ?? true) {
    $contacts[] = ['fa-discord', 'fab', $lang->get('protest.discord_title'), $lang->get('protest.discord_desc'), $protestConfig['discord_link'] !== '#' ? [$protestConfig['discord_link'], $lang->get('protest.discord_button')] : null, null];
}
if ($config['show_contact_email'] ?? true) {
    $contacts[] = ['fa-envelope', 'fas', $lang->get('protest.email_title'), $lang->get('protest.email_desc'), null, $protestConfig['email_address']];
}
if ($config['show_contact_forum'] ?? true) {
    $contacts[] = ['fa-comments', 'fas', $lang->get('protest.forum_title'), $lang->get('protest.forum_desc'), $protestConfig['forum_link'] !== '#' ? [$protestConfig['forum_link'], $lang->get('protest.forum_button')] : null, null];
}
?>
<div class="columns is-centered">
<div class="column is-10-desktop">
    <header class="has-text-centered mb-6">
        <h1 class="title is-1"><i class="fas fa-gavel has-text-primary mr-2"></i><?= $h($lang->get('protest.title')) ?></h1>
        <p class="subtitle"><?= $h($lang->get('protest.description')) ?></p>
    </header>

    <div class="box">
        <h2 class="title is-4"><i class="fas fa-info-circle mr-2"></i><?= $h($lang->get('protest.how_to_title')) ?></h2>
        <p class="mb-5"><?= $h($lang->get('protest.how_to_subtitle')) ?></p>

        <?= $step(1, 'fa-clipboard-list', $lang->get('protest.step1_title'), $lang->get('protest.step1_desc'), $items('protest.step1_items', 'fa-check-circle', 'has-text-success')) ?>

        <?php
        ob_start(); ?>
        <div class="columns is-centered mt-3">
            <?php foreach ($contacts as [$icon, $fam, $title, $desc, $link, $mail]): ?>
                <div class="column is-4-desktop is-6-tablet">
                    <div class="card h-100 has-text-centered" style="height:100%">
                        <div class="card-content">
                            <p class="is-size-1 has-text-primary mb-2"><i class="<?= $fam ?> <?= $icon ?>"></i></p>
                            <h4 class="title is-5"><?= $h($title) ?></h4>
                            <p class="mb-3"><?= $h($desc) ?></p>
                            <?php if ($link): ?><a class="button is-primary" href="<?= $h($link[0]) ?>" target="_blank" rel="noopener"><?= $h($link[1]) ?></a><?php endif; ?>
                            <?php if ($mail): ?><code><?= $h($mail) ?></code><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php $contactHtml = ob_get_clean(); ?>
        <?= $step(2, 'fa-envelope', $lang->get('protest.step2_title'), $lang->get('protest.step2_desc'), $contactHtml) ?>

        <?= $step(3, 'fa-edit', $lang->get('protest.step3_title'), $lang->get('protest.step3_desc'), $items('protest.step3_items', 'fa-arrow-right', 'has-text-primary')) ?>
        <?= $step(4, 'fa-clock', $lang->get('protest.step4_title'), $lang->get('protest.step4_desc')) ?>
    </div>

    <div class="box">
        <h2 class="title is-4"><i class="fas fa-book mr-2"></i><?= $h($lang->get('protest.guidelines_title')) ?></h2>
        <?= $items('protest.guidelines_items', 'fa-check', 'has-text-info') ?>
    </div>

    <article class="message is-danger">
        <div class="message-header"><p><i class="fas fa-exclamation-triangle mr-2"></i><?= $h($lang->get('protest.warning_title')) ?></p></div>
        <div class="message-body"><?= $h($lang->get('protest.warning_desc')) ?></div>
    </article>
</div>
</div>
