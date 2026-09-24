<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$whereClause = "r.status = 'Selesai'";
if (!empty($search)) {
    $whereClause .= " AND (r.nama_perental LIKE '%$search%' 
                      OR r.alamat_lengkap LIKE '%$search%' 
                      OR r.jaminan LIKE '%$search%' 
                      OR r.wa LIKE '%$search%' 
                      OR r.catatan LIKE '%$search%'
                      OR u.nama_lengkap LIKE '%$search%')";
}

$sqlTotal = "SELECT COUNT(*) as total FROM rental r WHERE r.status = 'Selesai'";
$queryTotal = mysqli_query($koneksi, $sqlTotal);
if (!$queryTotal) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Query Total Gagal: " . mysqli_error($koneksi)));
    exit;
}
$rowTotal = mysqli_fetch_assoc($queryTotal);
$totalData = isset($rowTotal['total']) ? intval($rowTotal['total']) : 0;
$sqlFiltered = "SELECT COUNT(*) as total FROM rental r LEFT JOIN user u ON r.id_kurir = u.id_user WHERE $whereClause";
$queryFiltered = mysqli_query($koneksi, $sqlFiltered);
if (!$queryFiltered) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Query Filtered Gagal: " . mysqli_error($koneksi)));
    exit;
}
$rowFiltered = mysqli_fetch_assoc($queryFiltered);
$totalFiltered = isset($rowFiltered['total']) ? intval($rowFiltered['total']) : 0;
$sqlData = "SELECT r.*, u.nama_lengkap AS nama_kurir 
            FROM rental r 
            LEFT JOIN user u ON r.id_kurir = u.id_user 
            WHERE $whereClause 
            ORDER BY r.id_rental DESC 
            LIMIT $start, $length";

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

    // Badge Jaminan
    $jaminan_html = '<span class="badge badge-secondary">'.htmlspecialchars($row['jaminan']).'</span>';

    // Badge Status Selesai (Warna Hijau)
    $status_html = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> '.htmlspecialchars($row['status']).'</span>';

    // Kompilasi teks Catatan
    $catatan_text = !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : '-';
    $catatan_html = '<small class="text-muted">'.$catatan_text.'</small>';

    // Set nama kurir jika tidak ditemukan atau NULL di database
    $nama_kurir_tampil = !empty($row['nama_kurir']) ? htmlspecialchars($row['nama_kurir']) : '<span class="text-muted font-italic">Tidak Set Kurir</span>';

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
        "nama_kurir"     => $nama_kurir_tampil, // DATA BARU UNTUK KOLOM KURIR
        "catatan"        => $catatan_html
    );
}

$json_data = array(
    "draw"            => intval($draw),
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
);

// Bersihkan output buffer dari ruang kosong/spasi tak sengaja sebelum mengirim JSON
if (ob_get_length()) ob_clean();

header('Content-Type: application/json');
echo json_encode($json_data);
exit;
?>