<?php

namespace App\Models;

use CodeIgniter\Model;

class PedagangModel extends Model
{
    protected $table = 'pedagang';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'aktif'];
    protected $useTimestamps = false;
    protected $returnType = 'array';
}