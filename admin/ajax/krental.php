<?php
ob_start();
error_reporting(0);
require_once __DIR__ . '/../function.php'; 

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = (isset($_POST['search']) && isset($_POST['search']['value'])) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$bulan = !empty($_POST['bulan']) ? mysqli_real_escape_string($koneksi, $_POST['bulan']) : date('m');
$tahun = !empty($_POST['tahun']) ? mysqli_real_escape_string($koneksi, $_POST['tahun']) : date('Y');

$resTotal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE level = 'karyawan'");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

$whereConditions = ["u.level = 'karyawan'"];
$joinType = "LEFT JOIN"; 

if (!empty($bulan) || !empty($tahun)) {
    $joinType = "JOIN"; 
    if (!empty($bulan)) {
        $whereConditions[] = "MONTH(r.tgl_selesai) = '$bulan'";
    }
    if (!empty($tahun)) {
        $whereConditions[] = "YEAR(r.tgl_selesai) = '$tahun'";
    }
}

if (!empty($search)) {
    $whereConditions[] = "u.nama_lengkap LIKE '%$search%'";
}

$sqlBase = "FROM user u 
            $joinType rental r ON u.id_user = r.id_kurir AND r.status = 'Selesai' 
            WHERE " . implode(' AND ', $whereConditions);

$sqlGroup = " GROUP BY u.id_user";

$sqlCountFiltered = "SELECT COUNT(DISTINCT u.id_user) as total " . $sqlBase;
$resFiltered = mysqli_query($koneksi, $sqlCountFiltered);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

$sqlChart = "SELECT u.id_user, u.nama_lengkap, COUNT(r.id_rental) as total_pengantaran " . $sqlBase . $sqlGroup . " ORDER BY total_pengantaran DESC";
$resChart = mysqli_query($koneksi, $sqlChart);

$chartData = [];
if ($resChart) {
    while ($cRow = mysqli_fetch_assoc($resChart)) {
        $chartData[] = [
            "id_user"           => $cRow['id_user'],
            "nama_kurir"        => $cRow['nama_lengkap'],
            "total_pengantaran" => $cRow['total_pengantaran']
        ];
    }
}

$sqlData = "SELECT u.id_user, u.nama_lengkap, COUNT(r.id_rental) as total_pengantaran " . $sqlBase . $sqlGroup . " ORDER BY total_pengantaran DESC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $data[] = [
            "no"                => $no++,
            "id_user"           => $row['id_user'],
            "nama_kurir"        => $row['nama_lengkap'],
            "total_pengantaran" => $row['total_pengantaran']
        ];
    }
}

$output = [
    "draw"            => $draw,
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data,
    "chartData"       => $chartData
];

ob_clean();
header('Content-Type: application/json');
echo json_encode($output);
exit();