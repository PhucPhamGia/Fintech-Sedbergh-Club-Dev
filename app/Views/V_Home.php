<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/home.css'); ?>">
</head>
<body>
    <header>
        <h1>Welcome to Our Application</h1>
        <nav>
            <ul>
                <li><a href="<?= site_url('/') ?>">Home</a></li>
                <li><a href="<?= site_url('/login') ?>">Login</a></li>
                <li><a href="<?= site_url('/register') ?>">Register</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="intro">
            <h2>Your Gateway to Efficient Data Management</h2>
            <p>Explore our platform to manage your data seamlessly and securely. Join us today!</p>
            <a href="<?= site_url('/register') ?>" class="btn-primary">Get Started</a>
        </section>
    </main>
    
    <!-- Footer -->
    <?= view('V_Footer') ?>
</body>
</html>