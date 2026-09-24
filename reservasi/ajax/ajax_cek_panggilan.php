<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');

// Set timezone ke WITA agar sinkron penuh dengan sistem komputer lobi/kasir Anda
date_default_timezone_set('Asia/Makassar'); 

// Dapatkan tanggal hari ini dalam format database SQL standar (YYYY-MM-DD)
$tgl_hari_ini = date('Y-m-d'); 

// Dapatkan jam dan menit waktu lokal sekarang (Format: 14.50)
$jam_menit_sekarang = date('H.i'); 

// Ambil data reservasi hari ini yang belum masuk ruangan/belum dipanggil
$sql = "SELECT id_reservasi, nama_reservasi, ruang, w_kedatangan FROM reservasi 
        WHERE tgl_bermain = '$tgl_hari_ini' 
        AND (status != 'Masuk Ruangan' AND status != 'Dipanggil' OR status = '' OR status IS NULL OR status = 'Pending')";

$result = mysqli_query($koneksi, $sql);
$response = [
    'ada_panggilan' => false,
    'debug_jam_server' => $jam_menit_sekarang,
    'debug_tgl_server' => $tgl_hari_ini
];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Bersihkan data jam dari database (Menghapus tulisan WITA, WIB, spasi, dsb)
        $clean_waktu = str_replace(['WITA', 'wita', 'WIB', 'wib', 'WIT', 'wit', ' '], '', $row['w_kedatangan']);
        $clean_waktu = trim($clean_waktu);
        
        // Seragamkan tanda pemisah titik dua (:) menjadi titik (.) agar bisa dibandingkan secara string
        $clean_waktu = str_replace(':', '.', $clean_waktu);

        // Jika jam komputer/server SEKARANG sama dengan jam kedatangan di data booking
        if ($clean_waktu === $jam_menit_sekarang) {
            $response['ada_panggilan'] = true;
            $response['id_reservasi']  = $row['id_reservasi'];
            $response['nama']          = $row['nama_reservasi'];
            $response['ruang']         = $row['ruang'];
            
            // Kunci baris data ini dengan status 'Dipanggil' agar tidak meloop berulang kali pada menit yang sama
            $id_res = $row['id_reservasi'];
            mysqli_query($koneksi, "UPDATE reservasi SET status = 'Dipanggil' WHERE id_reservasi = $id_res");
            
            break; 
        }
    }
}

echo json_encode($response);
exit();