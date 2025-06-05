<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Feedback | Quiz Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quiz-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feedback.css') }}">
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
                <div class="page-title-section">
                    <h1 class="page-title">My Feedback</h1>
                    <p class="page-subtitle">View feedback from your teachers</p>
                </div>
                <div class="search-section">
                    <div class="search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="search-input" class="search-input" placeholder="">
                        <button id="clear-search" class="clear-search" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="loading" class="loading-state">
                <div class="spinner-lg"></div>
                <p class="text-secondary">Loading your feedback...</p>
            </div>

            <div id="error" class="alert alert-error" style="display: none;"></div>

            <div id="feedback-container" style="display: none;">
                <div id="feedback-grid" class="feedback-grid"></div>
            </div>

            <div id="empty-state" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>No Feedback Yet</h3>
                <p>You don't have any feedback from your teachers yet.</p>
            </div>

            <div id="no-results" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>No Results Found</h3>
                <p>No feedback matches your search criteria.</p>
            </div>
        </div>
    </main>

    <script>
        const loginUrl = '{{ route("login") }}';
        const profileUrl = '{{ route("profile") }}';
        const feedbackApiUrl = '{{ url("api/student/feedbacks") }}';
        let feedbacks = [];
        let filteredFeedbacks = [];

        document.getElementById('logout-btn').addEventListener('click', function () {
            localStorage.removeItem('token');
            sessionStorage.removeItem('quiz_access_token');
            window.location.href = loginUrl;
        });

        // Search functionality
        const searchInput = document.getElementById('search-input');
        const clearSearchBtn = document.getElementById('clear-search');

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query) {
                clearSearchBtn.style.display = 'block';
                filterFeedbacks(query);
            } else {
                clearSearchBtn.style.display = 'none';
                showAllFeedbacks();
            }
        });

        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            this.style.display = 'none';
            showAllFeedbacks();
        });

        function filterFeedbacks(query) {
            const lowercaseQuery = query.toLowerCase();
            filteredFeedbacks = feedbacks.filter(feedback => 
                feedback.assignment_title.toLowerCase().includes(lowercaseQuery) ||
                feedback.teacher_name.toLowerCase().includes(lowercaseQuery)
            );
            renderFeedbacks(filteredFeedbacks);
            toggleNoResults(filteredFeedbacks.length === 0);
        }

        function showAllFeedbacks() {
            filteredFeedbacks = feedbacks;
            renderFeedbacks(filteredFeedbacks);
            toggleNoResults(false);
        }

        function toggleNoResults(show) {
            const noResults = document.getElementById('no-results');
            const feedbackContainer = document.getElementById('feedback-container');
            
            if (show && feedbacks.length > 0) {
                noResults.style.display = 'block';
                feedbackContainer.style.display = 'none';
            } else {
                noResults.style.display = 'none';
                if (feedbacks.length > 0) {
                    feedbackContainer.style.display = 'block';
                }
            }
        }

        async function fetchFeedbacks() {
            const token = localStorage.getItem('token');
            const loadingDiv = document.getElementById('loading');
            const errorDiv = document.getElementById('error');
            const feedbackContainer = document.getElementById('feedback-container');
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

                const response = await fetch(feedbackApiUrl, { headers });

                if (!response.ok) {
                    throw new Error(`API error: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    feedbacks = data.data;
                    filteredFeedbacks = feedbacks;
                }

                loadingDiv.style.display = 'none';

                if (feedbacks.length === 0) {
                    emptyState.style.display = 'block';
                } else {
                    renderFeedbacks(filteredFeedbacks);
                    feedbackContainer.style.display = 'block';
                    feedbackContainer.classList.add('fade-in');
                }

            } catch (error) {
                console.error('Error fetching feedback:', error);
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
                    localStorage.removeItem('token');
                    sessionStorage.removeItem('quiz_access_token');
                    window.location.href = loginUrl;
                }, 2000);
            }
        }

        function getGradeClass(grade) {
            const numGrade = parseFloat(grade);
            if (numGrade >= 90) return 'grade-excellent';
            if (numGrade >= 80) return 'grade-good';
            if (numGrade >= 70) return 'grade-average';
            if (numGrade >= 60) return 'grade-below-average';
            return 'grade-poor';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function renderFeedbacks(feedbacksToRender = feedbacks) {
            const grid = document.getElementById('feedback-grid');
            grid.innerHTML = '';

            feedbacksToRender.forEach((feedback, index) => {
                const gradeClass = getGradeClass(feedback.grade);
                const formattedDate = formatDate(feedback.updated_at);

                const card = document.createElement('div');
                card.className = 'feedback-card';
                card.innerHTML = `
                    <div class="feedback-header" onclick="toggleFeedback(${index})">
                        <div class="feedback-info">
                            <div class="feedback-title-section">
                                <h3 class="feedback-title">${feedback.assignment_title}</h3>
                                <p class="teacher-name">
                                    <i class="fas fa-user-tie"></i>
                                    ${feedback.teacher_name}
                                </p>
                            </div>
                            <div class="feedback-grade-preview">
                                <div class="grade-badge ${gradeClass}">
                                    ${Math.round(parseFloat(feedback.grade))}%
                                </div>
                            </div>
                        </div>
                        <div class="expand-icon">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="feedback-details">
                        <div class="feedback-content">
                            <div class="feedback-section">
                                <div class="section-header">
                                    <i class="fas fa-star"></i>
                                    <h4>Grade</h4>
                                </div>
                                <div class="grade-display ${gradeClass}">
                                    ${parseFloat(feedback.grade).toFixed(1)}%
                                </div>
                            </div>
                            
                            <div class="feedback-section">
                                <div class="section-header">
                                    <i class="fas fa-comment-alt"></i>
                                    <h4>Comments</h4>
                                </div>
                                <div class="comments-content">
                                    ${feedback.comments || 'No comments provided.'}
                                </div>
                            </div>
                            
                            <div class="feedback-section">
                                <div class="section-header">
                                    <i class="fas fa-calendar"></i>
                                    <h4>Date</h4>
                                </div>
                                <div class="date-content">
                                    ${formattedDate}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function toggleFeedback(index) {
            const cards = document.querySelectorAll('.feedback-card');
            const currentCard = cards[index];
            
            if (currentCard) {
                currentCard.classList.toggle('expanded');
            }
        }

        document.addEventListener('DOMContentLoaded', fetchFeedbacks);
    </script>
</body>

</html>