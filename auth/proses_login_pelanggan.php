<?php
session_start();
include '../config/koneksi.php';

$no_tlp = $_POST['no_tlp'];
$pin = $_POST['pin'];

$query = mysqli_query($conn,
"SELECT * FROM users
WHERE no_tlp='$no_tlp'
AND pin='$pin'
AND role='pelanggan'");

$data = mysqli_fetch_assoc($query);

if($data){

    $_SESSION['login'] = true;
    $_SESSION['id'] = $data['id'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['role'] = 'pelanggan';

    header("Location: ../pelanggan/dashboard.php");

} else {

    header("Location: login_pelanggan.php?error=1");

}
?>