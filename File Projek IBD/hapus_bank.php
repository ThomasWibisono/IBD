<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM `4_bank` WHERE ID_tabel_bank = ?");
    $stmt->execute([$id]);

    header("Location: anggaran_eco_bank.php");
    exit;

} catch (PDOException $e) {
    die("Gagal menghapus: " . $e->getMessage());
}
