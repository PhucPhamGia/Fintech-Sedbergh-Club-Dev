<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PublicPathFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        if (!str_starts_with($uri, '/public/') && $uri !== '/public') {
            return;
        }

        // Strip /public prefix, preserve query string
        $cleanPath = substr($uri, strlen('/public')) ?: '/';

        $response = service('response');
        $response->setStatusCode(200);
        $response->setBody(view('V_PublicRedirect', ['clean_path' => $cleanPath]));

        return $response;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
