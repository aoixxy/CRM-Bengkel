<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// ================= STATISTIK INVENTORY =================

$total_produk = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM produk")
);

$stok_rendah = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT * FROM produk WHERE stok <= 5 AND stok > 0"
    )
);

$produk_habis = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT * FROM produk WHERE stok = 0"
    )
);

?>

<?php $title = "Inventory"; ?>
<?php include '../templates/header.php'; ?>

<div class="flex min-h-screen bg-gradient-to-br from-slate-100 via-gray-100 to-slate-200">

<?php
$current_page = 'inventory';
include '../templates/sidebar_admin.php';
?>

<div class="flex-1 p-6 bg-gray-50">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Manajemen Inventaris</h1>

<button
onclick="openTambahProduk()"
class="bg-gradient-to-r from-blue-600 to-cyan-500
text-white px-6 py-3 rounded-2xl shadow-lg
hover:shadow-xl transition">

    + Tambah Sparepart

</button>
</div>

       <div class="grid md:grid-cols-3 gap-6 mb-8">

    <!-- TOTAL PRODUK -->
    <div class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white p-6 rounded-2xl shadow-lg">

        <p class="opacity-80">
            Total Produk
        </p>

        <h2 class="text-4xl font-bold mt-2">
            <?= $total_produk ?>
        </h2>

    </div>

    <!-- STOK RENDAH -->
    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-6 rounded-2xl shadow-lg">

        <p class="opacity-80">
            Stok Rendah
        </p>

        <h2 class="text-4xl font-bold mt-2">
            <?= $stok_rendah ?>
        </h2>

    </div>

    <!-- PRODUK HABIS -->
    <div class="bg-gradient-to-r from-red-500 to-pink-500 text-white p-6 rounded-2xl shadow-lg">

        <p class="opacity-80">
            Produk Habis
        </p>

        <h2 class="text-4xl font-bold mt-2">
            <?= $produk_habis ?>
        </h2>

    </div>

</div>

        <!-- FILTER -->
        <div class="bg-white p-4 rounded shadow mb-4 flex justify-between items-center">

            <input type="text" placeholder="Cari inventaris..."
                class="border p-2 rounded w-1/3">

            <div class="flex gap-2">


<button
onclick="openFilter()"
class="border px-4 py-2 rounded-xl hover:bg-gray-50 transition">

    Filter

</button>
            </div>

        </div>

        <!-- TABLE -->
        <div class="bg-white rounded shadow overflow-hidden">

            <table class="w-full text-sm">

                <thead class="bg-gray-100">
                    <tr>
    <th class="p-3 text-left">Nama Barang</th>
    <th class="p-3 text-left">Kategori</th>
    <th class="p-3 text-left">Jenis Motor</th>
    <th class="p-3 text-left">Harga</th>
    <th class="p-3 text-left">Stok</th>
    <th class="p-3 text-left">Status</th>
    <th class="p-3 text-left">Aksi</th>
