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
        return view('V_Home');
    }

    // Show dashboard page
    public function Dashboard()
    {
        return view('V_Dashboard');
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
            $tableData[] = [
                $row['date'],
                (float)$row['low_price'],
                (float)$row['open_price'],
                (float)$row['close_price'],
                (float)$row['high_price'],
            ];
            $ma20Data[] = [
                $row['date'],
                $row['ma20'] !== null ? (float)$row['ma20'] : null,
            ];
            $ma50Data[] = [
                $row['date'],
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

    public function Forgot_Password()
    {
        return view('V_Forgot_Password');
    }
}