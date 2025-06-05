<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Quizzes | Quiz Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quiz-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/quizManage.css') }}">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('profile') }}">
                <i class="fas fa-graduation-cap"></i>
                Quiz Platform - Teacher
            </a>
            <button id="logout-btn" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i>
                Log Out
            </button>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="page-header" id="main-page-header">
                <div class="header-content">
                    <div>
                        <h1 class="page-title">Quiz Management</h1>
                        <p class="page-subtitle">Create and manage your quizzes</p>
                    </div>
                    <button id="create-assignment-btn" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i>
                        Create New Quiz
                    </button>
                </div>
            </div>

            <div id="loading" class="loading-state">
                <div class="spinner-lg"></div>
                <p class="text-secondary">Loading quizzes...</p>
            </div>

            <div id="error" class="alert alert-error" style="display: none;"></div>

            <div id="assignments-list" class="assignments-container" style="display: none;">
                <div id="assignments-grid" class="assignments-grid"></div>
            </div>

            <div id="assignment-detail" class="assignment-detail-container" style="display: none;">
                <div class="detail-header">
                    <button id="back-to-list" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Quizzes
                    </button>
                    <div class="detail-actions">
                        <button id="edit-assignment-btn" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                        <button id="delete-assignment-btn" class="btn btn-outline-danger">
                            <i class="fas fa-trash"></i>
                            Delete
                        </button>
                        <button id="view-questions-btn" class="btn btn-primary">
                            <i class="fas fa-question-circle"></i>
                            View Questions
                        </button>
                    </div>
                </div>

                <div id="assignment-info" class="assignment-info-card"></div>

                <div class="assign-student-section">
                    <h3 class="section-title">Assign Student</h3>
                    <div class="assign-form">
                        <input type="email" id="student-email" placeholder="Enter student email" class="form-input">
                        <button id="assign-student-btn" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                            Assign Student
                        </button>
                    </div>
                </div>

                <div class="students-section">
                    <h3 class="section-title">Assigned Students</h3>
                    <div id="students-list" class="students-grid"></div>
                </div>
            </div>

            <div id="empty-state" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>No Quizzes Yet</h3>
                <p>Create your first quiz to get started.</p>
                <button class="btn btn-primary" onclick="showCreateForm()">
                    <i class="fas fa-plus"></i>
                    Create Quiz
                </button>
            </div>
        </div>
    </main>

    <div id="create-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create New Quiz</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="create-assignment-form">
                <div class="form-group">
                    <label for="assignment-title">Title *</label>
                    <input type="text" id="assignment-title" name="title" required class="form-input">
                </div>
                <div class="form-group">
                    <label for="assignment-description">Description</label>
                    <textarea id="assignment-description" name="description" rows="4" class="form-input"></textarea>
                </div>
                <div class="form-group">
                    <label for="assignment-due-date">Due Date *</label>
                    <input type="date" id="assignment-due-date" name="due_date" required class="form-input">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="hideCreateModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Quiz</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="edit-assignment-form">
                <div class="form-group">
                    <label for="edit-assignment-title">Title *</label>
                    <input type="text" id="edit-assignment-title" name="title" required class="form-input">
                </div>
                <div class="form-group">
                    <label for="edit-assignment-description">Description</label>
                    <textarea id="edit-assignment-description" name="description" rows="4"
                        class="form-input"></textarea>
                </div>
                <div class="form-group">
                    <label for="edit-assignment-due-date">Due Date *</label>
                    <input type="date" id="edit-assignment-due-date" name="due_date" required class="form-input">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="hideEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let assignmentDetails = new Map();
        let currentFeedbackStudentId = null;
        let currentFeedbackData = null;
        const loginUrl = '{{ route("login") }}';
        const profileUrl = '{{ route("profile") }}';
        const createAssignmentUrl = '{{ url("api/teacher/assignments") }}';
        const getAssignmentResultsUrl = '{{ url("api/teacher/assignments/init") }}';
        const feedbackUrl = '{{ url("api/teacher/feedback") }}';
        const questionsUrl = '{{ url("viewQuestions") }}';
        
        let assignments = [];
        let currentAssignment = null;
        let currentAssignmentData = null;

        document.addEventListener('DOMContentLoaded', function () {
            loadAssignments();
            setupEventListeners();
        });

        function setupEventListeners() {
            document.getElementById('logout-btn').addEventListener('click', logout);
            document.getElementById('create-assignment-btn').addEventListener('click', showCreateModal);
            document.getElementById('back-to-list').addEventListener('click', showAssignmentsList);
            document.getElementById('create-assignment-form').addEventListener('submit', createAssignment);
            document.getElementById('edit-assignment-form').addEventListener('submit', updateAssignment);
            document.getElementById('assign-student-btn').addEventListener('click', assignStudent);
            document.getElementById('edit-assignment-btn').addEventListener('click', showEditModal);
            document.getElementById('delete-assignment-btn').addEventListener('click', deleteAssignment);
            document.getElementById('view-questions-btn').addEventListener('click', viewQuestions);
            document.getElementById('feedback-edit-btn').addEventListener('click', editFeedback);
            document.getElementById('feedback-submit-btn').addEventListener('click', handleSubmitFeedback);
            document.getElementById('feedback-update-btn').addEventListener('click', handleUpdateFeedback);
            document.getElementById('feedback-cancel-btn').addEventListener('click', cancelFeedbackEdit);
            document.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', function () {
                    this.closest('.modal').style.display = 'none';
                });
            });

            window.addEventListener('click', function (e) {
                if (e.target.classList.contains('modal')) {
                    e.target.style.display = 'none';
                }
            });
        }

        function logout() {
            localStorage.removeItem('token');
            sessionStorage.removeItem('quiz_access_token');
            window.location.href = loginUrl;
        }

        async function loadAssignments() {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = loginUrl;
                return;
            }

            try {
                showLoading();

                const response = await fetch(getAssignmentResultsUrl, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    handleError(`HTTP error! status: ${response.status}`, response.status);
                    return;
                }

                const data = await response.json();
                if (!data.success) {
                    handleError(data.message || 'Failed to load quiz');
                    return;
                }

                const assignmentsData = data.data || [];
                assignments = [];
                assignmentDetails.clear();

                assignmentsData.forEach(item => {
                    if (item.assignment) {
                        assignments.push(item.assignment);
                        assignmentDetails.set(item.assignment.id, item);
                    }
                });

                for (let assignment of assignments) {
                    const statusData = await loadAssignmentStatus(assignment.id);
                    if (statusData) {
                        const existingData = assignmentDetails.get(assignment.id);
                        assignmentDetails.set(assignment.id, {
                            ...existingData,
                            status_data: statusData
                        });
                    }
                }

                displayAssignments();
            } catch (error) {
                console.error('Error loading quiz:', error);
                handleError('Failed to load quiz: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        function handleError(message, status = null) {
            const loadingDiv = document.getElementById('loading');
            const errorDiv = document.getElementById('error');
            const assignmentsContainer = document.getElementById('assignments-list');
            const emptyState = document.getElementById('empty-state');
            const mainPageHeader = document.getElementById('main-page-header');

            loadingDiv.style.display = 'none';
            assignmentsContainer.style.display = 'none';
            emptyState.style.display = 'none';
            mainPageHeader.style.display = 'none';

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

        function displayAssignments() {
            const container = document.getElementById('assignments-list');
            const grid = document.getElementById('assignments-grid');
            const emptyState = document.getElementById('empty-state');

            if (assignments.length === 0) {
                container.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }

            emptyState.style.display = 'none';
            container.style.display = 'block';

            const sortedAssignments = [...assignments].sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));

            grid.innerHTML = sortedAssignments.map(assignment => {
                const assignmentData = assignmentDetails.get(assignment.id);

                let averageScoreDisplay = '-';
                if (assignmentData && assignmentData.attempts && assignmentData.attempts.length > 0) {
                    const currentAttempts = assignmentData.attempts.filter(attempt =>
                        attempt.assignment_id === assignment.id
                    );
                    if (currentAttempts.length > 0) {
                        const avgScore = calculateAverageScore(currentAttempts);
                        if (avgScore !== null) {
                            averageScoreDisplay = `${avgScore}%`;
                        }
                    }
                }

                const stats = getCompletionStats(assignmentData);
                const completionDisplay = `${stats.completed}/${stats.total}`;

                return `
            <div class="assignment-card" onclick="showAssignmentDetail(${assignment.id})">
                <div class="assignment-header">
                    <h3 class="assignment-title">${assignment.title}</h3>
                    <div class="assignment-meta">
                        <div class="created-date">
                            <i class="fas fa-calendar-plus"></i>
                            Created: ${formatDate(assignment.created_at)}
                        </div>
                        <div class="due-date">
                            <i class="fas fa-calendar-alt"></i>
                            Due: ${formatDate(assignment.due_date)}
                        </div>
                    </div>
                </div>
                ${(!assignment.has_questions) ? `
                    <div class="assignment-stats">
                         <div class="stat no-questions-notice">
                            <i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i>
                            <span style="color: #f39c12;">No questions</span>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
            }).join('');
        }

        function showAssignmentDetail(assignmentId) {
            const assignment = assignments.find(a => a.id === assignmentId);
            if (!assignment) return;

            currentAssignment = null;
            currentAssignmentData = null;

            currentAssignment = assignment;
            currentAssignmentData = assignmentDetails.get(assignmentId);

            displayAssignmentDetail();
        }

        function displayAssignmentDetail() {
            const listContainer = document.getElementById('assignments-list');
            const detailContainer = document.getElementById('assignment-detail');
            const infoContainer = document.getElementById('assignment-info');
            const studentsContainer = document.getElementById('students-list');
            const mainPageHeader = document.getElementById('main-page-header');

            listContainer.style.display = 'none';
            detailContainer.style.display = 'block';
            mainPageHeader.style.display = 'none';

            studentsContainer.innerHTML = '';

            let averageScoreDisplay = '-';
            if (currentAssignmentData && currentAssignmentData.attempts && currentAssignmentData.attempts.length > 0) {
                const currentAttempts = currentAssignmentData.attempts.filter(attempt =>
                    attempt.assignment_id === currentAssignment.id
                );
                if (currentAttempts.length > 0) {
                    const avgScore = calculateAverageScore(currentAttempts);
                    if (avgScore !== null) {
                        averageScoreDisplay = `${avgScore}/100`;
                    }
                }
            }

            const stats = getCompletionStats(currentAssignmentData);
            const completionDisplay = `${stats.completed}/${stats.total}`;

            infoContainer.innerHTML = `
    <div class="info-grid">
        <div class="info-item">
            <label>Title:</label>
            <span>${currentAssignment.title}</span>
        </div>
        <div class="info-item">
            <label>Created Date:</label>
            <span>${formatDateDDMMYYYY(currentAssignment.created_at)}</span>
        </div>
        <div class="info-item">
            <label>Due Date:</label>
            <span>${formatDateDDMMYYYY(currentAssignment.due_date)}</span>
        </div>
        <div class="info-item">
            <label>Description:</label>
            <span>${currentAssignment.description || 'No description provided'}</span>
        </div>
        <div class="info-item">
            <label>Students Completed:</label>
            <span>${completionDisplay}</span>
        </div>
        <div class="info-item">
            <label>Average Score:</label>
            <span>${averageScoreDisplay}</span>
        </div>
    </div>
`;

            if (currentAssignmentData && currentAssignmentData.status_data && currentAssignmentData.status_data.data) {
                studentsContainer.innerHTML = currentAssignmentData.status_data.data.map(studentData => {
                    const hasFeedback = currentAssignmentData.feedbacks &&
                        currentAssignmentData.feedbacks.some(f => f.student_id === studentData.student_id && f.assignment_id === currentAssignment.id);

                    const studentAttempts = currentAssignmentData.attempts ?
                        currentAssignmentData.attempts.filter(attempt =>
                            attempt.student_id === studentData.student_id &&
                            attempt.assignment_id === currentAssignment.id
                        ) : [];

                    return `
                <div class="student-container">
                    <div class="student-header">
                        <div class="student-info">
                            <h4>${studentData.name}</h4>
                            <p>${studentData.email}</p>
                        </div>
                        <div class="student-status">
                            <span class="status-badge ${studentData.completed ? 'completed' : 'pending'}">
                                ${studentData.completed ? 'Completed' : 'Not Attempted'}
                            </span>
                            <span class="feedback-badge ${hasFeedback ? 'given' : 'pending'}">
                                ${hasFeedback ? 'Feedback Given' : 'No Feedback'}
                            </span>
                        </div>
                    </div>
                    
                    ${studentAttempts.length > 0 ? `
                        <div class="attempts-section">
                            <h5>Attempts:</h5>
                            <div class="attempts-list">
                                ${studentAttempts.map((attempt, index) => `
                                    <div class="attempt-item">
                                        <span class="attempt-number">Attempt ${index + 1}</span>
                                        <span class="attempt-score">${attempt.score}%</span>
                                        <span class="attempt-date">${formatDateDDMMYYYY(attempt.created_at)}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    
                    <div class="student-actions">
                        <button class="btn btn-primary btn-sm" onclick="submitFeedback(${studentData.student_id})">
                        <i class="fas fa-comment"></i>
                        ${hasFeedback ? 'View' : 'Submit'} Feedback
                        </button>
                    </div>
                </div>
            `;
                }).join('');
            } else {
                studentsContainer.innerHTML = `
            <div class="no-students">
                <i class="fas fa-user-slash"></i>
                <p>No students assigned to this quiz yet.</p>
            </div>
        `;
            }
        }

        function showAssignmentsList() {
            document.getElementById('assignments-list').style.display = 'block';
            document.getElementById('assignment-detail').style.display = 'none';
            document.getElementById('empty-state').style.display = 'none';
            document.getElementById('main-page-header').style.display = 'block';
            currentAssignment = null;
            currentAssignmentData = null;
        }

        function showCreateModal() {
            document.getElementById('create-modal').style.display = 'block';
        }

        function hideCreateModal() {
            document.getElementById('create-modal').style.display = 'none';
            document.getElementById('create-assignment-form').reset();
        }

        function showEditModal() {
            if (!currentAssignment) return;

            document.getElementById('edit-assignment-title').value = currentAssignment.title;
            document.getElementById('edit-assignment-description').value = currentAssignment.description || '';
            document.getElementById('edit-assignment-due-date').value = currentAssignment.due_date;
            document.getElementById('edit-modal').style.display = 'block';
        }

        function hideEditModal() {
            document.getElementById('edit-modal').style.display = 'none';
            document.getElementById('edit-assignment-form').reset();
        }

        async function createAssignment(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            try {
                const token = localStorage.getItem('token');
                const response = await fetch(createAssignmentUrl, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.success) {
                    hideCreateModal();
                    showSuccess('Quiz created successfully!');
                    loadAssignments();
                } else {
                    throw new Error(result.message || 'Failed to create quiz');
                }
            } catch (error) {
                console.error('Error creating quiz:', error);
                showError('Failed to create quiz: ' + error.message);
            }
        }

        async function updateAssignment(e) {
            e.preventDefault();
            if (!currentAssignment) return;

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            try {
                const token = localStorage.getItem('token');
                const response = await fetch(`${createAssignmentUrl}/${currentAssignment.id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.success) {
                    hideEditModal();
                    showSuccess('Quiz updated successfully!');
                    showAssignmentsList();
                    loadAssignments();
                } else {
                    throw new Error(result.message || 'Failed to update quiz');
                }
            } catch (error) {
                console.error('Error updating quiz:', error);
                showError('Failed to update quiz: ' + error.message);
            }
        }

        function calculateAverageScore(attempts) {
            if (!attempts || attempts.length === 0) {
                return null;
            }

            const studentScores = new Map();

            attempts.forEach(attempt => {
                const studentId = attempt.student_id;
                const score = parseFloat(attempt.score);
                if (!studentScores.has(studentId) || studentScores.get(studentId) < score) {
                    studentScores.set(studentId, score);
                }
            });

            const scores = Array.from(studentScores.values());
            const average = scores.reduce((sum, score) => sum + parseFloat(score), 0) / scores.length;

            return Math.round(average * 100) / 100;
        }

        function getCompletionStats(assignmentData) {
            if (!assignmentData || !assignmentData.status_data) {
                return { completed: 0, total: 0 };
            }

            return {
                completed: assignmentData.status_data.total_done,
                total: assignmentData.status_data.total_students
            };
        }
        async function loadAssignmentStatus(assignmentId) {
            const token = localStorage.getItem('token');
            if (!token) return null;

            try {
                const response = await fetch(`${createAssignmentUrl}/${assignmentId}/status`, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.success) {
                    return result.data;
                }
                return null;
            } catch (error) {
                console.error('Error loading assignment status:', error);
                return null;
            }
        }

        async function deleteAssignment() {
            if (!currentAssignment) return;

            if (confirm('Are you sure you want to delete this quiz?')) {
                try {
                    const token = localStorage.getItem('token');
                    const response = await fetch(`${createAssignmentUrl}/${currentAssignment.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    if (result.success) {
                        showSuccess('Quiz deleted successfully!');
                        showAssignmentsList();
                        loadAssignments();
                    } else {
                        throw new Error(result.message || 'Failed to delete quiz');
                    }
                } catch (error) {
                    console.error('Error deleting quiz:', error);
                    showError('Failed to delete quiz: ' + error.message);
                }
            }
        }

        async function assignStudent() {
            const email = document.getElementById('student-email').value.trim();
            if (!email) {
                showError('Please enter a student email');
                return;
            }

            if (!currentAssignment) {
                showError('No assignment selected');
                return;
            }

            try {
                const token = localStorage.getItem('token');
                const response = await fetch(`${createAssignmentUrl}/${currentAssignment.id}/assign`, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        student_emails: [email]
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.success) {
                    if (result.data.invalid_emails && result.data.invalid_emails.length > 0) {
                        showError(`Some emails were invalid: ${result.data.invalid_emails.join(', ')}`);
                    } else {
                        showSuccess('Student assigned successfully!');
                    }

                    document.getElementById('student-email').value = '';
                    loadAssignments();
                } else {
                    throw new Error(result.message || 'Failed to assign student');
                }
            } catch (error) {
                console.error('Error assigning student:', error);
                showError('Failed to assign student: ' + error.message);
            }
        }

        function submitFeedback(studentId) {
            if (!currentAssignment) return;

            const studentData = currentAssignmentData?.status_data?.data?.find(s => s.student_id === studentId);
            const hasFeedback = currentAssignmentData?.feedbacks?.some(f => f.student_id === studentId && f.assignment_id === currentAssignment.id);
            const existingFeedback = currentAssignmentData?.feedbacks?.find(f => f.student_id === studentId && f.assignment_id === currentAssignment.id);

            const modal = document.getElementById('feedback-modal');
            const title = document.getElementById('feedback-modal-title');
            const gradeInput = document.getElementById('feedback-grade');
            const commentsInput = document.getElementById('feedback-comments');
            const editBtn = document.getElementById('feedback-edit-btn');
            const submitBtn = document.getElementById('feedback-submit-btn');
            const updateBtn = document.getElementById('feedback-update-btn');
            const cancelBtn = document.getElementById('feedback-cancel-btn');

            editBtn.style.display = 'none';
            submitBtn.style.display = 'none';
            updateBtn.style.display = 'none';
            cancelBtn.style.display = 'none';

            currentFeedbackStudentId = studentId;
            currentFeedbackData = existingFeedback;

            if (hasFeedback && existingFeedback) {
                title.textContent = `Feedback for ${studentData?.name || 'Student'}`;
                editBtn.style.display = 'inline-flex';

                gradeInput.value = existingFeedback.grade;
                commentsInput.value = existingFeedback.comments;
                gradeInput.readOnly = true;
                commentsInput.readOnly = true;
            } else {
                title.textContent = `Submit Feedback for ${studentData?.name || 'Student'}`;
                submitBtn.style.display = 'inline-flex';

                gradeInput.value = '';
                commentsInput.value = '';
                gradeInput.readOnly = false;
                commentsInput.readOnly = false;
            }

            modal.style.display = 'block';
        }

        function viewQuestions() {
            if (!currentAssignment) return;
            window.location.href = `${questionsUrl}/${currentAssignment.id}`;
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function formatDateDDMMYYYY(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function showLoading() {
            document.getElementById('loading').style.display = 'block';
        }

        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }

        function showError(message) {
            const errorDiv = document.getElementById('error');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }

        function showSuccess(message) {
            alert(message);
        }

        function editFeedback() {
            const gradeInput = document.getElementById('feedback-grade');
            const commentsInput = document.getElementById('feedback-comments');
            const editBtn = document.getElementById('feedback-edit-btn');
            const updateBtn = document.getElementById('feedback-update-btn');
            const cancelBtn = document.getElementById('feedback-cancel-btn');

            editBtn.style.display = 'none';
            updateBtn.style.display = 'inline-flex';
            cancelBtn.style.display = 'inline-flex';

            gradeInput.readOnly = false;
            commentsInput.readOnly = false;
        }

        function cancelFeedbackEdit() {
            const gradeInput = document.getElementById('feedback-grade');
            const commentsInput = document.getElementById('feedback-comments');
            const editBtn = document.getElementById('feedback-edit-btn');
            const updateBtn = document.getElementById('feedback-update-btn');
            const cancelBtn = document.getElementById('feedback-cancel-btn');

            editBtn.style.display = 'inline-flex';
            updateBtn.style.display = 'none';
            cancelBtn.style.display = 'none';

            gradeInput.readOnly = true;
            commentsInput.readOnly = true;

            if (currentFeedbackData) {
                gradeInput.value = currentFeedbackData.grade;
                commentsInput.value = currentFeedbackData.comments;
            }
        }

        async function handleSubmitFeedback() {
            const data = {
                assignment_id: currentAssignment.id,
                student_id: currentFeedbackStudentId,
                grade: parseFloat(document.getElementById('feedback-grade').value),
                comments: document.getElementById('feedback-comments').value
            };

            try {
                const token = localStorage.getItem('token');
                const response = await fetch(feedbackUrl, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.success) {
                    document.getElementById('feedback-modal').style.display = 'none';
                    showSuccess('Feedback submitted successfully!');
                    await loadAssignments();
                    if (currentAssignment) {
                        showAssignmentDetail(currentAssignment.id);
                    }
                } else {
                    throw new Error(result.message || 'Failed to submit feedback');
                }
            } catch (error) {
                console.error('Error submitting feedback:', error);
                showError('Failed to submit feedback: ' + error.message);
            }
        }

        async function handleUpdateFeedback() {
            const data = {
                assignment_id: currentAssignment.id,
                student_id: currentFeedbackStudentId,
                grade: parseFloat(document.getElementById('feedback-grade').value),
                comments: document.getElementById('feedback-comments').value
            };

            try {
                const token = localStorage.getItem('token');
                const response = await fetch(feedbackUrl, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.success) {
                    document.getElementById('feedback-modal').style.display = 'none';
                    showSuccess('Feedback updated successfully!');
                    await loadAssignments();
                    if (currentAssignment) {
                        showAssignmentDetail(currentAssignment.id);
                    }
                } else {
                    throw new Error(result.message || 'Failed to update feedback');
                }
            } catch (error) {
                console.error('Error updating feedback:', error);
                showError('Failed to update feedback: ' + error.message);
            }
        }

    </script>

    <div id="feedback-modal" class="modal">
        <div class="modal-content feedback-modal-content">
            <div class="modal-header">
                <h2 id="feedback-modal-title">Submit Feedback</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="feedback-modal-body">
                <div class="form-group">
                    <label for="feedback-grade">Grade *</label>
                    <input type="number" id="feedback-grade" name="grade" min="0" max="100" step="0.01" required
                        class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label for="feedback-comments">Comments *</label>
                    <textarea id="feedback-comments" name="comments" rows="4" required class="form-input"
                        placeholder="Enter your feedback comments..." readonly></textarea>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" id="feedback-cancel-btn" class="btn btn-secondary"
                    style="display: none;">Cancel</button>
                <button type="button" id="feedback-edit-btn" class="btn btn-primary" style="display: none;">
                    <i class="fas fa-edit"></i>
                    Edit Feedback
                </button>
                <button type="button" id="feedback-submit-btn" class="btn btn-primary" style="display: none;">
                    <i class="fas fa-save"></i>
                    Submit Feedback
                </button>
                <button type="button" id="feedback-update-btn" class="btn btn-primary" style="display: none;">
                    <i class="fas fa-save"></i>
                    Update Feedback
                </button>
            </div>
        </div>
    </div>
</body>

</html>