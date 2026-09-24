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

    <div id="waNotifContainer"></div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 text-center">
            <h6 class="m-0 font-weight-bold" style="font-size:25px;">Reservasi</h6>
            <div class="mt-3">
                <button class="btn btn-success btn-sm" id="btnMessage" disabled><i class="fab fa-whatsapp"></i> Hubungi </button>
                <button class="btn btn-info btn-sm" id="btnEditDP" disabled><i class="fas fa-edit"></i> Upload DP </button>
                <button class="btn btn-primary btn-sm" id="btnUbahData" disabled><i class="fas fa-edit"></i> Ubah Data </button>
                <button class="btn btn-danger btn-sm" id="btnHapusData" disabled><i class="fas fa-times"></i> Pembatalan </button>
                <button class="btn btn-warning btn-sm" id="btnSelesai" disabled><i class="fas fa-check"></i> Selesai </button>
            </div>
            
            <div class="mt-4 d-flex justify-content-center">
                <div class="btn-group shadow-sm" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active btn-filter-utama" id="btnFilterSemua">Semua</button>
                    
                    <div class="btn-group" role="group">
                        <button id="btnGroupDropHari" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Hari
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDropHari">
                            <a class="dropdown-item filter-pilih-hari" data-hari="Senin">Senin</a>
                            <a class="dropdown-item filter-pilih-hari" data-hari="Selasa">Selasa</a>
                            <a class="dropdown-item filter-pilih-hari" data-hari="Rabu">Rabu</a>
                            <a class="dropdown-item filter-pilih-hari" data-hari="Kamis">Kamis</a>
                            <a class="dropdown-item filter-pilih-hari" data-hari="Jumat">Jumat</a>
                            <a class="dropdown-item filter-pilih-hari" data-hari="Sabtu">Sabtu</a>
                            <a class="dropdown-item filter-pilih-hari" data-hari="Minggu">Minggu</a>
                        </div>
                    </div>

                    <div class="btn-group" role="group">
                        <button id="btnGroupDropRuang" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Ruang
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDropRuang">
                            <a class="dropdown-item filter-pilih-ruang" data-ruang="semua">Semua Ruang</a>
                            <a class="dropdown-item filter-pilih-ruang" data-ruang="Reguler">Reguler</a>
                            <a class="dropdown-item filter-pilih-ruang" data-ruang="VIP 1">VIP 1</a>
                            <a class="dropdown-item filter-pilih-ruang" data-ruang="VIP 2">VIP 2</a>
                            <a class="dropdown-item filter-pilih-ruang" data-ruang="PREMIERE 1">PREMIERE 1</a>
                            <a class="dropdown-item filter-pilih-ruang" data-ruang="PREMIERE 2">PREMIERE 2</a>
                        </div>
                    </div>

                    <div class="btn-group" role="group">
                        <button id="btnGroupDropCabang" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Cabang
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDropCabang">
                            <a class="dropdown-item filter-pilih-cabang" data-cabang="semua">Semua Cabang</a>
                            <a class="dropdown-item filter-pilih-cabang" data-cabang="Gambut">Gambut</a>
                            <a class="dropdown-item filter-pilih-cabang" data-cabang="Beruntung">Beruntung</a>
                        </div>
                    </div>
                </div>
            </div>

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
                            <th>Tambahan</th>
                            <th>Kedatangan</th>
                            <th>Durasi</th>
                            <th>Kode Voucher</th>
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

