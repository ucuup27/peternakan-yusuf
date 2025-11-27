<?php
session_start();
session_destroy(); // Hapus sesi
header("Location: login.php"); // Kembali ke depan
?>
