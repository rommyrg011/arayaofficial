<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json'); 

$id_kurir_login = 0;

if (isset($_SESSION['id_kurir'])) {
    $id_kurir_login = intval($_SESSION['id_kurir']);
} elseif (isset($_SESSION['id_user'])) {
    $id_kurir_login = intval($_SESSION['id_user']);
} elseif (isset($_SESSION['id'])) {
    $id_kurir_login = intval($_SESSION['id']);
}

// Jika setelah dicek semua tetap 0, berarti kurir benar-benar belum login
if ($id_kurir_login === 0) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Sesi login tidak ditemukan. Pastikan Anda telah login sebagai kurir."));
    exit;
}

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';
$status_filter = "Proses";

// Deteksi cerdas: Jika kueri ini dipanggil oleh halaman riwayat selesai, ubah filter ke 'Selesai'
if (isset($_SERVER['HTTP_REFERER']) && strpos(strtolower($_SERVER['HTTP_REFERER']), 'riwayat') !== false) {
    $status_filter = "Selesai";
}

// FILTER UTAMA: Khusus Kurir yang login dan sesuai status halaman (Selesai / Proses)
$whereClause = "id_kurir = $id_kurir_login AND status = '$status_filter'";

// Jika user mengetik sesuatu di kolom pencarian DataTables
if (!empty($search)) {
    $whereClause .= " AND (nama_perental LIKE '%$search%' 
                      OR alamat_lengkap LIKE '%$search%' 
                      OR jaminan LIKE '%$search%' 
                      OR wa LIKE '%$search%' 
                      OR catatan LIKE '%$search%')";
}

// Menghitung total seluruh data khusus kurir tersebut dengan status terkait
$sqlTotal = "SELECT COUNT(*) as total FROM rental WHERE id_kurir = $id_kurir_login AND status = '$status_filter'";
$queryTotal = mysqli_query($koneksi, $sqlTotal);
if (!$queryTotal) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Query Total Gagal: " . mysqli_error($koneksi)));
    exit;
}
$rowTotal = mysqli_fetch_assoc($queryTotal);
$totalData = isset($rowTotal['total']) ? intval($rowTotal['total']) : 0;

// Menghitung total data setelah dicocokkan dengan input pencarian
$sqlFiltered = "SELECT COUNT(*) as total FROM rental WHERE $whereClause";
$queryFiltered = mysqli_query($koneksi, $sqlFiltered);
if (!$queryFiltered) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Query Filtered Gagal: " . mysqli_error($koneksi)));
    exit;
}
$rowFiltered = mysqli_fetch_assoc($queryFiltered);
$totalFiltered = isset($rowFiltered['total']) ? intval($rowFiltered['total']) : 0;

// Mengambil data spesifik tugas kurir dari database dengan LIMIT halaman
$sqlData = "SELECT * FROM rental WHERE $whereClause ORDER BY id_rental DESC LIMIT $start, $length";
$queryData = mysqli_query($koneksi, $sqlData);
if (!$queryData) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Query Data Gagal: " . mysqli_error($koneksi)));
    exit;
}

$data = array();
$no = $start + 1;

while ($row = mysqli_fetch_assoc($queryData)) {
    
    // Normalisasi & Pembuatan elemen HTML link WhatsApp
    $clean_wa = str_replace([' ', '-', '+'], '', $row['wa']);
    $wa_html = '<a href="https://wa.me/'.$clean_wa.'" target="_blank" class="text-success font-weight-bold">
                    <i class="fab fa-whatsapp"></i> '.htmlspecialchars($row['wa']).'
                </a>';
    $jaminan_html = '<span class="badge badge-secondary">'.htmlspecialchars($row['jaminan']).'</span>';

    // Badge Status Tugas (Kuning jika Proses, Hijau jika Selesai)
    if ($row['status'] == 'Selesai') {
        $status_html = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Selesai</span>';
    } else {
        $status_html = '<span class="badge badge-warning text-dark"><i class="fas fa-motorcycle"></i> Proses</span>';
    }

    // Kompilasi teks Catatan
    $catatan_text = !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : '-';
    $catatan_html = '<small class="text-muted">'.$catatan_text.'</small>';

    // Memasukkan baris data ke dalam susunan array JSON
    $data[] = array(
        "no"             => $no++,
        "nama_perental"  => htmlspecialchars($row['nama_perental']),
        "durasi_sewa"    => htmlspecialchars($row['durasi_sewa']),
        "jaminan"        => $jaminan_html,
        "wa"             => $wa_html,
        "alamat_lengkap" => htmlspecialchars($row['alamat_lengkap']),
        "status"         => $status_html,
        "img"            => $row['img'],
        "catatan"        => $catatan_html
    );
}

// Output format JSON standar DataTables Server-Side
$json_data = array(
    "draw"            => intval($draw),
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
);

// Bersihkan buffer dari text notice atau output tak sengaja sebelum baris ini
ob_clean();

header('Content-Type: application/json');
echo json_encode($json_data);
exit;
?>