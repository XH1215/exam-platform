<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quiz-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/accManage.css') }}">
</head>

<body>
    <div id="loading-state" class="loading-state">
        <div class="spinner-lg"></div>
        <p class="text-secondary">Loading account management...</p>
    </div>

    <div id="account-content" class="account-content" style="display: none;">
        <div class="account-management-container">
            <div class="account-management-header">
                <a href="{{ url('/profile') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Profile
                </a>
                <h1 class="heading-2">Account Management</h1>
            </div>

            <div class="account-management-main">
                <div class="account-management-sidebar">
                    <div class="admin-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <nav class="account-nav">
                        <button class="nav-item active" data-section="list">
                            <i class="fas fa-list-ul"></i>
                            <span>List All Users</span>
                        </button>
                        <button class="nav-item" data-section="register">
                            <i class="fas fa-user-plus"></i>
                            <span>Register User</span>
                        </button>
                        <button class="nav-item" data-section="delete">
                            <i class="fas fa-user-minus"></i>
                            <span>Delete User</span>
                        </button>
                    </nav>
                </div>

                <div class="account-management-content">
                    <div id="list-section" class="content-section active">
                        <div class="section-header">
                            <div class="section-icon primary">
                                <i class="fas fa-list-ul"></i>
                            </div>
                            <div class="section-title">
                                <h3 class="heading-3">User List</h3>
                                <p class="text-secondary">View all registered users with their details</p>
                            </div>
                            <button id="refresh-users" class="btn btn-primary">
                                <i class="fas fa-refresh"></i>
                                Refresh
                            </button>
                        </div>

                        <div class="section-body">
                            <div id="list-success" class="alert alert-success" style="display: none;"></div>
                            <div id="list-error" class="alert alert-error" style="display: none;"></div>

                            <div class="users-table-container">
                                <table class="users-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="users-table-body">
                                        <tr>
                                            <td colspan="6" class="loading-row">
                                                <div class="table-spinner"></div>
                                                Loading users...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="register-section" class="content-section">
                        <div class="section-header">
                            <div class="section-icon success">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="section-title">
                                <h3 class="heading-3">Register New User</h3>
                                <p class="text-secondary">Create a new user account with any role</p>
                            </div>
                        </div>

                        <div class="section-body">
                            <div id="register-success" class="alert alert-success" style="display: none;"></div>
                            <div id="register-error" class="alert alert-error" style="display: none;"></div>

                            <form id="register-form" novalidate>
                                <div class="form-group">
                                    <label for="register_name" class="form-label">
                                        <i class="fas fa-user"></i>
                                        Full Name
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" id="register_name" name="name" class="form-input"
                                            placeholder="Enter user's full name" required>
                                        <i class="fas fa-user input-icon"></i>
                                    </div>
                                    <div id="register-name-error" class="field-error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="register_email" class="form-label">
                                        <i class="fas fa-envelope"></i>
                                        Email Address
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="email" id="register_email" name="email" class="form-input"
                                            placeholder="Enter user's email address" required>
                                        <i class="fas fa-envelope input-icon"></i>
                                    </div>
                                    <div id="register-email-error" class="field-error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="register_password" class="form-label">
                                        <i class="fas fa-lock"></i>
                                        Password
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="password" id="register_password" name="password" class="form-input"
                                            placeholder="Enter user's password" required>
                                        <i class="fas fa-lock input-icon"></i>
                                    </div>
                                    <div id="register-password-error" class="field-error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="register_role" class="form-label">
                                        <i class="fas fa-user-tag"></i>
                                        Role
                                    </label>
                                    <div class="input-wrapper">
                                        <select id="register_role" name="role" class="form-input" required>
                                            <option value="">Select a role</option>
                                            <option value="student">Student</option>
                                            <option value="teacher">Teacher</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                        <i class="fas fa-user-tag input-icon"></i>
                                    </div>
                                    <div id="register-role-error" class="field-error"></div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success btn-full">
                                        <span class="btn-text">
                                            <i class="fas fa-user-plus"></i>
                                            Register User
                                        </span>
                                        <div class="spinner"></div>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="delete-section" class="content-section">
                        <div class="section-header">
                            <div class="section-icon danger">
                                <i class="fas fa-user-minus"></i>
                            </div>
                            <div class="section-title">
                                <h3 class="heading-3">Delete User</h3>
                                <p class="text-secondary">Remove a user account by User ID</p>
                            </div>
                        </div>

                        <div class="section-body">
                            <div id="delete-success" class="alert alert-success" style="display: none;"></div>
                            <div id="delete-error" class="alert alert-error" style="display: none;"></div>

                            <form id="delete-form" novalidate>
                                <div class="form-group">
                                    <label for="delete_user_id" class="form-label">
                                        <i class="fas fa-hashtag"></i>
                                        User ID
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="number" id="delete_user_id" name="user_id" class="form-input"
                                            placeholder="Enter user ID to delete" required min="1">
                                        <i class="fas fa-hashtag input-icon"></i>
                                    </div>
                                    <div id="delete-user-id-error" class="field-error"></div>
                                </div>

                                <div class="delete-warning">
                                    <div class="warning-icon">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="warning-text">
                                        <h4>Warning!</h4>
                                        <p>This action cannot be undone. The user account and all associated data will
                                            be permanently deleted.</p>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-danger btn-full">
                                        <span class="btn-text">
                                            <i class="fas fa-trash-alt"></i>
                                            Delete User
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

            let currentUserId = null;

            if (!token) {
                window.location.href = '{{ url("login") }}';
                return;
            }

            async function getCurrentUser() {
                try {
                    const response = await fetch('{{ url("api/profile") }}', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        currentUserId = data.data.id;
                    }
                } catch (error) {
                    console.error('Error getting current user:', error);
                }
            }

            const loadingState = document.getElementById('loading-state');
            const accountContent = document.getElementById('account-content');

            const registerForm = document.getElementById('register-form');
            const deleteForm = document.getElementById('delete-form');


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

            async function loadUsers() {
                const tableBody = document.getElementById('users-table-body');
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="loading-row">
                            <div class="table-spinner"></div>
                            Loading users...
                        </td>
                    </tr>
                `;

                try {
                    const response = await fetch('http://localhost/exam-platform/public/api/admin/users', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        const users = data.data;

                        if (users.length === 0) {
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="6" class="empty-row">No users found</td>
                                </tr>
                            `;
                        } else {
                            tableBody.innerHTML = users.map(user => `
                            <tr>
                                <td>${user.id}</td>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>
                                    <span class="role-badge role-${user.role}">${user.role}</span>
                                </td>
                                <td>${new Date(user.created_at).toLocaleDateString()}</td>
                                <td>
                                    ${user.id === currentUserId ?
                                    '<span class="text-muted"><i class="fas fa-shield-alt"></i> Current User</span>' :
                                    `<button class="btn-mini btn-danger" onclick="quickDelete(${user.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>`
                                }
                                </td>
                            </tr>
                        `).join('');
                        }

                        loadingState.style.display = 'none';
                        accountContent.style.display = 'block';
                    } else {
                        throw new Error('Failed to load users');
                    }
                } catch (error) {
                    console.error('Error loading users:', error);
                    showAlert('list-error', 'Failed to load users. Please try again.', true);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="error-row">Error loading users</td>
                        </tr>
                    `;
                }
            }

            window.quickDelete = function (userId) {
                if (userId === currentUserId) {
                    alert('You cannot delete your own account!');
                    return;
                }
                if (confirm('Are you sure you want to delete this user?')) {
                    deleteUser(userId);
                }
            };

            document.getElementById('refresh-users').addEventListener('click', loadUsers);

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

            registerForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                clearFieldErrors(this);
                const submitButton = this.querySelector('.btn');

                const name = document.getElementById('register_name').value.trim();
                const email = document.getElementById('register_email').value.trim();
                const password = document.getElementById('register_password').value;
                const role = document.getElementById('register_role').value;

                let hasErrors = false;
                if (!name) {
                    showFieldError('register-name-error', 'Name is required');
                    hasErrors = true;
                }
                if (!email) {
                    showFieldError('register-email-error', 'Email is required');
                    hasErrors = true;
                } else if (!isValidEmail(email)) {
                    showFieldError('register-email-error', 'Please enter a valid email address');
                    hasErrors = true;
                }
                if (!password) {
                    showFieldError('register-password-error', 'Password is required');
                    hasErrors = true;
                }
                if (!role) {
                    showFieldError('register-role-error', 'Role is required');
                    hasErrors = true;
                }

                if (hasErrors) return;

                submitButton.classList.add('loading');
                submitButton.disabled = true;

                try {
                    const response = await fetch('http://localhost/exam-platform/public/api/admin/users', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ name, email, password, role })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showAlert('register-success', 'User registered successfully!');
                        registerForm.reset();
                        loadUsers();
                    } else {
                        let message = data.message || 'Failed to register user';
                        if (data.data && data.data.errors) {
                            const errors = data.data.errors;
                            Object.keys(errors).forEach(field => {
                                const fieldName = field.replace('_', '-');
                                showFieldError(`register-${fieldName}-error`, errors[field][0]);
                            });
                        } else {
                            showAlert('register-error', message, true);
                        }
                    }
                } catch (error) {
                    showAlert('register-error', 'Network error, please try again later.', true);
                } finally {
                    submitButton.classList.remove('loading');
                    submitButton.disabled = false;
                }
            });

            deleteForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const userId = document.getElementById('delete_user_id').value;
                if (!userId) {
                    showFieldError('delete-user-id-error', 'User ID is required');
                    return;
                }

                if (!confirm('Are you absolutely sure you want to delete this user? This action cannot be undone.')) {
                    return;
                }

                await deleteUser(userId);
            });

            async function deleteUser(userId) {
                if (parseInt(userId) === currentUserId) {
                    showAlert('delete-error', 'You cannot delete your own account!', true);
                    return;
                }
                const submitButton = deleteForm.querySelector('.btn');
                clearFieldErrors(deleteForm);

                submitButton.classList.add('loading');
                submitButton.disabled = true;

                try {
                    const response = await fetch(`http://localhost/exam-platform/public/api/admin/users/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showAlert('delete-success', 'User deleted successfully!');
                        document.getElementById('delete_user_id').value = '';
                        loadUsers();
                    } else {
                        let message = data.message || 'Failed to delete user';
                        showAlert('delete-error', message, true);
                    }
                } catch (error) {
                    showAlert('delete-error', 'Network error, please try again later.', true);
                } finally {
                    submitButton.classList.remove('loading');
                    submitButton.disabled = false;
                }
            }

            getCurrentUser().then(() => {
                loadUsers();
            });
        });
    </script>
</body>

</html>