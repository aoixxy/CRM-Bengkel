<?php
session_start();
include "../config/koneksi.php"; // Jalur menuju file koneksi database Anda

// ================= CHECK SESSION LOGIN =================
if (isset($_SESSION['id']) && $_SESSION['role'] === 'pelanggan') {
    $user_id = $_SESSION['id'];
} else {
    header("Location: ../auth/login_pelanggan.php");
    exit;
}

/* ================= DATA PROFIL & KATEGORI PELANGGAN ================= */
// Menggabungkan data users dan data pelanggan berdasarkan relasi user_id
$query_pelanggan = mysqli_query($conn, "
    SELECT p.*, u.nama as nama_user, u.no_tlp 
    FROM pelanggan p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.user_id = '$user_id'
");

$p = mysqli_fetch_assoc($query_pelanggan);

if (!$p) {
    // Jika data profil di tabel pelanggan belum dibuat, ambil data dasar dari tabel users
    $query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
    $u = mysqli_fetch_assoc($query_user);
    
    $id_pelanggan = 0;
    $nama   = $u['nama'] ?? 'Pelanggan';
    $poin   = 0;
    $no_hp  = $u['no_tlp'] ?? '-';
    $status = 'New Customer';
    $tier   = 'Regular';
} else {
    $id_pelanggan = $p['id'];
    $nama   = $p['nama'] ?? $p['nama_user'];
    $poin   = $p['poin'] ?? 0;
    $no_hp  = $p['no_hp'] ?? $p['no_tlp'] ?? '-';
    $status = $p['status_rfm'] ?? 'New Customer';
    $tier   = $p['tier_member'] ?? 'Regular';
}

/* ================= TOTAL TRANSAKSI/SERVICE ================= */
$total_trx = 0;
if ($id_pelanggan > 0) {
    $q_trx = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE pelanggan_id = '$id_pelanggan'");
    if ($q_trx) {
        $trx = mysqli_fetch_assoc($q_trx);
        $total_trx = $trx['total'] ?? 0;
    }
}

/* ================= LOGIKA POP-UP PELANGGAN LOYAL ================= */
// Syarat: minimal 3 kali transaksi dalam periode 6 bulan ke belakang
$enam_bulan_lalu = date('Y-m-d', strtotime('-6 months'));
$jumlah_trx_6bln = 0;

if ($id_pelanggan > 0) {
    $q_loyal = mysqli_query($conn, "
        SELECT COUNT(*) as total_6bln 
        FROM transaksi 
        WHERE pelanggan_id = '$id_pelanggan' 
        AND (tanggal_servis >= '$enam_bulan_lalu' OR DATE(created_at) >= '$enam_bulan_lalu')
    ");
    if ($q_loyal) {
        $loyal_data = mysqli_fetch_assoc($q_loyal);
        $jumlah_trx_6bln = $loyal_data['total_6bln'] ?? 0;
    }
}

$is_loyal = ($jumlah_trx_6bln >= 3) ? true : false;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITANGGANG JAYA PRESS MOTOR - Panel Pelanggan</title>
    
    <!-- Google Fonts & Font Awesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-grad-1: #00c6ff;
            --primary-grad-2: #0072ff;
            --pink-grad: linear-gradient(135deg, #ff007f, #ff758c);
            --blue-grad: linear-gradient(135deg, #00c6ff, #0072ff);
            --green-grad: linear-gradient(135deg, #11998e, #38ef7d);
            --dark-blue: #1e293b;
            --bg-light: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0; padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: #334155;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* TOPBAR NAVBAR */
        .navbar {
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0; z-index: 100;
        }

        .navbar-brand { font-weight: 700; font-size: 18px; letter-spacing: 0.5px; }
        .navbar-menu { display: flex; gap: 25px; list-style: none; }
        .navbar-menu a { text-decoration: none; color: #64748b; font-weight: 500; }
        .navbar-icons { display: flex; align-items: center; gap: 20px; color: #64748b; font-size: 17px;}

        /* MASTER WRAPPER */
        .wrapper { display: flex; flex: 1; }

        /* SIDEBAR PANEL KIRI */
        .sidebar {
            width: 260px; background: white;
            border-right: 1px solid #e2e8f0;
            padding: 30px 20px;
            display: flex; flex-direction: column;
        }

        .profile-section { text-align: center; padding-bottom: 25px; border-bottom: 1px solid #e2e8f0; margin-bottom: 25px; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 12px; border: 3px solid #0072ff; }
        .profile-section h4 { font-size: 15px; font-weight: 600; color: #1e293b; }
        
        /* Title Kategori Pelanggan Di Bawah Profil */
        .badge-kategori {
            display: inline-block;
            background: #e0f2fe; color: #0369a1;
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 600; margin-top: 6px;
            text-transform: uppercase;
        }

        .menu-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .menu-list li a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 15px; color: #64748b; text-decoration: none;
            font-weight: 500; border-radius: 12px; transition: all 0.3s;
        }

        .menu-list li.active a, .menu-list li a:hover {
            background: linear-gradient(135deg, var(--primary-grad-1), var(--primary-grad-2));
            color: white; box-shadow: 0 4px 12px rgba(0, 114, 255, 0.2);
        }

        .menu-separator { height: 1px; background: #e2e8f0; margin: 15px 0; }

        /* MAIN CONTENT PANEL KANAN */
        .main-content { flex: 1; padding: 35px; max-width: 1200px; }
        .welcome-text { margin-bottom: 30px; }
        .welcome-text h2 { font-size: 25px; font-weight: 700; color: #1e293b; }

        /* GRADIENT CARDS GRID (Gaya image_8a77d9.jpg) */
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px; margin-bottom: 35px;
        }

        .card-grad {
            padding: 25px; border-radius: 20px; color: white;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .card-grad i { position: absolute; right: 20px; bottom: 20px; font-size: 45px; opacity: 0.2; }
        .card-grad p { font-size: 13px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }
        .card-grad h3 { font-size: 30px; margin: 8px 0 4px 0; font-weight: 700; }

        /* DATA INFO BLOCK (TABEL & SERVIS) */
        .info-section { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; margin-bottom: 35px; }
        @media (max-width: 992px) { .info-section { grid-template-columns: 1fr; } }

        .panel-data { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); border: 1px solid #e2e8f0; }
        .panel-title { font-size: 15px; font-weight: 600; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; color: #1e293b; }

        /* Table Histori Transaksi */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        table th { padding: 12px; background: #f1f5f9; color: #64748b; font-size: 12px; font-weight: 600; }
        table td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        .status-badge { background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 11px; }

        /* List Layanan & Jadwal Servis */
        .service-list { display: flex; flex-direction: column; gap: 15px; }
        .service-item { display: flex; align-items: center; gap: 15px; padding: 14px; border-radius: 12px; background: #f8fafc; border-left: 4px solid #0072ff; }
        .service-item.jadwal { border-left-color: #ff9900; }
        .service-info h5 { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
        .service-info p { font-size: 11px; color: #64748b; }

        /* POP-UP / MODAL LOYAL CUSTOMER */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); display: flex;
            justify-content: center; align-items: center; z-index: 1000;
            opacity: 0; visibility: hidden; transition: all 0.3s ease-in-out;
        }

        .modal-overlay.active { opacity: 1; visibility: visible; }
        .modal-content {
            background: white; padding: 35px; border-radius: 24px;
            width: 90%; max-width: 440px; text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15); transform: scale(0.8);
            transition: all 0.3s ease-in-out;
        }

        .modal-overlay.active .modal-content { transform: scale(1); }
        .modal-icon {
            width: 75px; height: 75px; background: #fef3c7; color: #d97706;
            font-size: 35px; display: flex; justify-content: center; align-items: center;
            border-radius: 50%; margin: 0 auto 20px auto; animation: bounce 2s infinite;
        }

        .modal-content h3 { font-size: 20px; margin-bottom: 10px; color: #1e293b; }
        .modal-content p { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 25px; }
        .btn-close {
            background: linear-gradient(135deg, var(--primary-grad-1), var(--primary-grad-2));
            color: white; border: none; padding: 11px 28px; border-radius: 10px;
            font-weight: 600; cursor: pointer; box-shadow: 0 4px 15px rgba(0, 114, 255, 0.25);
        }

        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }

        /* BOTTOM NAV BAR (Khusus Mode HP / Mobile Friendly) */
        .bottom-nav {
            display: none; position: fixed; bottom: 0; left: 0; width: 100%;
            background: white; box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            justify-content: space-around; padding: 12px 0; z-index: 99;
        }

        .bottom-nav a { text-decoration: none; color: #94a3b8; display: flex; flex-direction: column; align-items: center; font-size: 11px; gap: 4px; }
        .bottom-nav a.active, .bottom-nav a:hover { color: #0072ff; }

        /* RESPONSIF */
        @media (max-width: 768px) {
            .navbar-menu, .sidebar { display: none; }
            .bottom-nav { display: flex; }
            .main-content { padding: 20px; padding-bottom: 80px; }
            .navbar { padding: 15px 20px; }
        }
    </style>
</head>
<body>

    <!-- TOPBAR NAVBAR -->
    <nav class="navbar">
        <div class="navbar-brand">SITANGGANG JAYA PRESS MOTOR</div>
        <ul class="navbar-menu">
            <li><a href="#">Beranda</a></li>
            <li><a href="#">Layanan</a></li>
            <li><a href="#">Tentang Kami</a></li>
        </ul>
        <div class="navbar-icons">
            <i class="fas fa-search"></i>
            <i class="fas fa-bell"></i>
            <i class="fas fa-user-circle"></i>
        </div>
    </nav>

    <div class="wrapper">
        
        <!-- SIDEBAR (PANEL KIRI) -->
        <aside class="sidebar">
            <div class="profile-section">
                <!-- Fallback avatar placeholder jika logo belum diupload -->
                <img src="../assets/img/logo.jpeg" class="avatar" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3177/3177440.png'" alt="Profil">
                <h4><?= htmlspecialchars($nama) ?></h4>
                <!-- Menampilkan Kategori Pelanggan Tepat Di Bawah Profil -->
                <span class="badge-kategori"><?= htmlspecialchars($status) ?></span>
            </div>

            <ul class="menu-list">
                <li class="active"><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="profil.php"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="histori_transaksi.php"><i class="fas fa-history"></i> Histori Transaksi</a></li>
                <li><a href="reward.php"><i class="fas fa-gift"></i> Reward / Poin</a></li>
                <li><a href="chat.php"><i class="fas fa-comments"></i> Chat CS</a></li>
                <div class="menu-separator"></div>
                <li><a href="../auth/logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
            </ul>
        </aside>

        <!-- MAIN WINDOW PANEL KANAN -->
        <main class="main-content">
            <div class="welcome-text">
                <h2>Selamat datang kembali, <?= htmlspecialchars($nama) ?>! 👋</h2>
                <p style="color:#64748b; font-size:13px;">Monitor ringkasan aktivitas servis dan info poin loyalitas Anda secara berkala.</p>
            </div>

            <!-- CARDS GRID GRADIENT TAMPILAN GAYA REALTIME -->
            <div class="grid-cards">
                <div class="card-grad" style="background: var(--pink-grad);">
                    <p>Total Service</p>
                    <h3><?= $total_trx ?> Kali</h3>
                    <small>Riwayat servis resmi terdaftar</small>
                    <i class="fas fa-tools"></i>
                </div>
                <div class="card-grad" style="background: var(--blue-grad);">
                    <p>Poin Reward</p>
                    <h3><?= $poin ?> Pts</h3>
                    <small>Tingkatkan terus poin Anda</small>
                    <i class="fas fa-coins"></i>
                </div>
                <div class="card-grad" style="background: var(--green-grad);">
                    <p>Kategori Member</p>
                    <h3><?= htmlspecialchars($tier) ?></h3>
                    <small>Status loyalitas member saat ini</small>
                    <i class="fas fa-award"></i>
                </div>
            </div>

            <!-- TABEL DAN JADWAL SEKSI -->
            <div class="info-section">
                
                <!-- Histori Transaksi Terakhir (Kiri) -->
                <div class="panel-data">
                    <div class="panel-title">
                        <span><i class="fas fa-history"></i> Riwayat Transaksi Terakhir</span>
                        <a href="histori_transaksi.php" style="font-size:12px; color:#0072ff; text-decoration:none;">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode Transaksi</th>
                                    <th>Plat Nomor</th>
                                    <th>Total Biaya</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q_history = mysqli_query($conn, "SELECT * FROM transaksi WHERE pelanggan_id='$id_pelanggan' ORDER BY id DESC LIMIT 3");
                                if ($q_history && mysqli_num_rows($q_history) > 0) {
                                    while($row = mysqli_fetch_assoc($q_history)) {
                                        $tgl = $row['tanggal_servis'] ?? $row['created_at'];
                                        echo "<tr>
                                            <td>".date('d M Y', strtotime($tgl))."</td>
                                            <td><b>".htmlspecialchars($row['kode_transaksi'])."</b></td>
                                            <td><span style='text-transform:uppercase;'>".htmlspecialchars($row['plat_nomor'])."</span><br><small style='color:#64748b;'>".$row['jenis_motor']."</small></td>
                                            <td>Rp ".number_format($row['total'], 0, ',', '.')."</td>
                                            <td><span class='status-badge'>".($row['status'] ?? 'Selesai')."</span></td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center; color:#94a3b8; padding: 25px;'>Belum ada transaksi servis resmi terdaftar untuk akun ini.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Informasi Layanan & Jadwal Servis (Kanan) -->
                <div class="panel-data">
                    <div class="panel-title">
                        <span><i class="fas fa-info-circle"></i> Info Layanan & Jadwal</span>
                    </div>
                    <div class="service-list">
                        <!-- Informasi Layanan Produk -->
                        <div class="service-item">
                            <i class="fas fa-motorcycle" style="font-size: 20px; color: #0072ff;"></i>
                            <div class="service-info">
                                <h5>Layanan Paket Lengkap</h5>
                                <p>Tune-up CVT Komplit, Ganti Oli Mesin, & General Checkup Sasis.</p>
                            </div>
                        </div>
                        <!-- Jadwal Servis -->
                        <div class="service-item jadwal">
                            <i class="fas fa-calendar-alt" style="font-size: 20px; color: #ff9900;"></i>
                            <div class="service-info">
                                <h5>Rekomendasi Servis Rutin</h5>
                                <?php
                                // Ambil rekomendasi tanggal servis berikutnya jika ada dari tabel servis
                                $q_servis = mysqli_query($conn, "SELECT servis_berikutnya FROM servis WHERE pelanggan_id='$id_pelanggan' ORDER BY id_servis DESC LIMIT 1");
                                $s_data = mysqli_fetch_assoc($q_servis);
                                $tgl_next = isset($s_data['servis_berikutnya']) ? date('d M Y', strtotime($s_data['servis_berikutnya'])) : 'Silakan konsultasikan ke CS';
                                ?>
                                <p>Jadwal berikutnya: <b><?= $tgl_next ?></b></p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- BOTTOM NAVIGATION (Tampilan Responsif HP) -->
    <div class="bottom-nav">
        <a href="dashboard.php" class="active"><i class="fas fa-th-large"></i><span>Home</span></a>
        <a href="profil.php"><i class="fas fa-user"></i><span>Profil</span></a>
        <a href="histori_transaksi.php"><i class="fas fa-history"></i><span>Histori</span></a>
        <a href="reward.php"><i class="fas fa-gift"></i><span>Reward</span></a>
        <a href="chatcs.php"><i class="fas fa-comments"></i><span>Chat</span></a>
    </div>

    <!-- POP-UP LOYAL CUSTOMER BERDASARKAN FREKUENSI TRANSAKSI -->
    <?php if($is_loyal): ?>
    <div class="modal-overlay" id="loyalModal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-crown"></i>
            </div>
            <h3>Pelanggan Loyal Terdeteksi!</h3>
            <p>Luar biasa! Anda tercatat telah melakukan servis minimal <b>3 kali dalam periode 6 bulan terakhir</b>. Terima kasih banyak telah menjadi bagian pelanggan loyal di Sitanggang Jaya Press Motor.</p>
            <button class="btn-close" onclick="closeModal()">Sama-Sama</button>
        </div>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.getElementById('loyalModal').classList.add('active');
            }, 600); 
        });

        function closeModal() {
            document.getElementById('loyalModal').classList.remove('active');
        }
    </script>
    <?php endif; ?>

</body>
</html>