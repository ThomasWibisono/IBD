<?php
if (!isset($page_title)) {
    $page_title = "Website Komunitas Bruder";
}
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        header nav a { margin-right: 10px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; text-align: left; }
        img { max-width: 80px; }
        form label { display: block; margin-top: 10px; }
        form input, form select, form textarea { width: 100%; max-width: 400px; padding: 5px; }
        button { margin-top: 15px; padding: 8px 15px; }
    </style>
</head>
<body>
<header>
    <h1><?= htmlspecialchars($page_title) ?></h1>
    <?php if (is_logged_in()): ?>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="data_anggaran.php">Data Anggaran</a>
            <a href="data_bruder.php">Data Bruder</a>
            <a href="kas_harian.php">Kas Harian</a>
            <a href="bank.php">Bank</a>
            <a href="logout.php">Logout</a>
        </nav>
    <?php endif; ?>
    <hr>
</header>
<main>