<div class="modal fade" id="modalRuangan" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formKirimWA">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Kirim Pesan WhatsApp</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_reservasi" id="id_reservasi">
                    <div class="form-group">
                        <label>Nama Pelanggan</label>
                        <input type="text" class="form-control" id="nama_pelanggan" readonly>
                    </div>
                    <div class="form-group">
                        <label>Whatsapp Pelanggan</label>
                        <input type="text" class="form-control" name="target" id="whatsapp_pelanggan" required>
                    </div>
                    <div class="form-group">
                        <label>Isi pesan</label>
                        <textarea class="form-control" name="message" id="sendWaMessage" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary btn" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btnProsesKirim" class="btn btn-success"><i class="fab fa-whatsapp"></i> Kirim</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditDP" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formEditDP" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Bukti DP</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_reservasi" id="edit_id_reservasi">
                    <div class="form-group">
                        <label>Nama Pelanggan</label>
                        <input type="text" class="form-control" id="edit_nama_pelanggan" readonly>
                    </div>
                    <div class="form-group">
                        <label>Upload Bukti DP</label>
                        <input type="file" class="form-control-file" name="dp_berkas" id="dp_berkas" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary btn" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btnProsesEdit" class="btn btn-info"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalUbahData" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formUbahData">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Data Reservasi</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_reservasi" id="ubah_id_reservasi">
                    <div class="form-group">
                        <label>Nama Pelanggan</label>
                        <input type="text" class="form-control" name="nama_reservasi" id="ubah_nama_pelanggan" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Bermain</label>
                        <input type="date" class="form-control" name="tgl_bermain" id="ubah_tgl_bermain" required>
                    </div>
                    <div class="form-group">
                        <label>Ruang</label>
                        <select class="form-control" name="ruang" id="ubah_ruang" required>
                            <option value="Reguler">Reguler</option>
                            <option value="VIP 1">VIP 1</option>
                            <option value="VIP 2">VIP 2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jam Masuk</label>
                        <input type="time" class="form-control" name="w_kedatangan" id="ubah_w_kedatangan" required>
                    </div>
                    <div class="form-group">
                        <label>Durasi Bermain</label>
                        <select class="form-control" name="durasi" id="ubah_durasi" required>
                            <option value="1 Jam">1 Jam</option>
                            <option value="2 Jam">2 Jam</option>
                            <option value="3 Jam">3 Jam</option>
                            <option value="4 Jam">4 Jam</option>
                            <option value="5 Jam">5 Jam</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea class="form-control" name="catatan" id="ubah_catatan" rows="3" placeholder="Masukkan catatan atau ketik '-' jika tidak ada"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btnProsesUbahData" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </div>
        </form>
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
    var isAlertOpen = false; 
    
    var filterAktif = 'semua';
    var filterRuangAktif = 'semua';
    var filterCabangAktif = 'semua';

    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/ajax_reservasi.php",
            "type": "POST",
            "data": function(d) {
                d.filter_tanggal = filterAktif;
                d.filter_ruang = filterRuangAktif; 
                d.filter_cabang = filterCabangAktif;
            },
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
            { "data": "tambahan", "className": "text-center" },
            { "data": "w_kedatangan", "className": "text-center" },
            { "data": "durasi", "className": "text-center" },
            { "data": "kode_voucher", "className": "text-center font-weight-bold text-success" },
            { "data": "whatsapp", "className": "text-center" },
            { "data": "dp", "className": "text-center" },
            { "data": "catatan", "className": "text-center" },
        ],
        "createdRow": function(row, data) {
            $(row).attr('data-id', data.id_reservasi);
        }
    });

    $('#btnFilterSemua').click(function() {
        filterAktif = 'semua';
        $('.btn-filter-utama').removeClass('active');
        $('#btnGroupDropHari').removeClass('active').text('Pilih Hari'); 
        $(this).addClass('active');
        resetSeleksiBaris();
        table.ajax.reload();
    });

    $('.filter-pilih-hari').click(function(e) {
        e.preventDefault();
        var hariDipilih = $(this).data('hari');
        filterAktif = hariDipilih;
        $('.btn-filter-utama').removeClass('active');
        $('#btnGroupDropHari').addClass('active').text(hariDipilih);
        resetSeleksiBaris();
        table.ajax.reload();
    });

    $('.filter-pilih-ruang').click(function(e) {
        e.preventDefault();
        var ruangDipilih = $(this).data('ruang');
        filterRuangAktif = ruangDipilih;
        
        if (ruangDipilih === 'semua') {
            $('#btnGroupDropRuang').removeClass('active').text('Semua Ruang');
        } else {
            $('#btnGroupDropRuang').addClass('active').text(ruangDipilih);
        }
        resetSeleksiBaris();
        table.ajax.reload(); 
    });

    $('.filter-pilih-cabang').click(function(e) {
        e.preventDefault();
        var cabangDipilih = $(this).data('cabang');
        filterCabangAktif = cabangDipilih;
        
        if (cabangDipilih === 'semua') {
            $('#btnGroupDropCabang').removeClass('active').text('Semua Cabang');
        } else {
            $('#btnGroupDropCabang').addClass('active').text(cabangDipilih);
        }
        resetSeleksiBaris();
        table.ajax.reload(); 
    });

    function resetSeleksiBaris() {
        selectedId = null;
        selectedRowData = null;
        $('#btnMessage, #btnEditDP, #btnUbahData, #btnHapusData, #btnSelesai').prop('disabled', true);
    }

    $('#dataTable tbody').on('click', 'tr', function() {
        var data = table.row(this).data();
        if (!data) return;

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            resetSeleksiBaris();
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            selectedId = data.id_reservasi;
            selectedRowData = data; 
            $('#btnMessage, #btnEditDP, #btnUbahData, #btnHapusData, #btnSelesai').prop('disabled', false);
        }
    });

    $('#btnMessage').click(function() {
        if(selectedId && selectedRowData) {
            $('#modalLabel').text('Kirim Pesan WhatsApp (Fonnte)');
            $('#id_reservasi').val(selectedId);
            $('#nama_pelanggan').val(selectedRowData.nama_reservasi);
            $('#whatsapp_pelanggan').val(selectedRowData.whatsapp);
            
            var pesanTemplate = "Halo Kak " + selectedRowData.nama_reservasi + ",\n\n" +
                                "Berikut adalah detail reservasi kaka:\n" +
                                "📅 Tanggal: " + selectedRowData.tgl_bermain + "\n" +
                                "🚪 Ruangan: " + selectedRowData.ruang+ "\n" +
                                "👥 Jumlah Orang: " + selectedRowData.jml_orang + "\n" +
                                "⏰ Jam Kedatangan: " + selectedRowData.w_kedatangan + "\n" +
                                "⌛ Durasi: " + selectedRowData.durasi + "\n\n" +
                                "Sudah masuk waktunya ya, Kak. Mohon maaf, paling lambat dalam 15 menit ke depan akan langsung kami jalankan. Terima kasih";
            
            $('#sendWaMessage').val(pesanTemplate);
            $('#modalRuangan').modal('show');
        }
    });

    $('#formKirimWA').submit(function(e) {
        e.preventDefault();
        var targetNo = $('#whatsapp_pelanggan').val();
        var isiPesan = $('#sendWaMessage').val();
        $('#btnProsesKirim').prop('disabled', true).text('Mengirim...');

        $.ajax({
            url: 'ajax/kirim_wa.php', 
            type: 'POST',
            data: { target: targetNo, message: isiPesan },
            dataType: 'json',
            success: function(response) {
                $('#modalRuangan').modal('hide');
                $('#btnProsesKirim').prop('disabled', false).html('<i class="fab fa-whatsapp"></i> Kirim');
                
                if (response.status === true || response.status === 'true') {
                    var alertSukses = '<div class="alert alert-success alert-dismissible fade show alert-fixed">' +
                                      '<strong>Sukses!</strong> Pesan WhatsApp berhasil dikirim ke pelanggan.' +
                                      '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                                      '</div>';
                    $('#waNotifContainer').html(alertSukses);
                } else {
                    var alertError = '<div class="alert alert-danger alert-dismissible fade show alert-fixed">' +
                                     '<strong>Gagal Kirim!</strong> Respon Fonnte: ' + response.reason +
                                     '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                                     '</div>';
                    $('#waNotifContainer').html(alertError);
                }
                hilangkanAlertOtomatis();
            },
            error: function(xhr) {
                $('#btnProsesKirim').prop('disabled', false).html('<i class="fab fa-whatsapp"></i> Kirim');
                alert("Terjadi kegagalan sistem pada server lokal Anda.");
            }
        });
    });

    $('#btnEditDP').click(function() {
        if(selectedId && selectedRowData) {
            $('#edit_id_reservasi').val(selectedId);
            $('#edit_nama_pelanggan').val(selectedRowData.nama_reservasi);
            $('#dp_berkas').val(''); 
            $('#modalEditDP').modal('show');
        }
    });

    $('#formEditDP').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $('#btnProsesEdit').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: 'ajax/update_dp_reservasi.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                $('#modalEditDP').modal('hide');
                $('#btnProsesEdit').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');

                if(response.status === true) {
                    var alertSukses = '<div class="alert alert-success alert-dismissible fade show alert-fixed">' +
                                      '<strong>Sukses!</strong> Bukti DP milik ' + $('#edit_nama_pelanggan').val() + ' berhasil diperbarui.' +
                                      '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                                      '</div>';
                    $('#waNotifContainer').html(alertSukses);
                    resetSeleksiBaris();
                    table.ajax.reload(null, false);
                } else {
                    alert("Gagal memperbarui berkas: " + response.message);
                }
                hilangkanAlertOtomatis();
            },
            error: function(xhr) {
                $('#btnProsesEdit').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
                alert("Terjadi kegagalan komunikasi data saat unggah file.");
            }
        });
    });

    $('#btnUbahData').click(function() {
        if(selectedId && selectedRowData) {
            $('#ubah_id_reservasi').val(selectedId);
            $('#ubah_nama_pelanggan').val(selectedRowData.nama_reservasi);
            $('#ubah_tgl_bermain').val(selectedRowData.tgl_bermain_raw); 
            $('#ubah_ruang').val(selectedRowData.ruang);
            $('#ubah_w_kedatangan').val(selectedRowData.w_kedatangan.substring(0, 5));
            $('#ubah_durasi').val(selectedRowData.durasi);
            $('#ubah_catatan').val(selectedRowData.catatan);
            
            $('#modalUbahData').modal('show');
        }
    });

    $('#formUbahData').submit(function(e) {
        e.preventDefault();
        $('#btnProsesUbahData').prop('disabled', true).text('Menyimpan...');

        var idRes = $('#ubah_id_reservasi').val();
        var namaBaru = $('#ubah_nama_pelanggan').val();
        var tglBaru = $('#ubah_tgl_bermain').val();
        var ruangBaru = $('#ubah_ruang').val();
        var jamBaru = $('#ubah_w_kedatangan').val() + ' WITA'; 
        var durasiBaru = $('#ubah_durasi').val();
        var catatanBaru = $('#ubah_catatan').val();

        $.ajax({
            url: 'ajax/update_kedatangan_reservasi.php', 
            type: 'POST',
            data: {
                id_reservasi: idRes,
                nama_reservasi: namaBaru,
                tgl_bermain: tglBaru,
                ruang: ruangBaru,
                w_kedatangan: jamBaru,
                durasi: durasiBaru,
                catatan: catatanBaru
            },
            dataType: 'json',
            success: function(response) {
                $('#modalUbahData').modal('hide');
                $('#btnProsesUbahData').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');

                if(response.status === true) {
                    var alertSukses = '<div class="alert alert-success alert-dismissible fade show alert-fixed">' +
                                      '<strong>Sukses!</strong> Data reservasi ' + namaBaru + ' berhasil diubah.' +
                                      '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                                      '</div>';
                    $('#waNotifContainer').html(alertSukses);
                    resetSeleksiBaris();
                    table.ajax.reload(null, false);
                } else {
                    alert("Gagal mengubah data: " + response.message);
                }
                hilangkanAlertOtomatis();
            },
            error: function(xhr) {
                $('#btnProsesUbahData').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
                alert("Terjadi kegagalan komunikasi data dengan server.");
            }
        });
    });

    $('#btnHapusData').click(function() {
        if(selectedId && selectedRowData) {
            if(confirm('Apakah Anda yakin ingin melakukan pembatalan reservasi atas nama ' + selectedRowData.nama_reservasi + '? Data yang dihapus tidak dapat dikembalikan.')) {
                $('#btnHapusData').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Proses...');

                $.ajax({
                    url: 'ajax/hapus_reservasi.php',
                    type: 'POST',
                    data: {
                        id_reservasi: selectedId
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#btnHapusData').html('<i class="fas fa-trash"></i> Hapus Data');
                        if(response.status === true) {
                            var alertSukses = '<div class="alert alert-success alert-dismissible fade show alert-fixed">' +
                                              '<strong>Sukses!</strong> Data pelanggan ' + selectedRowData.nama_reservasi + ' berhasil dihapus.' +
                                              '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                                              '</div>';
                            $('#waNotifContainer').html(alertSukses);
                            resetSeleksiBaris();
                            table.ajax.reload(null, false); 
                        } else {
                            alert("Gagal menghapus data: " + response.message);
                            $('#btnHapusData').prop('disabled', false);
                        }
                        hilangkanAlertOtomatis();
                    },
                    error: function(xhr) {
                        $('#btnHapusData').prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus Data');
                        alert("Terjadi kegagalan komunikasi data dengan server.");
                    }
                });
            }
        }
    });

    $('#btnSelesai').click(function() {
        if(selectedId && selectedRowData) {
            if(confirm('Apakah pelanggan ' + selectedRowData.nama_reservasi + ' sudah masuk ruangan?')) {
                $('#btnSelesai').prop('disabled', true).text('Proses...');

                $.ajax({
                    url: 'ajax/update_status_reservasi.php',
                    type: 'POST',
                    data: {
                        id_reservasi: selectedId,
                        status: 'Masuk Ruangan'
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#btnSelesai').html('<i class="fas fa-check"></i> Selesai');
                        if(response.status === true) {
                            var alertSukses = '<div class="alert alert-success alert-dismissible fade show alert-fixed">' +
                                              '<strong>Sukses!</strong> Pelanggan ' + selectedRowData.nama_reservasi + ' telah masuk ruangan.' +
                                              '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                                              '</div>';
                            $('#waNotifContainer').html(alertSukses);
                            resetSeleksiBaris();
                            table.ajax.reload(null, false); 
                        } else {
                            alert("Gagal mengubah status: " + response.message);
                            $('#btnSelesai').prop('disabled', false);
                        }
                        hilangkanAlertOtomatis();
                    },
                    error: function(xhr) {
                        $('#btnSelesai').prop('disabled', false).html('<i class="fas fa-check"></i> Selesai');
                        alert("Terjadi kegagalan komunikasi data dengan server.");
                    }
                });
            }
        }
    });

    function hilangkanAlertOtomatis() {
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 4000);
    }
    
    hilangkanAlertOtomatis();

    function jalankanSuaraPanggilan(nama, ruang) {
        var kalimat = "Atas nama " + nama + ", sudah saatnya masuk ke ruangan " + ruang;
        isAlertOpen = true;

        function ucapkan() {
            if (!isAlertOpen) {
                window.speechSynthesis.cancel();
                return;
            }
            var speech = new SpeechSynthesisUtterance(kalimat);
            speech.lang = 'id-ID'; 
            speech.rate = 0.85;    
            speech.pitch = 1;
            speech.onend = function() {
                setTimeout(ucapkan, 1000); 
            };
            window.speechSynthesis.speak(speech);
        }

        ucapkan();

        Swal.fire({
            title: "Waktu Masuk Room!",
            text: "Pelanggan atas nama " + nama + " sudah saatnya memasuki " + ruang,
            icon: "info",
            confirmButtonText: "Selesai Mengingatkan",
            confirmButtonColor: "#3085d6",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                isAlertOpen = false;
                window.speechSynthesis.cancel();
            }
        });
    }

    function cekJadwalOtomatis() {
        if (isAlertOpen) return;
        $.ajax({
            url: 'ajax/ajax_cek_panggilan.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.ada_panggilan === true) {
                    jalankanSuaraPanggilan(response.nama, response.ruang);
                    table.ajax.reload(null, false);
                }
            },
            error: function(xhr) {}
        });
    }

    setInterval(cekJadwalOtomatis, 5000);
});
</script>
</body>
</html>