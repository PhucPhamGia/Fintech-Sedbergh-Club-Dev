<?php

// Control view-related functions such as /login and /register pages

namespace App\Controllers;


class C_View extends BaseController
{
    // Show dashboard page
    public function Dashboard()
    {
        return view('V_Dashboard');
    }

    // Show login page
    public function Login()
    {
        helper('cookie');
        $savedUser = get_cookie('username') ?? '';

        // Redirect to dashboard if already logged in
        if (session()->get('isLoggedIn') === true) {
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