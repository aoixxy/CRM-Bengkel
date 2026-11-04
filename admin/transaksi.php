<?php
session_start();

include '../config/koneksi.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$title = "Transaksi";
include '../templates/header.php';
?>

<div class="flex min-h-screen bg-gradient-to-br from-slate-100 via-gray-100 to-slate-200">

    <?php
    $current_page = 'transaksi';
    include '../templates/sidebar_admin.php';
    ?>

    <div class="flex-1 p-6">

        <h1 class="text-3xl font-bold text-slate-800 mb-6">
            Transaksi Baru
        </h1>

        <?php if (isset($_GET['success'])) { ?>
            <div class="bg-green-100 text-green-700 p-4 rounded-2xl mb-6 font-medium shadow-sm">
                🔹 Transaksi Berhasil disimpan dengan kode: <b><?= htmlspecialchars($_GET['code']); ?></b>. 
                Kembalian: <b>Rp <?= number_format($_GET['change'] ?? 0); ?></b>
            </div>
        <?php } ?>
        <?php if (isset($_GET['error'])) { ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-2xl mb-6 font-medium shadow-sm">
                ⚠ Gagal menyimpan transaksi. Periksa kembali inputan Anda.
            </div>
        <?php } ?>

        <form action="proses_transaksi.php" method="POST">

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

                <div class="xl:col-span-8 space-y-6">

                    <div class="bg-white rounded-3xl shadow-lg p-6">
                        <h2 class="font-bold text-xl mb-5">Data Kendaraan</h2>

                        <div class="mb-4">
                            <label class="block mb-2 font-medium">Jenis Pelanggan</label>
                            <select id="jenisPelanggan" name="jenis_pelanggan" class="w-full border border-gray-200 rounded-xl p-3">
                                <option value="umum">Pelanggan Umum</option>
                                <option value="member">Member</option>
                            </select>
                        </div>

                        <div id="memberArea" style="display:none;" class="mb-4">
                            <label class="block mb-2 font-medium">Cari Member</label>
                            <select name="pelanggan_id" class="w-full border border-gray-200 rounded-xl p-3">
                                <option value="">Pilih Member</option>
                                <?php
                                $member = mysqli_query($conn, "SELECT * FROM pelanggan ORDER BY nama ASC");
                                while ($m = mysqli_fetch_assoc($member)) {
                                ?>
                                    <option value="<?= $m['id']; ?>"><?= $m['nama']; ?> - <?= $m['no_hp']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="grid md:grid-cols-3 gap-4">
                            <input type="text" name="plat_nomor" placeholder="Plat Nomor" required class="border border-gray-200 rounded-xl p-3">
                            <input type="text" name="jenis_motor" placeholder="Jenis Motor" required class="border border-gray-200 rounded-xl p-3">
                            <input type="number" name="km_kendaraan" placeholder="KM Kendaraan" required class="border border-gray-200 rounded-xl p-3">
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-lg p-6">
                        <h2 class="font-bold text-xl mb-5">Produk / Jasa</h2>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="p-3 text-left">Item</th>
                                        <th class="p-3 text-center">Qty</th>
                                        <th class="p-3 text-right">Harga</th>
                                        <th class="p-3 text-right">Subtotal</th>
                                        <th class="p-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = 0;
                                    if (!empty($_SESSION['cart'])) {
                                        foreach ($_SESSION['cart'] as $index => $item) {
                                            $total += $item['subtotal'];
                                    ?>
                                            <tr class="border-b">
                                                <td class="p-3">
                                                    <?= htmlspecialchars($item['nama']); ?>
                                                    <input type="hidden" name="id_produk[]" value="<?= $item['id']; ?>">
                                                    <input type="hidden" name="harga[]" value="<?= $item['harga']; ?>">
                                                    <input type="hidden" name="qty[]" value="<?= $item['qty']; ?>">
                                                </td>
                                                <td class="p-3 text-center"><?= $item['qty']; ?></td>
                                                <td class="p-3 text-right">Rp <?= number_format($item['harga']); ?></td>
                                                <td class="p-3 text-right">Rp <?= number_format($item['subtotal']); ?></td>
                                                <td class="p-3 text-center">
                                                    <a href="hapus_item.php?index=<?= $index; ?>" class="text-red-500 hover:underline">Hapus</a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                    ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-gray-400 py-10">
                                                Belum ada item ditambahkan
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex gap-4 mt-6">
                            <button type="button" onclick="openModal('sparepart')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-sm font-semibold transition">+ Tambah Sparepart</button>
                            <button type="button" onclick="openModal('jasa')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-sm font-semibold transition">+ Tambah Jasa</button>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-4">
                    <div class="bg-white rounded-3xl shadow-lg p-6 sticky top-6">
                        <h2 class="font-bold text-xl mb-5">Ringkasan</h2>

                        <div class="flex justify-between mb-4 text-lg font-medium">
                            <span>Total</span>
                            <span id="total" class="font-bold text-blue-600">Rp <?= number_format($total); ?></span>
                            <input type="hidden" name="total_bayar" id="total_bayar" value="<?= $total; ?>">
                        </div>

                        <hr class="mb-4">

                        <label class="block mb-1 text-sm font-medium text-gray-600">Metode Pembayaran</label>
                        <select id="metodePembayaran" name="metode_pembayaran" class="w-full border border-gray-200 rounded-xl p-3 mb-4">
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>

                        <label class="block mb-1 text-sm font-medium text-gray-600">Jumlah Uang Bayar</label>
                        <input type="number" id="bayar" name="jumlah_bayar" min="<?= $total; ?>" placeholder="Masukkan nominal uang..." required class="w-full border border-gray-200 rounded-xl p-3 mb-4 font-semibold text-lg">

                        <div class="flex justify-between font-semibold text-lg border-t pt-4">
                            <span>Kembalian</span>
                            <span id="kembali" class="text-green-600 font-bold">Rp 0</span>
                        </div>

                        <button type="submit" name="selesai_transaksi" class="w-full mt-6 bg-gradient-to-r from-blue-600 to-cyan-500 hover:opacity-90 text-white rounded-xl py-4 font-bold shadow-md transition">
                            🚀 Selesaikan Transaksi
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

<div id="itemModal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <h3 id="modalTitle" class="font-bold text-lg mb-4">Tambah Item</h3>
        
        <form action="tambah_item.php" method="POST">
            
            <input type="hidden" name="tipe_item" id="tipe_item" value="sparepart">

            <div id="sparepartSelect" class="mb-4">
                <label class="block text-sm mb-1 font-medium">Pilih Sparepart</label>
                <select id="inputSparepart" class="w-full border border-gray-200 rounded-xl p-3">
                    <option value="">-- Pilih Sparepart --</option>
                    <?php
                    // Menampilkan sparepart yang stoknya di atas 0
                    $q_sp = mysqli_query($conn, "SELECT * FROM produk WHERE tipe='sparepart' AND stok > 0");
                    while($p = mysqli_fetch_assoc($q_sp)){
                        echo "<option value='".$p['id']."'>".htmlspecialchars($p['nama_produk'])." - Rp ".number_format($p['harga'])." (Stok: ".$p['stok'].")</option>";
                    }
                    ?>
                </select>
                <div class="mt-3">
                    <label class="block text-sm mb-1 font-medium">Jumlah (Qty)</label>
                    <input type="number" id="qtySparepart" value="1" min="1" class="w-full border border-gray-200 rounded-xl p-3">
                </div>
            </div>

            <div id="jasaSelect" class="mb-4" style="display: none;">
                <label class="block text-sm mb-1 font-medium">Pilih Jasa Servis</label>
                <select id="inputJasa" class="w-full border border-gray-200 rounded-xl p-3">
                    <option value="">-- Pilih Jasa --</option>
                    <?php
                    // Jasa tidak memiliki validasi stok
                    $q_js = mysqli_query($conn, "SELECT * FROM produk WHERE tipe='jasa'");
                    while($j = mysqli_fetch_assoc($q_js)){
                        echo "<option value='".$j['id']."'>".htmlspecialchars($j['nama_produk'])." - Rp ".number_format($j['harga'])."</option>";
                    }
                    ?>
                </select>
            </div>

            <input type="hidden" name="id_produk" id="final_id_produk" value="">
            <input type="hidden" name="qty" id="final_qty" value="1">

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-xl text-sm font-medium">Batal</button>
                <button type="submit" name="tambah_ke_keranjang" onclick="prepareSubmit()" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-semibold">Masukkan Keranjang</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    
    // Kontrol Tampilan Dropdown Member
    const jenisPelanggan = document.getElementById('jenisPelanggan');
    const memberArea = document.getElementById('memberArea');

    function toggleMember(){
        if(jenisPelanggan.value === 'member'){
            memberArea.style.display = 'block';
        } else {
            memberArea.style.display = 'none';
        }
    }
    toggleMember();
    jenisPelanggan.addEventListener('change', toggleMember);

    // Hitung Uang Kembalian Kasir Secara Real-time
    const totalHarga = parseFloat(document.getElementById('total_bayar').value) || 0;
    const inputBayar = document.getElementById('bayar');
    const textKembali = document.getElementById('kembali');

    inputBayar.addEventListener('input', function(){
        let nominalBayar = parseFloat(inputBayar.value) || 0;
        let kalkulasi = nominalBayar - totalHarga;

        if(kalkulasi >= 0){
            textKembali.innerText = "Rp " + kalkulasi.toLocaleString('id-ID');
            textKembali.style.color = "#16a34a";
        } else {
            textKembali.innerText = "Uang Kurang";
            textKembali.style.color = "#dc2626";
        }
    });
});

// Kontrol Modal Box Jasa / Sparepart
function openModal(type) {
    const modal = document.getElementById('itemModal');
    const sparepartDiv = document.getElementById('sparepartSelect');
    const jasaDiv = document.getElementById('jasaSelect');
    const tipeItemInput = document.getElementById('tipe_item');

    modal.style.display = 'flex';
    tipeItemInput.value = type; // Set penanda tipe aktif

    if(type === 'sparepart') {
        document.getElementById('modalTitle').innerText = "Tambah Sparepart Baru";
        sparepartDiv.style.display = 'block';
        jasaDiv.style.display = 'none';
    } else {
        document.getElementById('modalTitle').innerText = "Tambah Jasa Servis";
        sparepartDiv.style.display = 'none';
        jasaDiv.style.display = 'block';
    }
}

function closeModal() {
    document.getElementById('itemModal').style.display = 'none';
}

// Menjembatani ID Produk dan Qty terpilih agar dikirim secara tunggal dan rapi
function prepareSubmit() {
    const tipe = document.getElementById('tipe_item').value;
    const finalId = document.getElementById('final_id_produk');
    const finalQty = document.getElementById('final_qty');

    if(tipe === 'sparepart') {
        finalId.value = document.getElementById('inputSparepart').value;
        finalQty.value = document.getElementById('qtySparepart').value;
    } else {
        finalId.value = document.getElementById('inputJasa').value;
        finalQty.value = 1; // Jasa otomatis diset kuantitas = 1
    }
}
</script>

<?php include '../templates/footer.php'; ?>