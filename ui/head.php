<?php 
include 'function.php'; 

$database_connection = isset($koneksi) ? $koneksi : null;
$feedback_list = [];

if ($database_connection) {
    $query = "SELECT nama_pelanggan, email, rating, pesan, tanggal_dibuat FROM feedback_pelanggan ORDER BY tanggal_dibuat DESC";
    $result = mysqli_query($database_connection, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $feedback_list[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araya Gamestation</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logonew.webp">
    <link rel="stylesheet" href="ui/styles.css">
</head>