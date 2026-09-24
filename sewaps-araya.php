<?php 
require_once 'function.php';

$sukses_kirim = false;
$gagal_kirim = false;
$pesan_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_perental = mysqli_real_escape_string($koneksi, $_POST['nama_perental']);
    $durasi_sewa = mysqli_real_escape_string($koneksi, $_POST['durasi_sewa']); 
    $alamat_lengkap = mysqli_real_escape_string($koneksi, $_POST['alamat_lengkap']);
    $jaminan = mysqli_real_escape_string($koneksi, $_POST['jaminan']);
    
    $koordinat_mentah = $_POST['sharelok']; 
    if (!empty($koordinat_mentah)) {
        $link_maps_otomatis = "https://www.google.com/maps/dir/?api=1&destination=" . urlencode($koordinat_mentah);
    } else {
        $link_maps_otomatis = "";
    }
    $sharelok = mysqli_real_escape_string($koneksi, $link_maps_otomatis);

    $wa_input = $_POST['wa'];
    $wa_clean = preg_replace('/[^0-9]/', '', $wa_input);
    $wa = mysqli_real_escape_string($koneksi, $wa_clean);
    
    $catatan_input = trim($_POST['catatan']);
    if (empty($catatan_input)) {
        $catatan = "-";
    } else {
        $catatan = mysqli_real_escape_string($koneksi, $catatan_input);
    }
    
    $status_rental = "pending";

    $query = "INSERT INTO rental (nama_perental, durasi_sewa, alamat_lengkap, jaminan, sharelok, wa, catatan, status, img) 
              VALUES ('$nama_perental', '$durasi_sewa', '$alamat_lengkap', '$jaminan', '$sharelok', '$wa', '$catatan', '$status_rental', '')";
    
    if (mysqli_query($koneksi, $query)) {
        $sukses_kirim = true; 
    } else {
        $gagal_kirim = true;
        $pesan_error = "Gagal menyimpan ke database: " . mysqli_error($koneksi);
    }
}

$sql_booking = "SELECT * FROM rental WHERE status NOT IN ('selesai', 'ditolak') ORDER BY id_rental DESC";
$data_booking = mysqli_query($koneksi, $sql_booking);

$sql_harga = "SELECT * FROM daftar_harga WHERE p_paket IN ('Rental', 'TV') ORDER BY id_harga ASC";
$data_harga = mysqli_query($koneksi, $sql_harga);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="img/logoaraya.png">

    <meta name="title" content="Araya Gamestation - Sewa Playstation">
    <meta name="description" content="Form sewa playstation Araya Gamestation, sudah melayani antar - jemput">
    <meta name="theme-color" content="#28a745">
    <title>Form Sewa PS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" type="image/png" href="img/logonew.webp">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#28a745">
    <link rel="apple-touch-icon" href="img/logonew.webp">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://arayaofficial.site/">
    <meta property="og:title" content="Araya Gamestation - Sewa Playstation Araya Gamestation">
    <meta property="og:description" content="Form Sewa Playstation Araya Gamestation">
    <meta property="og:image" content="https://arayaofficial.site/img/bgaraya.webp">
    <meta property="og:image:type" content="image/webp">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://arayaofficial.site/">
    <meta property="twitter:title" content="Araya Gamestation - Sewa Playstation Araya Gamestation">
    <meta property="twitter:description" content="Form Sewa Playstation Araya Gamestation">
    <meta property="twitter:image" content="https://arayaofficial.site/img/bgaraya.webp">

    <style>
        body {
            position: relative;
            min-height: 100vh;
            background: url('img/bgaraya.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.65);
            z-index: -1;
        }

        /* Form semi-transparent background */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
        }

        .queue-container {
            max-height: 480px;
            overflow-y: auto;
        }

        .booking-card {
            border-left: 4px solid #28a745 !important;
        }
    </style>
</head>
<body>

<div class="bg-overlay"></div>

