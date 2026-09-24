<?php
// Memanggil koneksi dari function.php
require_once 'function.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form dan membersihkannya
    $nama   = $koneksi->real_escape_string($_POST['nama']);
    $email  = $koneksi->real_escape_string($_POST['email']);
    $rating = (int)$_POST['rating'];
    $pesan  = $koneksi->real_escape_string($_POST['pesan']);

    // Validasi sederhana
    if (!empty($nama) && !empty($rating) && !empty($pesan)) {
        // Query untuk memasukkan data
        $sql = "INSERT INTO feedback_pelanggan (nama_pelanggan, email, rating, pesan) 
                VALUES ('$nama', '$email', $rating, '$pesan')";
        
        if ($koneksi->query($sql) === TRUE) {
            // Berhasil, kembali ke halaman utama
            echo "<script>
                    alert('Terima kasih atas feedback Anda!');
                    window.location.href = './';
                  </script>";
        } else {
            // Error query
            echo "<script>
                    alert('Terjadi kesalahan saat mengirim feedback.');
                    window.history.back();
                  </script>";
        }
    } else {
        // Jika data kosong
        echo "<script>
                alert('Mohon isi semua kolom yang wajib diisi.');
                window.history.back();
              </script>";
    }
} else {
    // Jika diakses langsung tanpa submit form
    header("Location: ./");
    exit();
}

// Menutup koneksi
$koneksi->close();
?>