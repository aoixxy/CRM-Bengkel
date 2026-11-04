<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}

$total_pelanggan = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM users WHERE role='pelanggan'")
);
?>

<?php $title = "Data Pelanggan"; ?>
<?php include '../templates/header.php'; ?>

<div class="flex min-h-screen bg-gradient-to-br from-slate-100 via-gray-100 to-slate-200">

<?php
$current_page = 'pelanggan';
include '../templates/sidebar_admin.php';
?>

<div class="flex-1 p-6 bg-gray-50">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
           <div>

    <h1 class="text-3xl font-black text-gray-800 leading-tight">
        Data Pelanggan
    </h1>

    <p class="text-gray-500 mt-1 text-sm">
        Kelola data pelanggan bengkel secara realtime
    </p>

</div>

<button
onclick="openTambahPelanggan()"
class="bg-gradient-to-r from-blue-600 to-cyan-500
text-white px-6 py-3 rounded-xl shadow
hover:shadow-lg transition">

    + Tambah Pelanggan

</button>

        </div>

        <!-- CARD STATS -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">

            <div class="bg-gradient-to-r from-blue-600 to-blue-500 text-white p-6 rounded-2xl shadow">

                <p class="opacity-80">
                    Total Pelanggan
                </p>

                <h2 class="text-4xl font-bold mt-2">
                    <?= $total_pelanggan ?>
                </h2>

            </div>

            <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-6 rounded-2xl shadow">

                <p class="opacity-80">
                    Pelanggan Aktif
                </p>

                <h2 class="text-4xl font-bold mt-2">
                    <?= $total_pelanggan ?>
                </h2>

            </div>

            <div class="bg-gradient-to-r from-orange-500 to-yellow-400 text-white p-6 rounded-2xl shadow">

                <p class="opacity-80">
                    Member Loyal
                </p>

                <h2 class="text-4xl font-bold mt-2">
                    <?= floor($total_pelanggan / 2) ?>
                </h2>

            </div>

        </div>

<!-- SEARCH -->
<div class="bg-white p-4 rounded shadow mb-4 flex justify-between items-center">

    <input
    type="text"
    id="searchPelanggan"
    placeholder="Cari pelanggan..."
    class="border p-2 rounded w-1/3">

</div>

<!-- TABLE -->
<div class="bg-white rounded shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-100">

                    <tr>
                        <th class="p-4 text-left">Nama</th>
                        <th class="p-4 text-left">No Telepon</th>
                        <th class="p-4 text-left">Status PIN</th>
                        <th class="p-4 text-left">Aksi</th>
                    </tr>

                </thead>

                <tbody id="tabelPelanggan">

                <?php
                $query = mysqli_query($conn,
                "SELECT * FROM users WHERE role='pelanggan'");

                while($row = mysqli_fetch_assoc($query)){
                ?>

                    <tr class="border-t hover:bg-gray-50 transition">

                        <td class="p-3 font-medium">
                            <?= $row['nama']; ?>
                        </td>

                        <td class="p-3">
                            <?= $row['no_tlp']; ?>
                        </td>

                        <td class="p-3">

                            <?php if($row['pin'] == NULL || $row['pin'] == '') { ?>

                                <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs">
                                    Belum Buat PIN
                                </span>

                            <?php } else { ?>

                                <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs">
                                    Sudah Aktif
                                </span>

                            <?php } ?>

                        </td>

                        <td class="p-3">

                            <div class="flex gap-4">

    <button
    onclick="openEditPelanggan(
        '<?= $row['id']; ?>',
        '<?= htmlspecialchars($row['nama']); ?>',
        '<?= htmlspecialchars($row['no_tlp']); ?>'
    )"
    class="text-blue-600 font-medium hover:underline">

        Edit

    </button>

    <button
    onclick="openDeletePelanggan(
        '<?= $row['id']; ?>',
        '<?= htmlspecialchars($row['nama']); ?>'
    )"
    class="text-red-600 font-medium hover:underline">

        Hapus

    </button>

