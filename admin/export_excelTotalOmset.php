<?php
require_once __DIR__ . '/../function.php'; 

$cabang  = isset($_GET['cabang']) ? mysqli_real_escape_string($koneksi, $_GET['cabang']) : '';
$tanggal = isset($_GET['tanggal']) ? mysqli_real_escape_string($koneksi, $_GET['tanggal']) : '';
$bulan   = isset($_GET['bulan']) ? mysqli_real_escape_string($koneksi, $_GET['bulan']) : '';
$tahun   = isset($_GET['tahun']) ? mysqli_real_escape_string($koneksi, $_GET['tahun']) : '';

$is_init_load = (!isset($_GET['tanggal']) && !isset($_GET['bulan']) && !isset($_GET['tahun']));

if ($is_init_load) {
    $tanggal = date('d');
    $bulan   = date('m');
    $tahun   = date('Y');
}

$nama_bulan = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", 
    "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", 
    "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
];

$teks_periode = "Semua Waktu";
if (!empty($tanggal) && !empty($bulan) && !empty($tahun)) {
    $teks_periode = $tanggal . " " . $nama_bulan[$bulan] . " " . $tahun;
} elseif (!empty($bulan) && !empty($tahun)) {
    $teks_periode = $nama_bulan[$bulan] . " " . $tahun;
} elseif (!empty($bulan)) {
    $teks_periode = "Bulan " . $nama_bulan[$bulan];
} elseif (!empty($tahun)) {
    $teks_periode = "Tahun " . $tahun;
}

$teks_cabang = empty($cabang) ? "Semua Cabang" : "Cabang " . $cabang;

$teks_periode_tabel = "Semua Waktu";
if (!empty($tanggal) && !empty($bulan)) {
    $teks_periode_tabel = $tanggal . " " . $nama_bulan[$bulan];
} elseif (!empty($bulan)) {
    $teks_periode_tabel = $nama_bulan[$bulan];
} elseif (!empty($tahun)) {
    $teks_periode_tabel = "Tahun " . $tahun;
}

$whereConditions = ["u.level IN ('karyawan')"];
$joinType = "LEFT JOIN";

if (!empty($tanggal) || !empty($bulan) || !empty($tahun) || !empty($cabang)) {
    $joinType = "JOIN"; 
    if (!empty($tanggal)) {
        $whereConditions[] = "DAY(k.tanggal) = '$tanggal'";
    }
    if (!empty($bulan)) {
        $whereConditions[] = "MONTH(k.tanggal) = '$bulan'";
    }
    if (!empty($tahun)) {
        $whereConditions[] = "YEAR(k.tanggal) = '$tahun'";
    }
    if (!empty($cabang)) {
        $whereConditions[] = "k.cabang = '$cabang'";
    }
}

$sqlData = "SELECT u.nama_lengkap, IFNULL(k.cabang, '-') as cabang, 
            SUM(IFNULL(k.omset, 0)) as total_omset,
            SUM(IF(CAST(k.omset AS UNSIGNED) >= CAST(t.target AS UNSIGNED) AND t.target IS NOT NULL AND t.target != '', 1, 0)) as total_target_tercapai,
            SUM(IF(CAST(k.omset AS UNSIGNED) > CAST(t.target AS UNSIGNED) AND t.target IS NOT NULL AND t.target != '', CAST(k.omset AS UNSIGNED) - CAST(t.target AS UNSIGNED), 0)) as total_surplus 
            FROM user u 
            $joinType karyawan k ON u.id_user = k.id_user 
            LEFT JOIN target t ON k.cabang = t.ncabang AND k.shift = t.shift 
            WHERE " . implode(' AND ', $whereConditions) . " 
            GROUP BY k.cabang, u.id_user 
            ORDER BY cabang ASC, total_omset DESC";

$resData = mysqli_query($koneksi, $sqlData);

