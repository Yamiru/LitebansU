<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$oauth = ($googleAuthEnabled ?? false) || ($discordAuthEnabled ?? false);
$password = $showPasswordLogin ?? true;
$csrf = \SecurityManager::generateCsrfToken();
?>
<div class="narrow">
    <div class="center" style="margin-bottom:1.5rem">
        <p class="badge-icon"><i class="fas fa-shield-alt" style="margin:0"></i></p>
        <h1 style="border:0;padding:0"><?= $h($lang->get('admin.login')) ?></h1>
        <p class="muted"><?= $oauth ? 'Sign in to access the admin panel' : 'Enter your admin password to continue' ?></p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="notice error"><i class="fas fa-exclamation-circle"></i><?= $h($error) ?></div>
    <?php endif; ?>

    <?php if ($oauth): ?>
        <label class="check"><input type="checkbox" id="oauth-remember-me" checked> Stay signed in for 30 days</label>
        <?php foreach ([['google', $googleAuthEnabled ?? false, 'fa-google', 'Google'], ['discord', $discordAuthEnabled ?? false, 'fa-discord', 'Discord']] as [$provider, $on, $icon, $name]): ?>
            <?php if ($on): ?>
                <form action="<?= $h(url('admin/oauth-prepare')) ?>" method="POST" style="margin-bottom:0.75rem">
                    <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
                    <input type="hidden" name="provider" value="<?= $provider ?>">
                    <input type="hidden" name="remember_me" value="1" class="oauth-remember-input">
                    <button type="submit" class="btn block"><i class="fab <?= $icon ?>"></i>Sign in with <?= $name ?></button>
                </form>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($oauth && $password): ?>
        <p class="center"><button type="button" class="btn" id="password-toggle-form">or use password</button></p>
    <?php endif; ?>

    <?php if ($password): ?>
        <form action="<?= $h(url('admin/login')) ?>" method="POST" id="password-form"<?= $oauth ? ' hidden' : '' ?>>
            <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">
            <div class="field">
                <label for="password"><i class="fas fa-lock"></i><?= $h($lang->get('admin.password')) ?></label>
                <div class="inline">
                    <input type="password" id="password" name="password" placeholder="Enter admin password" autocomplete="current-password" required>
                    <button class="btn" type="button" id="toggle-password" aria-label="Show password"><i class="fas fa-eye" style="margin:0"></i></button>
                </div>
            </div>
            <div class="field">
                <label for="captcha"><i class="fas fa-shield-alt"></i>Security question: <strong><?= $h($captchaQuestion ?? '') ?> = ?</strong></label>
                <input type="text" id="captcha" name="captcha" inputmode="numeric" pattern="[0-9]*" autocomplete="off" maxlength="3" required>
            </div>
            <label class="check"><input type="checkbox" name="remember_me" value="1" checked> Stay signed in for 30 days</label>
            <button type="submit" class="btn primary block" style="margin-top:1rem"><i class="fas fa-sign-in-alt"></i>Login with Password</button>
        </form>
    <?php endif; ?>
</div>

<script>
(function () {
    var q = function (s) { return document.querySelector(s); };
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
