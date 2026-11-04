<?php
session_start();
include "../config/koneksi.php";

if (isset($_SESSION['id']) && $_SESSION['role'] === 'pelanggan') {
    $user_id = $_SESSION['id'];
} else {
    header("Location: ../auth/login_pelanggan.php");
    exit;
}

$query_pelanggan = mysqli_query($conn, "
    SELECT p.*, u.nama as nama_user, u.no_tlp 
    FROM pelanggan p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.user_id = '$user_id'
");
$p = mysqli_fetch_assoc($query_pelanggan);

$id_pelanggan = isset($p['id']) ? $p['id'] : 0;
$nama   = isset($p['nama']) ? $p['nama'] : 'Pelanggan';
$status = isset($p['status_rfm']) ? $p['status_rfm'] : 'New Customer';
$poin_sekarang = isset($p['poin']) ? $p['poin'] : 0;

$msg_success = "";
$msg_error = "";

if (isset($_GET['klaim'])) {
    $id_reward = intval($_GET['klaim']);
    $q_reward = mysqli_query($conn, "SELECT * FROM reward WHERE id_reward = '$id_reward' AND status = 'aktif'");
    
    if ($q_reward && mysqli_num_rows($q_reward) > 0) {
        $r_data = mysqli_fetch_assoc($q_reward);
        $poin_butuh = $r_data['poin_dibutuhkan'];
        $nama_reward = $r_data['nama_reward'];
        
        if ($poin_sekarang >= $poin_butuh) {
            $poin_baru = $poin_sekarang - $poin_butuh;
            $update_poin = mysqli_query($conn, "UPDATE pelanggan SET poin = '$poin_baru' WHERE id = '$id_pelanggan'");
            $insert_penukaran = mysqli_query($conn, "
                INSERT INTO penukaran_reward (pelanggan_id, reward_id, tanggal_penukaran, status) 
                VALUES ('$id_pelanggan', '$id_reward', NOW(), 'Menunggu')
            ");
            
            if ($update_poin && $insert_penukaran) {
                $poin_sekarang = $poin_baru;
                $msg_success = "Berhasil menukarkan reward!";
            } else {
                $msg_error = "Terjadi kesalahan sistem penukaran.";
            }
        } else {
            $msg_error = "Poin Anda tidak cukup.";
        }
    } else {
        $msg_error = "Reward tidak aktif.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reward - SITANGGANG MOTOR</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --primary-grad-1: #00c6ff; --primary-grad-2: #0072ff; --bg-light: #f8fafc; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg-light); color: #334155; display: flex; flex-direction: column; min-height: 100vh; }
        .navbar { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .navbar-brand { font-weight: 700; font-size: 18px; }
        .wrapper { display: flex; flex: 1; }
        .sidebar { width: 260px; background: white; border-right: 1px solid #e2e8f0; padding: 30px 20px; display: flex; flex-direction: column; }
        .profile-section { text-align: center; padding-bottom: 25px; border-bottom: 1px solid #e2e8f0; margin-bottom: 25px; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 12px; border: 3px solid #0072ff; }
        .badge-kategori { display: inline-block; background: #e0f2fe; color: #0369a1; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 6px; text-transform: uppercase; }
        .menu-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .menu-list li a { display: flex; align-items: center; gap: 12px; padding: 12px 15px; color: #64748b; text-decoration: none; font-weight: 500; border-radius: 12px; }
        .menu-list li.active a, .menu-list li a:hover { background: linear-gradient(135deg, var(--primary-grad-1), var(--primary-grad-2)); color: white; }
        .menu-separator { height: 1px; background: #e2e8f0; margin: 15px 0; }
        .main-content { flex: 1; padding: 35px; max-width: 1200px; }
        .poin-box { background: linear-gradient(135deg, #1e293b, #0f172a); color: white; padding: 30px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
        .poin-info h3 { font-size: 32px; font-weight: 700; color: #00c6ff; margin-top: 5px; }
        .reward-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        .reward-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 25px; text-align: center; display: flex; flex-direction: column; justify-content: space-between; height: 260px; }
        .reward-icon { width: 55px; height: 55px; background: #fff3b0; color: #e65c00; font-size: 22px; display: flex; justify-content: center; align-items: center; border-radius: 50%; margin: 0 auto 12px auto; }
        .pts-badge { display: inline-block; background: #fef3c7; color: #d97706; font-weight: 700; font-size: 13px; padding: 4px 14px; border-radius: 12px; margin-bottom: 12px; }
        .btn-klaim { background: linear-gradient(135deg, var(--primary-grad-1), var(--primary-grad-2)); color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 13px; display: block; }
        .btn-disabled { background: #e2e8f0; color: #94a3b8; pointer-events: none; }
        .alert { padding: 14px; border-radius: 12px; font-size: 13px; margin-bottom: 25px; font-weight: 500; }
        .alert-success { background: #dcfce7; color: #15803d; }
        .alert-error { background: #fee2e2; color: #b91c1c; }
        .bottom-nav { display: none; position: fixed; bottom: 0; left: 0; width: 100%; background: white; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); justify-content: space-around; padding: 12px 0; z-index: 99; }
        .bottom-nav a { text-decoration: none; color: #94a3b8; display: flex; flex-direction: column; align-items: center; font-size: 11px; gap: 4px; }
        .bottom-nav a.active { color: #0072ff; }
        @media (max-width: 768px) { .navbar-menu, .sidebar { display: none; } .bottom-nav { display: flex; } .main-content { padding: 20px; padding-bottom: 80px; } }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">SITANGGANG JAYA PRESS MOTOR</div>
    </nav>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="profile-section">
                <img src="../assets/img/logo.jpeg" class="avatar" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3177/3177440.png'">
                <h4><?= htmlspecialchars($nama) ?></h4>
                <span class="badge-kategori"><?= htmlspecialchars($status) ?></span>
            </div>
            <ul class="menu-list">
                <li><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="profil.php"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="histori_transaksi.php"><i class="fas fa-history"></i> Histori Transaksi</a></li>
                <li class="active"><a href="reward.php"><i class="fas fa-gift"></i> Reward / Poin</a></li>
                <li><a href="chat.php"><i class="fas fa-comments"></i> Chat CS</a></li>
                <div class="menu-separator"></div>
                <li><a href="../auth/logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="poin-box">
                <div>
                    <p style="font-size: 13px; text-transform: uppercase;">Poin Loyalitas Anda</p>
                    <h3><?= $poin_sekarang ?> Pts</h3>
                </div>
                <i class="fas fa-coins" style="font-size: 40px; color: #ffb703;"></i>
            </div>

            <?php if(!empty($msg_success)): ?><div class="alert alert-success"><?= $msg_success ?></div><?php endif; ?>
            <?php if(!empty($msg_error)): ?><div class="alert alert-error"><?= $msg_error ?></div><?php endif; ?>

            <div class="reward-grid">
                <?php
                $q_catalog = mysqli_query($conn, "SELECT * FROM reward WHERE status = 'aktif' ORDER BY poin_dibutuhkan ASC");
                if ($q_catalog && mysqli_num_rows($q_catalog) > 0) {
                    while($row = mysqli_fetch_assoc($q_catalog)) {
                        $cukup = ($poin_sekarang >= $row['poin_dibutuhkan']);
                        ?>
                        <div class="reward-card">
                            <div>
                                <div class="reward-icon"><i class="fas fa-gift"></i></div>
                                <h4><?= htmlspecialchars($row['nama_reward']) ?></h4>
                            </div>
                            <div>
                                <div class="pts-badge"><?= $row['poin_dibutuhkan'] ?> Poin</div>
                                <a href="<?= $cukup ? 'reward.php?klaim='.$row['id_reward'] : '#' ?>" class="btn-klaim <?= !$cukup ? 'btn-disabled' : '' ?>">Tukarkan</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="reward-card">
                        <div>
                            <div class="reward-icon"><i class="fas fa-box-open"></i></div>
                            <h4>Katalog Kosong</h4>
                            <p style="font-size: 12px; color: #64748b; margin-top:5px;">Belum ada reward aktif di database.</p>
                        </div>
                        <div><div class="pts-badge">0 Poin</div><a href="#" class="btn-klaim btn-disabled">Tukarkan</a></div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </main>
    </div>
    <div class="bottom-nav">
        <a href="dashboard.php"><i class="fas fa-th-large"></i><span>Home</span></a>
        <a href="profil.php"><i class="fas fa-user"></i><span>Profil</span></a>
        <a href="histori_transaksi.php"><i class="fas fa-history"></i><span>Histori</span></a>
        <a href="reward.php" class="active"><i class="fas fa-gift"></i><span>Reward</span></a>
        <a href="chat.php"><i class="fas fa-comments"></i><span>Chat</span></a>
    </div>
</body>
</html>