<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ArtikelModel;
use App\Models\WisataModel;
use App\Models\GaleriModel;
use App\Models\ActivityLogModel;
use App\Models\AdminModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $artikelModel = new ArtikelModel();
        $isSuperadmin = is_superadmin();
        $logs = [];

        if ($isSuperadmin) {
            $logs = (new ActivityLogModel())->getRecent(15);
        }

        return view('admin/dashboard', [
            'title' => 'Dashboard',
            'isSuperadmin' => $isSuperadmin,
            'totalArtikel' => $artikelModel->countAllResults(true),
            'artikelPublish' => $artikelModel->where('status', 'publish')->countAllResults(true),
            'artikelDraft' => $artikelModel->where('status', 'draft')->countAllResults(true),
            'totalWisata' => (new WisataModel())->countAllResults(true),
            'totalGaleri' => (new GaleriModel())->countAllResults(true),
            'totalAdmin' => $isSuperadmin ? (new AdminModel())->countAllResults(true) : 0,
            'artikelTerbaru' => admin_dashboard_artikel_rows(
                $artikelModel->orderBy('created_at', 'DESC')->findAll(5),
            ),
            'logs' => admin_dashboard_log_rows($logs),
        ]);
    }
}
