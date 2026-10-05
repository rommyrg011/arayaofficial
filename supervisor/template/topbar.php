<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?= isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Guest'; ?>
                </span>
                
                <?php
                if (!empty($_SESSION['images'])) {
                    // Cek apakah di database sudah ada nama folder 'img/' atau belum. 
                    // Jika belum, kita tambahkan manual path-nya. Sesuaikan nama foldernya jika berbeda.
                    $foto_profil = '../img/' . $_SESSION['images']; 
                } else {
                    // Gambar default bawaan SB Admin jika belum punya foto profil
                    $foto_profil = '../img/undraw_profile.svg'; 
                }
                ?>
                
                <img class="img-profile rounded-circle" src="<?= htmlspecialchars($foto_profil); ?>">
            </a>
            
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="<?= asset('admin/logout.php'); ?>">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>

</nav>