<?php 
require_once __DIR__ . '/../function.php';  
include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php';

$id_user_login = 0;
if (isset($_SESSION['id_kurir'])) {
    $id_user_login = intval($_SESSION['id_kurir']);
} elseif (isset($_SESSION['id_user'])) {
    $id_user_login = intval($_SESSION['id_user']);
} elseif (isset($_SESSION['id'])) {
    $id_user_login = intval($_SESSION['id']);
}

$queryUser = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $id_user_login");
$dataUser = mysqli_fetch_assoc($queryUser);

$nama_lengkap = isset($dataUser['nama_lengkap']) ? htmlspecialchars($dataUser['nama_lengkap']) : '-';
$no_wa        = isset($dataUser['no_wa']) ? htmlspecialchars($dataUser['no_wa']) : '-';
$username     = isset($dataUser['username']) ? htmlspecialchars($dataUser['username']) : '-';
$jabatan      = (!empty($dataUser['jabatan'])) ? htmlspecialchars($dataUser['jabatan']) : 'Karyawan';
$images       = isset($dataUser['images']) ? htmlspecialchars($dataUser['images']) : '';

$avatar = (!empty($images) && $images != '0' && $images != '8') ? '../img/' . $images : 'https://via.placeholder.com/150';
?>

<style>
    #dataTable tbody tr {
        cursor: pointer;
    }
    #dataTable tbody tr.selected {
        background-color: rgba(204, 184, 180)!important;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex flex-column flex-sm-row align-items-center text-center text-sm-left">
                        <div class="mb-3 mb-sm-0 mr-sm-4 flex-shrink-0">
                            <img src="<?= $avatar; ?>" alt="Foto Profil" class="img-profile rounded-circle shadow-sm" style="width: 90px; height: 90px; object-fit: cover; border: 3px solid #162c53;">
                        </div>
                        <div class="flex-grow-1 min-width-0 w-100 mb-3 mb-sm-0">
                            <h4 class="font-weight-bold text-dark mb-1 text-truncate"><?= $nama_lengkap; ?></h4>
                            <span class="badge badge-warning px-3 py-1 mb-2 text-uppercase font-weight-bold" style="background-color: #cfa239; color: #fff; font-size: 11px;"><?= $jabatan; ?></span>
                            <p class="text-muted m-0 text-truncate" style="font-size: 14px;"><i class="fab fa-whatsapp text-success mr-1"></i> <?= $no_wa; ?></p>
                            <p class="text-muted m-0 text-truncate" style="font-size: 14px;"><i class="fas fa-user-circle mr-1"></i> <?= $username; ?></p>
                        </div>
                        <div class="flex-shrink-0 mt-2 mt-sm-0 w-sm-auto text-center">
                            <button type="button" class="btn btn-sm btn-primary shadow-sm btn-block btn-sm-inline" data-toggle="modal" data-target="#modalEditProfil">
                                <i class="fas fa-edit mb-1 mb-sm-0"></i> Edit Profil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-id-card"></i> ID Card Preview</h6>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    
                    <div id="idCardPrint" style="width: 280px; height: 440px; background-color: #ffffff; border-radius: 16px; position: relative; overflow: hidden; font-family: 'Arial', sans-serif; box-shadow: 0 8px 24px rgba(0,0,0,0.12); border: 1px solid #e3e6f0; -webkit-border-radius: 16px;">
                        
                        <div style="position: absolute; top: 12px; left: 0; width: 100%; display: flex; justify-content: center; z-index: 10;">
                            <div style="width: 40px; height: 6px; background-color: #e3e6f0; border-radius: 10px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);"></div>
                        </div>

                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 155px; background: linear-gradient(135deg, #162c53 0%, #0d1e3d 100%); border-bottom: 4px solid #cfa239; z-index: 1;"></div>
                        
                        <div style="position: absolute; top: 28px; left: 0; width: 100%; z-index: 3; text-align: center; display: flex; flex-direction: column; align-items: center;">
                            <img src="../img/logonew.webp" alt="Logo" style="height: 60px; width: auto; object-fit: contain; margin-bottom: 8px;">
                            <h4 style="color: #ffffff; font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">Araya Gamestation</h4>
                        </div>

                        <div style="position: absolute; top: 12px; left: 0; width: 100%; display: flex; justify-content: center; z-index: 10;">
                            <div style="width: 40px; height: 6px; background-color: #e3e6f0; border-radius: 10px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);"></div>
                        </div>

                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 155px; background: linear-gradient(135deg, #162c53 0%, #0d1e3d 100%); border-bottom: 4px solid #cfa239; z-index: 1;"></div>
                        
                        <div style="position: absolute; top: 28px; left: 0; width: 100%; z-index: 3; text-align: center; display: flex; flex-direction: column; align-items: center;">
                            <img src="../img/logonew.webp" alt="Logo" style="height: 60px; width: auto; object-fit: contain; margin-bottom: 8px;">
                            <h4 style="color: #ffffff; font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">Araya Gamestation</h4>
                        </div>

                        <div style="position: absolute; top: 125px; left: 0; width: 100%; z-index: 3; display: flex; justify-content: center;">
                            <div style="width: 140px; height: 140px; border-radius: 50%; border: 5px solid #ffffff; display: flex; justify-content: center; align-items: center; background-color: #ffffff; box-shadow: 0 5px 15px rgba(0,0,0,0.15); overflow: hidden;">
                                <img src="<?= $avatar; ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            </div>
                        </div>

                        <div style="position: absolute; top: 28px; left: 0; width: 100%; z-index: 3; text-align: center; padding: 0 15px; box-sizing: border-box; margin-top: 252px;">
                            <h5 style="color: #162c53; font-weight: 800; margin: 0 0 10px 0; font-size: 19px; letter-spacing: 0.5px; text-transform: uppercase; line-height: 1.2; word-wrap: break-word; text-align: center; width: 100%;"><?= $nama_lengkap; ?></h5>
                            <div style="display: inline-block; background-color: #cfa239; color: #ffffff; font-weight: 700; padding: 6px 20px; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; border-radius: 20px; box-shadow: 0 2px 6px rgba(207,162,57,0.3); margin: 0 auto; text-align: center;"><?= $jabatan; ?></div>
                        </div>

                        <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 50px; background-color: #f8f9fc; border-top: 1px solid #eaecf4; z-index: 1; display: flex; align-items: center; justify-content: center;">
                            <small style="color: #858796; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 100%; display: block;">Identity Card &bull; Araya Employee</small>
                        </div>

                        <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 5px; background-color: #162c53; z-index: 3;"></div>

                    </div>

                    <div class="px-4 mt-4">
                        <button onclick="cetakGambar()" class="btn btn-sm btn-info btn-block shadow-sm">
                            <i class="fas fa-print mr-1"></i> Cetak ID Card
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditProfil" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="proses_datadiri.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_user" value="<?= $id_user_login; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Edit Data Diri</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= $nama_lengkap; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">WhatsApp</label>
                        <input type="text" name="no_wa" class="form-control" value="<?= $no_wa; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Foto Profil</label>
                        <div class="custom-file">
                            <input type="file" name="images" class="custom-file-input" id="customFile" accept="image/*">
                            <label class="custom-file-label" for="customFile">Pilih foto...</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php 
include 'template/footer.php'; 
include 'template/script.php'; 
?>

<script>
$(document).ready(function() {
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    Swal.fire({
        toast: true,
        position: "top-end",
        icon: "warning",
        title: "Profil anda berhasil diubah",
        showConfirmButton: false,
        timer: 3000,
        customClass: {
            popup: 'alert alert-warning'
        }
    });
    <?php endif; ?>
});

function cetakGambar() {
    var isiCard = document.getElementById('idCardPrint').outerHTML;
    var jendelaCetak = window.open('', '_blank', 'width=320,height=500');
    jendelaCetak.document.write('<!DOCTYPE html><html><head><title>Cetak ID Card</title>');
    jendelaCetak.document.write('<style>');
    jendelaCetak.document.write('@media print { @page { margin: 0; size: 280px 440px; } body { margin: 0; padding: 0; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; } }');
    jendelaCetak.document.write('body { margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background: #fff; }');
    jendelaCetak.document.write('<' + '/style></head><body>');
    jendelaCetak.document.write(isiCard);
    jendelaCetak.document.write('</body></html>');
    jendelaCetak.document.close();
    jendelaCetak.focus();
    
    setTimeout(function() {
        jendelaCetak.print();
        jendelaCetak.close();
    }, 500);
}
</script>
</body>
</html>