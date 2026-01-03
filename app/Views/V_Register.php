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
                    <input type="email" id="email" name="email" placeholder=" " required>
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
                
            </script>
        </main>

        <!-- Footer -->
         <footer>
            <p>Powered by <a href="https://codeigniter.com" target="_blank">CodeIgniter 4</a> and <a href="https://www.binance.com" target="_blank">Binance API</a>.</p>
            <p>&copy; <?php echo date("Y", time())?>. All rights reserved.</p>
        </footer>
    </body>
</html>