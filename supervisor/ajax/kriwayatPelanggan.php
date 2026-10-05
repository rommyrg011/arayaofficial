<?php
error_reporting(0);
require_once __DIR__ . '/../../function.php';

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$sqlWhere = " WHERE whatsapp IS NOT NULL AND whatsapp != '' AND kode_voucher IS NOT NULL AND kode_voucher != '' AND kode_voucher != '-'";

if (!empty($search)) {
    $sqlWhere .= " AND whatsapp LIKE '%$search%' ";
}

$qTotal = mysqli_query($koneksi, "SELECT COUNT(DISTINCT whatsapp) AS total FROM reservasi WHERE whatsapp IS NOT NULL AND whatsapp != '' AND kode_voucher IS NOT NULL AND kode_voucher != '' AND kode_voucher != '-'");
$totalData = ($qTotal) ? mysqli_fetch_assoc($qTotal)['total'] : 0;

$qFiltered = mysqli_query($koneksi, "SELECT COUNT(DISTINCT whatsapp) AS total FROM reservasi $sqlWhere");
$totalFiltered = ($qFiltered) ? mysqli_fetch_assoc($qFiltered)['total'] : 0;

$sqlData = "SELECT 
    MAX(id_reservasi) as last_id,
    whatsapp, 
    MAX(nama_reservasi) as nama,
    COUNT(id_reservasi) as total_pakai 
    FROM reservasi 
    $sqlWhere 
    GROUP BY whatsapp 
    ORDER BY last_id DESC 
    LIMIT $start, $length";

$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;
$errorMsg = "";

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $data[] = [
            "no" => $no++,
            "nama" => $row['nama'], 
            "whatsapp" => $row['whatsapp'],
            "total_pakai" => $row['total_pakai'] . ' Kali',
            "aksi" => '<button class="btn btn-success btn-sm btn-kirim-voucher"><i class="fab fa-whatsapp"></i> Kirim</button>'
        ];
    }
} else {
    $errorMsg = mysqli_error($koneksi);
}

ob_clean();
header('Content-Type: application/json');

$response = [
    "draw" => $draw,
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
];

if (!empty($errorMsg)) {
    $response["error"] = "SQL Error: " . $errorMsg;
}

echo json_encode($response);
exit();
?>