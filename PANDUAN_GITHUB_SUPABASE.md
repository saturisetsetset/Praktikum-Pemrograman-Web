# Panduan: Zip -> GitHub -> Supabase

## A. Upload ke GitHub
1. Buat akun/login di github.com -> tombol **+** -> **New repository**. Nama: `naa-archivevault`, pilih Public/Private, JANGAN centang README -> **Create repository**.
2. Ekstrak zip. Pastikan `config.php` (berisi key) tidak ada / tertulis di `.gitignore` (sudah disiapkan).
3. **Cara tanpa terminal:** di halaman repo klik **uploading an existing file**, seret SEMUA isi folder proyek (bukan zip-nya; GitHub tidak mengekstrak zip), tunggu selesai, isi pesan commit, klik **Commit changes**. (Batas 100 file per upload web.)
4. **Cara terminal (Git):**
```
cd 250911721355_Sarifa_Nurfadila_Tugas11
git init
git add .
git commit -m "Tugas 11 ArchiveVault"
git branch -M main
git remote add origin https://github.com/USERNAME/naa-archivevault.git
git push -u origin main
```
Saat diminta password, gunakan Personal Access Token (Settings > Developer settings > Tokens).

## B. Kirim database ke Supabase
1. supabase.com -> **New project** (catat password database, pilih region terdekat, misal Singapore).
2. Menu **SQL Editor** -> **New query** -> tempel seluruh isi `database/supabase_schema.sql` -> **Run**. Cek di **Table Editor**: ada 6 tabel dan data contoh.
3. **Project Settings > API**: salin **Project URL** dan **anon public key**.
4. Salin `config.example.php` -> `config.php`, isi dua nilai tadi. Jangan pernah memakai `service_role` key dan jangan commit `config.php`.
5. Jalankan web lewat XAMPP, isi formulir Daftar Anggota -> cek tabel `anggota` di Table Editor.

## C. Catatan penting
- GitHub (termasuk GitHub Pages) dan Supabase TIDAK menjalankan PHP. GitHub = penyimpanan kode; Supabase = database. Agar web PHP online, host di layanan PHP (mis. InfinityFree, Hostinger, Railway) yang mengambil kode dari GitHub; atau tetap demo lewat XAMPP.
- Di hosting, isi kredensial lewat variabel lingkungan `SUPABASE_URL` dan `SUPABASE_KEY` atau upload `config.php` secara manual.
