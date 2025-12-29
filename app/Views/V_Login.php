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
        <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
    </head>
    <body>
        <!-- Sign In Section -->
        <main id="signin">
            <!-- Title -->
            <h1>Sign In</h1>

            <!-- Error/Success Message -->
            <?php if (session()->getFlashdata('message')): ?>
                <div class="message">
                    <?= session()->getFlashdata('message') ?>
                </div>
            <?php endif;
            session()->remove('message');
            ?>

            <!-- Sign In Form -->
            <form action="<?= site_url('auth/login') ?>" method="POST" id="form">
                <div class="input-group">
                    <input type="text" name="username" id="password" value="<?= $savedUser ?>" placeholder=" " required>
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
        </main>

        <!-- Footer -->
         <footer>
            <p>Powered by <a href="https://codeigniter.com" target="_blank">CodeIgniter 4</a> and <a href="https://www.binance.com" target="_blank">Binance API</a>.</p>
            <p>&copy; <?php echo date("Y", time())?>. All rights reserved.</p>
        </footer>
    </body>
</html>