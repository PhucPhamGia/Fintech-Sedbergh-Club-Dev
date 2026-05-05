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
  <style>
    /* Google Charts tooltip override */
    div.google-visualization-tooltip {
      background: #151d2f !important;
      border: 1px solid rgba(255,255,255,0.08) !important;
      border-radius: 8px !important;
      color: #94a3b8 !important;
      font-family: 'Plus Jakarta Sans', sans-serif !important;
      font-size: 12px !important;
      padding: 8px 12px !important;
      box-shadow: 0 8px 24px rgba(0,0,0,0.5) !important;
    }
    div.google-visualization-tooltip span,
    div.google-visualization-tooltip table,
    div.google-visualization-tooltip td {
      color: #e2e8f0 !important;
      font-family: 'JetBrains Mono', monospace !important;
      font-size: 11px !important;
    }
    html[data-theme="light"] div.google-visualization-tooltip {
      background: #ffffff !important;
      border-color: rgba(0,0,0,0.08) !important;
      box-shadow: 0 4px 16px rgba(0,0,0,0.12) !important;
    }
    html[data-theme="light"] div.google-visualization-tooltip span,
    html[data-theme="light"] div.google-visualization-tooltip td {
      color: #334155 !important;
    }
  </style>
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
        <?php if ($role === 'Admin'): ?>
        <hr class="profile-dropdown-divider">
        <div class="dropdown-section-label">Admin</div>
        <a href="<?= site_url('admin/achievements') ?>" class="profile-dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
          Achievement Lab
        </a>
        <a href="<?= site_url('database/' . ($coins[0]['id_coin'] ?? 1) . '/1h/100') ?>" class="profile-dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
          Markets
        </a>
        <?php endif; ?>
        <hr class="profile-dropdown-divider">
        <a href="<?= site_url('/') ?>" class="profile-dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
          Home
        </a>
        <hr class="profile-dropdown-divider">
        <a href="<?= site_url('profile') ?>" class="profile-dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
          Profile
        </a>
        <a href="#" class="profile-dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0"/></svg>
          Achievements
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
            <span class="chart-price" id="chartPrice"><span class="skeleton skel-chart-price"></span></span>
            <span class="chart-change" id="chartChange"><span class="skeleton skel-chart-change"></span></span>
          </div>
        </div>
        <div class="seg-control" id="time-seg">
          <div class="seg-indicator"></div>
          <button class="seg-btn" data-tf="15m">15M</button>
          <button class="seg-btn" data-tf="30m">30M</button>
          <button class="seg-btn active" data-tf="1h">1H</button>
          <button class="seg-btn" data-tf="4h">4H</button>
          <button class="seg-btn" data-tf="6h">6H</button>
          <button class="seg-btn" data-tf="12h">12H</button>
        </div>
      </div>
      <div id="chart_div" style="width:100%;height:300px;"></div>
    </div>

    <!-- Coin Widgets -->
    <?php foreach ($coins as $i => $coin):
      $sym    = $coin['coinname'];
      $slug   = strtolower(str_replace('USDT', '', $sym));
      $pair   = str_replace('USDT', '', $sym) . '/USDT';
    ?>
    <button class="crypto-widget-btn<?= $i === 0 ? ' selected' : '' ?>" id="<?= $slug ?>-widget" onclick="selectCoin('<?= esc($sym) ?>')">
      <div class="crypto-title"><?= esc($pair) ?></div>
      <div class="main-price" id="<?= $slug ?>-price"><span class="skeleton skel-price"></span></div>
      <div class="widget-change" id="<?= $slug ?>-change"><span class="skeleton skel-change"></span></div>
    </button>
    <?php endforeach; ?>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
    // #Controls
    document.addEventListener('DOMContentLoaded', function () {
      // Theme toggle — redraw chart so axis colors update
      document.getElementById('theme-toggle').addEventListener('click', function () {
        var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
        if (currentChartData) drawDashChart(currentChartData.table, currentChartData.ma20, currentChartData.ma50);
      });

      // Profile dropdown
      var profileBtn      = document.getElementById('profileBtn');
      var profileDropdown = document.getElementById('profileDropdown');
      profileBtn.addEventListener('click', function(e) { e.stopPropagation(); profileDropdown.classList.toggle('show'); });
      document.addEventListener('click', function() { profileDropdown.classList.remove('show'); });
      profileDropdown.addEventListener('click', function(e) { e.stopPropagation(); });

      // Logout
      document.getElementById('logout-link').addEventListener('click', function () {
        window.location.href = '<?= site_url('logout') ?>';
      });
    });

    // #Chart
    var chartIsReady    = false;
    var currentChartData = null;
    var selectedTf      = '1h';
    var selectedSymbol  = <?= json_encode($coins[0]['coinname'] ?? 'BTCUSDT') ?>;
    var coinIdMap       = <?= json_encode(array_column($coins, 'id_coin', 'coinname')) ?>;

    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(function () {
      chartIsReady = true;
      drawDashChart(<?= $chartTable ?>, <?= $chartMa20 ?>, <?= $chartMa50 ?>);
    });

    function drawDashChart(ohlcData, ma20Data, ma50Data) {
      if (!chartIsReady) return;
      currentChartData = { table: ohlcData, ma20: ma20Data, ma50: ma50Data };

      var ma20Map = {}, ma50Map = {};
      ma20Data.forEach(function(r) { if (r[1] !== null) ma20Map[r[0]] = r[1]; });
      ma50Data.forEach(function(r) { if (r[1] !== null) ma50Map[r[0]] = r[1]; });

      var data = new google.visualization.DataTable();
      data.addColumn('datetime', 'Date');
      data.addColumn('number', 'Low');
      data.addColumn('number', 'Open');
      data.addColumn('number', 'Close');
      data.addColumn('number', 'High');
      data.addColumn('number', 'MA20');
      data.addColumn('number', 'MA50');

      ohlcData.forEach(function(r) {
        data.addRow([
          new Date(r[0]),
          r[1], r[2], r[3], r[4],
          ma20Map[r[0]] !== undefined ? ma20Map[r[0]] : null,
          ma50Map[r[0]] !== undefined ? ma50Map[r[0]] : null
        ]);
      });

      var isLight   = document.documentElement.getAttribute('data-theme') === 'light';
      var tickColor = isLight ? '#64748b' : '#94a3b8';
      var gridColor = isLight ? '#d1d9e6' : '#1a2744';
      var bgColor   = isLight ? '#e6eaef' : '#151d2f';

      var options = {
        fontName: 'Plus Jakarta Sans',
        backgroundColor: bgColor,
        legend: { position: 'bottom', textStyle: { color: tickColor, fontSize: 11, fontName: 'Plus Jakarta Sans' } },
        hAxis: { textStyle: { color: tickColor, fontSize: 11, fontName: 'JetBrains Mono' }, gridlines: { color: gridColor }, baselineColor: gridColor },
        vAxis: { textStyle: { color: tickColor, fontSize: 11, fontName: 'JetBrains Mono' }, gridlines: { color: gridColor }, baselineColor: gridColor },
        chartArea: { backgroundColor: bgColor, left: 70, right: 20, top: 16, bottom: 50 },
        interpolateNulls: true,
        seriesType: 'candlesticks',
        series: {
          0: { type: 'candlesticks', visibleInLegend: false },
          1: { type: 'line', color: '#34D399', lineWidth: 1.5, labelInLegend: 'MA20' },
          2: { type: 'line', color: '#38BDF8', lineWidth: 1.5, labelInLegend: 'MA50' }
        },
        candlestick: {
          fallingColor: { strokeWidth: 1, fill: '#F87171', stroke: '#F87171' },
          risingColor:  { strokeWidth: 1, fill: '#34D399', stroke: '#34D399' }
        }
      };

      new google.visualization.ComboChart(document.getElementById('chart_div')).draw(data, options);
    }

    function loadChart(tf) {
      selectedTf = tf;
      fetch('<?= site_url('api/chart/') ?>' + coinIdMap[selectedSymbol] + '/' + tf)
        .then(function(res) { return res.json(); })
        .then(function(d) { drawDashChart(d.table, d.ma20, d.ma50); })
        .catch(function() {});
    }

    // #Segmented control
    (function () {
      var seg  = document.getElementById('time-seg');
      var ind  = seg.querySelector('.seg-indicator');
      var btns = seg.querySelectorAll('.seg-btn');

      function moveIndicator(btn) {
        ind.style.width     = btn.offsetWidth + 'px';
        ind.style.transform = 'translateX(' + (btn.offsetLeft - 3) + 'px)';
      }

      var active = seg.querySelector('.seg-btn.active');
      if (active) moveIndicator(active);

      btns.forEach(function(btn) {
        btn.addEventListener('click', function() {
          btns.forEach(function(b) { b.classList.remove('active'); });
          btn.classList.add('active');
          moveIndicator(btn);
          loadChart(btn.dataset.tf);
        });
      });
    })();

    function selectCoin(symbol) {
      document.querySelectorAll('.crypto-widget-btn').forEach(function(b) { b.classList.remove('selected'); });
      document.getElementById(symbol.toLowerCase().replace('usdt', '') + '-widget').classList.add('selected');
      selectedSymbol = symbol;
      updateAllPrices();
      loadChart(selectedTf);
    }

    // #Prices
    var coinSymbols = <?= json_encode(array_column($coins, 'coinname')) ?>;
    var _encoded    = encodeURIComponent(JSON.stringify(coinSymbols));

    function updateAllPrices() {
      fetch('https://api.binance.com/api/v3/ticker/24hr?symbols=' + _encoded)
        .then(function(res) { return res.json(); })
        .then(function(list) {
          list.forEach(function(data) {
            var slug    = data.symbol.toLowerCase().replace('usdt', '');
            var pct     = parseFloat(data.priceChangePercent);
            var price   = '$' + parseFloat(data.lastPrice).toLocaleString('en-US', { minimumFractionDigits: 2 });
            var cls     = pct >= 0 ? 'up' : 'down';
            var pctText = (pct >= 0 ? '+' : '') + pct.toFixed(2) + '%';

            var priceEl = document.getElementById(slug + '-price');
            var chEl    = document.getElementById(slug + '-change');
            if (priceEl) priceEl.textContent = price;
            if (chEl) { chEl.textContent = pctText; chEl.className = 'widget-change ' + cls; }

            if (data.symbol === selectedSymbol) {
              document.getElementById('chartSymbol').textContent = slug.toUpperCase() + '/USDT';
              document.getElementById('chartPrice').textContent  = price;
              var hdr = document.getElementById('chartChange');
              hdr.textContent = pctText;
              hdr.className   = 'chart-change ' + cls;
            }
          });
        })
        .catch(function() {});
    }

    updateAllPrices();
    setInterval(updateAllPrices, 30000);
    </script>
  </div> <!-- /.dashboard-grid -->

  <!-- Grass toast -->
  <div id="grass-toast" class="grass-toast">
    <div class="grass-toast-icon">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
      </svg>
    </div>
    <div class="grass-toast-body">
      <div class="grass-toast-label">Achievement Unlocked</div>
      <div class="grass-toast-title">Go touch grass.</div>
    </div>
  </div>

  <script>
  // #Grass achievement toast
  (function () {
    if (!<?= json_encode((bool)$grassShown) ?>) {
      setTimeout(function () {
        var fd = new FormData();
        fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        fd.append('achievement', 'grass');
        fetch('<?= site_url('auth/achievement') ?>', { method: 'POST', body: fd }).catch(function(){});

        var t = document.getElementById('grass-toast');
        t.classList.add('show');
        setTimeout(function () { t.classList.remove('show'); }, 4000);
      }, 3600000);
    }
  })();
  </script>

  <!-- Footer -->
  <?= view('V_Footer') ?>
</body>
</html>
