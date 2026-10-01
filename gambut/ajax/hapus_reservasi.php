<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');

if (isset($_POST['id_reservasi'])) {
    $id_reservasi = mysqli_real_escape_string($koneksi, $_POST['id_reservasi']);

    // Cek dan hapus file DP jika ada
    $query = mysqli_query($koneksi, "SELECT dp FROM reservasi WHERE id_reservasi = '$id_reservasi'");
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        if (!empty($data['dp'])) {
            $path_gambar = "../../img/" . $data['dp'];
            if (file_exists($path_gambar)) {
                unlink($path_gambar); // Menghapus file gambar
            }
        }
    }

    // Hapus data dari database
    $delete = mysqli_query($koneksi, "DELETE FROM reservasi WHERE id_reservasi = '$id_reservasi'");
    
    if ($delete) {
        echo json_encode(['status' => true, 'message' => 'Data berhasil dihapus']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Gagal menghapus data dari database']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'ID Reservasi tidak ditemukan']);
}
exit();
?>