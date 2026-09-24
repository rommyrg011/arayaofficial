<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$resTotal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kat_unit");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

$sqlBase = "FROM kat_unit WHERE 1=1";
if (!empty($search)) {
    $sqlBase .= " AND nama_unit LIKE '%$search%'";
}

$resFiltered = mysqli_query($koneksi, "SELECT COUNT(*) as total " . $sqlBase);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

$sqlData = "SELECT * " . $sqlBase . " ORDER BY id_unit DESC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $data[] = [
            "no"           => $no++,
            "id_unit"   => $row['id_unit'],
            "nama_unit" => $row['nama_unit']
        ];
    }
}

$output = [
    "draw"            => $draw,
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
];

echo json_encode($output);
exit();