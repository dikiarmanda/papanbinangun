<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'admin_activity_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'admin_id', 'aksi', 'target_tabel', 'target_id', 'ip_address',
    ];
    protected $useTimestamps = false;

    public function getRecent(int $limit = 20): array
    {
        return $this->select('admin_activity_log.*, admin_users.nama as admin_nama')
            ->join('admin_users', 'admin_users.id = admin_activity_log.admin_id', 'left')
            ->orderBy('admin_activity_log.created_at', 'DESC')
            ->findAll($limit);
    }
}
