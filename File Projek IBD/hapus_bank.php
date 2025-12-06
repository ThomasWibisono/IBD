<?php
session_start();

// Cek sesi login
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    echo "unauthorized";
    exit;
}

// Pastikan ID dikirim lewat POST
if (!isset($_POST['id'])) {
    die("ID tidak ditemukan");
}

$id = $_POST['id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Ambil data lama dari tabel 4_bank untuk arsip
    $stmt = $pdo->prepare("SELECT * FROM `4_bank` WHERE ID_tabel_bank = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // 2. Masukkan ke tabel arsip (4_bank_deleted)
        // PERBAIKAN: Gunakan '??' untuk memberikan nilai default '-' jika kolom tidak ada
        $sqlInsert = "INSERT INTO `4_bank_deleted` 
        (ID_tabel_bank, nama_bank, no_rek_bank, atas_nama_bank, tgl_transaksi, ID_pos, keterangan_bank, nominal_penerimaan, nominal_pengeluaran)
        VALUES
        (:ID_tabel_bank, :nama_bank, :no_rek_bank, :atas_nama_bank, :tgl_transaksi, :ID_pos, :keterangan_bank, :nominal_penerimaan, :nominal_pengeluaran)";

        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([
            ':ID_tabel_bank'      => $data['ID_tabel_bank'],
            ':nama_bank'          => $data['nama_bank'] ?? '-',       // <-- Ini kuncinya
            ':no_rek_bank'        => $data['no_rek_bank'] ?? '-',     // <-- Ini kuncinya
            ':atas_nama_bank'     => $data['atas_nama_bank'] ?? '-',  // <-- Ini kuncinya
            ':tgl_transaksi'      => $data['tgl_transaksi'],
            ':ID_pos'             => $data['ID_pos'],
            ':keterangan_bank'    => $data['keterangan_bank'],
            ':nominal_penerimaan' => $data['nominal_penerimaan'],
            ':nominal_pengeluaran'=> $data['nominal_pengeluaran']
        ]);

        // 3. Hapus data dari tabel utama (4_bank)
        $stmtDelete = $pdo->prepare("DELETE FROM `4_bank` WHERE ID_tabel_bank = ?");
        $stmtDelete->execute([$id]);
    }

    // Kirim respon sukses ke JavaScript
    echo "success";

} catch (PDOException $e) {
    // Tampilkan pesan error jika gagal
    echo "Gagal menghapus data: " . $e->getMessage();
}
?>