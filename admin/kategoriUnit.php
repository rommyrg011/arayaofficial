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
                            <h6 class="m-0 font-weight-bold" style="font-size:25px;">Kategori Unit</h6>
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
                                            <th>Kategori Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

    <div class="modal fade" id="modalUnit" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="formUnit" action="function.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel"></h5>
                        <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_unit" id="id_unit">
                        <div class="form-group">
                            <label>Nama Unit</label>
                            <input type="text" class="form-control" name="nama_unit" id="nama_unit" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button type="submit" name="simpanKategoriUnit" class="btn btn-primary">Simpan</button>
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
                "url": "ajax/katUnit.php",
                "type": "POST",
                "error": function(xhr) {
                    console.error(xhr.responseText);
                    alert("Gagal memuat data. Cek Console (F12) untuk detail.");
                }
            },
            "columns": [
                { "data": "no", "className": "text-center" },
                { "data": "nama_unit", "className": "text-center" }
            ],
            "createdRow": function(row, data) {
                $(row).attr('data-id', data.id_unit);
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
                selectedId = data.id_unit;
                selectedNama = data.nama_unit;
                $('#btnEdit, #btnHapus').prop('disabled', false);
            }
        });

        $('#btnTambah').click(function() {
            $('#modalLabel').text('Tambah Kategori');
            $('#formUnit')[0].reset();
            $('#id_unit').val('');
            $('#modalUnit').modal('show');
        });

        $('#btnEdit').click(function() {
            if(selectedId) {
                $('#modalLabel').text('Edit Kategori');
                $('#id_unit').val(selectedId); 
                $('#nama_unit').val(selectedNama);
                $('#modalUnit').modal('show');
            }
        });

        $('#btnHapus').click(function() {
            if(selectedId) {
                if(confirm('Hapus ' + selectedNama + '?')) {
                    window.location.href = 'kategoriUnit.php?hapus_idUnit=' + selectedId;
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