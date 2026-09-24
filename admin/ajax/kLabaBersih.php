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

$resTotal = mysqli_query($koneksi, "SELECT COUNT(DISTINCT k.cabang) as total FROM karyawan k JOIN user u ON k.id_user = u.id_user WHERE u.level IN ('karyawan')");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

$sqlCountFiltered = "SELECT COUNT(DISTINCT k.cabang) as total " . $sqlBase;
$resFiltered = mysqli_query($koneksi, $sqlCountFiltered);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

$sqlData = "SELECT IFNULL(k.cabang, '-') as cabang, SUM(CASE WHEN LOWER(k.tipe_shift) = 'partner' THEN IFNULL(CAST(NULLIF(k.omset, '') AS UNSIGNED), 0) / 2 ELSE IFNULL(CAST(NULLIF(k.omset, '') AS UNSIGNED), 0) END) as pendapatan_kotor, SUM(CASE WHEN LOWER(k.tipe_shift) = 'partner' THEN IFNULL(CAST(NULLIF(k.total_pengeluaran, '') AS UNSIGNED), 0) / 2 ELSE IFNULL(CAST(NULLIF(k.total_pengeluaran, '') AS UNSIGNED), 0) END) as operasional_mingguan " . $sqlBase . " GROUP BY k.cabang ORDER BY cabang ASC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;
if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $cb = $row['cabang'];
        $pendapatan_kotor = floatval($row['pendapatan_kotor']);
        $operasional_mingguan = floatval($row['operasional_mingguan']);

        $qTransfer = "SELECT total_bersih FROM pengeluaran WHERE LOWER(level) = 'transfer'";
        if (!empty($bulan)) $qTransfer .= " AND MONTH(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$bulan'";
        if (!empty($tahun)) $qTransfer .= " AND YEAR(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$tahun'";
        if (!empty($cabang)) {
            $qTransfer .= " AND LOWER(cabang) = LOWER('$cabang')";
        } else {
            $qTransfer .= " AND LOWER(cabang) = LOWER('$cb')";
        }
        $resTransfer = mysqli_query($koneksi, $qTransfer);
        $transfer = 0;
        while ($rT = mysqli_fetch_assoc($resTransfer)) {
            $transfer += floatval(preg_replace('/[^0-9]/', '', $rT['total_bersih']));
        }
        
        $operasional_mingguan += $transfer;

        $qSewa = "SELECT total_bersih FROM pengeluaran WHERE LOWER(level) = 'sewa'";
        if (!empty($bulan)) $qSewa .= " AND MONTH(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$bulan'";
        if (!empty($tahun)) $qSewa .= " AND YEAR(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$tahun'";
        if (!empty($cabang)) {
            $qSewa .= " AND LOWER(cabang) = LOWER('$cabang')";
        } else {
            $qSewa .= " AND LOWER(cabang) = LOWER('$cb')";
        }
        $resSewa = mysqli_query($koneksi, $qSewa);
        $sewa = 0;
        while ($rS = mysqli_fetch_assoc($resSewa)) {
            $sewa += floatval(preg_replace('/[^0-9]/', '', $rS['total_bersih']));
        }

        $qGaji = "SELECT total_bersih FROM pengeluaran WHERE LOWER(level) = 'staff'";
        if (!empty($bulan)) $qGaji .= " AND MONTH(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$bulan'";
        if (!empty($tahun)) $qGaji .= " AND YEAR(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$tahun'";
        if (!empty($cabang)) {
            $qGaji .= " AND LOWER(cabang) = LOWER('$cabang')";
        } else {
            $qGaji .= " AND LOWER(cabang) = LOWER('$cb')";
        }
        $resGaji = mysqli_query($koneksi, $qGaji);
        $gaji = 0;
        while ($rG = mysqli_fetch_assoc($resGaji)) {
            $gaji += floatval(preg_replace('/[^0-9]/', '', $rG['total_bersih']));
        }

        $qOpBulanan = "SELECT total_bersih FROM pengeluaran WHERE LOWER(level) IN ('netflix', 'pdam', 'wifi', 'service')";
        if (!empty($bulan)) $qOpBulanan .= " AND MONTH(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$bulan'";
        if (!empty($tahun)) $qOpBulanan .= " AND YEAR(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$tahun'";
        if (!empty($cabang)) {
            $qOpBulanan .= " AND LOWER(cabang) = LOWER('$cabang')";
        } else {
            $qOpBulanan .= " AND LOWER(cabang) = LOWER('$cb')";
        }
        $resOpBulanan = mysqli_query($koneksi, $qOpBulanan);
        $operasional_bulanan = 0;
        while ($rB = mysqli_fetch_assoc($resOpBulanan)) {
            $operasional_bulanan += floatval(preg_replace('/[^0-9]/', '', $rB['total_bersih']));
        }

        $total_pengeluaran = $operasional_mingguan + $operasional_bulanan + $sewa + $gaji;
        $pendapatan_bersih = $pendapatan_kotor - $total_pengeluaran;

        $data[] = [
            "no"                   => $no++,
            "cabang"               => $cb,
            "pendapatan_kotor"     => "Rp. " . number_format($pendapatan_kotor, 0, ',', '.'),
            "operasional_mingguan" => "Rp. " . number_format($operasional_mingguan, 0, ',', '.'),
            "operasional_bulanan"  => "Rp. " . number_format($operasional_bulanan, 0, ',', '.'),
            "sewa"                 => "Rp. " . number_format($sewa, 0, ',', '.'),
            "gaji"                 => "Rp. " . number_format($gaji, 0, ',', '.'),
            "total_pengeluaran"    => "Rp. " . number_format($total_pengeluaran, 0, ',', '.'),
            "pendapatan_bersih"    => "Rp. " . number_format($pendapatan_bersih, 0, ',', '.')
        ];
    }
}

