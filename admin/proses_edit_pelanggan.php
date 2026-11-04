<?php

include '../config/koneksi.php';

$id     = $_POST['id'];
$nama   = $_POST['nama'];
$no_tlp = $_POST['no_tlp'];

mysqli_query(
$conn,
"UPDATE users
SET
nama='$nama',
no_tlp='$no_tlp'
WHERE id='$id'"
);

header("Location: pelanggan.php");
exit;