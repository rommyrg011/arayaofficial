<?php
ob_start();
error_reporting(0);
require_once __DIR__ . '/../function.php';

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = (isset($_POST['search']) && isset($_POST['search']['value'])) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$cabang = isset($_POST['cabang']) ? mysqli_real_escape_string($koneksi, $_POST['cabang']) : '';
$bulan  = isset($_POST['bulan']) ? mysqli_real_escape_string($koneksi, $_POST['bulan']) : '';
$tahun  = isset($_POST['tahun']) ? mysqli_real_escape_string($koneksi, $_POST['tahun']) : '';

$is_init_load = (!isset($_POST['bulan']) && !isset($_POST['tahun']));

if ($is_init_load) {
    $bulan = date('m');
    $tahun = date('Y');
}

$levelMingguan = ['transfer'];
$levelBulanan  = ['netflix', 'pdam', 'wifi', 'service', 'listrik', 'jagamalam', 'lainnya'];
$levelSewa     = ['sewa'];
$levelGaji     = ['staff'];

function totalPengeluaran($koneksi, $levels, $cb, $bulan, $tahun)
{
    $in = "'" . implode("','", array_map('strtolower', $levels)) . "'";
    $cbEsc = mysqli_real_escape_string($koneksi, $cb);
    $q = "SELECT total_bersih FROM pengeluaran WHERE LOWER(level) IN ($in) AND LOWER(cabang) = LOWER('$cbEsc')";
    if (!empty($bulan)) $q .= " AND MONTH(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$bulan'";
    if (!empty($tahun)) $q .= " AND YEAR(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$tahun'";
    $res = mysqli_query($koneksi, $q);
    $total = 0;
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $total += floatval(preg_replace('/[^0-9]/', '', $r['total_bersih']));
        }
    }
    return $total;
}

function hitungCabang($koneksi, $row, $bulan, $tahun, $levelMingguan, $levelBulanan, $levelSewa, $levelGaji)
{
    $cb  = $row['cabang'];
    $pk  = floatval($row['pendapatan_kotor']);
    $opM = floatval($row['operasional_mingguan']);

    $opM += totalPengeluaran($koneksi, $levelMingguan, $cb, $bulan, $tahun);
    $opB  = totalPengeluaran($koneksi, $levelBulanan, $cb, $bulan, $tahun);
    $sewa = totalPengeluaran($koneksi, $levelSewa, $cb, $bulan, $tahun);
    $gaji = totalPengeluaran($koneksi, $levelGaji, $cb, $bulan, $tahun);

    $tp = $opM + $opB + $sewa + $gaji;
    $pb = $pk - $tp;

    return [
        "cabang"               => $cb,
        "pendapatan_kotor"     => $pk,
        "operasional_mingguan" => $opM,
        "operasional_bulanan"  => $opB,
        "sewa"                 => $sewa,
        "gaji"                 => $gaji,
        "total_pengeluaran"    => $tp,
        "pendapatan_bersih"    => $pb
    ];
}

function rp($angka)
{
    return "Rp. " . number_format($angka, 0, ',', '.');
}

$whereConditions = ["u.level IN ('karyawan')"];

if (!empty($bulan)) {
    $whereConditions[] = "MONTH(k.tanggal) = '$bulan'";
}
if (!empty($tahun)) {
    $whereConditions[] = "YEAR(k.tanggal) = '$tahun'";
}
if (!empty($cabang)) {
    $whereConditions[] = "k.cabang = '$cabang'";
}
if (!empty($search)) {
    $whereConditions[] = "u.nama_lengkap LIKE '%$search%'";
}

$sqlBase = "FROM karyawan k JOIN user u ON k.id_user = u.id_user WHERE " . implode(' AND ', $whereConditions);

$sqlSelect = "SELECT IFNULL(k.cabang, '-') as cabang, SUM(CASE WHEN LOWER(k.tipe_shift) = 'partner' THEN IFNULL(CAST(NULLIF(k.omset, '') AS UNSIGNED), 0) / 2 ELSE IFNULL(CAST(NULLIF(k.omset, '') AS UNSIGNED), 0) END) as pendapatan_kotor, SUM(CASE WHEN LOWER(k.tipe_shift) = 'partner' THEN IFNULL(CAST(NULLIF(k.total_pengeluaran, '') AS UNSIGNED), 0) / 2 ELSE IFNULL(CAST(NULLIF(k.total_pengeluaran, '') AS UNSIGNED), 0) END) as operasional_mingguan ";

$resTotal = mysqli_query($koneksi, "SELECT COUNT(DISTINCT k.cabang) as total FROM karyawan k JOIN user u ON k.id_user = u.id_user WHERE u.level IN ('karyawan')");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

$resFiltered = mysqli_query($koneksi, "SELECT COUNT(DISTINCT k.cabang) as total " . $sqlBase);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

$sqlData = $sqlSelect . $sqlBase . " GROUP BY k.cabang ORDER BY cabang ASC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;
if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $h = hitungCabang($koneksi, $row, $bulan, $tahun, $levelMingguan, $levelBulanan, $levelSewa, $levelGaji);
        $data[] = [
            "no"                   => $no++,
            "cabang"               => $h['cabang'],
            "pendapatan_kotor"     => rp($h['pendapatan_kotor']),
            "operasional_mingguan" => rp($h['operasional_mingguan']),
            "operasional_bulanan"  => rp($h['operasional_bulanan']),
            "sewa"                 => rp($h['sewa']),
            "gaji"                 => rp($h['gaji']),
            "total_pengeluaran"    => rp($h['total_pengeluaran']),
            "pendapatan_bersih"    => rp($h['pendapatan_bersih'])
        ];
    }
}

$sqlChartCabang = $sqlSelect . "FROM karyawan k JOIN user u ON k.id_user = u.id_user WHERE u.level IN ('karyawan')";
if (!empty($bulan)) $sqlChartCabang .= " AND MONTH(k.tanggal) = '$bulan'";
if (!empty($tahun)) $sqlChartCabang .= " AND YEAR(k.tanggal) = '$tahun'";
if (!empty($cabang)) $sqlChartCabang .= " AND k.cabang = '$cabang'";
$sqlChartCabang .= " GROUP BY k.cabang ORDER BY cabang ASC";

$resChartCabang = mysqli_query($koneksi, $sqlChartCabang);
$chartDataCabang = [];
if ($resChartCabang) {
    while ($rowChart = mysqli_fetch_assoc($resChartCabang)) {
        $chartDataCabang[] = hitungCabang($koneksi, $rowChart, $bulan, $tahun, $levelMingguan, $levelBulanan, $levelSewa, $levelGaji);
    }
}

$output = [
    "draw"            => $draw,
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data,
    "chartDataCabang" => $chartDataCabang
];

if (ob_get_length()) {
    ob_end_clean();
}
header('Content-Type: application/json');
echo json_encode($output);
exit();
?>