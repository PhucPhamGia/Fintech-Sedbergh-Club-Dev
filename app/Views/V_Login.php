<!-- 
Redirect into login page if user is already logged in (cookie)

Use <form> to submit login data (POST method) to Auth controller
-->

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign in</title>
        <link rel="stylesheet" href="<?= base_url('assets/css/login.css'); ?>">
    </head>
    <body>
        <!-- Sign In Section -->
        <main id="signin">
            <!-- Title -->
            <h1>Sign In</h1>

            <!-- Error/Success Message -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="message error">
                        <?= nl2br(esc(session()->getFlashdata('error'))) ?>
                </div>
            <?php elseif (session()->getFlashdata('success')): ?>
                <div class="message success">
                        <?= nl2br(esc(session()->getFlashdata('success'))) ?>
                </div>
            <?php endif;
            session()->remove('success');
            session()->remove('error');
            ?>

            <!-- Sign In Form -->
            <form action="<?= site_url('auth/login') ?>" method="POST" id="form">
                <?= csrf_field() ?> 
                <div class="input-group">
                        <input type="text" name="username" id="username" value="<?= esc($savedUser ?? '') ?>" placeholder=" " required>
                    <span class="input-label">Username or Email</span>
                </div>

                <div class="input-group">
                        <input type="password" id="password" name="password" placeholder=" " required>
                    <span class="input-label">Password</span>
                </div>

                <label><input type="checkbox" name="remember" id="remember"> Remember me</label>
                
                <div class="form-links">
                    <a href="<?= site_url("login/forgot-password") ?>" id="fgpass">Forgot Password?</a>
                    <a href="<?= site_url("register") ?>" id="register">Don't have an account?</a>
                </div>

                <button type="submit" id="submit_button">Login</button> <!-- Submit button -->
            </form>

            <script>
                // Client-side validation for login form
                const form = document.getElementById('form');
                const usernameInput = document.getElementById('username');
                const passwordInput = document.getElementById('password');
                const submitButton = document.getElementById('submit_button');

                // Validation rules (matching server-side)
                const USERNAME_MIN_LENGTH = 4;
                const USERNAME_MAX_LENGTH = 100;
                const PASSWORD_MIN_LENGTH = 6;
                const PASSWORD_MAX_LENGTH = 255;

                /**
                 * Validate username format
                 * @param {string} username - Username or email to validate
                 * @returns {object} - { isValid: boolean, error: string }
                 */
                function validateUsername(username) {
                    username = username.trim();

                    // Check required
                    if (!username) {
                        return { isValid: false, error: 'Username or email is required.' };
                    }

                    // Format checks
                    if (username.length < USERNAME_MIN_LENGTH) {
                        return { isValid: false, error: 'Username or email is too short.' };
                    }

                    if (username.length > USERNAME_MAX_LENGTH) {
                        return { isValid: false, error: 'Username or email is too long.' };
                    }

                    // Check for spaces (regex_match[/^\S+$/])
                    if (/\s/.test(username)) {
                        return { isValid: false, error: 'Username or email must not contain spaces.' };
                    }

                    return { isValid: true, error: null };
                }

                /**
                 * Validate password format
                 * @param {string} password - Password to validate
                 * @returns {object} - { isValid: boolean, error: string }
                 */
                function validatePassword(password) {
                    if (!password) {
                        return { isValid: false, error: 'Password is required.' };
                    }

                    if (password.length < PASSWORD_MIN_LENGTH) {
                        return { isValid: false, error: 'Password must be at least 6 characters.' };
                    }

                    if (password.length > PASSWORD_MAX_LENGTH) {
                        return { isValid: false, error: 'Password is too long.' };
                    }

                    return { isValid: true, error: null };
                }

                /**
                 * Display validation error
                 * @param {string} errorMessage - Error message to display
                 */
                function showValidationError(errorMessage) {
                    // Remove existing error message if any
                    const existingError = document.querySelector('.message.error');
                    if (existingError) {
                        existingError.remove();
                    }

                    // Create and insert error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'message error';
                    errorDiv.textContent = errorMessage;
                    form.parentElement.insertBefore(errorDiv, form);
                }

                /**
                 * Clear validation errors
                 */
                function clearValidationError() {
                    const existingError = document.querySelector('.message.error');
                    if (existingError && !existingError.textContent.includes('temporarily')) {
                        existingError.remove();
                    }
                }

                // Validate on input (real-time feedback)
                usernameInput.addEventListener('input', clearValidationError);
                passwordInput.addEventListener('input', clearValidationError);

                // Validate on form submission
                form.addEventListener('submit', function (e) {
                    clearValidationError();

                    const usernameResult = validateUsername(usernameInput.value);
                    const passwordResult = validatePassword(passwordInput.value);

                    // Validate username
                    if (!usernameResult.isValid) {
                        e.preventDefault();
                        showValidationError(usernameResult.error);
                        usernameInput.focus();
                        return;
                    }

                    // Validate password
                    if (!passwordResult.isValid) {
                        e.preventDefault();
                        showValidationError(passwordResult.error);
                        passwordInput.focus();
                        return;
                    }

                    // Disable submit button to prevent double submission
                    submitButton.disabled = true;
                    submitButton.textContent = 'Logging in...';
                });
            </script>
        </main>

        <!-- Footer -->
        <?= view('V_Footer') ?>
    </body>
</html>