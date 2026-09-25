<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= asset('admin'); ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="<?= asset('admin'); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Master Data</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#masterData" aria-expanded="true" aria-controls="masterData">
            <i class="fas fa-solid fa-folder"></i>
            <span>Data</span>
        </a>
        <div id="masterData" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= asset('admin/pengguna'); ?>">Pengguna</a>
                <a class="collapse-item" href="<?= asset('admin/targetShift'); ?>">Target Shift</a>
                <a class="collapse-item" href="<?= asset('admin/kategoriRuangan'); ?>">Kategori Ruangan</a>
                <a class="collapse-item" href="<?= asset('admin/kategoriUnit'); ?>">Kategori Unit</a>
                <a class="collapse-item" href="<?= asset('admin/daftarHarga'); ?>">Daftar Harga</a>
                <a class="collapse-item" href="<?= asset('admin/pembayaran'); ?>">Pembayaran</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Transaksi Data</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#voucher" aria-expanded="true" aria-controls="voucher">
            <i class="fas fa-solid fa-folder"></i>
            <span>Voucher</span>
        </a>
        <div id="voucher" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= asset('admin/kodeVoucher'); ?>">Kode Voucher</a>
                <a class="collapse-item" href="<?= asset('admin/pelangganVoucher'); ?>">Pelanggan Voucher</a>
                <a class="collapse-item" href="<?= asset('admin/riwayatPelanggan'); ?>">Riwayat Pelanggan</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#pengeluaran" aria-expanded="true" aria-controls="pengeluaran">
            <i class="fas fa-solid fa-folder"></i>
            <span>Pengeluaran</span>
        </a>
        <div id="pengeluaran" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= asset('admin/adminTransfer'); ?>">Admin Transfer</a>
                <a class="collapse-item" href="<?= asset('admin/netflix'); ?>">Netflix</a>
                <a class="collapse-item" href="<?= asset('admin/pdam'); ?>">PDAM</a>
                <a class="collapse-item" href="<?= asset('admin/wifi'); ?>">Wifi</a>
                <a class="collapse-item" href="<?= asset('admin/service'); ?>">Service</a>
                <a class="collapse-item" href="<?= asset('admin/pengeluaranGaji'); ?>">Gaji Karyawan</a>
                <a class="collapse-item" href="<?= asset('admin/pengeluaranSewa'); ?>">Sewa Toko</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="sidebar-heading">Kumpulan Laporan</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#laporanData" aria-expanded="true" aria-controls="laporanData">
            <i class="fas fa-solid fa-folder"></i>
            <span>Laporan</span>
        </a>
        <div id="laporanData" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= asset('admin/rental'); ?>">Rental</a>
                <a class="collapse-item" href="<?= asset('admin/riwayatReservasi'); ?>">Reservasi</a>
                <a class="collapse-item" href="<?= asset('admin/omsetPerstaff'); ?>">Omset Perstaff</a>
                <a class="collapse-item" href="<?= asset('admin/labaBersih'); ?>">Laba Bersih</a>
            </div>
        </div>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">