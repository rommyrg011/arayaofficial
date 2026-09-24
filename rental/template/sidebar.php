<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
    <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-home"></i>
    </div>
    <div class="sidebar-brand-text mx-3">RENTAL</div>
</a>

<hr class="sidebar-divider my-0">

<li class="nav-item active">
    <a class="nav-link" href="<?= asset('rental'); ?>">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span></a>
</li>

<hr class="sidebar-divider">

<div class="sidebar-heading">
    Master Data
</div>

<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#masterData"
        aria-expanded="true" aria-controls="masterData">
        <i class="fas fa-fw fa-cog"></i>
        <span>Data</span>
    </a>
    <div id="masterData" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="<?= asset('rental/kategoriUnit'); ?>">Kategori Unit</a>
            <a class="collapse-item" href="<?= asset('rental/daftarHarga'); ?>">Daftar Harga</a>
            <a class="collapse-item" href="<?= asset('rental/syarat&ketentuan') ?>">Syarat & Ketentuan</a>
            
        </div>
    </div>
</li>

<hr class="sidebar-divider">

<div class="sidebar-heading">
    Transaksi Data
</div>

<li class="nav-item active">
    <a class="nav-link" href="direktoriPenyewa">
        <i class="fas fa-user"></i>
        <span>Direktori Penyewa</span></a>
</li>
<li class="nav-item active">
    <a class="nav-link" href="riwayat">
        <i class="fas fa-history"></i>
        <span>Riwayat</span></a>
</li>

<hr class="sidebar-divider d-none d-md-block">

<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

</ul>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">