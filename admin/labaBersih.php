<?php 
require_once __DIR__ . '/../function.php'; 

include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 

$bulan_sekarang = date('m');
$tahun_sekarang = date('Y');

$list_bulan = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", 
    "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", 
    "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
];
?>
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-auto font-weight-bold mb-2 mb-md-0">
                    <i class="fas fa-filter"></i> Filter :
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select class="form-control form-control-sm border-primary" id="filterCabang">
                        <option value="">-- Semua Cabang --</option>
                        <?php
                        $resCabang = mysqli_query($koneksi, "SELECT DISTINCT cabang FROM karyawan WHERE cabang IS NOT NULL AND cabang != '' ORDER BY cabang ASC");
                        while($rowC = mysqli_fetch_assoc($resCabang)) {
                            echo "<option value='".$rowC['cabang']."'>".$rowC['cabang']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select class="form-control form-control-sm border-primary" id="filterBulan">
                        <option value="">-- Semua Bulan --</option>
                        <?php
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
                        for ($i = 2024; $i <= $tahun_sekarang + 3; $i++) {
                            $selected = ($i == $tahun_sekarang) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="col-md-2 mb-2 mb-md-0">
                    <button type="button" id="btnExportExcel" class="btn btn-success btn-sm font-weight-bold btn-block">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="chartsContainer">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary text-center">Grafik Pendapatan Kotor Percabang</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="position: relative; height: 40vh; width: 100%;">
                        <canvas id="chartPendapatanKotor"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success text-center">Grafik Pendapatan Bersih Percabang</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="position: relative; height: 40vh; width: 100%;">
                        <canvas id="chartPendapatanBersih"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-info text-white text-center">
                    <h6 class="m-0 font-weight-bold" style="font-size:18px;">Ringkasan Pendapatan Kotor dan Bersih Percabang</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" rowspan="2" style="vertical-align: middle;" width="5%">No</th>
                                    <th rowspan="2" style="vertical-align: middle;">Cabang</th>
                                    <th class="text-right" rowspan="2" style="vertical-align: middle;">Pendapatan Kotor</th>
                                    <th class="text-center" colspan="5">Pengeluaran</th>
                                    <th class="text-right" rowspan="2" style="vertical-align: middle;">Pendapatan Bersih</th>
                                </tr>
                                <tr>
                                    <th class="text-right">Operasional Mingguan</th>
                                    <th class="text-right">Operasional Bulanan</th>
                                    <th class="text-right">Sewa Toko</th>
                                    <th class="text-right">Gaji Staff</th>
                                    <th class="text-right">Total Pengeluaran</th>
                                </tr>
                            </thead>
                            <tbody id="ringkasanBody">
                            </tbody>
                            <tfoot class="bg-light font-weight-bold" id="ringkasanFoot">
                            </tfoot>
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
    var chartKotorInstance = null;
    var chartBersihInstance = null;

    function formatRupiah(angka) {
        var isNegative = angka < 0 ? '-' : '';
        var absAngka = Math.abs(angka).toString();
        var reverse = absAngka.split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan ? ribuan.join('.').split('').reverse().join('') : '0';
        return isNegative + 'Rp. ' + ribuan;
    }

    function loadData() {
        $.ajax({
            url: "ajax/kLabaBersih.php",
            type: "POST",
            data: {
                cabang: $('#filterCabang').val(),
                bulan: $('#filterBulan').val(),
                tahun: $('#filterTahun').val()
            },
            dataType: "json",
            success: function(json) {
                renderGrafik(json.chartDataCabang);
                renderRingkasan(json.chartDataCabang);
            }
        });
    }

    loadData();

    $('#filterCabang, #filterBulan, #filterTahun').on('change', function() {
        loadData();
    });

    $('#btnExportExcel').click(function() {
        var cabang = $('#filterCabang').val();
        var bulan = $('#filterBulan').val();
        var tahun = $('#filterTahun').val();
        window.location.href = 'export_excelLabaBersih.php?cabang=' + encodeURIComponent(cabang) + '&bulan=' + bulan + '&tahun=' + tahun;
    });

    function renderGrafik(dataCabang) {
        if (chartKotorInstance !== null) chartKotorInstance.destroy();
        if (chartBersihInstance !== null) chartBersihInstance.destroy();

        var labels = [];
        var dataKotor = [];
        var dataBersih = [];

        dataCabang.forEach(function(item) {
            labels.push(item.cabang);
            dataKotor.push(item.pendapatan_kotor);
            dataBersih.push(item.pendapatan_bersih);
        });

        var ctxKotor = document.getElementById('chartPendapatanKotor').getContext('2d');
        chartKotorInstance = new Chart(ctxKotor, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan Kotor',
                    data: dataKotor,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) { return formatRupiah(context.raw); }
                        }
                    }
                }
            }
        });

        var ctxBersih = document.getElementById('chartPendapatanBersih').getContext('2d');
        chartBersihInstance = new Chart(ctxBersih, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan Bersih',
                    data: dataBersih,
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) { return formatRupiah(context.raw); }
                        }
                    }
                }
            }
        });
    }

    function renderRingkasan(dataCabang) {
        var tbody = $('#ringkasanBody');
        var tfoot = $('#ringkasanFoot');
        tbody.empty();
        tfoot.empty();

        var totalKotor = 0;
        var totalOpMingguan = 0;
        var totalOpBulanan = 0;
        var totalSewa = 0;
        var totalGaji = 0;
        var totalPengeluaran = 0;
        var totalBersih = 0;

        if(!dataCabang || dataCabang.length === 0) {
            tbody.append('<tr><td colspan="9" class="text-center">Data tidak ditemukan</td></tr>');
            return;
        }

        dataCabang.forEach(function(item, index) {
            totalKotor += parseFloat(item.pendapatan_kotor);
            totalOpMingguan += parseFloat(item.operasional_mingguan);
            totalOpBulanan += parseFloat(item.operasional_bulanan);
            totalSewa += parseFloat(item.sewa);
            totalGaji += parseFloat(item.gaji);
            totalPengeluaran += parseFloat(item.total_pengeluaran);
            totalBersih += parseFloat(item.pendapatan_bersih);

            var colorClass = item.pendapatan_bersih < 0 ? 'text-danger' : 'text-success';

            var tr = '<tr>' +
                '<td class="text-center">' + (index + 1) + '</td>' +
                '<td class="font-weight-bold">' + item.cabang + '</td>' +
                '<td class="text-right text-primary">' + formatRupiah(item.pendapatan_kotor) + '</td>' +
                '<td class="text-right text-muted">' + formatRupiah(item.operasional_mingguan) + '</td>' +
                '<td class="text-right text-muted">' + formatRupiah(item.operasional_bulanan) + '</td>' +
                '<td class="text-right text-muted">' + formatRupiah(item.sewa) + '</td>' +
                '<td class="text-right text-muted">' + formatRupiah(item.gaji) + '</td>' +
                '<td class="text-right text-danger">' + formatRupiah(item.total_pengeluaran) + '</td>' +
                '<td class="text-right ' + colorClass + ' font-weight-bold">' + formatRupiah(item.pendapatan_bersih) + '</td>' +
            '</tr>';
            tbody.append(tr);
        });

        var totalColorClass = totalBersih < 0 ? 'text-danger' : 'text-success';

        var tf = '<tr>' +
            '<td colspan="2" class="text-center">Total Keseluruhan</td>' +
            '<td class="text-right text-primary">' + formatRupiah(totalKotor) + '</td>' +
            '<td class="text-right text-muted">' + formatRupiah(totalOpMingguan) + '</td>' +
            '<td class="text-right text-muted">' + formatRupiah(totalOpBulanan) + '</td>' +
            '<td class="text-right text-muted">' + formatRupiah(totalSewa) + '</td>' +
            '<td class="text-right text-muted">' + formatRupiah(totalGaji) + '</td>' +
            '<td class="text-right text-danger">' + formatRupiah(totalPengeluaran) + '</td>' +
            '<td class="text-right ' + totalColorClass + ' font-weight-bold">' + formatRupiah(totalBersih) + '</td>' +
        '</tr>';
        tfoot.append(tf);
    }
});
</script>