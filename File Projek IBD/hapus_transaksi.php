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

    // ambil data dari 3_kas_harian
    $stmt = $pdo->prepare("SELECT * FROM `3_kas_harian` WHERE ID_kas_harian = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo "not_found";
        exit;
    }

    // masukkan ke tabel arsip kas_harian_deleted
    $stmt = $pdo->prepare("
        INSERT INTO 3_kas_harian_deleted
        (ID_kas_harian, tgl_kas_harian, ID_pos, keterangan_kas, ID_bruder, nominal, deleted_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $data['ID_kas_harian'],
        $data['tgl_kas_harian'],
        $data['ID_pos'],
        $data['keterangan_kas'],
        $data['ID_bruder'],
        $data['nominal'],
        $_SESSION['ID_bruder']
    ]);

    // hapus dari tabel utama
    $stmt = $pdo->prepare("DELETE FROM `3_kas_harian` WHERE ID_kas_harian = ?");
    $stmt->execute([$id]);

    echo "success";

} catch (PDOException $e) {
    echo "error: " . $e->getMessage();
}
