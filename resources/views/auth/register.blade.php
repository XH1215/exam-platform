<!-- resources/views/auth/register.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quiz-design-system.css') }}">
</head>

<body>
    <div class="container container-xs"
        style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        <div class="card" style="width: 100%; max-width: 400px;">
            <div class="card-header">
                <h2 class="heading-2">Create Account</h2>
                <p class="text-secondary">Please fill in the details to register</p>
            </div>
            <div class="card-body">
                <div id="error" class="alert alert-error" style="display: none;"></div>
                <form id="register-form">
                    <div class="form-group">
                        <label for="name" class="form-label">Username</label>
                        <div class="input-wrapper">
                            <input type="text" id="name" name="name" class="form-input"
                                placeholder="Enter your username" required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" class="form-input"
                                placeholder="Enter your email address" required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="form-input"
                                placeholder="At least 8 characters, uppercase, lowercase, number, symbol" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-input" placeholder="Re-enter your password" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-full">
                        <span class="btn-text">Register</span>
                        <div class="spinner"></div>
                    </button>
                </form>
                <div class="mt-3 text-center">
                    <span>Already have an account? <a href="{{ route('login') }}">Sign In</a></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            localStorage.removeItem('token');

            const form = document.getElementById('register-form');
            const errorDiv = document.getElementById('error');
            const submitButton = document.querySelector('.btn');

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                errorDiv.style.display = 'none';
                errorDiv.textContent = '';

                submitButton.classList.add('loading');
                submitButton.disabled = true;

                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const password_confirmation = document.getElementById('password_confirmation').value;

                try {
                    const response = await fetch('/api/auth/register', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ name, email, password, password_confirmation })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        localStorage.setItem('token', data.data.token);
                        window.location.href = '/profile';
                    } else {
                        let message = data.message || 'Registration failed';
                        if (data.data && data.data.errors) {
                            message = Object.values(data.data.errors).flat().join(' ');
                        }
                        showError(message);
                    }
                } catch (err) {
                    showError('Network error, please try again later.');
                } finally {
                    submitButton.classList.remove('loading');
                    submitButton.disabled = false;
                }
            });

            function showError(message) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }
        });
    </script>
</body>

</html>