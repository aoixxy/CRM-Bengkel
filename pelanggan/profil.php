<?php
session_start();
include "../config/koneksi.php"; // Jalur ke koneksi database

// ================= CHECK SESSION LOGIN =================
if (isset($_SESSION['id']) && $_SESSION['role'] === 'pelanggan') {
    $user_id = $_SESSION['id'];
} else {
    header("Location: ../auth/login_pelanggan.php");
    exit;
}

// Proses Update Profil jika tombol simpan ditekan
$success_msg = "";
$error_msg = "";

if (isset($_POST['update_profil'])) {
    $nama_baru   = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp_baru  = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat_baru = mysqli_real_escape_string($conn, $_POST['alamat']);

    // 1. Update data di tabel users
    $update_user = mysqli_query($conn, "UPDATE users SET nama='$nama_baru', no_tlp='$no_hp_baru' WHERE id='$user_id'");
    
    // 2. Update data di tabel pelanggan
    $update_pelanggan = mysqli_query($conn, "UPDATE pelanggan SET nama='$nama_baru', no_hp='$no_hp_baru', alamat='$alamat_baru' WHERE user_id='$user_id'");

    if ($update_user && $update_pelanggan) {
        $_SESSION['nama'] = $nama_baru; // Update session nama biar nama di pojok/sidebar langsung berubah
        $success_msg = "Profil berhasil diperbarui!";
    } else {
        $error_msg = "Gagal memperbarui profil. Silakan coba lagi.";
    }
}

