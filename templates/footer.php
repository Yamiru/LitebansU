</div>
</main>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= htmlspecialchars(asset('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>

<footer class="footer mt-auto">
    <div class="container">
        <div class="row text-center text-md-start align-items-center py-3">
            <!-- Left side -->
            <div class="col-md-6 mb-2 mb-md-0">
                <p class="mb-0">
                    &#169; <?= htmlspecialchars($config['footer_site_name'], ENT_QUOTES, 'UTF-8') ?> <?= date('Y') ?>
                </p>
            </div>
            <!-- Right side -->
            <div class="col-md-6 text-md-end">
                <p class="mb-0">
                    Powered by 
                    <strong>
                        <a href="https://github.com/Yamiru/LitebansU" target="_blank" rel="noopener noreferrer" class="text-decoration-none"><strong>LitebansU</strong></a>
                    </strong> 
                    <span class="text-muted">-</span> 
                    A project by <a href="https://yamiru.com" target="_blank" rel="noopener noreferrer" class="text-decoration-none">Yamiru</a>
                </p>
            </div>
        </div>
    </div>
</footer>


</body>
</html>