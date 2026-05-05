<?php

// Control view-related functions such as /login and /register pages

namespace App\Controllers;
use App\Models\M_Coin_Data;
use App\Models\M_Auth;
use App\Models\M_Users;


class C_View extends BaseController
{
    protected $M_Auth;
    protected $M_Users;
    public function __construct()
    {
        helper('url');
        $this->M_Auth = new M_Auth(); // Call M_Auth model by $this->M_Auth->method()
        $this->M_Users = new M_Users();
    }

    public function Home()
    {
        $M_Coin_Data = new M_Coin_Data();
        $coins = $M_Coin_Data->get_list_coin();

        $maData = [];
        foreach ($coins as $coin) {
            $rows = $M_Coin_Data->get_ma20(1, $coin['id_coin'], '1h');
            $maData[$coin['coinname']] = $rows[0] ?? null;
        }

        return view('V_Home', ['coins' => $coins, 'maData' => $maData]);
    }

    // Show dashboard page
    public function Dashboard()
    {
        $M_Coin_Data = new M_Coin_Data();
        $userId      = (int) session()->get('user_id');
        $grassShown  = $userId ? $this->M_Users->hasAchievement($userId, 'grass') : true;
        $coins       = $M_Coin_Data->get_list_coin();

        $defaultCoinId = (int)($coins[0]['id_coin'] ?? 1);
        $records       = $M_Coin_Data->get_data_by_coin_id_n_day($defaultCoinId, 30, '1h');

        $tableData = $ma20Data = $ma50Data = [];
        foreach ($records as $row) {
            $dt          = gmdate('Y-m-d\TH:i:s', (int)($row['open_time'] / 1000));
            $tableData[] = [$dt, (float)$row['low_price'], (float)$row['open_price'], (float)$row['close_price'], (float)$row['high_price']];
            $ma20Data[]  = [$dt, $row['ma20'] !== null ? (float)$row['ma20'] : null];
            $ma50Data[]  = [$dt, $row['ma50'] !== null ? (float)$row['ma50'] : null];
        }

        return view('V_Dashboard', [
            'coins'      => $coins,
            'grassShown' => $grassShown,
            'chartTable' => json_encode($tableData, JSON_NUMERIC_CHECK),
            'chartMa20'  => json_encode($ma20Data,  JSON_NUMERIC_CHECK),
            'chartMa50'  => json_encode($ma50Data,  JSON_NUMERIC_CHECK),
        ]);
    }

    public function Chart_Data($coin_id, $timeframe)
    {
        $validTf = ['15m' => 14, '30m' => 21, '1h' => 30, '4h' => 90, '6h' => 120, '12h' => 180];
        if (!isset($validTf[$timeframe])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid timeframe']);
        }

        $M_Coin_Data = new M_Coin_Data();
        $records     = $M_Coin_Data->get_data_by_coin_id_n_day((int)$coin_id, $validTf[$timeframe], $timeframe);

        $tableData = $ma20Data = $ma50Data = [];
        foreach ($records as $row) {
            $dt          = gmdate('Y-m-d\TH:i:s', (int)($row['open_time'] / 1000));
            $tableData[] = [$dt, (float)$row['low_price'], (float)$row['open_price'], (float)$row['close_price'], (float)$row['high_price']];
            $ma20Data[]  = [$dt, $row['ma20'] !== null ? (float)$row['ma20'] : null];
            $ma50Data[]  = [$dt, $row['ma50'] !== null ? (float)$row['ma50'] : null];
        }

        return $this->response->setJSON([
            'table' => $tableData,
            'ma20'  => $ma20Data,
            'ma50'  => $ma50Data,
        ]);
    }

