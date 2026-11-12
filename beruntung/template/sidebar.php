<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= asset(''); ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fa fa-calendar-check" aria-hidden="true"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Beruntung</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="<?= asset('beruntung'); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    
    <hr class="sidebar-divider">

    <div class="sidebar-heading">Transaksi Data</div>

    <li class="nav-item active">
        <a class="nav-link" href="<?= asset('beruntung/reservasi'); ?>">
            <i class="fas fa-briefcase"></i>
            <span>Reservasi</span>
        </a>
    </li>
    
    <li class="nav-item active">
        <a class="nav-link" href="<?= asset('beruntung/riwayat'); ?>">
            <i class="fas fa-history"></i>
            <span>Riwayat</span>
        </a>
    </li>
    <hr class="sidebar-divider">

    <div class="sidebar-heading">Timer Billing</div>
    <li class="nav-item active">
        <a class="nav-link" href="<?= asset('beruntung/timerBeruntung'); ?>">
            <i class="fas fa-clock"></i>
            <span>Timer</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="sidebar-heading">Promo</div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#voucher" aria-expanded="true" aria-controls="voucher">
            <i class="fas fa-solid fa-folder"></i>
            <span>Voucher</span>
        </a>
        <div id="voucher" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?= asset('beruntung/kodeVoucher'); ?>">Kode Voucher</a>
                <a class="collapse-item" href="<?= asset('beruntung/pelangganVoucher'); ?>">Pelanggan Voucher</a>
                <a class="collapse-item" href="<?= asset('beruntung/riwayatPelanggan'); ?>">Riwayat Pelanggan</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">