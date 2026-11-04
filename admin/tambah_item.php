<?php
session_start();
include '../config/koneksi.php';

$id_produk = (int)$_POST['id_produk'];
$qty = (int)$_POST['qty'];

$produk = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT * FROM produk WHERE id='$id_produk'"
    )
);

if (!$produk) {
    header("Location: transaksi.php");
    exit;
}

$item = [
    'id' => $produk['id'],
    'nama' => $produk['nama_produk'],
    'harga' => $produk['harga'],
    'qty' => $qty,
    'subtotal' => $produk['harga'] * $qty,
    'tipe' => $produk['tipe']
];

$_SESSION['cart'][] = $item;

header("Location: transaksi.php");
exit;