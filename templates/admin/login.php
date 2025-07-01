<div class="admin-login">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="login-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <h3><?= htmlspecialchars($lang->get('admin.login'), ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="text-muted">Enter your admin password to continue</p>
                        </div>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= htmlspecialchars(url('admin/login'), ENT_QUOTES, 'UTF-8') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(SecurityManager::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-key"></i>
                                    <?= htmlspecialchars($lang->get('admin.password'), ENT_QUOTES, 'UTF-8') ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input 
                                        type="password" 
                                        class="form-control form-control-lg" 
                                        id="password" 
                                        name="password" 
                                        placeholder="Enter admin password"
                                        required 
                                        autofocus
                                    >
                                    <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-sign-in-alt"></i>
                                Login
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>" class="text-muted">
                                <i class="fas fa-arrow-left"></i>
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-login {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-xl);
    border-radius: var(--radius-lg);
}

.login-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 2rem;
    color: white;
    box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.3);
}

.form-control-lg {
    padding: 0.75rem 1rem;
    font-size: 1rem;
}

#toggle-password {
    border-left: 0;
}

#toggle-password:hover {
    background: var(--hover-bg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
});
</script>
