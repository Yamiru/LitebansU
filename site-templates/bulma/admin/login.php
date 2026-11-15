<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$oauth = ($googleAuthEnabled ?? false) || ($discordAuthEnabled ?? false);
$password = $showPasswordLogin ?? true;
$csrf = \SecurityManager::generateCsrfToken();
?>
<div class="columns is-centered">
<div class="column is-5-tablet is-4-desktop">
    <div class="box login-box">
        <div class="has-text-centered mb-5">
            <p class="login-icon"><i class="fas fa-shield-alt"></i></p>
            <h1 class="title is-4 mb-2"><?= $h($lang->get('admin.login')) ?></h1>
            <p class="has-text-grey"><?= $oauth ? 'Sign in to access the admin panel' : 'Enter your admin password to continue' ?></p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="notification is-danger"><button class="delete" type="button" aria-label="Close"></button><?= $h($error) ?></div>
        <?php endif; ?>

        <?php if ($oauth): ?>
            <label class="checkbox is-block has-text-centered mb-4">
                <input type="checkbox" id="oauth-remember-me" checked> <i class="fas fa-clock"></i> Stay signed in for 30 days
            </label>
            <?php foreach ([['google', $googleAuthEnabled ?? false, 'fa-google', 'Google'], ['discord', $discordAuthEnabled ?? false, 'fa-discord', 'Discord']] as [$provider, $on, $icon, $name]): ?>
                <?php if ($on): ?>
                    <form action="<?= $h(url('admin/oauth-prepare')) ?>" method="POST" class="mb-3">
                        <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
                        <input type="hidden" name="provider" value="<?= $provider ?>">
                        <input type="hidden" name="remember_me" value="1" class="oauth-remember-input">
                        <button type="submit" class="button is-fullwidth is-medium <?= $provider === 'discord' ? 'is-link' : 'is-light' ?>">
                            <span class="icon"><i class="fab <?= $icon ?>"></i></span><span>Sign in with <?= $name ?></span>
                        </button>
                    </form>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($oauth && $password): ?>
            <button type="button" class="button is-ghost is-fullwidth mb-3" id="password-toggle-form">or use password</button>
        <?php endif; ?>

        <?php if ($password): ?>
            <form action="<?= $h(url('admin/login')) ?>" method="POST" id="password-form"<?= $oauth ? ' hidden' : '' ?>>
                <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
                <div class="field">
                    <label class="label" for="password"><i class="fas fa-lock mr-1"></i><?= $h($lang->get('admin.password')) ?></label>
                    <div class="field has-addons mb-0">
                        <div class="control is-expanded"><input class="input is-medium" type="password" id="password" name="password" placeholder="Enter admin password" autocomplete="current-password" required></div>
                        <div class="control"><button class="button is-medium" type="button" id="toggle-password" aria-label="Show password"><i class="fas fa-eye"></i></button></div>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="captcha"><i class="fas fa-shield-alt mr-1"></i>Security question: <strong><?= $h($captchaQuestion ?? '') ?> = ?</strong></label>
                    <div class="control"><input class="input is-medium" type="text" id="captcha" name="captcha" inputmode="numeric" pattern="[0-9]*" autocomplete="off" maxlength="3" required></div>
                </div>
                <label class="checkbox mb-4"><input type="checkbox" name="remember_me" value="1" checked> <i class="fas fa-clock"></i> Stay signed in for 30 days</label>
                <button type="submit" class="button is-primary is-medium is-fullwidth"><span class="icon"><i class="fas fa-sign-in-alt"></i></span><span>Login with Password</span></button>
            </form>
        <?php endif; ?>
    </div>
</div>
</div>

<script>
(function () {
    var q = function (s) { return document.querySelector(s); };
    var del = q('.notification .delete');
    if (del) del.addEventListener('click', function () { del.parentNode.remove(); });
    var form = q('#password-form'), btn = q('#password-toggle-form');
    if (btn) btn.addEventListener('click', function () { form.hidden = !form.hidden; if (!form.hidden) q('#password').focus(); });
    var eye = q('#toggle-password');
    if (eye) eye.addEventListener('click', function () {
        var input = q('#password'), show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        eye.firstElementChild.className = 'fas ' + (show ? 'fa-eye-slash' : 'fa-eye');
    });
    var remember = q('#oauth-remember-me');
    if (remember) remember.addEventListener('change', function () {
        document.querySelectorAll('.oauth-remember-input').forEach(function (i) { i.value = remember.checked ? '1' : '0'; });
    });
})();
</script>
