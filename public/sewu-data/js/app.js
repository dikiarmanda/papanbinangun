function rupiah(a) {
    return 'Rp ' + Math.round(a).toLocaleString('id-ID');
}

function rupiahPendek(a) {
    if (a >= 1000000) return 'Rp ' + (a / 1000000).toFixed(1).replace('.', ',') + ' jt';
    if (a >= 1000) return 'Rp ' + Math.round(a / 1000) + ' rb';
    return 'Rp ' + a;
}

function cocok(nama, kata) {
    return nama.toLowerCase().includes(kata.toLowerCase());
}

// ================================================================
// NAVIGASI
// ================================================================
function pindah(el) {
    document.querySelectorAll('.sidebar .menu li').forEach(li => li.classList.remove('aktif'));
    el.classList.add('aktif');

    document.querySelectorAll('.halaman').forEach(h => h.classList.remove('aktif'));
    const tujuan = el.getAttribute('data-halaman');
    document.getElementById('halaman-' + tujuan).classList.add('aktif');

    if (tujuan === 'statistik') isiHalamanStatistik();
    if (tujuan === 'pedagang') isiHalamanPedagang();
    if (tujuan === 'periode') isiHalamanPeriode();
}

// ================================================================
// GRAFIK
// ================================================================
let gBulan = null;
let gOmzet = null;

// ================================================================
// BERANDA
// ================================================================
function saring() {
    const bulan = document.getElementById('filterBulan').value;
    const orang = document.getElementById('filterOrang').value;
    const cari = document.getElementById('cariBeranda').value;

    let hasil = data;
    if (bulan !== 'all') hasil = hasil.filter(d => d.b === bulan);
    if (orang !== 'all') hasil = hasil.filter(d => d.n === orang);
    if (cari) hasil = hasil.filter(d => cocok(d.n, cari));

    tampilStat(hasil);
    tampilTop5(hasil);
    tampilGrafik(hasil);
}

function tampilStat(d) {
    const totalKeping = d.reduce((s, x) => s + x.k, 0);
    const totalOmzet = d.reduce((s, x) => s + x.o, 0);
    const jumlahOrang = new Set(d.map(x => x.n)).size;
    const harian = totalOmzet / 30;

    let maxO = 0, maxONama = '';
    d.forEach(x => {
        if (x.o > maxO) { maxO = x.o; maxONama = x.n; }
    });

    document.getElementById('sKeping').textContent = totalKeping.toLocaleString('id-ID');
    document.getElementById('sOmzet').textContent = rupiah(totalOmzet);
    document.getElementById('sOrang').textContent = jumlahOrang;
    document.getElementById('sHarian').textContent = rupiah(harian);
    document.getElementById('sMaxOmzet').textContent = rupiah(maxO);
    document.getElementById('sMaxOmzetNama').textContent = maxONama || '-';

    const persen = Math.round((jumlahOrang / TOTAL_TERDAFTAR) * 100);
    document.getElementById('persenAngka').textContent = persen + '%';
    document.getElementById('persenBar').style.width = persen + '%';
}

function tampilTop5(d) {
    const rekap = {};
    d.forEach(x => {
        if (!rekap[x.n]) rekap[x.n] = 0;
        rekap[x.n] += x.o;
    });
    const top5 = Object.entries(rekap)
        .map(([nama, o]) => ({ nama, o }))
        .sort((a, b) => b.o - a.o)
        .slice(0, 5);

    let html = '';
    top5.forEach((item, i) => {
        html += '<li>' +
            '<span class="no">' + (i + 1) + '</span>' +
            '<span class="nama">' + item.nama + '</span>' +
            '<span class="nilai">' + rupiahPendek(item.o) + '</span>' +
            '</li>';
    });

    document.getElementById('top5').innerHTML = html ||
        '<li><span class="no">-</span><span class="nama">Tidak ada data</span><span class="nilai">-</span></li>';
}

