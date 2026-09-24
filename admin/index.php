<?php 
// 2. Ambil fungsi global & koneksi database dari root
require_once __DIR__ . '/../function.php'; 

// 3. Render susunan layout template pembuka
include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Selamat Datang Di Dashboard Admin</h1>
    </div>
</div>
<?php
// 4. Render penutup layout dan script javascript
include 'template/footer.php'; 
include 'template/script.php'; 
?>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>