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
            <h6 class="m-0 font-weight-bold" style="font-size:25px;">Riwayat Pelanggan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="dataTable">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Whatsapp</th>
                            <th>Total Pakai Voucher</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKirimVoucher" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formKirimVoucher">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Voucher WhatsApp</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="kv_nama" name="nama">
                    <div class="form-group">
                        <label>No. WA Pelanggan</label>
                        <input type="text" class="form-control" id="kv_whatsapp" name="whatsapp" readonly>
                    </div>
                    <div class="form-group">
                        <label>Pilihan Promo</label>
                        <select class="form-control" id="kv_promo" name="promo" required>
                            <option value="Voucher Reguler">Voucher Reguler</option>
                            <option value="Voucher vip">Voucher VIP</option>
                            <option value="Voucher Premiere">Voucher Premiere</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kode Voucher</label>
                        <input type="text" class="form-control font-weight-bold text-success" id="kv_kode" name="kode" readonly>
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Pesan WhatsApp</label>
                        <textarea class="form-control" id="kv_pesan" name="pesan" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btnProsesKirimVoucher" class="btn btn-success"><i class="fab fa-whatsapp"></i> Buat & Kirim</button>
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
    // Inisialisasi DataTables
    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/kriwayatPelanggan.php",
            "type": "POST"
        },
        "columns": [
            { "data": "no", "className": "text-center" },
            { "data": "whatsapp", "className": "text-center" },
            { "data": "total_pakai", "className": "text-center font-weight-bold text-primary" },
            { "data": "aksi", "className": "text-center", "orderable": false }
        ]
    });

    // Fungsi Generate Kode Voucher
    function generateVoucherKode(jenisPromo) {
        var prefix = "REG-";
        if (jenisPromo === 'Voucher vip') prefix = "VIP-";
        if (jenisPromo === 'Voucher Premiere') prefix = "PMR-";
        
        var randomString = Math.random().toString(36).substring(2, 7).toUpperCase();
        return prefix + randomString;
    }

    // Fungsi Mendapatkan Waktu Sapaan (Pagi/Siang/Sore/Malam)
    function getWaktu() {
        var jam = new Date().getHours();
        if (jam >= 4 && jam < 11) return "pagi";
        if (jam >= 11 && jam < 15) return "siang";
        if (jam >= 15 && jam < 18) return "sore";
        return "malam";
    }

    // Fungsi Update Template Pesan (Dengan Sapaan Dinamis & Backtick untuk Tap-to-copy)
    function updateTemplatePesan(promo, kode) {
        var waktu = getWaktu();
        var pesan = "Selamat " + waktu + ",\n\nNomor Anda berhak mendapatkan " + promo + ".\nUntuk kode vouchernya adalah sebagai berikut:\n\n`" + kode + "`\n\nSilahkan masukkan kode voucher diatas ke form reservasi Araya Gamestation, berikut linknya :\n\nhttps://arayaofficial.site/reservasi-araya";
        $('#kv_pesan').val(pesan);
    }

    // Aksi Klik Tombol Kirim Voucher
    $('#dataTable tbody').on('click', '.btn-kirim-voucher', function() {
        var data = table.row($(this).closest('tr')).data();$('#kv_whatsapp').val(data.whatsapp);
        $('#kv_nama').val(data.nama ? data.nama : 'Pelanggan');
        $('#kv_promo').val('Voucher Reguler'); 
        
        var kodeBaru = generateVoucherKode('Voucher Reguler');
        $('#kv_kode').val(kodeBaru);
        updateTemplatePesan('Voucher Reguler', kodeBaru);

        $('#modalKirimVoucher').modal('show');
    });

    // Aksi Ganti Pilihan Promo
    $('#kv_promo').change(function() {
        var jenisPromo = $(this).val();
        var kodeBaru = generateVoucherKode(jenisPromo);
        $('#kv_kode').val(kodeBaru);
        updateTemplatePesan(jenisPromo, kodeBaru);
    });

    // Aksi Submit Form
    $('#formKirimVoucher').submit(function(e) {
        e.preventDefault();
        $('#btnProsesKirimVoucher').prop('disabled', true).text('Memproses...');

        $.ajax({
            url: 'ajax/proses_kirim_voucher.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $('#modalKirimVoucher').modal('hide');
                $('#btnProsesKirimVoucher').prop('disabled', false).html('<i class="fab fa-whatsapp"></i> Buat & Kirim');
                
                if (response.status === true) {
                    var alertSukses = '<div class="alert alert-success alert-dismissible fade show">' +
                                      '<strong>Sukses!</strong> ' + response.message +
                                      '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                                      '</div>';
                    $('#waNotifContainer').html(alertSukses);
                } else {
                    var alertError = '<div class="alert alert-danger alert-dismissible fade show">' +
                                     '<strong>Gagal!</strong> ' + response.message +
                                     '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                                     '</div>';
                    $('#waNotifContainer').html(alertError);
                }
                
                setTimeout(function() {
                    $(".alert").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove(); 
                    });
                }, 5000);
            },
            error: function(xhr) {
                $('#btnProsesKirimVoucher').prop('disabled', false).html('<i class="fab fa-whatsapp"></i> Buat & Kirim');
                console.error(xhr.responseText);
                alert("Gagal memproses data. Cek Console (F12) untuk detail respon server.");
            }
        });
    });
});
</script>
</body>
</html>