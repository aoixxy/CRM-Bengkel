<?php
$title = "Buat PIN";
include '../templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">

<div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">

<div class="bg-blue-900 text-white text-center py-8">
    <h1 class="text-2xl font-bold">BUAT PIN</h1>
</div>

<div class="p-6">

<?php if(isset($_GET['error'])) { ?>
<div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
    Nomor tidak terdaftar atau PIN sudah dibuat
</div>
<?php } ?>

<form action="proses_buat_pin.php" method="POST" class="space-y-4">

<div>
<label class="text-sm text-gray-600">Nomor Telepon</label>
<input type="text" name="no_tlp" required
class="w-full mt-1 border p-2 rounded">
</div>

<div>
<label class="text-sm text-gray-600">Buat PIN (6 Digit)</label>
<input type="password" name="pin" maxlength="6" required
class="w-full mt-1 border p-2 rounded">
</div>

<button type="submit"
class="w-full bg-blue-900 text-white py-2 rounded hover:bg-blue-800">
    Simpan PIN
</button>

</form>

</div>
</div>
</div>

<?php include '../templates/footer.php'; ?>