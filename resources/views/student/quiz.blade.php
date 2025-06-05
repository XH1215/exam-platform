<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz Assignment | Quiz Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quiz-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/quiz.css') }}">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('quizHome') }}">
                <i class="fas fa-arrow-left"></i>
                Back to My Quizzes
            </a>
            <div class="quiz-timer" id="quiz-timer" style="display: none;">
                <i class="fas fa-clock"></i>
                <span id="timer-display">00:00</span>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div id="loading" class="loading-state">
                <div class="spinner-lg"></div>
                <p class="text-secondary">Loading quiz...</p>
            </div>

            <div id="error" class="alert alert-error" style="display: none;"></div>

            <div id="access-denied" class="alert alert-error" style="display: none;">
                <h3><i class="fas fa-exclamation-triangle"></i> Access Denied</h3>
                <p>You don't have permission to access this quiz directly. Please go back to your assignments page and
                    start the quiz from there.</p>
                <button class="btn btn-primary" onclick="goBackToAssignments()">
                    <i class="fas fa-arrow-left"></i>
                    Back to My Quizzes
                </button>
            </div>

            <div id="quiz-container" style="display: none;">
                <div class="quiz-header">
                    <h1 class="quiz-title">Quiz</h1>
                    <div class="quiz-meta">
                        <div class="quiz-progress">
                            <span id="progress-text">Question 1 of 0</span>
                            <div class="progress">
                                <div id="progress-bar" class="progress-bar" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="quiz-content">
                    <form id="quiz-form">
                        <div id="questions-container"></div>

                        <div class="quiz-navigation">
                            <button type="button" id="prev-btn" class="btn btn-secondary" style="display: none;">
                                <i class="fas fa-chevron-left"></i>
                                Previous
                            </button>
                            <button type="button" id="next-btn" class="btn btn-primary" style="display: none;">
                                Next
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <button type="submit" id="submit-btn" class="btn btn-success" style="display: none;">
                                <i class="fas fa-check"></i>
                                Submit Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Success Modal -->
            <div id="success-modal" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-check-circle text-success"></i> Quiz Submitted!</h3>
                    </div>
                    <div class="modal-body">
                        <p>Your quiz has been submitted successfully.</p>
                        <div class="score-display">
                            <span>Your Score: </span>
                            <strong id="final-score">--</strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="goBackToAssignments()">
                            Back to My Quizzes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const questionsApiUrl = '{{ url("api/questions") }}';
        const submitApiUrl = '{{ url("api/student/assignments/submit") }}';
        const myAssignmentsUrl = '{{ route("quizHome") }}';
        const loginUrl = '{{ route("login") }}';

        @if(isset($assignment_id))
            const assignmentId = {{ $assignment_id }};
        @else
            const assignmentId = null;
        @endif

        let questions = [];
        let currentQuestion = 0;
        let answers = {};
        let startTime = null;

        document.addEventListener('DOMContentLoaded', function () {
            if (!checkAccess()) {
                showAccessDenied();
                return;
            }

            loadQuiz();
            setupEventListeners();
        });

        function checkAccess() {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = loginUrl;
                return false;
            }

            const accessToken = sessionStorage.getItem('quiz_access_token');
            const storedAssignmentId = sessionStorage.getItem('quiz_assignment_id');
            const accessTime = sessionStorage.getItem('quiz_access_time');

            if (!accessToken || !storedAssignmentId || !accessTime) {
                return false;
            }

            if (parseInt(storedAssignmentId) !== assignmentId) {
                return false;
            }

            const currentTime = Date.now();
            const timeDiff = currentTime - parseInt(accessTime);
            const maxAge = 120 * 60 * 1000;

            if (timeDiff > maxAge) {
                sessionStorage.removeItem('quiz_access_token');
                sessionStorage.removeItem('quiz_assignment_id');
                sessionStorage.removeItem('quiz_access_time');
                return false;
            }

            return true;
        }

        function showAccessDenied() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('access-denied').style.display = 'block';
        }

        function setupEventListeners() {
            document.getElementById('prev-btn').addEventListener('click', prevQuestion);
            document.getElementById('next-btn').addEventListener('click', nextQuestion);
            document.getElementById('quiz-form').addEventListener('submit', submitQuiz);

            window.addEventListener('beforeunload', function () {
                sessionStorage.removeItem('quiz_access_token');
                sessionStorage.removeItem('quiz_assignment_id');
                sessionStorage.removeItem('quiz_access_time');
            });
        }

        async function loadQuiz() {
            const token = localStorage.getItem('token');

            if (!assignmentId) {
                showError('Invalid assignment ID');
                return;
            }

            try {
                const questionsResponse = await fetch(`${questionsApiUrl}?assignment_id=${assignmentId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!questionsResponse.ok) {
                    throw new Error(`Failed to load questions: ${questionsResponse.status}`);
                }

                const questionsData = await questionsResponse.json();

                if (!questionsData.success) {
                    throw new Error(questionsData.message || 'Failed to load questions');
                }

                questions = questionsData.data.map(q => ({
                    id: q.id,
                    question: q.question_text,
                    options: q.options,
                    correct_answer: q.correct_answer
                }));

                if (questions.length === 0) {
                    throw new Error('No questions found for this assignment');
                }

                initializeQuiz();

            } catch (error) {
                console.error('Error loading quiz:', error);
                showError(error.message);
            }
        }

        function initializeQuiz() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('quiz-container').style.display = 'block';

            updateProgress();
            renderCurrentQuestion();
            startTime = new Date();
        }

        function renderCurrentQuestion() {
            const container = document.getElementById('questions-container');
            const question = questions[currentQuestion];

            container.innerHTML = `
                <div class="question-card">
                    <div class="question-header">
                        <span class="question-number">Question ${currentQuestion + 1}</span>
                    </div>
                    <div class="question-content">
                        <h3 class="question-text">${question.question}</h3>
                        <div class="options-container">
                            ${question.options.map((option, index) => `
                                <label class="option-item">
                                    <input type="radio" name="question_${question.id}" value="${option}" 
                                           ${answers[question.id] === option ? 'checked' : ''}>
                                    <span class="option-text">${option}</span>
                                    <span class="option-check"></span>
                                </label>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;

            const radioButtons = container.querySelectorAll('input[type="radio"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function () {
                    answers[question.id] = this.value;
                    updateNavigationButtons();
                });
            });

            updateNavigationButtons();
        }

        function updateProgress() {
            const progressText = document.getElementById('progress-text');
            const progressBar = document.getElementById('progress-bar');

            progressText.textContent = `Question ${currentQuestion + 1} of ${questions.length}`;
            const percentage = ((currentQuestion + 1) / questions.length) * 100;
            progressBar.style.width = percentage + '%';
        }

        function updateNavigationButtons() {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');

            prevBtn.style.display = currentQuestion > 0 ? 'inline-flex' : 'none';

            if (currentQuestion < questions.length - 1) {
                nextBtn.style.display = 'inline-flex';
                submitBtn.style.display = 'none';
            } else {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-flex';
            }
        }

        function prevQuestion() {
            if (currentQuestion > 0) {
                currentQuestion--;
                updateProgress();
                renderCurrentQuestion();
            }
        }

        function nextQuestion() {
            if (currentQuestion < questions.length - 1) {
                currentQuestion++;
                updateProgress();
                renderCurrentQuestion();
            }
        }

        async function submitQuiz(e) {
            e.preventDefault();

            const unansweredQuestions = questions.filter(q => !answers[q.id]);
            if (unansweredQuestions.length > 0) {
                alert(`Please answer all questions. ${unansweredQuestions.length} question(s) remaining.`);
                return;
            }

            if (!confirm('Are you sure you want to submit your quiz? You cannot change your answers after submission.')) {
                return;
            }

            const submitBtn = document.getElementById('submit-btn');
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<div class="spinner"></div> Submitting...';

            const token = localStorage.getItem('token');

            try {
                const response = await fetch(submitApiUrl, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        assignment_id: parseInt(assignmentId),
                        answers: answers
                    })
                });

                const data = await response.json();

                if (data.success) {
                    sessionStorage.removeItem('quiz_access_token');
                    sessionStorage.removeItem('quiz_assignment_id');
                    sessionStorage.removeItem('quiz_access_time');

                    showSuccessModal(data.data.score);
                } else {
                    throw new Error(data.message || 'Failed to submit quiz');
                }

            } catch (error) {
                console.error('Error submitting quiz:', error);
                alert('Failed to submit quiz: ' + error.message);

                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Submit Quiz';
            }
        }

        function showSuccessModal(score) {
            document.getElementById('final-score').textContent = score + '%';
            document.getElementById('success-modal').style.display = 'flex';
        }

        function goBackToAssignments() {
            sessionStorage.removeItem('quiz_access_token');
            sessionStorage.removeItem('quiz_assignment_id');
            sessionStorage.removeItem('quiz_access_time');

            window.location.href = myAssignmentsUrl;
        }

        function showError(message) {
            document.getElementById('loading').style.display = 'none';
            const errorDiv = document.getElementById('error');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            errorDiv.classList.add('show');
        }
    </script>
</body>

</html>