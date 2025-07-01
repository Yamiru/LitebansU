<div class="error-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger mb-4"></i>
                        <h1 class="display-4 mb-3"><?= htmlspecialchars($title ?? 'Error', ENT_QUOTES, 'UTF-8') ?></h1>
                        <p class="lead text-muted mb-4">
                            <?= htmlspecialchars($message ?? 'An error occurred', ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-home"></i>
                                <?= htmlspecialchars($lang->get('nav.home'), ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <button onclick="history.back()" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i>
                                Go Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="text-align: center;">
    <a href="https://github.com/Yamiru/LitebansU" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-github"></i> Github project
    </a>
</div>
<style>
.error-page {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>