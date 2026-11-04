<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}

// ================= TOTAL PELANGGAN =================
$total_pelanggan = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM users WHERE role='pelanggan'")
);

// ================= TOTAL PENJUALAN =================
$total_penjualan = 0;

// ================= PENDAPATAN HARIAN =================
$pendapatan_harian = 0;

?>

<?php $title = "Dashboard Admin"; ?>
<?php include '../templates/header.php'; ?>

<div class="flex min-h-screen bg-gradient-to-br from-slate-100 via-gray-100 to-slate-200">
    <!-- ================= SIDEBAR ================= -->
<div class="w-72 bg-white/80 backdrop-blur-xl border-r border-white/30 shadow-2xl p-6 flex flex-col justify-between min-h-screen">

    <!-- TOP -->
    <div>

        <!-- LOGO -->
        <div class="mb-10">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 rounded-2xl
                bg-gradient-to-br from-blue-600 to-cyan-400
                flex items-center justify-center text-white text-2xl shadow-lg">

                    🏍️

                </div>

                <div>

                    <h1 class="text-xl font-black text-gray-800 leading-tight">
                        SITANGGANG
                    </h1>

                    <p class="text-sm text-gray-500">
                        CRM Bengkel
                    </p>

                </div>

            </div>

        </div>

        <!-- MENU -->
        <ul class="space-y-3">

            <!-- DASHBOARD -->
            <li>

                <a href="dashboard.php"
                class="group flex items-center justify-between
                bg-gradient-to-r from-blue-600 to-cyan-500
                text-white px-4 py-4 rounded-2xl shadow-lg">

                    <div class="flex items-center gap-3">

                        <span class="text-xl">🏠</span>

                        <span class="font-semibold">
                            Dashboard
                        </span>

                    </div>

                    <span class="opacity-70 group-hover:translate-x-1 transition">
                        →
                    </span>

                </a>

            </li>

            <!-- PELANGGAN -->
            <li>

                <a href="pelanggan.php"
                class="group flex items-center justify-between
                hover:bg-blue-50 px-4 py-4 rounded-2xl transition">

                    <div class="flex items-center gap-3">

                        <span class="text-xl">👥</span>

                        <span class="font-medium text-gray-700">
                            Pelanggan
                        </span>

                    </div>

                    <span class="opacity-0 group-hover:opacity-100 transition">
                        →
                    </span>

                </a>

            </li>

            <!-- INVENTORY -->
            <li>

                <a href="inventory.php"
                class="group flex items-center justify-between
                hover:bg-orange-50 px-4 py-4 rounded-2xl transition">

                    <div class="flex items-center gap-3">

                        <span class="text-xl">📦</span>

                        <span class="font-medium text-gray-700">
                            Inventory
                        </span>

                    </div>

                    <span class="opacity-0 group-hover:opacity-100 transition">
                        →
                    </span>

                </a>

            </li>

            <!-- TRANSAKSI -->
            <li>

                <a href="transaksi.php"
                class="group flex items-center justify-between
                hover:bg-green-50 px-4 py-4 rounded-2xl transition">

                    <div class="flex items-center gap-3">

                        <span class="text-xl">💳</span>

                        <span class="font-medium text-gray-700">
                            Transaksi
                        </span>

                    </div>

                    <span class="opacity-0 group-hover:opacity-100 transition">
                        →
                    </span>

                </a>

            </li>

            <!-- LAPORAN -->
            <li>

                <a href="laporan.php"
                class="group flex items-center justify-between
                hover:bg-purple-50 px-4 py-4 rounded-2xl transition">

                    <div class="flex items-center gap-3">

                        <span class="text-xl">📊</span>

                        <span class="font-medium text-gray-700">
                            Laporan
                        </span>

                    </div>

                    <span class="opacity-0 group-hover:opacity-100 transition">
                        →
                    </span>

                </a>

            </li>

        </ul>

    </div>

    <!-- BOTTOM -->
    <div>

        <div class="bg-gradient-to-r from-red-500 to-pink-500 p-[1px] rounded-2xl">

            <button onclick="confirmLogout()"
            class="w-full bg-white hover:bg-red-50
            text-red-600 px-4 py-4 rounded-2xl
            flex items-center justify-between transition">

                <div class="flex items-center gap-3">

                    <span class="text-xl">🚪</span>

                    <span class="font-semibold">
                        Logout
                    </span>

                </div>

                <span>
                    →
                </span>

            </button>

        </div>

    </div>

