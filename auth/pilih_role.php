<?php
$title = "Pilih Role";
include '../templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">

<div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">

<div class="bg-blue-900 text-white text-center py-8">
    <h1 class="text-2xl font-bold">SITANGGANG JAYA</h1>
    <p class="text-sm opacity-80">PRESS MOTOR</p>
</div>

<div class="p-6">

<div class="text-center mb-6">
    <h2 class="text-xl font-semibold">Pilih Role Login</h2>
    <p class="text-gray-500 text-sm">
        Silakan pilih akses login
    </p>
</div>

<div class="space-y-4">

<a href="login_admin.php"
class="block w-full bg-blue-900 text-white py-3 rounded-lg text-center hover:bg-blue-800 transition">
    Login Sebagai Admin
</a>

<a href="login_pelanggan.php"
class="block w-full bg-gray-800 text-white py-3 rounded-lg text-center hover:bg-gray-700 transition">
    Login Sebagai Pelanggan
</a>

</div>

</div>
</div>
</div>

<?php include '../templates/footer.php'; ?>