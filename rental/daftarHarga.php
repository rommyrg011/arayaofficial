<?php 
require_once __DIR__ . '/../function.php'; 

include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 text-center">
                            <h6 class="m-0 font-weight-bold" style="font-size:25px;">Daftar Harga</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
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

<?php 
include 'template/footer.php'; 
include 'template/script.php'; 
?>

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
                "type": "POST",
                "error": function(xhr) {
                    console.error(xhr.responseText);
                    alert("Gagal memuat data. Cek Console (F12) untuk detail.");
                }
            },
            "columns": [
                { "data": "no", "className": "text-center" },
                { "data": "p_paket", "className": "text-center" },
                { "data": "durasi", "className": "text-center" },
                { "data": "daf_harga", "className": "text-center" }
            ],
            "createdRow": function(row, data) {
                $(row).attr('data-id', data.id_harga);
            }
        });

    });
        
</script>
</body>
</html>