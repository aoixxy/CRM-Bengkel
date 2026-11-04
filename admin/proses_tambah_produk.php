<?php

include '../config/koneksi.php';

$nama_produk = mysqli_real_escape_string(
    $conn,
    $_POST['nama_produk']
);

$kategori = mysqli_real_escape_string(
    $conn,
    $_POST['kategori']
);

$tipe = mysqli_real_escape_string(
    $conn,
    $_POST['tipe']
);

$jenis_motor = mysqli_real_escape_string(
    $conn,
    $_POST['jenis_motor']
);

$harga = (int)$_POST['harga'];

$stok = (int)$_POST['stok'];

mysqli_query(
    $conn,
    "INSERT INTO produk
    (
        nama_produk,
        kategori,
        tipe,
        jenis_motor,
        harga,
        stok
    )
    VALUES
    (
        '$nama_produk',
        '$kategori',
        '$tipe',
        '$jenis_motor',
        '$harga',
        '$stok'
    )"
);

header("Location: inventory.php");
exit;