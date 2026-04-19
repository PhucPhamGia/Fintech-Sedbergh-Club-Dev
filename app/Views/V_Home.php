<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/home.css'); ?>">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="<?= site_url('/') ?>" class="navbar-brand">CryptoTracker</a>
        <div class="navbar-links">
            <a href="<?= site_url('/login') ?>" class="btn-outline">Login</a>
            <a href="<?= site_url('/register') ?>" class="btn-primary">Register</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Track Crypto Markets <span class="highlight">In Real Time</span></h1>
        <p>Monitor live prices, analyze candlestick charts with moving averages, and make informed trading decisions — all in one dashboard.</p>
        <div class="hero-buttons">
            <a href="<?= site_url('/register') ?>" class="btn-hero-primary">Get Started</a>
            <a href="<?= site_url('/login') ?>" class="btn-hero-secondary">Sign In</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <h2>What You Get</h2>
        <div class="features-grid">
            <div class="feature-card">
                <span class="feature-icon">&#x1F4C8;</span>
                <h3>Live Price Charts</h3>
                <p>Interactive line charts powered by real-time Binance data with customizable time ranges.</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">&#x1F56F;</span>
                <h3>Candlestick Analysis</h3>
                <p>Detailed candlestick charts with MA20 and MA50 moving averages for technical analysis.</p>
            </div>
            <div class="feature-card">
                <span class="feature-icon">&#x1F4B0;</span>
                <h3>Multi-Coin Tracking</h3>
                <p>Track BTC, ETH, BNB, SOL and more — switch between coins instantly on the dashboard.</p>
            </div>
        </div>
    </section>

    <!-- Live Prices Preview -->
    <section class="prices-section">
        <h2>Live Prices</h2>
        <div class="prices-grid">
            <div class="price-card">
                <div class="coin-name">BTC/USDT</div>
                <div class="coin-price" id="btc-price">Loading...</div>
            </div>
            <div class="price-card">
                <div class="coin-name">ETH/USDT</div>
                <div class="coin-price" id="eth-price">Loading...</div>
            </div>
            <div class="price-card">
                <div class="coin-name">BNB/USDT</div>
                <div class="coin-price" id="bnb-price">Loading...</div>
            </div>
            <div class="price-card">
                <div class="coin-name">SOL/USDT</div>
                <div class="coin-price" id="sol-price">Loading...</div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-box">
            <h2>Ready to Start Tracking?</h2>
            <p>Create a free account and get access to the full dashboard with live charts and data analysis tools.</p>
            <a href="<?= site_url('/register') ?>">Create Account</a>
        </div>
    </section>

    <!-- Live Price Script -->
    <script>
        function updatePrices() {
            const symbols = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'SOLUSDT'];
            symbols.forEach(function(symbol) {
                fetch('https://api.binance.com/api/v3/ticker/price?symbol=' + symbol)
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        var id = symbol.toLowerCase().replace('usdt', '') + '-price';
                        var price = parseFloat(data.price).toLocaleString('en-US', { minimumFractionDigits: 2 });
                        document.getElementById(id).textContent = '$' + price;
                    })
                    .catch(function(err) {
                        console.error('Error fetching ' + symbol + ':', err);
                    });
            });
        }

        updatePrices();
        setInterval(updatePrices, 60000);
    </script>

    <!-- Footer -->
    <?= view('V_Footer') ?>
</body>
</html>
