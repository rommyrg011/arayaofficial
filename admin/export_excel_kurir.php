<?php
require_once __DIR__ . '/../function.php'; 

$bulan = !empty($_GET['bulan']) ? mysqli_real_escape_string($koneksi, $_GET['bulan']) : date('m');
$tahun = !empty($_GET['tahun']) ? mysqli_real_escape_string($koneksi, $_GET['tahun']) : date('Y');

$nama_bulan = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", 
    "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", 
    "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
];

$teks_periode = "Semua Waktu";
if (!empty($bulan) && !empty($tahun)) {
    $teks_periode = $nama_bulan[$bulan] . " " . $tahun;
} elseif (!empty($bulan)) {
    $teks_periode = "Bulan " . $nama_bulan[$bulan];
} elseif (!empty($tahun)) {
    $teks_periode = "Tahun " . $tahun;
}

$teks_periode_tabel = "Semua Waktu";
if (!empty($bulan)) {
    $teks_periode_tabel = $nama_bulan[$bulan];
} elseif (!empty($tahun)) {
    $teks_periode_tabel = "Tahun " . $tahun;
}

$whereConditions = ["u.level = 'karyawan'"];
$joinType = "LEFT JOIN";

if (!empty($bulan) || !empty($tahun)) {
    $joinType = "JOIN"; 
    if (!empty($bulan)) {
        $whereConditions[] = "MONTH(r.tgl_selesai) = '$bulan'";
    }
    if (!empty($tahun)) {
        $whereConditions[] = "YEAR(r.tgl_selesai) = '$tahun'";
    }
}

$sqlData = "SELECT u.nama_lengkap, COUNT(r.id_rental) as total_pengantaran 
            FROM user u 
            $joinType rental r ON u.id_user = r.id_kurir AND r.status = 'Selesai' 
            WHERE " . implode(' AND ', $whereConditions) . " 
            GROUP BY u.id_user 
            ORDER BY total_pengantaran DESC";

$resData = mysqli_query($koneksi, $sqlData);

$data_kurir = [];
$tertinggi = -1;
$kurir_terbaik = [];

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $data_kurir[] = $row;
        
        $total = intval($row['total_pengantaran']);
        if ($total > $tertinggi) {
            $tertinggi = $total;
            $kurir_terbaik = [$row['nama_lengkap']];
        } elseif ($total === $tertinggi && $tertinggi > 0) {
            $kurir_terbaik[] = $row['nama_lengkap'];
        }
    }
}

$teks_terbaik = "Belum Ada Data";
if ($tertinggi > 0 && count($kurir_terbaik) > 0) {
    $teks_terbaik = implode(" & ", $kurir_terbaik) . " (" . $tertinggi . " x Pengantaran)";
}

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Pengantaran_Kurir_".$teks_periode.".xls");
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
        <h2>Laporan Performa Pengantaran Kurir <br> Bulan <?= $teks_periode; ?></h2>
    </center>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="background-color: #f2f2f2;">No</th>
                <th style="background-color: #f2f2f2;">Periode</th>
                <th style="background-color: #f2f2f2;">Nama Operator</th>
                <th style="background-color: #f2f2f2;">Total Pengantaran</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if (count($data_kurir) > 0) {
                foreach ($data_kurir as $dk): 
            ?>
            <tr>
                <td align="center"><?= $no++; ?></td>
                <td align="center"><?= $teks_periode_tabel; ?></td>
                <td><?= htmlspecialchars($dk['nama_lengkap']); ?></td>
                <td align="center"><?= $dk['total_pengantaran']; ?></td>
            </tr>
            <?php 
                endforeach; 
            } else {
            ?>
            <tr>
                <td colspan="4" align="center">Tidak ada data pengantaran pada periode ini.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <br>
    
    <table border="1" style="border-collapse: collapse;">
        <tr>
            <td style="background-color: #ffffcc; font-weight: bold; width: 300px;">🏆 Kurir Terbaik Periode Ini :</td>
            <td style="font-weight: bold;"><?= $teks_terbaik; ?></td>
        </tr>
    </table>
</body>
</html>