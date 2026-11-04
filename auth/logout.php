<?php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke homepage (ABSOLUTE)
header("Location: /crm_bengkel/index.php");
exit;