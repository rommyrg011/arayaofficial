<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../function.php'; 

if (isset($db)) {
    $koneksi = $db;
} elseif (isset($koneksi)) {
    $koneksi = $koneksi;
} else {
    echo json_encode(["error" => "Variabel koneksi database (\$db atau \$koneksi) tidak ditemukan di function.php"]);
    exit;
}

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$rowperpage = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

if ($koneksi instanceof PDO) {
    try {
        $totalRecords = $koneksi->query("SELECT COUNT(*) FROM promos")->fetchColumn();

        $searchQuery = " WHERE 1=1";
        $searchParams = [];
        if (!empty($searchValue)) {
            $searchQuery .= " AND (nama_promo LIKE ? OR kode_prefix LIKE ? OR status LIKE ?)";
            $searchParams = ["%$searchValue%", "%$searchValue%", "%$searchValue%"];
        }

        $stmt = $koneksi->prepare("SELECT COUNT(*) FROM promos" . $searchQuery);
        $stmt->execute($searchParams);
        $totalRecordwithFilter = $stmt->fetchColumn();

        $empQuery = "SELECT * FROM promos" . $searchQuery . " ORDER BY id DESC LIMIT " . intval($start) . ", " . intval($rowperpage);
        $stmt = $koneksi->prepare($empQuery);
        $stmt->execute($searchParams);
        $empRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        $no = $start + 1;
        foreach ($empRecords as $row) {
            $statusBadge = (ucfirst($row['status']) == 'Aktif') ? '<span class="badge badge-success px-2 py-1">Aktif</span>' : '<span class="badge badge-danger px-2 py-1">Nonaktif</span>';
            $kuotaText = ($row['kuota'] == 0) ? '<span class="text-danger font-weight-bold">0</span>' : $row['kuota'];
            
            $hargaNormal = (float)$row['harga_normal'];
            $potongan = (float)$row['potongan'];
            $hargaDiskon = $hargaNormal - $potongan;
            if ($hargaDiskon < 0) $hargaDiskon = 0;

            $hargaCard = '
            <div class="border rounded p-2 text-center bg-white shadow-sm mx-auto" style="min-width:120px; max-width:180px;">
                <div class="text-muted small" style="text-decoration: line-through;">Rp ' . number_format($hargaNormal, 0, ',', '.') . '</div>
                <div class="text-success font-weight-bold">Rp ' . number_format($hargaDiskon, 0, ',', '.') . '</div>
            </div>';

            $bestValueBadge = ($row['best_value'] == 'Ya') ? '<span class="badge badge-warning px-2 py-1"><i class="fas fa-star"></i> Ya</span>' : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

            $data[] = [
                "no"               => $no++,
                "id"               => $row['id'],
                "nama_promo"       => htmlspecialchars($row['nama_promo']),
                "kode_prefix"      => htmlspecialchars($row['kode_prefix']),
                "kuota"            => $kuotaText,
                "harga_card"       => $hargaCard,
                "best_value"       => $bestValueBadge,
                "status"           => $statusBadge,
                "kuota_raw"        => $row['kuota'], 
                "harga_normal_raw" => $row['harga_normal'], 
                "potongan_raw"     => $row['potongan'],   
                "keterangan_raw"   => $row['keterangan'],
                "best_value_raw"   => $row['best_value'],
                "status_raw"       => $row['status']
            ];
        }

        echo json_encode([
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        ]);
        exit;

    } catch (PDOException $e) {
        echo json_encode(["error" => "PDO SQL Error: " . $e->getMessage()]);
        exit;
    }
} else if ($koneksi instanceof mysqli) {
    $totalQuery = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM promos");
    $totalRecords = mysqli_fetch_assoc($totalQuery)['total'];

    $searchQuery = " WHERE 1=1";
    if (!empty($searchValue)) {
        $searchEscaped = mysqli_real_escape_string($koneksi, $searchValue);
        $searchQuery .= " AND (nama_promo LIKE '%$searchEscaped%' OR kode_prefix LIKE '%$searchEscaped%' OR status LIKE '%$searchEscaped%')";
    }

    $filterQuery = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM promos" . $searchQuery);
    $totalRecordwithFilter = mysqli_fetch_assoc($filterQuery)['total'];

    $empQuery = "SELECT * FROM promos" . $searchQuery . " ORDER BY id DESC LIMIT " . intval($start) . ", " . intval($rowperpage);
    $empRecords = mysqli_query($koneksi, $empQuery);

    $data = [];
    $no = $start + 1;
    while ($row = mysqli_fetch_assoc($empRecords)) {
        $statusBadge = (ucfirst($row['status']) == 'Aktif') ? '<span class="badge badge-success px-2 py-1">Aktif</span>' : '<span class="badge badge-danger px-2 py-1">Nonaktif</span>';
        $kuotaText = ($row['kuota'] == 0) ? '<span class="text-danger font-weight-bold">0</span>' : $row['kuota'];
        
        $hargaNormal = (float)$row['harga_normal'];
        $potongan = (float)$row['potongan'];
        $hargaDiskon = $hargaNormal - $potongan;
        if ($hargaDiskon < 0) $hargaDiskon = 0;

        $hargaCard = '
        <div class="border rounded p-2 text-center bg-white shadow-sm mx-auto" style="min-width:120px; max-width:180px;">
            <div class="text-muted small" style="text-decoration: line-through;">Rp ' . number_format($hargaNormal, 0, ',', '.') . '</div>
            <div class="text-success font-weight-bold">Rp ' . number_format($hargaDiskon, 0, ',', '.') . '</div>
        </div>';

        $bestValueBadge = ($row['best_value'] == 'Ya') ? '<span class="badge badge-warning px-2 py-1"><i class="fas fa-star"></i> Ya</span>' : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

        $data[] = [
            "no"               => $no++,
            "id"               => $row['id'],
            "nama_promo"       => htmlspecialchars($row['nama_promo']),
            "kode_prefix"      => htmlspecialchars($row['kode_prefix']),
            "kuota"            => $kuotaText,
            "harga_card"       => $hargaCard,
            "best_value"       => $bestValueBadge,
            "status"           => $statusBadge,
            "kuota_raw"        => $row['kuota'],
            "harga_normal_raw" => $row['harga_normal'],
            "potongan_raw"     => $row['potongan'], 
            "keterangan_raw"   => $row['keterangan'],
            "best_value_raw"   => $row['best_value'],
            "status_raw"       => $row['status']
        ];
    }

    echo json_encode([
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    ]);
    exit;
}
?>