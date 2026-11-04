<?php
$title = "Login Pelanggan";
include '../templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">

<div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">

<div class="bg-blue-900 text-white text-center py-8">
    <h1 class="text-2xl font-bold">LOGIN PELANGGAN</h1>
</div>

<div class="p-6">

<?php if(isset($_GET['error'])) { ?>
<div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
    Nomor Telepon atau PIN salah
</div>
<?php } ?>

<?php if(isset($_GET['success'])) { ?>
<div class="bg-green-100 text-green-600 p-3 rounded mb-4 text-sm">
    PIN berhasil dibuat, silakan login
</div>
<?php } ?>

<form action="proses_login_pelanggan.php" method="POST" class="space-y-4">

<div>
<label class="text-sm text-gray-600">Nomor Telepon</label>
<input type="text" name="no_tlp" required
class="w-full mt-1 border p-2 rounded">
</div>

<div>
<label class="text-sm text-gray-600">PIN</label>
<input type="password" name="pin" maxlength="6" required
class="w-full mt-1 border p-2 rounded">
</div>

<button type="submit"
class="w-full bg-blue-900 text-white py-2 rounded hover:bg-blue-800">
    Login Pelanggan
</button>

</form>

<div class="text-center mt-4 text-sm">
Belum mempunyai PIN?
<a href="buat_pin.php" class="text-blue-600">
Buat PIN
</a>
</div>

<div class="text-center mt-3">
<a href="pilih_role.php" class="text-blue-600 text-sm">
← Kembali
</a>
</div>

</div>
</div>
</div>

<?php include '../templates/footer.php'; ?>