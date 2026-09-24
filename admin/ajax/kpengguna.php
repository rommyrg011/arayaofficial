<?php
require_once __DIR__ . '/../function.php';  
header('Content-Type: application/json');

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($koneksi, $_POST['search']['value']) : '';

$resTotal = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user");
$totalData = ($resTotal) ? mysqli_fetch_assoc($resTotal)['total'] : 0;

$sqlBase = "FROM user WHERE 1=1";
if (!empty($search)) {
    $sqlBase .= " AND (nama_lengkap LIKE '%$search%' 
                  OR username LIKE '%$search%' 
                  OR no_wa LIKE '%$search%' 
                  OR jabatan LIKE '%$search%' 
                  OR level LIKE '%$search%')";
}

$resFiltered = mysqli_query($koneksi, "SELECT COUNT(*) as total " . $sqlBase);
$totalFiltered = ($resFiltered) ? mysqli_fetch_assoc($resFiltered)['total'] : 0;

$sqlData = "SELECT * " . $sqlBase . " ORDER BY id_user DESC LIMIT $start, $length";
$resData = mysqli_query($koneksi, $sqlData);

$data = [];
$no = $start + 1;

if ($resData) {
    while ($row = mysqli_fetch_assoc($resData)) {
        $fotoNama = trim($row['images']);
        $projectRoot = dirname(__DIR__); 
        $fotoPathServer = $projectRoot . '/../img/' . $fotoNama;
        
        $fotoPathBrowser = '../img/' . $fotoNama; 
        
        $ext = strtolower(pathinfo($fotoNama, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        if (!empty($fotoNama) && $fotoNama !== '0' && in_array($ext, $allowedExt) && file_exists($fotoPathServer)) {
            $gambarHtml = '<img src="' . $fotoPathBrowser . '?t=' . time() . '" width="35" height="35" class="rounded-circle" style="object-fit: cover; border: 1px solid #ccc; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
        } else {
            $gambarHtml = '<span class="badge badge-light text-muted" style="font-size: 11px; padding: 5px;">No Image</span>';
        }

        $data[] = [
            "no"            => $no++,
            "id_user"       => $row['id_user'],
            "nama_lengkap"  => $row['nama_lengkap'],
            "jabatan"       => '<span class="text-capitalize">' . $row['jabatan'] . '</span>',
            "jabatan_raw"   => $row['jabatan'],
            "no_wa"         => $row['no_wa'],
            "username"      => $row['username'],
            "foto_nama"     => $fotoNama, 
            "images"        => $gambarHtml,   
            "level"         => '<span class="badge badge-info text-capitalize">' . $row['level'] . '</span>',
            "level_raw"     => $row['level']
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