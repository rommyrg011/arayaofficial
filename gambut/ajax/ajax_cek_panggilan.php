<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');

date_default_timezone_set('Asia/Makassar'); 

$tgl_hari_ini = date('Y-m-d'); 
$jam_menit_sekarang = date('H.i'); 

$sql = "SELECT id_reservasi, nama_reservasi, ruang, w_kedatangan FROM reservasi 
        WHERE tgl_bermain = '$tgl_hari_ini' 
        AND cabang = 'Gambut'
        AND (status != 'Masuk Ruangan' AND status != 'Dipanggil' OR status = '' OR status IS NULL OR status = 'Pending')";

$result = mysqli_query($koneksi, $sql);
$response = [
    'ada_panggilan' => false,
    'debug_jam_server' => $jam_menit_sekarang,
    'debug_tgl_server' => $tgl_hari_ini
];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $clean_waktu = str_replace(['WITA', 'wita', 'WIB', 'wib', 'WIT', 'wit', ' '], '', $row['w_kedatangan']);
        $clean_waktu = trim($clean_waktu);
        
        $clean_waktu = str_replace(':', '.', $clean_waktu);

        if ($clean_waktu === $jam_menit_sekarang) {
            $response['ada_panggilan'] = true;
            $response['id_reservasi']  = $row['id_reservasi'];
            $response['nama']          = $row['nama_reservasi'];
            $response['ruang']         = $row['ruang'];
            
            $id_res = $row['id_reservasi'];
            mysqli_query($koneksi, "UPDATE reservasi SET status = 'Dipanggil' WHERE id_reservasi = $id_res");
            
            break; 
        }
    }
}

echo json_encode($response);
exit();
?>