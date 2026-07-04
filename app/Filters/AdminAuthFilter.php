<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('admin_id')) {
            return redirect()->to(site_url('admin/login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($session->get('admin_status') === 'nonaktif') {
            $session->destroy();

            return redirect()->to(site_url('admin/login'))->with('error', 'Akun Anda dinonaktifkan.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
