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
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 260px));
    justify-content: center;
    gap: 15px;
    width: 100%;
}

.promo-card-highlight-glued,
.promo-card-standard-glued {
    position: relative;
    padding: 16px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.promo-title {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 2px;
    line-height: 1.2;
}

.promo-desc {
    font-size: 0.75rem;
    margin-bottom: 10px;
}

.promo-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 0.65rem;
    padding: 3px 8px;
    border-radius: 20px;
}

.promo-price-wrapper {
    margin: 10px 0;
}

.promo-price-normal {
    text-decoration: line-through;
    opacity: 0.7;
    font-size: 0.8rem;
    margin-bottom: -2px;
}

.promo-price {
    display: flex;
    align-items: baseline;
    gap: 2px;
}

.price-currency {
    font-size: 0.85rem;
    font-weight: 600;
}

.price-amount {
    font-size: 1.4rem;
    font-weight: 800;
    line-height: 1;
}

.promo-features {
    list-style: none;
    padding: 0;
    margin: 10px 0 15px 0;
    font-size: 0.7rem;
}

.promo-features li {
    margin-bottom: 6px;
    display: flex;
    align-items: flex-start;
    gap: 6px;
    line-height: 1.3;
}

.promo-features li i {
    font-size: 0.75rem;
    flex-shrink: 0;
    margin-top: 2px;
}

.btn-promo-highlight,
.btn-promo-standard {
    font-size: 0.8rem;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 600;
}

@media (max-width: 576px) {
    .promo-section {
        padding-top: 5px !important;
    }

    .promo-pricing-wrapper {
        grid-template-columns: repeat(auto-fit, minmax(100%, 1fr));
        gap: 12px;
        padding: 0 5px;
    }

    .promo-card-highlight-glued,
    .promo-card-standard-glued {
        padding: 14px;
    }

    .promo-title {
        font-size: 1rem;
    }

    .price-amount {
        font-size: 1.3rem;
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
            <?php foreach($promos_data as $promo): ?>
                <?php 
                    $card_class = ($promo['best_value'] == 'Ya') ? 'promo-card-highlight-glued' : 'promo-card-standard-glued';
                    $btn_class = ($promo['best_value'] == 'Ya') ? 'btn-promo-highlight' : 'btn-promo-standard';
                    
                    $harga_normal = (float)$promo['harga_normal'];
                    $potongan = (float)$promo['potongan'];
                    $harga_akhir = $harga_normal - $potongan;
                    if ($harga_akhir < 0) $harga_akhir = 0;

                    $harga_normal_format = number_format($harga_normal, 0, ',', '.');
                    $harga_akhir_format = number_format($harga_akhir, 0, ',', '.');
                    
                    $keterangan_list = explode("\n", $promo['keterangan']);
                ?>
                <div class="<?= $card_class; ?>">
                    <div>
                        <?php if($promo['best_value'] == 'Ya'): ?>
                            <div class="promo-badge">BEST VALUE</div>
                        <?php endif; ?>
                        <div class="promo-card-header">
                            <h3 class="promo-title"><?= htmlspecialchars($promo['nama_promo']); ?></h3>
                            <p class="promo-desc <?= ($promo['best_value'] == 'Tidak') ? 'text-muted-custom' : ''; ?>">Potongan Spesial</p>
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
                    <a href="promo-araya?id=<?= $promo['id']; ?>" class="btn <?= $btn_class; ?> w-100">Klaim Voucher</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>