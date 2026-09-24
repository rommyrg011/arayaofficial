<?php 
require_once __DIR__ . '/../function.php'; 

include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>
<div class="container-fluid">

    <?php if(isset($_SESSION['notif'])): ?>
        <div class="alert alert-success alert-dismissible fade show alert-fixed">
            <?= $_SESSION['notif']; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php unset($_SESSION['notif']); ?>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 text-center">
            <h6 class="m-0 font-weight-bold" style="font-size:25px;">Manajemen Pengguna</h6>
            <div class="mt-3">
                <button class="btn btn-primary btn-sm" id="btnTambah"><i class="fas fa-plus"></i> Tambah</button>
                <button class="btn btn-warning btn-sm" id="btnEdit" disabled><i class="fas fa-edit"></i> Edit</button>
                <button class="btn btn-danger btn-sm" id="btnHapus" disabled><i class="fas fa-trash"></i> Hapus</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan</th>
                            <th>WhatsApp</th>
                            <th>Username</th>
                            <th width="10%">Foto</th>
                            <th>Level</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPengguna" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formPengguna" action="proses_pengguna.php" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"></h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_user" id="id_user">
                    
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" required>
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <select class="form-control" name="jabatan" id="jabatan">
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="asisten manager">Asisten Manager</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="operator">Operator</option>  
                            <option value="owner">Owner</option>  
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. WhatsApp</label>
                        <input type="text" class="form-control" name="no_wa" id="no_wa" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Isi password">
                        <small class="text-muted id-edit-notif" style="display:none;">*Biarkan kosong jika tidak ingin mengubah password</small>
                    </div>
                    <div class="form-group">
                        <label>Foto Profil (Opsional)</label>
                        <input type="file" class="form-control-file" name="images" id="images" accept="image/*">
                        <div id="preview_foto" class="mt-2"></div>
                    </div>
                    <div class="form-group">
                        <label>Level</label>
                        <select class="form-control" name="level" id="level" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="admin">Admin</option>
                            <option value="reservasi">Reservasi</option>
                            <option value="rental">Rental</option>
                            <option value="karyawan">Karyawan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" name="simpanPengguna" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php 
include 'template/footer.php'; 
include 'template/script.php'; 
?>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script>
$(document).ready(function() {
    var selectedId = null;
    var selectedData = null;

    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/kpengguna.php",
            "type": "POST",
            "error": function(xhr) {
                console.error(xhr.responseText);
                alert("Gagal memuat data. Cek Console (F12) untuk detail.");
            }
        },
        "columns": [
            { "data": "no", "className": "text-center align-middle" },
            { "data": "nama_lengkap", "className": "align-middle" },
            { "data": "jabatan", "className": "align-middle" },
            { "data": "no_wa", "className": "text-center align-middle" },
            { "data": "username", "className": "align-middle" },
            { "data": "images", "className": "text-center align-middle", "orderable": false },
            { "data": "level", "className": "text-center align-middle" }
        ],
        "createdRow": function(row, data) {
            $(row).attr('data-id', data.id_user);
        }
    });

    $('#dataTable tbody').on('click', 'tr', function() {
        var data = table.row(this).data();
        if (!data) return;

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            selectedId = null; 
            selectedData = null;
            $('#btnEdit, #btnHapus').prop('disabled', true);
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            selectedId = data.id_user;
            selectedData = data;
            $('#btnEdit, #btnHapus').prop('disabled', false);
        }
    });

    $('#btnTambah').click(function() {
        $('#modalLabel').text('Tambah Pengguna');
        $('#formPengguna')[0].reset();
        $('#id_user').val('');
        $('#jabatan').val('');
        $('#password').prop('required', true);
        $('.id-edit-notif').hide();
        $('#preview_foto').html('');
        $('#modalPengguna').modal('show');
    });

    function getExtension(filename) {
        return filename.split('.').pop().toLowerCase();
    }

    $('#btnEdit').click(function() {
        if(selectedId && selectedData) {
            $('#modalLabel').text('Edit Pengguna');
            $('#formPengguna')[0].reset();
            
            $('#id_user').val(selectedData.id_user); 
            $('#nama_lengkap').val(selectedData.nama_lengkap);
            $('#jabatan').val(selectedData.jabatan_raw);
            $('#no_wa').val(selectedData.no_wa);
            $('#username').val(selectedData.username);
            $('#level').val(selectedData.level_raw);
            $('#password').prop('required', false);
            $('.id-edit-notif').show();
            
            var fName = selectedData.foto_nama ? selectedData.foto_nama.trim() : '';
            var allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            
            if(fName !== '' && fName !== '0' && allowedExtensions.includes(getExtension(fName))) {
                var timestamp = new Date().getTime();
                $('#preview_foto').html('<img src="../img/' + fName + '?t=' + timestamp + '" width="70" class="img-thumbnail">');
            } else {
                $('#preview_foto').html('<span class="text-muted">Tidak ada foto</span>');
            }
            
            $('#modalPengguna').modal('show');
        }
    });

    $('#btnHapus').click(function() {
        if(selectedId && selectedData) {
            if(confirm('Hapus pengguna bernama ' + selectedData.nama_lengkap + ' beserta file fotonya secara permanen?')) {
                window.location.href = 'proses_pengguna.php?hapus_idUser=' + selectedId;
            }
        }
    });
});

window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 2000);
</script>