<div class="container py-4 py-md-5" style="z-index: 1;">
    <div class="row justify-content-center g-4">
        
        <div class="col-12 col-md-6 col-lg-5">
            <div class="accordion shadow-sm" id="accordionAntrian">
                <div class="accordion-item glass-card">
                    <h2 class="accordion-header" id="headingAntrian">
                        <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAntrian" aria-expanded="true" aria-controls="collapseAntrian">
                            <i class="fas fa-motorcycle me-2 text-success"></i> Antrian Delivery Aktif
                        </button>
                    </h2>
                    <div id="collapseAntrian" class="accordion-collapse collapse" aria-labelledby="headingAntrian" data-bs-parent="#accordionAntrian">
                        <div class="accordion-body queue-container bg-light">
                            <?php 
                            if ($data_booking && mysqli_num_rows($data_booking) > 0) {
                                while ($row = mysqli_fetch_assoc($data_booking)) {
                            ?>
                                    <div class="card booking-card mb-3 shadow-sm">
                                        <div class="card-body p-3">
                                            <h6 class="card-title fw-bold text-capitalize text-dark mb-2">
                                                <?= htmlspecialchars($row['nama_perental']); ?>
                                            </h6>
                                            <div class="d-flex flex-column gap-1" style="font-size: 0.85rem; color: #555;">
                                                <div><i class="fas fa-clock"></i> <?= htmlspecialchars($row['durasi_sewa']); ?></div>
                                                <div><i class="fas fa-map-pin"></i> <?= htmlspecialchars($row['alamat_lengkap']); ?></div>
                                                <div><i class="fas fa-truck"></i> <strong class="text-success"><?= ucfirst(htmlspecialchars($row['status'])); ?> Pengantaran</strong></div>
                                            </div>
                                        </div>
                                    </div>
                            <?php 
                                }
                            } else {
                                echo "<p class='text-center text-muted small py-4'>Belum ada rental delivery hari ini.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-5">
            <div class="card glass-card shadow-lg">
                <div class="card-body p-4">
                    <h5 class="text-center fw-bold text-uppercase border-bottom border-success border-2 pb-2 mb-4">
                        Form Sewa PS <br> Araya Gamestation
                    </h5>
                    
                    <form action="" method="POST" autocomplete="off">
                        
                        <div class="accordion mb-4" id="accordionInfo">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingHarga">
                                    <button class="accordion-button collapsed bg-info text-white fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHarga" aria-expanded="false" aria-controls="collapseHarga" style="font-size: 0.85rem;">
                                        DAFTAR HARGA SEWA
                                    </button>
                                </h2>
                                <div id="collapseHarga" class="accordion-collapse collapse" aria-labelledby="headingHarga" data-bs-parent="#accordionInfo">
                                    <div class="accordion-body bg-light" style="font-size: 0.85rem;">
                                        <ol class="mb-0 ps-3 fw-bold text-capitalize">
                                            <?php 
                                            if ($data_harga && mysqli_num_rows($data_harga) > 0) {
                                                while ($row_harga = mysqli_fetch_assoc($data_harga)) {
                                                    $harga_rp = "Rp. " . number_format($row_harga['daf_harga'], 0, ',', '.');
                                                    echo "<li>{$row_harga['p_paket']} {$row_harga['durasi']} : {$harga_rp}</li>";
                                                }
                                            } else {
                                                echo "<li>Pricelist belum tersedia.</li>";
                                            }
                                            ?>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item mt-2">
                                <h2 class="accordion-header" id="headingSnk">
                                    <button class="accordion-button collapsed bg-danger text-white fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSnk" aria-expanded="false" aria-controls="collapseSnk" style="font-size: 0.85rem;">
                                        SYARAT & KETENTUAN
                                    </button>
                                </h2>
                                <div id="collapseSnk" class="accordion-collapse collapse" aria-labelledby="headingSnk" data-bs-parent="#accordionInfo">
                                    <div class="accordion-body bg-light" style="font-size: 0.85rem;">
                                        <ol class="mb-0 ps-3">
                                            <li>JAMINAN KTP / SIM.</li>
                                            <li>SHARE LOKASI RUMAH.</li>
                                            <li>BERSEDIA BERFOTO SAAT PENGANTARAN.</li>
                                            <li>DILARANG KERAS MENGKONEKSIKAN PS DENGAN JARINGAN, MENGAKIBATKAN KEHILANGAN GAME.</li>
                                            <li>KEHILANGAN GAME DI UNIT, TANGGUNG JAWAB PENYEWA.</li>
                                            <li>KELALAIAN PENYEWA MERUSAK / TIDAK SENGAJA MERUSAK DI TANGGUNG JAWAB PENYEWA.</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-1">
                            <label for="nama_perental" class="form-label fw-bold small">Nama Perental</label>
                            <input type="text" class="form-control form-control-sm" id="nama_perental" name="nama_perental" placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="mb-1">
                            <label for="durasi_sewa" class="form-label fw-bold small">Durasi Sewa</label>
                            <select class="form-select form-select-sm" id="durasi_sewa" name="durasi_sewa" required>
                                <option value="" disabled selected>-- Pilih Durasi Sewa --</option>
                                <option value="12 Jam">12 Jam</option>
                                <option value="24 Jam">24 Jam</option>
                                <option value="48 Jam">48 Jam</option>
                                <option value="12 jam + Tv 32 Inc">12 jam + Tv 32 Inc</option>
                                <option value="24 jam + Tv 32 Inc">24 jam + Tv 32 Inc</option>
                                <option value="48 jam + Tv 32 Inc">48 jam + Tv 32 Inc</option>
                            </select>
                        </div>

                        <div class="mb-1">
                            <label for="jaminan" class="form-label fw-bold small">Jaminan</label>
                            <input type="text" class="form-control form-control-sm" id="jaminan" name="jaminan" placeholder="KTP / SIM" required>
                        </div>

                        <div class="mb-1">
                            <label for="wa" class="form-label fw-bold small">Nomor WhatsApp</label>
                            <input type="number" class="form-control form-control-sm" id="wa" name="wa" placeholder="Masukkan nomor whatsapp aktif" required>
                        </div>

                        <div class="mb-1">
                            <label for="sharelok" class="form-label fw-bold small">Lokasi Rumah (Lokasi Anda Terkini)</label>
                            <div class="input-group input-group-sm mb-1">
                                <input type="text" class="form-control" id="sharelok" name="sharelok" placeholder="Klik tombol ambil lokasi saat di lokasi" readonly required>
                                <button type="button" class="btn btn-success fw-bold" onclick="ambilLokasi()">Ambil Lokasi</button>
                            </div>
                            <div id="status_lokasi" class="form-text text-danger" style="font-size: 0.75rem;">Harap aktifkan akses lokasi Anda</div>
                        </div>

                        <div class="mb-1">
                            <label for="alamat_lengkap" class="form-label fw-bold small">Alamat Lengkap Rumah</label>
                            <textarea class="form-control form-control-sm" id="alamat_lengkap" name="alamat_lengkap" rows="2" placeholder="Alamat Lengkap Anda" required></textarea>
                        </div>

                        <div class="mb-1">
                            <label for="catatan" class="form-label fw-bold small">Catatan Tambahan</label>
                            <textarea class="form-control form-control-sm" id="catatan" name="catatan" rows="2" placeholder="Tulis catatan jika ada permintaan"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold mt-2">Kirim Data Sewa</button>
                        <div class="form-text text-danger text-center mt-2" style="font-size: 0.75rem;"><i class="fas fa-lock"></i> Data anda aman dan hanya digunakan untuk keperluan penyewaan.</div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Logika untuk menampilkan/menyembunyikan antrian berdasarkan ukuran layar saat dimuat
window.addEventListener('DOMContentLoaded', function() {
    const collapseAntrian = document.getElementById('collapseAntrian');
    if (window.innerWidth > 768) {
        collapseAntrian.classList.add('show');
    }
});

function ambilLokasi() {
    const status = document.getElementById('status_lokasi');
    const sharelokInput = document.getElementById('sharelok');

    if (!navigator.geolocation) {
        status.textContent = 'Browser Anda tidak mendukung fitur sharelok.';
        status.classList.replace('text-danger', 'text-danger');
        return;
    }

    status.textContent = 'Menghubungkan ke GPS...';
    status.classList.replace('text-danger', 'text-primary');

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            sharelokInput.value = lat + "," + lng;
            status.textContent = 'Lokasi anda berhasil dikunci.';
            status.classList.replace('text-primary', 'text-success');
        }, 
        function(error) {
            status.classList.remove('text-primary', 'text-success');
            status.classList.add('text-danger');
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    status.textContent = 'Akses ditolak. Harap izinkan akses lokasi di browser.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    status.textContent = 'Informasi lokasi tidak tersedia.';
                    break;
                case error.TIMEOUT:
                    status.textContent = 'Waktu permintaan lokasi habis.';
                    break;
                default:
                    status.textContent = 'Gagal memuat koordinat lokasi.';
            }
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}
</script>

<?php if ($sukses_kirim): ?>
    <script>
        Swal.fire({
          icon: "success",
          title: "Data Rental Disimpan!",
          text: "Terima kasih, data Anda berhasil masuk ke sistem.",
          confirmButtonColor: "#28a745"
        }).then(function() {
            window.location = ''; 
        });
    </script>
<?php endif; ?>

<?php if ($gagal_kirim): ?>
    <script>
        Swal.fire({
          icon: "error",
          title: "Gagal Mengirim Data",
          text: "<?= $pesan_error; ?>",
          confirmButtonColor: "#28a745"
        });
    </script>
<?php endif; ?>

</body>
</html>