<!-- resources/views/auth/login.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quiz-design-system.css') }}">
</head>

<body>
    <div class="container container-xs"
        style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        <div class="card" style="width: 100%; max-width: 400px;">
            <div class="card-header">
                <h2 class="heading-2">Welcome Back</h2>
                <p class="text-secondary">Please sign in to your account</p>
            </div>

            <div class="card-body">
                <div id="error" class="alert alert-error" style="display: none;"></div>

                <form id="login-form" novalidate>
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" class="form-input"
                                placeholder="Enter your email address" required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <div id="email-error"
                            style="display: none; margin-top: 4px; font-size: 0.875rem; color: #ef4444; font-weight: 700;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="form-input"
                                placeholder="Enter your password" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">
                        <span class="btn-text">Sign In</span>
                        <div class="spinner"></div>
                    </button>
                </form>

                <div class="mt-3 text-center">
                    <span>Don’t have an account?
                        <a href="{{ route('register') }}">Register</a>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('login-form');
            const emailInput = document.getElementById('email');
            const emailErrorDiv = document.getElementById('email-error');
            const errorDiv = document.getElementById('error');
            const submitButton = document.querySelector('.btn');

            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                emailErrorDiv.style.display = 'none';
                emailErrorDiv.textContent = '';
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';

                const email = emailInput.value.trim();
                const password = document.getElementById('password').value;

                if (!email) {
                    emailErrorDiv.textContent = 'Email is required.';
                    emailErrorDiv.style.display = 'block';
                    return;
                }
                if (!isValidEmail(email)) {
                    emailErrorDiv.textContent = 'Please enter a valid email address.';
                    emailErrorDiv.style.display = 'block';
                    return;
                }

                submitButton.classList.add('loading');
                submitButton.disabled = true;

                try {
                    const response = await fetch('{{ url("api/auth/login") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ email, password })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        localStorage.setItem('token', data.data.token);
                        window.location.href = '{{ url("profile") }}';
                    } else {
                        let message = data.message || 'Login failed';
                        if (data.data && data.data.errors) {
                            message = Object.values(data.data.errors).flat().join(' ');
                        }
                        errorDiv.textContent = message;
                        errorDiv.style.display = 'block';
                        errorDiv.classList.add('show');
                    }
                } catch (err) {
                    errorDiv.textContent = 'Network error, please try again later.';
                    errorDiv.style.display = 'block';
                    errorDiv.classList.add('show');
                } finally {
                    submitButton.classList.remove('loading');
                    submitButton.disabled = false;
                }
            });
        });
    </script>
</body>

</html>