    public function Database()
    {
        $M_Coin_Data = new M_Coin_Data();

        $request = \Config\Services::request();
        $uri = $request->getUri();
        $coin      = $uri->getSegment(2);
        $timeframe = $uri->getSegment(3) ?: '12h';
        $days      = $uri->getSegment(4) ?: 100;
        $searchInput = $request->getPost('search_day');

        $validTimeframes = ['15m', '30m', '1h', '4h', '6h', '12h'];
        if (!in_array($timeframe, $validTimeframes)) $timeframe = '12h';

        // If user submits search form, redirect with new days value
        if (!empty($searchInput)) {
            $days = (int)$searchInput;
            return redirect()->to('database/' . $coin . '/' . $timeframe . '/' . $days);
        }

        // Fetch all necessary data — single query, ASC order
        $coinName = $M_Coin_Data->get_coinname_by_id($coin);
        $records = $M_Coin_Data->get_data_by_coin_id_n_day($coin, $days, $timeframe);

        // Derive chart data from the same record set (ensures chart and table are in sync)
        $tableData = [];
        $ma20Data  = [];
        $ma50Data  = [];
        foreach ($records as $row) {
            $dt = gmdate('Y-m-d\TH:i:s', (int)($row['open_time'] / 1000));
            $tableData[] = [
                $dt,
                (float)$row['low_price'],
                (float)$row['open_price'],
                (float)$row['close_price'],
                (float)$row['high_price'],
            ];
            $ma20Data[] = [
                $dt,
                $row['ma20'] !== null ? (float)$row['ma20'] : null,
            ];
            $ma50Data[] = [
                $dt,
                $row['ma50'] !== null ? (float)$row['ma50'] : null,
            ];
        }
        // Prepare data for view
        $data = [
            'coin'      => $coin,
            'coinname'  => $coinName,
            'timeframe' => $timeframe,
            'days'      => $days,
            'record'    => $records,
            'search_day'=> $searchInput,
            'table'     => json_encode($tableData, JSON_NUMERIC_CHECK),
            'ma20'      => json_encode($ma20Data, JSON_NUMERIC_CHECK),
            'ma50'      => json_encode($ma50Data, JSON_NUMERIC_CHECK)
        ];

        // Get user role from database (not from session to prevent spoofing)
        $userId = session()->get('user_id');
        $user = $this->M_Users->find($userId);
        
        if (!$user) {
            session()->destroy();
            return redirect()->to('/login');
        }

        $userRole = $user['role'] ?? null;
        
        if ($userRole === 'Admin') {
            return view('admin/V_Database_Admin', $data);
        } else {
            return view('V_Database', $data);
        }
    }

    // Show login page
    public function Login()
    {
        helper('cookie');
        $savedUser = get_cookie('username') ?? '';

        // Redirect to dashboard if already logged in
        if (session()->get('logged_in') === true) {
            return redirect()->to('/dashboard');
        }

        return view('V_Login', ['savedUser' => $savedUser]);
    }

    // Show registration page
    public function Register()
    {
        return view('V_Register');
    }

    public function Profile()
    {
        return view('V_Profile');
    }

    public function Forgot_Password()
    {
        return view('V_Forgot_Password');
    }

    public function Achievements_Admin()
    {
        $userId = (int) session()->get('user_id');

        $defined = [
            'grass' => [
                'key'          => 'grass',
                'label'        => 'Go touch grass.',
                'desc'         => 'Stay on the dashboard for 1 hour.',
                'icon'         => 'grass',
                'canvas_color' => '#34D399',
                'toast_color'  => '#34D399',
                'toast_bg'     => 'rgba(52,211,153,0.12)',
                'toast_border' => 'rgba(52,211,153,0.25)',
            ],
            'dummy' => [
                'key'          => 'dummy',
                'label'        => 'Certified Nerd.',
                'desc'         => 'You found the Achievement Lab.',
                'icon'         => 'academic_cap',
                'canvas_color' => '#9CA3AF',
                'toast_color'  => '#38BDF8',
                'toast_bg'     => 'rgba(56,189,248,0.12)',
                'toast_border' => 'rgba(56,189,248,0.25)',
            ],
        ];

        foreach ($defined as $key => &$a) {
            $a['earned'] = $this->M_Users->hasAchievement($userId, $key);
        }
        unset($a);

        return view('admin/V_Achievements_Admin', [
            'achievements' => $defined,
            'userId'       => $userId,
        ]);
    }

    public function Public_Redirect($path = '')
    {
        return redirect()->to('/' . $path, 301);
    }
}