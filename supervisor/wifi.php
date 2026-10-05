<?php 
require_once __DIR__ . '/../function.php'; 

if (isset($_GET['hapus_id'])) {
    $id = $_GET['hapus_id'];
    mysqli_query($koneksi, "DELETE FROM pengeluaran WHERE id_pengeluaran = '$id'");
    
    $_SESSION['notif'] = "Data Berhasil Dihapus";
    header("Location: wifi");
    exit();
}

if (isset($_POST['simpanPengeluaran'])) {
    $id = $_POST['id_pengeluaran'];
    $tanggal = $_POST['tanggal'];
    $cabang = $_POST['cabang'];
    $nama_toko = $_POST['nama_toko'];
    $total_bersih = $_POST['total_bersih'];
    $level = $_POST['level'];
    $pendapatan = "";
    $potongan = "";

    if (empty($id)) {
        mysqli_query($koneksi, "INSERT INTO pengeluaran (tanggal, cabang, nama_staff_ruko, pendapatan, potongan, total_bersih, level) VALUES ('$tanggal', '$cabang', '$nama_toko', '$pendapatan', '$potongan', '$total_bersih', '$level')");
        $_SESSION['notif'] = "Data Berhasil Disimpan";
    } else {
        mysqli_query($koneksi, "UPDATE pengeluaran SET tanggal='$tanggal', cabang='$cabang', nama_staff_ruko='$nama_toko', total_bersih='$total_bersih', level='$level' WHERE id_pengeluaran='$id'");
        $_SESSION['notif'] = "Data Berhasil Diubah";
    }
    
    header("Location: wifi");
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
            <h6 class="m-0 font-weight-bold" style="font-size:25px;">Wifi</h6>
            <div class="mt-3">
                <button class="btn btn-primary btn-sm" id="btnTambah"><i class="fas fa-plus"></i> Tambah</button>
                <button class="btn btn-warning btn-sm" id="btnEdit" disabled><i class="fas fa-edit"></i> Edit</button>
                <button class="btn btn-danger btn-sm" id="btnHapus" disabled><i class="fas fa-trash"></i> Hapus</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="dataTable" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Cabang</th>
                            <th>Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPengeluaran" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formPengeluaran" action="" method="POST" autocomplete="off">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"></h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_pengeluaran" id="id_pengeluaran">
                    <input type="hidden" name="level" id="level" value="wifi">
                    
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" id="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label>Cabang</label>
                        <select class="form-control" name="cabang" id="cabang" required>
                            <option value="" hidden>-- Pilih Cabang --</option>
                            <option value="gambut">Gambut</option>
                            <option value="beruntung">Beruntung</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Pengeluaran</label>
                        <input type="text" class="form-control" name="nama_toko" id="nama_toko" required>
                    </div>
                    <div class="form-group">
                        <label>Total Pengeluaran</label>
                        <input type="text" class="form-control rupiah-input" name="total_bersih" id="total_bersih" placeholder="Rp. 0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" name="simpanPengeluaran" class="btn btn-primary">Simpan</button>
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
            "url": "ajax/kwifi.php",
            "type": "POST"
        },
        "columns": [
            { "data": "no", "className": "text-center" },
            { "data": "tanggal", "className": "text-center" },
            { "data": "cabang" },
            { "data": "total_bersih", "className": "font-weight-bold text-success" }
        ],
        "createdRow": function(row, data) {
            $(row).attr('data-id', data.id_pengeluaran);
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
            selectedId = data.id_pengeluaran;
            selectedData = data;
            $('#btnEdit, #btnHapus').prop('disabled', false);
        }
    });

    $('#btnTambah').click(function() {
        $('#modalLabel').text('Tambah Data Pengeluaran');
        $('#formPengeluaran')[0].reset();
        $('#id_pengeluaran').val('');
        $('#cabang').val('');
        $('#level').val('wifi');
        $('#modalPengeluaran').modal('show');
    });

    $('#btnEdit').click(function() {
        if(selectedId && selectedData) {
            $('#modalLabel').text('Edit Data Pengeluaran');
            $('#id_pengeluaran').val(selectedData.id_pengeluaran);
            $('#tanggal').val(selectedData.tanggal_raw);
            if(selectedData.cabang !== undefined && selectedData.cabang !== null) {
                $('#cabang').val(selectedData.cabang.toLowerCase());
            } else {
                $('#cabang').val('');
            }
            $('#nama_toko').val(selectedData.nama_staff_ruko);
            $('#total_bersih').val(selectedData.total_bersih);
            $('#level').val('wifi');
            $('#modalPengeluaran').modal('show');
        }
    });

    $('#btnHapus').click(function() {
        if(selectedId) {
            if(confirm('Hapus data pengeluaran ' + selectedData.nama_staff_ruko + '?')) {
                window.location.href = 'wifi?hapus_id=' + selectedId;
            }
        }
    });

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    $('.rupiah-input').on('keyup', function() {
        $(this).val(formatRupiah($(this).val(), 'Rp. '));
    });

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 2000);
});
</script>