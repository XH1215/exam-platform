<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Questions | Quiz Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quiz-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewQuestions.css') }}">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('profile') }}">
                <i class="fas fa-graduation-cap"></i>
                Quiz Platform - Teacher
            </a>
            <div class="nav-actions">
                <button id="back-btn" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i>
                    Back to Quiz
                </button>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="page-header" id="main-page-header">
                <div class="header-content">
                    <div>
                        <h1 class="page-title">Quiz Questions</h1>
                        <p class="page-subtitle" id="quiz-title">Quiz Questions Management</p>
                    </div>
                    <button id="add-question-btn" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i>
                        Add Question
                    </button>
                </div>
            </div>

            <div id="loading" class="loading-state">
                <div class="spinner-lg"></div>
                <p class="text-secondary">Loading questions...</p>
            </div>

            <div id="error" class="alert alert-error" style="display: none;"></div>

            <div id="questions-container" class="questions-container" style="display: none;">
                <div class="questions-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" id="total-questions">0</div>
                            <div class="stat-label">Total Questions</div>
                        </div>
                    </div>
                </div>

                <div id="questions-list" class="questions-list"></div>
            </div>

            <div id="empty-state" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3>No Questions Yet</h3>
                <p>Add your first question to get started.</p>
                <button class="btn btn-primary" onclick="showAddQuestionModal()">
                    <i class="fas fa-plus"></i>
                    Add Question
                </button>
            </div>
        </div>
    </main>

    <div id="add-question-modal" class="modal" style="display: none;">
        <div class="modal-content question-modal-content">
            <div class="modal-header">
                <h2>Add New Question</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="add-question-form">
                <div class="form-group">
                    <label for="question-text">Question Text *</label>
                    <textarea id="question-text" name="question_text" rows="3" required class="form-input"
                        placeholder="Enter your question here..."></textarea>
                </div>
                <div class="form-group">
                    <label>Answer Options * (2-4 options)</label>
                    <div class="options-container" id="add-options-container">
                        <div class="option-item">
                            <input type="checkbox" name="correct_options" value="0">
                            <input type="text" name="option_0" class="form-input option-input" placeholder="Option A"
                                required>
                            <button type="button" class="btn-remove-option" onclick="removeOption(this)"
                                style="display: none;">×</button>
                        </div>
                        <div class="option-item">
                            <input type="checkbox" name="correct_options" value="1">
                            <input type="text" name="option_1" class="form-input option-input" placeholder="Option B"
                                required>
                            <button type="button" class="btn-remove-option" onclick="removeOption(this)"
                                style="display: none;">×</button>
                        </div>
                    </div>
                    <div class="option-controls">
                        <button type="button" id="add-option-btn" class="btn btn-outline-secondary btn-sm"
                            onclick="addOption('add')">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>
                    <p class="help-text">Check one or more correct answers. Minimum 2 options, maximum 4 options.</p>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="hideAddQuestionModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Add Question
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-question-modal" class="modal" style="display: none;">
        <div class="modal-content question-modal-content">
            <div class="modal-header">
                <h2>Edit Question</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="edit-question-form">
                <div class="form-group">
                    <label for="edit-question-text">Question Text *</label>
                    <textarea id="edit-question-text" name="question_text" rows="3" required
                        class="form-input"></textarea>
                </div>
                <div class="form-group">
                    <label>Answer Options * (2-4 options)</label>
                    <div class="options-container" id="edit-options-container">
                    </div>
                    <div class="option-controls">
                        <button type="button" id="edit-add-option-btn" class="btn btn-outline-secondary btn-sm"
                            onclick="addOption('edit')">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>
                    <p class="help-text">Check one or more correct answers. Minimum 2 options, maximum 4 options.</p>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="hideEditQuestionModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentAssignmentId = null;
        let currentQuestionId = null;
        let questions = [];

        const loginUrl = '{{ route("login") }}';
        const profileUrl = '{{ route("profile") }}';
        const questionsApiUrl = '{{ url("api/questions") }}';

        document.addEventListener('DOMContentLoaded', function () {
            const urlPath = window.location.pathname;
            const pathParts = urlPath.split('/');
            currentAssignmentId = pathParts[pathParts.length - 1];

            if (!currentAssignmentId || isNaN(currentAssignmentId)) {
                showError('Invalid assignment ID');
                return;
            }

            loadQuestions();
            setupEventListeners();
            document.getElementById('add-question-modal').style.display = 'none';
            document.getElementById('edit-question-modal').style.display = 'none';
        });

        function setupEventListeners() {
            document.getElementById('back-btn').addEventListener('click', goBack);
            document.getElementById('add-question-btn').addEventListener('click', showAddQuestionModal);
            document.getElementById('add-question-form').addEventListener('submit', addQuestion);
            document.getElementById('edit-question-form').addEventListener('submit', updateQuestion);

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

        function goBack() {
            window.history.back();
        }

        async function loadQuestions() {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = loginUrl;
                return;
            }

            try {
                showLoading();

                const response = await fetch(`${questionsApiUrl}?assignment_id=${currentAssignmentId}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        localStorage.removeItem('token');
                        window.location.href = loginUrl;
                        return;
                    }
                    if (response.status === 403) {
                        const errorResult = await response.json();
                        showError(errorResult.message || 'Access forbidden');
                        setTimeout(() => {
                            window.location.href = profileUrl;
                        }, 1500);
                        return;
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.success) {
                    questions = result.data || [];
                    displayQuestions();
                } else {
                    throw new Error(result.message || 'Failed to load questions');
                }
            } catch (error) {
                console.error('Error loading questions:', error);
                showError('Failed to load questions: ' + error.message);
                document.getElementById('questions-container').style.display = 'none';
                document.getElementById('empty-state').style.display = 'block';
            } finally {
                hideLoading();
            }
        }

        function displayQuestions() {
            const container = document.getElementById('questions-container');
            const list = document.getElementById('questions-list');
            const emptyState = document.getElementById('empty-state');
            const totalQuestionsEl = document.getElementById('total-questions');
            document.getElementById('quiz-title').textContent = 'Manage quiz questions and answers';

            if (questions.length === 0) {
                container.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }

            emptyState.style.display = 'none';
            container.style.display = 'block';
            totalQuestionsEl.textContent = questions.length;

            list.innerHTML = questions.map((question, index) => {
                const correctAnswers = Array.isArray(question.correct_answer) ? question.correct_answer : [question.correct_answer];
                const correctIndices = correctAnswers.map(answer => question.options.indexOf(answer));
                const correctLabels = correctIndices.map(idx => String.fromCharCode(65 + idx)).join(', ');

                return `
                <div class="question-card">
                    <div class="question-header">
                        <div class="question-number">Question ${index + 1}</div>
                        <div class="question-actions">
                            <button class="btn btn-outline-primary btn-sm" onclick="editQuestion(${question.id})">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteQuestion(${question.id})">
                                <i class="fas fa-trash"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                    <div class="question-content">
                        <div class="question-text">${question.question_text}</div>
                        <div class="question-options">
                            ${question.options.map((option, optionIndex) => `
                                <div class="option-item ${correctAnswers.includes(option) ? 'correct-option' : ''}">
                                    <span class="option-label">${String.fromCharCode(65 + optionIndex)}.</span>
                                    <span class="option-text">${option}</span>
                                    ${correctAnswers.includes(option) ? '<i class="fas fa-check-circle correct-indicator"></i>' : ''}
                                </div>
                            `).join('')}
                        </div>
                        <div class="question-meta">
                            <span class="correct-answer-label">Correct Answer${correctAnswers.length > 1 ? 's' : ''}: ${correctLabels}</span>
                            <span class="question-date">Created: ${formatDate(question.created_at)}</span>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }

        function showAddQuestionModal() {
            document.getElementById('add-question-modal').style.display = 'block';
        }

        function hideAddQuestionModal() {
            document.getElementById('add-question-modal').style.display = 'none';
            document.getElementById('add-question-form').reset();
        }

        function showEditQuestionModal() {
            document.getElementById('edit-question-modal').style.display = 'block';
        }

        function hideEditQuestionModal() {
            document.getElementById('edit-question-modal').style.display = 'none';
            document.getElementById('edit-question-form').reset();
        }

        async function addQuestion(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            const options = [];
            let optionIndex = 0;
            while (formData.get(`option_${optionIndex}`) !== null) {
                const optionValue = formData.get(`option_${optionIndex}`);
                if (optionValue && optionValue.trim()) {
                    options.push(optionValue.trim());
                }
                optionIndex++;
            }

            if (options.length < 2 || options.length > 4) {
                showModalError('add-question-modal', 'Please provide between 2 and 4 options');
                return;
            }

            const correctIndices = formData.getAll('correct_options');
            if (correctIndices.length === 0) {
                showModalError('add-question-modal', 'Please select exactly one correct answer');
                return;
            }

            if (correctIndices.length > 1) {
                showModalError('add-question-modal', 'Please select only one correct answer');
                return;
            }

            const correctAnswer = options[parseInt(correctIndices[0])];

            const data = {
                questions: [{
                    assignment_id: currentAssignmentId,
                    question_text: formData.get('question_text'),
                    options: options,
                    correct_answer: correctAnswer
                }]
            };

            try {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

                const token = localStorage.getItem('token');
                const response = await fetch(questionsApiUrl, {
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
                    hideAddQuestionModal();
                    showSuccess('Question added successfully!');
                    loadQuestions();
                } else {
                    throw new Error(result.message || 'Failed to add question');
                }
            } catch (error) {
                console.error('Error adding question:', error);
                showModalError('add-question-modal', 'Failed to add question: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        function addOption(modalType) {
            const container = document.getElementById(`${modalType}-options-container`);
            const currentOptions = container.querySelectorAll('.option-item');

            if (currentOptions.length >= 4) {
                const modalId = modalType === 'add' ? 'add-question-modal' : 'edit-question-modal';
                showModalError(modalId, 'Maximum 4 options allowed');
                return;
            }

            const optionIndex = currentOptions.length;
            const optionLabel = String.fromCharCode(65 + optionIndex);

            const optionItem = document.createElement('div');
            optionItem.className = 'option-item';
            optionItem.innerHTML = `
            <input type="checkbox" name="correct_options" value="${optionIndex}">
            <input type="text" name="option_${optionIndex}" class="form-input option-input" placeholder="Option ${optionLabel}" required>
            <button type="button" class="btn-remove-option" onclick="removeOption(this)">×</button>
        `;

            container.appendChild(optionItem);
            updateRemoveButtons(modalType);
        }

        function removeOption(button) {
            const container = button.closest('.options-container');
            const optionItem = button.closest('.option-item');

            if (container.querySelectorAll('.option-item').length <= 2) {
                const modalId = container.id.includes('add') ? 'add-question-modal' : 'edit-question-modal';
                showModalError(modalId, 'Minimum 2 options required');
                return;
            }

            optionItem.remove();

            const remainingOptions = container.querySelectorAll('.option-item');
            remainingOptions.forEach((item, index) => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                const textInput = item.querySelector('input[type="text"]');
                const placeholder = String.fromCharCode(65 + index);

                checkbox.value = index;
                textInput.name = `option_${index}`;
                textInput.placeholder = `Option ${placeholder}`;
            });

            const modalType = container.id.includes('add') ? 'add' : 'edit';
            updateRemoveButtons(modalType);
        }

        function updateRemoveButtons(modalType) {
            const container = document.getElementById(`${modalType}-options-container`);
            const options = container.querySelectorAll('.option-item');
            const removeButtons = container.querySelectorAll('.btn-remove-option');

            removeButtons.forEach(btn => {
                btn.style.display = options.length > 2 ? 'inline-block' : 'none';
            });
        }

        function editQuestion(questionId) {
            const question = questions.find(q => q.id === questionId);
            if (!question) return;

            currentQuestionId = questionId;

            document.getElementById('edit-question-text').value = question.question_text;

            const container = document.getElementById('edit-options-container');
            container.innerHTML = '';

            const correctAnswers = Array.isArray(question.correct_answer) ? question.correct_answer : [question.correct_answer];

            question.options.forEach((option, index) => {
                const optionLabel = String.fromCharCode(65 + index);
                const isCorrect = correctAnswers.includes(option);

                const optionItem = document.createElement('div');
                optionItem.className = 'option-item';
                optionItem.innerHTML = `
                <input type="checkbox" name="correct_options" value="${index}" ${isCorrect ? 'checked' : ''}>
                <input type="text" name="option_${index}" class="form-input option-input" value="${option}" required>
                <button type="button" class="btn-remove-option" onclick="removeOption(this)" ${question.options.length <= 2 ? 'style="display: none;"' : ''}>×</button>
            `;

                container.appendChild(optionItem);
            });

            updateRemoveButtons('edit');
            showEditQuestionModal();
        }

        async function updateQuestion(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            const options = [];
            let optionIndex = 0;
            while (formData.get(`option_${optionIndex}`) !== null) {
                const optionValue = formData.get(`option_${optionIndex}`);
                if (optionValue && optionValue.trim()) {
                    options.push(optionValue.trim());
                }
                optionIndex++;
            }

            const correctIndices = formData.getAll('correct_options');
            if (correctIndices.length === 0) {
                showModalError('edit-question-modal', 'Please select exactly one correct answer');
                return;
            }

            if (correctIndices.length > 1) {
                showModalError('edit-question-modal', 'Please select only one correct answer');
                return;
            }

            const correctAnswer = options[parseInt(correctIndices[0])];

            const data = {
                assignment_id: currentAssignmentId,
                question_text: formData.get('question_text'),
                options: options,
                correct_answer: correctAnswer
            };

            try {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

                const token = localStorage.getItem('token');
                const response = await fetch(`${questionsApiUrl}/${currentQuestionId}`, {
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
                    hideEditQuestionModal();
                    showSuccess('Question updated successfully!');
                    loadQuestions();
                } else {
                    throw new Error(result.message || 'Failed to update question');
                }
            } catch (error) {
                console.error('Error updating question:', error);
                showModalError('edit-question-modal', 'Failed to update question: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        function showModalError(modalId, message) {
            const modal = document.getElementById(modalId);
            let errorDiv = modal.querySelector('.modal-error');

            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-error modal-error';
                errorDiv.style.marginBottom = '1rem';
                const modalContent = modal.querySelector('.modal-content');
                modalContent.insertBefore(errorDiv, modalContent.children[1]);
            }

            errorDiv.textContent = message;
            errorDiv.style.display = 'block';

            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }

        async function deleteQuestion(questionId) {
            if (!confirm('Are you sure you want to delete this question?')) {
                return;
            }

            const deleteBtn = document.querySelector(`button[onclick="deleteQuestion(${questionId})"]`);
            const originalText = deleteBtn.innerHTML;

            try {
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

                const token = localStorage.getItem('token');
                const response = await fetch(`${questionsApiUrl}/${questionId}`, {
                    method: 'DELETE',
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
                    showSuccess('Question deleted successfully!');
                    loadQuestions();
                } else {
                    throw new Error(result.message || 'Failed to delete question');
                }
            } catch (error) {
                console.error('Error deleting question:', error);
                showError('Failed to delete question: ' + error.message);
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = originalText;
            }
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
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
    </script>
</body>

</html>