<?php
ob_start();
error_reporting(0);
require_once __DIR__ . '/../function.php'; 

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = (isset($_POST['search']) && isset($_POST['search']['value'])) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$cabang  = isset($_POST['cabang']) ? mysqli_real_escape_string($koneksi, $_POST['cabang']) : '';
$tanggal = isset($_POST['tanggal']) ? mysqli_real_escape_string($koneksi, $_POST['tanggal']) : '';
$bulan   = isset($_POST['bulan']) ? mysqli_real_escape_string($koneksi, $_POST['bulan']) : '';
$tahun   = isset($_POST['tahun']) ? mysqli_real_escape_string($koneksi, $_POST['tahun']) : '';

$is_init_load = (!isset($_POST['tanggal']) && !isset($_POST['bulan']) && !isset($_POST['tahun']));

if ($is_init_load) {
    $tanggal = date('d');
    $bulan   = date('m');
    $tahun   = date('Y');
}

$whereTable = ["u.level IN ('karyawan')"];
$joinTable = "LEFT JOIN";

if (!empty($bulan) || !empty($tahun) || !empty($cabang)) {
    $joinTable = "JOIN";
    if (!empty($bulan)) {
        $whereTable[] = "MONTH(k.tanggal) = '$bulan'";
    }
    if (!empty($tahun)) {
        $whereTable[] = "YEAR(k.tanggal) = '$tahun'";
    }
    if (!empty($cabang)) {
        $whereTable[] = "k.cabang = '$cabang'";
    }
}

if (!empty($search)) {
    $whereTable[] = "u.nama_lengkap LIKE '%$search%'";
}

$sqlBaseTable = "FROM user u $joinTable karyawan k ON u.id_user = k.id_user LEFT JOIN target t ON k.cabang = t.ncabang AND k.shift = t.shift WHERE " . implode(' AND ', $whereTable);

$resTotal = mysqli_query($koneksi, "SELECT COUNT(DISTINCT u.id_user) as total FROM user u $joinTable karyawan k ON u.id_user = k.id_user WHERE u.level IN ('karyawan')");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

$sqlCountFiltered = "SELECT COUNT(DISTINCT CONCAT(IFNULL(k.cabang, ''), '-', u.id_user)) as total " . $sqlBaseTable;
$resFiltered = mysqli_query($koneksi, $sqlCountFiltered);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

$sqlData = "SELECT u.id_user, u.nama_lengkap, IFNULL(k.cabang, '-') as cabang, SUM(IFNULL(k.omset, 0)) as total_omset, SUM(IF(CAST(k.omset AS UNSIGNED) >= CAST(t.target AS UNSIGNED) AND t.target IS NOT NULL AND t.target != '', 1, 0)) as total_target_tercapai, SUM(IF(CAST(k.omset AS UNSIGNED) > CAST(t.target AS UNSIGNED) AND t.target IS NOT NULL AND t.target != '', CAST(k.omset AS UNSIGNED) - CAST(t.target AS UNSIGNED), 0)) as total_surplus " . $sqlBaseTable . " GROUP BY k.cabang, u.id_user ORDER BY cabang ASC, total_omset DESC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;
if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $data[] = [
            "no"                    => $no++,
            "id_user"               => $row['id_user'],
            "cabang"                => $row['cabang'],
            "nama_lengkap"          => $row['nama_lengkap'],
            "total_omset"           => $row['total_omset'],
            "total_target_tercapai" => $row['total_target_tercapai'] ? $row['total_target_tercapai'] : "0",
            "total_surplus_format"  => "+ Rp. " . number_format($row['total_surplus'], 0, ',', '.'),
            "total_omset_format"    => "Rp. " . number_format($row['total_omset'], 0, ',', '.')
        ];
    }
}

$whereChart = ["u.level IN ('karyawan')"];
$joinChart = "LEFT JOIN";

if (!empty($bulan) || !empty($tahun) || !empty($cabang)) {
    $joinChart = "JOIN";
    if (!empty($bulan)) {
        $whereChart[] = "MONTH(k.tanggal) = '$bulan'";
    }
    if (!empty($tahun)) {
        $whereChart[] = "YEAR(k.tanggal) = '$tahun'";
    }
    if (!empty($cabang)) {
        $whereChart[] = "k.cabang = '$cabang'";
    }
}

if (!empty($search)) {
    $whereChart[] = "u.nama_lengkap LIKE '%$search%'";
}

$sqlBaseChart = "FROM user u $joinChart karyawan k ON u.id_user = k.id_user LEFT JOIN target t ON k.cabang = t.ncabang AND k.shift = t.shift WHERE " . implode(' AND ', $whereChart);

$condTanggal = !empty($tanggal) ? "DAY(k.tanggal) = '$tanggal'" : "1=1";

$sqlChartCabang = "SELECT u.nama_lengkap, IFNULL(k.cabang, '-') as cabang, SUM(IFNULL(k.omset, 0)) as total_omset, GROUP_CONCAT(IF($condTanggal AND CAST(k.omset AS UNSIGNED) >= CAST(t.target AS UNSIGNED) AND t.target IS NOT NULL AND t.target != '', CONCAT(k.shift, ':', k.omset, ':', (CAST(k.omset AS UNSIGNED) - CAST(t.target AS UNSIGNED))), NULL) SEPARATOR '|') as target_details " . $sqlBaseChart . " GROUP BY k.cabang, u.id_user ORDER BY cabang ASC, total_omset DESC";
$resChartCabang = mysqli_query($koneksi, $sqlChartCabang);
$chartDataCabang = [];
if ($resChartCabang) {
    while ($rowChart = mysqli_fetch_assoc($resChartCabang)) {
        $chartDataCabang[] = [
            "nama_lengkap"   => $rowChart['nama_lengkap'],
            "cabang"         => $rowChart['cabang'],
            "total_omset"    => $rowChart['total_omset'],
            "target_details" => $rowChart['target_details'] ? $rowChart['target_details'] : ""
        ];
    }
}

$sqlChartSemua = "SELECT u.nama_lengkap, SUM(IFNULL(k.omset, 0)) as total_omset " . $sqlBaseChart . " GROUP BY u.id_user ORDER BY total_omset DESC";
$resChartSemua = mysqli_query($koneksi, $sqlChartSemua);
$chartDataSemua = [];
if ($resChartSemua) {
    while ($rowChart = mysqli_fetch_assoc($resChartSemua)) {
        $chartDataSemua[] = [
            "nama_lengkap" => $rowChart['nama_lengkap'],
            "total_omset"  => $rowChart['total_omset']
        ];
    }
}

$output = [
    "draw"            => $draw,
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data,
    "chartDataCabang" => $chartDataCabang,
    "chartDataSemua"  => $chartDataSemua
];

if (ob_get_length()) {
    ob_end_clean();
}
header('Content-Type: application/json');
echo json_encode($output);
exit();
?>