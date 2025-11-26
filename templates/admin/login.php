<div class="admin-login">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="login-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3><?= htmlspecialchars($lang->get('admin.login'), ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="text-muted">
                                <?= ($googleAuthEnabled ?? false) || ($discordAuthEnabled ?? false) ? 'Sign in to access the admin panel' : 'Enter your admin password to continue' ?>
                            </p>
                        </div>
                        
                        <?php if (isset($error) && $error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($googleAuthEnabled ?? false): ?>
                            <!-- Google Sign In Button -->
                            <a href="<?= htmlspecialchars($googleAuthUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-google btn-lg w-100 mb-3">
                                <svg class="google-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="24px" height="24px">
                                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                                    <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                                    <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                                    <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                                </svg>
                                <span>Sign in with Google</span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($discordAuthEnabled ?? false): ?>
                            <!-- Discord Sign In Button -->
                            <a href="<?= htmlspecialchars($discordAuthUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-discord btn-lg w-100 mb-3">
                                <svg class="discord-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px">
                                    <path fill="#fff" d="M19.54 0c1.356 0 2.46 1.104 2.46 2.472v21.528l-2.58-2.28-1.452-1.344-1.536-1.428.636 2.22h-13.608c-1.356 0-2.46-1.104-2.46-2.472v-16.224c0-1.368 1.104-2.472 2.46-2.472h16.08zm-4.632 15.672c2.652-.084 3.672-1.824 3.672-1.824 0-3.864-1.728-6.996-1.728-6.996-1.728-1.296-3.372-1.26-3.372-1.26l-.168.192c2.04.624 2.988 1.524 2.988 1.524-1.248-.684-2.472-1.02-3.612-1.152-.864-.096-1.692-.072-2.424.024l-.204.024c-.42.036-1.44.192-2.724.756-.444.204-.708.348-.708.348s.996-.948 3.156-1.572l-.12-.144s-1.644-.036-3.372 1.26c0 0-1.728 3.132-1.728 6.996 0 0 1.008 1.74 3.66 1.824 0 0 .444-.54.804-.996-1.524-.456-2.1-1.416-2.1-1.416l.336.204.048.036.047.027.014.006.047.027c.3.168.6.3.876.408.492.192 1.08.384 1.764.516.9.168 1.956.228 3.108.012.564-.096 1.14-.264 1.74-.516.42-.156.888-.384 1.38-.708 0 0-.6.984-2.172 1.428.36.456.792.972.792.972zm-5.58-5.604c-.684 0-1.224.6-1.224 1.332 0 .732.552 1.332 1.224 1.332.684 0 1.224-.6 1.224-1.332.012-.732-.54-1.332-1.224-1.332zm4.38 0c-.684 0-1.224.6-1.224 1.332 0 .732.552 1.332 1.224 1.332.684 0 1.224-.6 1.224-1.332 0-.732-.54-1.332-1.224-1.332z"/>
                                </svg>
                                <span>Sign in with Discord</span>
                            </a>
                        <?php endif; ?>
                            
                            <?php if ((($googleAuthEnabled ?? false) || ($discordAuthEnabled ?? false)) && ($showPasswordLogin ?? false)): ?>
                                <div class="divider my-4">
                                    <span>or use password</span>
                                </div>
                            <?php endif; ?>
                        
                        <?php if ($showPasswordLogin ?? true): ?>
                            <form action="<?= htmlspecialchars(url('admin/login'), ENT_QUOTES, 'UTF-8') ?>" method="POST" <?= ($googleAuthEnabled ?? false) ? 'class="password-form-collapsed"' : '' ?>>
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
                                            <?= !($googleAuthEnabled ?? false) ? 'autofocus' : '' ?>
                                        >
                                        <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Login with Password
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if (($googleAuthEnabled ?? false) && !($hasUsers ?? true)): ?>
                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    First user to sign in will become administrator
                                </small>
                            </div>
                        <?php endif; ?>
                        
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

.btn-google {
    background: #fff;
    color: #757575;
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-weight: 500;
    transition: all var(--transition-fast);
}

.btn-google:hover {
    background: #f8f9fa;
    box-shadow: var(--shadow-md);
}

.google-icon {
    width: 20px;
    height: 20px;
}

.btn-discord {
    background: #5865F2;
    color: #fff;
    border: 1px solid #5865F2;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-weight: 500;
    transition: all var(--transition-fast);
}

.btn-discord:hover {
    background: #4752C4;
    border-color: #4752C4;
    color: #fff;
    box-shadow: var(--shadow-md);
}

.discord-icon {
    width: 24px;
    height: 24px;
}

.divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: var(--text-secondary);
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid var(--border-color);
}

.divider span {
    padding: 0 1rem;
    font-size: 0.875rem;
}

.password-form-collapsed {
    display: none;
}

.password-form-collapsed.show {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const passwordForm = document.querySelector('.password-form-collapsed');
    const divider = document.querySelector('.divider');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Toggle password form visibility when divider is clicked
    if (divider && passwordForm) {
        divider.style.cursor = 'pointer';
        divider.addEventListener('click', function() {
            passwordForm.classList.toggle('show');
            if (passwordForm.classList.contains('show')) {
                passwordInput?.focus();
            }
        });
    }
});
</script>
