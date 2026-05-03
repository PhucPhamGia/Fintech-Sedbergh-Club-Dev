<!-- 
Use <form> to submit register data (POST method) to Auth controller
-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <?= view('V_Head') ?>
        <title>Register</title>
        <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
    </head>
    <body>
        <canvas id="auth-canvas"></canvas>
        <!-- Register Section -->
        <main id="register">
            <!-- Title -->
            <h1>Register</h1>

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

            <!-- Register Form -->
            <form action="<?= site_url('auth/register') ?>" method="POST" id="form">
                <?= csrf_field() ?> 
                <div class="name-row">
                    <div class="input-group">
                        <input type="text" name="first_name" id="first_name" placeholder=" " required>
                        <span class="input-label">First Name</span>
                    </div>

                    <div class="input-group">
                        <input type="text" name="last_name" id="last_name" placeholder=" " required>
                        <span class="input-label">Last Name</span>
                    </div>
                </div>
                
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder=" " required>
                    <span class="input-label">Username</span>
                </div>

                <div class="input-group">
                    <input type="text" id="email" name="email" placeholder=" " required>
                    <span class="input-label">Email</span>
                </div>

                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder=" " required>
                    <span class="input-label">Password</span>
                    <button type="button" class="pwd-toggle" id="pwd-toggle" aria-label="Toggle password visibility" tabindex="-1">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>

                <div class="input-group">
                    <input type="password" id="password_confirm" name="password_confirm" placeholder=" " required>
                    <span class="input-label">Confirm Password</span>
                    <button type="button" class="pwd-toggle" id="pwd-toggle-confirm" aria-label="Toggle password visibility" tabindex="-1">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                
                <div class="form-links">
                    <a href="<?= site_url("login") ?>" id="login">Already have an account?</a>
                </div>

                <button type="submit" id="submit_button">Register</button> <!-- Submit button -->
            </form>

            <script>
                // Client-side validation for register form (matches server-side rules)
                const form = document.getElementById('form');
                const firstNameInput = document.getElementById('first_name');
                const lastNameInput = document.getElementById('last_name');
                const usernameInput = document.getElementById('username');
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                const passwordConfirmInput = document.getElementById('password_confirm');
                const submitButton = document.getElementById('submit_button');

                // Validation rules (matching server-side)
                const FIRST_NAME_MIN_LENGTH = 2;
                const FIRST_NAME_MAX_LENGTH = 50;
                const LAST_NAME_MIN_LENGTH = 2;
                const LAST_NAME_MAX_LENGTH = 50;
                const USERNAME_MIN_LENGTH = 4;
                const USERNAME_MAX_LENGTH = 25;
                const EMAIL_MAX_LENGTH = 100;
                const PASSWORD_MIN_LENGTH = 6;
                const PASSWORD_MAX_LENGTH = 255;

                function validateFirstName(firstName) {
                    firstName = firstName.trim();

                    if (!firstName) {
                        return { isValid: false, error: 'First name is required.' };
                    }
                    if (firstName.length < FIRST_NAME_MIN_LENGTH) {
                        return { isValid: false, error: 'First name must be at least 2 characters.' };
                    }
                    if (firstName.length > FIRST_NAME_MAX_LENGTH) {
                        return { isValid: false, error: 'First name must not exceed 50 characters.' };
                    }

                    return { isValid: true, error: null };
                }

                function validateLastName(lastName) {
                    lastName = lastName.trim();

                    if (!lastName) {
                        return { isValid: false, error: 'Last name is required.' };
                    }
                    if (lastName.length < LAST_NAME_MIN_LENGTH) {
                        return { isValid: false, error: 'Last name must be at least 2 characters.' };
                    }
                    if (lastName.length > LAST_NAME_MAX_LENGTH) {
                        return { isValid: false, error: 'Last name must not exceed 50 characters.' };
                    }

                    return { isValid: true, error: null };
                }

                function validateUsername(username) {
                    username = username.trim();

                    if (!username) {
                        return { isValid: false, error: 'Username is required.' };
                    }
                    if (username.length < USERNAME_MIN_LENGTH) {
                        return { isValid: false, error: 'Username must be at least 4 characters.' };
                    }
                    if (username.length > USERNAME_MAX_LENGTH) {
                        return { isValid: false, error: 'Username must not exceed 25 characters.' };
                    }
                    if (/\s/.test(username)) {
                        return { isValid: false, error: 'Username must not contain spaces.' };
                    }

                    return { isValid: true, error: null };
                }

                function validateEmail(email) {
                    email = email.trim();

                    if (!email) {
                        return { isValid: false, error: 'Email is required.' };
                    }
                    if (email.length > EMAIL_MAX_LENGTH) {
                        return { isValid: false, error: 'Email must not exceed 100 characters.' };
                    }
                    // valid_email
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(email)) {
                        return { isValid: false, error: 'Please enter a valid email address.' };
                    }
                    // no_email_subaddress
                    if (email.includes('+') || /\.{2,}/.test(email)) {
                        return { isValid: false, error: 'Email addresses with plus signs or consecutive dots are not allowed.' };
                    }

                    return { isValid: true, error: null };
                }

                function validatePassword(password) {
                    if (!password) {
                        return { isValid: false, error: 'Password is required.' };
                    }
                    if (password.length < PASSWORD_MIN_LENGTH) {
                        return { isValid: false, error: 'Password must be at least 6 characters.' };
                    }
                    if (password.length > PASSWORD_MAX_LENGTH) {
                        return { isValid: false, error: 'Password must not exceed 255 characters.' };
                    }

                    return { isValid: true, error: null };
                }

                function validatePasswordConfirm(passwordConfirm) {
                    if (!passwordConfirm) {
                        return { isValid: false, error: 'Password confirmation is required.' };
                    }
                    if (passwordConfirm.length < PASSWORD_MIN_LENGTH) {
                        return { isValid: false, error: 'Password confirmation must be at least 6 characters.' };
                    }
                    if (passwordConfirm.length > PASSWORD_MAX_LENGTH) {
                        return { isValid: false, error: 'Password confirmation must not exceed 255 characters.' };
                    }

                    return { isValid: true, error: null };
                }

                function validatePasswordsMatch(password, passwordConfirm) {
                    if (password !== passwordConfirm) {
                        return { isValid: false, error: 'Passwords do not match.' };
                    }
                    return { isValid: true, error: null };
                }

                function showValidationError(errorMessage) {
                    const existingError = document.querySelector('.message.error');
                    if (existingError) existingError.remove();

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'message error';
                    errorDiv.textContent = errorMessage;
                    form.parentElement.insertBefore(errorDiv, form);

                    form.classList.remove('shake');
                    void form.offsetWidth; // reflow to restart animation
                    form.classList.add('shake');
                }

                // Shake on server-side flash error
                if (document.querySelector('.message.error')) {
                    form.classList.add('shake');
                }

                function clearValidationError() {
                    const existingError = document.querySelector('.message.error');
                    if (existingError && !existingError.textContent.includes('temporarily')) {
                        existingError.remove();
                    }
                }

                // Clear errors on input
                [firstNameInput, lastNameInput, usernameInput, emailInput, passwordInput, passwordConfirmInput]
                    .forEach((el) => el.addEventListener('input', clearValidationError));

                form.addEventListener('submit', function (e) {
                    clearValidationError();

                    const firstNameResult = validateFirstName(firstNameInput.value);
                    if (!firstNameResult.isValid) {
                        e.preventDefault();
                        showValidationError(firstNameResult.error);
                        firstNameInput.focus();
                        return;
                    }

                    const lastNameResult = validateLastName(lastNameInput.value);
                    if (!lastNameResult.isValid) {
                        e.preventDefault();
                        showValidationError(lastNameResult.error);
                        lastNameInput.focus();
                        return;
                    }

                    const usernameResult = validateUsername(usernameInput.value);
                    if (!usernameResult.isValid) {
                        e.preventDefault();
                        showValidationError(usernameResult.error);
                        usernameInput.focus();
                        return;
                    }

                    const emailResult = validateEmail(emailInput.value);
                    if (!emailResult.isValid) {
                        e.preventDefault();
                        showValidationError(emailResult.error);
                        emailInput.focus();
                        return;
                    }

                    const passwordResult = validatePassword(passwordInput.value);
                    if (!passwordResult.isValid) {
                        e.preventDefault();
                        showValidationError(passwordResult.error);
                        passwordInput.focus();
                        return;
                    }

                    const passwordConfirmResult = validatePasswordConfirm(passwordConfirmInput.value);
                    if (!passwordConfirmResult.isValid) {
                        e.preventDefault();
                        showValidationError(passwordConfirmResult.error);
                        passwordConfirmInput.focus();
                        return;
                    }

                    const matchResult = validatePasswordsMatch(passwordInput.value, passwordConfirmInput.value);
                    if (!matchResult.isValid) {
                        e.preventDefault();
                        showValidationError(matchResult.error);
                        passwordConfirmInput.focus();
                        return;
                    }

                    // Disable submit button to prevent double submission
                    submitButton.disabled = true;
                    submitButton.textContent = 'Registering...';
                });

                // Password visibility toggles
                [['pwd-toggle', 'password'], ['pwd-toggle-confirm', 'password_confirm']].forEach(([btnId, inputId]) => {
                    document.getElementById(btnId).addEventListener('click', function () {
                        const input = document.getElementById(inputId);
                        const isHidden = input.type === 'password';
                        input.type = isHidden ? 'text' : 'password';
                        this.querySelector('.eye-icon').style.display     = isHidden ? 'none' : '';
                        this.querySelector('.eye-off-icon').style.display = isHidden ? ''     : 'none';
                    });
                });
            </script>
        </main>

        <footer class="auth-footer">&copy; <?= date('Y') ?> GiaPhuc. All rights reserved.</footer>
        <script>
        (function () {
            const canvas = document.getElementById('auth-canvas');
            const ctx = canvas.getContext('2d');
            let t = Math.random() * 100;
            const CELL = 18;
            let COLS, ROWS;

            function resize() {
                canvas.width  = window.innerWidth;
                canvas.height = window.innerHeight;
                COLS = Math.ceil(canvas.width  / CELL) + 1;
                ROWS = Math.ceil(canvas.height / CELL) + 1;
            }
            resize();
            window.addEventListener('resize', resize);

            function noise(x, y, t) {
                return Math.sin(x * 2.1 + t * 0.4) * Math.cos(y * 1.7 - t * 0.3)
                     + Math.sin(x * 0.9 - t * 0.2 + y * 1.3) * 0.6
                     + Math.cos(x * 3.2 + y * 0.8 + t * 0.5) * 0.35;
            }

            const LEVELS = 6;
            let lastTs = 0;

            function tick(ts) {
                requestAnimationFrame(tick);
                if (document.hidden || ts - lastTs < 22) return;
                lastTs = ts;

                ctx.clearRect(0, 0, canvas.width, canvas.height);

                const grid = [];
                for (let r = 0; r <= ROWS; r++) {
                    grid[r] = [];
                    for (let c = 0; c <= COLS; c++) {
                        grid[r][c] = noise(c / COLS * 4, r / ROWS * 3, t);
                    }
                }

                const isLight = document.documentElement.getAttribute('data-theme') === 'light';
                for (let l = 0; l < LEVELS; l++) {
                    const level = -1.3 + (l / (LEVELS - 1)) * 2.6;
                    const alpha = l % 3 === 0 ? 0.22 : 0.12;
                    const lw    = isLight ? (l % 3 === 0 ? 1.8 : 1.2) : (l % 3 === 0 ? 1.2 : 0.8);

                    ctx.beginPath();
                    ctx.strokeStyle = isLight ? `rgba(29,78,216,${alpha})` : `rgba(56,189,248,${alpha})`;
                    ctx.lineWidth = lw;

                    for (let r = 0; r < ROWS; r++) {
                        for (let c = 0; c < COLS; c++) {
                            const x0 = c * CELL, x1 = x0 + CELL;
                            const y0 = r * CELL, y1 = y0 + CELL;
                            const v00 = grid[r][c],   v10 = grid[r][c+1];
                            const v01 = grid[r+1][c], v11 = grid[r+1][c+1];
                            const pts = [];

                            if ((v00 < level) !== (v10 < level))
                                pts.push([x0 + (level - v00) / (v10 - v00) * CELL, y0]);
                            if ((v10 < level) !== (v11 < level))
                                pts.push([x1, y0 + (level - v10) / (v11 - v10) * CELL]);
                            if ((v01 < level) !== (v11 < level))
                                pts.push([x0 + (level - v01) / (v11 - v01) * CELL, y1]);
                            if ((v00 < level) !== (v01 < level))
                                pts.push([x0, y0 + (level - v00) / (v01 - v00) * CELL]);

                            if (pts.length >= 2) {
                                ctx.moveTo(pts[0][0], pts[0][1]);
                                ctx.lineTo(pts[1][0], pts[1][1]);
                            }
                        }
                    }
                    ctx.stroke();
                }

                t += 0.003;
            }
            requestAnimationFrame(tick);
        })();
        </script>
    </body>
</html>