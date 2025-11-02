<div class="search-page">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="search-container mb-5">
                <h1 class="text-center mb-4">
                    <i class="fas fa-search text-primary"></i>
                    <?= htmlspecialchars($lang->get('search.title'), ENT_QUOTES, 'UTF-8') ?>
                </h1>
                
                <form id="search-form" class="search-form">
                    <div class="row g-3">
                        <div class="col-md-9">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input 
                                    type="text" 
                                    id="search-input"
                                    class="form-control" 
                                    placeholder="<?= htmlspecialchars($lang->get('search.placeholder'), ENT_QUOTES, 'UTF-8') ?>"
                                    autocomplete="off"
                                    maxlength="36"
                                    minlength="1"
                                >
                            </div>
                            <small class="form-text text-muted">
                                <?= htmlspecialchars($lang->get('search.help'), ENT_QUOTES, 'UTF-8') ?>
                            </small>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-search"></i>
                                <?= htmlspecialchars($lang->get('search.button'), ENT_QUOTES, 'UTF-8') ?>
                            </button>
                        </div>
                    </div>
                </form>
                
                <div id="search-results" class="mt-4"></div>
            </div>
        </div>
    </div>
    
    <!-- Quick Search Tips -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb text-warning"></i>
                        Search Tips
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Search by Player Name</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> <code>Steve</code></li>
                                <li><i class="fas fa-check text-success"></i> <code>Notch</code></li>
                                <li><i class="fas fa-check text-success"></i> <code>jeb_</code></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Search by Punishment ID</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Ban ID: <code>12345</code></li>
                                <li><i class="fas fa-check text-success"></i> Mute ID: <code>999</code></li>
                                <li><i class="fas fa-info-circle text-info"></i> Any numeric ID</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Search by UUID</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Full UUID format</li>
                                <li><i class="fas fa-check text-success"></i> <code>550e8400-e29b-41d4-a716-446655440000</code></li>
                                <li><i class="fas fa-info-circle text-info"></i> Minimum 1 character</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
