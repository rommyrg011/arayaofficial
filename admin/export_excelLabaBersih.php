<?php
require_once __DIR__ . '/../function.php'; 

$cabang = isset($_GET['cabang']) ? mysqli_real_escape_string($koneksi, $_GET['cabang']) : '';
$bulan  = isset($_GET['bulan']) ? mysqli_real_escape_string($koneksi, $_GET['bulan']) : '';
$tahun  = isset($_GET['tahun']) ? mysqli_real_escape_string($koneksi, $_GET['tahun']) : '';

$nama_bulan = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", 
    "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", 
    "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
];

$teks_periode = "Semua Waktu";
if (!empty($bulan) && !empty($tahun)) {
    $teks_periode = "Bulan " . $nama_bulan[$bulan] . " " . $tahun;
} elseif (!empty($bulan)) {
    $teks_periode = "Bulan " . $nama_bulan[$bulan];
} elseif (!empty($tahun)) {
    $teks_periode = "Tahun " . $tahun;
}

$teks_cabang = "Semua Cabang";
if (!empty($cabang)) {
    $teks_cabang = "Cabang " . htmlspecialchars($cabang);
}

$filename = "Laba_Bersih_Cabang";
if(!empty($cabang)) $filename .= "_" . $cabang;
if(!empty($bulan)) $filename .= "_Bulan" . $bulan;
if(!empty($tahun)) $filename .= "_Tahun" . $tahun;

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=".$filename.".xls");
header("Pragma: no-cache");
header("Expires: 0");

$sql = "SELECT IFNULL(k.cabang, '-') as cabang, SUM(IFNULL(CAST(NULLIF(k.omset, '') AS UNSIGNED), 0)) as pendapatan_kotor, SUM(IFNULL(CAST(NULLIF(k.total_pengeluaran, '') AS UNSIGNED), 0)) as operasional_mingguan FROM karyawan k JOIN user u ON k.id_user = u.id_user WHERE u.level IN ('karyawan')";

if (!empty($bulan)) {
    $sql .= " AND MONTH(k.tanggal) = '$bulan'";
}
if (!empty($tahun)) {
    $sql .= " AND YEAR(k.tanggal) = '$tahun'";
}
if (!empty($cabang)) {
    $sql .= " AND k.cabang = '$cabang'";
}

$sql .= " GROUP BY k.cabang ORDER BY cabang ASC";

$res = mysqli_query($koneksi, $sql);
$data_laba = [];
$total_kotor = 0;
$total_operasional_mingguan = 0;
$total_operasional_bulanan = 0;
$total_sewa = 0;
$total_gaji = 0;
$total_pengeluaran_all = 0;
$total_bersih = 0;

if($res) {
    while($row = mysqli_fetch_assoc($res)) {
        $cb = $row['cabang'];
        $pk = floatval($row['pendapatan_kotor']);
        $op_mingguan = floatval($row['operasional_mingguan']);

        $qOpBulanan = "SELECT total_bersih FROM pengeluaran WHERE LOWER(level) IN ('netflix', 'pdam', 'wifi', 'service')";
        if (!empty($bulan)) $qOpBulanan .= " AND MONTH(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$bulan'";
        if (!empty($tahun)) $qOpBulanan .= " AND YEAR(STR_TO_DATE(tanggal, '%Y-%m-%d')) = '$tahun'";
        if (!empty($cabang)) {
            $qOpBulanan .= " AND LOWER(cabang) = LOWER('$cabang')";
        } else {
            $qOpBulanan .= " AND LOWER(cabang) = LOWER('$cb')";
        }
        $resOpBulanan = mysqli_query($koneksi, $qOpBulanan);
        $op_bulanan = 0;
        while ($rB = mysqli_fetch_assoc($resOpBulanan)) {
            $op_bulanan += floatval(preg_replace('/[^0-9]/', '', $rB['total_bersih']));
        }

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

        $tp = $op_mingguan + $op_bulanan + $sewa + $gaji;
        $pb = $pk - $tp;
        
        $total_kotor += $pk;
        $total_operasional_mingguan += $op_mingguan;
        $total_operasional_bulanan += $op_bulanan;
        $total_sewa += $sewa;
        $total_gaji += $gaji;
        $total_pengeluaran_all += $tp;
        $total_bersih += $pb;

        $data_laba[] = [
            'cabang' => $cb,
            'pendapatan_kotor' => $pk,
            'operasional_mingguan' => $op_mingguan,
            'operasional_bulanan' => $op_bulanan,
            'sewa' => $sewa,
            'gaji' => $gaji,
            'total_pengeluaran' => $tp,
            'pendapatan_bersih' => $pb
        ];
    }
}

