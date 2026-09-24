<?php 
require_once __DIR__ . '/../function.php'; 

include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 

$tanggal_sekarang = date('d');
$bulan_sekarang   = date('m');
$tahun_sekarang   = date('Y');

$list_bulan = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", 
    "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", 
    "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
];

$nama_bulan_ini = $list_bulan[$bulan_sekarang];

$qRingkasan = mysqli_query($koneksi, "SELECT cabang, SUM(IFNULL(omset, 0)) as total_omset FROM karyawan WHERE MONTH(tanggal) = '$bulan_sekarang' AND YEAR(tanggal) = '$tahun_sekarang' AND cabang IS NOT NULL AND cabang != '' GROUP BY cabang ORDER BY cabang ASC");
$dataRingkasan = [];
$totalSemuaRingkasan = 0;
if($qRingkasan) {
    while($rowR = mysqli_fetch_assoc($qRingkasan)) {
        $dataRingkasan[] = $rowR;
        $totalSemuaRingkasan += $rowR['total_omset'];
    }
}
?>
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-body py-2 px-3">
            <div class="form-row align-items-center">
                <div class="col-auto my-1 font-weight-bold">
                    <i class="fas fa-filter"></i> Filter :
                </div>
                <div class="col-md my-1">
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
                <div class="col-md my-1">
                    <select class="form-control form-control-sm border-primary" id="filterTanggal">
                        <option value="">-- Semua Tanggal --</option>
                        <?php
                        for ($i = 1; $i <= 31; $i++) {
                            $tgl = str_pad($i, 2, '0', STR_PAD_LEFT);
                            $selected = ($tgl == $tanggal_sekarang) ? 'selected' : '';
                            echo "<option value='$tgl' $selected>$tgl</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md my-1">
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
                <div class="col-md my-1">
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
                <div class="col-auto my-1">
                    <button type="button" id="btnExportExcel" class="btn btn-success btn-sm font-weight-bold px-3">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
                <div class="col-auto my-1">
                    <button type="button" class="btn btn-info btn-sm font-weight-bold px-3" data-toggle="modal" data-target="#modalRingkasan">
                        Ringkasan Bulan Ini
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4" id="containerSemuaCabang">
        <div class="col-12">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-center" style="font-size:18px;">Persentase Semua Omset Operator</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie" style="position: relative; height: 40vh; width: 100%;">
                        <canvas id="chartSemuaCabang"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="branchChartsContainer">
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow h-100">
                <div class="card-header py-3 text-center">
                    <h6 class="m-0 font-weight-bold" style="font-size:20px;">Tabel Omset Operator</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="15%">Cabang</th>
                                    <th>Nama Operator</th>
                                    <th class="text-center" width="15%">Total Target Tercapai</th>
                                    <th class="text-center" width="15%">Surplus</th>
                                    <th class="text-center" width="20%">Total Omset</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<div class="modal fade" id="modalRingkasan" tabindex="-1" role="dialog" aria-labelledby="modalRingkasanLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalRingkasanLabel">Ringkasan Omset Bulan <?= $nama_bulan_ini . ' ' . $tahun_sekarang ?></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" width="10%">No</th>
                                <th>Cabang</th>
                                <th class="text-right">Total Omset</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($dataRingkasan) > 0): ?>
                                <?php $noR = 1; foreach($dataRingkasan as $dr): ?>
                                <tr>
                                    <td class="text-center"><?= $noR++ ?></td>
                                    <td class="font-weight-bold"><?= $dr['cabang'] ?></td>
                                    <td class="text-right">Rp. <?= number_format($dr['total_omset'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="2" class="text-center">Total Keseluruhan</td>
                                    <td class="text-right text-success">Rp. <?= number_format($totalSemuaRingkasan, 0, ',', '.') ?></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada omset pada bulan ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
    var chartSemuaInstance = null;
    var chartCabangInstances = [];

    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return 'Rp. ' + ribuan;
    }

    function generateColors(count) {
        var colors = [];
        for (var i = 0; i < count; i++) {
            var r = Math.floor(Math.random() * 200) + 50;
            var g = Math.floor(Math.random() * 200) + 50;
            var b = Math.floor(Math.random() * 200) + 50;
            colors.push('rgba(' + r + ', ' + g + ', ' + b + ', 0.8)');
        }
        return colors;
    }

    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/k_omsetStaff.php",
            "type": "POST",
            "data": function(d) {
                d.cabang = $('#filterCabang').val();
                d.tanggal = $('#filterTanggal').val();
                d.bulan = $('#filterBulan').val();
                d.tahun = $('#filterTahun').val();
            },
            "dataSrc": function(json) {
                renderGrafikSemuaCabang(json.chartDataSemua);
                renderGrafikPerCabang(json.chartDataCabang);
                return json.data;
            }
        },
        "columns": [
            { "data": "no", "className": "text-center align-middle" },
            { "data": "cabang", "className": "text-center align-middle font-weight-bold" },
            { "data": "nama_lengkap", "className": "text-left align-middle" },
            { "data": "total_target_tercapai", "className": "text-center align-middle font-weight-bold" },
            { "data": "total_surplus_format", "className": "text-center align-middle font-weight-bold text-success" },
            { "data": "total_omset_format", "className": "text-center align-middle" }
        ]
    });

    $('#filterCabang, #filterTanggal, #filterBulan, #filterTahun').on('change', function() {
        if ($('#filterCabang').val() !== '') {
            $('#containerSemuaCabang').hide();
        } else {
            $('#containerSemuaCabang').show();
        }
        table.ajax.reload();
    });

    $('#btnExportExcel').click(function() {
        var cabang = $('#filterCabang').val();
        var tanggal = $('#filterTanggal').val();
        var bulan = $('#filterBulan').val();
        var tahun = $('#filterTahun').val();
        window.location.href = 'export_excelTotalOmset.php?cabang=' + encodeURIComponent(cabang) + '&tanggal=' + tanggal + '&bulan=' + bulan + '&tahun=' + tahun;
    });

    function renderGrafikSemuaCabang(dataSemua) {
        if (chartSemuaInstance !== null) {
            chartSemuaInstance.destroy();
        }

        var labels = [];
        var dataValues = [];
        var totalKeseluruhan = 0;

        dataSemua.forEach(function(item) {
            var val = parseFloat(item.total_omset);
            totalKeseluruhan += val;
            labels.push(item.nama_lengkap);
            dataValues.push(val);
        });

        var ctx = document.getElementById('chartSemuaCabang').getContext('2d');
        var bgColors = generateColors(dataValues.length);

        chartSemuaInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: bgColors,
                    hoverOffset: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var val = context.raw;
                                var percentage = totalKeseluruhan > 0 ? ((val / totalKeseluruhan) * 100).toFixed(2) : 0;
                                return context.label + ': ' + formatRupiah(val) + ' (' + percentage + '%)';
                            }
                        }
                    },
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }

    function renderGrafikPerCabang(dataCabang) {
        var container = $('#branchChartsContainer');
        container.empty();

        chartCabangInstances.forEach(function(chart) {
            chart.destroy();
        });
        chartCabangInstances = [];

        var dataByCabang = {};
        dataCabang.forEach(function(item) {
            var cbg = item.cabang;
            if (!dataByCabang[cbg]) {
                dataByCabang[cbg] = [];
            }
            dataByCabang[cbg].push(item);
        });

        var index = 0;
        Object.keys(dataByCabang).forEach(function(cbg) {
            var items = dataByCabang[cbg];
            var labels = [];
            var values = [];
            var tertinggi = -1;
            var operatorTerbaik = [];
            var operatorTargetFormatted = [];

            items.forEach(function(it) {
                var val = parseFloat(it.total_omset);
                labels.push(it.nama_lengkap);
                values.push(val);

                if (val > tertinggi) {
                    tertinggi = val;
                    operatorTerbaik = [it.nama_lengkap]; 
                } else if (val === tertinggi && tertinggi > 0) {
                    operatorTerbaik.push(it.nama_lengkap); 
                }

                if (it.target_details && it.target_details.trim() !== '') {
                    var detailText = '';
                    var details = it.target_details.split('|');
                    var formattedDetails = [];
                    details.forEach(function(dt) {
                        var parts = dt.split(':');
                        if (parts.length >= 2) {
                            var targetShiftStr = 'shift ' + parts[0] + ' : ' + formatRupiah(parts[1]);
                            var surplusValue = parts[2] ? parseFloat(parts[2]) : 0;
                            if(surplusValue > 0) {
                                targetShiftStr += ' - Surplus : <span class="text-success font-weight-bold">' + formatRupiah(surplusValue) + '</span>';
                            }
                            formattedDetails.push(targetShiftStr);
                        }
                    });
                    if (formattedDetails.length > 0) {
                        detailText = ' ( ' + formattedDetails.join(', ') + ' )';
                        operatorTargetFormatted.push('<strong>' + it.nama_lengkap + '</strong>' + detailText);
                    }
                }
            });

            var canvasId = 'chartCabang_' + index;
            var html = '<div class="col-lg-6 mb-4">' +
                           '<div class="card shadow h-100">' +
                               '<div class="card-header py-3">' +
                                   '<h6 class="m-0 font-weight-bold text-primary text-center">Cabang ' + cbg + '</h6>' +
                               '</div>' +
                               '<div class="card-body d-flex flex-column justify-content-between">' +
                                   '<div class="chart-bar" style="position: relative; height: 35vh; width: 100%;">' +
                                       '<canvas id="' + canvasId + '"></canvas>' +
                                   '</div>';

            if (tertinggi > 0 && operatorTerbaik.length > 0) {
                var textTerbaik = operatorTerbaik.join(' & ');
                html += '<div class="text-center mt-3 p-2 alert alert-success" style="font-size: 14px; width: 100%; margin-bottom: 0;">' +
                        '<strong>Peringkat 1 : </strong> <strong>' + textTerbaik + '</strong> (' + formatRupiah(tertinggi) + ')' +
                        '</div>';
            }

            if (operatorTargetFormatted.length > 0) {
                html += '<div class="text-left mt-2 p-3 alert alert-info" style="font-size: 14px; width: 100%; margin-bottom: 0;">' +
                            '<div class="font-weight-bold mb-1"><i class="fas fa-bullseye"></i> Operator yang mencapai target :</div>' +
                            '<ul class="mb-0 pl-3">';
                operatorTargetFormatted.forEach(function(opText) {
                    html += '<li>' + opText + '</li>';
                });
                html +=     '</ul>' +
                        '</div>';
            }

            html +=            '</div>' +
                           '</div>' +
                       '</div>';

            container.append(html);

            var ctx = document.getElementById(canvasId).getContext('2d');
            var newChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Omset',
                        data: values,
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return formatRupiah(context.raw);
                                }
                            }
                        }
                    }
                }
            });

            chartCabangInstances.push(newChart);
            index++;
        });
    }
});
</script>