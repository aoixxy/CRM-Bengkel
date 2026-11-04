<?php

$message = strtolower($_POST['message'] ?? '');

$reply = "Maaf, saya belum mengerti pertanyaan Anda.";

// RULE BASED AI (SIMPLE CRM)
if (strpos($message, 'servis') !== false) {
    $reply = "Kami menyediakan servis berkala motor mulai dari Rp50.000.";
}
elseif (strpos($message, 'oli') !== false) {
    $reply = "Ganti oli tersedia, harga mulai dari Rp40.000.";
}
elseif (strpos($message, 'buka') !== false) {
    $reply = "Bengkel buka pukul 08:00 - 19:00 WIB.";
}
elseif (strpos($message, 'alamat') !== false) {
    $reply = "Kami berada di SITANGGANG JAYA PRESS MOTOR.";
}
elseif (strpos($message, 'promo') !== false) {
    $reply = "Gratis ganti oli setelah 3x transaksi dalam 6 bulan!";
}

echo $reply;