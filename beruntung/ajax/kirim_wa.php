<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target = isset($_POST['target']) ? trim($_POST['target']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($target) || empty($message)) {
        echo json_encode(['status' => false, 'reason' => 'Nomor WhatsApp atau isi pesan tidak boleh kosong!']);
        exit;
    }

    // Membersihkan nomor target dari spasi, strip (-) atau tanda (+)
    $target = preg_replace('/[^0-9]/', '', $target);

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => http_build_query(array(
        'target' => $target,
        'message' => $message,
        'countryCode' => '62',
        'delay' => '2'
      )),
      CURLOPT_HTTPHEADER => array(
        'Authorization: pDch4KtF3uLaQQ4rwJ6L',
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo json_encode(['status' => false, 'reason' => 'cURL Error: ' . $err]);
    } else {
        echo $response;
    }
} else {
    echo json_encode(['status' => false, 'reason' => 'Akses langsung tidak diizinkan.']);
}