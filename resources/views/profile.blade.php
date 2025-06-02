<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | Quiz Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quiz-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile-styles.css') }}">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap"></i>
                Quiz Platform
            </a>
            <button id="logout-btn" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i>
                Log Out
            </button>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div id="loading" class="loading-state">
                <div class="spinner-lg"></div>
                <p class="text-secondary">Loading your profile...</p>
            </div>

            <div id="error" class="alert alert-error" style="display: none;"></div>

            <div id="profile-content" class="profile-content" style="display: none;">
                <div class="profile-header-card">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="profile-info">
                        <h1 id="user-name" class="profile-name"></h1>
                        <div class="profile-meta">
                            <span id="user-role-badge" class="badge badge-primary"></span>
                            <span class="profile-email">
                                <i class="fas fa-envelope"></i>
                                <span id="user-email"></span>
                            </span>
                        </div>
                        <p class="profile-welcome">Welcome to your dashboard</p>
                    </div>
                </div>

                <div id="dashboard-content" class="dashboard-content"></div>
            </div>
        </div>
    </main>

    <script>
        const loginUrl = '{{ route("login") }}';
        const apiProfileUrl = '{{ url("api/profile") }}';

        document.getElementById('logout-btn').addEventListener('click', function () {
            localStorage.removeItem('token');
            window.location.href = loginUrl;
        });

        (async function fetchProfile() {
            const token = localStorage.getItem('token');
            const loadingDiv = document.getElementById('loading');
            const errorDiv = document.getElementById('error');
            const profileDiv = document.getElementById('profile-content');

            if (!token) {
                window.location.href = loginUrl;
                return;
            }

            try {
                const response = await fetch(apiProfileUrl, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    const user = data.data;

                    document.getElementById('user-name').textContent = user.name;
                    document.getElementById('user-email').textContent = user.email;

                    const roleBadge = document.getElementById('user-role-badge');
                    roleBadge.textContent = user.role.toUpperCase();

                    if (user.role === 'admin') {
                        roleBadge.className = 'badge badge-error';
                    } else if (user.role === 'teacher') {
                        roleBadge.className = 'badge badge-primary';
                    } else {
                        roleBadge.className = 'badge badge-success';
                    }

                    const dashboardDiv = document.getElementById('dashboard-content');
                    dashboardDiv.innerHTML = createDashboard(user.role);

                    loadingDiv.style.display = 'none';
                    profileDiv.style.display = 'block';
                    profileDiv.classList.add('fade-in');

                } else {
                    handleError(data.message || 'Failed to load profile', response.status);
                }
            } catch (err) {
                handleError('Network error. Please check your connection.');
            }

            function handleError(message, status = null) {
                loadingDiv.style.display = 'none';
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                errorDiv.classList.add('show');

                if (status === 401 || status === 403) {
                    setTimeout(() => {
                        localStorage.removeItem('token');
                        window.location.href = loginUrl;
                    }, 2000);
                }
            }

            function createDashboard(role) {
                const dashboards = {
                    admin: `
                        <div class="dashboard-card">
                            <div class="dashboard-header">
                                <div class="dashboard-icon admin-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="dashboard-title">
                                    <h3>Admin Dashboard</h3>
                                    <p>Manage users and system settings</p>
                                </div>
                            </div>
                            <div class="dashboard-actions">
                                <div class="action-card">
                                    <div class="action-icon primary">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>User Management</h4>
                                        <p>Add, edit, and manage user accounts</p>
                                        <button class="btn btn-primary btn-sm">Manage Users</button>
                                    </div>
                                </div>
                                <div class="action-card">
                                    <div class="action-icon secondary">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>System Settings</h4>
                                        <p>Configure platform settings and preferences</p>
                                        <button class="btn btn-secondary btn-sm">Settings</button>
                                    </div>
                                </div>
                                <div class="action-card">
                                    <div class="action-icon success">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>Analytics</h4>
                                        <p>View system usage and performance metrics</p>
                                        <button class="btn btn-success btn-sm">View Reports</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    teacher: `
                        <div class="dashboard-card">
                            <div class="dashboard-header">
                                <div class="dashboard-icon teacher-icon">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="dashboard-title">
                                    <h3>Teacher Dashboard</h3>
                                    <p>Create and manage assignments</p>
                                </div>
                            </div>
                            <div class="dashboard-actions">
                                <div class="action-card">
                                    <div class="action-icon success">
                                        <i class="fas fa-plus-circle"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>Create Assignment</h4>
                                        <p>Design new quizzes and assessments</p>
                                        <button class="btn btn-success btn-sm">New Assignment</button>
                                    </div>
                                </div>
                                <div class="action-card">
                                    <div class="action-icon primary">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>My Assignments</h4>
                                        <p>View and edit existing assignments</p>
                                        <button class="btn btn-primary btn-sm">View All</button>
                                    </div>
                                </div>
                                <div class="action-card">
                                    <div class="action-icon warning">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>Student Progress</h4>
                                        <p>Review submissions and provide feedback</p>
                                        <button class="btn btn-outline btn-sm">Review Work</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    student: `
                        <div class="dashboard-card">
                            <div class="dashboard-header">
                                <div class="dashboard-icon student-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="dashboard-title">
                                    <h3>Student Portal</h3>
                                    <p>Access your assignments and track progress</p>
                                </div>
                            </div>
                            <div class="dashboard-actions">
                                <div class="action-card">
                                    <div class="action-icon primary">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>Available Assignments</h4>
                                        <p>View and complete your assignments</p>
                                        <button class="btn btn-primary btn-sm">Start Assignment</button>
                                    </div>
                                </div>
                                <div class="action-card">
                                    <div class="action-icon success">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>My Progress</h4>
                                        <p>Track your performance and grades</p>
                                        <button class="btn btn-success btn-sm">View Progress</button>
                                    </div>
                                </div>
                                <div class="action-card">
                                    <div class="action-icon warning">
                                        <i class="fas fa-comments"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>Feedback</h4>
                                        <p>Read teacher feedback on your work</p>
                                        <button class="btn btn-outline btn-sm">View Feedback</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `
                };
                return dashboards[role] || '';
            }
        })();
    </script>
</body>

</html>