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
                        <p>Manage accounts and profile settings</p>
                    </div>
                </div>
                <div class="dashboard-actions">
                    <div class="action-card">
                        <div class="action-icon primary">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="action-content">
                            <h4>Account Management</h4>
                            <p>Manage user accounts and permissions</p>
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('accManage') }}'">
                                <i class="fas fa-users-cog"></i>
                                Manage User
                            </button>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon success">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="action-content">
                            <h4>Manage Profile</h4>
                            <p>Update your personal information and password</p>
                            <button class="btn btn-success btn-sm" onclick="window.location.href='{{ route('editProfile') }}'">
                                <i class="fas fa-user-edit"></i>
                                Manage Profile
                            </button>
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
                        <p>Create and manage quizzes</p>
                    </div>
                </div>
                <div class="dashboard-actions">
                    <div class="action-card">
                        <div class="action-icon primary">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="action-content">
                            <h4>Manage Quiz</h4>
                            <p>Create, edit and manage quiz content</p>
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('quizManage') }}'">
                                <i class="fas fa-question-circle"></i>
                                Manage Quiz
                            </button>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon success">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="action-content">
                            <h4>Manage Profile</h4>
                            <p>Update your personal information</p>
                            <button class="btn btn-success btn-sm" onclick="window.location.href='{{ route('editProfile') }}'">
                                <i class="fas fa-user-edit"></i>
                                Manage Profile
                            </button>
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
                        <p>Access your quizzes and track progress</p>
                    </div>
                </div>
                <div class="dashboard-actions">
                    <div class="action-card">
                        <div class="action-icon primary">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="action-content">
                            <h4>Available Quizzes</h4>
                            <p>View and complete your quizzes</p>
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('quizHome') }}'">
                                <i class="fas fa-play"></i>
                                Start Quiz
                            </button>
                        </div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon success">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="action-content">
                            <h4>Manage Profile</h4>
                            <p>Update your personal information</p>
                            <button class="btn btn-success btn-sm" onclick="window.location.href='{{ route('editProfile') }}'">
                                <i class="fas fa-user-edit"></i>
                                Manage Profile
                            </button>
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