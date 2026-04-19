<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Controllers\BaseController;
use App\Models\M_Coin_Data;

class C_Database extends BaseController
{
    protected $M_Coin_Data;

    // All supported timeframes — order determines import sequence
    private const TIMEFRAMES = ['15m', '30m', '1h', '4h', '6h', '12h'];

    public function __construct()
    {
        $this->M_Coin_Data = new M_Coin_Data();
    }

    // Fetch one page (≤1000) of klines from Binance. Throws on network/API error.
    private function fetchKlines(string $symbol, string $interval, int $startTime, int $endTime): array
    {
        $url = "https://api.binance.com/api/v3/klines"
             . "?symbol={$symbol}&interval={$interval}"
             . "&startTime={$startTime}&endTime={$endTime}&limit=1000";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("cURL Error: $curlError");
        }
        $data = json_decode($response, true);
        if (isset($data['code'])) {
            throw new \RuntimeException("Binance API Error [{$data['code']}]: {$data['msg']}");
        }
        return is_array($data) ? $data : [];
    }

    // Build the insert row array from a raw Binance kline entry.
    private function buildRow(int $id_coin, string $timeframe, array $k): array
    {
        return [
            'id_coin'            => $id_coin,
            'timeframe'          => $timeframe,
            'date'               => date('Y-m-d', $k[0] / 1000),
            'open_time'          => $k[0],
            'open_price'         => $k[1],
            'high_price'         => $k[2],
            'low_price'          => $k[3],
            'close_price'        => $k[4],
            'volume'             => $k[5],
            'close_time'         => $k[6],
            'quote_volume'       => $k[7],
            'number_of_trades'   => $k[8],
            'taker_base_volume'  => $k[9],
            'taker_quote_volume' => $k[10],
        ];
    }

    /**
     * Binance_Import() — Historical import: last 100 days, all 6 timeframes.
     *
     * Paginates Binance API in 1000-candle pages per (coin × timeframe) until
     * the full 100-day window is covered. Uses INSERT IGNORE so re-running is safe.
     */
    public function Binance_Import()
    {
        set_time_limit(0);

        $coins   = $this->M_Coin_Data->get_list_coin();
        $M       = new M_Coin_Data();
        $endTime = (int) round(microtime(true) * 1000);

        foreach ($coins as $coin) {
            foreach (self::TIMEFRAMES as $timeframe) {
                $startTime = strtotime('-100 days') * 1000;

                while ($startTime < $endTime) {
                    try {
                        $klines = $this->fetchKlines($coin['coinname'], $timeframe, $startTime, $endTime);
                    } catch (\RuntimeException $e) {
                        return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
                    }

                    if (empty($klines)) break;

                    $batch = [];
                    foreach ($klines as $k) {
                        $batch[] = $this->buildRow($coin['id_coin'], $timeframe, $k);
                    }
                    $M->insertBatchIgnore($batch);

                    // Advance past the last candle's close_time; stop if page was incomplete
                    $lastClose = (int) end($klines)[6];
                    if (count($klines) < 1000 || $lastClose >= $endTime) break;
                    $startTime = $lastClose + 1;
                }
            }
        }

        return redirect()->to('/database/1/12h/200')->with('success', 'Historical data imported successfully!');
    }

    /**
     * Binance_Daily_Import() — Incremental import: today only, all 6 timeframes.
     *
     * Max candles per (coin × timeframe) today: 96 (15m) — always fits in one request.
     * Uses INSERT IGNORE so running multiple times per day is safe.
     */
    public function Binance_Daily_Import()
    {
        $coins     = $this->M_Coin_Data->get_list_coin();
        $M         = new M_Coin_Data();
        $startTime = strtotime('today midnight') * 1000;
        $endTime   = (int) round(microtime(true) * 1000);

        foreach ($coins as $coin) {
            foreach (self::TIMEFRAMES as $timeframe) {
                try {
                    $klines = $this->fetchKlines($coin['coinname'], $timeframe, $startTime, $endTime);
                } catch (\RuntimeException $e) {
                    return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
                }

                if (empty($klines)) continue;

                $batch = [];
                foreach ($klines as $k) {
                    $batch[] = $this->buildRow($coin['id_coin'], $timeframe, $k);
                }
                $M->insertBatchIgnore($batch);
            }
        }

        return redirect()->to('database/1/12h/200')->with('success', 'Daily data imported successfully!');
    }

    /**
     * MA20() — 20-period MA for all coins across all timeframes.
     *
     * Window = 40 candles (20 periods × 2 candles per period at 12h).
     * For shorter timeframes the window covers less calendar time but the same
     * number of candles — consistent with how MA20 is typically defined (period count).
     */
    public function MA20()
    {
        $btcModel = new M_Coin_Data();
        $coins    = $this->M_Coin_Data->get_list_coin();

        foreach ($coins as $c) {
            foreach (self::TIMEFRAMES as $timeframe) {
                $rows = $btcModel->where('id_coin', $c['id_coin'])
                                 ->where('timeframe', $timeframe)
                                 ->orderBy('open_time', 'ASC')
                                 ->findAll();

                if (count($rows) < 41) continue;

                $window  = [];
                $sum     = 0.0;
                $updates = [];

                foreach ($rows as $row) {
                    if (count($window) == 40) {
                        $ma = round($sum / 40, 8);
                        if ((float)$row['ma20'] !== $ma) {
                            $updates[] = ['id' => $row['id'], 'ma20' => $ma];
                        }
                    }
                    $close    = (float)$row['close_price'];
                    $window[] = $close;
                    $sum     += $close;
                    if (count($window) > 40) {
                        $sum -= array_shift($window);
                    }
                }

                if (!empty($updates)) {
                    $btcModel->updateBatch($updates, 'id');
                }
            }
        }

        return redirect()->to('database/1/12h/200')->with('success', 'MA20 calculated successfully!');
    }

    /**
     * MA50() — 50-period MA for all coins across all timeframes.
     *
     * Window = 100 candles (50 periods).
     */
    public function MA50()
    {
        $btcModel = new M_Coin_Data();
        $coins    = $this->M_Coin_Data->get_list_coin();

        foreach ($coins as $c) {
            foreach (self::TIMEFRAMES as $timeframe) {
                $rows = $btcModel->where('id_coin', $c['id_coin'])
                                 ->where('timeframe', $timeframe)
                                 ->orderBy('open_time', 'ASC')
                                 ->findAll();

                if (count($rows) < 101) continue;

                $window  = [];
                $sum     = 0.0;
                $updates = [];

                foreach ($rows as $row) {
                    if (count($window) == 100) {
                        $ma = round($sum / 100, 8);
                        if ((float)$row['ma50'] !== $ma) {
                            $updates[] = ['id' => $row['id'], 'ma50' => $ma];
                        }
                    }
                    $close    = (float)$row['close_price'];
                    $window[] = $close;
                    $sum     += $close;
                    if (count($window) > 100) {
                        $sum -= array_shift($window);
                    }
                }

                if (!empty($updates)) {
                    $btcModel->updateBatch($updates, 'id');
                }
            }
        }

        return redirect()->to('database/1/12h/200')->with('success', 'MA50 calculated successfully!');
    }

    // Find records where MA20, MA50, and close_price are all within $percent of each other.
    // Optional filters: $id_coin, $startDate/$endDate, $timeframe.
    // If $saveToDb=true, writes crossed_ma20_ma50=1 for matching rows.
    public function MA20_MA50_Close($percent = 5, $id_coin = null, $startDate = null, $endDate = null, $saveToDb = false, $timeframe = '12h')
    {
        $percent = (float) $percent;
        $model   = new M_Coin_Data();

        $coin_map = !empty($id_coin)
            ? [['id_coin' => $id_coin]]
            : $this->M_Coin_Data->get_list_coin();

        $matches = [];
        $updates = [];

        foreach ($coin_map as $c) {
            $query = $model->where('id_coin', $c['id_coin'])
                           ->where('timeframe', $timeframe)
                           ->orderBy('open_time', 'ASC');

            if (!empty($startDate)) $query->where('date >=', date('Y-m-d', strtotime($startDate)));
            if (!empty($endDate))   $query->where('date <=', date('Y-m-d', strtotime($endDate)));

            foreach ($query->findAll() as $row) {
                $ma20  = (float)($row['ma20'] ?? 0);
                $ma50  = (float)($row['ma50'] ?? 0);
                $close = (float)($row['close_price'] ?? 0);

                if ($ma20 <= 0 || $ma50 <= 0 || $close <= 0) continue;

                $avg = ($ma20 + $ma50 + $close) / 3.0;
                if ($avg == 0) continue;

                if (
                    abs($ma20 - $avg) / $avg * 100 <= $percent &&
                    abs($ma50 - $avg) / $avg * 100 <= $percent &&
                    abs($close - $avg) / $avg * 100 <= $percent
                ) {
                    $matches[] = [
                        'id'         => $row['id'],
                        'id_coin'    => $c['id_coin'],
                        'coinname'   => $c['coinname'] ?? null,
                        'timeframe'  => $row['timeframe'],
                        'date'       => $row['date'],
                        'open_time'  => $row['open_time'],
                        'ma20'       => $ma20,
                        'ma50'       => $ma50,
                        'close_price'=> $close,
                        'avg'        => round($avg, 8),
                    ];
                    if ($saveToDb) {
                        $updates[] = ['id' => $row['id'], 'crossed_ma20_ma50' => 1];
                    }
                }
            }
        }

        if ($saveToDb && !empty($updates)) {
            $model->updateBatch($updates, 'id');
        }

        return $this->response->setJSON([
            'percent'   => $percent,
            'timeframe' => $timeframe,
            'id_coin'   => $id_coin,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'saved'     => $saveToDb ? count($updates) : 0,
            'matches'   => $matches,
        ]);
    }
}
