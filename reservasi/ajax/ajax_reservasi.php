<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');
date_default_timezone_set('Asia/Makassar');

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$filter_tanggal = isset($_POST['filter_tanggal']) ? $_POST['filter_tanggal'] : 'semua';
$filter_ruang   = isset($_POST['filter_ruang']) ? mysqli_real_escape_string($koneksi, $_POST['filter_ruang']) : 'semua';
$filter_cabang  = isset($_POST['filter_cabang']) ? mysqli_real_escape_string($koneksi, $_POST['filter_cabang']) : 'semua';

$hari_indo = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
$bulan_indo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

// Query dasar (Dioptimasi tanpa fungsi memberatkan pada kolom)
$sqlBase = "FROM reservasi WHERE (status != 'Masuk Ruangan' OR status = '' OR status IS NULL OR status = 'Dipanggil')";

// Filter Tanggal
if ($filter_tanggal === 'hari_ini') {
    $hari_ini = date('Y-m-d');
    $sqlBase .= " AND tgl_bermain = '$hari_ini'";
} else if (in_array($filter_tanggal, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])) {
    $map_hari = ['Minggu' => 1, 'Senin' => 2, 'Selasa' => 3, 'Rabu' => 4, 'Kamis' => 5, 'Jumat' => 6, 'Sabtu' => 7];
    $angka_hari = $map_hari[$filter_tanggal];
    $sqlBase .= " AND DAYOFWEEK(tgl_bermain) = $angka_hari";
}

if ($filter_ruang !== 'semua') {
    $sqlBase .= " AND ruang = '$filter_ruang'";
}

if ($filter_cabang !== 'semua') {
    $sqlBase .= " AND cabang = '$filter_cabang'";
}

// Hitung total sebelum pencarian
$resTotal = mysqli_query($koneksi, "SELECT COUNT(id_reservasi) as total " . $sqlBase);
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

// Filter Pencarian
if (!empty($search)) {
    $sqlBase .= " AND (cabang LIKE '%$search%' OR nama_reservasi LIKE '%$search%' OR ruang LIKE '%$search%' OR whatsapp LIKE '%$search%')";
}

// Hitung total setelah pencarian
$resFiltered = mysqli_query($koneksi, "SELECT COUNT(id_reservasi) as total " . $sqlBase);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

// Ambil data
$sqlData = "SELECT * " . $sqlBase . " ORDER BY id_reservasi DESC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $tgl_db = $row['tgl_bermain'];
        $tgl_indonesia = $tgl_db;
        
        if (!empty($tgl_db) && $tgl_db != '0000-00-00' && strtotime($tgl_db) !== false) {
            $timestamp = strtotime($tgl_db);
            $tgl_indonesia = $hari_indo[date('w', $timestamp)] . ", " . date('j', $timestamp) . " " . $bulan_indo[(int)date('n', $timestamp)] . " " . date('Y', $timestamp);
        }

        $html_dp = '-'; 
        if (!empty($row['dp'])) {
            $path_gambar = "../img/" . $row['dp'];
            $html_dp = '<a href="' . $path_gambar . '" target="_blank">
                            <img src="' . $path_gambar . '" alt="DP" class="img-thumbnail" style="max-width: 70px; max-height: 50px; cursor: pointer; object-fit: cover;">
                        </a>';
        }

        $data[] = [
            "no"              => $no++,
            "id_reservasi"    => $row['id_reservasi'],
            "cabang"          => !empty($row['cabang']) ? $row['cabang'] : '-', 
            "nama_reservasi"  => $row['nama_reservasi'],
            "tgl_bermain"     => $tgl_indonesia,
            "tgl_bermain_raw" => $tgl_db, 
            "ruang"           => $row['ruang'],
            "jml_orang"       => $row['jml_orang'],
            "tambahan"    => $row['tambahan'],
            "w_kedatangan"    => $row['w_kedatangan'],
            "whatsapp"        => $row['whatsapp'],
            "dp"              => $html_dp,
            "durasi"          => $row['durasi'],
            "kode_voucher"    => !empty($row['kode_voucher']) ? $row['kode_voucher'] : '-',
            "catatan"         => $row['catatan'],
            "status"          => $row['status']
        ];
    }
}

echo json_encode([
    "draw"            => $draw,
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
]);
exit();
?>