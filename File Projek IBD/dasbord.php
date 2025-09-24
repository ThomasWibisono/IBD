<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['nama_bruder'])) {
    header('Location: login.php'); 
    exit;
}

$page_title = "Dashboard";
include 'includes/header.php';
?>
<h2>Selamat datang, <?= htmlspecialchars($_SESSION['nama_bruder'], ENT_QUOTES, 'UTF-8') ?></h2>

