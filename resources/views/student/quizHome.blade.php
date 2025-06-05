<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Quizzes | Quiz Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quiz-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/my-assignment.css') }}">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('profile') }}">
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
            <div class="page-header">
                <h1 class="page-title">My Quizzes</h1>
                <p class="page-subtitle">View and complete your assigned quizzes</p>
            </div>

            <div id="loading" class="loading-state">
                <div class="spinner-lg"></div>
                <p class="text-secondary">Loading your assignments...</p>
            </div>

            <div id="error" class="alert alert-error" style="display: none;"></div>

            <div id="assignments-container" style="display: none;">
                <div id="assignments-grid" class="assignments-grid"></div>
            </div>

            <div id="empty-state" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>No Assignments Yet</h3>
                <p>You don't have any assignments assigned to you yet.</p>
            </div>
        </div>
    </main>

    <script>
        const loginUrl = '{{ route("login") }}';
        const profileUrl = '{{ route(name: "profile") }}';
        const assignmentsApiUrl = '{{ url("api/student/assignments") }}';
        const attemptsApiUrl = '{{ url("api/attempts/my") }}';
        const attemptDetailUrl = '{{ url("attempts") }}';
        const startAssignmentUrl = '{{ route("assignment.show", "") }}';
        let assignments = [];
        let attempts = [];

        document.getElementById('logout-btn').addEventListener('click', function () {
            localStorage.removeItem('token');
            sessionStorage.removeItem('quiz_access_token');
            window.location.href = loginUrl;
        });

        async function fetchData() {
            const token = localStorage.getItem('token');
            const loadingDiv = document.getElementById('loading');
            const errorDiv = document.getElementById('error');
            const assignmentsContainer = document.getElementById('assignments-container');
            const emptyState = document.getElementById('empty-state');

            if (!token) {
                window.location.href = loginUrl;
                return;
            }

            try {
                const headers = {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                };

                const [assignmentsResponse, attemptsResponse] = await Promise.all([
                    fetch(assignmentsApiUrl, { headers }),
                    fetch(attemptsApiUrl, { headers })
                ]);

                if (!assignmentsResponse.ok) {
                    handleError(`HTTP error! status: ${assignmentsResponse.status}`, assignmentsResponse.status);
                    return;
                }

                if (!attemptsResponse.ok) {
                    handleError(`HTTP error! status: ${assignmentsResponse.status}`, assignmentsResponse.status);
                    return;
                }

                const assignmentsData = await assignmentsResponse.json();
                const attemptsData = await attemptsResponse.json();

                if (assignmentsData.success) {
                    assignments = assignmentsData.data;
                }

                if (attemptsData.success) {
                    attempts = attemptsData.data;
                }

                loadingDiv.style.display = 'none';

                if (assignments.length === 0) {
                    emptyState.style.display = 'block';
                } else {
                    renderAssignments();
                    assignmentsContainer.style.display = 'block';
                    assignmentsContainer.classList.add('fade-in');
                }

            } catch (error) {
                console.error('Error fetching data:', error);
                handleError(error.message);
            }
        }

        function handleError(message, status = null) {
            const loadingDiv = document.getElementById('loading');
            const errorDiv = document.getElementById('error');

            loadingDiv.style.display = 'none';
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            errorDiv.classList.add('show');

            if (status === 401 || status === 403) {
                setTimeout(() => {
                    sessionStorage.removeItem('quiz_access_token');
                    window.location.href = profileUrl;
                }, 1000);
            }
        }

        function getDueDateStatus(dueDate) {
            const now = new Date();
            const due = new Date(dueDate);
            const diffTime = due - now;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays < 0) {
                return { status: 'overdue', text: 'Overdue', class: 'overdue' };
            } else if (diffDays <= 7) {
                return { status: 'warning', text: `Due in ${diffDays} day${diffDays !== 1 ? 's' : ''}`, class: 'warning' };
            } else {
                return { status: 'upcoming', text: `Due ${due.toLocaleDateString()}`, class: 'upcoming' };
            }
        }

        function getAssignmentAttempts(assignmentId) {
            return attempts.filter(attempt => attempt.assignment_id === assignmentId);
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function renderAssignments() {
            const grid = document.getElementById('assignments-grid');
            grid.innerHTML = '';

            assignments.forEach(assignment => {
                const dueDateInfo = getDueDateStatus(assignment.due_date);
                const assignmentAttempts = getAssignmentAttempts(assignment.id);
                const canStartQuiz = dueDateInfo.status !== 'overdue';

                const card = document.createElement('div');
                card.className = 'assignment-card';
                card.innerHTML = `
                    <div class="assignment-header" onclick="toggleAssignment(${assignment.id})">
                        <div class="assignment-info">
                            <div>
                                <h3 class="assignment-title">${assignment.title}</h3>
                            </div>
                            <div class="assignment-meta">
                                <div class="due-date-badge ${dueDateInfo.class}">
                                    <i class="fas fa-calendar-alt"></i>
                                    ${dueDateInfo.text}
                                 </div>
                            </div>
                        </div>
                    </div>
                    <div class="assignment-details">
                        <div class="assignment-description">
                            <strong>Description:</strong><br>
                            ${assignment.description || 'No description provided.'}
                        </div>
                        <div class="attempts-section">
                            <h4 class="section-title">Previous Attempts</h4>
                            ${assignmentAttempts.length > 0 ? `
                                <div class="attempts-grid">
                                    ${assignmentAttempts.map((attempt, index) => `
                                        <div class="attempt-card" onclick="viewAttemptDetail(${attempt.attempt_id})">
                                            <div class="attempt-title">Attempt ${index + 1}</div>
                                            <div class="attempt-score">${attempt.score}%</div>
                                            <div class="attempt-date">${formatDate(attempt.submitted_at)}</div>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : `
                                <div class="no-attempts">
                                    <i class="fas fa-clipboard"></i>
                                    <p>No attempts yet</p>
                                </div>
                            `}
                        </div>
                        <div class="assignment-actions">
                            ${canStartQuiz ? `
                                <button class="btn btn-primary" onclick="startAssignment(${assignment.id})">
                                    <i class="fas fa-play"></i>
                                    Start Quiz
                                </button>
                            ` : `
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-clock"></i>
                                    Assignment Overdue
                                </button>
                            `}
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function toggleAssignment(assignmentId) {
            const cards = document.querySelectorAll('.assignment-card');
            cards.forEach(card => {
                const header = card.querySelector('.assignment-header');
                const isCurrentCard = header.getAttribute('onclick').includes(assignmentId.toString());

                if (isCurrentCard) {
                    card.classList.toggle('expanded');
                }
            });
        }

        function startAssignment(assignmentId) {
            const accessToken = btoa(Date.now() + '_' + Math.random() + '_' + assignmentId);

            sessionStorage.setItem('quiz_access_token', accessToken);
            sessionStorage.setItem('quiz_assignment_id', assignmentId);
            sessionStorage.setItem('quiz_access_time', Date.now());

            window.location.href = `${startAssignmentUrl}/${assignmentId}`;
        }

        function viewAttemptDetail(attemptId) {
            window.location.href = `${attemptDetailUrl}/${attemptId}`;
        }

        document.addEventListener('DOMContentLoaded', fetchData);
    </script>
</body>

</html>