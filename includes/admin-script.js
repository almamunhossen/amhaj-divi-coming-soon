/**
 * Amhaj Divi Coming Soon - Admin Script
 */
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation dialog when clicking the "Force Check Updates" button
    const updateBtn = document.querySelector('.adcs-btn-secondary');
    if (updateBtn) {
        updateBtn.addEventListener('click', function(e) {
            const confirmMsg = typeof adcsAdminParams !== 'undefined' && adcsAdminParams.confirm_text 
                ? adcsAdminParams.confirm_text 
                : 'Are you sure you want to force check for updates? This will clear the cached data and poll GitHub directly.';
            if (!confirm(confirmMsg)) {
                e.preventDefault();
            }
        });
    }
});
