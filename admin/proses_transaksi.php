<?php
session_start();
// PAKSA TIMEZONE KE WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

include "../config/koneksi.php"; 

// Pastikan yang mengakses adalah admin yang sudah login
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}

if (isset($_POST['selesai_transaksi'])) {
    
    // 1. AMBIL DATA UTAMA DARI FORM KENDARAAN & PEMBAYARAN
    $jenis_pelanggan    = $_POST['jenis_pelanggan']; 
    $plat_nomor         = mysqli_real_escape_string($conn, $_POST['plat_nomor']);
    $jenis_motor        = mysqli_real_escape_string($conn, $_POST['jenis_motor']);
    $km_kendaraan       = intval($_POST['km_kendaraan']);
    
    $metode_pembayaran  = $_POST['metode_pembayaran'];
    $total_bayar        = floatval($_POST['total_bayar']); 
    $jumlah_bayar       = floatval($_POST['jumlah_bayar']); 
    
    // Generate Otomatis Kode Transaksi (Contoh: TRX-20260606-001)
    $tanggal_hari_ini   = date('Ymd');
    $query_max_code     = mysqli_query($conn, "SELECT MAX(kode_transaksi) as max_code FROM transaksi WHERE kode_transaksi LIKE 'TRX-$tanggal_hari_ini%'");
    $data_max           = mysqli_fetch_assoc($query_max_code);
    
    $last_code          = $data_max['max_code'] ?? '';
    
    if (!empty($last_code)) {
        $no_urut = substr($last_code, -3);
        $no_urut = intval($no_urut) + 1;
    } else {
        $no_urut = 1;
    }
    $kode_transaksi = "TRX-" . $tanggal_hari_ini . "-" . sprintf("%03d", $no_urut);

    // Tentukan relasi pelanggan_id & kalkulasi bonus poin
    $pelanggan_id = NULL;
    $nama_pelanggan = "Pelanggan Umum";
    $no_tlp = NULL;
    $poin_didapat = 0;

    if ($jenis_pelanggan === "member" && isset($_POST['pelanggan_id']) && !empty($_POST['pelanggan_id'])) {
        $id_p = intval($_POST['pelanggan_id']);
        $q_pel = mysqli_query($conn, "SELECT * FROM pelanggan WHERE id = '$id_p'");
        if ($q_pel && mysqli_num_rows($q_pel) > 0) {
            $d_pel = mysqli_fetch_assoc($q_pel);
            $pelanggan_id   = $d_pel['id'];
            $nama_pelanggan = $d_pel['nama'];
            $no_tlp         = $d_pel['no_hp'];
            
            // Aturan CRM: Setiap kelipatan Rp 50.000 mendapatkan bonus 5 Poin
            $poin_didapat   = floor($total_bayar / 50000) * 5;
        }
    }

    // 🛠️ PERBAIKAN UTAMA: Ambil Tanggal dan Jam Realtime dari server PHP (WIB)
    $waktu_realtime_wib = date('Y-m-d H:i:s');
    $tanggal_servis_wib = date('Y-m-d');

    // 2. INSERT DATA UTAMA KE TABEL `transaksi`
    $val_pelanggan_id = $pelanggan_id ? "'$pelanggan_id'" : "NULL";
    $val_no_tlp       = $no_tlp ? "'$no_tlp'" : "NULL";

    // Gantikan fungsi NOW() dan CURDATE() dengan variabel string PHP agar waktu tercatat detail hingga ke detik jamnya
    $query_transaksi = mysqli_query($conn, "
        INSERT INTO transaksi (kode_transaksi, pelanggan_id, nama_pelanggan, no_tlp, plat_nomor, jenis_motor, km_kendaraan, metode_pembayaran, total, status, created_at, tanggal_servis, poin_didapat) 
        VALUES ('$kode_transaksi', $val_pelanggan_id, '$nama_pelanggan', $val_no_tlp, '$plat_nomor', '$jenis_motor', '$km_kendaraan', '$metode_pembayaran', '$total_bayar', 'Selesai', '$waktu_realtime_wib', '$tanggal_servis_wib', '$poin_didapat')
    ");

    if ($query_transaksi) {
        $transaksi_id = mysqli_insert_id($conn);

        // 3. AMBIL DATA BARANG & JASA YANG MASUK KERANJANG ARRAY
        if (isset($_POST['id_produk']) && is_array($_POST['id_produk'])) {
            for ($i = 0; $i < count($_POST['id_produk']); $i++) {
                $id_prod  = intval($_POST['id_produk'][$i]);
                $qty      = intval($_POST['qty'][$i]);
                $harga    = floatval($_POST['harga'][$i]);
                $subtotal = $qty * $harga;

                $q_p = mysqli_query($conn, "SELECT nama_produk, tipe FROM produk WHERE id = '$id_prod'");
                $nama_item = 'Item Tidak Diketahui';
                $tipe_item = 'sparepart';
                
                if ($q_p && mysqli_num_rows($q_p) > 0) {
                    $d_p = mysqli_fetch_assoc($q_p);
                    $nama_item = $d_p['nama_produk'] ?? 'Item Tidak Diketahui';
                    $tipe_item = $d_p['tipe'] ?? 'sparepart';
                }

                // Insert ke tabel `detail_transaksi`
                mysqli_query($conn, "
                    INSERT INTO detail_transaksi (transaksi_id, nama_item, qty, harga, subtotal) 
                    VALUES ('$transaksi_id', '$nama_item', '$qty', '$harga', '$subtotal')
                ");

                // Jika tipe item adalah 'sparepart', kurangi stok di tabel `produk`
                if ($tipe_item === 'sparepart') {
                    mysqli_query($conn, "UPDATE produk SET stok = stok - $qty WHERE id = '$id_prod'");
                }
            }
        }

        // 4. UPDATE POIN MEMBER JIKA DIA ADALAH PELANGGAN TERDAFTAR
        if ($pelanggan_id && $poin_didapat > 0) {
            mysqli_query($conn, "UPDATE pelanggan SET poin = poin + $poin_didapat WHERE id = '$pelanggan_id'");
        }

        // Kosongkan sesi keranjang belanja setelah berhasil disimpan permanen
        $_SESSION['cart'] = [];

        $kembalian = $jumlah_bayar - $total_bayar;
        header("Location: transaksi.php?success=1&code=$kode_transaksi&change=$kembalian");
        exit;

    } else {
        header("Location: transaksi.php?error=gagal_simpan");
        exit;
    }
} else {
    header("Location: ../admin/transaksi.php");
    exit;
}
?>