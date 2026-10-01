<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

// Total data asli
$resTotal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kat_ruangan");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

// Query dasar
$sqlBase = "FROM kat_ruangan WHERE 1=1";
if (!empty($search)) {
    $sqlBase .= " AND nama_ruangan LIKE '%$search%'";
}

// Total data terfilter
$resFiltered = mysqli_query($koneksi, "SELECT COUNT(*) as total " . $sqlBase);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

// Ambil data dengan Limit
$sqlData = "SELECT * " . $sqlBase . " ORDER BY id_ruangan DESC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $data[] = [
            "no"           => $no++,
            "id_ruangan"   => $row['id_ruangan'],
            "nama_ruangan" => $row['nama_ruangan']
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