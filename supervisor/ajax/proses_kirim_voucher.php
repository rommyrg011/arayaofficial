<?php
error_reporting(0);
ob_start();

require_once __DIR__ . '/../../function.php';

$whatsapp = isset($_POST['whatsapp']) ? mysqli_real_escape_string($koneksi, $_POST['whatsapp']) : '';
$nama = isset($_POST['nama']) ? mysqli_real_escape_string($koneksi, $_POST['nama']) : 'Pelanggan';
$promo = isset($_POST['promo']) ? mysqli_real_escape_string($koneksi, $_POST['promo']) : '';
$kode = isset($_POST['kode']) ? mysqli_real_escape_string($koneksi, $_POST['kode']) : '';
$pesan = isset($_POST['pesan']) ? $_POST['pesan'] : '';

if(empty($whatsapp) || empty($kode) || empty($pesan)) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Data tidak lengkap']);
    exit();
}

$promo_id = 1;
if ($promo == 'Voucher vip') $promo_id = 2;
if ($promo == 'Voucher Premiere') $promo_id = 3;

mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=0");

$query = "INSERT INTO customer_vouchers (nama_pelanggan, nomor_whatsapp, promo_id, kode_voucher, status_pakai, tanggal_klaim) 
          VALUES ('$nama', '$whatsapp', '$promo_id', '$kode', 'Belum Digunakan', NOW())";
$insert = mysqli_query($koneksi, $query);

mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=1");

if ($insert) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'target' => $whatsapp,
        'message' => $pesan,
      ),
      CURLOPT_HTTPHEADER => array(
        'Authorization: pDch4KtF3uLaQQ4rwJ6L'
      ),
    ));

    $response = curl_exec($curl);
    $curl_err = curl_error($curl);
    curl_close($curl);

    if ($curl_err) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'message' => 'Voucher tersimpan di DB, namun gagal kirim WA (SSL/Koneksi): ' . $curl_err]);
        exit();
    }
    
    $resObj = json_decode($response, true);
    
    ob_clean();
    header('Content-Type: application/json');

    if(isset($resObj['status']) && ($resObj['status'] === true || $resObj['status'] === 'true')) {
        echo json_encode(['status' => true, 'message' => 'Voucher berhasil dibuat dan dikirim ke pelanggan.']);
    } else {
        $reason = isset($resObj['reason']) ? $resObj['reason'] : 'Respon tidak valid dari Fonnte';
        echo json_encode(['status' => false, 'message' => 'Voucher tersimpan, namun gagal kirim WA: ' . $reason]);
    }
} else {
    $db_err = mysqli_error($koneksi);
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Gagal menyimpan ke database: ' . $db_err]);
}
exit();
?>