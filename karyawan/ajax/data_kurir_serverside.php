<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json'); 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// === DIAGNOSIS SESSION LOGIN ===
if (isset($_SESSION['id_user'])) {
    $id_kurir_login = intval($_SESSION['id_user']);
} elseif (isset($_SESSION['id'])) {
    $id_kurir_login = intval($_SESSION['id']);
} elseif (isset($_SESSION['id_kurir'])) {
    $id_kurir_login = intval($_SESSION['id_kurir']);
} elseif (isset($_SESSION['user_id'])) {
    $id_kurir_login = intval($_SESSION['user_id']);
} else {
    $id_kurir_login = 2; // Fallback ID testing
}

// Ambil parameter standar DataTables Server-Side
$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$sql_total = "SELECT COUNT(*) AS total FROM rental WHERE id_kurir = $id_kurir_login AND status != 'Selesai';";
$res_total = mysqli_query($koneksi, $sql_total);
$totalData = 0;
if ($res_total) {
    $row_total = mysqli_fetch_assoc($res_total);
    $totalData = $row_total['total'];
}

$where_search = "";
if (!empty($search)) {
    $where_search = " AND (nama_perental LIKE '%$search%' 
                        OR alamat_lengkap LIKE '%$search%' 
                        OR jaminan LIKE '%$search%')";
}

$sql_data = "SELECT * FROM rental 
             WHERE id_kurir = $id_kurir_login 
             AND status != 'Selesai' 
             $where_search 
             ORDER BY id_rental DESC 
             LIMIT $start, $length";
$res_data = mysqli_query($koneksi, $sql_data);

$data = array();
$no = $start + 1;

if ($res_data) {
    while ($row = mysqli_fetch_assoc($res_data)) {
        
        // Memproses string koordinat sharelok menjadi link rute Google Maps
        $data_sharelok = trim($row['sharelok']);
        $link_final = "";
        if (!empty($data_sharelok)) {
            if (strpos($data_sharelok, 'google') !== false) {
                $link_final = (strpos($data_sharelok, 'http') === false) ? "https://" . $data_sharelok : $data_sharelok;
            } else {
                $link_final = "https://www.google.com/maps/dir/?api=1&destination=" . urlencode($data_sharelok);
            }
        }

        // Pembuatan tombol rute maps
        $btn_rute = '<span class="badge badge-light text-muted"><i>Tidak ada sharelok</i></span>';
        if (!empty($link_final)) {
            $btn_rute = '<a href="'.$link_final.'" target="_blank" class="btn btn-info btn-sm font-weight-bold text-white btn-block shadow-sm">
                            <i class="fas fa-route"></i> Rute
                         </a>';
        }

        // Mengkondisikan status penugasan
        $status_badge = '<span class="badge badge-warning text-dark font-weight-bold"><i class="fas fa-shipping-fast"></i> Proses Antar</span>';
        $status_konteks = 'Proses';

        $clean_wa = preg_replace('/[^0-9]/', '', $row['wa']);
        if (substr($clean_wa, 0, 1) === '0') {
            $clean_wa = '62' . substr($clean_wa, 1);
        }
    
        $nestedData = array();
        $nestedData[] = '<div class="text-center">'.$no++.'</div>';
        $nestedData[] = '<span class="font-weight-bold text-capitalize">'.htmlspecialchars($row['nama_perental']).'</span>';
        $nestedData[] = htmlspecialchars($row['durasi_sewa']);
        $nestedData[] = '<div class="text-center"><span class="badge badge-secondary">'.htmlspecialchars($row['jaminan']).'</span></div>';
        $nestedData[] = '<a href="https://wa.me/'.$clean_wa.'" target="_blank" class="text-success font-weight-bold"><i class="fab fa-whatsapp"></i> '.htmlspecialchars($row['wa']).'</a>';
        $nestedData[] = htmlspecialchars($row['alamat_lengkap']);
        $nestedData[] = '<div class="text-center">'.$status_badge.'</div>';
        $nestedData[] = '<div class="text-center">'.$btn_rute.'</div>';
        $nestedData[] = '<span class="font-weight-bold text-capitalize">'.htmlspecialchars($row['catatan']).'</span>';
        
        // Parameter baris TR untuk memicu event klik JQuery
        $nestedData['DT_RowId'] = 'row_' . $row['id_rental'];
        $nestedData['DT_RowAttr'] = array(
            'data-id' => $row['id_rental'],
            'data-nama' => $row['nama_perental'],
            'data-status' => $status_konteks
        );

        $data[] = $nestedData;
    }
}

// Hitung baris data yang terfilter pencarian
$totalFiltered = $totalData;
if (!empty($search)) {
    $sql_filter_count = "SELECT COUNT(*) AS total FROM rental WHERE id_kurir = $id_kurir_login AND status != 'Selesai' $where_search";
    $res_filter_count = mysqli_query($koneksi, $sql_filter_count);
    if ($res_filter_count) {
        $row_filter_count = mysqli_fetch_assoc($res_filter_count);
        $totalFiltered = $row_filter_count['total'];
    }
}

// Response JSON DataTables API
$json_data = array(
    "draw"            => intval($draw),
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
);

if (ob_get_length()) ob_clean();
header('Content-Type: application/json');
echo json_encode($json_data);
exit;
?>