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
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');

        :root {
            --bg:      #0B1426;
            --surface: rgba(255,255,255,0.04);
            --border:  rgba(255,255,255,0.09);
            --accent:  #38BDF8;
            --green:   #34D399;
            --red:     #F87171;
            --muted:   rgba(255,255,255,0.45);
            --radius:  12px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }

        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            background: var(--bg);
            color: #fff;
            padding: 48px 16px 24px;
            display: flex;
            flex-direction: column;
        }

        .message {
            max-width: 420px;
            margin: 0 auto 16px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            text-align: center;
            border: 1px solid;
        }
        .message.error {
            background: rgba(248,113,113,.1);
            color: var(--red);
            border-color: rgba(248,113,113,.3);
        }
        .message.success {
            background: rgba(52,211,153,.1);
            color: var(--green);
            border-color: rgba(52,211,153,.3);
        }

        #signin {
            background: var(--surface);
            border: 1px solid var(--border);
            width: 100%;
            max-width: 420px;
            margin: auto;
            padding: 36px 32px 28px;
            border-radius: var(--radius);
            text-align: center;
            box-shadow: 0 4px 32px rgba(0,0,0,0.4);
        }

        #signin h1 {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -.02em;
            margin-bottom: 24px;
        }

        form { position: relative; margin-top: 18px; }

        .input-group {
            position: relative;
            margin-bottom: 18px;
        }

        .input-label {
            position: absolute;
            left: 14px;
            top: 13px;
            color: var(--muted);
            transition: all 0.18s ease;
            pointer-events: none;
            background: transparent;
            padding: 0 4px;
            font-size: 0.95rem;
        }

        form input[type="text"],
        form input[type="password"] {
            font-family: "Plus Jakarta Sans", sans-serif;
            width: 100%;
            padding: 14px 12px 12px;
            font-size: 0.95rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
            background: rgba(255,255,255,0.04);
            color: #fff;
            transition: border-color 0.18s, box-shadow 0.18s;
        }
        form input[type="text"]:hover,
        form input[type="password"]:hover { border-color: rgba(255,255,255,.2); }
        form input[type="text"]:focus,
        form input[type="password"]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(56,189,248,.15);
        }
        form input:focus + .input-label,
        form input:not(:placeholder-shown) + .input-label {
            top: -9px;
            left: 10px;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--accent);
            background: #0B1426;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
            text-align: left;
            font-size: 0.875rem;
            color: var(--muted);
        }
        .remember-row input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--accent);
            cursor: pointer;
            flex-shrink: 0;
        }

        .form-links {
            display: flex;
            justify-content: space-between;
            margin: 14px 0 8px;
        }
        .form-links a {
            font-size: 0.85rem;
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }
        .form-links a:hover { text-decoration: underline; }

        #submit_button {
            font-family: "Plus Jakarta Sans", sans-serif;
            width: 100%;
            padding: 13px 0;
            background: var(--accent);
            color: #0B1426;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: opacity .15s, transform .05s;
        }
        #submit_button:hover  { opacity: .85; }
        #submit_button:focus  { outline: none; box-shadow: 0 0 0 3px rgba(56,189,248,.25); }
        #submit_button:active { transform: translateY(1px); }
        #submit_button:disabled { opacity: .5; cursor: not-allowed; }

        html[data-theme="light"] {
            --bg:      #f1f5f9;
            --surface: rgba(15,23,42,0.05);
            --border:  rgba(15,23,42,0.12);
            --muted:   rgba(15,23,42,0.5);
        }
        html[data-theme="light"] body { background: #f1f5f9; color: #0f172a; }
        html[data-theme="light"] form input[type="text"],
        html[data-theme="light"] form input[type="password"] { background: rgba(0,0,0,.03); color: #0f172a; }
        html[data-theme="light"] form input:focus + .input-label,
        html[data-theme="light"] form input:not(:placeholder-shown) + .input-label { background: #f1f5f9; }

        @media (max-width: 480px) {
            body { padding-top: 28px; }
            #signin { padding: 28px 18px 22px; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
        </style>
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

                <label class="remember-row"><input type="checkbox" name="remember" id="remember"> Remember me</label>
                
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