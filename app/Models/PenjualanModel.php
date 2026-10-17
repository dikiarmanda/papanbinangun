<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pedagang_id', 'bulan', 'keping', 'omzet'];
    protected $useTimestamps = false;
    protected $returnType = 'array';
}