function tampilGrafik(d) {
    const perBulan = {};
    d.forEach(x => {
        if (!perBulan[x.b]) perBulan[x.b] = 0;
        perBulan[x.b] += x.o;
    });
    const nilaiBulan = urutBulan.map(b => perBulan[b] || 0);

    const ctx1 = document.getElementById('grafikBulan').getContext('2d');
    if (gBulan) gBulan.destroy();
    gBulan = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: urutBulan,
            datasets: [{
                data: nilaiBulan,
                borderColor: '#5a3e2e',
                backgroundColor: 'rgba(90,62,46,0.08)',
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointBackgroundColor: '#5a3e2e',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 10 }, callback: v => rupiahPendek(v) } },
                x: { ticks: { font: { size: 9 } } }
            }
        }
    });

    const rekap = {};
    d.forEach(x => {
        if (!rekap[x.n]) rekap[x.n] = 0;
        rekap[x.n] += x.o;
    });
    const top10 = Object.entries(rekap)
        .map(([nama, o]) => ({ nama, o }))
        .sort((a, b) => b.o - a.o)
        .slice(0, 10);

    const ctx2 = document.getElementById('grafikOmzet').getContext('2d');
    if (gOmzet) gOmzet.destroy();
    gOmzet = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: top10.map(x => x.nama.length > 14 ? x.nama.slice(0, 12) + '…' : x.nama),
            datasets: [{
                data: top10.map(x => x.o),
                backgroundColor: '#7a5a44',
                borderRadius: 3,
                barThickness: 18,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 10 }, callback: v => rupiahPendek(v) } },
                x: { ticks: { font: { size: 9 } } }
            }
        }
    });
}

// ================================================================
// HALAMAN STATISTIK
// ================================================================
function isiHalamanStatistik() {
    const cari = document.getElementById('cariStatistik').value;
    const d = data;

    const totalKeping = d.reduce((s, x) => s + x.k, 0);
    const totalOmzet = d.reduce((s, x) => s + x.o, 0);
    const jumlahOrang = new Set(d.map(x => x.n)).size;
    const harian = totalOmzet / 30;

    let maxK = 0, maxKNama = '';
    let maxO = 0, maxONama = '';
    d.forEach(x => {
        if (x.k > maxK) { maxK = x.k; maxKNama = x.n; }
        if (x.o > maxO) { maxO = x.o; maxONama = x.n; }
    });

    document.getElementById('statKeping').textContent = totalKeping.toLocaleString('id-ID') + ' kb';
    document.getElementById('statOmzet').textContent = rupiah(totalOmzet);
    document.getElementById('statOrang').textContent = jumlahOrang + ' orang';
    document.getElementById('statHarian').textContent = rupiah(harian);
    document.getElementById('statMaxOmzet').textContent = rupiah(maxO) + ' (' + maxONama + ')';
    document.getElementById('statMaxKeping').textContent = maxK.toLocaleString('id-ID') + ' kb (' + maxKNama + ')';

    const rekap = {};
    d.forEach(x => {
        if (!rekap[x.n]) rekap[x.n] = { k: 0, o: 0, minggu: 0 };
        rekap[x.n].k += x.k;
        rekap[x.n].o += x.o;
        rekap[x.n].minggu += 1;
    });

    let urut = Object.entries(rekap)
        .map(([nama, v]) => ({ nama, k: v.k, o: v.o, rata: v.o / v.minggu }))
        .sort((a, b) => b.o - a.o);

    if (cari) urut = urut.filter(x => cocok(x.nama, cari));

    let html = '';
    urut.forEach((item, i) => {
        let kelas = '';
        if (i === 0 && !cari) kelas = 'p1';
        else if (i === 1 && !cari) kelas = 'p2';
        else if (i === 2 && !cari) kelas = 'p3';

        html += '<tr>' +
            '<td>' + (i + 1) + '</td>' +
            '<td><strong>' + item.nama + '</strong></td>' +
            '<td>' + item.k.toLocaleString('id-ID') + ' kb</td>' +
            '<td>' + rupiah(item.o) + '</td>' +
            '<td>' + rupiah(item.rata) + '</td>' +
            '<td><span class="peringkat ' + kelas + '">' + (i + 1) + '</span></td>' +
            '</tr>';
    });

    document.getElementById('isiTabelStatistik').innerHTML = html ||
        '<tr><td colspan="6" style="text-align:center;color:#9a8a78;padding:20px;">Tidak ada data</td></tr>';
}

