<?php
require_once __DIR__ . '/../function.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = intval($_POST['id_user']);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $no_wa = mysqli_real_escape_string($koneksi, $_POST['no_wa']);

    $queryOld = mysqli_query($koneksi, "SELECT images FROM user WHERE id_user = $id_user");
    $dataOld = mysqli_fetch_assoc($queryOld);
    $oldImage = isset($dataOld['images']) ? $dataOld['images'] : '';
    
    $newFileName = null;

    if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['images']['tmp_name'];
        $fileName = $_FILES['images']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = 'profile_' . $id_user . '_' . time() . '.webp';
            $uploadFileDir = __DIR__ . '/../img/';
            $dest_path = $uploadFileDir . $newFileName;

            switch ($fileExtension) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($fileTmpPath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($fileTmpPath);
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($fileTmpPath);
                    break;
                case 'webp':
                    $image = imagecreatefromwebp($fileTmpPath);
                    break;
                default:
                    $image = false;
            }

            if ($image !== false) {
                if (imagewebp($image, $dest_path, 85)) {
                    imagedestroy($image);

                    if (!empty($oldImage) && $oldImage !== '0' && $oldImage !== '8') {
                        $oldFilePath = $uploadFileDir . $oldImage;
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }

                    $queryUpdate = "UPDATE user SET nama_lengkap = '$nama_lengkap', no_wa = '$no_wa', images = '$newFileName' WHERE id_user = $id_user";
                } else {
                    $newFileName = null;
                    $queryUpdate = "UPDATE user SET nama_lengkap = '$nama_lengkap', no_wa = '$no_wa' WHERE id_user = $id_user";
                }
            } else {
                $newFileName = null;
                $queryUpdate = "UPDATE user SET nama_lengkap = '$nama_lengkap', no_wa = '$no_wa' WHERE id_user = $id_user";
            }
        } else {
            $queryUpdate = "UPDATE user SET nama_lengkap = '$nama_lengkap', no_wa = '$no_wa' WHERE id_user = $id_user";
        }
    } else {
        $queryUpdate = "UPDATE user SET nama_lengkap = '$nama_lengkap', no_wa = '$no_wa' WHERE id_user = $id_user";
    }

    if (mysqli_query($koneksi, $queryUpdate)) {
        $_SESSION['nama'] = $nama_lengkap;
        
        if ($newFileName !== null) {
            $_SESSION['images'] = $newFileName;
        }

        header("Location: datadiri?status=success");
    } else {
        header("Location: datadiri?status=failed");
    }
    exit;
} else {
    header("Location: datadiri");
    exit;
}