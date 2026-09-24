<?php 
require_once __DIR__ . '/../function.php'; 

include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-auto font-weight-bold mb-2 mb-md-0">
                    <i class="fas fa-filter"></i> Filter Periode :
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select class="form-control form-control-sm border-primary" id="filterBulan">
                        <option value="">-- Semua Bulan --</option>
                        <?php
                        $bulan_sekarang = date('m');
                        $list_bulan = [
                            "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", 
                            "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", 
                            "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
                        ];

                        foreach ($list_bulan as $key => $nama) {
                            $selected = ($key == $bulan_sekarang) ? 'selected' : '';
                            echo "<option value='$key' $selected>$nama</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select class="form-control form-control-sm border-primary" id="filterTahun">
                        <option value="">-- Semua Tahun --</option>
                        <?php
                        $tahun_sekarang = date('Y');
                        for ($i = 2024; $i <= $tahun_sekarang + 3; $i++) {
                            $selected = ($i == $tahun_sekarang) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" id="btnExportExcel" class="btn btn-success btn-sm font-weight-bold btn-block">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary text-center" style="font-size:18px;">Grafik Pengantaran</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="chart-bar" style="position: relative; height: 35vh; width: 100%;">
                        <canvas id="myBarChart"></canvas>
                    </div>
                    <div id="kesimpulan-kurir" class="text-center mt-3 p-2 alert alert-success d-none" style="font-size: 14px; width: 100%;">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold" style="font-size:20px;">Data Rental Operator</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="8%" class="text-center">No</th>
                                    <th>Operator (Kurir)</th>
                                    <th class="text-center" width="35%">Total Pengantaran</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<?php 
include 'template/footer.php'; 
include 'template/script.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script>
$(document).ready(function() {
    var myChart = null; 

    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/krental.php",
            "type": "POST",
            "data": function(d) {
                d.bulan = $('#filterBulan').val();
                d.tahun = $('#filterTahun').val();
            },
            "dataSrc": function(json) {
                perbaruiGrafikDanKesimpulan(json.chartData);
                return json.data;
            }
        },
        "columns": [
            { "data": "no", "className": "text-center align-middle" },
            { "data": "nama_kurir", "className": "text-left font-weight-bold align-middle" },
            { "data": "total_pengantaran", "className": "text-center align-middle" }
        ],
        "createdRow": function(row, data) {
            $(row).attr('data-id', data.id_user);
        }
    });

    $('#filterBulan, #filterTahun').on('change', function() {
        table.ajax.reload();
    });

    $('#btnExportExcel').click(function() {
        var bulan = $('#filterBulan').val();
        var tahun = $('#filterTahun').val();
        window.location.href = 'export_excel_kurir.php?bulan=' + bulan + '&tahun=' + tahun;
    });

    function perbaruiGrafikDanKesimpulan(dataServer) {
        var labels = [];
        var dataValues = [];
        var tertinggi = -1;
        var kurirTerbaik = [];

        dataServer.forEach(function(item) {
            labels.push(item.nama_kurir);
            var totalAngka = parseInt(String(item.total_pengantaran).replace(/[^0-9]/g, '')) || 0;
            dataValues.push(totalAngka);

            if (totalAngka > tertinggi) {
                tertinggi = totalAngka;
                kurirTerbaik = [item.nama_kurir]; 
            } else if (totalAngka === tertinggi && tertinggi > 0) {
                kurirTerbaik.push(item.nama_kurir); 
            }
        });

        var boxKesimpulan = $('#kesimpulan-kurir');
        if (tertinggi > 0 && kurirTerbaik.length > 0) {
            var namaKurirText = kurirTerbaik.join(' & ');
            boxKesimpulan.html('<strong>Terbaik : </strong> <strong>' + namaKurirText + '</strong> (' + tertinggi + ' x pengantaran)');
            boxKesimpulan.removeClass('d-none');
        } else {
            boxKesimpulan.addClass('d-none'); 
        }

        var ctx = document.getElementById('myBarChart').getContext('2d');

        if (myChart !== null) {
            myChart.destroy();
        }

        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Pengantaran',
                    data: dataValues,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)', 
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1,
                    borderRadius: 5,
                    maxBarThickness: 30,  
                    barPercentage: 0.6    
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false 
                    }
                }
            }
        });
    }
});
</script>