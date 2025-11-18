<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_pp = $_POST['id_pp'] ?? null;
    if (!$id_pp) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
        exit;
    }

    // 1. Ambil data dari 5_bruder
    $stmtSelect = $pdo->prepare("SELECT * FROM `5_bruder` WHERE ID_pp = ?");
    $stmtSelect->execute([$id_pp]);
    $data = $stmtSelect->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        exit;
    }

    // 2. Masukkan ke 5_bruder_deleted (optional: deleted_at)
    $sqlInsert = "INSERT INTO `5_bruder_deleted` 
        (ID_bruder, tgl_datang_komunitas, tgl_pulang_komunitas, tgl_pergi_luarkota, tgl_pulang_luarkota, jumlah_hari, keterangan_pp)
        VALUES
        (:ID_bruder, :tgl_datang_komunitas, :tgl_pulang_komunitas, :tgl_pergi_luarkota, :tgl_pulang_luarkota, :jumlah_hari, :keterangan_pp)";

    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([
        ':ID_bruder' => $data['ID_bruder'],
        ':tgl_datang_komunitas' => $data['tgl_datang_komunitas'],
        ':tgl_pulang_komunitas' => $data['tgl_pulang_komunitas'],
        ':tgl_pergi_luarkota' => $data['tgl_pergi_luarkota'],
        ':tgl_pulang_luarkota' => $data['tgl_pulang_luarkota'],
        ':jumlah_hari' => $data['jumlah_hari'],
        ':keterangan_pp' => $data['keterangan_pp']
    ]);

    // 3. Hapus dari 5_bruder
    $stmtDelete = $pdo->prepare("DELETE FROM `5_bruder` WHERE ID_pp = ?");
    $stmtDelete->execute([$id_pp]);

    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}