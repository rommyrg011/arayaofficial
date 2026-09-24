<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'function.php'; 

if (isset($_SESSION['username']) && isset($_SESSION['level'])) {
    header("Location: " . $_SESSION['level'] . "/");
    exit();
}

$error = "";
$login_success = false;
$redirect_folder = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $level    = isset($_POST['level']) ? trim($_POST['level']) : '';

    if (!empty($username) && !empty($password) && !empty($level)) {
        
        $stmt = $koneksi->prepare("SELECT id_user, nama_lengkap, password, level, images FROM user WHERE username = ? AND level = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $level);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            if ($password === $row['password']) {
                session_regenerate_id(true);
                
                $_SESSION['id_user']  = $row['id_user'];
                $_SESSION['username'] = $username;
                $_SESSION['nama']     = $row['nama_lengkap'];
                $_SESSION['level']    = $row['level']; 
                $_SESSION['images']   = $row['images']; 

                $login_success = true; 
                $redirect_folder = $row['level'] . "/";
            } else {
                $error = "Username, Password, atau Hak Akses salah!";
            }
        } else {
            $error = "Username, Password, atau Hak Akses salah!";
        }
        $stmt->close();
    } else {
        $error = "Semua form wajib diisi termasuk Hak Akses!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login Dashboard</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logonew.webp">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: url('img/bgaraya.jpeg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.65);
            z-index: -1;
        }

        .bg-login-araya {
            background: url('img/logonew.webp');
            background-position: center;
            background-size: 300px;
            background-repeat: no-repeat;
            background-color: #ffffff;
        }

        .form-control-user-select {
            font-size: 0.8rem;
            border-radius: 10rem;
            padding: 0.5rem 1rem;
            height: 50px !important;
        }

        @media (max-width: 1199.98px) {
            .form-container-mobile {
                position: relative;
                background-color: rgba(255, 255, 255, 0.93) !important;
            }
            .form-container-mobile::before {
                content: "";
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background: url('img/logoaraya.png');
                background-position: center;
                background-size: 260px;
                background-repeat: no-repeat;
                opacity: 0.12;
                z-index: 0;
                pointer-events: none;
            }
            .form-container-mobile > div {
                position: relative;
                z-index: 1;
            }
        }
    </style>
</head>

<body>
    <div class="bg-overlay"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row align-items-center"> 
                            
                            <div class="col-xl-6 d-none d-xl-block bg-login-araya" style="min-height: 500px;"></div>
                            
                            <div class="col-xl-6 form-container-mobile">
                                <div class="p-5">
                                    <div>
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Selamat Datang</h1>
                                        </div>

                                        <?php if(!empty($error)): ?>
                                            <div class="alert alert-danger text-center small">
                                                <?= htmlspecialchars($error); ?>
                                            </div>
                                        <?php endif; ?>

                                        <form class="user" action="" method="POST">
                                            <div class="form-group">
                                                <input type="text" name="username" class="form-control form-control-user"
                                                    id="exampleInputUsername" placeholder="Masukkan Username..." required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <input type="password" name="password" class="form-control form-control-user"
                                                    id="exampleInputPassword" placeholder="Password" required>
                                            </div>

                                            <div class="form-group">
                                                <select name="level" class="form-control form-control-user-select text-gray-600" required>
                                                    <option value="" disabled selected hidden> Pilih hak akses...</option>
                                                    <option value="admin">Admin</option>
                                                    <option value="reservasi">Reservasi</option>
                                                    <option value="rental">Rental</option>
                                                    <option value="karyawan">Karyawan</option>
                                                </select>
                                            </div>

                                            <button type="submit" class="btn btn-primary btn-user btn-block mt-4">
                                                Login
                                            </button>
                                            <br>
                                            <center><a href="./"> Kembali ke halaman utama</a></center>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

    <?php if ($login_success): ?>
    <script>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Login Berhasil!",
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = "<?= $redirect_folder; ?>";
        });
    </script>
    <?php endif; ?>
</body>

</html>