<?php
include 'koneksi.php'; // pastikan ini koneksi PDO kamu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        try {
            $stmt = $pdo->prepare("DELETE FROM kas_harian WHERE id_kas_harian = ?");
            $stmt->execute([$id]);
            echo "success";
        } catch (PDOException $e) {
            echo "error: " . $e->getMessage();
        }
    } else {
        echo "invalid id";
    }
}
