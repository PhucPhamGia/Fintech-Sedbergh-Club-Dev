<!-- 
Redirect into login page if user is already logged in (cookie)

Use <form> to submit login data (POST method) to Auth controller
-->

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <?= view('V_Head') ?>
        <title>Sign in</title>
        <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
    </head>
    <body>
        <!-- Sign In Section -->
        <canvas id="auth-canvas"></canvas>
        <main id="signin">
            <!-- Title -->
            <h1>Welcome back, trader.</h1>
            <p class="auth-sub">Your charts are waiting.</p>

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

                <label class="remember-row"><input type="checkbox" name="remember" id="remember"> Remember me</label>
                
                <div class="form-links">
                    <a href="<?= site_url('login/forgot-password') ?>" id="fgpass">Forgot Password?</a>
                    <a href="<?= site_url('register') ?>">Don't have an account?</a>
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

                // Password visibility toggle
                document.getElementById('pwd-toggle').addEventListener('click', function () {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    this.querySelector('.eye-icon').style.display     = isHidden ? 'none'  : '';
                    this.querySelector('.eye-off-icon').style.display = isHidden ? ''      : 'none';
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