<?php
// Kunci Keamanan: Memastikan Session aktif sebelum mengecek data login
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// JIKA BELUM LOGIN: Tendang guest/tamu keluar secara paksa ke halaman login utama
if (!isset($_SESSION['username']) || !isset($_SESSION['level'])) {
    // Bersihkan semua data session sisa untuk keamanan
    session_unset();
    session_destroy();
    
    // Alihkan ke halaman login.php di luar folder dashboard
    header("Location: ../login");
    exit();
}
?>