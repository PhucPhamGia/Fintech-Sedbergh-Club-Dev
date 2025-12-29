<?php

// Handle authentication such as login, logout, and registration

namespace App\Controllers;


class C_Auth extends BaseController
{
    public function Login_Post() // Handle login form submission
    {
        // Login logic here (validate user, set session, etc.)
    }

    public function Register_Post() // Handle registration form submission
    {
        // Registration logic here (validate input, create user, etc.)
    }

    public function Forgot_Password() // Forgot password
    {
        // Logic to show forgot password page
    }
    
    public function Logout() // Handle user logout
    {
        // Logout logic here (destroy session, redirect to login, etc.)
    }
}