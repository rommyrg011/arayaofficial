<?php
session_start();

// 1. Menghapus semua data session yang tersimpan di server
$_SESSION = array();

// 2. Menghapus cookie session lama dari browser pengguna secara total (Mencegah Session Fixation/Hijacking)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Menghancurkan session di server
session_destroy();

// 4. Mengarahkan kembali ke halaman utama (index login)
header("Location: ../login"); // Atau ganti ke "index.php" jika file login utama Anda bernama index.php
exit();
?>