</div>
    <!-- CONTENT -->
<div class="flex-1 p-8 overflow-y-auto">

<!-- ================= HEADER ================= -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

    <!-- TITLE -->
    <div>

        <h1 class="text-3xl font-black text-gray-800 leading-tight">
            Ringkasan Dashboard
        </h1>

        <p class="text-gray-500 mt-1 text-sm">
            Monitor aktivitas bengkel dan manajemen pelanggan secara realtime
        </p>

    </div>

    <!-- PROFILE -->
    <div class="bg-white/70 backdrop-blur-xl rounded-2xl shadow-md px-5 py-3 flex items-center gap-3 border border-white/40">

        <div class="w-12 h-12 rounded-xl
        bg-gradient-to-br from-blue-600 to-cyan-400
        flex items-center justify-center text-white text-xl shadow-lg">

            👨‍💼

        </div>

        <div>

            <p class="text-xs text-gray-500">
                Selamat Datang
            </p>

            <h3 class="font-bold text-base text-gray-800">
                <?= $_SESSION['nama']; ?>
            </h3>

        </div>

    </div>

</div>

<!-- ================= DASHBOARD CARD ================= -->
<div class="grid grid-cols-3 gap-3 mb-5">

    <!-- TOTAL PENJUALAN -->
    <a href="laporan.php"
    class="group relative overflow-hidden rounded-xl p-3
    bg-gradient-to-br from-purple-600 via-pink-500 to-pink-400
    shadow hover:shadow-lg
    transition duration-300 min-h-[110px]">

        <div class="flex justify-between items-start">

            <div>

                <p class="text-white/80 text-[10px]">
                    Total Penjualan
                </p>

                <h2 class="text-xl font-black text-white mt-1">
                    Rp <?= number_format($total_penjualan,0,',','.') ?>
                </h2>

            </div>

            <div class="text-3xl opacity-80">
                📊
            </div>

        </div>

        <div class="mt-4 text-white text-[11px] font-semibold">
            Detail →
        </div>

    </a>

    <!-- TOTAL PELANGGAN -->
    <a href="pelanggan.php"
    class="group relative overflow-hidden rounded-xl p-3
    bg-gradient-to-br from-blue-600 via-cyan-500 to-sky-400
    shadow hover:shadow-lg
    transition duration-300 min-h-[110px]">

        <div class="flex justify-between items-start">

            <div>

                <p class="text-white/80 text-[10px]">
                    Total Pelanggan
                </p>

                <h2 class="text-2xl font-black text-white mt-1">
                    <?= $total_pelanggan ?>
                </h2>

            </div>

            <div class="text-3xl opacity-80">
                👥
            </div>

        </div>

        <div class="mt-4 text-white text-[11px] font-semibold">
            Detail →
        </div>

    </a>

    <!-- PENDAPATAN HARIAN -->
    <a href="transaksi.php"
    class="group relative overflow-hidden rounded-xl p-3
    bg-gradient-to-br from-green-600 via-emerald-500 to-lime-400
    shadow hover:shadow-lg
    transition duration-300 min-h-[110px]">

        <div class="flex justify-between items-start">

            <div>

                <p class="text-white/80 text-[10px]">
                    Pendapatan Harian
                </p>

                <h2 class="text-xl font-black text-white mt-1">
                    Rp <?= number_format($pendapatan_harian,0,',','.') ?>
                </h2>

            </div>

            <div class="text-3xl opacity-80">
                💰
            </div>

        </div>

        <div class="mt-4 text-white text-[11px] font-semibold">
            Detail →
        </div>

    </a>

