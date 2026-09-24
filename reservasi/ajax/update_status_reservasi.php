<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reservasi = isset($_POST['id_reservasi']) ? trim($_POST['id_reservasi']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : 'Masuk Ruangan';

    if (empty($id_reservasi)) {
        echo json_encode(['status' => false, 'message' => 'ID Reservasi tidak ditemukan.']);
        exit;
    }

    $id_reservasi = mysqli_real_escape_string($koneksi, $id_reservasi);
    $status = mysqli_real_escape_string($koneksi, $status);

    $query = "UPDATE reservasi SET status = '$status' WHERE id_reservasi = '$id_reservasi'";
    $exec = mysqli_query($koneksi, $query);

    if ($exec) {
        echo json_encode(['status' => true, 'message' => 'Status berhasil diperbarui.']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Gagal memperbarui database: ' . mysqli_error($koneksi)]);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Metode akses tidak sah.']);
}