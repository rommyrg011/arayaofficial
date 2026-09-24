<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= asset(''); ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fa fa-gamepad" aria-hidden="true"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Reservasi</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="<?= asset('reservasi'); ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    
    <hr class="sidebar-divider">

    <div class="sidebar-heading">Transaksi Data</div>

    <li class="nav-item active">
        <a class="nav-link" href="<?= asset('reservasi/reservasi'); ?>">
            <i class="fas fa-briefcase"></i>
            <span>Reservasi</span>
        </a>
    </li>
    
    <li class="nav-item active">
        <a class="nav-link" href="<?= asset('reservasi/riwayat'); ?>">
            <i class="fas fa-history"></i>
            <span>Riwayat</span>
        </a>
    </li>
    <hr class="sidebar-divider">

    <div class="sidebar-heading">Timer Billing</div>
    <li class="nav-item active">
        <a class="nav-link" href="<?= asset('reservasi/timerGambut'); ?>">
            <i class="fas fa-clock"></i>
            <span>Gambut</span>
        </a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="<?= asset('reservasi/timerBeruntung'); ?>">
            <i class="fas fa-clock"></i>
            <span>Beruntung</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">