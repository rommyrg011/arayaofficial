<?php include 'ui/head.php'; ?>

<body data-spy="scroll" data-target=".navbar" data-offset="70">

   <?php include 'ui/navbar.php'; ?>

<header class="mobile-top-header">
<div class="mobile-brand">ARAYA GAMESTATION</div>
<div class="mobile-header-actions">
<button type="button" class="mobile-header-btn mobile-search-btn" id="mobileSearchButton" aria-label="Cari"><i class="fas fa-search"></i></button>
<button type="button" class="mobile-header-btn mobile-menu-btn" id="mobileMenuButton" aria-label="Buka menu"><i class="fas fa-bars"></i></button>

</div>
</header>
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
<aside class="mobile-menu-panel" id="mobileMenuPanel">
<div class="mobile-menu-header"><div><span class="mobile-menu-kicker">ARAYA GAMESTATION</span><h3>Menu</h3></div><button type="button" class="mobile-menu-close" id="mobileMenuClose" aria-label="Tutup menu"><i class="fas fa-times"></i></button></div>
<nav class="mobile-menu-links">
<a href="#fasilitas"><i class="fas fa-concierge-bell"></i><span>Fasilitas Tersedia</span><i class="fas fa-chevron-right"></i></a>
<a href="#pricelist"><i class="fas fa-book"></i><span>Daftar Harga</span><i class="fas fa-chevron-right"></i></a>
<a href="#ketersediaan"><i class="fas fa-check-circle"></i><span>Cek Ketersediaan</span><i class="fas fa-chevron-right"></i></a>
<a href="#games"><i class="fas fa-gamepad"></i><span>Game Terbaik</span><i class="fas fa-chevron-right"></i></a>
<a href="#lokasi"><i class="fas fa-map-marker-alt"></i><span>Lokasi Playstation</span><i class="fas fa-chevron-right"></i></a>
<a href="#ulasan"><i class="fas fa-comments"></i><span>Ulasan Pelanggan</span><i class="fas fa-chevron-right"></i></a>
</nav>
</aside>
<div class="mobile-search-overlay" id="mobileSearchOverlay">
<div class="mobile-search-box"><div class="mobile-search-header"><h3>Cari</h3><button type="button" id="mobileSearchClose" aria-label="Tutup pencarian"><i class="fas fa-times"></i></button></div>
<div class="mobile-search-input-wrap"><i class="fas fa-search"></i><input type="search" id="mobileSearchInput" placeholder="Cari fasilitas, game, lokasi..." autocomplete="off"></div>
<div class="mobile-search-results" id="mobileSearchResults">
<a href="#fasilitas" data-search="fasilitas harga vip premiere wifi parkir cctv"><i class="fas fa-concierge-bell"></i><span>Fasilitas</span></a>
<a href="#ketersediaan" data-search="ketersediaan unit gambut beruntung"><i class="fas fa-check-circle"></i><span>Cek Ketersediaan</span></a>
<a href="#games" data-search="game ps4 pes tekken naruto resident"><i class="fas fa-gamepad"></i><span>Game</span></a>
<a href="#lokasi" data-search="lokasi gambut beruntung alamat map"><i class="fas fa-map-marker-alt"></i><span>Lokasi</span></a>
</div></div></div>

    
    <div id="beranda-carousel-wrapper">
        <div id="arayaHeroCarousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="7000">
            <div class="carousel-custom-controls">
                <span class="slide-number" id="carouselCounter">1 / 3</span>
                <div class="nav-buttons-wrapper">
                    <button type="button" data-target="#arayaHeroCarousel" data-slide="prev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" data-target="#arayaHeroCarousel" data-slide="next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div class="carousel-inner">
                <div class="carousel-item active" style="background-image: url('img/bgaraya.webp');">
                    <div class="container hero-container-fixed">
                        <div class="hero-content-left" data-aos="zoom-in" data-aos-duration="1000">
                            <h1 class="text-accent carousel-title">Selamat datang di <br class="d-none d-md-block"> Araya Gamestation</h1>
                            <p class="hero-desc-custom">Destinasi hiburan digital terpadu dalam genggamanmu. Yuk, reservasi tempat mabar, nobar, atau karaoke serumu sekarang.</p>
                            
                        </div>
                    </div>
                </div>

                <div class="carousel-item" style="background-image: url('img/gambut.webp');">
                    <div class="container hero-container-fixed">
                        <div class="hero-content-left">
                            <h1 class="text-accent carousel-title">ARAYA GAMESTATION 1</h1>
                            <p class="hero-desc-custom">Jl. A. Yani No.Km. 15, Malintang Baru, Kec. Gambut, Kabupaten Banjar, Kalimantan Selatan 70652</p>
                        </div>
                    </div>
                </div>

                <div class="carousel-item" style="background-image: url('img/beruntung.webp');">
                    <div class="container hero-container-fixed">
                        <div class="hero-content-left">
                            <h1 class="text-accent carousel-title">ARAYA GAMESTATION 2</h1>
                            <p class="hero-desc-custom">Pertokoan Depan Minimarket, Jl. Raya Krisna Jl. Bumi Jaya No.27, Pemurus Dalam, Kec. Banjarmasin Sel., Kota Banjarmasin, Kalimantan Selatan 70248</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="separator"></div>
    <section id="tentang" class="bg-solid py-5">
    <div class="container text-center" data-aos="fade-up" data-aos-duration="800">
        <h2 class="section-title">Tentang <span>Kami</span></h2>
        <p class="text-secondary" style="max-width: 900px; margin: 0 auto; line-height: 1.8; font-size: 1.1rem; text-align: justify;">
            Araya Gamestation hadir sebagai pelopor hiburan digital terpadu yang menyajikan pengalaman premium, bersih, 
            dan super nyaman bagi para pencari hiburan sejati. Tidak hanya memanjakan para gamer lewat jajaran konsol 
            PlayStation, kami juga bertransformasi menjadi pusat multi-fasilitas dengan menyediakan tiga pilihan zona 
            eksklusif: Reguler Room yang dinamis untuk keseruan mabar, VIP Room yang menawarkan privasi dan kenyamanan ekstra,
            serta Premiere Room sebagai kasta tertinggi yang menyajikan kemewahan dengan fasilitas private karaoke di dalamnya.
            Kombinasi sempurna antara teknologi gaming terkini dan hiburan musik private ini menjadikan Araya Gamestation 
            destinasi paling ideal untuk melepas penat serta merayakan momen kebersamaan dalam satu atap.
        </p>
    </div>
