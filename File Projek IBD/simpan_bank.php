<?php
session_start();
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama_bank = $_POST['nama_bank'];
        $no_rek = $_POST['no_rek_bank'];
        $atas_nama = $_POST['atas_nama_bank'];
        $tgl = $_POST['tgl_transaksi'];
        $nominal = $_POST['nominal_penerimaan'];

        // ID_pos misal default "POS01" — bisa disesuaikan
        $stmt = $pdo->prepare("
            INSERT INTO 4_bank 
            (ID_tabel_bank, nama_bank, no_rek_bank, atas_nama_bank, tgl_transaksi, ID_pos, nominal_penerimaan)
            VALUES (:id, :nama_bank, :no_rek, :atas_nama, :tgl, :id_pos, :nominal)
        ");

        $id_bank = 'BANK' . uniqid(); // bikin ID unik
        $id_pos = 'POS01';

        $stmt->execute([
            ':id' => $id_bank,
            ':nama_bank' => $nama_bank,
            ':no_rek' => $no_rek,
            ':atas_nama' => $atas_nama,
            ':tgl' => $tgl,
            ':id_pos' => $id_pos,
            ':nominal' => $nominal
        ]);

        echo "<script>alert('Data bank berhasil disimpan!'); window.location.href='halamanmu.php';</script>";
    }

} catch (PDOException $e) {
    echo "Koneksi / query error: " . $e->getMessage();
}
?>
