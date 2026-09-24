<?php 
require_once __DIR__ . '/../function.php'; 

include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>
                <div class="container-fluid">
                    <div id="waNotifContainer"></div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 text-center">
                            <h6 class="m-0 font-weight-bold" style="font-size:25px;">Riwayat Reservasi</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover w-100" id="dataTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Cabang</th>
                                            <th>Nama</th>
                                            <th>Tanggal</th>
                                            <th>Ruang</th>
                                            <th>Jumlah Orang</th>
                                            <th>Kedatangan</th>
                                            <th>Durasi</th>
                                            <th>Whatsapp</th>
                                            <th>Dp</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
        var selectedRowData = null; 
        var isAlertOpen = false; // Flag untuk melacak status pop-up SweetAlert2

        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }

        var table = $('#dataTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "ajax/ajax_riwayat.php",
                "type": "POST",
                "error": function(xhr) {
                    console.error(xhr.responseText);
                    alert("Gagal memuat data. Cek Console (F12) untuk detail.");
                }
            },
            "columns": [
                { "data": "no", "className": "text-center" },
                { "data": "cabang", "className": "text-center" },
                { "data": "nama_reservasi", "className": "text-center" },
                { "data": "tgl_bermain", "className": "text-center" },
                { "data": "ruang", "className": "text-center" },
                { "data": "jml_orang", "className": "text-center" },
                { "data": "w_kedatangan", "className": "text-center" },
                { "data": "durasi", "className": "text-center" },
                { "data": "whatsapp", "className": "text-center" },
                { "data": "dp", "className": "text-center" },
                { "data": "catatan", "className": "text-center" },
            ],
            "createdRow": function(row, data) {
                $(row).attr('data-id', data.id_reservasi);
            }
        });
    });
    </script>
</body>
</html>