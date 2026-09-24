<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../function.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpanVoucher'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nama_promo = $_POST['nama_promo'];
    $kode_prefix = strtoupper($_POST['kode_prefix']);
    $kuota = intval($_POST['kuota']);
    $harga_normal = preg_replace('/[^0-9]/', '', $_POST['harga_normal']);
    $potongan = preg_replace('/[^0-9]/', '', $_POST['potongan']);
    $keterangan = $_POST['keterangan'];
    $best_value = $_POST['best_value'];
    $status = $_POST['status'];

    if ($id == 0) {
        if ($koneksi instanceof PDO) {
            $stmt = $koneksi->prepare("INSERT INTO promos (nama_promo, kode_prefix, kuota, harga_normal, potongan, keterangan, best_value, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama_promo, $kode_prefix, $kuota, $harga_normal, $potongan, $keterangan, $best_value, $status]);
        } else {
            $nama_p   = mysqli_real_escape_string($koneksi, $nama_promo);
            $kode_p   = mysqli_real_escape_string($koneksi, $kode_prefix);
            $hn       = mysqli_real_escape_string($koneksi, $harga_normal);
            $potg     = mysqli_real_escape_string($koneksi, $potongan);
            $ket      = mysqli_real_escape_string($koneksi, $keterangan);
            $bv       = mysqli_real_escape_string($koneksi, $best_value);
            $stat     = mysqli_real_escape_string($koneksi, $status);
            mysqli_query($koneksi, "INSERT INTO promos (nama_promo, kode_prefix, kuota, harga_normal, potongan, keterangan, best_value, status) VALUES ('$nama_p', '$kode_p', $kuota, '$hn', '$potg', '$ket', '$bv', '$stat')");
        }
        $_SESSION['notif'] = "Data Voucher baru berhasil ditambahkan!";
    } else {
        if ($koneksi instanceof PDO) {
            $stmt = $koneksi->prepare("UPDATE promos SET nama_promo = ?, kode_prefix = ?, harga_normal = ?, potongan = ?, keterangan = ?, best_value = ?, status = ? WHERE id = ?");
            $stmt->execute([$nama_promo, $kode_prefix, $harga_normal, $potongan, $keterangan, $best_value, $status, $id]);
        } else {
            $nama_p   = mysqli_real_escape_string($koneksi, $nama_promo);
            $kode_p   = mysqli_real_escape_string($koneksi, $kode_prefix);
            $hn       = mysqli_real_escape_string($koneksi, $harga_normal);
            $potg     = mysqli_real_escape_string($koneksi, $potongan);
            $ket      = mysqli_real_escape_string($koneksi, $keterangan);
            $bv       = mysqli_real_escape_string($koneksi, $best_value);
            $stat     = mysqli_real_escape_string($koneksi, $status);
            mysqli_query($koneksi, "UPDATE promos SET nama_promo = '$nama_p', kode_prefix = '$kode_p', harga_normal = '$hn', potongan = '$potg', keterangan = '$ket', best_value = '$bv', status = '$stat' WHERE id = $id");
        }
        $_SESSION['notif'] = "Data Voucher berhasil diperbarui!";
    }
    header("Location: kodeVoucher.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpanTambahKuota'])) {
    $id = intval($_POST['id']);
    $jumlah_tambahan = intval($_POST['jumlah_tambahan']);

    if ($id > 0 && $jumlah_tambahan > 0) {
        if ($koneksi instanceof PDO) {
            $stmt = $koneksi->prepare("UPDATE promos SET kuota = kuota + ? WHERE id = ?");
            $stmt->execute([$jumlah_tambahan, $id]);
        } else {
            mysqli_query($koneksi, "UPDATE promos SET kuota = kuota + $jumlah_tambahan WHERE id = $id");
        }
        $_SESSION['notif'] = "Kuota voucher berhasil ditambahkan!";
    }
    header("Location: kodeVoucher.php");
    exit;
}

if (isset($_GET['hapus_idVoucher'])) {
    $id_hapus = intval($_GET['hapus_idVoucher']);

    if ($koneksi instanceof PDO) {
        $stmt = $koneksi->prepare("DELETE FROM promos WHERE id = ?");
        $stmt->execute([$id_hapus]);
    } else {
        mysqli_query($koneksi, "DELETE FROM promos WHERE id = $id_hapus");
    }
    
    $_SESSION['notif'] = "Data Voucher berhasil dihapus!";
    header("Location: kodeVoucher.php");
    exit;
}

include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>

<style>
    #dataTableVoucher tbody tr.selected {
        background-color: rgba(231, 74, 59, 0.15) !important; 
        color: #333 !important;
    }
    #dataTableVoucher tbody tr.selected code {
        background-color: rgba(255, 255, 255, 0.8) !important;
    }
    .preview-card {
        border: 2px dashed #4e73df;
        border-radius: 8px;
        background: #f8f9fc;
    }
