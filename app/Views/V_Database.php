<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Database</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/database.css') ?>?v=4">

  <!-- Google Charts Script For Candle Chart -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    // Raw data from PHP — immutable, so drawChart() can be called multiple times safely
    const _rawOhlc = <?= $table ?>;
    const _rawMa20 = <?= $ma20 ?>;
    const _rawMa50 = <?= $ma50 ?>;

    // PSAR parameters (adjustable via controls)
    let psarAF   = 0.02;
    let psarStep = 0.02;
    let psarMax  = 0.20;

    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      // Work on fresh copies so this function is safely re-callable
      const ohlcData = _rawOhlc.map(r => r.slice());
      const ma20Data = _rawMa20.map(r => r.slice());
      const ma50Data = _rawMa50.map(r => r.slice());

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

      // Compute PSAR from raw OHLC rows (no header) and add as two scatter columns
      const psarResult = computePSAR(_rawOhlc, psarAF, psarStep, psarMax);
      const psarByDate = {};
      _rawOhlc.forEach((row, i) => { if (psarResult[i]) psarByDate[row[0]] = psarResult[i]; });

      joinedData.addColumn('number', 'PSAR↑'); // col 6 — bull dot (below candle, green)
      joinedData.addColumn('number', 'PSAR↓'); // col 7 — bear dot (above candle, red)
      for (let r = 0; r < joinedData.getNumberOfRows(); r++) {
        const p = psarByDate[joinedData.getValue(r, 0)];
        if (p) joinedData.setValue(r, p.bull ? 6 : 7, p.value);
      }

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
        chartArea: { backgroundColor: '#202b3a', left: 65, right: 20, top: 40, bottom: 60 },
        interpolateNulls: true,
        seriesType: 'candlesticks',
        series: {
          0: { type: 'candlesticks', visibleInLegend: false },
          1: { type: 'line',    color: '#00e676', lineWidth: 2 },
          2: { type: 'line',    color: '#00bfff', lineWidth: 2 },
          3: { type: 'scatter', color: '#00e676', pointSize: 3, lineWidth: 0 },
          4: { type: 'scatter', color: '#ff595e', pointSize: 3, lineWidth: 0 },
        },
        candlestick: {
          fallingColor: { strokeWidth: 1, fill: '#ff595e', stroke: '#ff595e' },
          risingColor:  { strokeWidth: 1, fill: '#2176ff', stroke: '#2176ff' }
        }
      };

      new google.visualization.ComboChart(document.getElementById('chart_div'))
        .draw(joinedData, options);
    }

    // Parabolic SAR — data: [[date, low, open, close, high], ...] ASC, no header
    // Returns [{value, bull}, ...] same length; first entry is null
    function computePSAR(data, afStart, afStep, afMax) {
      const n = data.length;
      const out = new Array(n).fill(null);
      if (n < 2) return out;

      let bull = parseFloat(data[1][3]) >= parseFloat(data[0][3]);
      let af   = afStart;
      let ep, sar;

      if (bull) {
        ep  = Math.max(parseFloat(data[0][4]), parseFloat(data[1][4]));
        sar = Math.min(parseFloat(data[0][1]), parseFloat(data[1][1]));
      } else {
        ep  = Math.min(parseFloat(data[0][1]), parseFloat(data[1][1]));
        sar = Math.max(parseFloat(data[0][4]), parseFloat(data[1][4]));
      }
      out[1] = { value: sar, bull };

      for (let i = 2; i < n; i++) {
        const high = parseFloat(data[i][4]);
        const low  = parseFloat(data[i][1]);

        let newSar = sar + af * (ep - sar);

        if (bull) {
          newSar = Math.min(newSar, parseFloat(data[i-1][1]), parseFloat(data[i-2][1]));
          if (low <= newSar) {
            bull = false; newSar = ep; ep = low; af = afStart;
            newSar = Math.max(newSar, parseFloat(data[i-1][4]), parseFloat(data[i-2][4]));
          } else if (high > ep) {
            ep = high; af = Math.min(af + afStep, afMax);
          }
        } else {
          newSar = Math.max(newSar, parseFloat(data[i-1][4]), parseFloat(data[i-2][4]));
          if (high >= newSar) {
            bull = true; newSar = ep; ep = high; af = afStart;
            newSar = Math.min(newSar, parseFloat(data[i-1][1]), parseFloat(data[i-2][1]));
          } else if (low < ep) {
            ep = low; af = Math.min(af + afStep, afMax);
          }
        }

        sar = newSar;
        out[i] = { value: sar, bull };
      }
      return out;
    }

    function applyPSAR() {
      psarAF   = parseFloat(document.getElementById('psarAF').value)   || 0.02;
      psarStep = parseFloat(document.getElementById('psarStep').value) || 0.02;
      psarMax  = parseFloat(document.getElementById('psarMax').value)  || 0.20;
      drawChart();
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
    
    let currentFix = <?= $days ?? 100 ?>;
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

  <!-- PSAR Parameters -->
  <div class="psar-controls">
    <span class="psar-title">Parabolic SAR</span>
    <label>AF Start <input id="psarAF"   type="number" value="0.02" min="0.001" max="0.5"  step="0.005"></label>
    <label>AF Step  <input id="psarStep" type="number" value="0.02" min="0.001" max="0.5"  step="0.005"></label>
    <label>AF Max   <input id="psarMax"  type="number" value="0.20" min="0.01"  max="1.0"  step="0.01"></label>
    <button onclick="applyPSAR()">Apply</button>
  </div>

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

  <!-- Footer -->
  <?= view('V_Footer') ?>
</body>
</html>