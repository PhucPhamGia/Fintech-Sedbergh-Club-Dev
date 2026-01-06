<!-- 
Use <form> to submit register data (POST method) to Auth controller
-->

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <link rel="stylesheet" href="<?= base_url('assets/css/register.css'); ?>">
    </head>
    <body>
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
                </div>

                <div class="input-group">
                    <input type="password" id="password_confirm" name="password_confirm" placeholder=" " required>
                    <span class="input-label">Confirm Password</span>
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
            </script>
        </main>

        <!-- Footer -->
        <?= view('V_Footer') ?>
    </body>
</html>