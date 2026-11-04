<?php
session_start();

$index = $_GET['index'];

unset($_SESSION['cart'][$index]);

$_SESSION['cart'] = array_values($_SESSION['cart']);

header("Location: transaksi.php");
exit;