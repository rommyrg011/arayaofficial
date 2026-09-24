<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json'); 

$id_user_login = intval($_SESSION['id_user'] ?? $_SESSION['id_kurir'] ?? $_SESSION['id'] ?? 0);

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$filter_bulan = mysqli_real_escape_string($koneksi, $_POST['bulan'] ?? '');
$filter_tahun = mysqli_real_escape_string($koneksi, $_POST['tahun'] ?? '');

$whereClause = "id_user = $id_user_login";
if (!empty($filter_bulan)) $whereClause .= " AND MONTH(tanggal) = '$filter_bulan'";
if (!empty($filter_tahun)) $whereClause .= " AND YEAR(tanggal) = '$filter_tahun'";

$totalData = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM karyawan WHERE id_user = $id_user_login"))['total'];
$totalFiltered = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM karyawan WHERE $whereClause"))['total'];
$total_omset_bulan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(omset) as total FROM karyawan WHERE $whereClause"))['total'] ?? 0;

$sqlData = "SELECT * FROM karyawan WHERE $whereClause ORDER BY id_karyawan DESC LIMIT $start, $length";
$queryData = mysqli_query($koneksi, $sqlData);

$data = array();
$no = $start + 1;

while ($row = mysqli_fetch_assoc($queryData)) {
    $omset = (int)$row['omset'];
    $pengeluaran = (int)$row['total_pengeluaran'];
    $laba = $omset - $pengeluaran;
    
    $operasional_text = empty(trim($row['operasional'])) ? '-' : htmlspecialchars($row['operasional']);
    $pengeluaran_text = ($pengeluaran == 0 || empty($row['total_pengeluaran'])) ? '-' : 'Rp ' . number_format($pengeluaran, 0, ',', '.');
    
    $data[] = array(
        "no"                    => $no++,
        "id_karyawan"           => $row['id_karyawan'],
        "cabang"                => htmlspecialchars($row['cabang']),
        "tanggal"               => htmlspecialchars($row['tanggal']),
        "shift"                 => 'Shift ' . $row['shift'],
        "shift_raw"             => $row['shift'],
        "tipe_shift"            => htmlspecialchars($row['tipe_shift'] ?: '-'),
        "tipe_shift_raw"        => $row['tipe_shift'],
        "omset"                 => 'Rp ' . number_format($omset, 0, ',', '.'),
        "omset_raw"             => $omset,
        "operasional"           => $operasional_text,
        "operasional_raw"       => $row['operasional'],
        "total_pengeluaran"     => $pengeluaran_text,
        "total_pengeluaran_raw" => $pengeluaran,
        "laba_bersih"           => 'Rp ' . number_format($laba, 0, ',', '.')
    );
}

echo json_encode(array(
    "draw" => $draw,
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "total_omset_bulan" => 'Rp ' . number_format($total_omset_bulan, 0, ',', '.'),
    "data" => $data
));
?>