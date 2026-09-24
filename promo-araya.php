<?php
$host = "localhost";
$username = "root"; 
$password = "";     
$dbname = "arayaofficial";

define('FONNTE_TOKEN', 'pDch4KtF3uLaQQ4rwJ6L'); 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

function kirimWhatsAppFonnte($nomor_tujuan, $pesan) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'target' => $nomor_tujuan,
        'message' => $pesan,
        'delay' => '2',
      ),
      CURLOPT_HTTPHEADER => array(
        'Authorization: ' . FONNTE_TOKEN
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    
    return $response;
}

$pesan_sukses = "";
$pesan_error = "";
$voucher_dihasilkan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = htmlspecialchars(trim($_POST['nama']));
    $whatsapp = htmlspecialchars(trim($_POST['whatsapp']));
    $promo_id = isset($_POST['promo_id']) ? intval($_POST['promo_id']) : 0;

    if (empty($nama) || empty($whatsapp) || empty($promo_id)) {
        $pesan_error = "Mohon maaf, Anda belum terdaftar klaim voucher promo";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM promos WHERE id = ? AND status = 'Aktif'");
            $stmt->execute([$promo_id]);
            $promo = $stmt->fetch();

            if (!$promo) {
                $pesan_error = "Maaf, promo tidak ditemukan atau sudah tidak aktif.";
            } elseif ($promo['kuota'] <= 0) {
                $pesan_error = "Maaf, kuota untuk promo ini sudah habis!";
            } else {
                $stmtCheck = $pdo->prepare("SELECT id FROM customer_vouchers WHERE nomor_whatsapp = ? AND promo_id = ?");
                $stmtCheck->execute([$whatsapp, $promo_id]);
                $existingVoucher = $stmtCheck->fetch();

                if ($existingVoucher) {
                    $pesan_error = "Maaf, nomor WhatsApp Anda sudah terdaftar untuk promo ini!";
                    $voucher_dihasilkan = ""; 
                } else {
                    $pdo->beginTransaction();

                    $stmtLock = $pdo->prepare("SELECT kuota FROM promos WHERE id = ? FOR UPDATE");
                    $stmtLock->execute([$promo_id]);
                    $currentPromo = $stmtLock->fetch();

                    if ($currentPromo['kuota'] > 0) {
                        $karakter_acak = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
                        $kode_voucher = $promo['kode_prefix'] . "-" . $karakter_acak;

                        $stmtInsert = $pdo->prepare("INSERT INTO customer_vouchers (nama_pelanggan, nomor_whatsapp, promo_id, kode_voucher) VALUES (?, ?, ?, ?)");
                        $stmtInsert->execute([$nama, $whatsapp, $promo_id, $kode_voucher]);

                        $stmtUpdate = $pdo->prepare("UPDATE promos SET kuota = kuota - 1 WHERE id = ?");
                        $stmtUpdate->execute([$promo_id]);

                        $pdo->commit(); 

                        $pesan_sukses = "Kode voucher Anda berhasil dibuat.";
                        $voucher_dihasilkan = $kode_voucher;

                        $nama_promo_clean = $promo['nama_promo'];
                        
                        $pesan_wa = "Halo *$nama*,\n\n";
                        $pesan_wa .= "Terima kasih telah melakukan klaim voucher promo di *Araya Gamestation*.\n\n";
                        $pesan_wa .= "Berikut adalah detail kode voucher Anda:\n";
                        $pesan_wa .= "- Promo : $nama_promo_clean\n";
                        $pesan_wa .= "- Kode Voucher : *$kode_voucher*\n\n";
                        $pesan_wa .= "Silahkan masukkan kode voucher ini ke form reservasi Araya Gamestation.\n\n";
                        $pesan_wa .= "https://arayaofficial.site/reservasi-araya \n\n";
                        $pesan_wa .= "_Pesan ini dikirim otomatis oleh sistem billing Araya Gamestation._";

                        kirimWhatsAppFonnte($whatsapp, $pesan_wa);
                    } else {
                        $pdo->rollBack();
                        $pesan_error = "Maaf, kuota untuk promo ini baru saja habis direbut pelanggan lain!";
                    }
                }
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $pesan_error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}

$promos = [];
try {
    $queryPromo = $pdo->query("SELECT * FROM promos WHERE status = 'Aktif' AND kuota > 0");
    $promos = $queryPromo->fetchAll();
} catch (PDOException $e) {
    $pesan_error = "Gagal memuat daftar promo: " . $e->getMessage();
}

$selected_promo_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['promo_id']) ? intval($_POST['promo_id']) : 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Klaim Promo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logonew.webp">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://arayaofficial.site/">
    <meta property="og:title" content="Araya Gamestation - Klaim Promo Araya Gamestation">
    <meta property="og:description" content="Form klaim promo Araya Gamestation, kuota terbatas!">
    <meta property="og:image" content="https://arayaofficial.site/img/bgaraya.webp">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --success: #10b981;
            --danger: #ef4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        
        body { 
            background: url('img/bgaraya.webp') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        .bg-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.65); z-index: -1;
        }

        .container { 
            width: 100%; max-width: 400px; background: var(--card-bg); 
            padding: 24px; border-radius: 12px; 
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); border: 1px solid var(--border); z-index: 1;
        }

        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { font-size: 20px; font-weight: 700; color: var(--text-main); }
        .header p { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 12px; color: var(--text-muted); text-transform: uppercase; }
        
        .form-group input, .form-group select { 
            width: 100%; padding: 10px 14px; background-color: #0f172a;
            border: 1px solid var(--border); border-radius: 8px; font-size: 14px; 
            color: var(--text-main); transition: all 0.2s ease; outline: none;
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: var(--primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .form-group select:disabled {
            background-color: #0f172a;
            color: #64748b;
            cursor: not-allowed;
            border-color: #334155;
            opacity: 0.8;
        }

        .form-group select { appearance: none; cursor: pointer; }

        button.btn-submit { 
            width: 100%; padding: 12px; background-color: var(--primary); 
            border: none; color: white; font-size: 14px; font-weight: 600; 
            border-radius: 8px; cursor: pointer; transition: background 0.2s ease; margin-top: 6px;
        }

        button.btn-submit:hover { background-color: var(--primary-hover); }

        .alert { padding: 12px; margin-bottom: 16px; border-radius: 8px; font-size: 13px; }
        .alert-danger { background-color: rgba(239, 68, 68, 0.1); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.2); }
        .alert-success { background-color: rgba(16, 185, 129, 0.1); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.2); }
        
        .voucher-box { text-align: center; background: #0f172a; border: 2px dashed var(--primary); padding: 18px 14px; margin-top: 12px; border-radius: 8px; }
        .voucher-code { font-size: 24px; font-weight: 700; letter-spacing: 3px; color: #60a5fa; margin-bottom: 10px; }
        .note { font-size: 11px; color: var(--text-muted); margin-top: 12px; }

        .btn-copy {
            background-color: rgba(59, 130, 246, 0.1); border: 1px solid var(--primary);
            color: #60a5fa; padding: 6px 12px; font-size: 12px; font-weight: 500;
            border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-copy:hover { background-color: var(--primary); color: white; }
        .btn-copy.copied { background-color: var(--success); border-color: var(--success); color: white; }
        .btn-warning { background-color: #f59e0b; color: #ffffff; }
        .btn-warning:hover { background-color: #d97706; }
    </style>
</head>
<body>

<div class="bg-overlay"></div>

<div class="container">
    <div class="header">
        <h2>Araya Gamestation</h2>
        <p style="color:tomato;">Segera klaim promo Araya Gamestation, kuota terbatas!</p>
    </div>

    <?php if (!empty($pesan_error)): ?>
        <div class="alert alert-danger">
         <?php echo $pesan_error; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($pesan_sukses)): ?>
        <div class="alert alert-success">
            <?php echo $pesan_sukses; ?>
            <div class="voucher-box">
                <div class="voucher-code" id="teksVoucher"><?php echo $voucher_dihasilkan; ?></div>
                <button type="button" class="btn-copy" onclick="salinVoucher()" id="btnCopyBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span id="teksCopy">Salin Kode</span>
                </button>
                <div class="note">Kode juga telah dikirim ke WhatsApp Anda.</div>
            </div>
        </div>
    <?php endif; ?>

    <form action="?id=<?php echo $selected_promo_id; ?>" method="POST" autocomplete="off">
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama Anda" value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="whatsapp">Nomor WhatsApp <span style="color:#05F763; font-size: 12px; text-transform: none;">( Aktif )</span></label>
            <input type="number" id="whatsapp" name="whatsapp" placeholder="Masukkan Nomor Whatsapp" value="<?php echo isset($_POST['whatsapp']) ? htmlspecialchars($_POST['whatsapp']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="promo_id">Promo Diklaim</label>
            <div style="position: relative;">
                <select id="promo_id" disabled>
                    <option disabled <?php echo ($selected_promo_id == 0) ? 'selected' : ''; ?> value="">Promo Belum Diklaim</option>
                    <?php foreach ($promos as $p): ?>
                        <option value="<?php echo $p['id']; ?>" <?php echo ($p['id'] == $selected_promo_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['nama_promo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="hidden" name="promo_id" value="<?php echo $selected_promo_id; ?>">
                <span style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--text-muted); font-size: 11px;">
                    <i class="fas fa-lock"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn-submit">Ambil Kode Voucher</button>
    </form>
    
    <p style="color:yellow;font-size: 12px;padding: 10px;text-align: center;">Jika sudah mendapatkan kode voucher, anda dapat menggunakannya saat melakukan reservasi.</p>
    <a href="./#promo" class="btn-warning" style="display: block; text-align: center; text-decoration: none; margin-top: 15px; border-radius: 8px; padding:5px;font-size:14px;">
    Kembali ke halaman utama</a>
</div>

<script>
    function salinVoucher() {
        var kodeVoucher = document.getElementById("teksVoucher").innerText;
        var btnCopy = document.getElementById("btnCopyBtn");
        var teksCopy = document.getElementById("teksCopy");

        navigator.clipboard.writeText(kodeVoucher).then(function() {
            btnCopy.classList.add("copied");
            teksCopy.innerText = "Tersalin!";
            
            setTimeout(function() {
                btnCopy.classList.remove("copied");
                teksCopy.innerText = "Salin Kode";
            }, 2000);
        }).catch(function(err) {
            console.error('Gagal menyalin text: ', err);
            alert("Perangkat Anda tidak mendukung fitur salin otomatis. Silakan salin secara manual.");
        });
    }
</script>

</body>
</html>