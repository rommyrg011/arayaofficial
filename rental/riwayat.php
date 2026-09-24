<?php 
require_once __DIR__ . '/../function.php'; 

include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>

<style>
    #dataTable tbody tr.selected {
        background-color: rgba(78, 115, 223, 0.15) !important;
        cursor: pointer;
    }
    #dataTable tbody tr {
        cursor: pointer;
    }
    
    /* --- PERUBAHAN UKURAN GAMBAR BUKTI DI SINI --- */
    .img-bukti-tabel {
        max-width: 40px; /* Diperkecil dari 80px ke 40px agar pas dengan baris teks */
        height: auto;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s;
    }
    
    /* Efek Pop-out saat di-hover agar admin tetap bisa melihat sekilas tanpa klik */
    .img-bukti-tabel:hover {
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        cursor: zoom-in;
        position: relative;
        z-index: 10; /* Memastikan gambar berada di atas garis tabel saat membesar */
    }
</style>

                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-center" style="font-size:25px;">Riwayat</h6>
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
                                            <th class="text-center" style="width: 4%;">Foto Bukti</th>
                                            <th>Kurir</th> 
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
<?php 
// 3. Panggil kerangka layout komponen bagian bawah & JavaScript
include 'template/footer.php'; 
include 'template/script.php'; 
?>

    <script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }

        var targetUrl = "ajax_riwayat.php";
        
        $.ajax({
            url: targetUrl,
            type: 'HEAD',
            error: function() {
                targetUrl = "ajax/ajax_riwayat.php";
                inisialisasiDataTables(targetUrl);
            },
            success: function() {
                inisialisasiDataTables(targetUrl);
            }
        });

        function inisialisasiDataTables(urlDipilih) {
            $('#dataTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ordering": false,
                "ajax": {
                    "url": urlDipilih,
                    "type": "POST",
                    "dataSrc": function (json) {
                        if(json.error) {
                            alert("Pesan Sistem Server: " + json.error);
                            return [];
                        }
                        return json.data;
                    }
                },
                "columns": [
                    { "data": "no", "className": "text-center align-middle" },
                    { "data": "nama_perental", "className": "font-weight-bold text-capitalize align-middle" },
                    { "data": "durasi_sewa", "className": "align-middle" },
                    { "data": "jaminan", "className": "align-middle" },
                    { "data": "wa", "className": "align-middle" },
                    { "data": "alamat_lengkap", "className": "align-middle" },
                    { "data": "status", "className": "align-middle" },
                    { 
                        "data": "img", 
                        "className": "text-center align-middle",
                        "render": function(data, type, row) {
                            if (data && data.trim() !== "") {
                                return '<a href="../img/' + data + '" target="_blank">' +
                                       '<img src="../img/' + data + '" class="img-bukti-tabel" alt="Bukti">' +
                                       '</a>';
                            } else {
                                return '<span class="badge badge-secondary p-1 small" style="font-size: 10px;">Tidak Ada</span>';
                            }
                        }
                    },
                    { "data": "nama_kurir", "className": "align-middle font-weight-bold text-primary" },
                    { "data": "catatan", "className": "align-middle" }
                ]
            });
        }
    });
    </script>
</body>
</html>