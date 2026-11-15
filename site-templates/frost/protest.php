<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$items = function (string $key, string $icon) use ($lang, $h): string {
    $out = '<ul style="list-style:none;padding:0">';
    foreach ((array)$lang->get($key) as $item) {
        $out .= '<li><i class="fas ' . $icon . '" style="color:var(--accent)"></i>' . $h($item) . '</li>';
    }
    return $out . '</ul>';
};
$contacts = [];
if ($config['show_contact_discord'] ?? true) {
    $contacts[] = ['fab fa-discord', $lang->get('protest.discord_title'), $lang->get('protest.discord_desc'), $protestConfig['discord_link'] !== '#' ? [$protestConfig['discord_link'], $lang->get('protest.discord_button')] : null, null];
}
if ($config['show_contact_email'] ?? true) {
    $contacts[] = ['fas fa-envelope', $lang->get('protest.email_title'), $lang->get('protest.email_desc'), null, $protestConfig['email_address']];
}
if ($config['show_contact_forum'] ?? true) {
    $contacts[] = ['fas fa-comments', $lang->get('protest.forum_title'), $lang->get('protest.forum_desc'), $protestConfig['forum_link'] !== '#' ? [$protestConfig['forum_link'], $lang->get('protest.forum_button')] : null, null];
}
?>
<h1><i class="fas fa-gavel"></i><?= $h($lang->get('protest.title')) ?></h1>
<p class="muted"><?= $h($lang->get('protest.description')) ?></p>

<h2><?= $h($lang->get('protest.how_to_title')) ?></h2>
<p><?= $h($lang->get('protest.how_to_subtitle')) ?></p>

<h3>1. <?= $h($lang->get('protest.step1_title')) ?></h3>
<p><?= $h($lang->get('protest.step1_desc')) ?></p>
<?= $items('protest.step1_items', 'fa-check') ?>

<h3>2. <?= $h($lang->get('protest.step2_title')) ?></h3>
<p><?= $h($lang->get('protest.step2_desc')) ?></p>
<div class="cols">
    <?php foreach ($contacts as [$icon, $title, $desc, $link, $mail]): ?>
        <section class="panel">
            <div class="body">
                <p style="font-size:1.6rem;color:var(--accent);margin-bottom:0.4rem"><i class="<?= $icon ?>"></i></p>
                <h3 style="margin:0 0 0.4rem"><?= $h($title) ?></h3>
                <p class="muted"><?= $h($desc) ?></p>
                <?php if ($link): ?><a class="btn primary" href="<?= $h($link[0]) ?>" target="_blank" rel="noopener"><?= $h($link[1]) ?></a><?php endif; ?>
                <?php if ($mail): ?><code><?= $h($mail) ?></code><?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>

<h3>3. <?= $h($lang->get('protest.step3_title')) ?></h3>
<p><?= $h($lang->get('protest.step3_desc')) ?></p>
<?= $items('protest.step3_items', 'fa-chevron-right') ?>

<h3>4. <?= $h($lang->get('protest.step4_title')) ?></h3>
<p><?= $h($lang->get('protest.step4_desc')) ?></p>

<h2><?= $h($lang->get('protest.guidelines_title')) ?></h2>
<?= $items('protest.guidelines_items', 'fa-check') ?>

<div class="notice error">
    <strong><i class="fas fa-exclamation-triangle"></i><?= $h($lang->get('protest.warning_title')) ?></strong><br>
    <?= $h($lang->get('protest.warning_desc')) ?>
</div>
