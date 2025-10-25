<?php
session_start();

if (!isset($_SESSION['ID_bruder'])) {
    echo "unauthorized";
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // pastikan ada ID dikirim
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo "no_id";
        exit;
    }

    $id = intval($_POST['id']);

    // hapus data di tabel 3_kas_harian
    $stmt = $pdo->prepare("DELETE FROM `3_kas_harian` WHERE ID_kas_harian = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo "success";
    } else {
        echo "not_found";
    }

} catch (PDOException $e) {
    echo "error: " . $e->getMessage();
}
