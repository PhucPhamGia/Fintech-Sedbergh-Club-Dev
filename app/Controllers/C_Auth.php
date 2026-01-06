<?php

// Handle authentication such as login, logout, and registration
namespace App\Controllers;
use App\Models\M_Users;
use App\Models\M_Auth;
use Config\Services;
use CodeIgniter\I18n\Time;
//push test
class C_Auth extends BaseController
{
    protected $M_Users;
    protected $M_Auth;

    private const REMEMBER_COOKIE = 'remember_me';
    private const REMEMBER_TTL    = 2592000; // 30 days

    private const LOGIN_THROTTLE_WINDOW      = 1800; // 30 minutes
    private const LOGIN_THROTTLE_IP_CAPACITY = 20;  // attempts per window
    private const LOGIN_THROTTLE_ID_CAPACITY = 5;   // attempts per window

    public function __construct()
    {
        $this->M_Users = new M_Users();
        $this->M_Auth = new M_Auth();
    }

    public function Login_Post()
    {
        $username = trim((string)$this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        # Validation rules
        $rules = [
            // Allow email login (emails often exceed 25 chars)
            'username' => 'required|min_length[4]|max_length[100]|regex_match[/^\S+$/]',
            'password' => 'required|min_length[6]|max_length[255]', # Length 6 - 255, required
        ];

        # Error messages
        $messages = [
            'username' => [
                'required'    => 'Username or email is required.',
                'min_length'  => 'Username or email is too short.',
                'max_length'  => 'Username or email is too long.',
                'regex_match' => 'Username or email must not contain spaces.',
            ],
            'password' => [
                'required'   => 'Password is required.',
                'min_length' => 'Password must be at least 6 characters.',
                'max_length' => 'Password is too long.',
            ],
        ];

        # Error handling
        if (! $this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode("\n", $this->validator->getErrors()));
        }

        // Brute-force protection: rate limit by IP and by username/email.
        // Uses the token-bucket throttler backed by the configured cache handler.
        $throttler = null;
        $ip        = null;
        $idKey     = null;
        try {
            $throttler = Services::throttler();
            $ip        = $this->request->getIPAddress();

            # Throttle by IP
            if (! $throttler->check('login-ip-' . $ip, self::LOGIN_THROTTLE_IP_CAPACITY, self::LOGIN_THROTTLE_WINDOW)) {
                $wait = max(1, $throttler->getTokenTime());

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Too many login attempts. Try again in ' . $wait . ' seconds.');
            }

            # Throttle by username/email
            if ($username !== '') {
                $idKey = 'login-id-' . hash('sha256', strtolower($username));

                if (! $throttler->check($idKey, self::LOGIN_THROTTLE_ID_CAPACITY, self::LOGIN_THROTTLE_WINDOW)) {
                    $wait = max(1, $throttler->getTokenTime());

                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Too many login attempts. Try again in ' . $wait . ' seconds.');
                }
            }
        } catch (\Throwable $e) {
            // If throttling fails (e.g., cache misconfigured), do not block login.
            log_message('warning', 'Login throttling unavailable: ' . $e->getMessage());
        }

        try {
            $users = $this->M_Users;
            $auth = $this->M_Auth;

            // If user typed an email, normalize casing to match registration normalization.
            $loginValue = $username;
            $loginEmail = null;
            if (filter_var($loginValue, FILTER_VALIDATE_EMAIL)) {
                $loginEmail = strtolower($loginValue);
            }

            // fetch user by username OR email
            $auth = $auth->groupStart()
                ->where('username', $loginValue)
                ->orWhere('email', $loginEmail ?? $loginValue)
                ->groupEnd()
                ->first();

            $user = $users->where('id', $auth['id'] ?? 0)->first();
        } catch (\Throwable $e) {
            log_message('error', 'Login query failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Login is temporarily unavailable.');
        }

        # User not found
        if (! $auth) {
            return redirect()->back()->withInput()->with('error', 'Invalid username/password.');
        }

        $userId = null;
        $userNameValue = null;
        $userStatus = null;
        $storedPassword = null;

        # Get user profile info
        if (is_array($user)) {
            $userId        = $user['id'] ?? null;
            $userStatus    = $user['status'] ?? null;
        } elseif (is_object($user)) {
            $userId        = $user->id ?? null;
            $userStatus    = $user->status ?? null;
        }

        # Get auth info (username, password)
        if (is_array($auth)) {
            $userNameValue  = $auth['username'] ?? null;
            $storedPassword = $auth['password'] ?? null;
        } elseif (is_object($auth)) {
            $userNameValue  = $auth->username ?? null;
            $storedPassword = $auth->password ?? null;
        }

        if (! $userId) {
            return redirect()->back()->withInput()->with('error', 'Invalid username/password.');
        }

        # Check if account is banned
        if ($userStatus === 'banned') {
            return redirect()->back()->withInput()->with('error', 'This account has been banned. Please contact support for further assistance.');
        }

        # Verify password
        $isValidPassword = false;
        if (is_string($storedPassword) && $storedPassword !== '') {
            // Support legacy MD5 (32 hex chars) and modern password_hash() values
            if (preg_match('/^[a-f0-9]{32}$/i', $storedPassword) === 1) {
                $isValidPassword = hash_equals(strtolower($storedPassword), md5($password));
            } else {
                $isValidPassword = password_verify($password, $storedPassword);
            }
        }

        # Wrong format or invalid password
        if (! $isValidPassword) {
            return redirect()->back()->withInput()->with('error', 'Invalid username/password.');
        }

        // Upgrade legacy MD5 or weak/old hashes to the current default algorithm.
        if (
            is_string($storedPassword)
            && $storedPassword !== ''
            && (
                preg_match('/^[a-f0-9]{32}$/i', $storedPassword) === 1
                || password_needs_rehash($storedPassword, PASSWORD_DEFAULT)
            )
        ) {
            try {
                $newHash = password_hash($password, PASSWORD_DEFAULT);

                if ($userId !== null) {
                    $auth = $this->M_Auth;
                    $auth->update($userId, ['password' => $newHash]);
                }
            } catch (\Throwable $e) {
                // Ignore upgrade failure (e.g., column doesn't exist) and continue login.
                log_message('warning', 'Password rehash upgrade skipped: ' . $e->getMessage());
            }
        }

        // Establish session securely.
        $session = session();
        $session->regenerate(true);

        $session->set([
            'user_id'   => $userId,
            'username'  => $userNameValue,
            'logged_in' => true,
        ]);

        // Remember-me: store a random token (hashed server-side) and set a cookie.
        if ($userId) {
            if ($remember) {
                try {
                    $selector  = bin2hex(random_bytes(9));
                    $validator = bin2hex(random_bytes(32));

                    $auth = $this->M_Auth;
                    $auth->update($userId, [
                        'remember_selector'   => $selector,
                        'remember_hash'       => hash('sha256', $validator),
                        'remember_expires_at' => Time::now()->addSeconds(self::REMEMBER_TTL)->toDateTimeString(),
                    ]);

                    $this->response->setCookie(
                        self::REMEMBER_COOKIE,
                        $selector . ':' . $validator,
                        self::REMEMBER_TTL,
                        '',
                        '/',
                        '',
                        $this->request->isSecure(),
                        true,
                        'Lax'
                    );
                } catch (\Throwable $e) {
                    log_message('warning', 'Remember-me setup skipped: ' . $e->getMessage());
                }
            } else {
                // If user did not request remember-me, clear any previous token.
                try {
                    $auth = $this->M_Auth;
                    $auth->update($userId, [
                        'remember_selector'   => null,
                        'remember_hash'       => null,
                        'remember_expires_at' => null,
                    ]);
                } catch (\Throwable $e) {
                    // ignore
                }

                $this->response->deleteCookie(self::REMEMBER_COOKIE);
            }
        }

        // Successful login: clear throttling buckets so valid users aren't locked out.
        if ($throttler !== null) {
            try {
                if ($ip !== null) {
                    $throttler->remove('login-ip-' . $ip);
                }
                if ($idKey !== null) {
                    $throttler->remove($idKey);
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return redirect()->to('/dashboard')->with('success', 'Login successful.');
    }

    public function Register_Post() // Handle registration form submission
    {
        $first_name = trim((string) $this->request->getPost('first_name'));
        $last_name = trim((string) $this->request->getPost('last_name'));
        $username = trim((string) $this->request->getPost('username'));
        // Normalize email to reduce false duplicates due to casing/whitespace.
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        # Validation rules
        $rules = [
            'first_name'       => 'required|min_length[2]|max_length[50]',
            'last_name'        => 'required|min_length[2]|max_length[50]',
            'username'         => 'required|min_length[4]|max_length[25]|regex_match[/^\S+$/]',
            'email'            => 'required|valid_email|max_length[100]',
            'password'         => 'required|min_length[6]|max_length[255]|matches[password_confirm]',
            'password_confirm' => 'required|min_length[6]|max_length[255]',
        ];

        # Error messages
        $messages = [
            'first_name' => [
                'required'   => 'First name is required.',
                'min_length' => 'First name must be at least 2 characters.',
                'max_length' => 'First name must not exceed 50 characters.',
            ],
            'last_name' => [
                'required'   => 'Last name is required.',
                'min_length' => 'Last name must be at least 2 characters.',
                'max_length' => 'Last name must not exceed 50 characters.',
            ],
            'username' => [
                'required'    => 'Username is required.',
                'min_length'  => 'Username must be at least 4 characters.',
                'max_length'  => 'Username must not exceed 25 characters.',
                'regex_match' => 'Username must not contain spaces.',
                'is_unique'   => 'Username is already taken.',
            ],
            'email' => [
                'required'    => 'Email is required.',
                'valid_email' => 'Please enter a valid email address.',
                'max_length'  => 'Email must not exceed 100 characters.',
                'is_unique'   => 'Email is already registered.',
            ],
            'password' => [
                'required'   => 'Password is required.',
                'min_length' => 'Password must be at least 6 characters.',
                'max_length' => 'Password must not exceed 255 characters.',
                'matches'    => 'Passwords do not match.',
            ],
            'password_confirm' => [
                'required'   => 'Password confirmation is required.',
                'min_length' => 'Password confirmation must be at least 6 characters.',
                'max_length' => 'Password confirmation must not exceed 255 characters.',
            ],
        ];
        # Error handling
        if (! $this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode("\n", $this->validator->getErrors()));
        }

        // Create new user (atomic). We rely on DB unique constraints and handle duplicate-key errors.
        try {
            $authModel  = $this->M_Auth;
            $usersModel = $this->M_Users;
            $now        = Time::now()->toDateTimeString();

            // Both models should share the same connection group; use it for an atomic registration.
            $conn = $authModel->db;
            $conn->transBegin();

            try {
                // Insert auth row first
                $authId = $authModel->insert([
                    'username'   => $username,
                    'email'      => $email,
                    'password'   => password_hash($password, PASSWORD_DEFAULT),
                    'created_at' => $now,
                ], true);

                if (! is_numeric($authId) || (int) $authId <= 0) {
                    // If Model insert fails without throwing, surface a generic error.
                    throw new \RuntimeException('Failed to create auth record.');
                }

                $authId = (int) $authId;

                // Insert users row with matching id (required by login flow)
                $usersOk = $usersModel->insert([
                    'id'         => $authId,
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'role'       => 'User',
                    'status'     => 'active',
                    'created_at' => $now,
                ], false);

                if ($usersOk === false) {
                    throw new \RuntimeException('Failed to create user profile record.');
                }

                $conn->transCommit();
            } catch (\Throwable $e) {
                $conn->transRollback();

                // Handle duplicate key errors gracefully.
                $msg = strtolower($e->getMessage());
                if (str_contains($msg, 'duplicate') || str_contains($msg, '1062')) {
                    if (str_contains($msg, 'email')) {
                        return redirect()->back()->withInput()->with('error', 'Email is already registered.');
                    }
                    if (str_contains($msg, 'username')) {
                        return redirect()->back()->withInput()->with('error', 'Username is already taken.');
                    }
                    return redirect()->back()->withInput()->with('error', 'Email or username is already registered.');
                }

                throw $e;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Registration failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Registration is temporarily unavailable.');
        }

        return redirect()->to('/login')->with('success', 'Registration successful. You can now log in.');
    }

    public function Forgot_Password() // Forgot password
    {
        // Logic to show forgot password page
    }
    
    public function Logout() // Handle user logout
    {
        $session = session();

        // Clear remember-me token for this user.
        $userId = $session->get('user_id');
        if ($userId) {
            try {
                $auth = $this->M_Auth;
                $auth->update($userId, [
                    'remember_selector'   => null,
                    'remember_hash'       => null,
                    'remember_expires_at' => null,
                ]);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $this->response->deleteCookie(self::REMEMBER_COOKIE);
        $session->destroy();

        return redirect()->to('/login')->with('message', 'Logged out.');
    }
}