<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class DownloadController extends BaseController
{
    public function apk(): ResponseInterface
    {
        $possiblePaths = [
            FCPATH . 'upload' . DIRECTORY_SEPARATOR . 'papan_binangun.apk',
            FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'papan_binangun.apk',
            ROOTPATH . 'public' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'papan_binangun.apk',
            ROOTPATH . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'papan_binangun.apk',
        ];

        foreach ($possiblePaths as $path) {
            if (is_file($path)) {
                return $this->response->download($path, null)
                    ->setFileName('papan_binangun.apk')
                    ->setHeader('Content-Type', 'application/vnd.android.package-archive');
            }
        }

        throw PageNotFoundException::forPageNotFound('File papan_binangun.apk belum tersedia di folder public/upload.');
    }
}
