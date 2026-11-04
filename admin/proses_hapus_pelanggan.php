<?php

include '../config/koneksi.php';

$id = $_POST['id'];

mysqli_query(
$conn,
"DELETE FROM users
WHERE id='$id'"
);

header("Location: pelanggan.php");
exit;