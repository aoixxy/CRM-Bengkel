<?php
$title = "Login Admin";
include '../templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">

<div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">

<div class="bg-blue-900 text-white text-center py-8">
    <h1 class="text-2xl font-bold">LOGIN ADMIN</h1>
</div>

<div class="p-6">

<?php if(isset($_GET['error'])) { ?>
<div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
    Username atau Password salah
</div>
<?php } ?>

<form action="proses_login_admin.php" method="POST" class="space-y-4">

<div>
<label class="text-sm text-gray-600">Username</label>
<input type="text" name="username" required
class="w-full mt-1 border p-2 rounded">
</div>

<div>
<label class="text-sm text-gray-600">Password</label>
<input type="password" name="password" required
class="w-full mt-1 border p-2 rounded">
</div>

<button type="submit"
class="w-full bg-blue-900 text-white py-2 rounded hover:bg-blue-800">
    Login Admin
</button>

</form>

<div class="text-center mt-4">
<a href="pilih_role.php" class="text-blue-600 text-sm">
← Kembali
</a>
</div>

</div>
</div>
</div>

<?php include '../templates/footer.php'; ?>