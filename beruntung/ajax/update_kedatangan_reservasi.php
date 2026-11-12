<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');
date_default_timezone_set('Asia/Makassar');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reservasi   = isset($_POST['id_reservasi']) ? mysqli_real_escape_string($koneksi, $_POST['id_reservasi']) : '';
    $nama_reservasi = isset($_POST['nama_reservasi']) ? mysqli_real_escape_string($koneksi, $_POST['nama_reservasi']) : '';
    $tgl_bermain    = isset($_POST['tgl_bermain']) ? mysqli_real_escape_string($koneksi, $_POST['tgl_bermain']) : '';
    $ruang          = isset($_POST['ruang']) ? mysqli_real_escape_string($koneksi, $_POST['ruang']) : '';
    $w_kedatangan   = isset($_POST['w_kedatangan']) ? mysqli_real_escape_string($koneksi, $_POST['w_kedatangan']) : '';
    $durasi         = isset($_POST['durasi']) ? mysqli_real_escape_string($koneksi, $_POST['durasi']) : '';
    $catatan        = isset($_POST['catatan']) ? mysqli_real_escape_string($koneksi, $_POST['catatan']) : '-';

    if (!empty($id_reservasi) && !empty($nama_reservasi) && !empty($tgl_bermain) && !empty($ruang) && !empty($w_kedatangan) && !empty($durasi)) {
        
        $waktu_bersih = str_replace(' WITA', '', $w_kedatangan);
        $angka_durasi = intval($durasi);
        $timestamp_kedatangan = strtotime($waktu_bersih);
        $timestamp_berakhir = $timestamp_kedatangan + ($angka_durasi * 3600);
        $jam_berakhir = date('H:i', $timestamp_berakhir) . " WITA";

        $query = "UPDATE reservasi SET 
                    nama_reservasi = '$nama_reservasi', 
                    tgl_bermain = '$tgl_bermain', 
                    ruang = '$ruang', 
                    w_kedatangan = '$w_kedatangan', 
                    durasi = '$durasi',
                    jam_berakhir = '$jam_berakhir',
                    catatan = '$catatan' 
                  WHERE id_reservasi = '$id_reservasi'";
                  
        $update = mysqli_query($koneksi, $query);

        if ($update) {
            echo json_encode(['status' => true, 'message' => 'Data reservasi berhasil diperbarui sepenuhnya']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Gagal memperbarui database: ' . mysqli_error($koneksi)]);
        }
    } else {
        echo json_encode(['status' => false, 'message' => 'Data tidak lengkap. Pastikan field utama terisi']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Metode request tidak valid']);
}

exit;
?>