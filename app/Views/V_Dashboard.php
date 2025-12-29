<!--
Yo, write sum comments dawg
-->

<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body>
  <!-- Circle Dropdown Menu -->
  <div class="circle-menu-container">
    <div class="circle-menu-wrapper">
    <!-- Circle Menu Red Button -->
      <button class="circle-menu-btn" id="circleMenuBtn" aria-label="Open menu">
        <span class="dots">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </button>
      <span class="menu-label">Menu</span>

    </div>
    <!-- Circle Dropdown Content -->
    <div class="circle-dropdown" id="circleDropdown">
      <form style="margin: 0;">
        <button class="simple-btn">Program is in development</button>
      </form>

      <!-- Dropdown Boxes -->
      <hr style="border: 0.5px solid #2a3d59; margin: 6px 0;">
      <a href="<?= site_url('public/database/1/50') ?>">Database</a>
      <!-- <a href="<= site_url('login') ?>">Login</a> -->
    </div>
  </div>


  <!-- Line chart time interval buttons -->
  <div class="dashboard-grid">
    <div class="chart-box">
      <div id="second-time-selector" style="position: absolute; top: 12px; left: 24px; z-index: 10;">
        <button onclick="changeTimeRange('1w')">1W</button>
        <button onclick="changeTimeRange('1m')">1M</button>
        <button onclick="changeTimeRange('3m')">3M</button>
        <button onclick="changeTimeRange('6m')">6M</button>
      </div>
      
      <canvas id="secondChart" width="1000" height="300"></canvas>
  </div>
  
  <!-- Coin Widgets -->
  <button class="crypto-widget-btn selected" id="btc-widget" onclick="selectCoin('BTCUSDT')">
    <div class="crypto-title">BTC/USDT</div>
    <div class="main-price" id="btc-price">Loading...</div>
  </button>
  <button class="crypto-widget-btn" id="eth-widget" onclick="selectCoin('ETHUSDT')">
    <div class="crypto-title">ETH/USDT</div>
    <div class="main-price" id="eth-price">Loading...</div>
  </button>
  <button class="crypto-widget-btn" id="bnb-widget" onclick="selectCoin('BNBUSDT')">
    <div class="crypto-title">BNB/USDT</div>
    <div class="main-price" id="bnb-price">Loading...</div>
  </button>
  <button class="crypto-widget-btn" id="sol-widget" onclick="selectCoin('SOLUSDT')">
    <div class="crypto-title">SOL/USDT</div>
    <div class="main-price" id="sol-price">Loading...</div>
  </button>


  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // =========================
    // Dropdown Menu + Logout
    // =========================
    document.addEventListener('DOMContentLoaded', function () {
      const btn = document.getElementById('circleMenuBtn');
      const dropdown = document.getElementById('circleDropdown');

      // Toggle dropdown
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function () {
        dropdown.classList.remove('show');
      });

      // Prevent dropdown from closing on internal click
      dropdown.addEventListener('click', function (e) {
        e.stopPropagation();
      });

      // Logout button → submit hidden form
      document.getElementById('logout-link').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('logout-form').submit();
      });
    });

    // =========================
    // Chart.js Setup
    // =========================
    const ctx2 = document.getElementById('secondChart').getContext('2d');
    let secondChart = null;
    let selectedSymbol = "BTCUSDT"; // Default coin
    let selectedRange = "3m";       // Default time range

    // =========================
    // Load Chart Data from Binance
    // =========================
    function loadBinanceChartByRange(range, symbol) {
      selectedRange = range;
      selectedSymbol = symbol || selectedSymbol;

      const now = Date.now();
      let days = 7;

      // Map selected range → number of days
      switch (range) {
        case '1m': days = 30; break;
        case '3m': days = 90; break;
        case '6m': days = 180; break;
        default: days = 7; break;
      }

      const oneDayMs = 24 * 60 * 60 * 1000;
      const startTime = now - (days * oneDayMs);

      // Binance API URL for Klines
      const url = `https://api.binance.com/api/v3/klines?symbol=${selectedSymbol}&interval=1d&startTime=${startTime}&endTime=${now}&limit=${days}`;

      fetch(url)
        .then(res => res.json())
        .then(data => {
          // The output is a JSON array, where each element is an array representing a candlestick (kline).
          // Example:
          // [
          //   [
          //     1499040000000,      // Open time (ms)
          //     "0.01634790",       // Open
          //     "0.80000000",       // High
          //     "0.01575800",       // Low
          //     "0.01577100",       // Close
          //     "148976.11427815",  // Volume
          //     1499644799999,      // Close time (ms)
          //     "2434.19055334",    // Quote asset volume
          //     308,                // Number of trades
          //     "1756.87402397",    // Taker buy base asset volume
          //     "28.46694368",      // Taker buy quote asset volume
          //     "17928899.62484339" // Ignore
          //   ],
          //   ...
          // ]
          if (!Array.isArray(data)) throw new Error("Binance API error");

          // Extract labels and closing prices
          const chartLabels = data.map(entry => {
        const date = new Date(entry[0]);
        return `${date.getMonth() + 1}/${date.getDate()}`;
          });
          const closePrices = data.map(entry => parseFloat(entry[4]));

          // Destroy old chart before creating new one
          if (secondChart) secondChart.destroy();

          // Create line chart
          secondChart = new Chart(ctx2, {
            type: 'line',
            data: {
              labels: chartLabels,
              datasets: [{
                label: selectedSymbol + ` Closing Price (${range.toUpperCase()})`,
                data: closePrices,
                borderColor: "#20b3e7",
                borderWidth: 4,
                pointRadius: 1.5,
                pointBackgroundColor: "#20b3e7",
                fill: false,
                tension: 0.34
              }]
            },
            options: {
              plugins: { legend: { display: false }},
              scales: {
                x: {
                  grid: { color: "#253042" },
                  ticks: { color: "#b0bec5", font: { size: 15 }, padding: 16 }
                },
                y: {
                  min: Math.min(...closePrices) * 0.97,
                  max: Math.max(...closePrices) * 1.03,
                  grid: { color: "#253042" },
                  ticks: { color: "#b0bec5", font: { size: 15 }, padding: 18 }
                }
              },
              layout: { padding: { top: 22, right: 22, bottom: 18, left: 22 } },
              animation: false,
              responsive: false,
              maintainAspectRatio: false
            }
          });
        })
        .catch(err => {
          // Error fallback: draw message on canvas
          ctx2.font = "16px sans-serif";
          ctx2.fillStyle = "#fff";
          ctx2.fillText("Failed to load Binance data.", 50, 50);
        });
    }

    // =========================
    // Chart Controls
    // =========================
    // Change time range (1w, 1m, 3m, etc.)
    function changeTimeRange(range) {
      loadBinanceChartByRange(range, selectedSymbol);
    }

    // Switch between different coins (BTC, ETH, etc.)
    function selectCoin(symbol) {
      document.querySelectorAll('.crypto-widget-btn').forEach(btn => btn.classList.remove('selected'));
      document.getElementById(symbol.toLowerCase().replace('usdt', '') + '-widget').classList.add('selected');
      loadBinanceChartByRange(selectedRange, symbol);
    }

    // =========================
    // Live Widget Prices
    // =========================
    function updateWidgetPrices() {
      const symbols = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'SOLUSDT'];
      symbols.forEach(symbol => {
        fetch(`https://api.binance.com/api/v3/ticker/price?symbol=${symbol}`)
          .then(res => res.json())
          .then(data => {
            const id = symbol.toLowerCase().replace('usdt', '') + '-price';
            const price = parseFloat(data.price).toLocaleString('en-US', { minimumFractionDigits: 2 });
            document.getElementById(id).textContent = `$${price}`;
          })
          .catch(err => console.error(`Error fetching ${symbol}:`, err));
      });
    }

    // =========================
    // Init (on page load)
    // =========================
    updateWidgetPrices();                        // Load prices immediately
    loadBinanceChartByRange(selectedRange, selectedSymbol); // Load default chart
    setInterval(updateWidgetPrices, 60000);      // Refresh prices every 60s
  </script>
</body>
</html>