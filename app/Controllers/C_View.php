<?php

// Control view-related functions such as /login and /register pages

namespace App\Controllers;
use App\Models\M_Coin_Data;
use App\Models\M_Auth;


class C_View extends BaseController
{
    protected $M_Auth;
    public function __construct()
    {
        helper('url');
        $this->M_Auth = new M_Auth(); // Call M_Auth model by $this->M_Auth->method()
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
        $coin = $uri->getSegment(2);
        $days = $uri->getSegment(3);
        $searchInput = $request->getPost('search_day');

        // If user submits search form, redirect with new days value
        if (!empty($searchInput)) {
            $days = (int)$searchInput;
            return redirect()->to('database/' . $coin . '/' . $days);
        }

        // Fetch all necessary data
        $coinName = $M_Coin_Data->get_coinname_by_id($coin);
        $records = $M_Coin_Data->get_data_by_coin_id_n_day($coin, $days);
        $rows = $M_Coin_Data->get_data_for_candlestickchart($coin, $days);
        $rowsMA = $M_Coin_Data->get_ma20($days, $coin);

        // Prepare candlestick chart data
        $tableData = [];
        foreach ($rows as $row) {
            $tableData[] = [
                $row['date'],
                (float)$row['low_price'],
                (float)$row['open_price'],
                (float)$row['close_price'],
                (float)$row['high_price']
            ];
        }

        // Prepare moving average data
        $ma20Data = [];
        $ma50Data = [];
        foreach ($rowsMA as $row) {
            $ma20Data[] = [
                $row['date'],
                (float)$row['ma20']
            ];
            $ma50Data[] = [
                $row['date'],
                (float)$row['ma50']
            ];
        }

        // Reverse order for chart display
        $tableData = array_reverse($tableData);
        $ma20Data = array_reverse($ma20Data);
        $ma50Data = array_reverse($ma50Data);

        // Prepare data for view
        $data = [
            'coin'      => $coin,
            'coinname'  => $coinName,
            'days'      => $days,
            'record'    => $records,
            'search_day'=> $searchInput,
            'table'     => json_encode($tableData, JSON_NUMERIC_CHECK),
            'ma20'      => json_encode($ma20Data, JSON_NUMERIC_CHECK),
            'ma50'      => json_encode($ma50Data, JSON_NUMERIC_CHECK)
        ];

        // Get user role from database (not from session to prevent spoofing)
        $userId = session()->get('user_id');
        $user = $this->M_Auth->find($userId);
        
        if (!$user) {
            session()->destroy();
            return redirect()->to('/login');
        }

        $userRole = $user['role'] ?? null;
        
        if ($userRole === 'Admin') {
            return view('V_Database_Admin', $data);
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