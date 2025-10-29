<?php
session_start();

if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_pp = $_POST['id_pp'] ?? null;

    if ($id_pp) {
        // Set variabel pengguna ke MySQL agar terbaca trigger
        $nama_pengguna = $_SESSION['nama_bruder'] ?? 'Tidak diketahui';
        $pdo->exec("SET @pengguna = " . $pdo->quote($nama_pengguna));

        // Hapus data dari tabel utama (trigger akan memindahkan ke riwayat)
        $stmt = $pdo->prepare("DELETE FROM `5_bruder` WHERE ID_pp = ?");
        $stmt->execute([$id_pp]);

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
