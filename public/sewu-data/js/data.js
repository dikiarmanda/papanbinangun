const API_URL = 'https://script.google.com/macros/s/AKfycbyQmdTSRjkMIlpiJIuZLetxF9-FYmnSfWMm5c12wJrSpSfFZBU57aiSSUtV1eHelAp9/exec'
let data = [];
const TOTAL_TERDAFTAR = 46;

const urutBulan = ['NOV 2025', 'DES 2025', 'JAN 2026', 'FEB 2026', 'MAR 2026',
                   'APRIL 2026', 'MEI 2026', 'JUNI 2026', 'JULI 2026',
                   'AGUSTUS 2026', 'SEPTEMBER 2026'];
                   

async function ambilData() {
    console.log(' Mulai ambil data dari:', API_URL);
    try {
        const res = await fetch(API_URL, {
            method: 'GET',
            redirect: 'follow'
        });
        console.log(' Status:', res.status);
        const teks = await res.text();
        console.log(' Panjang respons:', teks.length);
        data = JSON.parse(teks);
        console.log(' Data berhasil diambil:', data.length, 'baris');

        if (typeof siapkanDashboard === 'function') {
            siapkanDashboard();
        } else {
            console.error(' Fungsi siapkanDashboard belum ada di app.js');
        }
    } catch (err) {
        console.error(' Gagal ambil data:', err);
        alert('Gagal memuat data: ' + err.message);
    }
}

window.addEventListener('load', function() {
    console.log(' Halaman siap, mulai ambil data dari Sheets');
    ambilData();
});