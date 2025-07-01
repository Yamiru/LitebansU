<div class="protest-page">
    <!-- Header -->
    <div class="page-header mb-5">
        <h1 class="display-4 text-center">
            <i class="fas fa-gavel text-primary"></i>
            <?= htmlspecialchars($lang->get('protest.title'), ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p class="lead text-center text-muted">
            <?= htmlspecialchars($lang->get('protest.description'), ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>

    <!-- How To Section -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">
                <i class="fas fa-info-circle"></i>
                <?= htmlspecialchars($lang->get('protest.how_to_title'), ENT_QUOTES, 'UTF-8') ?>
            </h3>
        </div>
        <div class="card-body">
            <p class="lead mb-4"><?= htmlspecialchars($lang->get('protest.how_to_subtitle'), ENT_QUOTES, 'UTF-8') ?></p>
            
            <!-- Step 1 -->
            <div class="protest-step">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-clipboard-list"></i>
                    <?= htmlspecialchars($lang->get('protest.step1_title'), ENT_QUOTES, 'UTF-8') ?>
                </h4>
                <p><?= htmlspecialchars($lang->get('protest.step1_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                <ul class="list-unstyled ms-4">
                    <?php 
                    $step1Items = $lang->get('protest.step1_items');
                    if (is_array($step1Items)):
                        foreach ($step1Items as $item): ?>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success"></i>
                                <?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?>
                            </li>
                        <?php endforeach;
                    else: ?>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <?= htmlspecialchars($step1Items, ENT_QUOTES, 'UTF-8') ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <hr class="my-4">

            <!-- Step 2: Contact Methods -->
            <div class="protest-step">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-envelope"></i>
                    <?= htmlspecialchars($lang->get('protest.step2_title'), ENT_QUOTES, 'UTF-8') ?>
                </h4>
                <p><?= htmlspecialchars($lang->get('protest.step2_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                
                <div class="row g-4 mt-3">
                    <!-- Discord -->
                    <div class="col-md-4">
                        <div class="contact-method-card h-100">
                            <div class="contact-icon bg-discord">
                                <i class="fab fa-discord"></i>
                            </div>
                            <h5><?= htmlspecialchars($lang->get('protest.discord_title'), ENT_QUOTES, 'UTF-8') ?></h5>
                            <p><?= htmlspecialchars($lang->get('protest.discord_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                            <?php if ($protestConfig['discord_link'] !== '#'): ?>
                                <a href="<?= htmlspecialchars($protestConfig['discord_link'], ENT_QUOTES, 'UTF-8') ?>" 
                                   class="btn btn-discord" target="_blank" rel="noopener">
                                    <i class="fab fa-discord"></i>
                                    <?= htmlspecialchars($lang->get('protest.discord_button'), ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-md-4">
                        <div class="contact-method-card h-100">
                            <div class="contact-icon bg-email">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h5><?= htmlspecialchars($lang->get('protest.email_title'), ENT_QUOTES, 'UTF-8') ?></h5>
                            <p><?= htmlspecialchars($lang->get('protest.email_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="email-address">
                                <code><?= htmlspecialchars($protestConfig['email_address'], ENT_QUOTES, 'UTF-8') ?></code>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Forum -->
                    <div class="col-md-4">
                        <div class="contact-method-card h-100">
                            <div class="contact-icon bg-forum">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h5><?= htmlspecialchars($lang->get('protest.forum_title'), ENT_QUOTES, 'UTF-8') ?></h5>
                            <p><?= htmlspecialchars($lang->get('protest.forum_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                            <?php if ($protestConfig['forum_link'] !== '#'): ?>
                                <a href="<?= htmlspecialchars($protestConfig['forum_link'], ENT_QUOTES, 'UTF-8') ?>" 
                                   class="btn btn-forum" target="_blank" rel="noopener">
                                    <i class="fas fa-comments"></i>
                                    <?= htmlspecialchars($lang->get('protest.forum_button'), ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Step 3 -->
            <div class="protest-step">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-edit"></i>
                    <?= htmlspecialchars($lang->get('protest.step3_title'), ENT_QUOTES, 'UTF-8') ?>
                </h4>
                <p><?= htmlspecialchars($lang->get('protest.step3_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                <ul class="list-unstyled ms-4">
                    <?php 
                    $step3Items = $lang->get('protest.step3_items');
                    if (is_array($step3Items)):
                        foreach ($step3Items as $item): ?>
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary"></i>
                                <?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?>
                            </li>
                        <?php endforeach;
                    else: ?>
                        <li class="mb-2">
                            <i class="fas fa-arrow-right text-primary"></i>
                            <?= htmlspecialchars($step3Items, ENT_QUOTES, 'UTF-8') ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <hr class="my-4">

            <!-- Step 4 -->
            <div class="protest-step">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-clock"></i>
                    <?= htmlspecialchars($lang->get('protest.step4_title'), ENT_QUOTES, 'UTF-8') ?>
                </h4>
                <p><?= htmlspecialchars($lang->get('protest.step4_desc'), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
    </div>

    <!-- Guidelines -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">
                <i class="fas fa-book"></i>
                <?= htmlspecialchars($lang->get('protest.guidelines_title'), ENT_QUOTES, 'UTF-8') ?>
            </h4>
        </div>
        <div class="card-body">
            <ul class="guidelines-list">
                <?php 
                $guidelinesItems = $lang->get('protest.guidelines_items');
                if (is_array($guidelinesItems)):
                    foreach ($guidelinesItems as $guideline): ?>
                        <li class="mb-3">
                            <i class="fas fa-check text-info"></i>
                            <?= htmlspecialchars($guideline, ENT_QUOTES, 'UTF-8') ?>
                        </li>
                    <?php endforeach;
                else: ?>
                    <li class="mb-3">
                        <i class="fas fa-check text-info"></i>
                        <?= htmlspecialchars($guidelinesItems, ENT_QUOTES, 'UTF-8') ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Warning -->
    <div class="alert alert-danger">
        <h5 class="alert-heading">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($lang->get('protest.warning_title'), ENT_QUOTES, 'UTF-8') ?>
        </h5>
        <p class="mb-0"><?= htmlspecialchars($lang->get('protest.warning_desc'), ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</div>

<!-- Protest Page Styles -->
<style>
.protest-page {
    max-width: 1000px;
    margin: 0 auto;
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.protest-step {
    margin-bottom: 2rem;
}

.protest-step h4 {
    margin-bottom: 1rem;
}

.contact-method-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: 2rem;
    text-align: center;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: all var(--transition-base);
}

.contact-method-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.contact-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    margin-bottom: 1.5rem;
}

.bg-discord {
    background: #7289DA;
}

.bg-email {
    background: #EA4335;
}

.bg-forum {
    background: #FFA500;
}

.btn-discord {
    background: #7289DA;
    color: white;
    border: none;
}

.btn-discord:hover {
    background: #5b6eae;
    color: white;
}

.btn-forum {
    background: #FFA500;
    color: white;
    border: none;
}

.btn-forum:hover {
    background: #e69500;
    color: white;
}

.email-address {
    background: var(--bg-tertiary);
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-md);
    margin-top: 1rem;
}

.email-address code {
    color: var(--primary);
    font-size: 1.1rem;
}

.guidelines-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.guidelines-list li {
    padding-left: 1.5rem;
    position: relative;
}

.guidelines-list li i {
    position: absolute;
    left: 0;
    top: 0.25rem;
}

/* Dark mode adjustments */
.theme-dark .contact-method-card {
    background: var(--bg-secondary);
}

.theme-dark .card-header.bg-primary,
.theme-dark .card-header.bg-info {
    background: var(--primary) !important;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .contact-method-card {
        padding: 1.5rem;
    }
    
    .contact-icon {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .protest-step h4 {
        font-size: 1.25rem;
    }
}
</style>