</section>

    <div class="separator"></div>

    <section id="fasilitas" style="background-color: var(--bg-dark-accent); padding: 60px 0;">
        <div class="container text-center" data-aos="fade-up" data-aos-duration="800">
            <h2 class="section-title text-white">Fasilitas <span>Kami</span></h2>
            <div class="fasilitas-grid">
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="fasilitas-text">Harga Mulai Dari</div>
                    <div class="fasilitas-subtext">Rp.5.000</div>
                </div>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas fa-gamepad"></i></div>
                    <div class="fasilitas-text">Game PS 4</div>
                    <div class="fasilitas-subtext">Koleksi Game Terbaru & Terupdate</div>
                </div>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas fa-user-shield"></i></div>
                    <div class="fasilitas-text">VIP Room</div>
                    <div class="fasilitas-subtext">Menyediakan ruang private untuk nobar</div>
                </div>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas fa-microphone"></i></div>
                    <div class="fasilitas-text">Premiere Room</div>
                    <div class="fasilitas-subtext">Menyediakan ruang private karaoke</div>
                </div>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas fa-tv"></i></div>
                    <div class="fasilitas-text">TV Mulai 43 Inch</div>
                    <div class="fasilitas-subtext">Layar Jernih & Responsif</div>
                </div>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas fa-wifi"></i></div>
                    <div class="fasilitas-text">Free Wifi</div>
                    <div class="fasilitas-subtext">Kecepatan 100 Mbps</div>
                </div>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas fa-snowflake"></i></div>
                    <div class="fasilitas-text">Full Kipas Angin</div>
                    <div class="fasilitas-subtext">& AC</div>
                </div>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas fa-video"></i></div>
                    <div class="fasilitas-text">CCTV 24 Jam</div>
                    <div class="fasilitas-subtext">Keamanan Terjamin</div>
                </div>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas fa-archive"></i></div>
                    <div class="fasilitas-text">Lemari Penyimpanan</div>
                    <div class="fasilitas-subtext">Tersedia untuk menyimpan barang anda</div>
                </div>
                <div class="fasilitas-card">
                    <div class="fasilitas-icon"><i class="fas fa-motorcycle"></i></div>
                    <div class="fasilitas-text">Parkir Gratis</div>
                    <div class="fasilitas-subtext">Aman & Luas</div>
                </div>
            </div>
        </div>
    </section>

    <div class="separator"></div>
    <section id="pricelist" class="bg-solid py-4">
        <div class="container" data-aos="fade-up" data-aos-duration="800">
            <h2 class="section-title text-center mb-4">Daftar <span>Harga</span></h2>
            
            <div class="row justify-content-center">
                <?php 
                $db_conn = isset($koneksi) ? $koneksi : (isset($conn) ? $conn : null);
                $harga_by_cabang = [];
                if ($db_conn) {
                    $q_harga = mysqli_query($db_conn, "SELECT * FROM daftar_harga ORDER BY cabang ASC, id_harga ASC");
                    if ($q_harga) {
                        while ($row_harga = mysqli_fetch_assoc($q_harga)) {
                            $c_name = !empty(trim($row_harga['cabang'])) ? trim($row_harga['cabang']) : 'Semua Cabang';
                            $harga_by_cabang[$c_name][] = $row_harga;
                        }
                    }
                }
                ?>
                <?php if (!empty($harga_by_cabang)): ?>
                    <?php foreach ($harga_by_cabang as $nama_cabang => $items_harga): ?>
                        <div class="col-lg-5 col-md-6 col-12 mb-3" data-aos="zoom-in" data-aos-delay="100">
                            <div class="card h-100 p-3 rounded-lg shadow-sm" style="border: 1px solid var(--border-color);">
                                <div class="text-center mb-3">
                                    <h5 class="font-weight-bold text-accent mb-0" style="font-size: 1.1rem;"><?php echo htmlspecialchars($nama_cabang); ?></h5>
                                </div>
                                <ul class="list-group list-group-flush bg-transparent">
                                    <?php foreach ($items_harga as $item): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent py-2 px-1" style="border-bottom: 1px dashed var(--border-color);">
                                            <div class="pr-2">
                                                <div class="font-weight-bold mb-0" style="color: var(--text-heading); font-size: 0.88rem; line-height: 1.2;"><?php echo htmlspecialchars($item['p_paket']); ?></div>
                                                <small class="text-secondary" style="font-size: 0.78rem;"><?php echo htmlspecialchars($item['durasi']); ?></small>
                                            </div>
                                            <span class="font-weight-bold text-accent text-nowrap" style="font-size: 0.88rem;">
                                                <?php 
                                                if (is_numeric($item['daf_harga'])) {
                                                    echo 'Rp ' . number_format((float)$item['daf_harga'], 0, ',', '.');
                                                } else {
                                                    echo htmlspecialchars($item['daf_harga']);
                                                }
                                                ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-secondary">Daftar harga tidak ditemukan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <div class="separator"></div>

    <?php include 'ui/promo.php'; ?>

    <div class="separator"></div>

    <section id="sewa-rumah" class="my-5" style="padding: 0;">
        <div class="container">
            <div class="banner-iklan" data-aos="fade-up">
                <div class="banner-overlay"></div>
                <div class="banner-content">
                    <div class="banner-badge"><i class="fas fa-home mr-2"></i> LAYANAN HOME SERVICE</div>
                    <h2 class="banner-title">Mager Keluar Rumah? <br><span>Sewa PlayStation Biar Kami Antar!</span></h2>
                    <p class="banner-text">Nikmati keseruan bermain PS4 bersama keluarga atau teman langsung di ruang tamu Anda. Unit lengkap, bersih, siap pakai, dan gratis ongkir layanan antar-jemput.</p>
                    <a href="sewaps-araya" class="btn btn-banner shadow mb-4 mb-md-0">
                        <i class="fas fa-paper-plane mr-2"></i> Pesan Sekarang
                    </a>
                </div>
                <div class="banner-visual d-none d-lg-block">
                    <i class="fab fa-playstation"></i>
                </div>
            </div>
        </div>
    </section>

    <div class="separator"></div>

    <section id="games" class="bg-solid py-5">
        <div class="container text-center">
            <h2 class="section-title" data-aos="fade-up">Game <span>Tersedia</span></h2>
            <div class="ps-game-grid mt-4">
                <div class="ps-game-card" data-aos="fade-up" data-aos-delay="100">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR1nstIiMFrA5Kh6pAkbiX7D4izUCzNZoQP6-6YO7jJNg&s=10" alt="PES 2026" class="ps-game-box-art">
                    <div class="ps-game-label">E-FOOTBAL PES UPDATE</div>
                </div>
                <div class="ps-game-card" data-aos="fade-up" data-aos-delay="200">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRMxMJsaKiFnT29qET3x8Aa8pc355b4ypBLVeRwPk-yEg&s=10" alt="TEKKEN 7" class="ps-game-box-art">
                    <div class="ps-game-label">TEKKEN 7</div>
                </div>
                <div class="ps-game-card" data-aos="fade-up" data-aos-delay="300">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ535RYhFOb4pNHPT3jFUg7I_tpJGL2-pw1Z_WMz6lRSw&s=10" alt="NARUTO STORM 4" class="ps-game-box-art">
                    <div class="ps-game-label">NARUTO STORM 4</div>
                </div>
                <div class="ps-game-card" data-aos="fade-up" data-aos-delay="400">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRuB_yHiV64XL9x2WbidndIYmeG-bKXcYtRMgkQ6Vrk9g&s=10" alt="I TAKE TWO" class="ps-game-box-art">
                    <div class="ps-game-label">I TAKE TWO</div>
                </div>
                <div class="ps-game-card" data-aos="fade-up" data-aos-delay="500">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSSb3f8m26eLFMvwhy1v1z65gOU04lWl-zn0gH-eD4zQQ&s=10" alt="A WAY OUT" class="ps-game-box-art">
                    <div class="ps-game-label">A WAY OUT</div>
                </div>
                <div class="ps-game-card" data-aos="fade-up" data-aos-delay="600">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQrbXyYC4b8eSHSd2T_t75wwfqTbCnX_i8MTThXo7mYVg&s=10" alt="RESIDENT 4 REMAKE" class="ps-game-box-art">
                    <div class="ps-game-label">RESIDENT 4 REMAKE</div>
                </div>
            </div>
            <h6 class="mt-4 text-secondary">Dan masih banyak lagi...</h6>
        </div>
    </section>

    <div class="separator"></div>

    <section id="ketersediaan" class="py-5">
        <div class="container" data-aos="fade-up" data-aos-duration="800">
            <h2 class="section-title text-center mb-4">Ketersediaan <span>Unit</span></h2>
            <div class="pagination-container">
                <button class="page-btn active" id="btn-gambut" onclick="switchPage('gambut')">Gambut</button>
                <button class="page-btn" id="btn-beruntung" onclick="switchPage('beruntung')">Beruntung</button>
            </div>
            <div class="grid-container" id="display-area"></div>
        </div>
    </section>
    
    <div class="separator"></div>

    <section id="reservasi" class="my-5" style="padding: 0;">
        <div class="container">
            <div class="banner-reservasi" data-aos="fade-up">
                <div class="banner-res-content">
                    <div class="banner-res-badge"><i class="fas fa-calendar-alt mr-2"></i> RESERVASI ONLINE</div>
                    <h2 class="banner-res-title">Amankan Unit Bermain Anda?<br><span>Reservasi Sekarang Tanpa Antri!</span></h2>
                    <p class="banner-res-text">Sistem reservasi online kami memungkinkan Anda untuk memilih cabang, unit, dan waktu bermain dengan mudah dan cepat langsung dari gadget Anda.</p>
                    <a href="reservasi-araya" class="btn btn-res-action">
                        <i class="fas fa-paper-plane mr-2"></i> PESAN SEKARANG
                    </a>
                </div>
                <div class="banner-res-visual d-none d-lg-block">
                    <i class="fab fa-playstation"></i>
                </div>
            </div>
        </div>
    </section>

    <div class="separator"></div>

    <section id="lokasi" class="py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5" data-aos="fade-up">Lokasi <span>Kami</span></h2>
            <div class="row">
                
                <div class="col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="200">
                    <h5 class="mb-3 text-center font-weight-bold">Araya Gamestation 1</h5>
                    <div class="map-container-wrapper">
                        <div class="map-info-card-custom">
                            <div class="card-body-left">
                                <span class="map-place-title">Araya Gamestation 1</span>
                                <span class="map-place-address">Jl. A. Yani No.Km. 15, Malintang Baru, Kec. Gambut, Kabupaten Banjar, Kalimantan Selatan 70652</span>
                                <div class="map-place-meta">
                                    <span class="meta-rating-score">4.8</span>
                                    <span class="meta-rating-stars"><i class="fas fa-star"></i></span>
                                    <span class="meta-rating-count">(35)</span>
                                    <span class="meta-info-icon"><i class="fas fa-info-circle"></i></span>
                                </div>
                            </div>
                            <div class="card-body-right">
                                <a href="https://maps.google.com/?q=Araya+Gamestation+1+Gambut" target="_blank" class="map-action-btn btn-share-link">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <a href="https://maps.google.com/?daddr=-3.4194,114.6712" target="_blank" class="map-action-btn btn-directions-link">
                                    <i class="fas fa-directions"></i>
                                </a>
                            </div>
                        </div>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.752249659411!2d114.67038837349689!3d-3.410469541634404!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de69d61671e468f%3A0xd4e7dff7437a1a18!2sAraya%20Gamestation%201!5e0!3m2!1sid!2sid!4v1782582063577!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </div>
                </div>

                <div class="col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="100">
                    <h5 class="mb-3 text-center font-weight-bold">Araya Gamestation 2</h5>
                    <div class="map-container-wrapper">
                        <div class="map-info-card-custom">
                            <div class="card-body-left">
                                <span class="map-place-title">Araya Gamestation 2</span>
                                <span class="map-place-address">Pertokoan Depan Minimarket, Jl. Raya Krisna Jl. Bumi Jaya No.27, Pemurus Dalam, Kec. Banjarmasin Sel., Kota Banjarmasin, Kalimantan Selatan 70248</span>
                                <div class="map-place-meta">
                                    <span class="meta-rating-score">4.9</span>
                                    <span class="meta-rating-stars"><i class="fas fa-star"></i></span>
                                    <span class="meta-rating-count">(42)</span>
                                    <span class="meta-info-icon"><i class="fas fa-info-circle"></i></span>
                                </div>
                            </div>
                            <div class="card-body-right">
                                <a href="https://maps.google.com/?q=Araya+Gamestation+2+Banjarmasin" target="_blank" class="map-action-btn btn-share-link">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <a href="https://maps.google.com/?daddr=-3.3456,114.6012" target="_blank" class="map-action-btn btn-directions-link">
                                    <i class="fas fa-directions"></i>
                                </a>
                            </div>
                        </div>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.987339741411!2d114.61601767349634!3d-3.3532395413757166!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de4218a36b40429%3A0x43df044ecd8be595!2sAraya%20GameStation%202!5e0!3m2!1sid!2sid!4v1782582029994!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <div class="separator"></div>
    <section id="ulasan" class="py-5 bg-solid">
        <div class="container">
            <h2 class="section-title text-center mb-5" data-aos="fade-up">Ulasan <span>Pelanggan</span></h2>
            <div class="row text-left">
                <div class="col-lg-5 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card card-body p-4 rounded-lg">
                        <h5 class="font-weight-bold mb-3"><i class="fas fa-pen-alt text-primary mr-2"></i>Tulis Ulasan Anda</h5>
                        <form action="proses_feedback.php" method="POST" autocomplete="off">
                            <div class="form-group">
                                <input type="text" name="nama" class="form-control" placeholder="Nama Anda" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-control" placeholder="Email Anda">
                            </div>
                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-secondary small mb-1 d-block">Rating Anda:</label>
                                <div class="rating-input">
                                    <input type="radio" id="star5" name="rating" value="5" required/><label for="star5"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4" name="rating" value="4"/><label for="star4"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3" name="rating" value="3"/><label for="star3"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2" name="rating" value="2"/><label for="star2"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1" name="rating" value="1"/><label for="star1"><i class="fas fa-star"></i></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="pesan" class="form-control" rows="4" placeholder="Bagikan pengalaman Anda bermain di sini..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-promo-standard w-100 py-3 mt-2">Kirim Ulasan</button>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-7 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <h5 class="font-weight-bold mb-3"><i class="fas fa-star-half-alt text-warning mr-2"></i>Ulasan Terbaru</h5>
                    <div style="max-height: 480px; overflow-y: auto; padding-right: 5px;">
                        <?php if (!empty($feedback_list)): ?>
                            <?php foreach ($feedback_list as $feedback): ?>
                                <?php 
                                    $initial = strtoupper(substr($feedback['nama_pelanggan'], 0, 1)); 
                                    $tanggal = date('d M Y, H:i', strtotime($feedback['tanggal_dibuat']));
                                ?>
                                <div class="google-review-card">
                                    <div class="review-header">
                                        <div class="review-avatar" style="background-color: var(--accent-main); color: #fff;"><?php echo htmlspecialchars($initial); ?></div>
                                        <div class="review-user-info">
                                            <h6><?php echo htmlspecialchars($feedback['nama_pelanggan']); ?></h6>
                                            <span><?php echo $tanggal; ?> WITA</span>
                                        </div>
                                    </div>
                                    <div class="review-stars">
                                        <?php 
                                        $stars = intval($feedback['rating']);
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $stars) {
                                                echo '<i class="fas fa-star" style="color: #2e7d32;"></i>';
                                            } else {
                                                echo '<i class="far fa-star text-muted"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <p class="review-text"><?php echo nl2br(htmlspecialchars($feedback['pesan'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center p-5 card rounded-lg">
                                <i class="fas fa-comment-slash text-muted mb-3" style="font-size: 32px;"></i>
                                <p class="text-secondary m-0">Belum ada ulasan masuk. Jadilah yang pertama memberikan penilaian!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

   <?php include 'ui/footer.php'; ?>

    <?php include 'ui/mobile.php'; ?>

   <?php include 'ui/script.php' ; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var menuBtn = document.getElementById('mobileMenuButton');
    var menuClose = document.getElementById('mobileMenuClose');
    var menuOverlay = document.getElementById('mobileMenuOverlay');
    var searchBtn = document.getElementById('mobileSearchButton');
    var searchClose = document.getElementById('mobileSearchClose');
    var searchOverlay = document.getElementById('mobileSearchOverlay');
    var menuLinks = document.querySelectorAll('.mobile-menu-links a');
    var detailBtns = document.querySelectorAll('.mobile-detail-btn, .btn-detail, [data-target="sidebar"], [href="#mobileMenuPanel"], .mobile-header-btn.mobile-menu-btn, .mobile-bottom-nav a[href="#menu"], .mobile-bottom-nav a[href="#sidebar"], .mobile-bottom-nav a[href="#mobileMenuPanel"]');

    function openSidebar() {
        document.body.classList.add('mobile-menu-open');
    }

    function closeSidebar() {
        document.body.classList.remove('mobile-menu-open');
    }

    if (menuBtn) {
        menuBtn.addEventListener('click', openSidebar);
    }

    detailBtns.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            openSidebar();
        });
    });

    if (menuClose) {
        menuClose.addEventListener('click', closeSidebar);
    }

    if (menuOverlay) {
        menuOverlay.addEventListener('click', closeSidebar);
    }

    menuLinks.forEach(function (link) {
        link.addEventListener('click', closeSidebar);
    });

    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            document.body.classList.add('mobile-search-open');
        });
    }

    if (searchClose) {
        searchClose.addEventListener('click', function () {
            document.body.classList.remove('mobile-search-open');
        });
    }

    if (searchOverlay) {
        searchOverlay.addEventListener('click', function (e) {
            if (e.target === searchOverlay) {
                document.body.classList.remove('mobile-search-open');
            }
        });
    }
});


