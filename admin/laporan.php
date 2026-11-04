<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<?php $title = "Laporan"; ?>
<?php include '../templates/header.php'; ?>

<div class="flex min-h-screen bg-gradient-to-br from-slate-100 via-gray-100 to-slate-200">

<?php
$current_page = 'laporan';
include '../templates/sidebar_admin.php';
?>

<div class="flex-1 p-6">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Tinjauan Kinerja</h1>

            <div class="flex gap-2">

    <form action="export_laporan.php" method="GET" class="flex gap-2">

        <select name="periode" class="border p-2 rounded">
            <option value="1bulan">Bulan Ini</option>
            <option value="3bulan" selected>3 Bulan</option>
            <option value="1tahun">1 Tahun</option>
        </select>

        <button type="submit" class="border px-3 py-2 rounded">
            Export
        </button>

    </form>

</div>
        </div>

        <!-- CARD -->
        <div class="grid md:grid-cols-3 gap-4 mb-6">

            <div class="bg-white p-4 rounded shadow">
                <p class="text-gray-500 text-sm">Total Pendapatan</p>
                <h2 class="text-xl font-bold text-blue-600">Rp 0</h2>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <p class="text-gray-500 text-sm">Transaksi Selesai</p>
                <h2 class="text-xl font-bold">0</h2>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <p class="text-gray-500 text-sm">Rata-rata Transaksi</p>
                <h2 class="text-xl font-bold">Rp 0</h2>
            </div>

        </div>

        <div class="grid md:grid-cols-3 gap-6">

            <!-- CHART -->
            <div class="md:col-span-2 bg-white p-4 rounded shadow">
                <h2 class="font-semibold mb-3">Pendapatan Seiring Waktu</h2>

                <canvas id="chart"></canvas>
            </div>

            <!-- PELANGGAN LOYAL -->
            <div class="bg-white p-4 rounded shadow">
                <h2 class="font-semibold mb-3">Pelanggan Loyal</h2>

                <div class="space-y-3">

                    <div class="flex justify-between">
                        <span>Budi Santoso</span>
                        <span class="text-blue-600">Rp 0</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Ahmad</span>
                        <span class="text-blue-600">Rp 0</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Siti</span>
                        <span class="text-blue-600">Rp 0</span>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('chart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        datasets: [{
            label: 'Pendapatan',
            data: [100, 200, 150, 300, 250, 400, 350],
            borderWidth: 1
        }]
    },
});
</script>


<?php include '../templates/footer.php'; ?>