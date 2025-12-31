<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Database</title>

  <!-- CSS -->
  <style>
    :root {
      --section-gap: 28px; /* equal vertical spacing between sections */
    }

    body {
      background: #181e27;
      color: #e8eaf6;
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow-y: scroll; /* keeps scrollbar space consistent */
    }




    .circle-menu-container {
      position: absolute;
      top: 18px;
      right: 32px;
      z-index: 20;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
    }

    .circle-menu-btn {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: #2176ff;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 2px 8px 0 rgba(33,118,255,0.18);
      position: relative;
      transition: background 0.2s;
    }

    .circle-menu-btn:hover {
      background: #1656b7;
    }

    .circle-menu-btn .dots {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 20px;
    }

    .circle-menu-btn span {
      display: block;
      width: 6px;
      height: 6px;
      background: #fff;
      border-radius: 50%;
      margin: 2px 0;
    }

    .circle-dropdown {
      display: none;
      position: absolute;
      top: 54px;
      right: 0;
      background: #202b3a;
      border: 1.5px solid #2176ff;
      border-radius: 12px;
      min-width: 160px;
      box-shadow: 0 4px 18px 0 rgba(33,118,255,0.13);
      padding: 8px 0;
      z-index: 30;
    }

    .circle-dropdown.show {
      display: block;
      animation: fadeIn 0.18s;
    }

    .circle-dropdown a {
      display: block;
      padding: 10px 18px;
      color: #e8eaf6;
      text-decoration: none;
      font-size: 1rem;
      transition: background 0.15s, color 0.15s;
    }

    .circle-dropdown a:hover {
      background: #2176ff;
      color: #fff;
    }

    .simple-btn {
      width: 85%;
      padding: 10px 0;
      background: #ff595e;
      color: #fff;
      border: none;
      border-radius: 12px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s;
      margin: 6px 12px;
      box-sizing: border-box;
    }

    .simple-btn:hover {
      background: #d5464a;
    }

    .circle-menu-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .menu-label {
      position: absolute;
      right: 54px; /* attaches edge to circle */
      top: 50%;
      transform: translateY(-50%);
      background: #2176ff;
      color: #fff;
      font-weight: 600;
      font-size: 15px;
      padding: 4px 10px 4px 14px;
      border-radius: 8px 0 0 8px;
      box-shadow: 0 2px 6px rgba(33,118,255,0.25);
      user-select: none;
      display: flex;
      align-items: center;
      height: 28px;
    }

    /* optional small triangle "connector" */
    .menu-label::after {
      content: '';
      position: absolute;
      right: -6px;
      top: 50%;
      transform: translateY(-50%);
      width: 0;
      height: 0;
      border-top: 6px solid transparent;
      border-bottom: 6px solid transparent;
      border-left: 6px solid #2176ff;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-8px);}
      to { opacity: 1; transform: translateY(0);}
    }


    /* ===== Circle Menu ===== */
    .circle-menu-container {
      position: absolute;
      top: 18px;
      right: 32px;
      z-index: 20;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
    }
    .circle-menu-btn {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: #2176ff;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 2px 8px 0 rgba(33,118,255,0.18);
      position: relative;
      transition: background 0.2s;
    }
    .circle-menu-btn:hover {
      background: #1656b7;
    }
    .circle-menu-btn .dots {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 20px;
    }
    .circle-menu-btn span {
      display: block;
      width: 6px;
      height: 6px;
      background: #fff;
      border-radius: 50%;
      margin: 2px 0;
    }
    .circle-dropdown {
      display: none;
      position: absolute;
      top: 54px;
      right: 0;
      background: #202b3a;
      border: 1.5px solid #2176ff;
      border-radius: 12px;
      min-width: 160px;
      box-shadow: 0 4px 18px 0 rgba(33,118,255,0.13);
      padding: 8px 0;
      z-index: 30;
    }
    .circle-dropdown.show {
      display: block;
      animation: fadeIn 0.18s;
    }
    .circle-dropdown a {
      display: block;
      padding: 10px 18px;
      color: #e8eaf6;
      text-decoration: none;
      font-size: 1rem;
      transition: background 0.15s, color 0.15s;
    }
    .circle-dropdown a:hover {
      background: #2176ff;
      color: #fff;
    }
    .simple-btn {
      width: 85%;
      padding: 10px 0;
      background: #ff595e;
      color: #fff;
      border: none;
      border-radius: 12px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s;
      margin: 6px 12px;
      box-sizing: border-box;
    }
    .simple-btn:hover {
      background: #d5464a;
    }

    .circle-menu-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .menu-label {
      position: absolute;
      right: 54px; /* attaches edge to circle */
      top: 50%;
      transform: translateY(-50%);
      background: #2176ff;
      color: #fff;
      font-weight: 600;
      font-size: 15px;
      padding: 4px 10px 4px 14px;
      border-radius: 8px 0 0 8px;
      box-shadow: 0 2px 6px rgba(33,118,255,0.25);
      user-select: none;
      display: flex;
      align-items: center;
      height: 28px;
    }

    /* optional small triangle "connector" */
    .menu-label::after {
      content: '';
      position: absolute;
      right: -6px;
      top: 50%;
      transform: translateY(-50%);
      width: 0;
      height: 0;
      border-top: 6px solid transparent;
      border-bottom: 6px solid transparent;
      border-left: 6px solid #2176ff;
    }

    .chart-container {
      width: 100%;
      max-width: 1400px;
      height: 520px;
      background: #202b3a;
      border: 2px solid #2176ff;
      border-radius: 18px;
      box-shadow: 0 6px 36px 0 rgba(33,118,255,0.08);
      margin: var(--section-gap) auto;
      padding: 10px;
    }

    .search-container {
      width: 100%;
      max-width: 1400px;
      margin: var(--section-gap) auto;
      background: #202b3a;
      padding: 12px 18px;
      border-radius: 12px;
      border: 2px solid #2176ff;
      box-shadow: 0 4px 18px 0 rgba(33,118,255,0.08);
      display: flex;
      align-items: center;
      gap: 10px;
      box-sizing: border-box;
    }
    .search-container input {
      flex: 1 1 720px;
      min-width: 280px;
      max-width: 1100px;
      padding: 10px 14px;
      border-radius: 8px;
      border: 1.5px solid #2176ff;
      outline: none;
      background: #181e27;
      color: #e8eaf6;
      font-size: 14px;
      margin-right: 8px;
    }
    .search-container button {
      padding: 10px 16px;
      background: #ff595e;
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }
    .search-container button:hover {
      background: #d5464a;
    }
    .widget-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin: var(--section-gap) auto;
      margin-bottom: var(--section-gap);
    }

    .crypto-widget-btn {
      background: #261b21;
      border: 2px solid #ff595e;
      border-radius: 16px;
      padding: 18px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 16px 0 rgba(255,89,94,0.08);
      color: inherit;
      cursor: pointer;
      outline: none;
      font: inherit;
      width: 200px;
      transition: all 0.2s;
    }

    .crypto-widget-btn:hover {
      border: 2.5px solid #ffd600;
      background: #2a2430;
      box-shadow: 0 0 0 3px #ffd60044;
    }

    .crypto-widget-btn.active {
      border: 2.5px solid #ffd600;
      background: #2a2430;
      box-shadow: 0 0 0 3px #ffd60044;
    }

    .crypto-title {
      color: #ff595e;
      font-weight: bold;
      margin-bottom: 6px;
    }


    .table-container {
      width: 100%;
      max-width: 1400px;
      max-height: 520px; /* allow vertical scrolling while keeping header visible */
      overflow: auto; /* enable both horizontal and vertical scroll */
      background: #202b3a;
      border-radius: 18px;
      box-shadow: 0 6px 36px 0 rgba(33,118,255,0.08);
      padding: 0; /* keep table flush so sticky cells align */
      border: 2.5px solid #2176ff;
      margin: var(--section-gap) auto var(--section-gap);
    }

    /* ensure table layout is fixed so column widths are stable */
    .table-container table {
      width: 100%;
      min-width: 800px; /* prevents columns from collapsing when container is narrow */
      border-collapse: collapse;
      table-layout: fixed;
      font-size: 15px;
    }

    /* sticky header */
    .table-container thead th {
      position: sticky;
      top: 0;
      background: #253042;
      color: #ffd600;
      z-index: 3;
      padding: 14px 12px;
      text-align: left;
      border-bottom: 1px solid #2a3d59;
    }

    /* sticky first column */
    .table-container th:first-child,
    .table-container td:first-child {

      left: 0;
      background: #202b3a;
      z-index: 2;
      border-right: 1px solid #2a3d59;
    }

    .table-container td {
      padding: 14px 12px;
      text-align: left;
      border-bottom: 1px solid #2a3d59;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 15px;
    }

    th, td {
      padding: 14px 12px;
      text-align: left;
      border-bottom: 1px solid #2a3d59;
    }

    th {
      background: #253042;
      color: #ffd600;
      font-weight: bold;
    }

    tr:hover {
      background: #2a2430;
    }
  </style>

  <!-- Google Charts Script For Candle Chart -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      // Load PHP data arrays
      const ohlcData = <?= $table ?>;  // Candlestick data: [Date, Low, Open, Close, High]
      const ma20Data = <?= $ma20 ?>;   // MA20 data: [Date, MA20]
      const ma50Data = <?= $ma50 ?>;   // MA50 data: [Date, MA50]

      // Create DataTables
      const ohlcTable = google.visualization.arrayToDataTable(ohlcData, true);
      const ma20Table = google.visualization.arrayToDataTable(ma20Data, true);
      const ma50Table = google.visualization.arrayToDataTable(ma50Data, true);

      // Join both datasets on Date column
      const joinedData = google.visualization.data.join(
        ohlcTable, 
        ma20Table, 
        'full', 
        [[0, 0]],   // match by first column (Date)
        [1, 2, 3, 4], // keep Low, Open, Close, High from OHLC
        [1],          // keep MA20,
        [1]
      );

      const options = {
        backgroundColor: '#202b3a',
        legend: 'none',
        hAxis: {
          textStyle: { color: '#e8eaf6', fontSize: 12 },
          gridlines: { color: '#2a3d59' },
          baselineColor: '#2a3d59'
        },
        vAxis: {
          textStyle: { color: '#e8eaf6', fontSize: 12 },
          gridlines: { color: '#2a3d59' },
          baselineColor: '#2a3d59'
        },
        chartArea: {
          backgroundColor: '#202b3a',
          left: 60,
          top: 40,
          width: '88%',
          height: '75%'
        },
        seriesType: 'candlesticks',
        series: {
          1: { type: 'line', color: '#ffd600', lineWidth: 2 }, // MA20 line
          2: { type: 'line', color: '#00ffab', lineWidth: 2 }  // MA50 line
        },
        candlestick: {
          fallingColor: { strokeWidth: 1, fill: '#ff595e', stroke: '#ff595e' },
          risingColor: { strokeWidth: 1, fill: '#2176ff', stroke: '#2176ff' }
        }
      };

      const chart = new google.visualization.ComboChart(
        document.getElementById('chart_div')
      );
      chart.draw(joinedData, options);
    }
  </script>

  <!-- JavaScript for Circle Menu -->
  <script>
    // Circle menu toggle
    document.addEventListener('DOMContentLoaded', function () {
      const btn = document.getElementById('circleMenuBtn');
      const dropdown = document.getElementById('circleDropdown');
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
      });
      document.addEventListener('click', function () {
        dropdown.classList.remove('show');
      });
      dropdown.addEventListener('click', function (e) {
        e.stopPropagation();
      });
    });
    
    let currentFix = <?= $fix ?? 50 ?>;
    function updateFix(value) {
      currentFix = value;
      document.getElementById('fixValue').textContent = value;
    }

    function openDatabase(coinId) {
      window.location.href = `/public/database/${coinId}/${currentFix}`;
    }

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      dropdown.classList.toggle('show');
      document.querySelector('.menu-label').style.display =
        dropdown.classList.contains('show') ? 'none' : 'inline';
    });

  </script>
