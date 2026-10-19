<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pedagang_id', 'bulan', 'minggu_ke', 'keping', 'omzet', 'ppn', 'hasil_bersih'];
    protected $useTimestamps = false;
    protected $returnType = 'array';
}