// ================================================================
// HALAMAN PEDAGANG
// ================================================================
function isiHalamanPedagang() {
    const cari = document.getElementById('cariPedagang').value;
    const rekap = {};
    data.forEach(x => {
        if (!rekap[x.n]) rekap[x.n] = { k: 0, o: 0, entri: 0 };
        rekap[x.n].k += x.k;
        rekap[x.n].o += x.o;
        rekap[x.n].entri += 1;
    });

    let urut = Object.entries(rekap)
        .map(([nama, v]) => ({ nama, k: v.k, o: v.o, entri: v.entri }))
        .sort((a, b) => b.o - a.o);

    if (cari) urut = urut.filter(x => cocok(x.nama, cari));

    let html = '';
    urut.forEach((item, i) => {
        html += '<tr>' +
            '<td>' + (i + 1) + '</td>' +
            '<td><strong>' + item.nama + '</strong></td>' +
            '<td>' + item.entri + '</td>' +
            '<td>' + item.k.toLocaleString('id-ID') + ' kb</td>' +
            '<td>' + rupiah(item.o) + '</td>' +
            '</tr>';
    });

    document.getElementById('isiTabelPedagang').innerHTML = html ||
        '<tr><td colspan="5" style="text-align:center;color:#9a8a78;padding:20px;">Tidak ada data</td></tr>';
}

// ================================================================
// HALAMAN PERIODE
// ================================================================
function isiHalamanPeriode() {
    const cari = document.getElementById('cariPeriode').value;
    let daftar = urutBulan;
    if (cari) daftar = daftar.filter(b => b.toLowerCase().includes(cari.toLowerCase()));

    let html = '';
    daftar.forEach(b => {
        const isi = data.filter(x => x.b === b);
        const keping = isi.reduce((s, x) => s + x.k, 0);
        const omzet = isi.reduce((s, x) => s + x.o, 0);
        const orang = new Set(isi.map(x => x.n)).size;

        html += '<tr>' +
            '<td><strong>' + b + '</strong></td>' +
            '<td>' + keping.toLocaleString('id-ID') + ' kb</td>' +
            '<td>' + rupiah(omzet) + '</td>' +
            '<td>' + orang + ' orang</td>' +
            '</tr>';
    });

    document.getElementById('isiTabelPeriode').innerHTML = html ||
        '<tr><td colspan="4" style="text-align:center;color:#9a8a78;padding:20px;">Tidak ada data</td></tr>';
}

