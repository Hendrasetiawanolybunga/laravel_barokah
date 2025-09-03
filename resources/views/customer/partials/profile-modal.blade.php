<!-- Profile Edit Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="profileModalLabel">
                    <i class="fas fa-user-edit"></i> Edit Profil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="profileForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Loading State -->
                    <div id="profileLoading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data profil...</p>
                    </div>
                    
                    <!-- Form Content -->
                    <div id="profileFormContent">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user"></i> Informasi Dasar
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="profileName" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="profileName" name="name" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profileEmail" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="profileEmail" name="email" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profileTglLahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="profileTglLahir" name="tgl_lahir">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-address-book"></i> Informasi Kontak
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="profileTelepon" class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control" id="profileTelepon" name="no_hp" placeholder="08xxxxxxxxxx">
                                    <div class="invalid-feedback"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profileAlamat" class="form-label">Alamat Lengkap</label>
                                    <textarea class="form-control" id="profileAlamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap..."></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Password Section -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-lock"></i> Ubah Password (Opsional)
                                </h6>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <small>Kosongkan jika tidak ingin mengubah password</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="profilePassword" class="form-label">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="profilePassword" name="password" placeholder="Minimal 8 karakter">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('profilePassword')">
                                            <i class="fas fa-eye" id="profilePasswordIcon"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="profilePasswordConfirmation" class="form-label">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="profilePasswordConfirmation" name="password_confirmation" placeholder="Ulangi password baru">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('profilePasswordConfirmation')">
                                            <i class="fas fa-eye" id="profilePasswordConfirmationIcon"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="profileSubmitBtn">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Profile Modal Functions
function openProfileModal() {
    const modal = new bootstrap.Modal(document.getElementById('profileModal'));
    const loadingDiv = document.getElementById('profileLoading');
    const formContent = document.getElementById('profileFormContent');
    
    // Show loading state
    loadingDiv.style.display = 'block';
    formContent.style.display = 'none';
    
    // Clear any previous errors
    clearFormErrors();
    
    modal.show();
    
    // Load profile data
    fetch('/customer/profile/edit')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateProfileForm(data.user);
                loadingDiv.style.display = 'none';
                formContent.style.display = 'block';
            } else {
                throw new Error('Failed to load profile data');
            }
        })
        .catch(error => {
            console.error('Error loading profile:', error);
            loadingDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Gagal memuat data profil. Silakan coba lagi.
                </div>
            `;
        });
}

function populateProfileForm(user) {
    document.getElementById('profileName').value = user.name || '';
    document.getElementById('profileEmail').value = user.email || '';
    document.getElementById('profileTelepon').value = user.customer?.telepon || '';
    document.getElementById('profileAlamat').value = user.customer?.alamat || '';
    document.getElementById('profileTglLahir').value = user.customer?.tgl_lahir || '';
    
    // Clear password fields
    document.getElementById('profilePassword').value = '';
    document.getElementById('profilePasswordConfirmation').value = '';
}

function clearFormErrors() {
    const form = document.getElementById('profileForm');
    const errorElements = form.querySelectorAll('.is-invalid');
    const feedbackElements = form.querySelectorAll('.invalid-feedback');
    
    errorElements.forEach(element => element.classList.remove('is-invalid'));
    feedbackElements.forEach(element => element.textContent = '');
}

function displayFormErrors(errors) {
    clearFormErrors();
    
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.querySelector(`[name="${field}"]`);
        const feedback = input?.parentElement.querySelector('.invalid-feedback') || 
                        input?.closest('.mb-3')?.querySelector('.invalid-feedback');
        
        if (input && feedback) {
            input.classList.add('is-invalid');
            feedback.textContent = Array.isArray(messages) ? messages[0] : messages;
        }
    }
}

function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + 'Icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Handle form submission
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('profileSubmitBtn');
    const originalBtnText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    submitBtn.disabled = true;
    
    // Clear previous errors
    clearFormErrors();
    
    const formData = new FormData(this);
    
    fetch('/customer/profile/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('profileModal'));
            modal.hide();
            
            // Show success notification
            showNotification('Profil berhasil diperbarui', 'success');
            
            // Update navbar name if changed
            const nameField = document.getElementById('profileName');
            if (nameField) {
                document.querySelectorAll('.navbar .dropdown-toggle').forEach(element => {
                    if (element.textContent.includes('{{')) return; // Skip template text
                    const iconHtml = element.querySelector('i')?.outerHTML || '';
                    element.innerHTML = iconHtml + ' ' + nameField.value;
                });
            }
        } else {
            if (data.errors) {
                displayFormErrors(data.errors);
            } else {
                showNotification(data.message || 'Terjadi kesalahan', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error updating profile:', error);
        showNotification('Terjadi kesalahan saat menyimpan profil', 'error');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
});

function showNotification(message, type) {
    const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>