<?php

namespace App\Libraries;

use App\Models\ActivityLogModel;

class ActivityLogService
{
    public static function log(string $aksi, ?string $targetTabel = null, ?int $targetId = null): void
    {
        $adminId = session()->get('admin_id');

        if (! $adminId) {
            return;
        }

        $model = new ActivityLogModel();
        $model->insert([
            'admin_id'     => $adminId,
            'aksi'         => $aksi,
            'target_tabel' => $targetTabel,
            'target_id'    => $targetId,
            'ip_address'   => service('request')->getIPAddress(),
        ]);
    }
}
