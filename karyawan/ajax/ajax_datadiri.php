<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json'); 

$id_user_login = 0;
if (isset($_SESSION['id_kurir'])) {
    $id_user_login = intval($_SESSION['id_kurir']);
} elseif (isset($_SESSION['id_user'])) {
    $id_user_login = intval($_SESSION['id_user']);
} elseif (isset($_SESSION['id'])) {
    $id_user_login = intval($_SESSION['id']);
}

if ($id_user_login === 0) {
    echo json_encode(array("error" => "Sesi login tidak ditemukan."));
    exit;
}

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$whereClause = "id_user = $id_user_login";
if (!empty($search)) {
    $whereClause .= " AND (cabang LIKE '%$search%' OR tanggal LIKE '%$search%' OR shift LIKE '%$search%')";
}

$sqlTotal = "SELECT COUNT(*) as total FROM karyawan WHERE id_user = $id_user_login";
$queryTotal = mysqli_query($koneksi, $sqlTotal);
$rowTotal = mysqli_fetch_assoc($queryTotal);
$totalData = isset($rowTotal['total']) ? intval($rowTotal['total']) : 0;

$sqlFiltered = "SELECT COUNT(*) as total FROM karyawan WHERE $whereClause";
$queryFiltered = mysqli_query($koneksi, $sqlFiltered);
$rowFiltered = mysqli_fetch_assoc($queryFiltered);
$totalFiltered = isset($rowFiltered['total']) ? intval($rowFiltered['total']) : 0;

$sqlData = "SELECT * FROM karyawan WHERE $whereClause ORDER BY id_karyawan DESC LIMIT $start, $length";
$queryData = mysqli_query($koneksi, $sqlData);

$data = array();
$no = $start + 1;

while ($row = mysqli_fetch_assoc($queryData)) {
    $format_rupiah = 'Rp ' . number_format($row['omset'], 0, ',', '.');
    $data[] = array(
        "no"          => $no++,
        "id_karyawan" => $row['id_karyawan'],
        "cabang"      => htmlspecialchars($row['cabang']),
        "tanggal"     => htmlspecialchars($row['tanggal']),
        "shift"       => 'Shift ' . htmlspecialchars($row['shift']),
        "shift_raw"   => htmlspecialchars($row['shift']),
        "omset"       => $format_rupiah,
        "omset_raw"   => $row['omset']
    );
}

$json_data = array(
    "draw"            => intval($draw),
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
);

ob_clean();
echo json_encode($json_data);
exit;
?>