<?php
session_start();
include '../config/koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($conn,
"SELECT * FROM users
WHERE username='$username'
AND password='$password'
AND role='admin'");

$data = mysqli_fetch_assoc($query);

if($data){

    $_SESSION['login'] = true;
    $_SESSION['id'] = $data['id'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['role'] = 'admin';

    header("Location: ../admin/dashboard.php");

} else {

    header("Location: login_admin.php?error=1");

}
?>