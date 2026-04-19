<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Database</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/database.css') ?>?v=3">

  <!-- Google Charts Script For Candle Chart -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      const ohlcData = <?= $table ?>;
      const ma20Data = <?= $ma20 ?>;
      const ma50Data = <?= $ma50 ?>;

      if (!ohlcData[0].includes('Date')) ohlcData.unshift(['Date', 'Low', 'Open', 'Close', 'High']);
      if (!ma20Data[0].includes('MA20')) ma20Data.unshift(['Date', 'MA20']);
      if (!ma50Data[0].includes('MA50')) ma50Data.unshift(['Date', 'MA50']);

      const ohlcTable = google.visualization.arrayToDataTable(ohlcData);
      const ma20Table = google.visualization.arrayToDataTable(ma20Data);
      const ma50Table = google.visualization.arrayToDataTable(ma50Data);

      let joinedData = google.visualization.data.join(
        ohlcTable, ma20Table, 'full', [[0, 0]], [1, 2, 3, 4], [1]
      );
      joinedData = google.visualization.data.join(
        joinedData, ma50Table, 'full', [[0, 0]], [1, 2, 3, 4, 5], [1]
      );

      const options = {
        backgroundColor: '#202b3a',
        legend: { position: 'bottom', textStyle: { color: '#e8eaf6', fontSize: 12 } },
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
        interpolateNulls: true,
        seriesType: 'candlesticks',
        series: {
          0: { type: 'candlesticks', visibleInLegend: false },
          1: { type: 'line', color: '#00e676', lineWidth: 2 },
          2: { type: 'line', color: '#00bfff', lineWidth: 2 }
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
    
    let currentFix = <?= $days ?? 200 ?>;
    let currentTimeframe = '<?= $timeframe ?? '12h' ?>';

    function openDatabase(coinId) {
      window.location.href = '<?= site_url('database/') ?>' + coinId + '/' + currentTimeframe + '/' + currentFix;
    }

    function openTimeframe(tf) {
      currentTimeframe = tf;
      window.location.href = '<?= site_url('database/') ?>' + '<?= $coin ?>' + '/' + tf + '/' + currentFix;
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
    <form action="<?= site_url('importbinance') ?>" method="post" style="margin: 0;">
      <button type="submit" class="simple-btn">Import Data</button>
    </form>
    <form action="<?= site_url('importbinancedaily') ?>" method="post" style="margin: 0;">
      <button type="submit" class="simple-btn">Import data today</button>
    </form>
    <form action="<?= site_url('importma20') ?>" method="post" style="margin: 0;">
      <button type="submit" class="simple-btn">Calculate MA20</button>
    </form>
    <form action="<?= site_url('importma50') ?>" method="post" style="margin: 0;">
      <button type="submit" class="simple-btn">Calculate MA50</button>
    </form>



    <hr style="border: 0.5px solid #2a3d59; margin: 6px 0;">
    <a href="<?= site_url('dashboard') ?>">Back</a>
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

  <!-- Timeframe widgets -->
  <div class="widget-container">
    <?php
      $timeframes = ['15m', '30m', '1h', '4h', '6h', '12h'];
      foreach ($timeframes as $tf):
    ?>
      <button
        class="crypto-widget-btn <?= ($timeframe == $tf) ? 'active' : '' ?>"
        onclick="openTimeframe('<?= $tf ?>')">
        <div class="crypto-title"><?= strtoupper($tf) ?></div>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- Search Form -->
  <div class="search-container">
    <form action="<?= site_url('database/' . $coin . '/' . $timeframe . '/' . $days) ?>" method="post">
      <input type="text" name="search_day" placeholder="Enter number of days" value="<?= esc($search_day) ?>">
      <button type="submit">Search</button>
    </form>
  </div>

  <div id="chart_div" class="chart-container"></div>

  <!-- Data Table -->
  <div class="table-container">
    <table id="dataTable">
      <thead>
        <tr>
          <th>Coin</th>
          <th>Timeframe</th>
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
            <td><?= $timeframe ?></td>
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

  <!-- Pagination -->
  <div class="pagination-container">
    <button id="prevPageBtn" onclick="showPage(currentPage - 1)" disabled>← Previous 50</button>
    <span id="pageInfo"></span>
    <button id="nextPageBtn" onclick="showPage(currentPage + 1)">Next 50 →</button>
  </div>

  <script>
    const ROWS_PER_PAGE = 50;
    let currentPage = 1;

    function showPage(page) {
      const rows = Array.from(document.querySelectorAll('#dataTable tbody tr'));
      const totalPages = Math.max(1, Math.ceil(rows.length / ROWS_PER_PAGE));
      page = Math.max(1, Math.min(page, totalPages));
      currentPage = page;

      const start = (page - 1) * ROWS_PER_PAGE;
      rows.forEach((row, i) => {
        row.style.display = (i >= start && i < start + ROWS_PER_PAGE) ? '' : 'none';
      });

      document.getElementById('prevPageBtn').disabled = (page === 1);
      document.getElementById('nextPageBtn').disabled = (page === totalPages);
      document.getElementById('pageInfo').textContent =
        'Page ' + page + ' of ' + totalPages + ' (' + rows.length + ' records)';
    }

    document.addEventListener('DOMContentLoaded', function() { showPage(1); });
  </script>

</body>
</html>





















