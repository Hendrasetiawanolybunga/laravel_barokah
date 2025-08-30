/**
 * Notification handler for Barokah Store
 */

document.addEventListener('DOMContentLoaded', function() {
    // Update notification count every 30 seconds
    setInterval(updateNotificationCount, 30000);
    
    // Initial update
    updateNotificationCount();
});

/**
 * Update notification count via AJAX
 */
function updateNotificationCount() {
    fetch('/pelanggan/notifikasi/unread-count')
        .then(response => response.json())
        .then(data => {
            const notificationBadges = document.querySelectorAll('.notification-badge');
            
            notificationBadges.forEach(badge => {
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            });
        })
        .catch(error => console.error('Error updating notification count:', error));
}

/**
 * Mark notification as read
 * 
 * @param {number} id Notification ID
 * @param {Event} event Click event
 */
function markAsRead(id, event) {
    event.preventDefault();
    
    fetch(`/pelanggan/notifikasi/${id}/mark-read`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                const notificationElement = document.querySelector(`#notification-${id}`);
                if (notificationElement) {
                    notificationElement.classList.remove('bg-light');
                    notificationElement.querySelector('.notification-title').classList.remove('fw-bold');
                }
                
                // Update notification count
                updateNotificationCount();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
}