/* ================= DATA PROFIL PELANGGAN ================= */
$query_pelanggan = mysqli_query($conn, "
    SELECT p.*, u.nama as nama_user, u.no_tlp 
    FROM pelanggan p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.user_id = '$user_id'
");
$p = mysqli_fetch_assoc($query_pelanggan);

// Fallback aman jika data di tabel pelanggan belum tersinkron utuh
$id_pelanggan = $p['id'] ?? 0;
$nama   = $p['nama'] ?? $p['nama_user'] ?? 'Pelanggan';
$no_hp  = $p['no_hp'] ?? $p['no_tlp'] ?? '-';
$alamat = $p['alamat'] ?? '';
$status = $p['status_rfm'] ?? 'New Customer';
$tier   = $p['tier_member'] ?? 'Regular';
$poin   = $p['poin'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - SITANGGANG JAYA PRESS MOTOR</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-grad-1: #00c6ff;
            --primary-grad-2: #0072ff;
            --bg-light: #f8fafc;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg-light); color: #334155; display: flex; flex-direction: column; min-height: 100vh; }

        /* NAVBAR */
        .navbar {
            background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100;
        }
        .navbar-brand { font-weight: 700; font-size: 18px; }
        .navbar-icons { display: flex; align-items: center; gap: 20px; color: #64748b; }

        .wrapper { display: flex; flex: 1; }

        /* SIDEBAR */
        .sidebar { width: 260px; background: white; border-right: 1px solid #e2e8f0; padding: 30px 20px; display: flex; flex-direction: column; }
        .profile-section { text-align: center; padding-bottom: 25px; border-bottom: 1px solid #e2e8f0; margin-bottom: 25px; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 12px; border: 3px solid #0072ff; }
        .profile-section h4 { font-size: 15px; font-weight: 600; }
        .badge-kategori { display: inline-block; background: #e0f2fe; color: #0369a1; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 6px; text-transform: uppercase; }

        .menu-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .menu-list li a { display: flex; align-items: center; gap: 12px; padding: 12px 15px; color: #64748b; text-decoration: none; font-weight: 500; border-radius: 12px; }
        .menu-list li.active a, .menu-list li a:hover { background: linear-gradient(135deg, var(--primary-grad-1), var(--primary-grad-2)); color: white; }
        .menu-separator { height: 1px; background: #e2e8f0; margin: 15px 0; }

        /* MAIN CONTENT */
        .main-content { flex: 1; padding: 35px; max-width: 900px; }
        .panel-profil { background: white; border-radius: 20px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
        .panel-title { font-size: 18px; font-weight: 600; margin-bottom: 25px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }

        /* Form Styling */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; color: #64748b; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; color: #334155; transition: border-color 0.2s; }
        .form-control:focus { outline: none; border-color: #0072ff; }
        .form-control:disabled { background-color: #f1f5f9; color: #94a3b8; }
        
        .row-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 576px) { .row-grid { grid-template-columns: 1fr; } }

        .btn-submit { background: linear-gradient(135deg, var(--primary-grad-1), var(--primary-grad-2)); color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 14px; box-shadow: 0 4px 12px rgba(0, 114, 255, 0.2); }
        
        /* Alert Box */
        .alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 500; margin-bottom: 20px; }
        .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* BOTTOM NAV (Mobile) */
        .bottom-nav { display: none; position: fixed; bottom: 0; left: 0; width: 100%; background: white; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); justify-content: space-around; padding: 12px 0; z-index: 99; }
        .bottom-nav a { text-decoration: none; color: #94a3b8; display: flex; flex-direction: column; align-items: center; font-size: 11px; gap: 4px; }
        .bottom-nav a.active { color: #0072ff; }

        @media (max-width: 768px) {
            .navbar-menu, .sidebar { display: none; }
            .bottom-nav { display: flex; }
            .main-content { padding: 20px; padding-bottom: 80px; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-brand">SITANGGANG JAYA PRESS MOTOR</div>
        <div class="navbar-icons">
            <i class="fas fa-bell"></i>
            <i class="fas fa-user-circle"></i>
        </div>
    </nav>

    <div class="wrapper">
        
        <!-- SIDEBAR (PANEL KIRI) -->
        <aside class="sidebar">
            <div class="profile-section">
                <img src="../assets/img/logo.jpeg" class="avatar" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3177/3177440.png'" alt="Profil">
                <h4><?= htmlspecialchars($nama) ?></h4>
                <span class="badge-kategori"><?= htmlspecialchars($status) ?></span>
            </div>

            <ul class="menu-list">
                <li><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li class="active"><a href="profil.php"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="histori_transaksi.php"><i class="fas fa-history"></i> Histori Transaksi</a></li>
                <li><a href="reward.php"><i class="fas fa-gift"></i> Reward / Poin</a></li>
                <li><a href="chat.php"><i class="fas fa-comments"></i> Chat CS</a></li>
                <div class="menu-separator"></div>
                <li><a href="../auth/logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
            </ul>
        </aside>

        <!-- MAIN PANEL CONTENT (FORM PROFIL) -->
        <main class="main-content">
            <div class="panel-profil">
                <div class="panel-title"><i class="fas fa-id-card"></i> Detail Profil Saya</div>
                
                <!-- Notifikasi System -->
                <?php if(!empty($success_msg)): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success_msg ?></div>
                <?php endif; ?>
                <?php if(!empty($error_msg)): ?>
                    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error_msg ?></div>
                <?php endif; ?>

                <form action="profil.php" method="POST">
                    
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($no_hp) ?>" required>
                    </div>

                    <div class="row-grid">
                        <div class="form-group">
                            <label>Poin Loyalitas</label>
                            <input type="text" class="form-control" value="<?= $poin ?> Pts" disabled>
                        </div>
                        <div class="form-group">
                            <label>Kategori Akun (Tier)</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($tier) ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat Rumah</label>
                        <textarea name="alamat" class="form-control" rows="4" placeholder="Masukkan alamat lengkap Anda..."><?= htmlspecialchars($alamat) ?></textarea>
                    </div>

                    <button type="submit" name="update_profil" class="btn-submit">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>

                </form>
            </div>
        </main>
    </div>

    <!-- BOTTOM NAVIGATION (Tampilan Responsif HP) -->
    <div class="bottom-nav">
        <a href="dashboard.php"><i class="fas fa-th-large"></i><span>Home</span></a>
        <a href="profil.php" class="active"><i class="fas fa-user"></i><span>Profil</span></a>
        <a href="histori_transaksi.php"><i class="fas fa-history"></i><span>Histori</span></a>
        <a href="reward.php"><i class="fas fa-gift"></i><span>Reward</span></a>
        <a href="chat.php"><i class="fas fa-comments"></i><span>Chat</span></a>
    </div>

</body>
</html>