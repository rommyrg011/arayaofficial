<?php
session_start();

$koneksi = mysqli_connect('localhost', 'root', '', 'arayaofficial');

// Tambah & Edit Data kategori ruangan
if (isset($_POST['simpanKategoriRuangan'])) {
    $nama_ruangan = mysqli_real_escape_string($koneksi, $_POST['nama_ruangan']);
    $id_ruangan = $_POST['id_ruangan'];

    if (empty($id_ruangan)) {
        // Logika Tambah
        $query = mysqli_query($koneksi, "INSERT INTO kat_ruangan (nama_ruangan) VALUES ('$nama_ruangan')");
        if ($query) {
            $_SESSION['notif'] = "Data Berhasil Ditambahkan";
        } else {
            $_SESSION['notif_gagal'] = "Gagal Menambahkan Data";
        }
    } else {
        // Logika Edit
        $query = mysqli_query($koneksi, "UPDATE kat_ruangan SET nama_ruangan='$nama_ruangan' WHERE id_ruangan='$id_ruangan'");
        if ($query) {
            $_SESSION['notif'] = "Data Berhasil Diperbarui";
        } else {
            $_SESSION['notif_gagal'] = "Gagal Memperbarui Data";
        }
    }
    header('location: kategoriRuangan.php'); // Sesuaikan nama file halaman Anda
    exit();
}

// Hapus Data kategori ruangan
if (isset($_GET['hapus_idRuangan'])) {
    $id = $_GET['hapus_idRuangan'];
    $query = mysqli_query($koneksi, "DELETE FROM kat_ruangan WHERE id_ruangan='$id'");
    
    if ($query) {
        $_SESSION['notif'] = "Data Berhasil Dihapus";
    } else {
        $_SESSION['notif_gagal'] = "Gagal Menghapus Data";
    }
    header('location: kategoriRuangan.php');
    exit();
}

// simpan dan edit data kategori unit
if (isset($_POST['simpanKategoriUnit'])) {
    $nama_pembayaran = mysqli_real_escape_string($koneksi, $_POST['nama_pembayaran']);
    $id_unit = $_POST['id_unit'];

    if (empty($id_unit)) {
        $query = mysqli_query($koneksi, "INSERT INTO kat_unit (nama_pembayaran) VALUES ('$nama_pembayaran')");
        if ($query) {
            $_SESSION['notif'] = "Data Berhasil Ditambahkan";
        } else {
            $_SESSION['notif_gagal'] = "Gagal Menambahkan Data";
        }
    } else {
        // Logika Edit
        $query = mysqli_query($koneksi, "UPDATE kat_unit SET nama_pembayaran='$nama_pembayaran' WHERE id_unit='$id_unit'");
        if ($query) {
            $_SESSION['notif'] = "Data Berhasil Diperbarui";
        } else {
            $_SESSION['notif_gagal'] = "Gagal Memperbarui Data";
        }
    }
    header('location: kategoriUnit.php');
    exit();
}