</div>

<!-- ================= MENU AKSI DASHBOARD ================= -->
<div class="grid grid-cols-2 gap-3">

    <!-- DATA PELANGGAN -->
    <a href="pelanggan.php"
    class="group relative overflow-hidden rounded-xl p-4
    bg-gradient-to-br from-blue-600 via-cyan-500 to-sky-400
    shadow hover:shadow-lg
    transition duration-300 min-h-[115px]">

        <div class="flex justify-between items-start">

            <div>

                <p class="text-white/80 text-[10px] tracking-wide">
                    Kelola Data
                </p>

                <h2 class="text-2xl font-black text-white mt-1">
                    Pelanggan
                </h2>

            </div>

            <div class="text-3xl opacity-80">
                👥
            </div>

        </div>

        <div class="flex items-center justify-between mt-6">

            <p class="text-white/70 text-[11px]">
                Data pelanggan
            </p>

            <span class="text-white font-semibold text-xs">
                →
            </span>

        </div>

    </a>

    <!-- INVENTORY -->
    <a href="inventory.php"
    class="group relative overflow-hidden rounded-xl p-4
    bg-gradient-to-br from-orange-500 via-amber-400 to-yellow-300
    shadow hover:shadow-lg
    transition duration-300 min-h-[115px]">

        <div class="flex justify-between items-start">

            <div>

                <p class="text-white/80 text-[10px] tracking-wide">
                    Manajemen
                </p>

                <h2 class="text-2xl font-black text-white mt-1">
                    Inventory
                </h2>

            </div>

            <div class="text-3xl opacity-80">
                📦
            </div>

        </div>

        <div class="flex items-center justify-between mt-6">

            <p class="text-white/70 text-[11px]">
                Stok sparepart
            </p>

            <span class="text-white font-semibold text-xs">
                →
            </span>

        </div>

    </a>

    <!-- TRANSAKSI -->
    <a href="transaksi.php"
    class="group relative overflow-hidden rounded-xl p-4
    bg-gradient-to-br from-green-600 via-emerald-500 to-lime-400
    shadow hover:shadow-lg
    transition duration-300 min-h-[115px]">

        <div class="flex justify-between items-start">

            <div>

                <p class="text-white/80 text-[10px] tracking-wide">
                    Penjualan
                </p>

                <h2 class="text-2xl font-black text-white mt-1">
                    Transaksi
                </h2>

            </div>

            <div class="text-3xl opacity-80">
                💳
            </div>

        </div>

        <div class="flex items-center justify-between mt-6">

            <p class="text-white/70 text-[11px]">
                Data transaksi
            </p>

            <span class="text-white font-semibold text-xs">
                →
            </span>

        </div>

    </a>

    <!-- LAPORAN -->
    <a href="laporan.php"
    class="group relative overflow-hidden rounded-xl p-4
    bg-gradient-to-br from-purple-600 via-pink-500 to-rose-400
    shadow hover:shadow-lg
    transition duration-300 min-h-[115px]">

        <div class="flex justify-between items-start">

            <div>

                <p class="text-white/80 text-[10px] tracking-wide">
                    Analisis
                </p>

                <h2 class="text-2xl font-black text-white mt-1">
                    Laporan
                </h2>

            </div>

            <div class="text-3xl opacity-80">
                📊
            </div>

        </div>

        <div class="flex items-center justify-between mt-6">

            <p class="text-white/70 text-[11px]">
                Statistik bengkel
            </p>

            <span class="text-white font-semibold text-xs">
                →
            </span>

        </div>

    </a>

</div>

        </div>

    </div>

</div>

<?php include '../templates/footer.php'; ?>