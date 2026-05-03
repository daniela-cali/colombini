<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAreaFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (auth()->loggedIn() && auth()->user()->inGroup('cliente')) {
            return redirect()->to(base_url('portale'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
