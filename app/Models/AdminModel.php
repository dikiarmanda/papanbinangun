<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'admin_users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'nama',
        'email',
        'password',
        'role',
        'status',
        'last_login_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'nama' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|max_length[150]',
        'role' => 'required|in_list[superadmin,admin]',
        'status' => 'required|in_list[aktif,nonaktif]',
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /** @return list<array> */
    public function findAllManageable(): array
    {
        return $this->where('role', 'admin')->orderBy('nama', 'ASC')->findAll();
    }
}
