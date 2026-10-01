<?php
require_once __DIR__ . '/../function.php';
header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'load') {
    $timers = [];
    $history = [];

    // Load Timers
    $resTimers = mysqli_query($koneksi, "SELECT * FROM timer_billing");
    if ($resTimers) {
        while ($row = mysqli_fetch_assoc($resTimers)) {
            $timers[$row['id_timer']] = [
                'cabang' => $row['cabang'],
                'noMeja' => $row['no_meja'],
                'judulMeja' => $row['judul_meja'],
                'paket' => (int)$row['paket'],
                'timeLeft' => (int)$row['time_left'],
                'endTime' => $row['end_time'],
                'isRunning' => (int)$row['is_running'] == 1
            ];
        }
    }

    // Load History
    $resHistory = mysqli_query($koneksi, "SELECT * FROM timer_history ORDER BY id DESC LIMIT 20");
    if ($resHistory) {
        while ($row = mysqli_fetch_assoc($resHistory)) {
            $history[] = [
                'id' => $row['id'],
                'cabang' => $row['cabang'],
                'noMeja' => $row['no_meja'],
                'judulMeja' => $row['judul_meja'],
                'endTime' => $row['end_time']
            ];
        }
    }

    echo json_encode(['timers' => $timers, 'history' => $history]);
    exit;
}

if ($action == 'add') {
    $id_timer = mysqli_real_escape_string($koneksi, $_POST['id_timer']);
    $cabang = mysqli_real_escape_string($koneksi, $_POST['cabang']);
    $no_meja = mysqli_real_escape_string($koneksi, $_POST['no_meja']);
    $judul_meja = mysqli_real_escape_string($koneksi, $_POST['judul_meja']);

    $query = "INSERT INTO timer_billing (id_timer, cabang, no_meja, judul_meja, paket, time_left, end_time, is_running) 
              VALUES ('$id_timer', '$cabang', '$no_meja', '$judul_meja', 3600, 0, '0', 0)";
    mysqli_query($koneksi, $query);
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'update') {
    $id_timer = mysqli_real_escape_string($koneksi, $_POST['id_timer']);
    $paket = (int)$_POST['paket'];
    $time_left = (int)$_POST['time_left'];
    $end_time = mysqli_real_escape_string($koneksi, $_POST['end_time']);
    $is_running = (int)$_POST['is_running'];

    $query = "UPDATE timer_billing SET paket=$paket, time_left=$time_left, end_time='$end_time', is_running=$is_running WHERE id_timer='$id_timer'";
    mysqli_query($koneksi, $query);
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'delete') {
    $id_timer = mysqli_real_escape_string($koneksi, $_POST['id_timer']);
    mysqli_query($koneksi, "DELETE FROM timer_billing WHERE id_timer='$id_timer'");
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'add_history') {
    $cabang = mysqli_real_escape_string($koneksi, $_POST['cabang']);
    $no_meja = mysqli_real_escape_string($koneksi, $_POST['no_meja']);
    $judul_meja = mysqli_real_escape_string($koneksi, $_POST['judul_meja']);
    $end_time = mysqli_real_escape_string($koneksi, $_POST['end_time']);

    mysqli_query($koneksi, "INSERT INTO timer_history (cabang, no_meja, judul_meja, end_time) VALUES ('$cabang', '$no_meja', '$judul_meja', '$end_time')");
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'clear_history') {
    mysqli_query($koneksi, "TRUNCATE TABLE timer_history");
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'finish') {
    $id_timer = mysqli_real_escape_string($koneksi, $_POST['id_timer']);
    $end_time_str = mysqli_real_escape_string($koneksi, $_POST['end_time_str']);

    $check = mysqli_query($koneksi, "SELECT * FROM timer_billing WHERE id_timer='$id_timer' AND is_running=1");
    
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $cabang = $row['cabang'];
        $no_meja = $row['no_meja'];
        $judul_meja = $row['judul_meja'];

        mysqli_query($koneksi, "UPDATE timer_billing SET time_left=0, end_time='0', is_running=0 WHERE id_timer='$id_timer'");
        
        mysqli_query($koneksi, "INSERT INTO timer_history (cabang, no_meja, judul_meja, end_time) VALUES ('$cabang', '$no_meja', '$judul_meja', '$end_time_str')");
        
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'already_finished']);
    }
    exit;
}
?>