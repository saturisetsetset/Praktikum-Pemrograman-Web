<?php
// Tugas 9: validasi pendaftaran anggota dengan PHP Native.
// Tugas 11: data yang lolos validasi disimpan ke Supabase (tabel anggota + minat_anggota) lewat koneksi.php.
// Skenario: data lengkap/sesuai format -> lolos; selain itu -> perlu diperbaiki.
date_default_timezone_set('Asia/Jakarta');
require_once __DIR__ . '/koneksi.php';
$judul_halaman = 'Hasil Validasi Pendaftaran — NAA ArchiveVault';
$halaman_aktif = 'daftar';

// Sequence: ambil input POST dan tetapkan aturan yang diizinkan.
$metode_post = $_SERVER['REQUEST_METHOD'] === 'POST';
$nama_pengunjung = trim((string)($_POST['nama'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$telepon = trim((string)($_POST['telepon'] ?? ''));
$tanggal_lahir = trim((string)($_POST['tanggal_lahir'] ?? ''));
$kategori_anggota = trim((string)($_POST['kategori_anggota'] ?? ''));
$tujuan = trim((string)($_POST['tujuan'] ?? ''));
$minat_koleksi = $_POST['minat_koleksi'] ?? [];
$kategori_diizinkan = ['Pelajar', 'Mahasiswa', 'Peneliti', 'Umum'];
$tujuan_diizinkan = ['Penelitian', 'Tugas sekolah atau kuliah', 'Penelusuran sejarah keluarga', 'Pembelajaran umum', 'Kebutuhan lain'];
$minat_diizinkan = ['Poster', 'Dokumen', 'Foto', 'Audiovisual'];
$kesalahan = [];

function aman($nilai) {
    return htmlspecialchars((string)$nilai, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if ($metode_post) {
    // Selection: jalankan pemeriksaan ulang di sisi server.
    if (!preg_match('/^.{3,80}$/us', $nama_pengunjung)) {
        $kesalahan[] = 'Nama lengkap wajib terdiri dari 3–80 karakter.';
    }
    if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $kesalahan[] = 'Alamat email harus menggunakan format yang benar.';
    }
    if (!preg_match('/^\+?[0-9]{10,15}$/D', $telepon)) {
        $kesalahan[] = 'Nomor telepon harus berisi 10–15 digit, boleh diawali +.';
    }
    $tanggal = DateTimeImmutable::createFromFormat('!Y-m-d', $tanggal_lahir);
    if (!$tanggal || $tanggal->format('Y-m-d') !== $tanggal_lahir ||
        $tanggal_lahir < '1900-01-01' || $tanggal_lahir > date('Y-m-d')) {
        $kesalahan[] = 'Tanggal lahir harus valid dan tidak boleh melebihi hari ini.';
    }
    if (!in_array($kategori_anggota, $kategori_diizinkan, true)) {
        $kesalahan[] = 'Pilih kategori anggota yang tersedia.';
    }
    if (!in_array($tujuan, $tujuan_diizinkan, true)) {
        $kesalahan[] = 'Pilih tujuan penggunaan arsip yang tersedia.';
    }
    if (!is_array($minat_koleksi)) {
        $kesalahan[] = 'Pilihan minat koleksi tidak sesuai.';
        $minat_koleksi = [];
    } else {
        // Iteration: periksa setiap pilihan minat (boleh kosong).
        foreach ($minat_koleksi as $minat) {
            if (!is_string($minat) || !in_array($minat, $minat_diizinkan, true)) {
                $kesalahan[] = 'Ada minat koleksi yang tidak valid.';
                break;
            }
        }
    }
}
// Selection utama: lolos hanya jika POST dan tidak ada kesalahan.
$status_valid = $metode_post && count($kesalahan) === 0;

// Tugas 11: simpan ke database hanya jika validasi lolos.
$status_db = null; $pesan_db = '';
if ($status_valid) {
    [$status_db, $pesan_db] = simpan_anggota([
        'nama' => $nama_pengunjung, 'email' => $email, 'telepon' => $telepon,
        'tanggal_lahir' => $tanggal_lahir, 'kategori_anggota' => $kategori_anggota,
        'tujuan' => $tujuan, 'minat' => $minat_koleksi,
    ]);
    if ($status_db === 'duplikat' || $status_db === 'gagal') {
        $kesalahan[] = $pesan_db;
        $status_valid = false;
    }
}
include "layout/header.php";
?>
<main class="container">
  <a class="back" href="formulir.html">← Kembali ke Formulir</a>
  <div class="form-head">
    <p class="eyebrow">Tugas 09 &amp; 11 / PHP Native + Supabase</p>
    <h1>Hasil pemeriksaan pendaftaran</h1>
    <p class="muted">Sistem memeriksa data yang dikirim oleh formulir dengan aturan validasi PHP pada server lokal.</p>
  </div>
  <section class="result-card" aria-live="polite">
    <?php if (!$metode_post): ?>
      <span class="result-label">Belum ada data</span>
      <h2>Silakan lengkapi formulir terlebih dahulu.</h2>
      <p>Data pendaftaran belum dikirim melalui metode POST.</p>
      <a href="formulir.html" class="btn">Isi Formulir →</a>
    <?php elseif ($status_valid): ?>
      <span class="result-label result-label--success">IF / kondisi benar</span>
      <h2 class="result-success">Data pendaftaran lolos validasi server.</h2>
      <p><strong><?= aman($nama_pengunjung) ?></strong>, isian Anda memenuhi aturan format yang ditetapkan pada prototipe ini.</p>
      <div class="result-grid">
        <div><span>Email</span><strong><?= aman($email) ?></strong></div>
        <div><span>Kategori anggota</span><strong><?= aman($kategori_anggota) ?></strong></div>
        <div><span>Tujuan penggunaan</span><strong><?= aman($tujuan) ?></strong></div>
        <div><span>Minat koleksi</span><strong><?= aman(count($minat_koleksi) ? implode(', ', $minat_koleksi) : 'Tidak dipilih') ?></strong></div>
      </div>
      <p class="result-note"><?= $status_db === 'ok'
        ? 'Data pendaftaran berhasil disimpan ke database Supabase (tabel anggota dan minat_anggota). Prototipe ini tidak mengaktifkan keanggotaan resmi.'
        : 'Data divalidasi tetapi belum disimpan: ' . aman($pesan_db) ?></p>
    <?php else: ?>
      <span class="result-label result-label--error">ELSE / kondisi salah</span>
      <h2 class="result-error">Data pendaftaran perlu diperbaiki.</h2>
      <p>Pemeriksaan server menemukan kesalahan berikut:</p>
      <ul class="result-errors">
        <?php foreach ($kesalahan as $pesan): ?>
          <li><?= aman($pesan) ?></li>
        <?php endforeach; ?>
      </ul>
      <a href="formulir.html" class="btn">Perbaiki Formulir →</a>
    <?php endif; ?>
  </section>
  <p class="muted result-after">Halaman ini menggunakan <code>include</code> untuk memuat <code>layout/header.php</code> dan <code>layout/footer.php</code>.</p>
</main>
<?php include "layout/footer.php"; ?>
