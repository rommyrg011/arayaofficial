<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="./">
    <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-address-book"></i>
    </div>
    <div class="sidebar-brand-text mx-3">KARYAWAN</div>
</a>

<!-- Divider -->
<hr class="sidebar-divider my-0">
<li class="nav-item active">
    <a class="nav-link" href="<?= asset('karyawan'); ?>">
        <i class="fas fa-money-bill-wave"></i>
        <span>Pendapatan</span></a>
</li>

<li class="nav-item active">
    <a class="nav-link" href="<?= asset('karyawan/datadiri'); ?>">
        <i class="fas fa-user"></i>
        <span>Data Diri</span></a>
</li>


<!-- Divider -->
<hr class="sidebar-divider">
<div class="sidebar-heading">Sewa PlayStation</div>
<li class="nav-item active">
    <a class="nav-link" href="<?= asset('karyawan/direktoriPenyewa'); ?>">
        <i class="fas fa-user"></i>
        <span>Direktori Penyewa</span></a>
</li>
<!-- Divider -->
<hr class="sidebar-divider">
<li class="nav-item active">
    <a class="nav-link" href="<?= asset('karyawan/riwayat'); ?>">
        <i class="fas fa-history"></i>
        <span>Riwayat</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

</ul>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">