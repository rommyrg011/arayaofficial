<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../function.php'; 

if (isset($_GET['hapus_idVoucher'])) {
    $id_hapus = intval($_GET['hapus_idVoucher']);
    if ($koneksi instanceof PDO) {
        $stmt = $koneksi->prepare("DELETE FROM customer_vouchers WHERE id = ?");
        $stmt->execute([$id_hapus]);
    } else {
        mysqli_query($koneksi, "DELETE FROM customer_vouchers WHERE id = $id_hapus");
    }
    
    $_SESSION['notif'] = "Data klaim voucher pelanggan berhasil dihapus!";
    header("Location: pelangganVoucher.php");
    exit;
}

if (isset($_GET['reset_data'])) {
    if ($koneksi instanceof PDO) {
        $koneksi->query("DELETE FROM customer_vouchers");
    } else {
        mysqli_query($koneksi, "DELETE FROM customer_vouchers");
    }
    
    $_SESSION['notif'] = "Semua data klaim voucher pelanggan berhasil direset!";
    header("Location: pelangganVoucher.php");
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
            <h6 class="m-0 font-weight-bold text-primary" style="font-size:25px;">Manajemen Voucher Pelanggan</h6>
            <div class="mt-3">
                <button class="btn btn-warning btn-sm mr-2" id="btnReset"><i class="fas fa-sync"></i> Reset Data</button>
                <button class="btn btn-danger btn-sm" id="btnHapus" disabled><i class="fas fa-trash"></i> Hapus Data Terpilih</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTableVoucher" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Nama Pelanggan</th>
                            <th>No. WhatsApp</th>
                            <th>Kode Voucher</th>
                            <th class="text-center">Status Pakai</th>
                            <th>Tanggal Klaim</th>
                            <th>Tanggal Pakai</th>
                        </tr>
                    </thead>
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
    var selectedNama = "";

    var table = $('#dataTableVoucher').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/kpelangganVoucher.php",
            "type": "POST"
        },
        "columns": [
            { "data": "no", "className": "text-center" },
            { "data": "nama_pelanggan" },
            { "data": "nomor_whatsapp" },
            { "data": "kode_voucher", "className": "text-center font-weight-bold" },
            { "data": "status_pakai", "className": "text-center" },
            { "data": "tanggal_klaim" },
            { "data": "tanggal_pakai" }
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
            $('#btnHapus').prop('disabled', true);
        } else {
            table.$('tr.selected').removeClass('selected');
            tr.addClass('selected');
            selectedId = data.id;
            selectedNama = data.nama_pelanggan;
            $('#btnHapus').prop('disabled', false);
        }
    });

    $('#btnHapus').click(function() {
        if(selectedId) {
            if(confirm('Apakah Anda yakin ingin menghapus data voucher milik "' + selectedNama + '"?')) {
                window.location.href = 'pelangganVoucher.php?hapus_idVoucher=' + selectedId;
            }
        }
    });

    $('#btnReset').click(function() {
        if(confirm('PERINGATAN: Anda akan mereset dan menghapus semua data pelanggan. Apakah anda yakin ingin melanjutkan?')) {
            window.location.href = 'pelangganVoucher.php?reset_data=true';
        }
    });
});

window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 2000);
</script>