<?php
// Tugas 11: penghubung PHP -> Supabase (REST/RPC lewat cURL).
// Konfigurasi dibaca dari config.php atau variabel lingkungan SUPABASE_URL / SUPABASE_KEY.
function supabase_config(): ?array {
    $cfg = is_file(__DIR__ . '/config.php') ? require __DIR__ . '/config.php' : [];
    $url = $cfg['supabase_url'] ?? getenv('SUPABASE_URL');
    $key = $cfg['supabase_key'] ?? getenv('SUPABASE_KEY');
    if (!$url || !$key || str_contains($url, 'XXXX')) return null;
    return ['url' => rtrim($url, '/'), 'key' => $key];
}

// Panggil fungsi Postgres public.daftar_anggota(). Mengembalikan [status, pesan, id].
// status: 'ok' | 'duplikat' | 'nonaktif' | 'gagal'
function simpan_anggota(array $d): array {
    $cfg = supabase_config();
    if (!$cfg) return ['nonaktif', 'Database Supabase belum dikonfigurasi (config.php belum dibuat).', null];
    if (!function_exists('curl_init')) return ['gagal', 'Ekstensi cURL PHP belum aktif.', null];

    $ch = curl_init($cfg['url'] . '/rest/v1/rpc/daftar_anggota');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $cfg['key'],
            'Authorization: Bearer ' . $cfg['key'],
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'p_nama' => $d['nama'], 'p_email' => $d['email'], 'p_telepon' => $d['telepon'],
            'p_tanggal_lahir' => $d['tanggal_lahir'], 'p_kategori_anggota' => $d['kategori_anggota'],
            'p_tujuan' => $d['tujuan'], 'p_minat' => array_values($d['minat']),
        ]),
    ]);
    $body = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($body === false) return ['gagal', 'Tidak dapat terhubung ke Supabase: ' . $err, null];
    if ($http >= 200 && $http < 300) return ['ok', 'Data tersimpan di Supabase.', json_decode($body, true)];
    $j = json_decode($body, true);
    if ($http === 409 || ($j['code'] ?? '') === '23505') return ['duplikat', 'Email ini sudah terdaftar.', null];
    return ['gagal', 'Supabase menolak permintaan (HTTP ' . $http . ').', null];
}
