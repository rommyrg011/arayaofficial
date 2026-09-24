<?php 
require_once __DIR__ . '/../function.php'; 

if (isset($_POST['prosesDaftarHarga'])) {
    $id_harga  = mysqli_real_escape_string($koneksi, $_POST['id_harga']);
    $cabang    = mysqli_real_escape_string($koneksi, $_POST['cabang']);
    $p_paket   = mysqli_real_escape_string($koneksi, $_POST['p_paket']);
    $durasi    = mysqli_real_escape_string($koneksi, $_POST['durasi']);
    $daf_harga = mysqli_real_escape_string($koneksi, $_POST['daf_harga']);

    if (empty($id_harga)) {
        $query = "INSERT INTO daftar_harga (cabang, p_paket, durasi, daf_harga) VALUES ('$cabang', '$p_paket', '$durasi', '$daf_harga')";
        mysqli_query($koneksi, $query);
        $_SESSION['notif'] = "Data berhasil ditambahkan!";
    } else {
        $query = "UPDATE daftar_harga SET cabang='$cabang', p_paket='$p_paket', durasi='$durasi', daf_harga='$daf_harga' WHERE id_harga='$id_harga'";
        mysqli_query($koneksi, $query);
        $_SESSION['notif'] = "Data berhasil diubah!";
    }
    header("Location: daftarHarga.php");
    exit();
}

if (isset($_GET['hapus_data_harga'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus_data_harga']);
    mysqli_query($koneksi, "DELETE FROM daftar_harga WHERE id_harga='$id'");
    $_SESSION['notif'] = "Data berhasil dihapus!";
    header("Location: daftarHarga.php");
    exit();
}

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
            <h6 class="m-0 font-weight-bold" style="font-size:25px;">Daftar Harga</h6>
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
                            <th>Cabang</th>
                            <th>Pilihan Paket</th>
                            <th>Durasi</th>
                            <th>Daftar Harga</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
        
<div class="modal fade" id="modalDafHarga" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formUnit" action="" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"></h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_harga" id="id_harga">
                    <div class="form-group">
                        <label>Cabang</label>
                        <select name="cabang" id="cabang" class="form-control">
                            <option value="Gambut">Gambut</option>
                            <option value="Beruntung">Beruntung</option>
                        </select>
                        <label>Pilihan Paket</label>
                        <select name="p_paket" id="p_paket" class="form-control">
                            <option value="Reguler">Reguler</option>
                            <option value="Paket">Paket</option>
                            <option value="Voucher">Voucher</option>
                            <option value="Vip">Vip</option>
                            <option value="Premiere 1">Premiere 1</option>
                            <option value="Premiere 2">Premiere 2</option>
                            <option value="Rental">Rental</option>
                        </select>
                        <label>Durasi</label>
                        <select name="durasi" id="durasi" class="form-control">
                            <option value="1 Jam">1 Jam</option>
                            <option value="2 Jam">2 Jam</option>
                            <option value="3 Jam">3 Jam</option>
                            <option value="4 Jam">4 Jam</option>
                            <option value="5 Jam">5 Jam</option>
                            <option value="10 Jam">10 Jam</option>
                            <option value="12 Jam">12 Jam</option>
                            <option value="24 Jam">24 Jam</option>
                            <option value="48 Jam">48 Jam</option>
                        </select>
                        <label>Harga</label>
                        <input type="text" class="form-control" name="daf_harga" id="daf_harga" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" name="prosesDaftarHarga" class="btn btn-primary">Simpan</button>
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
    var selectedNama = "";

    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/dHarga.php",
            "type": "POST"
        },
        "columns": [
            { "data": "no", "className": "text-center" },
            { "data": "cabang", "className": "text-center" },
            { "data": "p_paket", "className": "text-center" },
            { "data": "durasi", "className": "text-center" },
            { "data": "daf_harga", "className": "text-center" }
        ],
        "createdRow": function(row, data) {
            $(row).attr('data-id', data.id_harga);
        }
    });

    $('#dataTable tbody').on('click', 'tr', function() {
        var data = table.row(this).data();
        if (!data) return;

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            selectedId = null; 
            selectedNama = "";
            $('#btnEdit, #btnHapus').prop('disabled', true);
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            selectedId = data.id_harga;
            selectedNama = data.daf_harga;
            $('#btnEdit, #btnHapus').prop('disabled', false);
        }
    });

    $('#btnTambah').click(function() {
        $('#modalLabel').text('Tambah Daftar Harga');
        $('#formUnit')[0].reset();
        $('#id_harga').val('');
        $('#modalDafHarga').modal('show');
    });

    $('#btnEdit').click(function() {
        if(selectedId) {
            var data = table.row('.selected').data(); 
            $('#modalLabel').text('Edit Daftar Harga');
            $('#id_harga').val(selectedId); 
            $('#cabang').val(data.cabang);
            $('#p_paket').val(data.raw_paket); 
            $('#durasi').val(data.durasi);
            $('#daf_harga').val(data.raw_harga); 
            $('#modalDafHarga').modal('show');
        }
    });

    $('#btnHapus').click(function() {
        if(selectedId) {
            if(confirm('Hapus ' + selectedNama + '?')) {
                window.location.href = 'daftarHarga.php?hapus_data_harga=' + selectedId;
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