$data_operator = [];
$peringkat_cabang_omset = [];
$peringkat_cabang_target = [];

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $data_operator[] = $row;
        
        $cbg = $row['cabang'];
        $total_omset = intval($row['total_omset']);
        $total_target = intval($row['total_target_tercapai']);
        
        if (!isset($peringkat_cabang_omset[$cbg])) {
            $peringkat_cabang_omset[$cbg] = [
                'tertinggi' => -1,
                'operator' => []
            ];
        }
        
        if ($total_omset > $peringkat_cabang_omset[$cbg]['tertinggi']) {
            $peringkat_cabang_omset[$cbg]['tertinggi'] = $total_omset;
            $peringkat_cabang_omset[$cbg]['operator'] = [$row['nama_lengkap']];
        } elseif ($total_omset === $peringkat_cabang_omset[$cbg]['tertinggi'] && $total_omset > 0) {
            $peringkat_cabang_omset[$cbg]['operator'][] = $row['nama_lengkap'];
        }

        if (!isset($peringkat_cabang_target[$cbg])) {
            $peringkat_cabang_target[$cbg] = [
                'tertinggi' => 0,
                'operator' => []
            ];
        }

        if ($total_target > $peringkat_cabang_target[$cbg]['tertinggi']) {
            $peringkat_cabang_target[$cbg]['tertinggi'] = $total_target;
            $peringkat_cabang_target[$cbg]['operator'] = [$row['nama_lengkap']];
        } elseif ($total_target === $peringkat_cabang_target[$cbg]['tertinggi'] && $total_target > 0) {
            $peringkat_cabang_target[$cbg]['operator'][] = $row['nama_lengkap'];
        }
    }
}

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Total_Omset_".$teks_cabang."_".$teks_periode.".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <center>
        <h2>Laporan Total Omset Operator <br> <?= $teks_cabang; ?> Periode <?= $teks_periode; ?></h2>
    </center>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="background-color: #f2f2f2; width: 50px;">No</th>
                <th style="background-color: #f2f2f2; width: 150px;">Periode</th>
                <th style="background-color: #f2f2f2; width: 150px;">Cabang</th>
                <th style="background-color: #f2f2f2; width: 250px;">Nama Operator</th>
                <th style="background-color: #f2f2f2; width: 180px;">Total Target Tercapai</th>
                <th style="background-color: #f2f2f2; width: 180px;">Surplus</th>
                <th style="background-color: #f2f2f2; width: 200px;">Total Omset</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if (count($data_operator) > 0) {
                foreach ($data_operator as $do): 
            ?>
            <tr>
                <td align="center"><?= $no++; ?></td>
                <td align="center"><?= $teks_periode_tabel; ?></td>
                <td align="center"><?= htmlspecialchars($do['cabang']); ?></td>
                <td><?= htmlspecialchars($do['nama_lengkap']); ?></td>
                <td align="center"><?= $do['total_target_tercapai'] ? $do['total_target_tercapai'] : 0; ?></td>
                <td align="right" style="color: green; font-weight: bold;">+ Rp. <?= number_format($do['total_surplus'], 0, ',', '.'); ?></td>
                <td align="right">Rp. <?= number_format($do['total_omset'], 0, ',', '.'); ?></td>
            </tr>
            <?php 
                endforeach; 
            } else {
            ?>
            <tr>
                <td colspan="7" align="center">Tidak ada data input omset pada periode dan cabang ini.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <br>
    <?php if (count($peringkat_cabang_omset) > 0) { ?>
    <table border="1" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th colspan="2" style="background-color: #ffffcc; font-weight: bold;">🏆 Peringkat 1 Omset Berdasarkan Cabang</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($peringkat_cabang_omset as $cbg => $data_peringkat) { 
                if ($data_peringkat['tertinggi'] > 0 && count($data_peringkat['operator']) > 0) {
                    $teks_terbaik = implode(" & ", $data_peringkat['operator']) . " (Rp. " . number_format($data_peringkat['tertinggi'], 0, ',', '.') . ")";
            ?>
            <tr>
                <td style="font-weight: bold; width: 150px;">Cabang <?= $cbg; ?></td>
                <td style="font-weight: bold; width: 350px;"><?= $teks_terbaik; ?></td>
            </tr>
            <?php 
                } 
            } 
            ?>
        </tbody>
    </table>
    <br>
    <table border="1" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th colspan="2" style="background-color: #e6f2ff; font-weight: bold;">🎯 Peringkat 1 Target Tercapai Berdasarkan Cabang</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($peringkat_cabang_target as $cbg => $data_peringkat) { 
                if ($data_peringkat['tertinggi'] > 0 && count($data_peringkat['operator']) > 0) {
                    $teks_terbaik = implode(" & ", $data_peringkat['operator']) . " (" . $data_peringkat['tertinggi'] . " Kali Tercapai)";
            ?>
            <tr>
                <td style="font-weight: bold; width: 150px;">Cabang <?= $cbg; ?></td>
                <td style="font-weight: bold; width: 350px;"><?= $teks_terbaik; ?></td>
            </tr>
            <?php 
                } 
            } 
            ?>
        </tbody>
    </table>
    <?php } ?>
</body>
</html>