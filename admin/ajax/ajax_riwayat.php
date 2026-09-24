<?php
ob_start();
error_reporting(0);
require_once __DIR__ . '/../function.php'; 

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$cabang = isset($_POST['cabang']) ? mysqli_real_escape_string($koneksi, $_POST['cabang']) : '';
$bulan  = isset($_POST['bulan']) ? mysqli_real_escape_string($koneksi, $_POST['bulan']) : '';
$tahun  = isset($_POST['tahun']) ? mysqli_real_escape_string($koneksi, $_POST['tahun']) : '';

$is_init_load = (!isset($_POST['bulan']) && !isset($_POST['tahun']));

if ($is_init_load) {
    $bulan = date('m');
    $tahun = date('Y');
}

$bulan_indo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

$resTotal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM reservasi WHERE status = 'Masuk Ruangan'");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

$whereConditions = ["status = 'Masuk Ruangan'"];

if (!empty($cabang)) {
    $whereConditions[] = "cabang = '$cabang'";
}
if (!empty($bulan)) {
    $whereConditions[] = "MONTH(tgl_bermain) = '$bulan'";
}
if (!empty($tahun)) {
    $whereConditions[] = "YEAR(tgl_bermain) = '$tahun'";
}
if (!empty($search)) {
    $whereConditions[] = "(nama_reservasi LIKE '%$search%' OR ruang LIKE '%$search%')";
}

$sqlBase = "FROM reservasi WHERE " . implode(' AND ', $whereConditions);

$resFiltered = mysqli_query($koneksi, "SELECT COUNT(*) as total " . $sqlBase);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

$sqlChart = "SELECT cabang, tgl_bermain " . $sqlBase;
$resChart = mysqli_query($koneksi, $sqlChart);
$chartData = [];
if ($resChart) {
    while ($cRow = mysqli_fetch_assoc($resChart)) {
        $chartData[] = [
            "cabang" => $cRow['cabang'],
            "tgl_bermain" => $cRow['tgl_bermain']
        ];
    }
}

$sqlData = "SELECT * " . $sqlBase . " ORDER BY id_reservasi DESC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        
        $tgl_db = $row['tgl_bermain'];
        if (!empty($tgl_db) && $tgl_db != '0000-00-00') {
            $timestamp = strtotime($tgl_db);
            $hari      = date('j', $timestamp);
            $bulan_int = (int)date('n', $timestamp);
            $tahun_int = date('Y', $timestamp);
            $tgl_indonesia = "$hari " . $bulan_indo[$bulan_int] . " $tahun_int";
        } else {
            $tgl_indonesia = $tgl_db;
        }

        $file_dp = $row['dp'];
        $html_dp = '-';
        if (!empty($file_dp)) {
            $html_dp = '<a href="../img/' . $file_dp . '" target="_blank">
                            <img src="../img/' . $file_dp . '" alt="DP" class="img-thumbnail" style="max-width: 70px; max-height: 50px; cursor: pointer;">
                        </a>';
        }

        $data[] = [
            "no"             => $no++,
            "id_reservasi"   => $row['id_reservasi'],
            "cabang"         => $row['cabang'],
            "nama_reservasi" => $row['nama_reservasi'],
            "tgl_bermain"    => $tgl_indonesia,
            "ruang"          => $row['ruang'],
            "jml_orang"      => $row['jml_orang'],
            "w_kedatangan"   => $row['w_kedatangan'],
            "whatsapp"       => $row['whatsapp'],
            "dp"             => $html_dp,
            "durasi"         => $row['durasi'],
            "catatan"        => $row['catatan']
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

if (ob_get_length()) {
    ob_end_clean();
}
header('Content-Type: application/json');
echo json_encode($output);
exit();