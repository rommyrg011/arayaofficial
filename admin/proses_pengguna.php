<?php
session_start();
require_once __DIR__ . '/../function.php'; 

if (isset($_POST['simpanPengguna'])) {
    $id_user      = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $jabatan      = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $no_wa        = mysqli_real_escape_string($koneksi, $_POST['no_wa']);
    $username     = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password     = $_POST['password'];
    $level        = mysqli_real_escape_string($koneksi, $_POST['level']);

    $nama_file_baru = "";
    if (isset($_FILES['images']['name']) && $_FILES['images']['name'] != "") {
        $filename  = $_FILES['images']['name'];
        $ext       = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($ext, $allowed)) {
            $nama_file_baru = time() . '_' . uniqid() . '.webp';
            $target_dir     = "../img/";

            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $source_file = $_FILES['images']['tmp_name'];
            $target_path = $target_dir . $nama_file_baru;
            $img = null;

            if ($ext == 'jpg' || $ext == 'jpeg') {
                $img = imagecreatefromjpeg($source_file);
            } elseif ($ext == 'png') {
                $img = imagecreatefrompng($source_file);
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
            } elseif ($ext == 'gif') {
                $img = imagecreatefromgif($source_file);
                imagepalettetotruecolor($img);
            } elseif ($ext == 'webp') {
                $img = imagecreatefromwebp($source_file);
            }

            if ($img !== null) {
                imagewebp($img, $target_path, 80);
                imagedestroy($img);
            } else {
                $nama_file_baru = "";
            }
        }
    }

    if (empty($id_user)) {
        $password_fix = mysqli_real_escape_string($koneksi, $password);
        $foto_insert  = ($nama_file_baru != "") ? $nama_file_baru : "";

        $query = "INSERT INTO user (nama_lengkap, jabatan, no_wa, username, password, images, level) 
                  VALUES ('$nama_lengkap', '$jabatan', '$no_wa', '$username', '$password_fix', '$foto_insert', '$level')";
        
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['notif'] = "Data pengguna berhasil ditambahkan!";
        } else {
            $_SESSION['notif'] = "Gagal menambah data: " . mysqli_error($koneksi);
        }
    } else {
        if (!empty($password)) {
            $pass_query = ", password = '" . mysqli_real_escape_string($koneksi, $password) . "'";
        } else {
            $pass_query = "";
        }

        if ($nama_file_baru != "") {
            $cek_lama = mysqli_query($koneksi, "SELECT images FROM user WHERE id_user = '$id_user'");
            $data_lama = mysqli_fetch_assoc($cek_lama);
            $foto_lama = trim($data_lama['images']);
            $ext_lama = strtolower(pathinfo($foto_lama, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (!empty($foto_lama) && $foto_lama !== '0' && in_array($ext_lama, $allowed_ext)) {
                $path_lama = "../img/" . $foto_lama;
                if (file_exists($path_lama)) {
                    @unlink($path_lama); 
                }
            }
            $foto_query = ", images = '$nama_file_baru'";
        } else {
            $foto_query = "";
        }

        $query = "UPDATE user SET 
                    nama_lengkap = '$nama_lengkap', 
                    jabatan = '$jabatan',
                    no_wa = '$no_wa', 
                    username = '$username', 
                    level = '$level'
                    $pass_query 
                    $foto_query 
                  WHERE id_user = '$id_user'";

        if (mysqli_query($koneksi, $query)) {
            $_SESSION['notif'] = "Data pengguna berhasil diperbarui!";
        } else {
            $_SESSION['notif'] = "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    }

    header("Location: pengguna.php");
    exit();
}

if (isset($_GET['hapus_idUser'])) {
    $id_user = mysqli_real_escape_string($koneksi, $_GET['hapus_idUser']);

    $cek_foto = mysqli_query($koneksi, "SELECT images FROM user WHERE id_user = '$id_user'");
    if ($cek_foto && mysqli_num_rows($cek_foto) > 0) {
        $data = mysqli_fetch_assoc($cek_foto);
        $foto_hapus = trim($data['images']);
        $ext_hapus = strtolower(pathinfo($foto_hapus, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!empty($foto_hapus) && $foto_hapus !== '0' && in_array($ext_hapus, $allowed_ext)) {
            $path_hapus = "../img/" . $foto_hapus;
            if (file_exists($path_hapus)) {
                @unlink($path_hapus); 
            }
        }
    }

    $query_hapus = "DELETE FROM user WHERE id_user = '$id_user'";
    if (mysqli_query($koneksi, $query_hapus)) {
        $_SESSION['notif'] = "Data pengguna dan file foto berhasil dihapus!";
    } else {
        $_SESSION['notif'] = "Gagal menghapus data: " . mysqli_error($koneksi);
    }

    header("Location: pengguna.php");
    exit();
}