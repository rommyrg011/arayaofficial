<?php
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reservasi = isset($_POST['id_reservasi']) ? intval($_POST['id_reservasi']) : 0;

    if ($id_reservasi === 0) {
        echo json_encode(['status' => false, 'message' => 'ID Reservasi tidak valid.']);
        exit;
    }

    // Periksa apakah ada file yang diunggah
    if (isset($_FILES['dp_berkas']) && $_FILES['dp_berkas']['error'] === UPLOAD_ERR_OK) {
        
        $fileTmpPath = $_FILES['dp_berkas']['tmp_name'];
        $fileName = $_FILES['dp_berkas']['name'];
        
        // Dapatkan ekstensi file asli
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Ekstensi input yang diizinkan sebelum dikonversi
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            
            // Siapkan nama file baru dengan ekstensi terpaksa .webp
            $rawFileName = pathinfo($fileName, PATHINFO_FILENAME);
            $cleanFileName = str_replace(' ', '_', $rawFileName);
            $newFileName = time() . '_' . $cleanFileName . '.webp';
            
            // Tentukan target direktori folder destinasi ke ../img
            $uploadFileDir = '../../img/';
            
            // Pastikan folder img ada
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $dest_path = $uploadFileDir . $newFileName;

            // Proses konversi gambar asli dari temporary path langsung ke format WebP tujuan
            if (konversiKeWebp($fileTmpPath, $dest_path, $fileExtension)) {
                
                // Ambil data file lama untuk dihapus dari server agar menghemat storage
                $queryGetLama = mysqli_query($koneksi, "SELECT dp FROM reservasi WHERE id_reservasi = $id_reservasi");
                if ($dataLama = mysqli_fetch_assoc($queryGetLama)) {
                    $fileLama = $uploadFileDir . $dataLama['dp'];
                    if (!empty($dataLama['dp']) && file_exists($fileLama)) {
                        unlink($fileLama); // Hapus gambar DP lama berformat apapun / .webp lama
                    }
                }

                // Update data nama file baru (.webp) ke database
                $queryUpdate = "UPDATE reservasi SET dp = '$newFileName' WHERE id_reservasi = $id_reservasi";
                
                if (mysqli_query($koneksi, $queryUpdate)) {
                    echo json_encode(['status' => true, 'message' => 'Berhasil mengonversi dan memperbarui berkas DP ke format WebP.']);
                } else {
                    // Jika query gagal, hapus file webp yang terlanjur dibuat agar tidak jadi sampah sampah di storage
                    if (file_exists($dest_path)) {
                        unlink($dest_path);
                    }
                    echo json_encode(['status' => false, 'message' => 'Gagal memperbarui database: ' . mysqli_error($koneksi)]);
                }

            } else {
                echo json_encode(['status' => false, 'message' => 'Gagal melakukan konversi gambar ke format WebP. Pastikan ekstensi GD library aktif.']);
            }

        } else {
            echo json_encode(['status' => false, 'message' => 'Format file tidak diizinkan. Hanya menerima JPG, JPEG, PNG, dan WebP.']);
        }
    } else {
        echo json_encode(['status' => false, 'message' => 'Tidak ada file yang diunggah atau terjadi error pada file.']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Metode request tidak valid.']);
}

/**
 * Fungsi pembantu untuk membaca resource gambar asal dan menyimpannya langsung menjadi WebP
 */
function konversiKeWebp($sourcePath, $destinationPath, $extension, $quality = 80) {
    // Buat resource gambar berdasarkan tipe file aslinya
    switch ($extension) {
        case 'jpeg':
        case 'jpg':
            $image = @imagecreatefromjpeg($sourcePath);
            break;
        case 'png':
            $image = @imagecreatefrompng($sourcePath);
            // Pertahankan transparansi bila file aslinya PNG
            if ($image) {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }
            break;
        case 'webp':
            $image = @imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    // Jika gagal memuat resource gambar
    if (!$image) {
        return false;
    }

    // Ubah dan simpan ke tujuan dengan format WebP, kualitas bawaan 80% (seimbang antara ketajaman dan ukuran file)
    $proses = imagewebp($image, $destinationPath, $quality);
    
    // Hapus resource gambar dari memori setelah selesai digunakan
    imagedestroy($image);

    return $proses;
}
?>