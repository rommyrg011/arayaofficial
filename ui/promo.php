<?php
if (!isset($koneksi)) {
    require_once 'function.php';
}

$promos_data = [];
if ($koneksi instanceof PDO) {
    $stmt = $koneksi->query("SELECT * FROM promos WHERE status = 'Aktif' AND kuota > 0 ORDER BY best_value DESC, id DESC");
    $promos_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $res = mysqli_query($koneksi, "SELECT * FROM promos WHERE status = 'Aktif' AND kuota > 0 ORDER BY best_value DESC, id DESC");
    while($row = mysqli_fetch_assoc($res)) {
        $promos_data[] = $row;
    }
}
?>

<style>
.promo-section {
    padding-top: 10px !important;
    padding-bottom: 10px !important;
    margin-top: 0 !important;
    margin-bottom: 10px !important;
}

.promo-section .section-title {
    margin-top: 0 !important;
    margin-bottom: 4px !important;
}

.promo-section .text-subtitle-promo {
    margin-bottom: 15px !important;
}

.promo-pricing-wrapper {
    display: flex !important;
    flex-direction: column !important;
    gap: 15px !important;
    width: 100% !important;
    box-shadow: none !important;
    padding: 0 !important;
}

.promo-card-standard-glued,
.promo-card-highlight-glued {
    position: relative !important;
    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 16px !important;
    padding: 18px !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03) !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    width: 100% !important;
    margin: 0 !important;
    box-sizing: border-box !important;
    color: #333333 !important;
}

.promo-card-header {
    margin-bottom: 6px !important;
}

.promo-title {
    font-size: 1.1rem !important;
    font-weight: 700 !important;
    color: #111827 !important;
    margin-bottom: 2px !important;
    line-height: 1.2 !important;
}

.promo-desc {
    font-size: 0.8rem !important;
    color: #6b7280 !important;
    margin-bottom: 10px !important;
}

.promo-badge {
    position: absolute !important;
    top: 16px !important;
    right: 16px !important;
    background-color: #dcfce7 !important;
    color: #166534 !important;
    border: 1px solid #86efac !important;
    font-size: 0.65rem !important;
    font-weight: 700 !important;
    padding: 3px 8px !important;
    border-radius: 20px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.promo-price-wrapper {
    margin: 10px 0 14px 0 !important;
}

.promo-price-normal {
    text-decoration: line-through !important;
    color: #9ca3af !important;
    font-size: 0.85rem !important;
    margin-bottom: 2px !important;
}

.promo-price {
    display: flex !important;
    align-items: baseline !important;
    gap: 4px !important;
    color: #166534 !important;
}

.price-currency {
    font-size: 0.95rem !important;
    font-weight: 700 !important;
}

.price-amount {
    font-size: 1.5rem !important;
    font-weight: 800 !important;
    line-height: 1 !important;
}

.promo-features {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 0 16px 0 !important;
    font-size: 0.85rem !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
}

.promo-features li {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    color: #374151 !important;
    line-height: 1.3 !important;
}

.promo-features li i {
    font-size: 0.9rem !important;
    color: #22c55e !important;
    flex-shrink: 0 !important;
}

.btn-promo-highlight,
.btn-promo-standard {
    display: block !important;
    width: 100% !important;
    padding: 10px 12px !important;
    border-radius: 8px !important;
    background-color: #e8f7ee !important;
    color: #166534 !important;
    font-size: 0.85rem !important;
    font-weight: 700 !important;
    text-align: center !important;
    text-decoration: none !important;
    border: none !important;
    box-sizing: border-box !important;
}

.promo-empty-state {
    text-align: center;
    padding: 40px 20px;
    width: 100%;
}

.promo-empty-icon {
    font-size: 3.5rem;
    color: #6c757d;
    margin-bottom: 15px;
    opacity: 0.7;
}

.promo-empty-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #343a40;
    margin-bottom: 8px;
}

.promo-empty-desc {
    font-size: 0.9rem;
    color: #6c757d;
    max-width: 400px;
    margin: 0 auto;
}

@media (min-width: 768px) {
    .promo-pricing-wrapper {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(260px, 320px)) !important;
        justify-content: center !important;
        gap: 20px !important;
    }
}
</style>

<br>
<section id="promo" class="promo-section">
    <div class="container">
        <div class="text-center mt-5" data-aos="fade-up">
            <h2 class="section-title">Promo <span>Tersedia</span></h2>
            <p class="text-subtitle-promo">Klaim Sekarang, Kuota Terbatas</p>
        </div>
        
        <div class="promo-pricing-wrapper" data-aos="fade-up" data-aos-delay="100">
            <?php if (empty($promos_data)): ?>
                <div class="promo-empty-state">
                    <i class="fas fa-ticket-alt promo-empty-icon"></i>
                    <h3 class="promo-empty-title">Belum Ada Promo</h3>
                    <p class="promo-empty-desc">Mohon maaf, saat ini sedang tidak ada promo yang tersedia.</p>
                </div>
            <?php else: ?>
                <?php foreach($promos_data as $promo): ?>
                    <?php 
                        $harga_normal = (float)$promo['harga_normal'];
                        $potongan = (float)$promo['potongan'];
                        $harga_akhir = $harga_normal - $potongan;
                        if ($harga_akhir < 0) $harga_akhir = 0;

                        $harga_normal_format = number_format($harga_normal, 0, ',', '.');
                        $harga_akhir_format = number_format($harga_akhir, 0, ',', '.');
                        
                        $keterangan_list = explode("\n", $promo['keterangan']);
                    ?>
                    <div class="promo-card-standard-glued">
                        <div>
                            <?php if($promo['best_value'] == 'Ya'): ?>
                                <div class="promo-badge">BEST VALUE</div>
                            <?php endif; ?>
                            <div class="promo-card-header">
                                <h3 class="promo-title"><?= htmlspecialchars($promo['nama_promo']); ?></h3>
                                <p class="promo-desc">Potongan Spesial</p>
                            </div>
                            <div class="promo-price-wrapper">
                                <div class="promo-price-normal">
                                    Rp <?= $harga_normal_format; ?>
                                </div>
                                <div class="promo-price">
                                    <span class="price-currency">Rp</span>
                                    <span class="price-amount"><?= $harga_akhir_format; ?></span>
                                </div>
                            </div>
                            <ul class="promo-features">
                                <?php foreach($keterangan_list as $ket): ?>
                                    <?php if(trim($ket) !== ''): ?>
                                        <li><i class="fas fa-check-circle"></i> <?= htmlspecialchars(trim($ket)); ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <a href="promo-araya?id=<?= $promo['id']; ?>" class="btn btn-promo-standard w-100">Klaim Voucher</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>