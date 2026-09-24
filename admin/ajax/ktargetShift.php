<?php
error_reporting(0);
require_once __DIR__ . '/../function.php';
header('Content-Type: application/json');

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$resTotal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM target");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

$sqlBase = "FROM target WHERE 1=1";
if (!empty($search)) {
    $sqlBase .= " AND (ncabang LIKE '%$search%' OR shift LIKE '%$search%' OR target LIKE '%$search%')";
}

$resFiltered = mysqli_query($koneksi, "SELECT COUNT(*) as total " . $sqlBase);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

$limitQuery = "";
if ($length != -1) {
    $limitQuery = " LIMIT $start, $length";
}

$sqlData = "SELECT * " . $sqlBase . " ORDER BY ncabang ASC, shift ASC" . $limitQuery;
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $data[] = [
            "id_target" => $row['id_target'],
            "ncabang"   => $row['ncabang'],
            "shift"     => $row['shift'],
            "target"    => $row['target']
        ];
    }
}

$output = [
    "draw"            => intval($draw),
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
];

if (ob_get_length()) {
    ob_clean();
}

echo json_encode($output);
exit();