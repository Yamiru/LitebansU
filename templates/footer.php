</div>
    </main>
    
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($config['site_name'] ?? 'LiteBans', ENT_QUOTES, 'UTF-8') ?>. <?= htmlspecialchars($lang->get('footer.rights'), ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="col-md-6 text-end">
                    <p>
                        <?= htmlspecialchars($lang->get('footer.powered_by'), ENT_QUOTES, 'UTF-8') ?> 
                        <a href="https://github.com/Yamiru/LitebansU" target="_blank" rel="noopener noreferrer" class="text-primary">
                            LitebansU
                        </a>
                    </p>
                    <p class="small text-muted">
                        &copy; 2024 <a href="https://github.com/Yamiru/LitebansU" target="_blank" rel="noopener noreferrer" class="text-muted">LitebansU</a> - 
                        <?= htmlspecialchars($lang->get('footer.license'), ENT_QUOTES, 'UTF-8') ?> MIT License
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="<?= htmlspecialchars(asset('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    
    <!-- Performance monitoring -->
    <script>
        if (window.performance && window.performance.timing) {
            window.addEventListener('load', () => {
                const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                console.log(`Page loaded in ${loadTime}ms`);
            });
        }
    </script>
</body>
</html>