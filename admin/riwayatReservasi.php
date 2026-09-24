<?php
require_once __DIR__ . '/../function.php'; 
include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>
<div class="container-fluid">
    <div id="waNotifContainer"></div>

    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-auto font-weight-bold mb-2 mb-md-0">
                    <i class="fas fa-filter"></i> Filter Data :
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select class="form-control form-control-sm border-primary" id="filterCabang">
                        <option value="">-- Semua Cabang --</option>
                        <?php
                        $cabangQ = mysqli_query($koneksi, "SELECT DISTINCT cabang FROM reservasi WHERE cabang != ''");
                        while($cb = mysqli_fetch_assoc($cabangQ)) {
                            echo "<option value='".$cb['cabang']."'>".$cb['cabang']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
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
                <div class="col-md-2 mb-2 mb-md-0">
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary text-center" style="font-size:18px;">Grafik Total Reservasi Per Cabang</h6>
        </div>
        <div class="card-body">
            <div class="row" id="container-grafik-cabang">
                <div class="col-12 text-center text-muted py-3" id="grafik-placeholder">
                    Memuat grafik...
                </div>
            </div>
        </div>
    </div>

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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script>
$(document).ready(function() {
    var activeCharts = {}; 

    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/ajax_riwayat.php",
            "type": "POST",
            "data": function(d) {
                d.cabang = $('#filterCabang').val();
                d.bulan = $('#filterBulan').val();
                d.tahun = $('#filterTahun').val();
            },
            "dataSrc": function(json) {
                prosesGrafikPerCabang(json.chartData);
                return json.data;
            },
            "error": function(xhr) {
                console.error(xhr.responseText);
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

    $('#filterCabang, #filterBulan, #filterTahun').on('change', function() {
        table.ajax.reload();
    });

    $('#btnExportExcel').click(function() {
        var cabang = $('#filterCabang').val();
        var bulan = $('#filterBulan').val();
        var tahun = $('#filterTahun').val();
        window.location.href = 'export_excel_reservasi.php?cabang=' + cabang + '&bulan=' + bulan + '&tahun=' + tahun;
    });

    function prosesGrafikPerCabang(dataServer) {
        var container = $('#container-grafik-cabang');
        
        for (var key in activeCharts) {
            if (activeCharts.hasOwnProperty(key)) {
                activeCharts[key].destroy();
            }
        }
        activeCharts = {}; 
        container.empty(); 

        if (!dataServer || dataServer.length === 0) {
            container.html('<div class="col-12 text-center text-muted py-3">Tidak ada data reservasi untuk ditampilkan grafik.</div>');
            return;
        }

        var namaBulanIndo = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"];
        var dataKelompokCabang = {};
        var totalReservasiPerCabang = {};

        dataServer.forEach(function(item) {
            if (!item.cabang) return; 
            
            var namaCabang = item.cabang.trim();

            if (!dataKelompokCabang[namaCabang]) {
                dataKelompokCabang[namaCabang] = {};
                totalReservasiPerCabang[namaCabang] = 0;
                namaBulanIndo.forEach(function(bln) {
                    dataKelompokCabang[namaCabang][bln] = 0;
                });
            }

            if (item.tgl_bermain && item.tgl_bermain !== '0000-00-00') {
                var cleanedDate = item.tgl_bermain.replace(/^[a-zA-Z\s]+/, '');
                var dateParsed = new Date(cleanedDate);
                var bulanIndex = -1;

                if (isNaN(dateParsed.getTime())) {
                    var parts = item.tgl_bermain.split(' ');
                    if (parts.length >= 2) {
                        bulanIndex = namaBulanIndo.findIndex(b => parts[1].includes(b));
                    }
                } else {
                    bulanIndex = dateParsed.getMonth();
                }

                if (bulanIndex >= 0 && bulanIndex <= 11) {
                    var blnNama = namaBulanIndo[bulanIndex];
                    dataKelompokCabang[namaCabang][blnNama]++;
                    totalReservasiPerCabang[namaCabang]++; 
                }
            }
        });

        var listCabang = Object.keys(dataKelompokCabang).filter(function(cabang) {
            return totalReservasiPerCabang[cabang] > 0; 
        });

        if (listCabang.length === 0) {
            container.html('<div class="col-12 text-center text-muted py-3">Tidak ada data cabang aktif untuk ditampilkan grafik.</div>');
            return;
        }

        var colClass = "col-lg-6 col-md-12 mb-4";
        if (listCabang.length === 1) {
            colClass = "col-lg-12 col-md-12 mb-4";
        } else if (listCabang.length >= 3) {
            colClass = "col-lg-4 col-md-12 mb-4";
        }

        listCabang.forEach(function(cabangKey, index) {
            var idCanvas = "chart_cabang_" + index;
            
            var htmlCard = `
                <div class="${colClass}">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-center font-weight-bold text-dark text-capitalize mb-2" style="font-size: 14px;">
                            ${cabangKey}
                        </div>
                        <div style="position: relative; height: 22vh; width: 100%;">
                            <canvas id="${idCanvas}"></canvas>
                        </div>
                    </div>
                </div>
            `;
            container.append(htmlCard);

            var labels = Object.keys(dataKelompokCabang[cabangKey]);
            var dataValues = Object.values(dataKelompokCabang[cabangKey]);

            var warnaPilihan = [
                { bg: 'rgba(78, 115, 223, 0.85)', border: 'rgba(78, 115, 223, 1)' },  
                { bg: 'rgba(28, 200, 138, 0.85)', border: 'rgba(28, 200, 138, 1)' },  
                { bg: 'rgba(246, 194, 62, 0.85)', border: 'rgba(246, 194, 62, 1)' },   
                { bg: 'rgba(54, 185, 204, 0.85)', border: 'rgba(54, 185, 204, 1)' }   
            ];
            var warnaIndex = index % warnaPilihan.length;

            var ctx = document.getElementById(idCanvas).getContext('2d');
            activeCharts[idCanvas] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Reservasi',
                        data: dataValues,
                        backgroundColor: warnaPilihan[warnaIndex].bg,
                        borderColor: warnaPilihan[warnaIndex].border,
                        borderWidth: 1,
                        borderRadius: 3,
                        maxBarThickness: 15,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 10 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                maxRotation: 0,
                                minRotation: 0,
                                font: { size: 10 }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    }
});
</script>