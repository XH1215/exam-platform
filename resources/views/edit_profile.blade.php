<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quiz-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/edit-profile.css') }}">
</head>

<body>
    <div id="loading-state" class="loading-state">
        <div class="spinner-lg"></div>
        <p class="text-secondary">Loading profile...</p>
    </div>

    <div id="profile-content" class="profile-content" style="display: none;">
        <div class="edit-profile-container">
            <div class="edit-profile-header">
                <a href="{{ url('/profile') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Profile
                </a>
                <h1 class="heading-2">Edit Profile</h1>
            </div>

            <div class="edit-profile-main">
                <div class="edit-profile-sidebar">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <nav class="profile-nav">
                        <button class="nav-item active" data-section="profile">
                            <i class="fas fa-user-edit"></i>
                            <span>Update Profile</span>
                        </button>
                        <button class="nav-item" data-section="password">
                            <i class="fas fa-key"></i>
                            <span>Change Password</span>
                        </button>
                    </nav>
                </div>

                <div class="edit-profile-content">
                    <div id="profile-section" class="content-section active">
                        <div class="section-header">
                            <div class="section-icon primary">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div class="section-title">
                                <h3 class="heading-3">Profile Information</h3>
                                <p class="text-secondary">Update your basic profile information</p>
                            </div>
                        </div>

                        <div class="section-body">
                            <div id="profile-success" class="alert alert-success" style="display: none;"></div>
                            <div id="profile-error" class="alert alert-error" style="display: none;"></div>

                            <form id="profile-form" novalidate>
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user"></i>
                                        Full Name
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" id="name" name="name" class="form-input"
                                            placeholder="Enter your full name" required>
                                        <i class="fas fa-user input-icon"></i>
                                    </div>
                                    <div id="name-error" class="field-error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i>
                                        Email Address
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="email" id="email" name="email" class="form-input"
                                            placeholder="Enter your email address" required>
                                        <i class="fas fa-envelope input-icon"></i>
                                    </div>
                                    <div id="email-error" class="field-error"></div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary btn-full">
                                        <span class="btn-text">
                                            <i class="fas fa-save"></i>
                                            Update Profile
                                        </span>
                                        <div class="spinner"></div>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="password-section" class="content-section">
                        <div class="section-header">
                            <div class="section-icon warning">
                                <i class="fas fa-key"></i>
                            </div>
                            <div class="section-title">
                                <h3 class="heading-3">Change Password</h3>
                                <p class="text-secondary">Update your account password for better security</p>
                            </div>
                        </div>

                        <div class="section-body">
                            <div id="password-success" class="alert alert-success" style="display: none;"></div>
                            <div id="password-error" class="alert alert-error" style="display: none;"></div>

                            <form id="password-form" novalidate>
                                <div class="form-group">
                                    <label for="current_password" class="form-label">
                                        <i class="fas fa-lock"></i>
                                        Current Password
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="password" id="current_password" name="current_password"
                                            class="form-input" placeholder="Enter your current password" required>
                                        <i class="fas fa-lock input-icon"></i>
                                    </div>
                                    <div id="current-password-error" class="field-error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="new_password" class="form-label">
                                        <i class="fas fa-key"></i>
                                        New Password
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="password" id="new_password" name="new_password" class="form-input"
                                            placeholder="Enter your new password" required>
                                        <i class="fas fa-key input-icon"></i>
                                    </div>
                                    <div class="password-requirements">
                                        <p class="text-muted">Password must contain:</p>
                                        <ul class="requirements-list">
                                            <li id="req-length" class="requirement">
                                                <i class="fas fa-times"></i> At least 8 characters
                                            </li>
                                            <li id="req-uppercase" class="requirement">
                                                <i class="fas fa-times"></i> One uppercase letter
                                            </li>
                                            <li id="req-lowercase" class="requirement">
                                                <i class="fas fa-times"></i> One lowercase letter
                                            </li>
                                            <li id="req-number" class="requirement">
                                                <i class="fas fa-times"></i> One number
                                            </li>
                                            <li id="req-special" class="requirement">
                                                <i class="fas fa-times"></i> One special character
                                            </li>
                                        </ul>
                                    </div>
                                    <div id="new-password-error" class="field-error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="new_password_confirmation" class="form-label">
                                        <i class="fas fa-check-double"></i>
                                        Confirm New Password
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="password" id="new_password_confirmation"
                                            name="new_password_confirmation" class="form-input"
                                            placeholder="Confirm your new password" required>
                                        <i class="fas fa-check-double input-icon"></i>
                                    </div>
                                    <div id="confirm-password-error" class="field-error"></div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-warning btn-full">
                                        <span class="btn-text">
                                            <i class="fas fa-key"></i>
                                            Change Password
                                        </span>
                                        <div class="spinner"></div>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const token = localStorage.getItem('token');

            if (!token) {
                window.location.href = '{{ url("login") }}';
                return;
            }

            const loadingState = document.getElementById('loading-state');
            const profileContent = document.getElementById('profile-content');

            const profileForm = document.getElementById('profile-form');
            const passwordForm = document.getElementById('password-form');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('new_password_confirmation');

            // Navigation functionality
            const navItems = document.querySelectorAll('.nav-item');
            const contentSections = document.querySelectorAll('.content-section');

            navItems.forEach(item => {
                item.addEventListener('click', function () {
                    const targetSection = this.dataset.section;

                    navItems.forEach(nav => nav.classList.remove('active'));
                    contentSections.forEach(section => section.classList.remove('active'));

                    this.classList.add('active');
                    document.getElementById(targetSection + '-section').classList.add('active');
                });
            });

            async function loadProfile() {
                try {
                    const response = await fetch('{{ url("api/profile") }}', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        nameInput.value = data.data.name || '';
                        emailInput.value = data.data.email || '';

                        loadingState.style.display = 'none';
                        profileContent.style.display = 'block';
                    } else {
                        throw new Error('Failed to load profile');
                    }
                } catch (error) {
                    console.error('Error loading profile:', error);
                    window.location.href = '{{ url("login") }}';
                }
            }

            function showAlert(alertId, message, isError = false) {
                const alert = document.getElementById(alertId);
                alert.textContent = message;
                alert.className = `alert ${isError ? 'alert-error' : 'alert-success'} show`;
                alert.style.display = 'block';

                setTimeout(() => {
                    alert.style.display = 'none';
                    alert.classList.remove('show');
                }, 5000);
            }

            function showFieldError(fieldId, message) {
                const errorDiv = document.getElementById(fieldId);
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }

            function clearFieldErrors(form) {
                const errors = form.querySelectorAll('.field-error');
                errors.forEach(error => {
                    error.style.display = 'none';
                    error.textContent = '';
                });
            }

            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            function validatePassword(password) {
                const requirements = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    special: /[^\w\d]/.test(password)
                };

                Object.keys(requirements).forEach(req => {
                    const element = document.getElementById(`req-${req}`);
                    const icon = element.querySelector('i');

                    if (requirements[req]) {
                        element.classList.add('valid');
                        icon.className = 'fas fa-check';
                    } else {
                        element.classList.remove('valid');
                        icon.className = 'fas fa-times';
                    }
                });

                return Object.values(requirements).every(req => req);
            }

            newPasswordInput.addEventListener('input', function () {
                validatePassword(this.value);
            });

            confirmPasswordInput.addEventListener('input', function () {
                const newPassword = newPasswordInput.value;
                const confirmPassword = this.value;

                if (confirmPassword && newPassword !== confirmPassword) {
                    showFieldError('confirm-password-error', 'Passwords do not match');
                } else {
                    document.getElementById('confirm-password-error').style.display = 'none';
                }
            });

            profileForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                clearFieldErrors(this);
                const submitButton = this.querySelector('.btn');

                const name = nameInput.value.trim();
                const email = emailInput.value.trim();

                let hasErrors = false;
                if (!name) {
                    showFieldError('name-error', 'Name is required');
                    hasErrors = true;
                }
                if (!email) {
                    showFieldError('email-error', 'Email is required');
                    hasErrors = true;
                } else if (!isValidEmail(email)) {
                    showFieldError('email-error', 'Please enter a valid email address');
                    hasErrors = true;
                }

                if (hasErrors) return;

                submitButton.classList.add('loading');
                submitButton.disabled = true;

                try {
                    const response = await fetch('{{ url("api/profile") }}', {
                        method: 'PUT',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ name, email })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showAlert('profile-success', 'Profile updated successfully!');
                    } else {
                        let message = data.message || 'Failed to update profile';
                        if (data.data && data.data.errors) {
                            const errors = data.data.errors;
                            Object.keys(errors).forEach(field => {
                                showFieldError(`${field}-error`, errors[field][0]);
                            });
                        } else {
                            showAlert('profile-error', message, true);
                        }
                    }
                } catch (error) {
                    showAlert('profile-error', 'Network error, please try again later.', true);
                } finally {
                    submitButton.classList.remove('loading');
                    submitButton.disabled = false;
                }
            });

            passwordForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                clearFieldErrors(this);
                const submitButton = this.querySelector('.btn');

                const currentPassword = document.getElementById('current_password').value;
                const newPassword = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                let hasErrors = false;
                if (!currentPassword) {
                    showFieldError('current-password-error', 'Current password is required');
                    hasErrors = true;
                }
                if (!newPassword) {
                    showFieldError('new-password-error', 'New password is required');
                    hasErrors = true;
                } else if (!validatePassword(newPassword)) {
                    showFieldError('new-password-error', 'Password does not meet requirements');
                    hasErrors = true;
                }
                if (!confirmPassword) {
                    showFieldError('confirm-password-error', 'Please confirm your new password');
                    hasErrors = true;
                } else if (newPassword !== confirmPassword) {
                    showFieldError('confirm-password-error', 'Passwords do not match');
                    hasErrors = true;
                }

                if (hasErrors) return;

                submitButton.classList.add('loading');
                submitButton.disabled = true;

                try {
                    const response = await fetch('{{ url("api/change-password") }}', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            current_password: currentPassword,
                            new_password: newPassword,
                            new_password_confirmation: confirmPassword
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showAlert('password-success', 'Password changed successfully!');
                        passwordForm.reset();
                        document.querySelectorAll('.requirement').forEach(req => {
                            req.classList.remove('valid');
                            req.querySelector('i').className = 'fas fa-times';
                        });
                    } else {
                        let message = data.message || 'Failed to change password';
                        if (data.data && data.data.errors) {
                            const errors = data.data.errors;
                            Object.keys(errors).forEach(field => {
                                const fieldName = field.replace('_', '-');
                                showFieldError(`${fieldName}-error`, errors[field][0]);
                            });
                        } else {
                            showAlert('password-error', message, true);
                        }
                    }
                } catch (error) {
                    showAlert('password-error', 'Network error, please try again later.', true);
                } finally {
                    submitButton.classList.remove('loading');
                    submitButton.disabled = false;
                }
            });

            loadProfile();
        });
    </script>
</body>

</html>