<!-- Notifications Modal -->
<div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="notificationsModalLabel">
                    <i class="fas fa-bell"></i> Notifikasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="notifications-content">
                    <!-- Notifications will be loaded here via AJAX -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                        <p class="mt-2">Memuat notifikasi...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
                {{-- <button type="button" class="btn btn-primary" id="mark-all-read-btn">
                    <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                </button> --}}
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationsModal = document.getElementById('notificationsModal');
    const notificationsContent = document.getElementById('notifications-content');
    const markAllReadBtn = document.getElementById('mark-all-read-btn');
    
    // Load notifications when modal is shown
    notificationsModal.addEventListener('show.bs.modal', function () {
        loadNotifications();
    });
    
    // Function to load notifications via AJAX
    function loadNotifications() {
        fetch('{{ route("customer.notifications.data") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayNotifications(data.notifications);
            } else {
                notificationsContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> ${data.message || 'Gagal memuat notifikasi'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            notificationsContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat memuat notifikasi
                </div>
            `;
        });
    }
    
    // Function to display notifications
    function displayNotifications(notifications) {
        if (notifications.length === 0) {
            notificationsContent.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5>Tidak ada notifikasi</h5>
                    <p class="text-muted">Anda tidak memiliki notifikasi baru saat ini.</p>
                </div>
            `;
            return;
        }
        
        let notificationsHtml = `
            <div class="list-group list-group-flush">
        `;
        
        notifications.forEach(notification => {
            const iconColor = getIconColor(notification.color);
            const timeAgo = getTimeAgo(notification.created_at);
            
            // Check if notification is expired
            const isExpired = notification.type === 'discount' && !notification.is_active;
            
            notificationsHtml += `
                <div class="list-group-item list-group-item-action ${isExpired ? 'opacity-75' : ''}">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-${notification.color} text-white p-2">
                                <i class="${notification.icon}"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1 ${isExpired ? 'text-decoration-line-through' : ''}">${notification.title}</h6>
                                <small class="text-muted">${timeAgo}</small>
                            </div>
                            <p class="mb-1">${notification.message}</p>
                            ${
                                notification.expires_at ? 
                                `<small class="text-muted">
                                    <i class="fas fa-clock"></i> Berlaku hingga: ${notification.expires_at}
                                </small>` : ''
                            }
                            ${
                                notification.type === 'discount' ? 
                                `<div class="mt-2">
                                    ${
                                        notification.is_active ? 
                                        `<span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> Aktif
                                        </span>` : 
                                        `<span class="badge bg-danger">
                                            <i class="fas fa-times-circle"></i> Kadaluarsa
                                        </span>`
                                    }
                                </div>` : ''
                            }
                            ${
                                isExpired && notification.type === 'discount' ? 
                                `<div class="mt-2">
                                    <button class="btn btn-sm btn-outline-danger delete-notification" 
                                            data-id="${notification.id}" 
                                            data-type="${notification.type}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>` : ''
                            }
                        </div>
                    </div>
                </div>
            `;
        });
        
        notificationsHtml += `
            </div>
        `;
        
        notificationsContent.innerHTML = notificationsHtml;
        
        // Add event listeners for delete buttons
        document.querySelectorAll('.delete-notification').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const type = this.getAttribute('data-type');
                deleteNotification(id, type);
            });
        });
    }
    
    // Helper function to get icon color
    function getIconColor(color) {
        const colors = {
            'success': '#28a745',
            'info': '#17a2b8',
            'warning': '#ffc107',
            'danger': '#dc3545'
        };
        return colors[color] || colors['info'];
    }
    
    // Helper function to get time ago
    function getTimeAgo(dateString) {
        // Simple implementation - in real application, you might want to use a library like moment.js
        return dateString;
    }
    
    // Delete notification function
    function deleteNotification(id, type) {
        if (!confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
            return;
        }
        
        fetch(`/customer/notifications/delete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                type: type
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload notifications
                loadNotifications();
                
                // Show success message
                showNotificationToast('success', data.message);
            } else {
                showNotificationToast('error', data.message || 'Gagal menghapus notifikasi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotificationToast('error', 'Terjadi kesalahan saat menghapus notifikasi');
        });
    }
    
    // Show notification toast
    function showNotificationToast(type, message) {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white border-0 bg-${type === 'success' ? 'success' : 'danger'}`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', function () {
            toast.remove();
            toastContainer.remove();
        });
    }
    
    // Mark all notifications as read
    markAllReadBtn.addEventListener('click', function() {
        fetch('{{ route("customer.notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload notifications
                loadNotifications();
                
                // Show success message
                showNotificationToast('success', data.message);
            } else {
                showNotificationToast('error', data.message || 'Gagal menandai notifikasi sebagai sudah dibaca');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotificationToast('error', 'Terjadi kesalahan saat menandai notifikasi');
        });
    });
});
</script>