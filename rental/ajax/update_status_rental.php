<?php 
require_once __DIR__ . '/../../function.php'; 
header('Content-Type: application/json'); 

// PROSES TRADISIONAL JIKA TOMBOL SELESAI DIKLIK (FORM POST)
if (isset($_POST['proses_selesai_rental'])) {
    $id_yang_selesai = intval($_POST['id_rental_selesai']);
    
    // Eksekusi Update status langsung di halaman ini
    $query_update = "UPDATE rental SET status = 'Selesai' WHERE id_rental = $id_yang_selesai";
    if (mysqli_query($koneksi, $query_update)) {
        echo "<script>
                alert('Data rental berhasil diselesaikan!');
                window.location='?status=sukses';
              </script>";
    } else {
        echo "<script>alert('Gagal memperbarui database: " . mysqli_error($koneksi) . "');</script>";
    }
}

// QUERY UTAMA: Menyaring data agar yang berstatus 'Selesai' tidak dimunculkan lagi di tabel
$sql_rental  = "SELECT * FROM rental WHERE status IS NULL OR status != 'Selesai' ORDER BY id_rental DESC";
$data_rental = mysqli_query($koneksi, $sql_rental);
?>

<?php include 'template/head.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    #dataTable tbody tr.selected {
        background-color: rgba(78, 115, 223, 0.15) !important;
        cursor: pointer;
    }
    #dataTable tbody tr {
        cursor: pointer;
    }
</style>

