<?php

namespace App\Controllers\Api;

use App\Libraries\TransaksiService;
use CodeIgniter\HTTP\ResponseInterface;

class ChatController extends BaseApiController
{
    public function kirim(): ResponseInterface
    {
        $input = $this->getJsonInput();
        $noHp  = trim((string) ($input['no_hp'] ?? ''));
        $nama  = trim((string) ($input['nama'] ?? ''));
        $pesan = trim((string) ($input['pesan'] ?? ''));

        if ($noHp === '' || $nama === '' || $pesan === '') {
            return $this->respondJson(false, null, 'Nama, no HP, dan pesan wajib diisi', 400);
        }

        // Finding 6.5 (flock) & 6.7 (public/chat + privacy)
        $ok = TransaksiService::simpanPesanChat($noHp, $nama, 'user', $pesan);
        if (!$ok) {
            return $this->respondJson(false, null, 'Gagal menyimpan pesan chat', 500);
        }

        return $this->respondJson(true, null, 'Pesan terkirim');
    }

    public function riwayat(): ResponseInterface
    {
        $noHp = trim((string) $this->request->getGet('no_hp'));
        if ($noHp === '') {
            return $this->respondJson(false, null, 'Parameter no_hp wajib diisi', 400);
        }

        // Finding 6.5 (flock) & 6.7 (public/chat)
        $data = TransaksiService::bacaChat($noHp);
        return $this->respondJson(true, $data ?? ['no_hp' => $noHp, 'nama' => '', 'pesan' => []], 'Riwayat chat');
    }
}