</div>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- ================= MODAL TAMBAH PELANGGAN ================= -->
<div id="modalTambahPelanggan"
class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden">

        <!-- HEADER -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white p-6">

            <h2 class="text-2xl font-bold">
                Tambah Pelanggan
            </h2>

            <p class="text-sm opacity-90">
                Daftarkan member baru bengkel
            </p>

        </div>

        <!-- FORM -->
        <form action="proses_tambah_pelanggan.php" method="POST">

            <div class="p-6 space-y-5">

                <div>

                    <label class="block mb-2 font-medium">
                        Nama Pelanggan
                    </label>

                    <input
                    type="text"
                    name="nama"
                    required
                    class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-cyan-500">

                </div>

                <div>

                    <label class="block mb-2 font-medium">
                        Nomor Telepon
                    </label>

                    <input
                    type="text"
                    name="no_tlp"
                    required
                    class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-cyan-500">

                </div>

            </div>

            <!-- FOOTER -->
            <div class="border-t p-6 flex justify-end gap-3">

                <button
                type="button"
                onclick="closeTambahPelanggan()"
                class="px-5 py-2 border rounded-xl">

                    Batal

                </button>

                <button
                type="submit"
                class="bg-gradient-to-r from-blue-600 to-cyan-500
                text-white px-6 py-2 rounded-xl">

                    Simpan Pelanggan

                </button>

            </div>

        </form>

    </div>

</div>

<script>
const searchInput = document.getElementById('searchPelanggan');

searchInput.addEventListener('keyup', function() {

    let keyword = this.value.toLowerCase();

    let rows = document.querySelectorAll('#tabelPelanggan tr');

    rows.forEach(function(row){

        let text = row.innerText.toLowerCase();

        if(text.includes(keyword)){
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }

    });

});
</script>

<script>

function openTambahPelanggan(){

    document
    .getElementById('modalTambahPelanggan')
    .classList.remove('hidden');

    document
    .getElementById('modalTambahPelanggan')
    .classList.add('flex');
}

function closeTambahPelanggan(){

    document
    .getElementById('modalTambahPelanggan')
    .classList.remove('flex');

    document
    .getElementById('modalTambahPelanggan')
    .classList.add('hidden');
}

</script>

<!-- ================= MODAL EDIT ================= -->
<div id="modalEditPelanggan"
class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden">

        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white p-6">

            <h2 class="text-2xl font-bold">
                Edit Pelanggan
            </h2>

        </div>

        <form action="proses_edit_pelanggan.php" method="POST">

            <input
            type="hidden"
            id="edit_id"
            name="id">

            <div class="p-6 space-y-5">

                <div>

                    <label class="block mb-2 font-medium">
                        Nama Pelanggan
                    </label>

                    <input
                    type="text"
                    id="edit_nama"
                    name="nama"
                    required
                    class="w-full border rounded-xl p-3">

                </div>

                <div>

                    <label class="block mb-2 font-medium">
                        Nomor Telepon
                    </label>

                    <input
                    type="text"
                    id="edit_tlp"
                    name="no_tlp"
                    required
                    class="w-full border rounded-xl p-3">

                </div>

            </div>

            <div class="border-t p-6 flex justify-end gap-3">

                <button
                type="button"
                onclick="closeEditPelanggan()"
                class="border px-5 py-2 rounded-xl">

                    Batal

                </button>

                <button
                type="submit"
                class="bg-blue-600 text-white px-5 py-2 rounded-xl">

                    Update

                </button>

            </div>

        </form>

    </div>

</div>
<!-- ================= MODAL HAPUS ================= -->
<div id="modalDeletePelanggan"
class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md">

        <div class="p-8 text-center">

            <div class="text-6xl mb-4">
                ⚠️
            </div>

            <h3 class="text-xl font-bold mb-3">
                Hapus Pelanggan?
            </h3>

            <p class="text-gray-500 mb-6">

                Data

                <span
                id="delete_nama"
                class="font-bold text-gray-800">
                </span>

                akan dihapus permanen.

            </p>

            <form
            action="proses_hapus_pelanggan.php"
            method="POST">

                <input
                type="hidden"
                id="delete_id"
                name="id">

                <div class="flex justify-center gap-3">

                    <button
                    type="button"
                    onclick="closeDeletePelanggan()"
                    class="border px-5 py-2 rounded-xl">

                        Batal

                    </button>

                    <button
                    type="submit"
                    class="bg-red-600 text-white px-5 py-2 rounded-xl">

                        Hapus

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include '../templates/footer.php'; ?>