</script>
</body>

<div class="wa-floating-container">
    <div class="wa-options" id="wa-options">
        <a href="https://api.whatsapp.com/send?phone=6285147520182&text=Halo%20Admin%20Araya%20Gamestation%20cabang%20Gambut,%20saya%20ingin%20bertanya" target="_blank" class="wa-option-btn">Cabang Gambut</a>
        <a href="https://api.whatsapp.com/send?phone=6283167079509&text=Halo%20Admin%20Araya%20Gamestation%20cabang%20Beruntung,%20saya%20ingin%20bertanya" target="_blank" class="wa-option-btn">Cabang Beruntung</a>
    </div>
    <button class="wa-main-btn" onclick="toggleWa()">
        <svg viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.01 2.014a9.98 9.98 0 0 0-8.5 15.22L2 22l4.89-1.49a9.96 9.96 0 0 0 5.12 1.41h.01a9.98 9.98 0 0 0 9.98-9.98 9.98 9.98 0 0 0-9.99-9.926zm0 18.28a8.3 8.3 0 0 1-4.25-1.16l-.3-.18-3.15.96.84-3.08-.2-.32a8.32 8.32 0 0 1-1.26-4.48c0-4.6 3.75-8.35 8.35-8.35 2.23 0 4.33.87 5.91 2.45a8.34 8.34 0 0 1 2.44 5.92c0 4.6-3.74 8.35-8.34 8.35zm4.58-6.24c-.25-.13-1.49-.74-1.72-.82-.23-.08-.4-.13-.57.12-.17.25-.65.82-.8 1-.15.17-.3.2-.55.07-.25-.12-1.06-.39-2.02-1.25-.74-.67-1.25-1.49-1.4-1.74-.15-.25-.02-.38.11-.5.11-.11.25-.29.37-.44.13-.14.17-.25.25-.41.08-.17.04-.32-.02-.45-.06-.12-.57-1.37-.78-1.88-.2-.5-.41-.43-.57-.44h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1s.9 2.44 1.03 2.62c.13.17 1.78 2.72 4.32 3.82.6.26 1.07.41 1.44.53.6.19 1.15.16 1.58.1.48-.07 1.49-.61 1.7-1.2.2-.59.2-1.1.14-1.2-.06-.09-.23-.15-.48-.27z"/>
        </svg>
    </button>
</div>
</html>