<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$whereClause = "(status IS NULL OR status != 'Selesai')";
if (!empty($search)) {
    $whereClause .= " AND (nama_perental LIKE '%$search%' 
                     OR alamat_lengkap LIKE '%$search%' 
                     OR jaminan LIKE '%$search%' 
                     OR wa LIKE '%$search%' 
                     OR status LIKE '%$search%' 
                     OR catatan LIKE '%$search%')";
}

$sqlTotal = "SELECT COUNT(*) as total FROM rental WHERE status IS NULL OR status != 'Selesai'";
$queryTotal = mysqli_query($koneksi, $sqlTotal);
$rowTotal = mysqli_fetch_assoc($queryTotal);
$totalData = $rowTotal['total'];
$sqlFiltered = "SELECT COUNT(*) as total FROM rental WHERE $whereClause";
$queryFiltered = mysqli_query($koneksi, $sqlFiltered);
$rowFiltered = mysqli_fetch_assoc($queryFiltered);
$totalFiltered = $rowFiltered['total'];

$sqlData = "SELECT * FROM rental WHERE $whereClause ORDER BY id_rental DESC LIMIT $start, $length";
$queryData = mysqli_query($koneksi, $sqlData);

$data = array();
$no = $start + 1;

while ($row = mysqli_fetch_assoc($queryData)) {
    
    // Logika pembuatan URL sharelok maps
    $data_sharelok = trim($row['sharelok']);
    $link_final = "";
    if (!empty($data_sharelok)) {
        if (strpos($data_sharelok, 'google') !== false) {
            $link_final = (strpos($data_sharelok, 'http') === false) ? "https://" . $data_sharelok : $data_sharelok;
        } else {
            $link_final = "https://www.google.com/maps/dir/?api=1&destination=" . urlencode($data_sharelok);
        }
    }

    $clean_wa = preg_replace('/[^0-9]/', '', $row['wa']);
    
    if (substr($clean_wa, 0, 1) === '0') {
        $clean_wa = '62' . substr($clean_wa, 1);
    }

    // Kompilasi Elemen HTML untuk kolom WhatsApp
    $wa_html = '<a href="https://wa.me/'.$clean_wa.'" target="_blank" class="text-success font-weight-bold">
                    <i class="fab fa-whatsapp"></i> '.htmlspecialchars($row['wa']).'
                </a>';

    // Kompilasi Elemen HTML untuk Jaminan
    $jaminan_html = '<span class="badge badge-secondary">'.htmlspecialchars($row['jaminan']).'</span>';

    // Kompilasi Catatan jika kosong
    $catatan_text = !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : '-';
    $catatan_html = '<small class="text-muted">'.$catatan_text.'</small>';

    // Struktur array data JSON sesuai definisi kolom Javascript
    $data[] = array(
        "no"             => $no++,
        "nama_perental"  => htmlspecialchars($row['nama_perental']),
        "durasi_sewa"    => htmlspecialchars($row['durasi_sewa']),
        "jaminan"        => $jaminan_html,
        "wa"             => $wa_html,
        "alamat_lengkap" => htmlspecialchars($row['alamat_lengkap']),
        "status"         => htmlspecialchars($row['status']),
        "catatan"        => $catatan_html,
        
        // Data tanpa HTML khusus untuk dibaca oleh atribut TR modal pembagian kurir
        "id_rental"      => $row['id_rental'],
        "nama_mentah"    => $row['nama_perental'],
        "alamat_mentah"  => $row['alamat_lengkap']
    );
}
$json_data = array(
    "draw"            => $draw,
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
);

header('Content-Type: application/json');
echo json_encode($json_data);
?>