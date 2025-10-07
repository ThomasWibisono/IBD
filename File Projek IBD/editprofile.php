<?php
session_start();
if (!isset($_SESSION['ID_bruder'])) {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

$id = $_SESSION['ID_bruder'];

// Ambil data profil
$stmt = $pdo->prepare("SELECT * FROM data_bruder WHERE ID_bruder = ?");
$stmt->execute([$id]);
$bruder = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bruder) {
    die("Data tidak ditemukan!");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $ttl = $_POST['ttl'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $postulan = $_POST['postulan'] ?? '';
    $prasetia = $_POST['prasetia'] ?? '';
    $kaul = $_POST['kaul'] ?? '';
    $riwayat = $_POST['riwayat'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simpan foto jika ada
    if (!empty($_FILES['foto']['name'])) {
        $fotoName = time() . "_" . basename($_FILES['foto']['name']);
        $target = "foto/" . $fotoName;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target);
    } else {
        $fotoName = $bruder['foto'];
    }

    // Update data ke database
    $stmt = $pdo->prepare("
        UPDATE data_bruder 
        SET nama_bruder = ?, ttl = ?, alamat = ?, tahun_postulan = ?, tahun_prasetia = ?, 
            tahun_kaul = ?, riwayat_tugas = ?, foto = ?
        WHERE ID_bruder = ?
    ");
    $stmt->execute([$nama, $ttl, $alamat, $postulan, $prasetia, $kaul, $riwayat, $fotoName, $id]);

    // Update password jika diisi
    if (!empty($password)) {
        $hashed = hash('sha512', $password);
        $stmt2 = $pdo->prepare("UPDATE login_bruder SET password = ? WHERE ID_bruder = ?");
        $stmt2->execute([$hashed, $id]);
    }

    $_SESSION['nama_bruder'] = $nama;
    header("Location: dashboard_eco.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Profil Bruder</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f5f5f5;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 480px;
        margin: 50px auto;
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #333;
    }
    label {
        font-weight: bold;
        display: block;
        margin-top: 10px;
    }
    input[type="text"], input[type="password"], input[type="file"], input[type="date"], textarea {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #ccc;
        margin-top: 5px;
        box-sizing: border-box;
    }
    textarea {
        resize: none;
        height: 60px;
    }
    .profile-pic {
        display: block;
        margin: 10px auto;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
    }
    button {
        margin-top: 20px;
        width: 100%;
        padding: 10px;
        background: #1e90ff;
        color: white;
        border: none;
        border-radius: 25px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    button:hover {
        background: #0b75d1;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Edit Profil Bruder</h2>
    <form method="POST" enctype="multipart/form-data">
        <img src="foto/<?= htmlspecialchars($bruder['foto'] ?: 'default.png') ?>" alt="Foto Profil" class="profile-pic">

        <label>Nama Bruder:</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($bruder['nama_bruder']) ?>" required>

        <label>Tanggal Lahir (TTL):</label>
        <input type="date" name="ttl" value="<?= htmlspecialchars($bruder['ttl']) ?>">

        <label>Alamat:</label>
        <textarea name="alamat"><?= htmlspecialchars($bruder['alamat']) ?></textarea>

        <label>Tahun Masuk Postulan:</label>
        <input type="text" name="postulan" value="<?= htmlspecialchars($bruder['tahun_postulan']) ?>">

        <label>Tahun Prasetia Pertama:</label>
        <input type="text" name="prasetia" value="<?= htmlspecialchars($bruder['tahun_prasetia']) ?>">

        <label>Tahun Kaul Kekal:</label>
        <input type="text" name="kaul" value="<?= htmlspecialchars($bruder['tahun_kaul']) ?>">

        <label>Riwayat Tugas:</label>
        <textarea name="riwayat"><?= htmlspecialchars($bruder['riwayat_tugas']) ?></textarea>

        <label>Foto Profil:</label>
        <input type="file" name="foto" accept="image/*">

        <label>Password Baru (kosongkan jika tidak diubah):</label>
        <input type="password" name="password" placeholder="********">

        <button type="submit">Simpan Perubahan</button>
    </form>
</div>
</body>
</html>
