# Website Profil Wisata Desa (Tema Vintage)

Website profil destinasi wisata desa berbasis **CodeIgniter 4 + MySQL**, dengan panel admin (superadmin & admin) dan tampilan publik bergaya vintage.

## Fitur

- Halaman publik: beranda, tentang, wisata, artikel, galeri, kontak
- Panel admin: CRUD artikel, kategori, destinasi wisata, galeri
- Role superadmin: kelola akun admin + pengaturan situs
- Activity log di dashboard (superadmin)
- SEO dasar: meta/OG, `sitemap.xml`, `robots.txt`
- Upload gambar dengan resize otomatis

## Setup lokal (XAMPP)

1. Pastikan Apache & MySQL aktif
2. Import `database.sql` ke MySQL
3. Salin konfigurasi di `.env` (sudah disiapkan untuk XAMPP)
4. Buka: [http://localhost/papanbinangun/public/](http://localhost/papanbinangun/public/)
5. Admin: [http://localhost/papanbinangun/public/admin/login](http://localhost/papanbinangun/public/admin/login)

### Akun default

| Role       | Email                      | Password       |
| ---------- | -------------------------- | -------------- |
| Superadmin | `superadmin@desawisata.id` | `ChangeMe123!` |
| Admin      | `admin@desawisata.id`      | `ChangeMe123!` |

**Ganti password segera setelah instalasi.**

## Dokumentasi

- [DOCS_OPERATOR.md](DOCS_OPERATOR.md) — panduan operator konten
- [DEPLOY.md](DEPLOY.md) — deploy ke Hostinger
- [plan.md](plan.md) — rencana proyek asli

## Stack

- CodeIgniter 4.7
- MySQL / MariaDB
- CSS tema vintage (`public/assets/css/app.css`)
- Lexical (editor konten admin, bundle lokal)
- Tailwind CLI opsional (`npm run css:build`)
