<?php
session_start();
include "../config/koneksi.php"; // Jalur koneksi database

// ================= CHECK SESSION LOGIN =================
if (isset($_SESSION['id']) && $_SESSION['role'] === 'pelanggan') {
    $user_id = $_SESSION['id'];
} else {
    header("Location: ../auth/login_pelanggan.php");
    exit;
}

/* ================= DATA PROFIL PELANGGAN ================= */
$query_pelanggan = mysqli_query($conn, "
    SELECT p.*, u.nama as nama_user, u.no_tlp 
    FROM pelanggan p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.user_id = '$user_id'
");
$p = mysqli_fetch_assoc($query_pelanggan);

$id_pelanggan = $p['id'] ?? 0;
$nama   = $p['nama'] ?? $p['nama_user'] ?? 'Pelanggan';
$status = $p['status_rfm'] ?? 'New Customer';

/* ================= FITUR FILTER PENCARIAN ================= */
$search_query = "";
if (isset($_POST['search'])) {
    $keyword = mysqli_real_escape_string($conn, $_POST['keyword']);
    $search_query = " AND (kode_transaksi LIKE '%$keyword%' OR plat_nomor LIKE '%$keyword%' OR jenis_motor LIKE '%$keyword%') ";
}

/* ================= HITUNG TOTAL NOMINAL BELANJA ================= */
$total_pengeluaran = 0;
if ($id_pelanggan > 0) {
    $q_total = mysqli_query($conn, "SELECT SUM(total) as total_biaya FROM transaksi WHERE pelanggan_id = '$id_pelanggan'");
    $res_total = mysqli_fetch_assoc($q_total);
    $total_pengeluaran = $res_total['total_biaya'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Transaksi - SITANGGANG JAYA PRESS MOTOR</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary-grad-1: #00c6ff;
            --primary-grad-2: #0072ff;
            --blue-grad: linear-gradient(135deg, #00c6ff, #0072ff);
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
        .main-content { flex: 1; padding: 35px; max-width: 1200px; }
        .header-content { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px; }
        .header-content h2 { font-size: 24px; font-weight: 700; color: #1e293b; }

        /* CLOCK & SUMMARY CONTAINER */
        .header-right { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        
        /* LIVE CLOCK STYLE */
        .live-clock-box {
            background: white; border: 1px solid #e2e8f0; padding: 12px 20px; border-radius: 15px;
            display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }
        .live-clock-box i { color: #0072ff; font-size: 20px; }
        .live-clock-text { font-size: 13px; font-weight: 600; color: #1e293b; line-height: 1.4; }

        /* CARD PENGELUARAN */
        .card-summary { background: var(--blue-grad); color: white; padding: 15px 25px; border-radius: 15px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 12px rgba(0, 114, 255, 0.15); }
        .card-summary i { font-size: 30px; opacity: 0.8; }
        .card-summary p { font-size: 12px; opacity: 0.9; }
        .card-summary h4 { font-size: 18px; font-weight: 700; }

        /* SEARCH BAR & BARIS ACTION */
        .search-container { background: white; padding: 15px 25px; border-radius: 15px; border: 1px solid #e2e8f0; margin-bottom: 25px; }
        .search-form { display: flex; gap: 10px; }
        .search-input { flex: 1; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; }
        .search-input:focus { outline: none; border-color: #0072ff; }
        .btn-search { background: #1e293b; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .btn-search:hover { background: #0f172a; }

        /* PANEL TABEL HISTORI */
        .panel-table { background: white; border-radius: 20px; padding: 25px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        table th { padding: 14px 12px; background: #f1f5f9; color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        table td { padding: 16px 12px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #334155; }
        table tr:last-child td { border-bottom: none; }
        
        .status-badge { background: #dcfce7; color: #15803d; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 11px; display: inline-block; }
        .payment-badge { background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 500; }

        @media (max-width: 768px) {
            .navbar-menu, .sidebar { display: none; }
            .bottom-nav { display: flex; }
            .main-content { padding: 20px; padding-bottom: 80px; }
            .header-content { flex-direction: column; align-items: flex-start; }
            .header-right { width: 100%; flex-direction: column; align-items: stretch; gap: 10px; }
            .card-summary, .live-clock-box { width: 100%; justify-content: space-between; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">SITANGGANG JAYA PRESS MOTOR</div>
        <div class="navbar-icons">
            <i class="fas fa-bell"></i>
            <i class="fas fa-user-circle"></i>
        </div>
    </nav>

    <div class="wrapper">
        
        <aside class="sidebar">
            <div class="profile-section">
                <img src="../assets/img/logo.jpeg" class="avatar" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3177/3177440.png'" alt="Profil">
                <h4><?= htmlspecialchars($nama) ?></h4>
                <span class="badge-kategori"><?= htmlspecialchars($status) ?></span>
            </div>

            <ul class="menu-list">
                <li><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="profil.php"><i class="fas fa-user"></i> Profil</a></li>
                <li class="active"><a href="histori_transaksi.php"><i class="fas fa-history"></i> Histori Transaksi</a></li>
                <li><a href="reward.php"><i class="fas fa-gift"></i> Reward / Poin</a></li>
                <li><a href="chat.php"><i class="fas fa-comments"></i> Chat CS</a></li>
                <div class="menu-separator"></div>
                <li><a href="../auth/logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
            </ul>
        </aside>

        <main class="main-content">
            
            <div class="header-content">
                <div>
                    <h2>Histori Servis & Transaksi</h2>
                    <p style="color:#64748b; font-size:13px; margin-top:2px;">Transparansi seluruh riwayat perawatan berkala kendaraan Anda.</p>
                </div>
                
                <div class="header-right">
                    <div class="live-clock-box">
                        <i class="far fa-clock"></i>
                        <div class="live-clock-text" id="liveClock">Loading waktu...</div>
                    </div>

                    <div class="card-summary">
                        <i class="fas fa-wallet"></i>
                        <div>
                            <p>Total Dana Servis</p>
                            <h4>Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="search-container">
                <form action="histori_transaksi.php" method="POST" class="search-form">
                    <input type="text" name="keyword" class="search-input" placeholder="Cari berdasarkan Kode Transaksi, Plat Nomor, atau Jenis Motor..." value="<?= isset($_POST['keyword']) ? htmlspecialchars($_POST['keyword']) : '' ?>">
                    <button type="submit" name="search" class="btn-search">
                        <i class="fas fa-search"></i> Cari Data
                    </button>
                    <?php if(isset($_POST['search'])): ?>
                        <a href="histori_transaksi.php" class="btn-search" style="background:#e2e8f0; color:#475569; text-decoration:none;"><i class="fas fa-sync"></i> Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="panel-table">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Transaksi</th>
                                <th>Detail Kendaraan</th>
                                <th>Metode</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query menampilkan seluruh histori berdasarkan filter pencarian
                            $q_all_history = mysqli_query($conn, "
                                SELECT * FROM transaksi 
                                WHERE pelanggan_id='$id_pelanggan' 
                                $search_query 
                                ORDER BY id DESC
                            ");

                            if ($q_all_history && mysqli_num_rows($q_all_history) > 0) {
                                while($row = mysqli_fetch_assoc($q_all_history)) {
                                    // 🛠️ PERBAIKAN: Membaca kolom created_at terlebih dahulu agar data jam transaksi tidak menjadi 00:00
                                    $tgl_raw = $row['created_at'] ?? $row['tanggal_servis'];
                                    $metode  = $row['metode_pembayaran'] ?? 'Tunai';
                                    $status_trx = $row['status'] ?? 'Selesai';
                                    $poin_didapat = $row['poin_didapat'] ?? 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <b><?= date('d M Y', strtotime($tgl_raw)) ?></b><br>
                                            <small style="color:#94a3b8;"><?= date('H:i', strtotime($tgl_raw)) ?> WIB</small>
                                        </td>
                                        <td>
                                            <span style="color:#0072ff; font-weight:600;"><?= htmlspecialchars($row['kode_transaksi']) ?></span>
                                        </td>
                                        <td>
                                            <b style="text-transform:uppercase;"><?= htmlspecialchars($row['plat_nomor']) ?></b><br>
                                            <span style="color:#64748b; font-size:12px;"><?= htmlspecialchars($row['jenis_motor']) ?></span>
                                        </td>
                                        <td>
                                            <span class="payment-badge"><?= htmlspecialchars($metode) ?></span>
                                        </td>
                                        <td>
                                            <b style="color:#1e293b;">Rp <?= number_format($row['total'], 0, ',', '.') ?></b><br>
                                            <small style="color:#16a34a;">+<?= $poin_didapat ?> Poin</small>
                                        </td>
                                        <td>
                                            <span class="status-badge"><?= htmlspecialchars($status_trx) ?></span>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align:center; color:#94a3b8; padding: 40px;'>Tidak ditemukan riwayat data transaksi servis yang cocok.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <div class="bottom-nav">
        <a href="dashboard.php"><i class="fas fa-th-large"></i><span>Home</span></a>
        <a href="profil.php"><i class="fas fa-user"></i><span>Profil</span></a>
        <a href="histori_transaksi.php" class="active"><i class="fas fa-history"></i><span>Histori</span></a>
        <a href="reward.php"><i class="fas fa-gift"></i><span>Reward</span></a>
        <a href="chat.php"><i class="fas fa-comments"></i><span>Chat</span></a>
    </div>

    <script>
    function updateClock() {
        const now = new Date();
        const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        
        const date = now.getDate().toString().padStart(2, '0');
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        
        const liveString = `${date} ${month} ${year}<br><span style="color:#0072ff; font-weight:700;">${hours}:${minutes}:${seconds} WIB</span>`;
        document.getElementById('liveClock').innerHTML = liveString;
    }

    setInterval(updateClock, 1000);
    updateClock();
    </script>

</body>
</html>