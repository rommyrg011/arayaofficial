<?php 
require_once __DIR__ . '/../function.php';  
include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php';

$id_user_login = 0;
if (isset($_SESSION['id_kurir'])) {
    $id_user_login = intval($_SESSION['id_kurir']);
} elseif (isset($_SESSION['id_user'])) {
    $id_user_login = intval($_SESSION['id_user']);
} elseif (isset($_SESSION['id'])) {
    $id_user_login = intval($_SESSION['id']);
}

$currentMonth = date('m'); 
$currentYear = date('Y');

$queryTotalOmset = mysqli_query($koneksi, "SELECT SUM(omset) as total_omset FROM karyawan WHERE id_user = $id_user_login");
$dataTotalOmset = mysqli_fetch_assoc($queryTotalOmset);
$total_omset_rp = 'Rp ' . number_format((float)$dataTotalOmset['total_omset'], 0, ',', '.');
?>

<style>
    #dataTable tbody tr {
        cursor: pointer;
    }
    #dataTable tbody tr.selected {
        background-color: rgba(204, 184, 180)!important;
    }
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-center" style="font-size:25px;">Pendapatan Harian</h6>
            <div class="text-center mt-3">
                <button type="button" class="btn btn-sm btn-primary mx-1 shadow-sm" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah
                </button>
                <button type="button" class="btn btn-sm btn-warning mx-1 shadow-sm" id="btnEditAtas" disabled>
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-sm btn-danger mx-1 shadow-sm" id="btnHapusAtas" disabled>
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
            <div class="text-center mt-3">
            <button class="btn btn-sm btn-success font-weight-bold mx-1 shadow-sm" style="cursor: default;" id="text_omset_bulan">
                    <i class="fas fa-calendar-alt"></i> Omset Perbulan: Rp 0
                </button>
                <button class="btn btn-sm btn-dark font-weight-bold mx-1 shadow-sm" style="cursor: default;">
                    <i class="fas fa-wallet"></i> Total Omset: <?= $total_omset_rp; ?>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <select id="filter_bulan" class="form-control shadow-sm">
                        <option value="">-- Semua Bulan --</option>
                        <option value="01" <?= $currentMonth == '01' ? 'selected' : '' ?>>Januari</option>
                        <option value="02" <?= $currentMonth == '02' ? 'selected' : '' ?>>Februari</option>
                        <option value="03" <?= $currentMonth == '03' ? 'selected' : '' ?>>Maret</option>
                        <option value="04" <?= $currentMonth == '04' ? 'selected' : '' ?>>April</option>
                        <option value="05" <?= $currentMonth == '05' ? 'selected' : '' ?>>Mei</option>
                        <option value="06" <?= $currentMonth == '06' ? 'selected' : '' ?>>Juni</option>
                        <option value="07" <?= $currentMonth == '07' ? 'selected' : '' ?>>Juli</option>
                        <option value="08" <?= $currentMonth == '08' ? 'selected' : '' ?>>Agustus</option>
                        <option value="09" <?= $currentMonth == '09' ? 'selected' : '' ?>>September</option>
                        <option value="10" <?= $currentMonth == '10' ? 'selected' : '' ?>>Oktober</option>
                        <option value="11" <?= $currentMonth == '11' ? 'selected' : '' ?>>November</option>
                        <option value="12" <?= $currentMonth == '12' ? 'selected' : '' ?>>Desember</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select id="filter_tahun" class="form-control shadow-sm">
                        <option value="">-- Semua Tahun --</option>
                        <?php
                        for($i = 2024; $i <= date('Y'); $i++){
                            $selected = ($currentYear == $i) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <hr>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Cabang</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Tipe Shift</th>
                            <th>Omset</th>
                            <th>Operasional</th>
                            <th>Pengeluaran</th>
                            <th>Laba Bersih</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="proses_karyawan.php" method="POST" id="formTambah">
            <input type="hidden" name="aksi" value="tambah">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pendapatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Cabang</label>
                        <select name="cabang" class="form-control" required>
                            <option value="">Pilih Cabang...</option>
                            <option value="Gambut">Gambut</option>
                            <option value="Beruntung">Beruntung</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Shift</label>
                        <select name="shift" class="form-control" required>
                            <option value="">Pilih Shift...</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipe Shift</label>
                        <select name="tipe_shift" class="form-control" required>
                            <option value="">Pilih Tipe Shift...</option>
                            <option value="Single">Single</option>
                            <option value="Partner">Partner</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Omset (Rp)</label>
                        <input type="text" name="omset" id="tambah_omset" class="form-control" placeholder="Rp." required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan Operasional</label>
                        <textarea name="operasional" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Total Pengeluaran (Rp)</label>
                        <input type="text" name="total_pengeluaran" id="tambah_total_pengeluaran" class="form-control" placeholder="Rp.">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="proses_karyawan.php" method="POST" id="formEdit">
            <input type="hidden" name="aksi" value="edit">
            <input type="hidden" name="id_karyawan" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pendapatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Cabang</label>
                        <select name="cabang" id="edit_cabang" class="form-control" required>
                            <option value="Gambut">Gambut</option>
                            <option value="Beruntung">Beruntung</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Shift</label>
                        <select name="shift" id="edit_shift" class="form-control" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipe Shift</label>
                        <select name="tipe_shift" id="edit_tipe_shift" class="form-control" required>
                            <option value="Single">Single</option>
                            <option value="Partner">Partner</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Omset (Rp)</label>
                        <input type="text" name="omset" id="edit_omset" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan Operasional</label>
                        <textarea name="operasional" id="edit_operasional" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Total Pengeluaran (Rp)</label>
                        <input type="text" name="total_pengeluaran" id="edit_total_pengeluaran" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php 
include 'template/footer.php'; 
include 'template/script.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "destroy": true,
        "ajax": {
            "url": "ajax/ajax_karyawan.php", 
            "type": "POST",
            "data": function(d) {
                d.bulan = $('#filter_bulan').val();
                d.tahun = $('#filter_tahun').val();
            },
            "dataSrc": function (json) {
                $('#text_omset_bulan').html('<i class="fas fa-calendar-alt"></i> Omset Perbulan: ' + json.total_omset_bulan);
                return json.data;
            }
        },
        "columns": [
            { "data": "no", "className": "text-center align-middle" },
            { "data": "cabang", "className": "font-weight-bold align-middle" },
            { "data": "tanggal", "className": "align-middle" },
            { "data": "shift", "className": "align-middle text-center" },
            { "data": "tipe_shift", "className": "align-middle text-center" },
            { "data": "omset", "className": "align-middle font-weight-bold text-success" },
            { "data": "operasional", "className": "align-middle text-center" },
            { "data": "total_pengeluaran", "className": "align-middle font-weight-bold text-danger text-center" },
            { "data": "laba_bersih", "className": "align-middle font-weight-bold text-primary" }
        ]
    });

    $('#filter_bulan, #filter_tahun').on('change', function() {
        table.ajax.reload();
    });

    var dataTerpilih = null;
    $('#dataTable tbody').on('click', 'tr', function () {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            dataTerpilih = null;
            $('#btnEditAtas').attr('disabled', true);
            $('#btnHapusAtas').attr('disabled', true);
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            dataTerpilih = table.row(this).data();
            $('#btnEditAtas').removeAttr('disabled');
            $('#btnHapusAtas').removeAttr('disabled');
        }
    });

    function formatRupiah(angka, prefix = 'Rp. ') {
        var number_string = angka.toString().replace(/[^0-9]/g, '');
        if (!number_string) return '';
        var sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/g);
        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return prefix + rupiah;
    }

    $('#tambah_omset, #edit_omset, #tambah_total_pengeluaran, #edit_total_pengeluaran').on('keyup', function() {
        $(this).val(formatRupiah($(this).val()));
    });

    $('#btnEditAtas').on('click', function() {
        if (dataTerpilih) {
            $('#edit_id').val(dataTerpilih.id_karyawan);
            $('#edit_cabang').val(dataTerpilih.cabang);
            $('#edit_tanggal').val(dataTerpilih.tanggal);
            $('#edit_shift').val(dataTerpilih.shift_raw);
            $('#edit_tipe_shift').val(dataTerpilih.tipe_shift_raw);
            $('#edit_omset').val(formatRupiah(dataTerpilih.omset_raw.toString()));
            $('#edit_operasional').val(dataTerpilih.operasional_raw);
            $('#edit_total_pengeluaran').val(dataTerpilih.total_pengeluaran_raw == 0 ? '' : formatRupiah(dataTerpilih.total_pengeluaran_raw.toString()));
            $('#modalEdit').modal('show');
        }
    });

    $('#formTambah').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        $(form).find('input[name="omset"], input[name="total_pengeluaran"]').each(function() {
            var val = $(this).val();
            if(val === '') {
                $(this).val(0);
            } else {
                $(this).val(val.replace(/[^0-9]/g, ''));
            }
        });
        Swal.fire({
            title: 'Konfirmasi Simpan',
            text: "Apakah Anda yakin ingin menyimpan data ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('#formEdit').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        $(form).find('input[name="omset"], input[name="total_pengeluaran"]').each(function() {
            var val = $(this).val();
            if(val === '') {
                $(this).val(0);
            } else {
                $(this).val(val.replace(/[^0-9]/g, ''));
            }
        });
        Swal.fire({
            title: 'Konfirmasi Update',
            text: "Apakah Anda yakin ingin mengubah data ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f6c23e',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('#btnHapusAtas').on('click', function() {
        if (dataTerpilih) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus data ini? Aksi ini tidak dapat dibatalkan.",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = $('<form action="proses_karyawan.php" method="POST">' +
                        '<input type="hidden" name="aksi" value="hapus">' +
                        '<input type="hidden" name="id_karyawan" value="' + dataTerpilih.id_karyawan + '">' +
                        '</form>');
                    $('body').append(form);
                    form.submit();
                }
            });
        }
    });
});
</script>