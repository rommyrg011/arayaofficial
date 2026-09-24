<?php
require_once 'function.php';

if (isset($_GET['aksi']) && $_GET['aksi'] == 'cek_voucher') {
    header('Content-Type: application/json');
    $kode = mysqli_real_escape_string($koneksi, strtoupper(trim($_GET['kode'])));
    
    $query_voucher = "SELECT cv.*, p.potongan FROM customer_vouchers cv 
                      INNER JOIN promos p ON cv.promo_id = p.id 
                      WHERE cv.kode_voucher = '$kode'";
    $cek = mysqli_query($koneksi, $query_voucher);
    
    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        if ($data['status_pakai'] == 'Sudah Digunakan') {
            echo json_encode(['status' => 'error', 'pesan' => 'Voucher sudah digunakan sebelumnya!']);
        } else {
            $diskon = isset($data['potongan']) ? intval($data['potongan']) : 0; 
            echo json_encode(['status' => 'sukses', 'diskon' => $diskon, 'pesan' => 'Voucher berhasil diterapkan!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'pesan' => 'Kode voucher tidak valid / salah ketik.']);
    }
    exit;
}

$target_dir = "img/";
$sukses_kirim = false;
$gagal_kirim  = false;
$pesan_error  = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cabang          = mysqli_real_escape_string($koneksi, $_POST['cabang']);
    $nama_reservasi  = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $tgl_bermain     = mysqli_real_escape_string($koneksi, $_POST['tanggal_bermain']);
    $ruang           = mysqli_real_escape_string($koneksi, $_POST['ruang']);
    $jml_orang       = mysqli_real_escape_string($koneksi, $_POST['jumlah_orang']);
    $mikrofon_input  = isset($_POST['mikrofon']) ? mysqli_real_escape_string($koneksi, $_POST['mikrofon']) : 'tidak';
    $tambahan        = $mikrofon_input;
    $waktu_input     = mysqli_real_escape_string($koneksi, $_POST['waktu_kedatangan']);
    $w_kedatangan    = $waktu_input . " WITA"; 
    $durasi          = mysqli_real_escape_string($koneksi, $_POST['durasi']);
    $wa_input        = trim($_POST['whatsapp']);
    $whatsapp        = empty($wa_input) ? "-" : mysqli_real_escape_string($koneksi, $wa_input);
    $catatan_input   = trim($_POST['catatan']);
    $catatan         = empty($catatan_input) ? "-" : mysqli_real_escape_string($koneksi, $catatan_input);
    $status          = "Pending"; 
    $input_voucher   = strtoupper(trim($_POST['kode_voucher']));
    $kode_voucher    = empty($input_voucher) ? NULL : mysqli_real_escape_string($koneksi, $input_voucher);
    $proses_database = true;

    if (!empty($kode_voucher)) {
        $cek_voucher = mysqli_query($koneksi, "SELECT * FROM customer_vouchers WHERE kode_voucher = '$kode_voucher'");
        
        if (mysqli_num_rows($cek_voucher) > 0) {
            $data_v = mysqli_fetch_assoc($cek_voucher);
            if ($data_v['status_pakai'] == 'Sudah Digunakan') {
                $proses_database = false;
                $gagal_kirim = true;
                $pesan_error = "MOHON MAAF: Kode voucher tersebut sudah pernah digunakan sebelumnya!";
            }
        } else {
            $proses_database = false;
            $gagal_kirim = true;
            $pesan_error = "MOHON MAAF: Kode voucher tidak terdaftar atau salah ketik.";
        }
    }

    $nama_file_baru = ""; 
    $proses_upload = true;

    if ($proses_database && isset($_FILES['bukti_dp']) && $_FILES['bukti_dp']['error'] != 4) {
        $file_name = $_FILES['bukti_dp']['name'];
        $file_tmp  = $_FILES['bukti_dp']['tmp_name'];
        $file_size = $_FILES['bukti_dp']['size']; 
        $file_error = $_FILES['bukti_dp']['error'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = array('jpg', 'jpeg', 'png', 'pdf', 'docx');
        $max_size = 10 * 1024 * 1024; 

        if ($file_error === 0) {
            if (in_array($file_ext, $allowed_ext)) {
                if ($file_size <= $max_size) {
                    $nama_dasar = pathinfo($file_name, PATHINFO_FILENAME);
                    $nama_bersih = preg_replace("/[^a-zA-Z0-9]/", "_", $nama_dasar);
                    
                    if (in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
                        $nama_file_baru = time() . "_" . $nama_bersih . ".webp";
                        $target_file    = $target_dir . $nama_file_baru;
                        $image = null;
                        
                        if ($file_ext == 'jpg' || $file_ext == 'jpeg') {
                            $image = @imagecreatefromjpeg($file_tmp);
                        } elseif ($file_ext == 'png') {
                            $image = @imagecreatefrompng($file_tmp);
                            if ($image !== false) {
                                imagepalettetotruecolor($image);
                                imagealphablending($image, true);
                                imagesavealpha($image, true);
                            }
                        }
                        
                        if ($image !== false) {
                            if (!imagewebp($image, $target_file, 80)) {
                                $proses_upload = false;
                                $gagal_kirim = true;
                                $pesan_error = "Gagal mengkonversi dan menyimpan gambar ke WebP.";
                            }
                            imagedestroy($image);
                        } else {
                            $proses_upload = false;
                            $gagal_kirim = true;
                            $pesan_error = "File gambar rusak atau tidak dapat diproses.";
                        }
                    } else {
                        $nama_file_baru = time() . "_" . $nama_bersih . "." . $file_ext;
                        $target_file    = $target_dir . $nama_file_baru;
                        
                        if (!move_uploaded_file($file_tmp, $target_file)) {
                            $proses_upload = false;
                            $gagal_kirim = true;
                            $pesan_error = "Gagal memindahkan file dokumen.";
                        }
                    }
                } else {
                    $proses_upload = false;
                    $gagal_kirim = true;
                    $pesan_error = "Ukuran file terlalu besar. Maksimal 10 MB.";
                }
            } else {
                $proses_upload = false;
                $gagal_kirim = true;
                $pesan_error = "Format file tidak didukung (Gunakan JPG, JPEG, PNG, PDF, DOCX).";
            }
        } else {
            $proses_upload = false;
            $gagal_kirim = true;
            if ($file_error == 1 || $file_error == 2) {
                $pesan_error = "Ukuran file melebihi batas maksimal konfigurasi server.";
            } else {
                $pesan_error = "Terjadi kesalahan pada file yang diunggah.";
            }
        }
    }

    if ($proses_database && $proses_upload) {
        $val_voucher = $kode_voucher ? "'$kode_voucher'" : "NULL";

        $query = "INSERT INTO reservasi (cabang, nama_reservasi, tgl_bermain, ruang, jml_orang, tambahan, w_kedatangan, durasi, whatsapp, dp, catatan, status, kode_voucher) 
                  VALUES ('$cabang', '$nama_reservasi', '$tgl_bermain', '$ruang', '$jml_orang', '$tambahan', '$w_kedatangan', '$durasi', '$whatsapp', '$nama_file_baru', '$catatan', '$status', $val_voucher)";
        
        if (mysqli_query($koneksi, $query)) {
            if (!empty($kode_voucher)) {
                $waktu_sekarang = date('Y-m-d H:i:s');
                mysqli_query($koneksi, "UPDATE customer_vouchers SET status_pakai = 'Sudah Digunakan', tanggal_pakai = '$waktu_sekarang' WHERE kode_voucher = '$kode_voucher'");
            }
            $sukses_kirim = true; 
        } else {
            $gagal_kirim = true;
            $pesan_error = "Gagal menyimpan ke database: " . mysqli_error($koneksi);
        }
    }
}

$sql_gambut  = "SELECT * FROM reservasi WHERE status NOT IN ('Masuk Ruangan', 'Ditolak') AND cabang = 'Gambut' ORDER BY id_reservasi DESC";
$data_gambut = mysqli_query($koneksi, $sql_gambut);

$sql_beruntung  = "SELECT * FROM reservasi WHERE status NOT IN ('Masuk Ruangan', 'Ditolak') AND cabang = 'Beruntung' ORDER BY id_reservasi DESC";
$data_beruntung = mysqli_query($koneksi, $sql_beruntung);

$query_harga = mysqli_query($koneksi, "SELECT p_paket, durasi, daf_harga FROM daftar_harga");
$array_harga = [];
while ($h = mysqli_fetch_assoc($query_harga)) {
    $array_harga[] = $h;
}

$hari_id = array('Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu');
$bulan_id = array('01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Form Reservasi - Araya Gamestation</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" type="image/png" href="img/logonew.webp">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#28a745">
    <link rel="apple-touch-icon" href="img/logonew.webp">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://arayaofficial.site/">
    <meta property="og:title" content="Araya Gamestation - Reservasi Araya Gamestation">
    <meta property="og:description" content="Form Reservasi Araya Gamestation">
    <meta property="og:image" content="https://arayaofficial.site/img/bgaraya.webp">
    <meta property="og:image:type" content="image/webp">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://arayaofficial.site/">
    <meta property="twitter:title" content="Araya Gamestation - Reservasi Araya Gamestation">
    <meta property="twitter:description" content="Form Reservasi Araya Gamestation">
    <meta property="twitter:image" content="https://arayaofficial.site/img/bgaraya.webp">

    <style>
        body { 
            font-family: 'Nunito', Arial, sans-serif;
            background: url('img/bgaraya.jpeg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            min-height: 100vh;
        }
        .bg-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0, 0, 0, 0.65); z-index: 0; 
        }
        .container-custom {
            position: relative; z-index: 1; max-width: 1100px; padding-top: 30px; padding-bottom: 30px;
        }
        .dropdown-booking { 
            background: rgba(255, 255, 255, 0.95); border: 1px solid #ccc; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.3); 
        }
        .dropdown-summary { 
            padding: 10px 15px; font-weight: bold; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; background: #f8f9fa; user-select: none; list-style: none; color: #222; 
        }
        .gambut-theme { border-bottom: 2px solid #007bff; }
        .beruntung-theme { border-bottom: 2px solid #198754; }
        .dropdown-summary::after { content: '\25BC'; margin-left: auto; font-size: 0.7rem; transition: transform 0.2s; }
        .gambut-theme::after { color: #007bff; }
        .beruntung-theme::after { color: #198754; }
        .dropdown-booking[open] .dropdown-summary::after { transform: rotate(180deg); }
        .booking-list-wrapper { padding: 10px; max-height: 200px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; background: #fff; }
        .booking-card { background: #fdfdfd; border: 1px solid #eef0f2; border-radius: 6px; padding: 8px; font-size: 0.75rem; }
        .card-gambut { border-left: 4px solid #007bff; }
        .card-beruntung { border-left: 4px solid #198754; }
        .client-name { font-weight: bold; text-transform: capitalize; color: #222; margin-bottom: 4px; }
        
        .form-container { 
            background: rgba(255, 255, 255, 0.96); padding: 18px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); 
        }
        .form-label-sm {
            font-size: 0.72rem; font-weight: 700; color: #444; margin-bottom: 2px;
        }
        .price-box-wrapper { background: #e8f5e9; padding: 8px; border-radius: 5px; border-left: 4px solid #198754; }
        .price-display { font-size: 1.15rem; font-weight: 800; color: #1b5e20; }
        .voucher-status-text { font-size: 0.68rem; font-weight: bold; }
        .file-info-sm { font-size: 0.65rem; }
    </style>
</head>
<body>

<div class="bg-overlay"></div>

<div class="container container-custom">
    <div class="row g-3 justify-content-center align-items-start">
        
        <div class="col-12 col-md-5 d-flex flex-column gap-2">
            <details id="gambutDropdown" class="dropdown-booking" open> 
                <summary class="dropdown-summary gambut-theme">Reservasi Aktif Gambut</summary>
                <div class="booking-list-wrapper">
                    <?php 
                    if ($data_gambut && mysqli_num_rows($data_gambut) > 0) {
                        while ($row = mysqli_fetch_assoc($data_gambut)) {
                            $tanggal_asal = $row['tgl_bermain']; 
                            $timestamp    = strtotime($tanggal_asal);
                            $nama_hari    = isset($hari_id[date('l', $timestamp)]) ? $hari_id[date('l', $timestamp)] : date('l', $timestamp);
                            $tgl          = date('d', $timestamp);
                            $nama_bulan   = isset($bulan_id[date('m', $timestamp)]) ? $bulan_id[date('m', $timestamp)] : date('m', $timestamp);
                            $tahun        = date('Y', $timestamp);
                            $tanggal_indo = "$nama_hari, $tgl $nama_bulan $tahun";
                    ?>
                            <div class="booking-card card-gambut">
                                <div class="client-name"><?= htmlspecialchars($row['nama_reservasi']); ?></div>
                                <div class="row g-1 text-secondary">
                                    <div class="col-6"><i class="fas fa-map-pin"></i> <?= htmlspecialchars($row['ruang']); ?></div>
                                    <div class="col-6"><i class="fas fa-calendar"></i> <?= htmlspecialchars($tanggal_indo); ?></div>
                                    <div class="col-6"><i class="fas fa-clock"></i> <?= htmlspecialchars($row['w_kedatangan']); ?></div>
                                    <div class="col-6"><i class="fas fa-user"></i> <?= htmlspecialchars($row['jml_orang']); ?> Org (<?= htmlspecialchars($row['durasi']); ?>)</div>
                                </div>
                            </div>
                    <?php 
                        }
                    } else {
                        echo "<p class='text-center text-muted my-3 file-info-sm'>Belum ada bookingan aktif di Gambut.</p>";
                    }
                    ?>
                </div>
            </details>

            <details id="beruntungDropdown" class="dropdown-booking" open> 
                <summary class="dropdown-summary beruntung-theme">Reservasi Aktif Beruntung</summary>
                <div class="booking-list-wrapper">
                    <?php 
                    if ($data_beruntung && mysqli_num_rows($data_beruntung) > 0) {
                        while ($row = mysqli_fetch_assoc($data_beruntung)) {
                            $tanggal_asal = $row['tgl_bermain']; 
                            $timestamp    = strtotime($tanggal_asal);
                            $nama_hari    = isset($hari_id[date('l', $timestamp)]) ? $hari_id[date('l', $timestamp)] : date('l', $timestamp);
                            $tgl          = date('d', $timestamp);
                            $nama_bulan   = isset($bulan_id[date('m', $timestamp)]) ? $bulan_id[date('m', $timestamp)] : date('m', $timestamp);
                            $tahun        = date('Y', $timestamp);
                            $tanggal_indo = "$nama_hari, $tgl $nama_bulan $tahun";
                    ?>
                            <div class="booking-card card-beruntung">
                                <div class="client-name"><?= htmlspecialchars($row['nama_reservasi']); ?></div>
                                <div class="row g-1 text-secondary">
                                    <div class="col-6"><i class="fas fa-map-pin"></i> <?= htmlspecialchars($row['ruang']); ?></div>
                                    <div class="col-6"><i class="fas fa-calendar"></i> <?= htmlspecialchars($tanggal_indo); ?></div>
                                    <div class="col-6"><i class="fas fa-clock"></i> <?= htmlspecialchars($row['w_kedatangan']); ?></div>
                                    <div class="col-6"><i class="fas fa-user"></i> <?= htmlspecialchars($row['jml_orang']); ?> Org (<?= htmlspecialchars($row['durasi']); ?>)</div>
                                </div>
                            </div>
                    <?php 
                        }
                    } else {
                        echo "<p class='text-center text-muted my-3 file-info-sm'>Belum ada bookingan aktif di Beruntung.</p>";
                    }
                    ?>
                </div>
            </details>
        </div>

        <div class="col-12 col-md-7">
            <div class="form-container">
                <h5 class="text-center fw-bold text-uppercase border-bottom border-success border-2 pb-1 mb-1">Form Reservasi</h5>
                <p class="text-center fw-bold text-muted file-info-sm mb-3 mt-3">Isi formulir secara berurutan agar seluruh fitur dapat terbuka</p>
                
                <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="cabang" class="form-label-sm">Pilih Cabang</label>
                            <select id="cabang" name="cabang" class="form-select form-select-sm" required>
                                <option value="" disabled selected>-- Pilih Cabang --</option>
                                <option value="Gambut">Gambut</option>
                                <option value="Beruntung">Beruntung</option>
                            </select>
                        </div>
                        
                        <div class="col-6">
                            <label for="nama" class="form-label-sm">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama_lengkap" class="form-control form-control-sm" placeholder="Nama lengkap" required>
                        </div>

                        <div class="col-6">
                            <label for="tanggal" class="form-label-sm">Tanggal Bermain</label>
                            <input type="date" id="tanggal" name="tanggal_bermain" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-6">
                            <label for="waktu" class="form-label-sm">Waktu Kedatangan</label>
                            <div class="input-group input-group-sm">
                                <input type="time" id="waktu" name="waktu_kedatangan" class="form-control" required>
                                <span class="input-group-text fw-bold text-dark bg-light" style="font-size:0.75rem;">WITA</span>
                            </div>
                        </div>

                        <div class="col-6">
                            <label for="ruang" class="form-label-sm">Pilih Ruang</label>
                            <select id="ruang" name="ruang" class="form-select form-select-sm" required>
                                <option value="" disabled selected>-- Pilih Ruang --</option>
                            </select>
                        </div>

                        <div class="col-6" id="grup_durasi">
                            <label for="durasi" class="form-label-sm">Durasi Bermain</label>
                            <select id="durasi" name="durasi" class="form-select form-select-sm" required>
                                <option value="" disabled selected>-- Pilih Durasi --</option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label for="jumlah_orang" class="form-label-sm">Jumlah Orang</label>
                            <input type="number" id="jumlah_orang" name="jumlah_orang" class="form-control form-control-sm" min="1" placeholder="Contoh: 2" required>
                            <div id="info_jumlah_orang" class="file-info-sm text-danger mt-1" style="display:none;"></div>
                        </div>

                        <div class="col-6" id="grup_mikrofon" style="display: none;">
                            <label for="mikrofon" class="form-label-sm">Mikrofon</label>
                            <select id="mikrofon" name="mikrofon" class="form-select form-select-sm">
                                <option value="tidak" selected>Tidak</option>
                                <option value="1 mic">1 Mic</option>
                                <option value="2 mic">2 Mic</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="whatsapp" class="form-label-sm">Nomor WhatsApp</label>
                            <input type="number" id="whatsapp" name="whatsapp" class="form-control form-control-sm" placeholder="08xxx">
                        </div>   
                        
                        <div class="col-12">
                            <label for="kode_voucher" class="form-label-sm">Kode Voucher (Opsional)</label>
                            <input type="text" id="kode_voucher" name="kode_voucher" class="form-control form-control-sm text-uppercase" placeholder="Masukkan kode voucher">
                        </div>

                        <div class="col-12">
                            <label class="form-label-sm">Total Biaya Sewa</label>
                            <div class="price-box-wrapper">
                                <div id="total_harga_display" class="price-display">Rp 0</div>
                                <span id="voucher_status" class="voucher-status-text text-muted">Silakan pilih ruang & durasi bermain.</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label-sm">Download QRIS</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="img/qrisgambut.jpeg" download="QRIS_Araya_Gambut.jpeg" class="btn btn-sm btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-1" style="font-size:0.75rem;">
                                        <i class="fas fa-qrcode"></i> QRIS Gambut
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="img/qrisberuntung.jpeg" download="QRIS_Araya_Beruntung.jpeg" class="btn btn-sm btn-success w-100 fw-bold d-flex align-items-center justify-content-center gap-1" style="font-size:0.75rem;">
                                        <i class="fas fa-qrcode"></i> QRIS Beruntung
                                    </a>
                                </div>
                            </div>
                            <div class="file-info-sm text-danger mt-1 fw-bold">Klik tombol untuk mengunduh QRIS cabang terkait.</div>
                        </div>

                        <div class="col-12">
                            <label for="dp" class="form-label-sm">Bukti DP / Lunas</label>
                            <input type="file" id="dp" name="bukti_dp" class="form-control form-control-sm" accept=".jpg, .jpeg, .png, .pdf, .docx">
                            <div class="file-info-sm text-danger mt-1">Minimal DP 50% / Lunas. Jika bayar di tempat harap konfirmasi admin.</div>
                        </div>

                        <div class="col-12">
                            <label for="catatan" class="form-label-sm">Catatan Tambahan</label>
                            <textarea id="catatan" name="catatan" class="form-control form-control-sm" rows="2" placeholder="Tulis catatan jika ada..."></textarea>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-sm btn-success w-100 fw-bold py-2 shadow-sm text-uppercase" style="font-size: 0.85rem;">Kirim Reservasi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const masterHarga = <?php echo json_encode($array_harga); ?>;
    
    const selectCabang = document.getElementById('cabang');
    const selectRuang = document.getElementById('ruang');
    const selectDurasi = document.getElementById('durasi');
    const inputJumlahOrang = document.getElementById('jumlah_orang');
    const inputVoucher = document.getElementById('kode_voucher');
    const displayTotal = document.getElementById('total_harga_display');
    const statusVoucher = document.getElementById('voucher_status');
    const infoOrang = document.getElementById('info_jumlah_orang');
    const selectMikrofon = document.getElementById('mikrofon');
    const grupMikrofon = document.getElementById('grup_mikrofon');

    let nominalDiskon = 0;
    let hargaStandar = 0;

    const opsiGambut = [
        { value: "", text: "-- Pilih Ruang --", disabled: true, selected: true },
        { value: "Reguler", text: "Reguler" },
        { value: "VIP 1", text: "VIP 1" },
        { value: "VIP 2", text: "VIP 2" },
        { value: "Premiere 1", text: "Premiere 1" },
        { value: "Premiere 2", text: "Premiere 2" }
    ];

    const opsiBeruntung = [
        { value: "", text: "-- Pilih Ruang --", disabled: true, selected: true },
        { value: "Reguler", text: "Reguler" },
        { value: "VIP 1", text: "VIP 1" },
        { value: "VIP 2", text: "VIP 2" }
    ];

    const opsiDurasi = [
        { value: "", text: "-- Pilih Durasi --", disabled: true, selected: true },
        { value: "1 Jam", text: "1 Jam" },
        { value: "2 Jam", text: "2 Jam" },
        { value: "3 Jam", text: "3 Jam" },
        { value: "4 Jam", text: "4 Jam" },
        { value: "5 Jam", text: "5 Jam" }
    ];

    function updateOpsiRuang() {
        const cabang = selectCabang.value;
        selectRuang.innerHTML = "";
        let opsi = [];

        if (cabang === "Gambut") {
            opsi = opsiGambut;
        } else if (cabang === "Beruntung") {
            opsi = opsiBeruntung;
        } else {
            opsi = [{ value: "", text: "-- Pilih Ruang --", disabled: true, selected: true }];
        }

        opsi.forEach(opt => {
            const option = document.createElement("option");
            option.value = opt.value;
            option.text = opt.text;
            if (opt.disabled) option.disabled = true;
            if (opt.selected) option.selected = true;
            selectRuang.appendChild(option);
        });
        
        updateOpsiDurasi(); 
    }

    function updateOpsiDurasi() {
        const ruang = selectRuang.value;
        selectDurasi.innerHTML = "";
        let opsi = [{ value: "", text: "-- Pilih Durasi --", disabled: true, selected: true }];
        
        if (ruang && ruang !== "") {
            opsi = opsiDurasi;
        }

        opsi.forEach(opt => {
            const option = document.createElement("option");
            option.value = opt.value;
            option.text = opt.text;
            if (opt.disabled) option.disabled = true;
            if (opt.selected) option.selected = true;
            selectDurasi.appendChild(option);
        });
        
        hitungBiayaRealtime();
    }

    function hitungBiayaRealtime() {
        const ruangTerpilih = selectRuang.value;
        const durasiTerpilih = selectDurasi.value; 
        const jumlahOrangTerpilih = parseInt(inputJumlahOrang.value) || 0;

        if (ruangTerpilih === "Premiere 1" || ruangTerpilih === "Premiere 2") {
            grupMikrofon.style.display = "block";
        } else {
            grupMikrofon.style.display = "none";
            selectMikrofon.value = "tidak";
        }

        let biayaMikrofon = 0;
        if (selectMikrofon.value === "1 mic") {
            biayaMikrofon = 10000;
        } else if (selectMikrofon.value === "2 mic") {
            biayaMikrofon = 20000;
        }

        let maxOrang = 0;
        let adaMaksimal = false;

        if (!ruangTerpilih || ruangTerpilih === "Reguler") {
            infoOrang.style.display = "none";
            infoOrang.innerText = "";
            adaMaksimal = false;
        } else if (ruangTerpilih === "Premiere 1") {
            infoOrang.style.display = "block";
            infoOrang.innerText = "Maks. 7 orang, tambahan orang dikenakan biaya";
            maxOrang = 7;
            adaMaksimal = true;
        } else if (ruangTerpilih === "Premiere 2") {
            infoOrang.style.display = "block";
            infoOrang.innerText = "Maks. 5 orang, tambahan orang dikenakan biaya";
            maxOrang = 5;
            adaMaksimal = true;
        } else {
            infoOrang.style.display = "block";
            infoOrang.innerText = "Maks. 4 orang, tambahan orang dikenakan biaya";
            maxOrang = 4;
            adaMaksimal = true;
        }

        if (!ruangTerpilih || !durasiTerpilih) {
            displayTotal.innerText = "Rp 0";
            statusVoucher.innerText = "Silahkan pilih ruang & durasi bermain.";
            statusVoucher.className = "voucher-status-text text-muted";
            return;
        }

        let paketKey = "";
        if (ruangTerpilih === "Reguler") {
            paketKey = "Reguler";
        } else if (ruangTerpilih === "VIP 1" || ruangTerpilih === "VIP 2") {
            paketKey = "Vip";
        } else if (ruangTerpilih === "Premiere 1") {
            paketKey = "Premiere 1";
        } else if (ruangTerpilih === "Premiere 2") {
            paketKey = "Premiere 2";
        }

        const jam = parseInt(durasiTerpilih); 

        if (paketKey === "Premiere 1" || paketKey === "Premiere 2") {
            const data1Jam = masterHarga.find(item => item.p_paket.toLowerCase() === paketKey.toLowerCase() && item.durasi.toLowerCase() === "1 jam");
            const data2Jam = masterHarga.find(item => item.p_paket.toLowerCase() === paketKey.toLowerCase() && item.durasi.toLowerCase() === "2 jam");
            
            let harga1Jam = data1Jam ? parseInt(data1Jam.daf_harga) : 0;
            let harga2Jam = data2Jam ? parseInt(data2Jam.daf_harga) : 0;

            if (jam === 1) {
                hargaStandar = harga1Jam;
            } else if (jam === 2) {
                hargaStandar = harga2Jam;
            } else if (jam > 2) {
                hargaStandar = harga2Jam + (harga1Jam * (jam - 2));
            }
        } else {
            const dataMatch = masterHarga.find(item => 
                item.p_paket.toLowerCase() === paketKey.toLowerCase() && 
                item.durasi.toLowerCase() === durasiTerpilih.toLowerCase()
            );

            if (dataMatch) {
                hargaStandar = parseInt(dataMatch.daf_harga);
            } else {
                const dataBasis1Jam = masterHarga.find(item => 
                    item.p_paket.toLowerCase() === paketKey.toLowerCase() && 
                    item.durasi.toLowerCase() === "1 jam"
                );
                if (dataBasis1Jam) {
                    hargaStandar = parseInt(dataBasis1Jam.daf_harga) * jam;
                } else {
                    hargaStandar = 0;
                }
            }
        }

        let biayaTambahanOrang = 0;
        if (adaMaksimal && jumlahOrangTerpilih > maxOrang) {
            const kelebihanOrang = jumlahOrangTerpilih - maxOrang;
            biayaTambahanOrang = kelebihanOrang * 10000;
        }

        let totalSebelumDiskon = hargaStandar + biayaTambahanOrang + biayaMikrofon;
        let hargaAkhir = totalSebelumDiskon - nominalDiskon;
        if (hargaAkhir < 0) hargaAkhir = 0; 

        displayTotal.innerText = "Rp " + hargaAkhir.toLocaleString('id-ID');

        let teksStatus = "";
        if (biayaTambahanOrang > 0) {
            teksStatus += `Tambahan Orang: Rp ${biayaTambahanOrang.toLocaleString('id-ID')} | `;
        }
        if (biayaMikrofon > 0) {
            teksStatus += `Mikrofon: Rp ${biayaMikrofon.toLocaleString('id-ID')} | `;
        }

        if (nominalDiskon > 0) {
            teksStatus += `Potongan Voucher: -Rp ${nominalDiskon.toLocaleString('id-ID')} | Total Normal: Rp ${totalSebelumDiskon.toLocaleString('id-ID')}`;
            statusVoucher.className = "voucher-status-text text-success";
        } else {
            teksStatus += "Tarif Standar.";
            statusVoucher.className = "voucher-status-text text-muted";
        }
        
        statusVoucher.innerText = teksStatus;
    }

    selectCabang.addEventListener('change', updateOpsiRuang);
    selectRuang.addEventListener('change', function() {
        updateOpsiDurasi();
        hitungBiayaRealtime();
    });
    selectDurasi.addEventListener('change', hitungBiayaRealtime);
    inputJumlahOrang.addEventListener('input', hitungBiayaRealtime);
    selectMikrofon.addEventListener('change', hitungBiayaRealtime);

    inputVoucher.addEventListener('input', function() {
        const kode = this.value.trim();
        
        if (kode === "") {
            nominalDiskon = 0;
            hitungBiayaRealtime();
            return;
        }

        fetch(`?aksi=cek_voucher&kode=${encodeURIComponent(kode)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'sukses') {
                    nominalDiskon = parseInt(data.diskon);
                } else {
                    nominalDiskon = 0; 
                }
                hitungBiayaRealtime(); 
            })
            .catch(error => {
                console.error(error);
                nominalDiskon = 0;
                hitungBiayaRealtime();
            });
    });

    function sesuaikanDropdown() {
        const dropGambut = document.getElementById('gambutDropdown');
        const dropBeruntung = document.getElementById('beruntungDropdown');
        if (window.innerWidth <= 768) {
            dropGambut.removeAttribute('open');
            dropBeruntung.removeAttribute('open');
        } else {
            dropGambut.setAttribute('open', '');
            dropBeruntung.setAttribute('open', '');
        }
    }
    window.addEventListener('DOMContentLoaded', sesuaikanDropdown);
</script>

<?php if ($sukses_kirim): ?>
    <script>
        Swal.fire({
          icon: "success",
          title: "Reservasi Berhasil Terkirim!",
          showConfirmButton: true,
          confirmButtonColor: "#198754"
        }).then(function() {
            window.location = 'reservasi-araya';
        });
    </script>
<?php endif; ?>

<?php if ($gagal_kirim): ?>
    <script>
        Swal.fire({
          icon: "error",
          title: "Gagal Mengirim Data",
          text: "<?= $pesan_error; ?>",
          confirmButtonColor: "#dc3545"
        });
    </script>
<?php endif; ?>

<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/sw.js')
        .then(registration => {
          console.log('ServiceWorker', registration.scope);
        })
        .catch(err => {
          console.log('ServiceWorker', err);
        });
    });
  }
</script>

</body>
</html>