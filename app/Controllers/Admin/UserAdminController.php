<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Libraries\ActivityLogService;

class UserAdminController extends BaseController
{
    protected AdminModel $model;

    public function __construct()
    {
        $this->model = new AdminModel();
    }

    public function index()
    {
        $users = $this->model->findAllManageable();

        return view('admin/users/index', [
            'title' => 'Kelola Akun Admin',
            'rows' => admin_user_index_rows($users, (int) session()->get('admin_id')),
        ]);
    }

    public function create()
    {
        return view('admin/users/form', $this->formViewData(null));
    }

    public function store()
    {
        $email = $this->request->getPost('email');

        if ($this->model->findByEmail($email)) {
            return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar.');
        }

        $password = $this->request->getPost('password');
        if (strlen($password) < 8) {
            return redirect()->back()->withInput()->with('error', 'Password minimal 8 karakter.');
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'admin',
            'status' => $this->request->getPost('status') ?: 'aktif',
        ];

        $id = $this->model->insert($data);
        ActivityLogService::log('menambah akun admin: ' . $data['nama'], 'admin_users', (int) $id);

        return redirect()->to(site_url('admin/users'))->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $user = $this->findManageableUser($id);
        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Akun tidak ditemukan.');
        }

        return view('admin/users/form', $this->formViewData($user));
    }

    public function update(int $id)
    {
        $user = $this->findManageableUser($id);
        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Akun tidak ditemukan.');
        }

        $email = $this->request->getPost('email');
        $existing = $this->model->findByEmail($email);
        if ($existing && (int) $existing['id'] !== $id) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan akun lain.');
        }

        $status = $this->request->getPost('status') ?: 'aktif';
        if ($id === (int) session()->get('admin_id') && $status === 'nonaktif') {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $email,
            'role' => 'admin',
            'status' => $status,
        ];

        $password = $this->request->getPost('password');
        if ($password) {
            if (strlen($password) < 8) {
                return redirect()->back()->withInput()->with('error', 'Password minimal 8 karakter.');
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->model->update($id, $data);
        ActivityLogService::log('mengubah akun admin: ' . $data['nama'], 'admin_users', $id);

        return redirect()->to(site_url('admin/users'))->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if ($id === (int) session()->get('admin_id')) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user = $this->findManageableUser($id);
        if ($user) {
            try {
                $this->model->delete($id);
                ActivityLogService::log('menghapus akun admin: ' . $user['nama'], 'admin_users', $id);
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Akun tidak dapat dihapus karena masih terkait data lain. Nonaktifkan saja.');
            }
        }

        return redirect()->to(site_url('admin/users'))->with('success', 'Akun berhasil dihapus.');
    }

    private function findManageableUser(int $id): ?array
    {
        $user = $this->model->find($id);

        if (!$user || ($user['role'] ?? '') === 'superadmin') {
            return null;
        }

        return $user;
    }

    private function formViewData(?array $user): array
    {
        $id = isset($user['id']) ? (int) $user['id'] : null;

        return [
            'title' => $user ? 'Edit Admin' : 'Tambah Admin',
            'form' => [
                'action' => admin_crud_action('admin/users', $id),
                'backUrl' => site_url('admin/users'),
                'isEdit' => $user !== null,
                'nama' => admin_form_value('nama', $user['nama'] ?? ''),
                'email' => admin_form_value('email', $user['email'] ?? ''),
                'statusOptions' => admin_select_options([
                    'aktif' => 'Aktif',
                    'nonaktif' => 'Nonaktif',
                ], admin_form_raw('status', $user['status'] ?? 'aktif')),
            ],
        ];
    }
}
