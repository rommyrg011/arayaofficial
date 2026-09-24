<?php 
require_once __DIR__ . '/../function.php'; 

$sukses_selesai = false;

if (isset($_POST['proses_selesai_kurir'])) {
    $id_rental_selesai = intval($_POST['id_rental_konfirmasi']);
    
    if (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] === 0) {
        $nama_file_mentah = $_FILES['foto_bukti']['name'];
        $tmp_file         = $_FILES['foto_bukti']['tmp_name'];
        
        $ekstensi = pathinfo($nama_file_mentah, PATHINFO_EXTENSION);
        $nama_file_baru = "bukti_" . $id_rental_selesai . "_" . time() . "." . $ekstensi;
        $target_direktori = "../img/";
        
        if (!is_dir($target_direktori)) {
            mkdir($target_direktori, 0755, true);
        }
        
        $path_tujuan = $target_direktori . $nama_file_baru;
        
        if (move_uploaded_file($tmp_file, $path_tujuan)) {
            $query_update = "UPDATE rental SET status = 'Selesai', img = '$nama_file_baru', tgl_selesai = NOW() WHERE id_rental = $id_rental_selesai";
            
            if (mysqli_query($koneksi, $query_update)) {
                $sukses_selesai = true;
            } else {
                echo "<script>alert('Gagal memperbarui database: " . mysqli_error($koneksi) . "');</script>";
            }
        } else {
            echo "<script>alert('Gagal memindahkan file foto ke direktori tujuan.');</script>";
        }
    } else {
        echo "<script>alert('Harap ambil foto bukti pengantaran terlebih dahulu sebelum menyelesaikan tugas.');</script>";
    }
}
?>

<style>
    #tableKurirServerSide tbody tr.selected {
        background-color: rgba(78, 115, 223, 0.15) !important;
        cursor: pointer;
    }
    #tableKurirServerSide tbody tr {
        cursor: pointer;
    }
    .preview-kamera-box {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        background: #fdfdfd;
        cursor: pointer;
    }
    .preview-kamera-box:hover {
        border-color: #28a745;
    }
</style>

<?php 
include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-center text-primary" style="font-size:25px;">
                Tugas Pengantaran Kurir
            </h6>
            <div class="text-center my-2">
                <button type="button" class="btn btn-sm btn-success font-weight-bold px-4" id="btnPemicuModalSelesai" disabled>
                    <i class="fas fa-check-double"></i> Tugas Selesai
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tableKurirServerSide" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th class="text-center" style="width: 10%;">Navigasi</th>
                            <th>Nama Perental</th>
                            <th>Durasi Sewa</th>
                            <th class="text-center">Jaminan</th>
                            <th>WhatsApp</th>
                            <th>Alamat Tujuan</th>
                            <th class="text-center" style="width: 12%;">Status</th>
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

<div class="modal fade" id="modalSelesaiKurir" tabindex="-1" role="dialog" aria-labelledby="modalSelesaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold" id="modalSelesaiLabel"><i class="fas fa-camera"></i> Ambil Foto Bukti</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="POST" id="formSelesaiKurir" enctype="multipart/form-data">
                <div class="modal-body">
                    <p class="text-dark small">Ambil foto unit PlayStation di rumah perental sebagai bukti validasi pengantaran.</p>
                    
                    <input type="hidden" name="id_rental_konfirmasi" id="id_rental_konfirmasi">
                    <input type="hidden" name="proses_selesai_kurir" value="1">
                    
                    <div class="preview-kamera-box" onclick="picKamera()">
                        <i class="fas fa-camera-retro fa-3x text-muted mb-2" id="iconKamera"></i>
                        <div class="font-weight-bold text-secondary small" id="teksKamera">Klik di sini untuk buka Kamera HP</div>
                        
                        <input type="file" name="foto_bukti" id="foto_bukti" accept="image/*" capture="environment" style="display:none;" required>
                        
                        <img id="imagePreview" src="#" alt="Pratinjau Foto" style="display:none; max-width:100%; height:auto; border-radius:6px; margin-top:5px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold" id="btnSubmitFinal" disabled>
                        <i class="fas fa-cloud-upload-alt"></i> Upload & Selesaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
<?php 
include 'template/footer.php'; 
include 'template/script.php'; 
?>
<script>
function picKamera() {
    document.getElementById('foto_bukti').click();
}

$(document).ready(function() {
    var selectedId = null;
    var selectedRowData = null;

    var table = $('#tableKurirServerSide').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/data_kurir_serverside.php",
            "type": "POST"
        },
        "columns": [
            { "data": 0, "orderable": false }, 
            { "data": 7, "orderable": false },  
            { "data": 1 },                    
            { "data": 2 },                    
            { "data": 3, "orderable": false }, 
            { "data": 4, "orderable": false }, 
            { "data": 5 },                    
            { "data": 6, "orderable": false },
            { "data": 8, "orderable": false } 
        ],
        "language": {
            "processing": "<div class='text-center'><div class='spinner-border text-primary' role='status'></div><br>Sedang memuat data tugas...</div>",
            "emptyTable": "Tidak ada tugas pengantaran untuk Anda.",
            "zeroRecords": "Data yang dicari tidak ditemukan."
        }
    });

    $('#tableKurirServerSide tbody').on('click', 'tr', function() {
        if ($(this).hasClass('dataTables_empty') || $(this).find('td').hasClass('dataTables_empty')) {
            return;
        }

        var currentStatus = $(this).attr('data-status');

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            selectedId = null;
            selectedRowData = null;
            $('#btnPemicuModalSelesai').prop('disabled', true);
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            
            selectedId = $(this).attr('data-id');
            selectedRowData = {
                id: selectedId,
                nama: $(this).attr('data-nama'),
                status: currentStatus
            };
            
            $('#id_rental_konfirmasi').val(selectedId);
            
            if(currentStatus === 'Selesai') {
                $('#btnPemicuModalSelesai').prop('disabled', true);
            } else {
                $('#btnPemicuModalSelesai').prop('disabled', false);
            }
        }
    });

    $('#btnPemicuModalSelesai').click(function() {
        if(selectedId && selectedRowData) {
            $('#foto_bukti').val('');
            $('#imagePreview').hide().attr('src', '#');
            $('#iconKamera').show();
            $('#teksKamera').text('Klik di sini untuk buka Kamera HP');
            $('#btnSubmitFinal').prop('disabled', true);
            
            $('#modalSelesaiKurir').modal('show');
        }
    });

    $('#foto_bukti').change(function() {
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#iconKamera').hide();
                $('#teksKamera').text('Foto Berhasil Diambil! Klik lagi untuk ganti.');
                $('#imagePreview').attr('src', e.target.result).show();
                $('#btnSubmitFinal').prop('disabled', false);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    });
});
</script>

<?php if ($sukses_selesai): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Tugas Diselesaikan!',
            text: 'Status pengantaran dan foto bukti berhasil disimpan.',
            confirmButtonColor: '#28a745'
        }).then(function() {
            window.location = window.location.pathname;
        });
    </script>
<?php endif; ?>

</body>
</html>