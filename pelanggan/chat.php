<?php
session_start();
include "../config/koneksi.php";

// ================= CHECK SESSION LOGIN =================
if (isset($_SESSION['id']) && $_SESSION['role'] === 'pelanggan') {
    $user_id = $_SESSION['id'];
} else {
    header("Location: ../auth/login_pelanggan.php");
    exit;
}

/* ================= DATA PROFIL PELANGGAN ================= */
$query_pelanggan = mysqli_query($conn, "
    SELECT p.*, u.nama as nama_user 
    FROM pelanggan p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.user_id = '$user_id'
");
$p = mysqli_fetch_assoc($query_pelanggan);

// Perbaikan logika baris 34: Menggunakan Null Coalescing Operator (??) yang jauh lebih aman dari parse error
$id_pelanggan = $p['id'] ?? 0;
$nama   = $p['nama'] ?? $p['nama_user'] ?? 'Pelanggan';
$status = $p['status_rfm'] ?? 'New Customer';

/* ================= PROSES KIRIM PESAN CHAT ================= */
if (isset($_POST['kirim_pesan']) && !empty(trim($_POST['pesan']))) {
    $pesan = mysqli_real_escape_string($conn, $_POST['pesan']);
    
    $query_kirim = mysqli_query($conn, "
        INSERT INTO chat (pelanggan_id, pengirim, pesan, waktu) 
        VALUES ('$id_pelanggan', 'pelanggan', '$pesan', NOW())
    ");
    
    if ($query_kirim) {
        header("Location: chat.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat CS - SITANGGANG JAYA PRESS MOTOR</title>
    
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

        /* MAIN CONTENT - INTERFASE CHAT BOX */
        .main-content { flex: 1; padding: 35px; max-width: 1000px; display: flex; flex-direction: column; }
        
        .chat-box {
            background: white; border: 1px solid #e2e8f0; border-radius: 20px;
            flex: 1; display: flex; flex-direction: column; overflow: hidden;
            height: 500px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        
        .chat-header { background: #1e293b; color: white; padding: 15px 25px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .chat-header .status-dot { width: 8px; height: 8px; background: #2ecc71; border-radius: 50%; }
        
        .chat-messages { flex: 1; padding: 25px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; background: #f1f5f9; }
        
        /* Balon Chat */
        .msg { max-width: 70%; padding: 12px 16px; border-radius: 16px; font-size: 13px; line-height: 1.5; position: relative; word-wrap: break-word; }
        .msg.pelanggan { background: linear-gradient(135deg, #00c6ff, #0072ff); color: white; align-self: flex-end; border-bottom-right-radius: 2px; box-shadow: 0 2px 8px rgba(0,114,255,0.15); }
        .msg.admin { background: white; color: #334155; align-self: flex-start; border-bottom-left-radius: 2px; border: 1px solid #e2e8f0; }
        
        .msg-time { display: block; font-size: 10px; margin-top: 5px; opacity: 0.6; text-align: right; }

        /* Bagian Input Pesan */
        .chat-footer { padding: 15px 20px; background: white; border-top: 1px solid #e2e8f0; }
        .chat-form { display: flex; gap: 12px; }
        .chat-input { flex: 1; padding: 12px 18px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; outline: none; transition: border-color 0.2s; }
        .chat-input:focus { border-color: #0072ff; }
        
        .btn-send {
            background: linear-gradient(135deg, var(--primary-grad-1), var(--primary-grad-2));
            color: white; border: none; padding: 0 22px; border-radius: 12px;
            cursor: pointer; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center;
        }
        .btn-send:hover { opacity: 0.9; }

        /* BOTTOM NAV (Mobile) */
        .bottom-nav { display: none; position: fixed; bottom: 0; left: 0; width: 100%; background: white; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); justify-content: space-around; padding: 12px 0; z-index: 99; }
        .bottom-nav a { text-decoration: none; color: #94a3b8; display: flex; flex-direction: column; align-items: center; font-size: 11px; gap: 4px; }
        .bottom-nav a.active { color: #0072ff; }

        @media (max-width: 768px) {
            .navbar-menu, .sidebar { display: none; }
            .bottom-nav { display: flex; }
            .main-content { padding: 15px; padding-bottom: 80px; }
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
                <li><a href="profil.php"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="histori_transaksi.php"><i class="fas fa-history"></i> Histori Transaksi</a></li>
                <li><a href="reward.php"><i class="fas fa-gift"></i> Reward / Poin</a></li>
                <li class="active"><a href="chat.php"><i class="fas fa-comments"></i> Chat CS</a></li>
                <div class="menu-separator"></div>
                <li><a href="../auth/logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
            </ul>
        </aside>

        <!-- MAIN PANEL CONTENT -->
        <main class="main-content">
            <div class="chat-box">
                <div class="chat-header">
                    <div class="status-dot"></div>
                    <span><i class="fas fa-headset"></i> Customer Service Bengkel (Online)</span>
                </div>
                
                <div class="chat-messages" id="chatArea">
                    <?php
                    $q_chat = mysqli_query($conn, "SELECT * FROM chat WHERE pelanggan_id = '$id_pelanggan' ORDER BY id_chat ASC");
                    
                    if ($q_chat && mysqli_num_rows($q_chat) > 0) {
                        while ($c = mysqli_fetch_assoc($q_chat)) {
                            $is_pelanggan = ($c['pengirim'] === 'pelanggan');
                            ?>
                            <div class="msg <?= $is_pelanggan ? 'pelanggan' : 'admin' ?>">
                                <p><?= htmlspecialchars($c['pesan']) ?></p>
                                <span class="msg-time"><?= date('H:i', strtotime($c['waktu'])) ?></span>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="msg admin">
                            <p>Halo! Selamat datang di layanan konsultasi Sitanggang Jaya Press Motor. Ada yang bisa kami bantu mengenai keluhan atau booking jadwal servis motor Anda hari ini?</p>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <div class="chat-footer">
                    <form action="chat.php" method="POST" class="chat-form">
                        <input type="text" name="pesan" class="chat-input" placeholder="Tulis pesan konsultasi Anda di sini..." autocomplete="off" required>
                        <button type="submit" name="kirim_pesan" class="btn-send">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- BOTTOM NAVIGATION (Mobile) -->
    <div class="bottom-nav">
        <a href="dashboard.php"><i class="fas fa-th-large"></i><span>Home</span></a>
        <a href="profil.php"><i class="fas fa-user"></i><span>Profil</span></a>
        <a href="histori_transaksi.php"><i class="fas fa-history"></i><span>Histori</span></a>
        <a href="reward.php"><i class="fas fa-gift"></i><span>Reward</span></a>
        <a href="chat.php" class="active"><i class="fas fa-comments"></i><span>Chat</span></a>
    </div>

    <script>
        var chatArea = document.getElementById("chatArea");
        chatArea.scrollTop = chatArea.scrollHeight;
    </script>

</body>
</html>