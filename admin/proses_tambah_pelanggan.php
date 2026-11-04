<?php

session_start();
include '../config/koneksi.php';

$nama = mysqli_real_escape_string(
    $conn,
    $_POST['nama']
);

$no_tlp = mysqli_real_escape_string(
    $conn,
    $_POST['no_tlp']
);

$cek = mysqli_query(
    $conn,
    "SELECT * FROM users
     WHERE no_tlp='$no_tlp'"
);

if(mysqli_num_rows($cek) > 0){

    echo "
    <script>
    alert('Nomor telepon sudah terdaftar');
    window.location='pelanggan.php';
    </script>
    ";

    exit;
}

/* ================= USERS ================= */

mysqli_query(
    $conn,
    "INSERT INTO users
    (
        nama,
        no_tlp,
        pin,
        role
    )
    VALUES
    (
        '$nama',
        '$no_tlp',
        NULL,
        'pelanggan'
    )"
);

$user_id = mysqli_insert_id($conn);

/* ================= CRM PELANGGAN ================= */

mysqli_query(
    $conn,
    "INSERT INTO pelanggan
    (
        nama,
        no_hp,
        poin,
        status_rfm,
        tier_member,
        user_id
    )
    VALUES
    (
        '$nama',
        '$no_tlp',
        0,
        'New Customer',
        'Regular',
        '$user_id'
    )"
);

header("Location: pelanggan.php");
exit;