<!--
Yo, write sum comments dawg
-->

<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/database.css') ?>">

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
        [1]          // keep MA20
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
          1: { type: 'line', color: '#ffd600', lineWidth: 2 } // MA20 line
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