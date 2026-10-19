<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · SEWU DATA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('sewu-data/css/style.css') ?>">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <div class="brand">
            <div class="brand-logo">SD</div>
            <div class="brand-text">
                <strong>SEWU DATA</strong>
                <small>Panel Manajer · Pasar Pring Sewu</small>
            </div>
        </div>
        <nav class="site-nav">
            <a href="<?= base_url('sewu-data/') ?>"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            <a href="<?= base_url('sewu-data/admin') ?>" class="aktif"><i class="fas fa-cog"></i> <span>Admin</span></a>
            <a href="<?= base_url('sewu-data/logout') ?>" style="color: #c1502e;"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </div>
</header>

<div class="app">
    <main class="konten" style="padding: 24px 28px;">
        
        <div class="konten-head">
            <div>
                <span class="stamp">Panel Manajer</span>
                <h1><?= isset($edit_data) ? 'Edit Data Penjualan' : 'Input Data Mingguan' ?></h1>
                <div class="sub">Selamat datang, <strong><?= session()->get('sewu_nama') ?></strong></div>
            </div>
        </div>

        <?php if (session()->getFlashdata('sukses')) : ?>
            <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?= session()->getFlashdata('sukses') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="grid-utama" style="grid-template-columns: 350px 1fr;">
            
            <!-- FORM INPUT / EDIT -->
            <div class="kotak" style="height: fit-content;">
                <h3><i class="fas fa-<?= isset($edit_data) ? 'edit' : 'plus-circle' ?>"></i> <?= isset($edit_data) ? 'Edit Penjualan' : 'Tambah Penjualan' ?></h3>
                <form method="post" action="<?= isset($edit_data) ? base_url('sewu-data/admin/update/' . $edit_data['id']) : base_url('sewu-data/admin/simpan') ?>" id="formPenjualan">
                    <?= csrf_field() ?>
                    
                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="display:block; margin-bottom:4px; font-size:13px;">Pilih Pedagang</label>
                        <select name="pedagang_id" required style="width:100%; padding:8px; border:1px solid #c9a66b; border-radius:6px; background:#f4e9d8;">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($pedagang as $p) : ?>
                                <option value="<?= $p['id'] ?>" <?= (isset($edit_data) && $edit_data['pedagang_id'] == $p['id']) ? 'selected' : '' ?>>
                                    <?= $p['nama'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                        <div class="form-group" style="flex: 1;">
                            <label style="display:block; margin-bottom:4px; font-size:13px;">Bulan</label>
                            <select id="inputBulan" required style="width:100%; padding:8px; border:1px solid #c9a66b; border-radius:6px; background:#f4e9d8;">
                                <option value="">-- Pilih --</option>
                                <?php 
                                    $bulanList = ['JAN','FEB','MAR','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOV','DES'];
                                    $selectedBulan = isset($edit_data) ? explode(' ', $edit_data['bulan'])[0] : '';
                                    $selectedTahun = isset($edit_data) ? explode(' ', $edit_data['bulan'])[1] : '2026';
                                    foreach($bulanList as $b) {
                                        $sel = ($b == $selectedBulan) ? 'selected' : '';
                                        echo "<option value='$b' $sel>$b</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label style="display:block; margin-bottom:4px; font-size:13px;">Tahun</label>
                            <input type="number" id="inputTahun" required value="<?= $selectedTahun ?>" min="2020" max="2030" style="width:100%; padding:8px; border:1px solid #c9a66b; border-radius:6px; background:#f4e9d8;">
                        </div>
                    </div>
                    <input type="hidden" name="bulan" id="hiddenBulan" value="<?= isset($edit_data) ? $edit_data['bulan'] : '' ?>">

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="display:block; margin-bottom:4px; font-size:13px;">Minggu Ke-</label>
                        <select name="minggu_ke" required style="width:100%; padding:8px; border:1px solid #c9a66b; border-radius:6px; background:#f4e9d8;">
                            <option value="">-- Pilih Minggu --</option>
                            <?php for($i=1; $i<=5; $i++): ?>
                                <option value="<?= $i ?>" <?= (isset($edit_data) && $edit_data['minggu_ke'] == $i) ? 'selected' : '' ?>>Minggu <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="display:block; margin-bottom:4px; font-size:13px;">Jumlah Keping</label>
                        <input type="number" name="keping" required placeholder="Contoh: 150" value="<?= isset($edit_data) ? $edit_data['keping'] : '' ?>" style="width:100%; padding:8px; border:1px solid #c9a66b; border-radius:6px; background:#f4e9d8;">
                    </div>

                    <div class="form-group" style="margin-bottom: 8px;">
                        <label style="display:block; margin-bottom:4px; font-size:13px;">Omzet (Rp)</label>
                        <input type="number" name="omzet" id="inputOmzet" required placeholder="Contoh: 300000" value="<?= isset($edit_data) ? $edit_data['omzet'] : '' ?>" style="width:100%; padding:8px; border:1px solid #c9a66b; border-radius:6px; background:#f4e9d8;" oninput="hitungPPN()">
                    </div>

                    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label style="display:block; margin-bottom:4px; font-size:13px;">PPN 20% (Rp)</label>
                            <input type="number" id="inputPPN" readonly value="<?= isset($edit_data) ? $edit_data['ppn'] : '' ?>" style="width:100%; padding:8px; border:1px solid #c9a66b; border-radius:6px; background:#e8d9c0; color:#5a4a3a;">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label style="display:block; margin-bottom:4px; font-size:13px;">Hasil Bersih (Rp)</label>
                            <input type="number" id="inputBersih" readonly value="<?= isset($edit_data) ? $edit_data['hasil_bersih'] : '' ?>" style="width:100%; padding:8px; border:1px solid #c9a66b; border-radius:6px; background:#e8d9c0; color:#5a4a3a;">
                        </div>
                    </div>

                    <button type="submit" class="tombol tombol-coklat" style="width:100%; justify-content:center; padding:10px; font-size:14px;">
                        <i class="fas fa-save"></i> <?= isset($edit_data) ? 'Update Data' : 'Simpan Data' ?>
                    </button>
                    
                    <?php if(isset($edit_data)): ?>
                        <a href="<?= base_url('sewu-data/admin') ?>" style="display:block; text-align:center; margin-top:10px; font-size:12px; color:#991b1b;">Batal Edit</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- TABEL DATA HARI INI -->
            <div class="kotak">
                <h3><i class="fas fa-table"></i> Data Penjualan Hari Ini</h3>
                <div style="max-height: 600px; overflow-y: auto; overflow-x: auto;">
                    <table style="width:100%; font-size:12px; border-collapse: collapse; white-space: nowrap;">
                        <thead style="background:#e8d9c0; position: sticky; top: 0;">
                            <tr>
                                <th style="padding:8px; text-align:left; border-bottom:2px solid #c9a66b;">PEDAGANG</th>
                                <th style="padding:8px; text-align:left; border-bottom:2px solid #c9a66b;">BULAN</th>
                                <th style="padding:8px; text-align:center; border-bottom:2px solid #c9a66b;">MINGGU</th>
                                <th style="padding:8px; text-align:right; border-bottom:2px solid #c9a66b;">KEPING</th>
                                <th style="padding:8px; text-align:right; border-bottom:2px solid #c9a66b;">OMZET</th>
                                <th style="padding:8px; text-align:right; border-bottom:2px solid #c9a66b;">PPN 20%</th>
                                <th style="padding:8px; text-align:right; border-bottom:2px solid #c9a66b;">HASIL BERSIH</th>
                                <th style="padding:8px; text-align:center; border-bottom:2px solid #c9a66b;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($penjualan)): ?>
                                <tr><td colspan="8" style="text-align:center; padding:20px;">Belum ada data yang diinput hari ini.</td></tr>
                            <?php else: ?>
                                <?php foreach ($penjualan as $pj) : ?>
                                <tr style="border-bottom:1px solid #ede6dc;">
                                    <td style="padding:6px 8px;"><?= $pj['nama'] ?></td>
                                    <td style="padding:6px 8px;"><?= $pj['bulan'] ?></td>
                                    <td style="padding:6px 8px; text-align:center;"><strong>Minggu <?= $pj['minggu_ke'] ?></strong></td>
                                    <td style="padding:6px 8px; text-align:right;"><?= number_format($pj['keping'], 0, ',', '.') ?> kb</td>
                                    <td style="padding:6px 8px; text-align:right;">Rp <?= number_format($pj['omzet'], 0, ',', '.') ?></td>
                                    <td style="padding:6px 8px; text-align:right;">Rp <?= number_format($pj['ppn'], 0, ',', '.') ?></td>
                                    <td style="padding:6px 8px; text-align:right; color:#065f46; font-weight:bold;">Rp <?= number_format($pj['hasil_bersih'], 0, ',', '.') ?></td>
                                    <td style="padding:6px 8px; text-align:center; display: flex; gap: 5px; justify-content: center;">
                                        <a href="<?= base_url('sewu-data/admin/edit/' . $pj['id']) ?>" style="background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:4px; text-decoration:none; font-size:11px; font-weight:bold;">
                                           <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?= base_url('sewu-data/admin/hapus/' . $pj['id']) ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" style="background:#fee2e2; color:#991b1b; padding:4px 8px; border-radius:4px; text-decoration:none; font-size:11px; font-weight:bold;">
                                           <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    function hitungPPN() {
        const omzet = parseFloat(document.getElementById('inputOmzet').value) || 0;
        const ppn = omzet * 0.20;
        const bersih = omzet - ppn;
        document.getElementById('inputPPN').value = Math.round(ppn);
        document.getElementById('inputBersih').value = Math.round(bersih);
    }

    document.getElementById('formPenjualan').addEventListener('submit', function(e) {
        const bulan = document.getElementById('inputBulan').value;
        const tahun = document.getElementById('inputTahun').value;
        if (!bulan || !tahun) {
            e.preventDefault();
            alert('Bulan dan Tahun wajib diisi!');
            return;
        }
        document.getElementById('hiddenBulan').value = bulan + ' ' + tahun;
    });
</script>

</body>
</html>