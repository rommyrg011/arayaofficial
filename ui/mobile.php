<?php
if (!isset($koneksi)) {
    require_once 'function.php';
}

$jumlah_promo_aktif = 0;
if ($koneksi instanceof PDO) {
    $stmt_promo = $koneksi->query("SELECT COUNT(*) FROM promos WHERE status = 'Aktif' AND kuota > 0");
    $jumlah_promo_aktif = $stmt_promo->fetchColumn();
} else {
    $res_promo = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM promos WHERE status = 'Aktif' AND kuota > 0");
    if ($res_promo) {
        $row_promo = mysqli_fetch_assoc($res_promo);
        $jumlah_promo_aktif = $row_promo['total'];
    }
}
?>

<style>
.mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 64px;
    background-color: #ffffff;
    border-radius: 20px 20px 0 0;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.06);
    z-index: 1000;
    overflow: visible !important;
}

.mobile-bottom-nav ul {
    display: flex;
    justify-content: space-around;
    align-items: center;
    margin: 0;
    padding: 0 8px;
    height: 100%;
    list-style: none;
    overflow: visible !important;
}

.mobile-bottom-nav li {
    flex: 1;
    text-align: center;
}

.mobile-bottom-nav a {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #6c757d;
    font-size: 11px;
    font-weight: 500;
    gap: 2px;
    transition: color 0.2s ease;
}

.mobile-bottom-nav a i {
    font-size: 19px;
}

.mobile-bottom-nav li.active a,
.mobile-bottom-nav a.active {
    color: #16a34a;
}

.mobile-bottom-nav li.nav-item-featured {
    position: relative;
    height: 100%;
}

.mobile-bottom-nav li.nav-item-featured a {
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 62px;
    height: 62px;
    background-color: #16a34a;
    border-radius: 50%;
    border: none;
    box-shadow: 0 8px 18px rgba(22, 163, 74, 0.35);
    color: #ffffff !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1px;
    z-index: 1001;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.mobile-bottom-nav li.nav-item-featured a:active {
    transform: translateX(-50%) scale(0.94);
    box-shadow: 0 4px 10px rgba(22, 163, 74, 0.25);
}

.mobile-bottom-nav li.nav-item-featured a i {
    font-size: 21px;
    color: #ffffff;
}

.mobile-bottom-nav li.nav-item-featured a span {
    font-size: 9px;
    font-weight: 600;
    color: #ffffff;
    line-height: 1;
}

.cart-badge {
    position: absolute;
    top: -4px;
    right: 15%;
    min-width: 16px;
    height: 16px;
    background-color: #ef4444;
    border-radius: 10px;
    border: 2px solid #ffffff;
    color: #ffffff;
    font-size: 9px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    line-height: 1;
}
</style>

<nav class="mobile-bottom-nav d-md-none">
    <ul>
        <li class="nav-item active">
            <a href="./">
                <i class="fas fa-home"></i>
                <span>Beranda</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="#reservasi">
                <i class="fas fa-ticket-alt"></i>
                <span>Reservasi</span>
            </a>
        </li>

        <li class="nav-item-featured" style="position: relative;">
            <a href="#promo">
                <i class="fas fa-bullhorn"></i>
                <span>Promo</span>
                <?php if ($jumlah_promo_aktif > 0): ?>
                    <span class="cart-badge"><?= $jumlah_promo_aktif; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a href="#sewa-rumah">
                <i class="fas fa-shipping-fast"></i>
                <span>Sewa</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="#mobileMenuPanel" class="mobile-detail-btn">
                <i class="fas fa-bars"></i>
                <span>Detail</span>
            </a>
        </li>
    </ul>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navItems = document.querySelectorAll('.mobile-bottom-nav .nav-item');
    
    function setActiveItem() {
        let currentHash = window.location.hash;
        
        if (currentHash) {
            navItems.forEach(item => {
                const link = item.querySelector('a');
                if (link && link.getAttribute('href') === currentHash) {
                    navItems.forEach(nav => nav.classList.remove('active'));
                    item.classList.add('active');
                }
            });
        }
    }

    navItems.forEach(item => {
        item.addEventListener('click', function() {
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
        });
    });

    setActiveItem();
});
</script>