<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By returning false, it will stop processing of subsequent filters.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (session()->get('logged_in') !== true) {
            return redirect()->to('/login');
        }

        // Also verify the user still exists in the database to prevent spoofed sessions
        $userId = session()->get('user_id');
        if (!$userId) {
            session()->destroy();
            return redirect()->to('/login');
        }

        $users = new \App\Models\M_Users();
        $user = $users->find($userId);

        if (!$user) {
            session()->destroy();
            return redirect()->to('/login');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop processing of subsequent filters, since nothing
     * can intelligently determine the response will continue
     * to be used or not. If you need that capability,
     * an exception should be thrown instead.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
