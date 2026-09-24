<?php
require_once __DIR__ . '/../function.php'; 

$id_user_login = intval($_SESSION['id_user'] ?? $_SESSION['id_kurir'] ?? $_SESSION['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];
    
    if ($aksi == 'hapus') {
        $id = intval($_POST['id_karyawan']);
        mysqli_query($koneksi, "DELETE FROM karyawan WHERE id_karyawan=$id AND id_user=$id_user_login");
    } else {
        $cabang            = mysqli_real_escape_string($koneksi, $_POST['cabang'] ?? '');
        $tanggal           = mysqli_real_escape_string($koneksi, $_POST['tanggal'] ?? '');
        $shift             = intval($_POST['shift'] ?? 0);
        $tipe_shift        = mysqli_real_escape_string($koneksi, $_POST['tipe_shift'] ?? '');
        $omset             = intval($_POST['omset'] ?? 0);
        $operasional       = mysqli_real_escape_string($koneksi, $_POST['operasional'] ?? '');
        $total_pengeluaran = intval($_POST['total_pengeluaran'] ?? 0);

        if ($aksi == 'tambah') {
            $query = "INSERT INTO karyawan (id_user, cabang, tanggal, shift, tipe_shift, omset, operasional, total_pengeluaran) VALUES ('$id_user_login', '$cabang', '$tanggal', '$shift', '$tipe_shift', '$omset', '$operasional', '$total_pengeluaran')";
            mysqli_query($koneksi, $query);
        } elseif ($aksi == 'edit') {
            $id = intval($_POST['id_karyawan']);
            $query = "UPDATE karyawan SET cabang='$cabang', tanggal='$tanggal', shift='$shift', tipe_shift='$tipe_shift', omset='$omset', operasional='$operasional', total_pengeluaran='$total_pengeluaran' WHERE id_karyawan=$id AND id_user=$id_user_login";
            mysqli_query($koneksi, $query);
        }
    }
    
    header("Location: ./");
    exit();
}
?>