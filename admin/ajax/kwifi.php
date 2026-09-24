<?php
require_once __DIR__ . '/../function.php';  
header('Content-Type: application/json');

function tanggal_indonesia($tanggal) {
    if(empty($tanggal)) return '-';
    
    $bulan = array (
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $pecahkan = explode('-', $tanggal);
    if(count($pecahkan) == 3) {
        return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
    }
    
    return $tanggal;
}

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$resTotal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengeluaran WHERE level='wifi'");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

$sqlBase = "FROM pengeluaran WHERE level='wifi'";
if (!empty($search)) {
    $sqlBase .= " AND (nama_staff_ruko LIKE '%$search%' OR tanggal LIKE '%$search%' OR cabang LIKE '%$search%')";
}

$resFiltered = mysqli_query($koneksi, "SELECT COUNT(*) as total " . $sqlBase);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

$sqlData = "SELECT * " . $sqlBase . " ORDER BY id_pengeluaran DESC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $data[] = [
            "no"                => $no++,
            "id_pengeluaran"    => $row['id_pengeluaran'],
            "tanggal_raw"       => $row['tanggal'],
            "tanggal"           => tanggal_indonesia($row['tanggal']),
            "cabang"            => ucfirst($row['cabang']),
            "nama_staff_ruko"   => $row['nama_staff_ruko'],
            "total_bersih"      => $row['total_bersih'],
            "level"             => $row['level']
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
?>