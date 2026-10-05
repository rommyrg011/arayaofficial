<?php 
require_once __DIR__ . '/../function.php'; 

include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>
<style>
    table.dataTable tbody td {
        vertical-align: middle;
    }
</style>
<div class="container-fluid">
    <?php if(isset($_SESSION['notif'])): ?>
        <div class="alert alert-success alert-dismissible fade show alert-fixed">
            <?= $_SESSION['notif']; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php unset($_SESSION['notif']); ?>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 text-center">
            <h6 class="m-0 font-weight-bold" style="font-size:25px;">Target Shift</h6>
            <div class="mt-3">
                <button class="btn btn-primary btn-sm" id="btnTambah"><i class="fas fa-plus"></i> Tambah</button>
                <button class="btn btn-warning btn-sm" id="btnEdit" disabled><i class="fas fa-edit"></i> Edit</button>
                <button class="btn btn-danger btn-sm" id="btnHapus" disabled><i class="fas fa-trash"></i> Hapus</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th class="text-center">Cabang</th>
                            <th class="text-center">Shift</th>
                            <th class="text-center">Target</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modaltarget" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formtarget" action="function.php" method="POST" autocomplete="off">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"></h5>
                    <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_target" id="id_target">
                    <div class="form-group">
                        <label>Cabang</label>
                        <select class="form-control" name="ncabang" id="ncabang" required>
                            <option value="">- Pilih Cabang -</option>
                            <option value="Gambut">Gambut</option>
                            <option value="Beruntung">Beruntung</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Shift</label>
                        <input type="number" class="form-control" name="shift" id="shift" required>
                    </div>
                    <div class="form-group">
                        <label>Target</label>
                        <input type="text" class="form-control" name="target" id="target" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button type="submit" name="simpanTarget" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php 
include 'template/footer.php'; 
include 'template/script.php'; 
?>
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>
<script>
$(document).ready(function() {
    var selectedId = null;
    var selectedCabang = "";
    var selectedShift = "";
    var selectedTarget = "";

    function formatRupiah(angka, prefix) {
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    var targetInput = document.getElementById('target');
    targetInput.addEventListener('keyup', function(e) {
        targetInput.value = formatRupiah(this.value, 'Rp. ');
    });

    if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().destroy();
    }

    var table = $('#dataTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "ajax/ktargetShift.php",
            "type": "POST",
            "error": function(xhr) {}
        },
        "columns": [
            { "data": null, "defaultContent": "", "className": "text-center" },
            { "data": "ncabang", "className": "text-center" },
            { "data": "shift", "className": "text-center" },
            { 
                "data": "target", 
                "className": "text-center",
                "render": function (data, type, row) {
                    return formatRupiah(data, 'Rp. ');
                }
            }
        ],
        "createdRow": function(row, data) {
            $(row).attr('data-id', data.id_target);
        },
        "drawCallback": function (settings) {
            var api = this.api();
            
            if (api.data().length === 0) {
                return;
            }

            var rows = api.rows({page:'current'}).nodes();
            var last = null;
            var trSpan = null;
            var counter = settings._iDisplayStart + 1;

            api.column(1, {page:'current'}).data().each(function (group, i) {
                if (last !== group) {
                    $(rows).eq(i).find('td:eq(0)').html(counter);
                    $(rows).eq(i).find('td:eq(0)').attr('rowspan', 1);
                    $(rows).eq(i).find('td:eq(1)').attr('rowspan', 1);
                    trSpan = $(rows).eq(i);
                    last = group;
                    counter++;
                } else {
                    var rowspan = parseInt(trSpan.find('td:eq(1)').attr('rowspan')) + 1;
                    trSpan.find('td:eq(0)').attr('rowspan', rowspan);
                    trSpan.find('td:eq(1)').attr('rowspan', rowspan);
                    $(rows).eq(i).find('td:eq(0)').hide();
                    $(rows).eq(i).find('td:eq(1)').hide();
                }
            });
        }
    });

    $('#dataTable tbody').on('click', 'tr', function() {
        if ($(this).find('.dataTables_empty').length > 0) return;
        
        var data = table.row(this).data();
        if (!data) return;

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            selectedId = null;
            selectedCabang = "";
            selectedShift = "";
            selectedTarget = "";
            $('#btnEdit, #btnHapus').prop('disabled', true);
        } else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            selectedId = data.id_target;
            selectedCabang = data.ncabang;
            selectedShift = data.shift;
            selectedTarget = data.target;
            $('#btnEdit, #btnHapus').prop('disabled', false);
        }
    });

    $('#btnTambah').click(function() {
        $('#modalLabel').text('Tambah Target Shift');
        $('#formtarget')[0].reset();
        $('#id_target').val('');
        $('#ncabang').val('');
        $('#modaltarget').modal('show');
    });

    $('#btnEdit').click(function() {
        if(selectedId) {
            $('#modalLabel').text('Edit Target Shift');
            $('#id_target').val(selectedId);
            $('#ncabang').val(selectedCabang);
            $('#shift').val(selectedShift);
            $('#target').val(formatRupiah(selectedTarget, 'Rp. '));
            $('#modaltarget').modal('show');
        }
    });

    $('#btnHapus').click(function() {
        if(selectedId) {
            if(confirm('Hapus target untuk cabang ' + selectedCabang + ' shift ' + selectedShift + '?')) {
                window.location.href = 'targetShift?hapus_idTarget=' + selectedId;
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