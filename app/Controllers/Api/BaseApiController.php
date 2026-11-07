<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BaseApiController extends BaseController
{
    public function options(): ResponseInterface
    {
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setStatusCode(204);
    }

    protected function getJsonInput(): array
    {
        try {
            $json = $this->request->getJSON(true);
            if (is_array($json) && !empty($json)) {
                return $json;
            }
        } catch (\Throwable $e) {
            // Fallback jika getJSON() melempar HTTPException
        }

        $raw = $this->request->getBody();
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }

        $phpInput = file_get_contents('php://input');
        if ($phpInput) {
            $decoded = json_decode($phpInput, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }

        return $this->request->getPost() ?: [];
    }

    protected function respondJson(bool $success, $data = null, string $message = '', int $httpCode = 200): ResponseInterface
    {
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setStatusCode($httpCode)
            ->setJSON([
                'success' => $success,
                'message' => $message,
                'data'    => $data,
            ]);
    }
}
