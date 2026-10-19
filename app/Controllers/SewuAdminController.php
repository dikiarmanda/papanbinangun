<?php

namespace App\Controllers;

use App\Models\PedagangModel;
use App\Models\PenjualanModel;

class SewuAdminController extends BaseController
{
    public function index()
    {
        $pedagangModel = new PedagangModel();
        $penjualanModel = new PenjualanModel();

        // Hanya tampilkan data yang diinput HARI INI
        $penjualan = $penjualanModel
            ->select('penjualan.*, pedagang.nama')
            ->join('pedagang', 'pedagang.id = penjualan.pedagang_id')
            ->where('DATE(penjualan.created_at)', date('Y-m-d'))
            ->orderBy('penjualan.id', 'DESC')
            ->findAll();

        $data = [
            'pedagang'  => $pedagangModel->orderBy('nama', 'ASC')->findAll(),
            'penjualan' => $penjualan,
            'title'     => 'Admin SEWU DATA'
        ];
        
        return view('sewu_data/admin', $data);
    }

    public function simpan()
    {
        $session = session();
        $penjualanModel = new PenjualanModel();

        $omzet = (int) $this->request->getPost('omzet');
        $ppn = $omzet * 0.20;
        $hasil_bersih = $omzet - $ppn;

        $data = [
            'pedagang_id'  => $this->request->getPost('pedagang_id'),
            'bulan'        => $this->request->getPost('bulan'),
            'minggu_ke'    => (int) $this->request->getPost('minggu_ke'),
            'keping'       => $this->request->getPost('keping'),
            'omzet'        => $omzet,
            'ppn'          => $ppn,
            'hasil_bersih' => $hasil_bersih,
        ];

        if (empty($data['pedagang_id']) || empty($data['bulan']) || empty($data['minggu_ke']) || empty($data['keping']) || empty($data['omzet'])) {
            $session->setFlashdata('error', 'Semua field wajib diisi.');
            return redirect()->to(base_url('sewu-data/admin'));
        }

        if ($penjualanModel->save($data)) {
            $session->setFlashdata('sukses', 'Data Minggu ' . $data['minggu_ke'] . ' berhasil disimpan!');
        } else {
            $session->setFlashdata('error', 'Gagal menyimpan data.');
        }

        return redirect()->to(base_url('sewu-data/admin'));
    }

    public function hapus($id)
    {
        $session = session();
        $penjualanModel = new PenjualanModel();

        if ($penjualanModel->find($id)) {
            $penjualanModel->delete($id);
            $session->setFlashdata('sukses', 'Data penjualan berhasil dihapus.');
        } else {
            $session->setFlashdata('error', 'Data tidak ditemukan.');
        }

        return redirect()->to(base_url('sewu-data/admin'));
    }

    // Tampilkan form edit
    public function edit($id)
    {
        $pedagangModel = new PedagangModel();
        $penjualanModel = new PenjualanModel();

        $penjualan = $penjualanModel->find($id);

        if (!$penjualan) {
            session()->setFlashdata('error', 'Data tidak ditemukan.');
            return redirect()->to(base_url('sewu-data/admin'));
        }

        $dataPenjualan = $penjualanModel
            ->select('penjualan.*, pedagang.nama')
            ->join('pedagang', 'pedagang.id = penjualan.pedagang_id')
            ->where('DATE(penjualan.created_at)', date('Y-m-d'))
            ->orderBy('penjualan.id', 'DESC')
            ->findAll();

        $data = [
            'pedagang'  => $pedagangModel->orderBy('nama', 'ASC')->findAll(),
            'penjualan' => $dataPenjualan,
            'edit_data' => $penjualan, // Kirim data ke view
            'title'     => 'Edit SEWU DATA'
        ];
        
        return view('sewu_data/admin', $data);
    }

    // Proses update data
    public function update($id)
    {
        $session = session();
        $penjualanModel = new PenjualanModel();

        $omzet = (int) $this->request->getPost('omzet');
        $ppn = $omzet * 0.20;
        $hasil_bersih = $omzet - $ppn;

        $data = [
            'pedagang_id'  => $this->request->getPost('pedagang_id'),
            'bulan'        => $this->request->getPost('bulan'),
            'minggu_ke'    => (int) $this->request->getPost('minggu_ke'),
            'keping'       => $this->request->getPost('keping'),
            'omzet'        => $omzet,
            'ppn'          => $ppn,
            'hasil_bersih' => $hasil_bersih,
        ];

        if ($penjualanModel->update($id, $data)) {
            $session->setFlashdata('sukses', 'Data berhasil diperbarui!');
        } else {
            $session->setFlashdata('error', 'Gagal memperbarui data.');
        }

        return redirect()->to(base_url('sewu-data/admin'));
    }
}