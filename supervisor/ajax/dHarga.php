<?php
require_once __DIR__ . '/../function.php';
header('Content-Type: application/json');

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$resTotal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM daftar_harga");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

$sqlBase = "FROM daftar_harga WHERE 1=1";
if (!empty($search)) {
    $sqlBase .= " AND (cabang LIKE '%$search%' OR p_paket LIKE '%$search%' OR durasi LIKE '%$search%' OR daf_harga LIKE '%$search%')";
}

$resFiltered = mysqli_query($koneksi, "SELECT COUNT(*) as total " . $sqlBase);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

$sqlData = "SELECT * " . $sqlBase . " ORDER BY p_paket ASC, id_harga ASC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;
$last_paket = "";

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $display_paket = ($row['p_paket'] == $last_paket) ? "" : $row['p_paket'];
        $format_harga = "Rp. " . number_format($row['daf_harga'], 0, ',', '.');

        $data[] = [
            "no"         => $no++,
            "id_harga"   => $row['id_harga'],
            "cabang"     => $row['cabang'],
            "p_paket"    => $display_paket,
            "raw_paket"  => $row['p_paket'],
            "durasi"     => $row['durasi'],
            "daf_harga"  => $format_harga,
            "raw_harga"  => $row['daf_harga']
        ];
        $last_paket = $row['p_paket'];
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