<?php 
require_once __DIR__ . '/../function.php'; 

if (isset($_POST['proses_selesai_rental'])) {
    $id_rental_update = intval($_POST['id_rental_selesai']);
    
    $query_update = "UPDATE rental SET status = 'Selesai' WHERE id_rental = $id_rental_update";
    if (mysqli_query($koneksi, $query_update)) {
        $sukses_aksi = "Selesai";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
    }
}

if (isset($_POST['proses_tugas_kurir'])) {
    $id_rental_kurir = intval($_POST['id_rental']);
    $id_kurir_pilih   = intval($_POST['id_kurir']);

    $query_kurir = "UPDATE rental SET id_kurir = $id_kurir_pilih, status = 'Proses' WHERE id_rental = $id_rental_kurir";
    
    if (mysqli_query($koneksi, $query_kurir)) {
        $sukses_aksi = "Kurir";
    } else {
        echo "<script>alert('Gagal Menugaskan Kurir: " . mysqli_error($koneksi) . "');</script>";
    }
}

if (isset($_POST['proses_hapus_rental'])) {
    $id_rental_hapus = intval($_POST['id_rental_hapus']);
    
    $query_hapus = "DELETE FROM rental WHERE id_rental = $id_rental_hapus";
    if (mysqli_query($koneksi, $query_hapus)) {
        $sukses_aksi = "Hapus";
    } else {
        echo "<script>alert('Gagal Menghapus: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<?php 
include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-center" style="font-size:25px;">Direktori Penyewa</h6>
            <div class="text-center my-2">
                <button class="btn btn-warning btn-sm btn-mobile btn-xs" id="btnKurir" disabled>
                    <i class="fas fa-shipping-fast"></i> Kurir
                </button>
                <button class="btn btn-danger btn-sm btn-mobile btn-xs ml-1" id="btnHapus" disabled>
                    <i class="fas fa-times"></i> Pembatalan
                </button>
                <input type="hidden" id="id_rental_selesai">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th>Nama Perental</th>
                            <th>Durasi Sewa</th>
                            <th>Jaminan</th>
                            <th>WhatsApp</th>
                            <th>Alamat Lengkap</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<form id="formHapus" method="POST" style="display:none;">
    <input type="hidden" name="proses_hapus_rental" value="1">
    <input type="hidden" name="id_rental_hapus" id="id_rental_hapus">
</form>

<div class="modal fade" id="modalKurir" tabindex="-1" role="dialog" aria-labelledby="modalKurirLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="" method="POST" id="formTunjukKurirManual">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKurirLabel"><i class="fas fa-shipping-fast text-warning"></i> Atur Kurir Pengantaran</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_rental" id="mdl_id_rental">
                    <input type="hidden" name="proses_tugas_kurir" value="1">
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Perental</label>
                        <input type="text" class="form-control" id="mdl_nama_perental" readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Alamat Pengiriman</label>
                        <textarea class="form-control" id="mdl_alamat_lengkap" rows="3" readonly></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Pilih Personel Kurir / Staf</label>
                        <select class="form-control text-dark font-weight-bold" name="id_kurir" id="mdl_id_kurir" required>
                            <option value="" disabled selected>-- Pilih Kurir Tugas --</option>
                            <?php 
                            $tampil_kurir = mysqli_query($koneksi, "SELECT * FROM user WHERE level = 'karyawan'");
                            while($k = mysqli_fetch_assoc($tampil_kurir)){
                                echo "<option value='".$k['id_user']."'>".$k['nama_lengkap']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning font-weight-bold text-dark">Konfirmasi Tugas</button>
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
    var selectedId = null;
    var selectedRowData = null;

    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    var table = $('#dataTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "ajax": {
            "url": "ajax/ajax_direktoriPenyewa.php",
            "type": "POST"
        },
        "columns": [
            { "data": "no", "className": "text-center" },
            { "data": "nama_perental", "className": "font-weight-bold text-capitalize" },
            { "data": "durasi_sewa" },
            { "data": "jaminan" },
            { "data": "wa" },
            { "data": "alamat_lengkap" },
            { "data": "status" },
            { "data": "catatan" }
        ],
        "createdRow": function(row, data, dataIndex) {
            $(row).attr('data-id', data.id_rental);
            $(row).attr('data-nama', data.nama_mentah);
            $(row).attr('data-alamat', data.alamat_mentah);
        },
        "drawCallback": function() {
            $('#btnSelesai, #btnKurir, #btnHapus').prop('disabled', true);
            selectedId = null;
            selectedRowData = null;
        }
    });

    $('#dataTable tbody').on('click', 'tr', function() {
        if ($(this).find('td').hasClass('dataTables_empty')) {
            return;
        }

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            selectedId = null;
            selectedRowData = null;
            $('#btnSelesai, #btnKurir, #btnHapus').prop('disabled', true);
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            
            selectedId = $(this).attr('data-id');
            selectedRowData = {
                id: $(this).attr('data-id'),
                nama: $(this).attr('data-nama'),
                alamat: $(this).attr('data-alamat')
            };
            
            $('#btnSelesai, #btnKurir, #btnHapus').prop('disabled', false);
            $('#id_rental_selesai').val(selectedId);
        }
    });

    $('#btnKurir').click(function() {
        if(selectedId && selectedRowData) {
            $('#mdl_id_rental').val(selectedRowData.id);
            $('#mdl_nama_perental').val(selectedRowData.nama);
            $('#mdl_alamat_lengkap').val(selectedRowData.alamat);
            $('#modalKurir').modal('show');
        }
    });

    $('#btnHapus').click(function() {
        if(selectedId) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data rental yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#id_rental_hapus').val(selectedId);
                    $('#formHapus').submit();
                }
            });
        }
    });
});
</script>

<?php if (isset($sukses_aksi)): ?>
    <script>
        var pesan = "";
        <?php if ($sukses_aksi == 'Selesai'): ?>
            pesan = "Data rental telah diselesaikan!";
        <?php elseif ($sukses_aksi == 'Kurir'): ?>
            pesan = "Kurir berhasil ditugaskan & Status menjadi Proses!";
        <?php elseif ($sukses_aksi == 'Hapus'): ?>
            pesan = "Data rental berhasil dihapus!";
        <?php endif; ?>
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: pesan,
            confirmButtonColor: '#28a745'
        }).then(function() {
            window.location = window.location.pathname;
        });
    </script>
<?php endif; ?>
</body>
</html>