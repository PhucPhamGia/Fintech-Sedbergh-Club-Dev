<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Controllers\BaseController;
use App\Models\M_Coin_Data;

class C_Dashboard extends BaseController
{
    protected $M_Coin_Data;
    public function __construct()
    {
        $this->M_Coin_Data = model(M_Coin_Data::class); // Call M)Coin_Data model by $this->M_Coin_Data->method()
    }

    public function Model_Data() // Return list_coin and all_data to view
    {
       
        return view('V_Dashboard');
    }
}