function formatExcel($angka) {
    $is_negative = $angka < 0;
    $abs = abs($angka);
    $formatted = "Rp. " . number_format($abs, 0, ',', '.');
    return $is_negative ? "-" . $formatted : $formatted;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <center>
        <h2>Laporan Laba Bersih <?= $teks_periode; ?> <br> <?= $teks_cabang; ?></h2>
    </center>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th rowspan="2" style="background-color: #f2f2f2; height: 30px; vertical-align: middle;">No</th>
                <th rowspan="2" style="background-color: #f2f2f2; vertical-align: middle;">Cabang</th>
                <th rowspan="2" style="background-color: #f2f2f2; vertical-align: middle;">Pendapatan Kotor</th>
                <th colspan="5" style="background-color: #f2f2f2; text-align: center;">Pengeluaran</th>
                <th rowspan="2" style="background-color: #f2f2f2; vertical-align: middle;">Pendapatan Bersih</th>
            </tr>
            <tr>
                <th style="background-color: #f2f2f2; text-align: center;">Operasional Mingguan</th>
                <th style="background-color: #f2f2f2; text-align: center;">Operasional Bulanan</th>
                <th style="background-color: #f2f2f2; text-align: center;">Sewa Toko</th>
                <th style="background-color: #f2f2f2; text-align: center;">Gaji Staff</th>
                <th style="background-color: #f2f2f2; text-align: center;">Total Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if (count($data_laba) > 0) {
                foreach ($data_laba as $dl): 
                    $colorClass = $dl['pendapatan_bersih'] < 0 ? 'color: red;' : 'color: #858796;';
                    $kotorColor = 'color: #4e73df;';
                    $pengeluaranColor = 'color: #e74a3b;';
                    $mutedColor = 'color: #858796;';
            ?>
            <tr>
                <td align="center" style="<?= $mutedColor ?>"><?= $no++; ?></td>
                <td align="center" style="font-weight: bold; <?= $mutedColor ?>"><?= htmlspecialchars($dl['cabang']); ?></td>
                <td align="right" style="<?= $kotorColor ?>"><?= formatExcel($dl['pendapatan_kotor']); ?></td>
                <td align="right" style="<?= $mutedColor ?>"><?= formatExcel($dl['operasional_mingguan']); ?></td>
                <td align="right" style="<?= $mutedColor ?>"><?= formatExcel($dl['operasional_bulanan']); ?></td>
                <td align="right" style="<?= $mutedColor ?>"><?= formatExcel($dl['sewa']); ?></td>
                <td align="right" style="<?= $mutedColor ?>"><?= formatExcel($dl['gaji']); ?></td>
                <td align="right" style="<?= $pengeluaranColor ?>"><?= formatExcel($dl['total_pengeluaran']); ?></td>
                <td align="right" style="<?= $colorClass ?> font-weight: bold;"><?= formatExcel($dl['pendapatan_bersih']); ?></td>
            </tr>
            <?php 
                endforeach; 
            ?>
            <tr>
                <td colspan="2" align="center" style="font-weight: bold; color: #858796;">Total Keseluruhan</td>
                <td align="right" style="font-weight: bold; color: #4e73df;"><?= formatExcel($total_kotor); ?></td>
                <td align="right" style="font-weight: bold; color: #858796;"><?= formatExcel($total_operasional_mingguan); ?></td>
                <td align="right" style="font-weight: bold; color: #858796;"><?= formatExcel($total_operasional_bulanan); ?></td>
                <td align="right" style="font-weight: bold; color: #858796;"><?= formatExcel($total_sewa); ?></td>
                <td align="right" style="font-weight: bold; color: #858796;"><?= formatExcel($total_gaji); ?></td>
                <td align="right" style="font-weight: bold; color: #e74a3b;"><?= formatExcel($total_pengeluaran_all); ?></td>
                <?php $totalColorClass = $total_bersih < 0 ? 'color: red;' : 'color: #858796;'; ?>
                <td align="right" style="font-weight: bold; <?= $totalColorClass ?>"><?= formatExcel($total_bersih); ?></td>
            </tr>
            <?php
            } else {
            ?>
            <tr>
                <td colspan="9" align="center">Tidak ada data pada filter ini.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>