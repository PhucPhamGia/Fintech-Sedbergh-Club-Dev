<!DOCTYPE HTML>
<html lang="en">
<head>
  <?= view('V_Head') ?>
  <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark');</script>
  <title>Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Space+Grotesk:wght@300..700&family=JetBrains+Mono:wght@400;700&display=swap">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css'); ?>">
</head>
<body>

  <!-- Top-right controls -->
  <div class="top-controls">
    <button id="theme-toggle" class="btn-theme" aria-label="Toggle theme">
      <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
      </svg>
      <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
      </svg>
    </button>
    <div class="nav-profile-wrap" id="profileWrap">
      <?php
        $email       = trim(session()->get('email') ?? '');
        $displayName = trim(session()->get('display_name') ?? '');
        $username    = session()->get('username') ?? 'U';
        $showName    = $displayName !== '' ? $displayName : $username;
        $nameParts   = array_values(array_filter(explode(' ', $showName)));
        $initials    = count($nameParts) >= 2
            ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
            : strtoupper(substr($nameParts[0] ?? 'U', 0, 2));
      ?>
      <button class="nav-avatar" id="profileBtn" aria-label="Account menu">
        <?php if ($email !== ''): ?>
          <img src="https://www.gravatar.com/avatar/<?= md5(strtolower($email)) ?>?s=64&d=404"
               onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
               alt="" width="34" height="34">
          <span class="nav-avatar-initials" style="display:none"><?= esc($initials) ?></span>
        <?php else: ?>
          <span class="nav-avatar-initials"><?= esc($initials) ?></span>
        <?php endif; ?>
      </button>
      <div class="profile-dropdown" id="profileDropdown">
        <?php $role = session()->get('role') ?? 'User'; ?>
        <div class="dropdown-username">
          <?= esc($showName) ?>
          <?php if ($role === 'Admin'): ?>
          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;position:relative;top:-0.5px;margin-left:4px;flex-shrink:0"><path d="m15 12-8.5 8.5a2.12 2.12 0 1 1-3-3L12 9"/><path d="M17.64 15 22 10.64"/><path d="m20.91 11.7-1.25-1.25c-.6-.6-.93-1.4-.93-2.25v-.86L16.01 4.6a5.56 5.56 0 0 0-3.94-1.64H9l.92.82A6.18 6.18 0 0 1 12 8.4v1.56l2 2h2.47l2.26 1.91"/></svg>
          <?php endif; ?>
        </div>
        <span class="dropdown-role role-<?= strtolower(esc($role)) ?>"><?= esc($role) ?></span>
        <hr class="profile-dropdown-divider">
        <a href="<?= site_url('/') ?>" class="profile-dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
          Home
        </a>
        <a href="<?= site_url('database/' . ($coins[0]['id_coin'] ?? 1) . '/1h/100') ?>" class="profile-dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
          Markets
        </a>
        <hr class="profile-dropdown-divider">
        <a href="#" class="profile-dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
          Profile
        </a>
        <a href="#" class="profile-dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
          Settings
        </a>
        <hr class="profile-dropdown-divider">
        <button class="profile-dropdown-item" id="logout-link">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15"/></svg>
          Log out
        </button>
      </div>
    </div>
  </div>

  <!-- Background depth -->
  <div class="bg-orb bg-orb-1"></div>
  <div class="bg-orb bg-orb-2"></div>

  <!-- Dashboard content -->
  <div class="dashboard-grid">
    <div class="chart-box">
      <div class="chart-header">
        <div class="chart-meta">
          <div class="chart-symbol" id="chartSymbol"><?= esc(str_replace('USDT', '', $coins[0]['coinname'] ?? 'BTC') . '/USDT') ?></div>
          <div class="chart-price-row">
            <span class="chart-price" id="chartPrice">—</span>
            <span class="chart-change" id="chartChange"></span>
          </div>
        </div>
        <div class="seg-control" id="time-seg">
          <div class="seg-indicator"></div>
          <button class="seg-btn" data-range="1w">1W</button>
          <button class="seg-btn" data-range="1m">1M</button>
          <button class="seg-btn active" data-range="3m">3M</button>
          <button class="seg-btn" data-range="6m">6M</button>
        </div>
      </div>
      <div style="position:relative;height:300px;">
        <canvas id="secondChart"></canvas>
      </div>
    </div>

    <!-- Coin Widgets -->
    <?php foreach ($coins as $i => $coin):
      $sym    = $coin['coinname'];
      $slug   = strtolower(str_replace('USDT', '', $sym));
      $pair   = str_replace('USDT', '', $sym) . '/USDT';
    ?>
    <button class="crypto-widget-btn<?= $i === 0 ? ' selected' : '' ?>" id="<?= $slug ?>-widget" onclick="selectCoin('<?= esc($sym) ?>')">
      <div class="crypto-title"><?= esc($pair) ?></div>
      <div class="main-price" id="<?= $slug ?>-price">—</div>
      <div class="widget-change" id="<?= $slug ?>-change"></div>
    </button>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // =========================
    // Controls
    // =========================
    document.addEventListener('DOMContentLoaded', function () {
      // Theme toggle
      document.getElementById('theme-toggle').addEventListener('click', function () {
        const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
        if (secondChart) {
          const isLight   = next === 'light';
          const tickColor = isLight ? '#475569' : '#b0bec5';
          const gridColor = isLight ? '#cbd5e1' : '#1e3048';
          secondChart.options.scales.x.grid.color  = gridColor;
          secondChart.options.scales.x.ticks.color = tickColor;
          secondChart.options.scales.y.grid.color  = gridColor;
          secondChart.options.scales.y.ticks.color = tickColor;
          secondChart.update();
        }
      });

      // Profile dropdown
      const profileBtn      = document.getElementById('profileBtn');
      const profileDropdown = document.getElementById('profileDropdown');
      profileBtn.addEventListener('click', e => { e.stopPropagation(); profileDropdown.classList.toggle('show'); });
      document.addEventListener('click', () => profileDropdown.classList.remove('show'));
      profileDropdown.addEventListener('click', e => e.stopPropagation());

      // Logout
      document.getElementById('logout-link').addEventListener('click', function () {
        window.location.href = '<?= site_url('logout') ?>';
      });
    });

    // =========================
    // Chart.js Setup
    // =========================
    const ctx2 = document.getElementById('secondChart').getContext('2d');
    let secondChart = null;
    let selectedSymbol = <?= json_encode($coins[0]['coinname'] ?? 'BTCUSDT') ?>;
    let selectedRange  = "3m";

    function loadBinanceChartByRange(range, symbol) {
      selectedRange  = range;
      selectedSymbol = symbol || selectedSymbol;

      const now = Date.now();
      let days = 7;
      switch (range) {
        case '1m': days = 30;  break;
        case '3m': days = 90;  break;
        case '6m': days = 180; break;
        default:   days = 7;   break;
      }

      const startTime = now - days * 86400000;
      const url = `https://api.binance.com/api/v3/klines?symbol=${selectedSymbol}&interval=1d&startTime=${startTime}&endTime=${now}&limit=${days}`;

      fetch(url)
        .then(res => res.json())
        .then(data => {
          if (!Array.isArray(data)) throw new Error("Binance API error");

          const chartLabels = data.map(e => {
            const d = new Date(e[0]);
            return `${d.getMonth() + 1}/${d.getDate()}`;
          });
          const closePrices = data.map(e => parseFloat(e[4]));

          if (secondChart) secondChart.destroy();

          const isLight   = document.documentElement.getAttribute('data-theme') === 'light';
          const tickColor = isLight ? '#475569' : '#b0bec5';
          const gridColor = isLight ? '#cbd5e1' : '#1e3048';

          const gradient = ctx2.createLinearGradient(0, 0, 0, ctx2.canvas.clientHeight || 300);
          gradient.addColorStop(0, 'rgba(32,179,231,0.18)');
          gradient.addColorStop(1, 'rgba(32,179,231,0)');

          secondChart = new Chart(ctx2, {
            type: 'line',
            data: {
              labels: chartLabels,
              datasets: [{
                label: selectedSymbol + ` (${range.toUpperCase()})`,
                data: closePrices,
                borderColor: "#20b3e7",
                borderWidth: 3,
                pointRadius: 1.5,
                pointBackgroundColor: "#20b3e7",
                fill: true,
                backgroundColor: gradient,
                tension: 0.34
              }]
            },
            options: {
              plugins: { legend: { display: false } },
              scales: {
                x: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 12, family: "'JetBrains Mono', monospace" }, padding: 16 } },
                y: {
                  min: Math.min(...closePrices) * 0.97,
                  max: Math.max(...closePrices) * 1.03,
                  grid: { color: gridColor },
                  ticks: { color: tickColor, font: { size: 12, family: "'JetBrains Mono', monospace" }, padding: 18 }
                }
              },
              layout: { padding: { top: 22, right: 22, bottom: 18, left: 22 } },
              animation: false,
              responsive: true,
              maintainAspectRatio: false
            }
          });
        })
        .catch(() => {
          ctx2.font = "16px sans-serif";
          ctx2.fillStyle = "#fff";
          ctx2.fillText("Failed to load Binance data.", 50, 50);
        });
    }

    // Segmented control
    (function () {
      const seg  = document.getElementById('time-seg');
      const ind  = seg.querySelector('.seg-indicator');
      const btns = seg.querySelectorAll('.seg-btn');

      function moveIndicator(btn) {
        ind.style.width     = btn.offsetWidth + 'px';
        ind.style.transform = `translateX(${btn.offsetLeft - 3}px)`;
      }

      const active = seg.querySelector('.seg-btn.active');
      if (active) moveIndicator(active);

      btns.forEach(btn => btn.addEventListener('click', () => {
        btns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        moveIndicator(btn);
        loadBinanceChartByRange(btn.dataset.range, selectedSymbol);
      }));
    })();

    function selectCoin(symbol) {
      document.querySelectorAll('.crypto-widget-btn').forEach(b => b.classList.remove('selected'));
      document.getElementById(symbol.toLowerCase().replace('usdt', '') + '-widget').classList.add('selected');
      loadBinanceChartByRange(selectedRange, symbol);
      updateChartHeader(symbol);
    }

    const coinSymbols = <?= json_encode(array_column($coins, 'coinname')) ?>;

    function updateChartHeader(symbol) {
      fetch(`https://api.binance.com/api/v3/ticker/24hr?symbol=${symbol}`)
        .then(res => res.json())
        .then(data => {
          const price  = parseFloat(data.lastPrice);
          const pct    = parseFloat(data.priceChangePercent);
          const base   = symbol.replace('USDT', '');
          document.getElementById('chartSymbol').textContent = base + '/USDT';
          document.getElementById('chartPrice').textContent  = '$' + price.toLocaleString('en-US', { minimumFractionDigits: 2 });
          const chEl = document.getElementById('chartChange');
          chEl.textContent = (pct >= 0 ? '+' : '') + pct.toFixed(2) + '%';
          chEl.className   = 'chart-change ' + (pct >= 0 ? 'up' : 'down');
        })
        .catch(() => {});
    }

    function updateWidgetPrices() {
      coinSymbols.forEach(symbol => {
        fetch(`https://api.binance.com/api/v3/ticker/24hr?symbol=${symbol}`)
          .then(res => res.json())
          .then(data => {
            const slug    = symbol.toLowerCase().replace('usdt', '');
            const priceEl = document.getElementById(slug + '-price');
            const chEl    = document.getElementById(slug + '-change');
            if (priceEl) priceEl.textContent = '$' + parseFloat(data.lastPrice).toLocaleString('en-US', { minimumFractionDigits: 2 });
            if (chEl) {
              const pct = parseFloat(data.priceChangePercent);
              chEl.textContent = (pct >= 0 ? '+' : '') + pct.toFixed(2) + '%';
              chEl.className   = 'widget-change ' + (pct >= 0 ? 'up' : 'down');
            }
          })
          .catch(() => {});
      });
    }

    updateWidgetPrices();
    updateChartHeader(selectedSymbol);
    loadBinanceChartByRange(selectedRange, selectedSymbol);
    setInterval(updateWidgetPrices, 30000);
    setInterval(() => updateChartHeader(selectedSymbol), 30000);
    </script>
  </div> <!-- /.dashboard-grid -->

  <!-- Footer -->
  <?= view('V_Footer') ?>
</body>
</html>