$sqlChartCabang = "SELECT IFNULL(k.cabang, '-') as cabang, SUM(CASE WHEN LOWER(k.tipe_shift) = 'partner' THEN IFNULL(CAST(NULLIF(k.omset, '') AS UNSIGNED), 0) / 2 ELSE IFNULL(CAST(NULLIF(k.omset, '') AS UNSIGNED), 0) END) as pendapatan_kotor, SUM(CASE WHEN LOWER(k.tipe_shift) = 'partner' THEN IFNULL(CAST(NULLIF(k.total_pengeluaran, '') AS UNSIGNED), 0) / 2 ELSE IFNULL(CAST(NULLIF(k.total_pengeluaran, '') AS UNSIGNED), 0) END) as operasional_mingguan FROM karyawan k JOIN user u ON k.id_user = u.id_user WHERE u.level IN ('karyawan')";
if (!empty($bulan)) $sqlChartCabang .= " AND MONTH(k.tanggal) = '$bulan'";
if (!empty($tahun)) $sqlChartCabang .= " AND YEAR(k.tanggal) = '$tahun'";
if (!empty($cabang)) $sqlChartCabang .= " AND k.cabang = '$cabang'";
$sqlChartCabang .= " GROUP BY k.cabang ORDER BY cabang ASC";

$resChartCabang = mysqli_query($koneksi, $sqlChartCabang);
$chartDataCabang = [];
if ($resChartCabang) {
    while ($rowChart = mysqli_fetch_assoc($resChartCabang)) {
        $cb = $rowChart['cabang'];
        $pk = floatval($rowChart['pendapatan_kotor']);
        $opM = floatval($rowChart['operasional_mingguan']);

        $qTransfer = "SELECT total_bersih FROM pengeluaran WHERE LOWER(level) = 'transfer'";
        if (!empty($bulan)) $qTransfer .= " AND MONTH(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$bulan'";
        if (!empty($tahun)) $qTransfer .= " AND YEAR(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$tahun'";
        if (!empty($cabang)) {
            $qTransfer .= " AND LOWER(cabang) = LOWER('$cabang')";
        } else {
            $qTransfer .= " AND LOWER(cabang) = LOWER('$cb')";
        }
        $resTransfer = mysqli_query($koneksi, $qTransfer);
        $transfer = 0;
        while ($rT = mysqli_fetch_assoc($resTransfer)) {
            $transfer += floatval(preg_replace('/[^0-9]/', '', $rT['total_bersih']));
        }
        
        $opM += $transfer;

        $qSewa = "SELECT total_bersih FROM pengeluaran WHERE LOWER(level) = 'sewa'";
        if (!empty($bulan)) $qSewa .= " AND MONTH(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$bulan'";
        if (!empty($tahun)) $qSewa .= " AND YEAR(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$tahun'";
        if (!empty($cabang)) {
            $qSewa .= " AND LOWER(cabang) = LOWER('$cabang')";
        } else {
            $qSewa .= " AND LOWER(cabang) = LOWER('$cb')";
        }
        $resSewa = mysqli_query($koneksi, $qSewa);
        $sewa = 0;
        while ($rS = mysqli_fetch_assoc($resSewa)) {
            $sewa += floatval(preg_replace('/[^0-9]/', '', $rS['total_bersih']));
        }

        $qGaji = "SELECT total_bersih FROM pengeluaran WHERE LOWER(level) = 'staff'";
        if (!empty($bulan)) $qGaji .= " AND MONTH(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$bulan'";
        if (!empty($tahun)) $qGaji .= " AND YEAR(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$tahun'";
        if (!empty($cabang)) {
            $qGaji .= " AND LOWER(cabang) = LOWER('$cabang')";
        } else {
            $qGaji .= " AND LOWER(cabang) = LOWER('$cb')";
        }
        $resGaji = mysqli_query($koneksi, $qGaji);
        $gaji = 0;
        while ($rG = mysqli_fetch_assoc($resGaji)) {
            $gaji += floatval(preg_replace('/[^0-9]/', '', $rG['total_bersih']));
        }

        $qOpBulanan = "SELECT total_bersih FROM pengeluaran WHERE LOWER(level) IN ('netflix', 'pdam', 'wifi', 'service')";
        if (!empty($bulan)) $qOpBulanan .= " AND MONTH(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$bulan'";
        if (!empty($tahun)) $qOpBulanan .= " AND YEAR(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$tahun'";
        if (!empty($cabang)) {
            $qOpBulanan .= " AND LOWER(cabang) = LOWER('$cabang')";
        } else {
            $qOpBulanan .= " AND LOWER(cabang) = LOWER('$cb')";
        }
        $resOpBulanan = mysqli_query($koneksi, $qOpBulanan);
        $opB = 0;
        while ($rB = mysqli_fetch_assoc($resOpBulanan)) {
            $opB += floatval(preg_replace('/[^0-9]/', '', $rB['total_bersih']));
        }

        $tp = $opM + $opB + $sewa + $gaji;
        $pb = $pk - $tp;

        $chartDataCabang[] = [
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