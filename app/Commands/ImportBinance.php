<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\M_Coin_Data;

class ImportBinance extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:import';
    protected $description = 'Import Binance kline data for all coins and timeframes.';
    protected $usage       = 'db:import [--timeframe <tf>] [--coin <symbol>] [--days <n>] [--daily]';
    protected $options     = [
        '--timeframe' => 'Single timeframe to import (e.g. 15m). Default: all.',
        '--coin'      => 'Single coin symbol (e.g. BTCUSDT). Default: all.',
        '--days'      => 'Number of days back to import. Default: 100.',
        '--daily'     => 'Import today only (overrides --days).',
    ];

    private const TIMEFRAMES = ['15m', '30m', '1h', '4h', '6h', '12h'];

    public function run(array $params)
    {
        $model = new M_Coin_Data();
        $coins = $model->get_list_coin();

        // Apply --coin filter
        $coinFilter = CLI::getOption('coin');
        if ($coinFilter) {
            $coins = array_filter($coins, fn($c) => strtoupper($c['coinname']) === strtoupper($coinFilter));
            if (empty($coins)) {
                CLI::error("Coin '$coinFilter' not found in tbl_coin.");
                return;
            }
        }

        // Apply --timeframe filter
        $tfFilter   = CLI::getOption('timeframe');
        $timeframes = $tfFilter ? [$tfFilter] : self::TIMEFRAMES;

        // Time range
        $isDaily  = CLI::getOption('daily');
        $days     = (int)(CLI::getOption('days') ?? 100);
        $endTime  = (int) round(microtime(true) * 1000);
        $baseStart = $isDaily
            ? strtotime('today midnight') * 1000
            : strtotime("-{$days} days") * 1000;

        $totalCoins = count($coins);
        $totalTf    = count($timeframes);
        CLI::write("Importing {$totalCoins} coin(s) × {$totalTf} timeframe(s)" . ($isDaily ? ' [today only]' : " [{$days} days]"), 'green');

        foreach ($coins as $coin) {
            foreach ($timeframes as $tf) {
                $startTime  = $baseStart;
                $inserted   = 0;
                $pages      = 0;

                CLI::write("  {$coin['coinname']} / {$tf} ...", 'yellow');

                while ($startTime < $endTime) {
                    $klines = $this->fetchKlines($coin['coinname'], $tf, $startTime, $endTime);
                    if ($klines === null) break; // error already printed
                    if (empty($klines)) break;

                    $batch = [];
                    foreach ($klines as $k) {
                        $batch[] = [
                            'id_coin'            => $coin['id_coin'],
                            'timeframe'          => $tf,
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
                    $model->insertBatchIgnore($batch);
                    $inserted += count($batch);
                    $pages++;

                    $lastClose = (int) end($klines)[6];
                    if (count($klines) < 1000 || $lastClose >= $endTime) break;
                    $startTime = $lastClose + 1;
                }

                CLI::write("    → {$inserted} candles ({$pages} page(s))", 'cyan');
            }
        }

        CLI::write('Done.', 'green');
    }

    private function fetchKlines(string $symbol, string $interval, int $startTime, int $endTime): ?array
    {
        $url = "https://api.binance.com/api/v3/klines"
             . "?symbol={$symbol}&interval={$interval}"
             . "&startTime={$startTime}&endTime={$endTime}&limit=1000";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            CLI::error("cURL Error ($symbol/$interval): $curlError");
            return null;
        }
        $data = json_decode($response, true);
        if (isset($data['code'])) {
            CLI::error("Binance API Error ($symbol/$interval): {$data['msg']}");
            return null;
        }
        return is_array($data) ? $data : [];
    }
}