</tr>
                </thead>

                <tbody>

                    <?php
                    $query = mysqli_query($conn, "SELECT * FROM produk");
                    while ($row = mysqli_fetch_assoc($query)) {

                        $status = "Aman";
                        $warna = "green";

                        if ($row['stok'] <= 5) {
                            $status = "Rendah";
                            $warna = "yellow";
                        }

                        if ($row['stok'] == 0) {
                            $status = "Habis";
                            $warna = "red";
                        }
                    ?>

                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3"><?= $row['nama_produk']; ?></td>
                        <td class="p-3">
    <?= $row['kategori']; ?>
</td>

<td class="p-3">
    <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
        <?= ucfirst($row['jenis_motor']); ?>
    </span>
</td>

<td class="p-3">
    Rp <?= number_format($row['harga'],0,',','.'); ?>
</td>
                        <td class="p-3"><?= $row['stok']; ?></td>

                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded bg-<?= $warna ?>-100 text-<?= $warna ?>-700">
                                <?= $status ?>
                            </span>
                        </td>

                        <td class="p-3 flex gap-2">
                            <a href="#" class="text-blue-600">Edit</a>
                            <a href="#" class="text-red-600">Hapus</a>
                        </td>
                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- MODAL FILTER -->
<div id="filterModal"
class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white rounded-2xl p-6 w-full max-w-2xl shadow-2xl max-h-[90vh] overflow-y-auto">

        <h2 class="text-2xl font-bold mb-5">
            Filter Produk
        </h2>

        <form method="GET">

            <!-- JENIS MOTOR -->
            <h3 class="font-semibold mb-3 text-gray-700">
                Jenis Motor
            </h3>

            <div class="grid grid-cols-3 gap-3 mb-6">

                <label><input type="checkbox" name="motor[]" value="matic"> Matic</label>

                <label><input type="checkbox" name="motor[]" value="manual"> Manual</label>

                <label><input type="checkbox" name="motor[]" value="sport"> Sport</label>

            </div>

            <!-- KATEGORI -->
            <h3 class="font-semibold mb-3 text-gray-700">
                Kategori Produk
            </h3>

            <div class="grid grid-cols-2 gap-3">

                <label><input type="checkbox" name="kategori[]" value="Oli"> Oli</label>

                <label><input type="checkbox" name="kategori[]" value="Lahar"> Lahar</label>

                <label><input type="checkbox" name="kategori[]" value="Rantai"> Rantai</label>

                <label><input type="checkbox" name="kategori[]" value="Piston"> Piston</label>

                <label><input type="checkbox" name="kategori[]" value="Batrai"> Batrai</label>

                <label><input type="checkbox" name="kategori[]" value="Kampas Rem"> Kampas Rem</label>

                <label><input type="checkbox" name="kategori[]" value="Seal"> Seal</label>

                <label><input type="checkbox" name="kategori[]" value="Packing"> Packing</label>

                <label><input type="checkbox" name="kategori[]" value="Tali Belting"> Tali Belting</label>

                <label><input type="checkbox" name="kategori[]" value="Klep"> Klep</label>

                <label><input type="checkbox" name="kategori[]" value="Karburator"> Karburator</label>

                <label><input type="checkbox" name="kategori[]" value="Ban"> Ban</label>

                <label><input type="checkbox" name="kategori[]" value="Shockart"> Shockart</label>

                <label><input type="checkbox" name="kategori[]" value="Ring Piston"> Ring Piston</label>

                <label><input type="checkbox" name="kategori[]" value="Shock"> Shock</label>

                <label><input type="checkbox" name="kategori[]" value="As Shock"> As Shock</label>

                <label><input type="checkbox" name="kategori[]" value="Cakram"> Cakram</label>

                <label><input type="checkbox" name="kategori[]" value="Tali Gas"> Tali Gas</label>

                <label><input type="checkbox" name="kategori[]" value="Velg"> Velg</label>

                <label><input type="checkbox" name="kategori[]" value="Handgrip"> Handgrip</label>

            </div>

            <div class="flex justify-end gap-3 mt-8">

                <button
                type="button"
                onclick="closeFilter()"
                class="border px-4 py-2 rounded-xl">

                    Batal

                </button>

                <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-xl">

                    Terapkan

                </button>

            </div>

        </form>

    </div>

</div>

<script>

function openFilter(){
    document.getElementById('filterModal')
    .classList.remove('hidden');

    document.getElementById('filterModal')
    .classList.add('flex');
}

function closeFilter(){
    document.getElementById('filterModal')
    .classList.remove('flex');

    document.getElementById('filterModal')
    .classList.add('hidden');
}

</script>
<!-- MODAL TAMBAH PRODUK -->
<div id="modalTambahProduk"
class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-3xl w-full max-w-3xl shadow-2xl overflow-hidden">

        <!-- HEADER -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white p-5">

            <h2 class="text-2xl font-bold">
                Tambah Sparepart
            </h2>

            <p class="text-sm opacity-90">
                Tambahkan produk inventory bengkel
            </p>

        </div>

        <!-- FORM -->
        <form action="proses_tambah_produk.php" method="POST">

            <div class="p-6 grid md:grid-cols-2 gap-5">

                <!-- NAMA -->
                <div>
                    <label class="block mb-2 font-medium">
                        Nama Produk
                    </label>

                    <input
                    type="text"
                    name="nama_produk"
                    required
                    class="w-full border border-gray-300 rounded-xl p-3">
                </div>

                <!-- KATEGORI -->
                <div>

                    <label class="block mb-2 font-medium">
                        Kategori
                    </label>

                    <select
                    name="kategori"
                    required
                    class="w-full border border-gray-300 rounded-xl p-3">

                        <option value="">Pilih Kategori</option>

                        <option>Oli</option>
                        <option>Lahar</option>
                        <option>Rantai</option>
                        <option>Piston</option>
                        <option>Batrai</option>
                        <option>Kampas Rem</option>
                        <option>Seal</option>
                        <option>Packing</option>
                        <option>Tali Belting</option>
                        <option>Klep</option>
                        <option>Karburator</option>
                        <option>Ban</option>
                        <option>Shockart</option>
                        <option>Ring Piston</option>
                        <option>Shock</option>
                        <option>As Shock</option>
                        <option>Cakram</option>
                        <option>Tali Gas</option>
                        <option>Velg</option>
                        <option>Handgrip</option>

                    </select>

                </div>

                <div>

    <label class="block mb-2 font-medium">
        Tipe Produk
    </label>

    <select
    name="tipe"
    required
    class="w-full border border-gray-300 rounded-xl p-3">

        <option value="sparepart">
            Sparepart
        </option>

        <option value="jasa">
            Jasa
        </option>

    </select>

</div>

<div>

    <label class="block mb-2 font-medium">
        Tipe Produk
    </label>

    <select
    name="tipe"
    required
    class="w-full border border-gray-300 rounded-xl p-3">

        <option value="sparepart">
            Sparepart
        </option>

        <option value="jasa">
            Jasa
        </option>

    </select>

</div>

                <!-- JENIS MOTOR -->
                <div>

                    <label class="block mb-2 font-medium">
                        Jenis Motor
                    </label>

                    <select
                    name="jenis_motor"
                    required
                    class="w-full border border-gray-300 rounded-xl p-3">

                        <option value="">Pilih Jenis Motor</option>

                        <option value="matic">Matic</option>
                        <option value="manual">Manual</option>
                        <option value="sport">Sport</option>

                    </select>

                </div>

                <!-- HARGA -->
                <div>

                    <label class="block mb-2 font-medium">
                        Harga
                    </label>

                    <input
                    type="number"
                    name="harga"
                    required
                    class="w-full border border-gray-300 rounded-xl p-3">

                </div>

                <!-- STOK -->
                <div>

                    <label class="block mb-2 font-medium">
                        Stok
                    </label>

                    <input
                    type="number"
                    name="stok"
                    required
                    class="w-full border border-gray-300 rounded-xl p-3">

                </div>

            </div>

            <!-- FOOTER -->
            <div class="flex justify-end gap-3 p-6 border-t">

                <button
                type="button"
                onclick="closeTambahProduk()"
                class="px-5 py-2 border rounded-xl">

                    Batal

                </button>

                <button
                type="submit"
                class="bg-gradient-to-r from-blue-600 to-cyan-500
                text-white px-6 py-2 rounded-xl">

                    Simpan Produk

                </button>

            </div>

        </form>

    </div>

</div>

<script>

function openTambahProduk(){

    document
    .getElementById('modalTambahProduk')
    .classList.remove('hidden');

    document
    .getElementById('modalTambahProduk')
    .classList.add('flex');
}

function closeTambahProduk(){

    document
    .getElementById('modalTambahProduk')
    .classList.remove('flex');

    document
    .getElementById('modalTambahProduk')
    .classList.add('hidden');
}

</script>
<?php include '../templates/footer.php'; ?>