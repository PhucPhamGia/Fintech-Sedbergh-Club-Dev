<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Database</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/database.css') ?>">

  <!-- Google Charts Script For Candle Chart -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      // Load PHP data arrays
      const ohlcData = <?= $table ?>;    // [Date, Low, Open, Close, High]
      const ma20Data = <?= $ma20 ?>;     // [Date, MA20]
      const ma50Data = <?= $ma50 ?>;     // [Date, MA50]

      // DEBUG: Log the data to console
      console.log('OHLC Data:', ohlcData);
      console.log('MA20 Data:', ma20Data);
      console.log('MA50 Data:', ma50Data);

      // Add headers if missing
      if (!ohlcData[0].includes('Date')) {
        ohlcData.unshift(['Date', 'Low', 'Open', 'Close', 'High']);
      }
      if (!ma20Data[0].includes('MA20')) {
        ma20Data.unshift(['Date', 'MA20']);
      }
      if (!ma50Data[0].includes('MA50')) {
        ma50Data.unshift(['Date', 'MA50']);
      }

      // Create DataTables
      const ohlcTable = google.visualization.arrayToDataTable(ohlcData);
      const ma20Table = google.visualization.arrayToDataTable(ma20Data);
      const ma50Table = google.visualization.arrayToDataTable(ma50Data);

      console.log('OHLC Table:', ohlcTable);
      console.log('MA20 Table:', ma20Table);
      console.log('MA50 Table:', ma50Table);

      // Join OHLC + MA20 first
      let joinedData = google.visualization.data.join(
        ohlcTable, 
        ma20Table, 
        'full', 
        [[0, 0]],         // match by Date (column 0)
        [1, 2, 3, 4],     // keep Low, Open, Close, High from OHLC
        [1]               // keep MA20 (becomes column 5)
      );

      console.log('After MA20 Join:', joinedData);
      console.log('After MA20 Join Column Count:', joinedData.getNumberOfColumns());

      // Then join with MA50
      joinedData = google.visualization.data.join(
        joinedData,
        ma50Table,
        'full',
        [[0, 0]],         // match by Date
        [1, 2, 3, 4, 5],  // keep Low, Open, Close, High, MA20
        [1]               // keep MA50 (becomes column 6)
      );

      console.log('After MA50 Join:', joinedData);
      console.log('After MA50 Join Column Count:', joinedData.getNumberOfColumns());

      const options = {
        backgroundColor: '#202b3a',
        legend: {
          position: 'bottom',
          textStyle: { color: '#e8eaf6', fontSize: 12 }
        },
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
          0: { type: 'candlesticks', visibleInLegend: false },
          1: { type: 'line', color: '#ffd600', lineWidth: 2, labelInLegend: 'MA20' },
          2: { type: 'line', color: '#00bfff', lineWidth: 2, labelInLegend: 'MA50' }
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
      
      // ✅ SINGLE event listener (removed duplicate)
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
        document.querySelector('.menu-label').style.display =
          dropdown.classList.contains('show') ? 'none' : 'inline';
      });
      
      document.addEventListener('click', function () {
        dropdown.classList.remove('show');
        document.querySelector('.menu-label').style.display = 'inline';
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
      window.location.href = '<?= site_url('/database/') ?>' + coinId + '/' + currentFix;
    }
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
      <hr style="border: 0.5px solid #2a3d59; margin: 6px 0;">
      <a href="<?= site_url('dashboard') ?>">Back</a>
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
    <form action="<?= site_url('database/' . $coin . '/' . $days) ?>" method="post">
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

  <!-- Footer -->
  <?= view('V_Footer') ?>
</body>
</html>