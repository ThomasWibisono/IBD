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

    // 1. Ambil data dari 4_bank
    $stmt = $pdo->prepare("SELECT * FROM `4_bank` WHERE ID_tabel_bank = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // 2. Masukkan ke 4_bank_deleted
        $sqlInsert = "INSERT INTO `4_bank_deleted` 
        (ID_tabel_bank, nama_bank, no_rek_bank, atas_nama_bank, tgl_transaksi, ID_pos, keterangan_bank, nominal_penerimaan, nominal_pengeluaran)
        VALUES
        (:ID_tabel_bank, :nama_bank, :no_rek_bank, :atas_nama_bank, :tgl_transaksi, :ID_pos, :keterangan_bank, :nominal_penerimaan, :nominal_pengeluaran)";

        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([
            ':ID_tabel_bank' => $data['ID_tabel_bank'],
            ':nama_bank' => $data['nama_bank'],
            ':no_rek_bank' => $data['no_rek_bank'],
            ':atas_nama_bank' => $data['atas_nama_bank'],
            ':tgl_transaksi' => $data['tgl_transaksi'],
            ':ID_pos' => $data['ID_pos'],
            ':keterangan_bank' => $data['keterangan_bank'],
            ':nominal_penerimaan' => $data['nominal_penerimaan'],
            ':nominal_pengeluaran' => $data['nominal_pengeluaran']
        ]);

        // 3. Hapus dari 4_bank
        $stmtDelete = $pdo->prepare("DELETE FROM `4_bank` WHERE ID_tabel_bank = ?");
        $stmtDelete->execute([$id]);
    }

    header("Location: anggaran_eco_bank.php");
    exit;

} catch (PDOException $e) {
    die("Gagal memindahkan dan menghapus: " . $e->getMessage());
}