<body id="page-top">

    <div id="wrapper">
       <?php include 'template/sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'template/topbar.php'; ?>

                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-center" style="font-size:25px;">Direktori Penyewa</h6>
                            <div class="text-center my-2">
                                
                                <form action="" method="POST" id="formSelesaiManual" style="display:inline;">
                                    <input type="hidden" name="id_rental_selesai" id="id_rental_selesai">
                                    <button type="button" class="btn btn-success btn-sm btn-mobile btn-xs" id="btnSelesai" disabled>
                                        <i class="fas fa-check"></i> Selesai
                                    </button>
                                    <input type="hidden" name="proses_selesai_rental" value="1">
                                </form>

                                <button class="btn btn-warning btn-sm btn-mobile btn-xs" id="btnKurir" disabled>
                                    <i class="fas fa-shipping-fast"></i> Kurir
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%;">No</th>
                                            <th>Nama Perental</th>
                                            <th>Durasi Sewa</th>
                                            <th>Jaminan</th>
                                            <th>WhatsApp</th>
                                            <th>Alamat Lengkap</th>
                                            <th>Catatan</th>
                                            <th class="text-center" style="width: 15%;">Navigasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (mysqli_num_rows($data_rental) > 0) {
                                            $no = 1;
                                            while ($row = mysqli_fetch_assoc($data_rental)) {
                                                
                                                $data_sharelok = trim($row['sharelok']);
                                                $link_final = "";

                                                if (!empty($data_sharelok)) {
                                                    if (strpos($data_sharelok, 'google') !== false) {
                                                        if (strpos($data_sharelok, 'http') === false) {
                                                            $link_final = "https://" . $data_sharelok;
                                                        } else {
                                                            $link_final = $data_sharelok;
                                                        }
                                                    } else {
                                                        $link_final = "https://www.google.com/maps/dir/?api=1&destination=" . urlencode($data_sharelok);
                                                    }
                                                }
                                        ?>
                                                <tr data-id="<?= $row['id_rental']; ?>" 
                                                    data-nama="<?= htmlspecialchars($row['nama_perental']); ?>"
                                                    data-wa="<?= htmlspecialchars($row['wa']); ?>"
                                                    data-alamat="<?= htmlspecialchars($row['alamat_lengkap']); ?>">
                                                    
                                                    <td class="text-center"><?= $no++; ?></td>
                                                    <td class="font-weight-bold text-capitalize"><?= htmlspecialchars($row['nama_perental']); ?></td>
                                                    <td><?= htmlspecialchars($row['durasi_sewa']); ?></td>
                                                    <td><span class="badge badge-secondary"><?= htmlspecialchars($row['jaminan']); ?></span></td>
                                                    <td>
                                                        <a href="https://wa.me/<?= str_replace([' ', '-', '+'], '', $row['wa']); ?>" target="_blank" class="text-success font-weight-bold">
                                                            <i class="fab fa-whatsapp"></i> <?= htmlspecialchars($row['wa']); ?>
                                                        </a>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['alamat_lengkap']); ?></td>
                                                    <td><small class="text-muted"><?= !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : '-'; ?></small></td>
                                                    <td class="text-center">
                                                        <?php if (!empty($link_final)): ?>
                                                            <a href="<?= $link_final; ?>" target="_blank" class="btn btn-info btn-sm font-weight-bold text-white shadow-sm btn-block">
                                                                <i class="fas fa-route"></i> Mulai Rute
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="badge badge-light text-muted"><i>Tidak ada sharelok</i></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                        <?php 
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'template/footer.php'; ?>
        </div>
    </div>

    <div class="modal fade" id="modalKurir" tabindex="-1" role="dialog" aria-labelledby="modalKurirLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formTunjukKurir">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKurirLabel"><i class="fas fa-shipping-fast text-warning"></i> Atur Kurir Pengantaran</h5>
                        <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_rental" id="mdl_id_rental">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Perental</label>
                            <input type="text" class="form-control" id="mdl_nama_perental" readonly>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Alamat Pengiriman</label>
                            <textarea class="form-control" id="mdl_alamat_lengkap" rows="3" readonly></textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Pilih Personel Kurir / Staf</label>
                            <select class="form-control text-dark font-weight-bold" name="nama_kurir" id="mdl_nama_kurir" required>
                                <option value="" disabled selected>-- Pilih Kurir Tugas --</option>
                                <option value="Kurir Lapangan A">Kurir Lapangan A</option>
                                <option value="Kurir Lapangan B">Kurir Lapangan B</option>
                                <option value="Staf Store Hub">Staf Store Hub</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button type="submit" id="btnProsesKurir" class="btn btn-warning font-weight-bold text-dark">Konfirmasi Tugas</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include 'template/script.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        var selectedId = null;
        var selectedRowData = null;
        var table = $('#dataTable').DataTable();

        // Fitur Seleksi Baris Tabel
        $('#dataTable tbody').on('click', 'tr', function() {
            if ($(this).find('td').hasClass('dataTables_empty') || $(this).text().includes('Belum ada data')) {
                return;
            }

            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                selectedId = null;
                selectedRowData = null;
                $('#btnSelesai, #btnKurir').prop('disabled', true);
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                
                selectedId = $(this).attr('data-id');
                selectedRowData = {
                    id: $(this).attr('data-id'),
                    nama: $(this).attr('data-nama'),
                    alamat: $(this).attr('data-alamat')
                };
                
                $('#btnSelesai, #btnKurir').prop('disabled', false);
                // Masukkan ID terpilih ke input form tersembunyi
                $('#id_rental_selesai').val(selectedId);
            }
        });

        // Event Tombol Kurir Klik -> Buka Modal
        $('#btnKurir').click(function() {
            if(selectedId && selectedRowData) {
                $('#mdl_id_rental').val(selectedRowData.id);
                $('#mdl_nama_perental').val(selectedRowData.nama);
                $('#mdl_alamat_lengkap').val(selectedRowData.alamat);
                $('#modalKurir').modal('show');
            }
        });

        // Event Klik Tombol Selesai (Menggunakan Konfirmasi SweetAlert2 sebelum Submit Form POST)
        $('#btnSelesai').click(function() {
            if(selectedId && selectedRowData) {
                Swal.fire({
                    title: 'Selesaikan Rental?',
                    text: 'Apakah unit rental atas nama ' + selectedRowData.nama + ' sudah dikembalikan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Selesai!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit form POST secara langsung tanpa AJAX external file
                        $('#formSelesaiManual').submit();
                    }
                });
            }
        });
    });
    </script>
</body>
</html>