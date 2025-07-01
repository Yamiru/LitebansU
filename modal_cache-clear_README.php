<!-- Clear Cache Button for All Pages 

To enable the cache clear modal window, add this between </footer> and </body> in the /templates/footer.php file.  

-->


<div class="clear-cache-wrapper">
    <button id="clear-cache-btn" class="btn btn-sm" title="Clear Cache">
        <i class="fas fa-sync-alt"></i> Clear Cache
    </button>
</div>

<!-- Cache Clear Modal -->
<div class="modal fade" id="cacheModal" tabindex="-1" aria-labelledby="cacheModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cacheModalLabel">Clear Statistics Cache</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to clear the statistics cache? This will force regeneration of all statistics on the next page load.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-clear-cache">Clear Cache</button>
            </div>
        </div>
    </div>
</div>


<script>
// Theme Toggle Script - Fixed to prevent page not found errors
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('change', function(e) {
            e.preventDefault();
            const theme = this.checked ? 'dark' : 'light';

            // Get current URL
            const currentUrl = window.location.href;
            const url = new URL(currentUrl);
            
            // Add theme parameter
            url.searchParams.set('theme', theme);

            // Navigate to URL with theme parameter
            window.location.href = url.toString();
        });
    }
    
    // Clear Cache functionality - FIXED
    const clearCacheBtn = document.getElementById('clear-cache-btn');
    const confirmBtn = document.getElementById('confirm-clear-cache');
    const modalEl = document.getElementById('cacheModal');
    
    if (clearCacheBtn && modalEl && confirmBtn) {
        // Remove any duplicate event listeners by cloning
        const newClearCacheBtn = clearCacheBtn.cloneNode(true);
        clearCacheBtn.parentNode.replaceChild(newClearCacheBtn, clearCacheBtn);
        
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        // Initialize modal
        const bsModal = new bootstrap.Modal(modalEl, {
            keyboard: true,
            backdrop: true
        });
        
        // Clear cache button click handler
        newClearCacheBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Clear cache button clicked');
            bsModal.show();
        });
        
        // Confirm button click handler
        newConfirmBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Confirm button clicked');
            
            // Disable button to prevent multiple clicks
            newConfirmBtn.disabled = true;
            newConfirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }
                
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                
                // Build the correct URL
                const basePath = '<?= htmlspecialchars($config['base_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>';
                const clearCacheUrl = (basePath ? basePath : '') + '/stats/clear-cache';
                
                console.log('Sending request to:', clearCacheUrl);
                
                const response = await fetch(clearCacheUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    bsModal.hide();
                    
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                    alert.style.zIndex = '9999';
                    alert.innerHTML = `
                        <i class="fas fa-check-circle"></i> ${data.message || 'Cache cleared successfully'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alert);
                    
                    // Auto dismiss after 2 seconds and reload page
                    setTimeout(() => {
                        alert.remove();
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to clear cache');
                }
            } catch (error) {
                console.error('Cache clear error:', error);
                
                // Re-enable button
                newConfirmBtn.disabled = false;
                newConfirmBtn.innerHTML = 'Clear Cache';
                
                // Show error message
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                alert.style.zIndex = '9999';
                alert.innerHTML = `
                    <i class="fas fa-exclamation-triangle"></i> ${error.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alert);
                
                // Auto dismiss error after 5 seconds
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 5000);
            }
        });
    }
});
</script>
