<?php
$judul_halaman = 'Beranda — NAA ArchiveVault';
$halaman_aktif = 'beranda';
include "layout/header.php";
?>
<main>
<section class="hero container">
  <p class="eyebrow">Prototipe mahasiswa · Studi kasus NAA</p>
  <h1>Jelajahi sejarah<br>melalui arsip.</h1>
  <p>NAA ArchiveVault adalah portal penjelajahan arsip digital: dokumen, foto, poster, dan rekaman audiovisual dalam satu tempat yang mudah diakses.</p>
</section>
<section class="container">
  <div class="banner">
    <div>
      <p class="eyebrow">Pameran unggulan</p>
      <h2>Suara Para Pendatang: Arsip Imigrasi 1940–1960-an</h2>
      <p>Poster, wawancara, dan dokumen yang merekam perjalanan keluarga migran pascaperang menuju Australia.</p>
      <a class="btn" href="katalog.html">Lihat Katalog Koleksi →</a>
    </div>
    <img src="images/av-001.svg" alt="Poster Imigrasi Australia">
  </div>
</section>
<section class="container" style="padding-bottom:56px">
  <h2>Mulai menjelajah</h2>
  <p class="muted">Telusuri seluruh koleksi dan buka metadata lengkap tiap arsip hanya dengan dua klik dari beranda.</p>
  <a class="btn btn-outline" href="katalog.html">Buka Katalog Koleksi</a>
  <a class="btn btn-outline" href="erd.html" style="margin-left:8px">Model Data (ERD)</a>
  <a class="btn btn-outline" href="#atas" style="margin-left:8px">↑ Kembali ke atas</a>
</section>
</main>
<?php include "layout/footer.php"; ?>
