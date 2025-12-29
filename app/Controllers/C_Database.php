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
        $this->M_Coin_Data = model(M_Coin_Data::class); // Call M)Coin_Data model by $this->M_Coin_Data->method()
    }

    public function Database() 
    {
        helper(['form', 'url']);
        $request = \Config\Services::request();
        $uri = $request->getUri();
        $coin = $uri->getSegment(2);
        $days = $uri->getSegment(3);
        $searchInput = $request->getPost('search_day');

        $data["coin"] = $coin;
        $data["coinname"] = $this->M_Coin_Data->get_coinname_by_id($coin);
        $data["days"] = $days;
        $data['record']= $this->M_Coin_Data->get_data_by_coin_id_n_day($coin, $days);
        $data['search_day'] = $searchInput;


        if (!empty($searchInput)) {
            $days = (int)$searchInput;
            return redirect()->to('/public/database/' . $coin . '/' . $days);
        }

        $rows = $this->M_Coin_Data->get_data_for_candlestickchart($coin, $days);

        $data['table'] = [];
        foreach ($rows as $row) {
            $data['table'][] = [
            $row['date'],                   // label
            (float)$row['low_price'],       // low
            (float)$row['open_price'],      // open
            (float)$row['close_price'],     // close
            (float)$row['high_price']       // high
            ];
        }

        $coin2 = $coin * 2 /3;
        $rowsma20 = $this->M_Coin_Data->get_ma20($days, $coin);

        $data['ma20'] = [];
        foreach ($rowsma20 as $row) {
            $data['ma20'][] = [
            $row['date'],                   // label
            (float)$row['ma20'],            // ma20
            ];
        }

        // reverse the rows order
        $data['table'] = array_reverse($data['table']);
  

        $data['table'] = json_encode($data['table'], JSON_NUMERIC_CHECK);
        $data['ma20'] = array_reverse($data['ma20']);
        $data['ma20'] = json_encode($data['ma20'], JSON_NUMERIC_CHECK);

        return view('V_Database', $data);
 
    }

        // import data for latest 1 year, 12h interval
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
        return redirect()->to('/public/database/1/50')->with('success', 'Data imported successfully to database!');
    }

    // import daily data for today, 12h interval
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
            curl_close($ch);
            return $this->response->setStatusCode(500)->setJSON(['error' => "cURL Error: $error"]);
            }
            curl_close($ch);

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
        return redirect()->to('/public/database/1/50')->with('success', 'Data imported successfully to database!');
    }

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
        return redirect()->to('/public/database/1/50')->with('success', 'Data imported successfully to database!');
    }

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
        return redirect()->to('/public/database/1/50')->with('success', 'Data imported successfully to database!');
    }
}