// hapus data kategori unit
if (isset($_GET['hapus_idUnit'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus_idUnit']);
    if (mysqli_query($koneksi, "DELETE FROM kat_unit WHERE id_unit = '$id'")) {
        $_SESSION['notif'] = "Data berhasil dihapus!";
    } else {
        $_SESSION['notif_gagal'] = "Gagal hapus: " . mysqli_error($koneksi);
    }
    header("Location: kategoriUnit.php");
    exit();
}

// simpan dan edit data pembayaran
if (isset($_POST['simpanPembayaran'])) {
    $nama_pembayaran = mysqli_real_escape_string($koneksi, $_POST['nama_pembayaran']);
    $id_pembayaran = $_POST['id_pembayaran'];

    if (empty($id_pembayaran)) {
        $query = mysqli_query($koneksi, "INSERT INTO pembayaran (nama_pembayaran) VALUES ('$nama_pembayaran')");
        if ($query) {
            $_SESSION['notif'] = "Data Berhasil Ditambahkan";
        } else {
            $_SESSION['notif_gagal'] = "Gagal Menambahkan Data";
        }
    } else {
        // Logika Edit
        $query = mysqli_query($koneksi, "UPDATE pembayaran SET nama_pembayaran='$nama_pembayaran' WHERE id_pembayaran='$id_pembayaran'");
        if ($query) {
            $_SESSION['notif'] = "Data Berhasil Diperbarui";
        } else {
            $_SESSION['notif_gagal'] = "Gagal Memperbarui Data";
        }
    }
    header('location: pembayaran.php');
    exit();
}

// hapus data pembayaran
if (isset($_GET['hapus_idPembayaran'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus_idPembayaran']);
    if (mysqli_query($koneksi, "DELETE FROM pembayaran WHERE id_pembayaran = '$id'")) {
        $_SESSION['notif'] = "Data berhasil dihapus!";
    } else {
        $_SESSION['notif_gagal'] = "Gagal hapus: " . mysqli_error($koneksi);
    }
    header("Location: pembayaran.php");
    exit();
}

// simpan dan edit Daftar Harga
if (isset($_POST['simpanDaftarHarga'])) {
    $p_paket = mysqli_real_escape_string($koneksi, $_POST['p_paket']);
    $durasi = mysqli_real_escape_string($koneksi, $_POST['durasi']);
    $daf_harga = mysqli_real_escape_string($koneksi, $_POST['daf_harga']);
    $id_harga = $_POST['id_harga'];

    if (empty($id_harga)) {
        $query = mysqli_query($koneksi, "INSERT INTO daftar_harga (p_paket, durasi, daf_harga) VALUES ('$p_paket', '$durasi', '$daf_harga')");
        if ($query) {
            $_SESSION['notif'] = "Data Berhasil Ditambahkan";
        } else {
            $_SESSION['notif_gagal'] = "Gagal Menambahkan Data";
        }
    } else {
        // Logika Edit
        $query = mysqli_query($koneksi, "UPDATE daftar_harga SET p_paket='$p_paket', durasi='$durasi',daf_harga='$daf_harga' WHERE id_harga='$id_harga'");
        if ($query) {
            $_SESSION['notif'] = "Data Berhasil Diperbarui";
        } else {
            $_SESSION['notif_gagal'] = "Gagal Memperbarui Data";
        }
    }
    header('location: daftarHarga.php');
    exit();
}

// hapus Daftar Harga
if (isset($_GET['hapus_idDharga'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus_idDharga']);
    if (mysqli_query($koneksi, "DELETE FROM daftar_harga WHERE id_harga = '$id'")) {
        $_SESSION['notif'] = "Data berhasil dihapus!";
    } else {
        $_SESSION['notif_gagal'] = "Gagal hapus: " . mysqli_error($koneksi);
    }
    header("Location: daftarHarga.php");
    exit();
}

if (isset($_POST['simpanTarget'])) {
    $id_target = mysqli_real_escape_string($koneksi, $_POST['id_target']);
    $ncabang   = mysqli_real_escape_string($koneksi, $_POST['ncabang']);
    $shift     = mysqli_real_escape_string($koneksi, $_POST['shift']);
    
    $target_raw = $_POST['target'];
    $target     = preg_replace('/[^0-9]/', '', $target_raw);

    if (empty($id_target)) {
        $query = "INSERT INTO target (ncabang, shift, target) VALUES ('$ncabang', '$shift', '$target')";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['notif'] = "Data berhasil ditambahkan!";
        } else {
            $_SESSION['notif'] = "Gagal menambahkan data!";
        }
    } else {
        $query = "UPDATE target SET ncabang='$ncabang', shift='$shift', target='$target' WHERE id_target='$id_target'";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['notif'] = "Data berhasil diubah!";
        } else {
            $_SESSION['notif'] = "Gagal mengubah data!";
        }
    }
    
    header("Location: targetShift");
    exit();
}

if (isset($_GET['hapus_idTarget'])) {
    $id_target = mysqli_real_escape_string($koneksi, $_GET['hapus_idTarget']);
    
    $query = "DELETE FROM target WHERE id_target='$id_target'";
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['notif'] = "Data berhasil dihapus!";
    } else {
        $_SESSION['notif'] = "Gagal menghapus data!";
    }
    
    header("Location: targetShift");
    exit();
}

?>