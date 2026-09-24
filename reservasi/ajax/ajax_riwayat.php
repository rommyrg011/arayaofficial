<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

// Array pembantu untuk mengubah tanggal SQL ke Bahasa Indonesia
$bulan_indo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

$resTotal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM reservasi WHERE status = 'Masuk Ruangan'");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;
$sqlBase = "FROM reservasi WHERE status = 'Masuk Ruangan'";

// Fitur pencarian bawaan DataTables
if (!empty($search)) {
    $sqlBase .= " AND (nama_reservasi LIKE '%$search%' OR ruang LIKE '%$search%')";
}

// Total data terfilter
$resFiltered = mysqli_query($koneksi, "SELECT COUNT(*) as total " . $sqlBase);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

// Ambil data dengan Limit dan Urutan terbaru
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
            $bulan     = $bulan_indo[(int)date('n', $timestamp)];
            $tahun     = date('Y', $timestamp);
            $tgl_indonesia = "$hari $bulan $tahun";
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
            "cabang"   => $row['cabang'],
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
    "data"            => $data
];

echo json_encode($output);
exit();