</style>

<div class="container-fluid">
    <div id="notifAjaxPlaceholder"></div>

    <?php if(isset($_SESSION['notif'])): ?>
        <div class="alert alert-success alert-dismissible fade show alert-fixed">
            <?= $_SESSION['notif']; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php unset($_SESSION['notif']); ?>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 text-center">
            <h6 class="m-0 font-weight-bold text-primary" style="font-size:25px;">Manajemen Data Voucher</h6>
            <div class="mt-3">
                <button class="btn btn-primary btn-sm" id="btnTambah"><i class="fas fa-plus"></i> Tambah</button>
                <button class="btn btn-info btn-sm" id="btnTambahKuota" disabled><i class="fas fa-layer-group"></i> Tambah Kuota</button>
                <button class="btn btn-success btn-sm" id="btnStatus" disabled><i class="fas fa-edit"></i> Edit / Status</button>
                <button class="btn btn-danger btn-sm" id="btnHapus" disabled><i class="fas fa-trash"></i> Hapus</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTableVoucher" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Nama Promo</th>
                            <th>Prefix / Kode</th>
                            <th class="text-center">Kuota</th>
                            <th class="text-center">Harga</th>
                            <th class="text-center">Best Value</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVoucher" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form id="formVoucher" action="kodeVoucher.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"></h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id_voucher">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="preview-card p-3 text-center">
                                <h6 class="font-weight-bold text-primary text-uppercase mb-1">Preview Card Harga</h6>
                                <h5 class="font-weight-bold mb-1" id="preview_nama">Nama Promo</h5>
                                <p class="mb-0 text-muted" style="text-decoration: line-through; font-size: 14px;" id="preview_harga_normal">Rp. 0</p>
                                <h4 class="font-weight-bold text-success mb-1" id="preview_harga_diskon">Rp. 0</h4>
                                <span class="badge badge-danger">Hemat <span id="preview_potongan">Rp. 0</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_promo">Nama Promo</label>
                                <input type="text" class="form-control" name="nama_promo" id="nama_promo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode_prefix">Prefix / Kode Promo</label>
                                <input type="text" class="form-control text-uppercase" name="kode_prefix" id="kode_prefix" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kuota">Kuota</label>
                                <input type="number" class="form-control" name="kuota" id="kuota" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="harga_normal">Harga Normal</label>
                                <input type="text" class="form-control rupiah-input" name="harga_normal" id="harga_normal" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="potongan">Potongan</label>
                                <input type="text" class="form-control rupiah-input" name="potongan" id="potongan" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan / Fasilitas (Pisahkan dengan baris baru / Enter)</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="4" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="best_value">Tandai sebagai Best Value?</label>
                                <select class="form-control" name="best_value" id="best_value" required>
                                    <option value="Tidak">Tidak</option>
                                    <option value="Ya">Ya (Highlight)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" name="simpanVoucher" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalTambahKuota" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <form id="formTambahKuota" action="kodeVoucher.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kuota Voucher</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id_voucher_kuota">
                    <input type="text" class="form-control-plaintext font-weight-bold text-primary mb-2" id="nama_promo_kuota" readonly>
                    <div class="form-group">
                        <label for="jumlah_tambahan">Jumlah Tambahan</label>
                        <input type="number" class="form-control" name="jumlah_tambahan" id="jumlah_tambahan" min="1" placeholder="Isi Jumlah Kuota" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" name="simpanTambahKuota" class="btn btn-success">Tambah</button>
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
function formatRupiah(angka, prefix){
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
    split           = number_string.split(','),
    sisa            = split[0].length % 3,
    rupiah          = split[0].substr(0, sisa),
    ribuan          = split[0].substr(sisa).match(/\d{3}/gi);

    if(ribuan){
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}

function updatePreview() {
    let nama = $('#nama_promo').val() || 'Nama Promo';
    let valNormal = $('#harga_normal').val() || '';
    let valPotongan = $('#potongan').val() || '';
    
    let h_normal = parseInt(valNormal.replace(/[^0-9]/g, '')) || 0;
    let pot = parseInt(valPotongan.replace(/[^0-9]/g, '')) || 0;
    
    let diskon = h_normal - pot;
    if(diskon < 0) diskon = 0;

    $('#preview_nama').text(nama);
    $('#preview_harga_normal').text('Rp. ' + h_normal.toLocaleString('id-ID'));
    $('#preview_harga_diskon').text('Rp. ' + diskon.toLocaleString('id-ID'));
    $('#preview_potongan').text('Rp. ' + pot.toLocaleString('id-ID'));
}

$(document).ready(function() {
    $(document).on('keyup', '.rupiah-input', function(){
        $(this).val(formatRupiah($(this).val(), 'Rp. '));
        updatePreview();
    });

    $(document).on('keyup', '#nama_promo', function(){
        updatePreview();
    });

    var selectedId = null;
    var selectedNama = "";
    var selectedPrefix = "";
    var selectedKuota = "";
    var selectedHargaNormal = "";
    var selectedPotongan = "";
    var selectedKeterangan = "";
    var selectedBestValue = "";
    var selectedStatus = "";

    var table = $('#dataTableVoucher').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/kkodeVoucher.php",
            "type": "POST"
        },
        "columns": [
            { "data": "no", "className": "text-center" },
            { "data": "nama_promo" },
            { "data": "kode_prefix", "className": "text-center font-weight-bold" },
            { "data": "kuota", "className": "text-center" },
            { "data": "harga_card", "className": "text-center" },
            { "data": "best_value", "className": "text-center" },
            { "data": "status", "className": "text-center" }
        ],
        "createdRow": function(row, data) {
            $(row).attr('data-id', data.id);
        }
    });

    $('#dataTableVoucher tbody').on('click', 'tr', function() {
        var tr = $(this).closest('tr');
        var data = table.row(tr).data();
        if (!data) return;

        if (tr.hasClass('selected')) {
            tr.removeClass('selected');
            selectedId = null; 
            $('#btnStatus, #btnHapus, #btnTambahKuota').prop('disabled', true);
        } else {
            table.$('tr.selected').removeClass('selected');
            tr.addClass('selected');
            selectedId = data.id;
            selectedNama = data.nama_promo;
            selectedPrefix = data.kode_prefix;
            selectedKuota = data.kuota_raw;
            selectedHargaNormal = data.harga_normal_raw;
            selectedPotongan = data.potongan_raw;
            selectedKeterangan = data.keterangan_raw;
            selectedBestValue = data.best_value_raw;
            selectedStatus = data.status_raw;
            
            $('#btnStatus, #btnHapus, #btnTambahKuota').prop('disabled', false);
        }
    });

    $('#btnTambah').click(function() {
        $('#modalLabel').text('Tambah Data Voucher Baru');
        $('#formVoucher')[0].reset();
        $('#id_voucher').val('0');
        
        $('#nama_promo').prop('readonly', false);
        $('#kode_prefix').prop('readonly', false);
        $('#kuota').prop('readonly', false).prop('disabled', false);
        $('#harga_normal').prop('readonly', false);
        $('#potongan').prop('readonly', false);
        $('#keterangan').prop('readonly', false);
        $('#best_value').prop('disabled', false);
        $('#status').prop('disabled', false);
        
        updatePreview();
        $('#modalVoucher').modal('show');
    });

    $('#btnStatus').click(function() {
        if(selectedId) {
            $('#modalLabel').text('Edit Data Voucher');
            $('#id_voucher').val(selectedId); 
            $('#nama_promo').val(selectedNama).prop('readonly', false);
            $('#kode_prefix').val(selectedPrefix).prop('readonly', false);
            $('#kuota').val(selectedKuota).prop('readonly', true); 
            $('#harga_normal').val(formatRupiah(selectedHargaNormal.toString(), 'Rp. ')).prop('readonly', false); 
            $('#potongan').val(formatRupiah(selectedPotongan.toString(), 'Rp. ')).prop('readonly', false); 
            $('#keterangan').val(selectedKeterangan).prop('readonly', false);
            $('#best_value').val(selectedBestValue).prop('disabled', false);
            $('#status').val(selectedStatus).prop('disabled', false);
            
            updatePreview();
            $('#modalVoucher').modal('show');
        }
    });

    $('#btnTambahKuota').click(function() {
        if(selectedId) {
            $('#formTambahKuota')[0].reset();
            $('#id_voucher_kuota').val(selectedId);
            $('#nama_promo_kuota').val(selectedNama); 
            $('#modalTambahKuota').modal('show');
        }
    });

    $('#btnHapus').click(function() {
        if(selectedId) {
            if(confirm('Apakah Anda yakin ingin menghapus data promo "' + selectedNama + '"?')) {
                window.location.href = 'kodeVoucher.php?hapus_idVoucher=' + selectedId;
            }
        }
    });
});

window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 2000);
</script>