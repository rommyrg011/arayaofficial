<?php
require_once __DIR__ . '/../function.php'; 

$cabang = isset($_GET['cabang']) ? mysqli_real_escape_string($koneksi, $_GET['cabang']) : '';
$bulan  = isset($_GET['bulan']) ? mysqli_real_escape_string($koneksi, $_GET['bulan']) : '';
$tahun  = isset($_GET['tahun']) ? mysqli_real_escape_string($koneksi, $_GET['tahun']) : '';

$is_init_load = (!isset($_GET['bulan']) && !isset($_GET['tahun']));

if ($is_init_load) {
    $bulan = date('m');
    $tahun = date('Y');
}

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

$teks_cabang = "Semua Cabang";
if (!empty($cabang)) {
    $teks_cabang = "Cabang " . htmlspecialchars($cabang);
}

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

$sqlData = "SELECT cabang, nama_reservasi, tgl_bermain, ruang, durasi, whatsapp 
            FROM reservasi 
            WHERE " . implode(' AND ', $whereConditions) . " 
            ORDER BY tgl_bermain DESC, id_reservasi DESC";

$resData = mysqli_query($koneksi, $sqlData);

$data_reservasi = [];
$total_pelanggan = 0;

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        
        $tgl_db = $row['tgl_bermain'];
        if (!empty($tgl_db) && $tgl_db != '0000-00-00') {
            $timestamp = strtotime($tgl_db);
            $hari      = date('j', $timestamp);
            $bulan_int = sprintf("%02d", date('n', $timestamp));
            $tahun_int = date('Y', $timestamp);
            $tgl_indonesia = "$hari " . $nama_bulan[$bulan_int] . " $tahun_int";
        } else {
            $tgl_indonesia = $tgl_db;
        }

        $row['tgl_bermain_indo'] = $tgl_indonesia;
        $data_reservasi[] = $row;
        $total_pelanggan++;
    }
}

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Reservasi_".$teks_cabang."_".$teks_periode.".xls");
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
        <h2>Laporan Riwayat Reservasi <?= $teks_periode; ?> <br> <?= $teks_cabang; ?></h2>
    </center>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="background-color: #f2f2f2;">No</th>
                <th style="background-color: #f2f2f2;">Cabang</th>
                <th style="background-color: #f2f2f2;">Nama</th>
                <th style="background-color: #f2f2f2;">Tanggal</th>
                <th style="background-color: #f2f2f2;">Ruang</th>
                <th style="background-color: #f2f2f2;">Durasi</th>
                <th style="background-color: #f2f2f2;">Whatsapp</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if ($total_pelanggan > 0) {
                foreach ($data_reservasi as $dr): 
            ?>
            <tr>
                <td align="center"><?= $no++; ?></td>
                <td align="center"><?= htmlspecialchars($dr['cabang']); ?></td>
                <td><?= htmlspecialchars($dr['nama_reservasi']); ?></td>
                <td align="center"><?= $dr['tgl_bermain_indo']; ?></td>
                <td align="center"><?= htmlspecialchars($dr['ruang']); ?></td>
                <td align="center"><?= htmlspecialchars($dr['durasi']); ?></td>
                <td align="center"><?= htmlspecialchars($dr['whatsapp']); ?></td>
            </tr>
            <?php 
                endforeach; 
            } else {
            ?>
            <tr>
                <td colspan="7" align="center">Tidak ada data reservasi pada filter ini.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <br>
    
    <table border="1" style="border-collapse: collapse;">
        <tr>
            <td style="background-color: #ccffcc; font-weight: bold; width: 250px;">Total Pelanggan :</td>
            <td style="font-weight: bold; text-align: center; width: 100px;"><?= $total_pelanggan; ?> Pelanggan</td>
        </tr>
    </table>
</body>
</html>