// ================================================================
// UNDUH CSV
// ================================================================
function unduhCSV(jenis) {
    let judul = [];
    let isi = [];
    let namaFile = 'sewu-data.csv';

    if (jenis === 'beranda') {
        const bulan = document.getElementById('filterBulan').value;
        const orang = document.getElementById('filterOrang').value;
        const cari = document.getElementById('cariBeranda').value;

        let hasil = data;
        if (bulan !== 'all') hasil = hasil.filter(d => d.b === bulan);
        if (orang !== 'all') hasil = hasil.filter(d => d.n === orang);
        if (cari) hasil = hasil.filter(d => cocok(d.n, cari));

        judul = ['Bulan', 'Nama Pedagang', 'Keping', 'Omzet'];
        isi = hasil.map(d => [d.b, d.n, d.k, d.o]);
        namaFile = 'sewu-data-beranda.csv';
    } else if (jenis === 'statistik') {
        const rekap = {};
        data.forEach(x => {
            if (!rekap[x.n]) rekap[x.n] = { k: 0, o: 0, minggu: 0 };
            rekap[x.n].k += x.k;
            rekap[x.n].o += x.o;
            rekap[x.n].minggu += 1;
        });
        const urut = Object.entries(rekap)
            .map(([nama, v]) => ({ nama, k: v.k, o: v.o, rata: v.o / v.minggu }))
            .sort((a, b) => b.o - a.o);

        judul = ['Peringkat', 'Nama Pedagang', 'Total Keping', 'Total Omzet', 'Rata-rata per Minggu'];
        isi = urut.map((x, i) => [i + 1, x.nama, x.k, x.o, Math.round(x.rata)]);
        namaFile = 'sewu-data-statistik.csv';
    } else if (jenis === 'pedagang') {
        const rekap = {};
        data.forEach(x => {
            if (!rekap[x.n]) rekap[x.n] = { k: 0, o: 0, entri: 0 };
            rekap[x.n].k += x.k;
            rekap[x.n].o += x.o;
            rekap[x.n].entri += 1;
        });
        const urut = Object.entries(rekap)
            .map(([nama, v]) => ({ nama, k: v.k, o: v.o, entri: v.entri }))
            .sort((a, b) => b.o - a.o);

        judul = ['No', 'Nama Pedagang', 'Jumlah Entri', 'Total Keping', 'Total Omzet'];
        isi = urut.map((x, i) => [i + 1, x.nama, x.entri, x.k, x.o]);
        namaFile = 'sewu-data-pedagang.csv';
    } else if (jenis === 'periode' || jenis === 'laporan') {
        judul = ['Bulan', 'Total Keping', 'Total Omzet', 'Jumlah Pedagang'];
        isi = urutBulan.map(b => {
            const d = data.filter(x => x.b === b);
            const keping = d.reduce((s, x) => s + x.k, 0);
            const omzet = d.reduce((s, x) => s + x.o, 0);
            const orang = new Set(d.map(x => x.n)).size;
            return [b, keping, omzet, orang];
        });
        namaFile = 'sewu-data-periode.csv';
    }

    const baris = [judul, ...isi];
    const csv = baris.map(r => r.map(cell => {
        const s = String(cell);
        if (s.includes(',') || s.includes('"') || s.includes('\n')) {
            return '"' + s.replace(/"/g, '""') + '"';
        }
        return s;
    }).join(',')).join('\n');

    const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = namaFile;
    link.click();
    URL.revokeObjectURL(link.href);
}

// ================================================================
// UNDUH EXCEL (.xls)
// ================================================================
function unduhExcel(jenis) {
    let judul = [];
    let isi = [];
    let namaFile = 'sewu-data.xls';

    if (jenis === 'beranda') {
        const bulan = document.getElementById('filterBulan').value;
        const orang = document.getElementById('filterOrang').value;
        const cari = document.getElementById('cariBeranda').value;

        let hasil = data;
        if (bulan !== 'all') hasil = hasil.filter(d => d.b === bulan);
        if (orang !== 'all') hasil = hasil.filter(d => d.n === orang);
        if (cari) hasil = hasil.filter(d => cocok(d.n, cari));

        judul = ['Bulan', 'Nama Pedagang', 'Keping', 'Omzet'];
        isi = hasil.map(d => [d.b, d.n, d.k, d.o]);
        namaFile = 'sewu-data-beranda.xls';
    } else if (jenis === 'statistik') {
        const rekap = {};
        data.forEach(x => {
            if (!rekap[x.n]) rekap[x.n] = { k: 0, o: 0, minggu: 0 };
            rekap[x.n].k += x.k;
            rekap[x.n].o += x.o;
            rekap[x.n].minggu += 1;
        });
        const urut = Object.entries(rekap)
            .map(([nama, v]) => ({ nama, k: v.k, o: v.o, rata: v.o / v.minggu }))
            .sort((a, b) => b.o - a.o);

        judul = ['Peringkat', 'Nama Pedagang', 'Total Keping', 'Total Omzet', 'Rata-rata per Minggu'];
        isi = urut.map((x, i) => [i + 1, x.nama, x.k, x.o, Math.round(x.rata)]);
        namaFile = 'sewu-data-statistik.xls';
    } else if (jenis === 'pedagang') {
        const rekap = {};
        data.forEach(x => {
            if (!rekap[x.n]) rekap[x.n] = { k: 0, o: 0, entri: 0 };
            rekap[x.n].k += x.k;
            rekap[x.n].o += x.o;
            rekap[x.n].entri += 1;
        });
        const urut = Object.entries(rekap)
            .map(([nama, v]) => ({ nama, k: v.k, o: v.o, entri: v.entri }))
            .sort((a, b) => b.o - a.o);

        judul = ['No', 'Nama Pedagang', 'Jumlah Entri', 'Total Keping', 'Total Omzet'];
        isi = urut.map((x, i) => [i + 1, x.nama, x.entri, x.k, x.o]);
        namaFile = 'sewu-data-pedagang.xls';
    } else if (jenis === 'periode' || jenis === 'laporan') {
        judul = ['Bulan', 'Total Keping', 'Total Omzet', 'Jumlah Pedagang'];
        isi = urutBulan.map(b => {
            const d = data.filter(x => x.b === b);
            const keping = d.reduce((s, x) => s + x.k, 0);
            const omzet = d.reduce((s, x) => s + x.o, 0);
            const orang = new Set(d.map(x => x.n)).size;
            return [b, keping, omzet, orang];
        });
        namaFile = 'sewu-data-periode.xls';
    }

    let html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    html += '<head><meta charset="UTF-8">';
    html += '<style>table{border-collapse:collapse}th{background:#7a5a44;color:#fff;padding:6px 10px;border:1px solid #5a3e2e;font-size:11pt}td{padding:5px 10px;border:1px solid #d8ccbc;font-size:11pt}</style>';
    html += '</head><body>';
    html += '<h2>SEWU DATA · Pasar Pring Sewu</h2>';
    html += '<p>Dusun Binangun, Desa Plintahan, Pandaan</p>';
    html += '<p>Periode: November 2025 – Agustus 2026</p><br>';
    html += '<table><tr>';
    judul.forEach(j => html += '<th>' + j + '</th>');
    html += '</tr>';
    isi.forEach(r => {
        html += '<tr>';
        r.forEach(c => html += '<td>' + c + '</td>');
        html += '</tr>';
    });
    html += '</table></body></html>';

    const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = namaFile;
    link.click();
    URL.revokeObjectURL(link.href);
}

// ================================================================
// INISIALISASI SETELAH DATA SIAP
// ================================================================
function siapkanDashboard() {
    // === Isi dropdown BULAN otomatis ===
    const daftarBulan = [...new Set(data.map(d => d.b))];

    // Urutan bulan yang kita mau (biar rapi)
    const urutanBulan = ['NOV 2025', 'DES 2025', 'JAN 2026', 'FEB 2026', 'MAR 2026',
                         'APRIL 2026', 'MEI 2026', 'JUNI 2026', 'JULI 2026',
                         'AGUSTUS 2026', 'SEPTEMBER 2026'];

    // Gabungkan: pakai urutan manual dulu, lalu tambah bulan lain yang belum terdaftar
    const bulanFinal = [
        ...urutanBulan.filter(b => daftarBulan.includes(b)),
        ...daftarBulan.filter(b => !urutanBulan.includes(b))
    ];

    const selBulan = document.getElementById('filterBulan');
    selBulan.innerHTML = '<option value="all">Semua Bulan</option>';
    bulanFinal.forEach(b => {
        const opt = document.createElement('option');
        opt.value = b;
        opt.textContent = b;
        selBulan.appendChild(opt);
    });

    // === Isi dropdown PEDAGANG otomatis ===
    const daftarOrang = [...new Set(data.map(d => d.n))].sort();
    const selOrang = document.getElementById('filterOrang');
    selOrang.innerHTML = '<option value="all">Semua Pedagang</option>';
    daftarOrang.forEach(n => {
        const opt = document.createElement('option');
        opt.value = n;
        opt.textContent = n;
        selOrang.appendChild(opt);
    });

    // === Jalankan semua tampilan ===
    saring();
    isiHalamanStatistik();
    isiHalamanPedagang();
    isiHalamanPeriode();
}