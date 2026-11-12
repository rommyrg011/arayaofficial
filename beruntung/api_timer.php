<?php
require_once __DIR__ . '/../function.php';
header('Content-Type: application/json');

$action = isset($_POST['action']) ?$_POST['action'] : '';

if ($action == 'load') {
    $timers = [];$history = [];

    // Load Timers (pastikan mengambil is_active dan pindah_ke)
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
                'isRunning' => (int)$row['is_running'] == 1,
                'isActive' => isset($row['is_active']) ? (int)$row['is_active'] == 1 : true,
                'pindahKe' => $row['pindah_ke'] ?? null
            ];
        }
    }

    // Load History
    $resHistory = mysqli_query($koneksi, "SELECT * FROM timer_history ORDER BY id DESC LIMIT 20");
    if ($resHistory) {
        while ($row = mysqli_fetch_assoc($resHistory)) {$history[] = [
                'id' => $row['id'],
                'cabang' => $row['cabang'],
                'noMeja' => $row['no_meja'],
                'judulMeja' => $row['judul_meja'],
                'endTime' => $row['end_time']
            ];
        }
    }

    echo json_encode(['timers' => $timers, 'history' =>$history]);
    exit;
}

if ($action == 'add') {
    $id_timer = mysqli_real_escape_string($koneksi, $_POST['id_timer']);$cabang = mysqli_real_escape_string($koneksi,$_POST['cabang']);
    $no_meja = mysqli_real_escape_string($koneksi, $_POST['no_meja']);$judul_meja = mysqli_real_escape_string($koneksi,$_POST['judul_meja']);

    $query = "INSERT INTO timer_billing (id_timer, cabang, no_meja, judul_meja, paket, time_left, end_time, is_running, is_active) 
              VALUES ('$id_timer', '$cabang', '$no_meja', '$judul_meja', 3600, 0, '0', 0, 1)";
    mysqli_query($koneksi,$query);
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'update') {
    $id_timer = mysqli_real_escape_string($koneksi, $_POST['id_timer']);$paket = (int)$_POST['paket'];$time_left = (int)$_POST['time_left'];$end_time = mysqli_real_escape_string($koneksi,$_POST['end_time']);
    $is_running = (int)$_POST['is_running'];

    $query = "UPDATE timer_billing SET paket=$paket, time_left=$time_left, end_time='$end_time', is_running=$is_running WHERE id_timer='$id_timer'";
    mysqli_query($koneksi,$query);
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'toggle_active') {$id_timer = mysqli_real_escape_string($koneksi,$_POST['id_timer']);
    $is_active = (int)$_POST['is_active'];

    $query = "UPDATE timer_billing SET is_active=$is_active WHERE id_timer='$id_timer'";
    mysqli_query($koneksi,$query);
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'pindah_meja') {
    $id_sumber = mysqli_real_escape_string($koneksi, $_POST['id_sumber']);$id_tujuan = mysqli_real_escape_string($koneksi,$_POST['id_tujuan']);

    // Ambil data dari meja sumber
    $resSumber = mysqli_query($koneksi, "SELECT * FROM timer_billing WHERE id_timer='$id_sumber'");
    if (mysqli_num_rows($resSumber) > 0) {
        $sumber = mysqli_fetch_assoc($resSumber);
        
        $resTujuan = mysqli_query($koneksi, "SELECT * FROM timer_billing WHERE id_timer='$id_tujuan'");
        if (mysqli_num_rows($resTujuan) > 0) {
            $tujuan = mysqli_fetch_assoc($resTujuan);

            // Pindahkan data waktu (time_left, end_time, is_running, paket) ke meja tujuan
            $qUpdateTujuan = "UPDATE timer_billing SET 
                paket = {$sumber['paket']},
                time_left = {$sumber['time_left']},
                end_time = '{$sumber['end_time']}',
                is_running = {$sumber['is_running']},
                pindah_ke = NULL 
                WHERE id_timer = '$id_tujuan'";
            mysqli_query($koneksi,$qUpdateTujuan);

            // Kosongkan/reset meja sumber dan tandai pindah ke meja tujuan
            $noMejaTujuan = $tujuan['no_meja'];$qUpdateSumber = "UPDATE timer_billing SET 
                time_left = 0,
                end_time = '0',
                is_running = 0,
                pindah_ke = '$noMejaTujuan'
                WHERE id_timer = '$id_sumber'";
            mysqli_query($koneksi,$qUpdateSumber);

            echo json_encode(['status' => 'success']);
            exit;
        }
    }
    echo json_encode(['status' => 'error', 'message' => 'Meja tidak ditemukan']);
    exit;
}

if ($action == 'delete') {$id_timer = mysqli_real_escape_string($koneksi,$_POST['id_timer']);
    mysqli_query($koneksi, "DELETE FROM timer_billing WHERE id_timer='$id_timer'");
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'clear_history') {
    mysqli_query($koneksi, "TRUNCATE TABLE timer_history");
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action == 'finish') {
    $id_timer = mysqli_real_escape_string($koneksi, $_POST['id_timer']);$end_time_str = mysqli_real_escape_string($koneksi,$_POST['end_time_str']);

    $check = mysqli_query($koneksi, "SELECT * FROM timer_billing WHERE id_timer='$id_timer' AND is_running=1");
    
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $cabang =$row['cabang'];
        $no_meja =$row['no_meja'];
        $judul_meja =$row['judul_meja'];

        mysqli_query($koneksi, "UPDATE timer_billing SET time_left=0, end_time='0', is_running=0, pindah_ke=NULL WHERE id_timer='$id_timer'");
        mysqli_query($koneksi, "INSERT INTO timer_history (cabang, no_meja, judul_meja, end_time) VALUES ('$cabang', '$no_meja', '$judul_meja', '$end_time_str')");
        
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'already_finished']);
    }
    exit;
}
?>