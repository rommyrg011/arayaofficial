<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../function.php'; 

if (isset($db)) {
    $koneksi = $db;
} elseif (isset($koneksi)) {
    $koneksi = $koneksi;
} else {
    echo json_encode(["error" => "Variabel koneksi database tidak ditemukan"]);
    exit;
}

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$rowperpage = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

if ($koneksi instanceof PDO) {
    try {
        $totalRecords = $koneksi->query("SELECT COUNT(*) FROM customer_vouchers")->fetchColumn();
        $searchQuery = " WHERE 1=1";
        $searchParams = [];

        if (!empty($searchValue)) {
            $searchQuery .= " AND (nama_pelanggan LIKE ? OR nomor_whatsapp LIKE ? OR kode_voucher LIKE ? OR status_pakai LIKE ?)";
            $searchParams = ["%$searchValue%", "%$searchValue%", "%$searchValue%", "%$searchValue%"];
        }

        $stmt = $koneksi->prepare("SELECT COUNT(*) FROM customer_vouchers" . $searchQuery);
        $stmt->execute($searchParams);
        $totalRecordwithFilter = $stmt->fetchColumn();
        $empQuery = "SELECT * FROM customer_vouchers" . $searchQuery . " ORDER BY id DESC LIMIT " . intval($start) . ", " . intval($rowperpage);
        $stmt = $koneksi->prepare($empQuery);
        $stmt->execute($searchParams);
        $empRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        $no = $start + 1;

        foreach ($empRecords as $row) {
            $statusLower = strtolower($row['status_pakai']);
            if (strpos($statusLower, 'belum') !== false || $row['status_pakai'] == '0') {
                $statusBadge = '<span class="badge badge-warning px-2 py-1">Belum Terpakai</span>';
            } elseif (strpos($statusLower, 'sudah') !== false || $row['status_pakai'] == '1') {
                $statusBadge = '<span class="badge badge-success px-2 py-1">Sudah Terpakai</span>';
            } else {
                $statusBadge = '<span class="badge badge-secondary px-2 py-1">'.htmlspecialchars($row['status_pakai']).'</span>';
            }

            $tglKlaim = !empty($row['tanggal_klaim']) ? date('d-m-Y', strtotime($row['tanggal_klaim'])) : '-';
            $tglPakai = !empty($row['tanggal_pakai']) ? date('d-m-Y', strtotime($row['tanggal_pakai'])) : '-';

            $data[] = [
                "no"             => $no++,
                "id"             => $row['id'],
                "nama_pelanggan" => htmlspecialchars($row['nama_pelanggan']),
                "nomor_whatsapp" => htmlspecialchars($row['nomor_whatsapp']),
                "kode_voucher"   => '<code>' . htmlspecialchars($row['kode_voucher']) . '</code>',
                "status_pakai"   => $statusBadge,
                "tanggal_klaim"  => $tglKlaim,
                "tanggal_pakai"  => $tglPakai
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
    $totalQuery = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM customer_vouchers");
    $totalRecords = mysqli_fetch_assoc($totalQuery)['total'];
    $searchQuery = " WHERE 1=1";

    if (!empty($searchValue)) {
        $searchEscaped = mysqli_real_escape_string($koneksi, $searchValue);
        $searchQuery .= " AND (nama_pelanggan LIKE '%$searchEscaped%' OR nomor_whatsapp LIKE '%$searchEscaped%' OR kode_voucher LIKE '%$searchEscaped%' OR status_pakai LIKE '%$searchEscaped%')";
    }

    $filterQuery = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM customer_vouchers" . $searchQuery);
    $totalRecordwithFilter = mysqli_fetch_assoc($filterQuery)['total'];

    $empQuery = "SELECT * FROM customer_vouchers" . $searchQuery . " ORDER BY id DESC LIMIT " . intval($start) . ", " . intval($rowperpage);
    $empRecords = mysqli_query($koneksi, $empQuery);

    $data = [];
    $no = $start + 1;

    while ($row = mysqli_fetch_assoc($empRecords)) {
        $statusLower = strtolower($row['status_pakai']);
        if (strpos($statusLower, 'belum') !== false || $row['status_pakai'] == '0') {
            $statusBadge = '<span class="badge badge-warning px-2 py-1">Belum Terpakai</span>';
        } elseif (strpos($statusLower, 'sudah') !== false || $row['status_pakai'] == '1') {
            $statusBadge = '<span class="badge badge-success px-2 py-1">Sudah Terpakai</span>';
        } else {
            $statusBadge = '<span class="badge badge-secondary px-2 py-1">'.htmlspecialchars($row['status_pakai']).'</span>';
        }

        $tglKlaim = !empty($row['tanggal_klaim']) ? date('d-m-Y', strtotime($row['tanggal_klaim'])) : '-';
        $tglPakai = !empty($row['tanggal_pakai']) ? date('d-m-Y', strtotime($row['tanggal_pakai'])) : '-';

        $data[] = [
            "no"             => $no++,
            "id"             => $row['id'],
            "nama_pelanggan" => htmlspecialchars($row['nama_pelanggan']),
            "nomor_whatsapp" => htmlspecialchars($row['nomor_whatsapp']),
            "kode_voucher"   => '<code>' . htmlspecialchars($row['kode_voucher']) . '</code>',
            "status_pakai"   => $statusBadge,
            "tanggal_klaim"  => $tglKlaim,
            "tanggal_pakai"  => $tglPakai
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