</head>
<body>
  <!-- Circle Menu -->
  <div class="circle-menu-container">

    <!-- Menu Label -->
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
  </div>

  <!-- Circle Dropdown Content -->
  <div class="circle-dropdown" id="circleDropdown">
    <form action="<?= site_url('public/importbinance') ?>" method="post" style="margin: 0;">
      <button type="submit" class="simple-btn">Import Data</button>
    </form>
    <form action="<?= site_url('public/importbinancedaily') ?>" method="post" style="margin: 0;">
      <button type="submit" class="simple-btn">Import data today</button>
    </form>
    <form action="<?= site_url('public/importma20') ?>" method="post" style="margin: 0;">
      <button type="submit" class="simple-btn">Calculate MA20</button>
    </form>
    <form action="<?= site_url('public/importma50') ?>" method="post" style="margin: 0;">
      <button type="submit" class="simple-btn">Calculate MA50</button>
    </form>



      <hr style="border: 0.5px solid #2a3d59; margin: 6px 0;">
      <a href="/public">Back</a>
    </div>
  </div>

  <!-- Coin widgets -->
  <div class="widget-container">
    <?php
      $coins = [
        1 => 'BTC/USDT',
        5 => 'ETH/USDT',
        4 => 'BNB/USDT',
        3 => 'SOL/USDT'
      ];
      foreach ($coins as $id => $name):
    ?>
      <button
        class="crypto-widget-btn <?= ($coin == $id) ? 'active' : '' ?>"
        onclick="openDatabase(<?= $id ?>)">
        <div class="crypto-title"><?= $name ?></div>
        <div class="main-price">#<?= $id ?></div>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- Search Form -->
  <div class="search-container">
    <form action="<?= site_url('public/database/' . $coin . '/' . $days) ?>" method="post">
      <input type="text" name="search_day" placeholder="Enter number of days" value="<?= esc($search_day) ?>">
      <button type="submit">Search</button>
    </form>
  </div>

  <div id="chart_div" class="chart-container" style="width: 900px; height: 500px;"></div>

  <!-- Data Table -->
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Coin</th>
          <th>Date</th>
          <th>Open Price</th>
          <th>Close Price</th>
          <th>High Price</th>
          <th>Low Price</th>
          <th>Volume</th>
          <th>Number of Trades</th>
          <th>MA20</th>
          <th>MA50</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (array_reverse($record) as $item): ?>
          <tr>
            <td><?= $coinname ?></td>
            <td><?= $item['date'] ?></td>
            <td><?= $item['open_price'] ?></td>
            <td><?= $item['close_price'] ?></td>
            <td><?= $item['high_price'] ?></td>
            <td><?= $item['low_price'] ?></td>
            <td><?= $item['volume'] ?></td>
            <td><?= $item['number_of_trades'] ?></td>
            <td><?= $item['ma20'] ?></td>
            <td><?= $item['ma50'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</body>
</html>





















