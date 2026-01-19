<?php

// Handle database-related functions such as data import and retrieval

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Controllers\BaseController;
use App\Models\M_Coin_Data;

class C_Database extends BaseController
{
    protected $M_Coin_Data;
    public function __construct()
    {
        $this->M_Coin_Data = new M_Coin_Data(); // Call M_Coin_Data model by $this->M_Coin_Data->method()
    }

    /**
     * Binance_Import() - Import Historical Kline Data
     * 
     * PURPOSE: Import last 100 days of cryptocurrency kline data from Binance API at 12h intervals
     * 
     * FLOW:
     * 1. Get all coins from tbl_coin via get_list_coin()
     * 2. For each coin, construct Binance API URL with 100-day time range
     * 3. Execute cURL request to https://api.binance.com/api/v3/klines
     * 4. Validate cURL response and parse JSON
     * 5. Check for Binance API errors (if response contains 'code' field)
     * 6. For each kline in response:
     *    - Extract date from open_time (milliseconds → Y-m-d format)
     *    - Check if record already exists (duplicate prevention via date + id_coin + open_time)
     *    - If not exists, insert all 13 kline fields into btcdatadb
     * 7. Redirect to /database/1/50 with success message
     * 
     * API DETAILS:
     * - Endpoint: https://api.binance.com/api/v3/klines
     * - Symbol format: coinname (e.g., BTCUSDT)
     * - Interval: 12h (12-hour candles)
     * - Time range: Last 100 days (calculated via strtotime)
     * - Response: Array of klines [open_time, open_price, high_price, low_price, close_price, volume, close_time, quote_volume, number_of_trades, taker_base_volume, taker_quote_volume, ...]
     * 
     * ERROR HANDLING:
     * ✅ cURL failure detection: if ($response === false)
     * ✅ API error detection: if (isset($klines['code']))
     * ✅ Duplicate prevention: countAllResults() check before insert
     * 
     * DATABASE FIELDS INSERTED:
     * id_coin, date, open_time, open_price, high_price, low_price, close_price, 
     * volume, close_time, quote_volume, number_of_trades, taker_base_volume, taker_quote_volume
     */
    public function Binance_Import()
    {
        $data['coin_map'] = $this->M_Coin_Data->get_list_coin();

        $M_Coin_Data = new M_Coin_Data();
        foreach ($data['coin_map'] as $coin_item) {
            $symbol = $coin_item['coinname'];
            $interval = '12h';
            $startTime = strtotime('-100 days') * 1000;
            $endTime = round(microtime(true) * 1000);

            $url = "https://api.binance.com/api/v3/klines?symbol=$symbol&interval=$interval&startTime=$startTime&endTime=$endTime&limit=1000";
          
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return $this->response->setStatusCode(500)->setJSON(['error' => "cURL Error: $error"]);
            }
            curl_close($ch);

            $klines = json_decode($response, true);

            if (isset($klines['code'])) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $klines['msg']]);
            }

            if (is_array($klines)) {
            foreach ($klines as $kline) {
                $date = date('Y-m-d', $kline[0] / 1000);
                $exists = $M_Coin_Data->where('date', $date)
                           ->where('id_coin', $coin_item['id_coin'])
                           ->where('open_time', $kline[0]) 
                           ->countAllResults();

                if ($exists == 0) {
                        $M_Coin_Data->insert([
                            'id_coin'            => $coin_item['id_coin'],
                            'date'               => $date,
                            'open_time'          => $kline[0],
                            'open_price'         => $kline[1],
                            'high_price'         => $kline[2],
                            'low_price'          => $kline[3],
                            'close_price'        => $kline[4],
                            'volume'             => $kline[5],
                            'close_time'         => $kline[6],
                            'quote_volume'       => $kline[7],
                            'number_of_trades'   => $kline[8],
                            'taker_base_volume'  => $kline[9],
                            'taker_quote_volume' => $kline[10],
                        ]);
                    }
                }
            }
        }
        return redirect()->to('/database/1/50')->with('success', 'Data imported successfully to database!');
    }

    /**
     * Binance_Daily_Import() - Import Today's Kline Data
     * 
     * PURPOSE: Import today's cryptocurrency kline data from Binance API at 12h intervals
     * 
     * FLOW:
     * 1. Get all coins from tbl_coin via get_list_coin()
     * 2. For each coin, construct Binance API URL with today's time range only
     * 3. Execute cURL request to https://api.binance.com/api/v3/klines
     * 4. Validate cURL response and parse JSON
     * 5. Check for Binance API errors (if response contains 'code' field)
     * 6. For each kline in response:
     *    - Extract date from open_time (milliseconds → Y-m-d format)
     *    - Check if record already exists (duplicate prevention via date + id_coin + open_time)
     *    - If not exists, insert all 13 kline fields into btcdatadb
     * 7. Redirect to database/1/50 with success message
     * 
     * API DETAILS:
     * - Endpoint: https://api.binance.com/api/v3/klines
     * - Symbol format: coinname (e.g., BTCUSDT)
     * - Interval: 12h (12-hour candles)
     * - Time range: Today only (from 00:00 UTC to current time)
     * - Response: Array of klines [open_time, open_price, high_price, low_price, close_price, volume, close_time, quote_volume, number_of_trades, taker_base_volume, taker_quote_volume, ...]
     * 
     * ERROR HANDLING:
     * ✅ cURL failure detection: if ($response === false)
     * ✅ API error detection: if (isset($data['code']))
     * ✅ Duplicate prevention: countAllResults() check before insert
     * 
     * DIFFERENCES FROM Binance_Import():
     * - Time scope: Today only (strtotime('today midnight')) instead of last 100 days
     * - Use case: Daily incremental updates instead of historical backfill
     */
    public function Binance_Daily_Import()
    {
        $data['coin_map'] = $this->M_Coin_Data->get_list_coin();

        $M_Coin_Data = new M_Coin_Data();
        foreach ($data['coin_map'] as $coin_item) {
            $symbol = $coin_item['coinname'];
            $interval = '12h';
            $startTime = strtotime('today midnight') * 1000;
            $endTime = round(microtime(true) * 1000);

            $url = "https://api.binance.com/api/v3/klines?symbol=$symbol&interval=$interval&startTime=$startTime&endTime=$endTime&limit=1000";
          
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            if ($response === false) {
            $error = curl_error($ch);
            return $this->response->setStatusCode(500)->setJSON(['error' => "cURL Error: $error"]);
            }

            $data = json_decode($response, true);

            if (isset($data['code'])) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $data['msg']]);
            }

            if (is_array($data)) {
            foreach ($data as $kline) {
                $date = date('Y-m-d', $kline[0] / 1000);
                $exists = $M_Coin_Data->where('date', $date)
                           ->where('id_coin', $coin_item['id_coin'])
                           ->where('open_time', $kline[0]) 
                           ->countAllResults();

                if ($exists == 0) {
                        $M_Coin_Data->insert([
                            'id_coin'            => $coin_item['id_coin'],
                            'date'               => $date,
                            'open_time'          => $kline[0],
                            'open_price'         => $kline[1],
                            'high_price'         => $kline[2],
                            'low_price'          => $kline[3],
                            'close_price'        => $kline[4],
                            'volume'             => $kline[5],
                            'close_time'         => $kline[6],
                            'quote_volume'       => $kline[7],
                            'number_of_trades'   => $kline[8],
                            'taker_base_volume'  => $kline[9],
                            'taker_quote_volume' => $kline[10],
                        ]);
                    }
                }
            }
        }
        return redirect()->to('database/1/50')->with('success', 'Data imported successfully to database!');
    }

    /**
     * MA20() - Calculate 20-Period Moving Average
     * 
     * PURPOSE: Calculate 20-period moving average (MA20) for all coins and update btcdatadb
     * 
     * ALGORITHM:
     * 1. Get all coins from tbl_coin
     * 2. For each coin:
     *    - Fetch all kline records sorted by open_time ASC (oldest → newest)
     *    - Skip if less than 41 records (need 40 candles for first MA calculation)
     *    - Initialize sliding window (array) and sum accumulator
     * 3. For each record in sorted klines:
     *    - When window reaches 40 elements:
     *      • Calculate MA20 = sum / 40 (rounded to 8 decimals)
     *      • If MA20 differs from current value, add to updates batch
     *    - Add current close_price to window and sum
     *    - If window exceeds 40 elements, remove oldest (shift) and subtract from sum
     * 4. Batch update all changed records via updateBatch()
     * 5. Redirect to database/1/50 with success message
     * 
     * MOVING AVERAGE DETAILS:
     * - Window size: 40 candles (represents 20 periods at 12h intervals = 10 days of data)
     * - Calculation: SUM(close_price of 40 candles) / 40
     * - Update condition: Only update if new MA20 ≠ stored MA20 (optimizes DB writes)
     * - Precision: Rounded to 8 decimal places
     * 
     * PERFORMANCE OPTIMIZATION:
     * ✅ Sliding window technique (O(n) instead of O(n²))
     * ✅ Batch updates (single query instead of N queries)
     * ✅ Conditional updates (skip if value unchanged)
     * ✅ Early skip (continue if insufficient data)
     */
    public function MA20()
    {
        $btcModel = new M_Coin_Data();
        $data['coin_map'] = $this->M_Coin_Data->get_list_coin();

        foreach ($data['coin_map'] as $c) {
            $rows = $btcModel->where('id_coin', $c['id_coin'])
                             ->orderBy('open_time', 'ASC')
                             ->findAll();

            if (count($rows) < 41) continue;

            $window = [];
            $sum = 0.0;
            $updates = [];

            foreach ($rows as $i => $row) {
                if (count($window) == 40) {
                    $ma = round($sum / 40, 8);
                    if ((float)$row['ma20'] !== $ma) {
                        $updates[] = ['id' => $row['id'], 'ma20' => $ma];
                    }
                }
                $close = (float)$row['close_price'];
                $window[] = $close;
                $sum += $close;

                if (count($window) > 40) {
                    $sum -= array_shift($window);
                }
            }
            if (!empty($updates)) {
                $btcModel->updateBatch($updates, 'id');
            }
        }
        return redirect()->to('database/1/50')->with('success', 'Data imported successfully to database!');
    }

    /**
     * MA50() - Calculate 50-Period Moving Average
     * 
     * PURPOSE: Calculate 50-period moving average (MA50) for all coins and update btcdatadb
     * 
     * ALGORITHM:
     * 1. Get all coins from tbl_coin
     * 2. For each coin:
     *    - Fetch all kline records sorted by open_time ASC (oldest → newest)
     *    - Skip if less than 101 records (need 100 candles for first MA calculation)
     *    - Initialize sliding window (array) and sum accumulator
     * 3. For each record in sorted klines:
     *    - When window reaches 100 elements:
     *      • Calculate MA50 = sum / 100 (rounded to 8 decimals)
     *      • If MA50 differs from current value, add to updates batch
     *    - Add current close_price to window and sum
     *    - If window exceeds 100 elements, remove oldest (shift) and subtract from sum
     * 4. Batch update all changed records via updateBatch()
     * 5. Redirect to database/1/50 with success message
     * 
     * MOVING AVERAGE DETAILS:
     * - Window size: 100 candles (represents 50 periods at 12h intervals = 25 days of data)
     * - Calculation: SUM(close_price of 100 candles) / 100
     * - Update condition: Only update if new MA50 ≠ stored MA50 (optimizes DB writes)
     * - Precision: Rounded to 8 decimal places
     * 
     * PERFORMANCE OPTIMIZATION:
     * ✅ Sliding window technique (O(n) instead of O(n²))
     * ✅ Batch updates (single query instead of N queries)
     * ✅ Conditional updates (skip if value unchanged)
     * ✅ Early skip (continue if insufficient data)
     * 
     * STATUS: WIP (Work In Progress) - Functional but may need optimization for large datasets
     */
    public function MA50()
    {
        $btcModel = new M_Coin_Data();
        $data['coin_map'] = $this->M_Coin_Data->get_list_coin();

        foreach ($data['coin_map'] as $c) {
            $coin = $c['id_coin'];
            $rows = $btcModel->where('id_coin', $coin)
                             ->orderBy('open_time', 'ASC')
                             ->findAll();

            if (count($rows) < 101) continue;

            $window = [];
            $sum = 0.0;
            $updates = [];

            foreach ($rows as $i => $row) {
                if (count($window) == 100) {
                    $ma = round($sum / 100, 8);
                    if ((float)$row['ma50'] !== $ma) {
                        $updates[] = ['id' => $row['id'], 'ma50' => $ma];
                    }
                }
                $close = (float)$row['close_price'];
                $window[] = $close;
                $sum += $close;

                if (count($window) > 100) {
                    $sum -= array_shift($window);
                }
            }
            if (!empty($updates)) {
                $btcModel->updateBatch($updates, 'id');
            }
        }
        return redirect()->to('database/1/50')->with('success', 'Data imported successfully to database!');
    }
}

