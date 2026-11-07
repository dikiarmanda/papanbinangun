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

        $stats = cache()->remember('admin_dashboard_stats', 120, function () {
            return [
                'totalArtikel'   => (new ArtikelModel())->countAllResults(true),
                'artikelPublish' => (new ArtikelModel())->where('status', 'publish')->countAllResults(true),
                'artikelDraft'   => (new ArtikelModel())->where('status', 'draft')->countAllResults(true),
                'totalWisata'    => (new WisataModel())->countAllResults(true),
                'totalGaleri'    => (new GaleriModel())->countAllResults(true),
                'totalAdmin'     => (new AdminModel())->countAllResults(true),
            ];
        });

        return view('admin/dashboard', [
            'title' => 'Dashboard',
            'isSuperadmin' => $isSuperadmin,
            'totalArtikel' => $stats['totalArtikel'],
            'artikelPublish' => $stats['artikelPublish'],
            'artikelDraft' => $stats['artikelDraft'],
            'totalWisata' => $stats['totalWisata'],
            'totalGaleri' => $stats['totalGaleri'],
            'totalAdmin' => $isSuperadmin ? $stats['totalAdmin'] : 0,
            'artikelTerbaru' => admin_dashboard_artikel_rows(
                $artikelModel->orderBy('created_at', 'DESC')->findAll(5),
            ),
            'logs' => admin_dashboard_log_rows($logs),
        ]);
    }
}
