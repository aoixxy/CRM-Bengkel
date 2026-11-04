<?php
include '../config/koneksi.php';

$no_tlp = $_POST['no_tlp'];
$pin = $_POST['pin'];

$cek = mysqli_query($conn,
"SELECT * FROM users
WHERE no_tlp='$no_tlp'
AND role='pelanggan'
AND (pin IS NULL OR pin='')");

$data = mysqli_fetch_assoc($cek);

if($data){

    mysqli_query($conn,
    "UPDATE users SET pin='$pin'
    WHERE id='".$data['id']."'");

    header("Location: login_pelanggan.php?success=1");

} else {

    header("Location: buat_pin.php?error=1");

}
?>