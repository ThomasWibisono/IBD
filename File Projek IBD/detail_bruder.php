<?php
session_start();
if (!isset($_SESSION['ID_bruder'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: anggota.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM data_bruder WHERE ID_bruder = ?");
    $stmt->execute([$_GET['id']]);
    $bruder = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bruder) {
        die("Data Bruder tidak ditemukan.");
    }

} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Bruder</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: relative;
        }
        .logo {
            height: 60px;
        }
        nav {
            display: flex;
            gap: 15px;
        }
        nav a {
            padding: 10px 20px;
            border-radius: 25px;
            background: #1e90ff;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        nav a.active {
            background: #0b75d1;
        }
        nav a:hover {
            background: #0096e0;
        }
        .profile-wrapper {
            position: relative;
            cursor: pointer;
        }
        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 60px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border-radius: 8px;
            overflow: hidden;
            min-width: 260px;
            display: none;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
        }
        .dropdown a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
        }
        .dropdown a:hover {
            background: #f4f4f4;
        }
        .detail-card {
            max-width: 600px;
            margin: 80px auto 20px auto; /* atas 80px, kanan auto, bawah 20px, kiri auto */
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .detail-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .detail-card h2 {
            margin: 0 0 15px;
        }
        .detail-card p {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <header>
        <img src="foto/logo.png" alt="Logo" class="logo">
        <nav>
            <a href="dashboard_eco.php" class="active">Home</a>
            <a href="anggota.php">Daftar Anggota</a>
            <a href="anggaran_eco.php">Anggaran</a>
        </nav>
        <div class="profile-wrapper" onclick="toggleDropdown()">
            <img src="foto/thom.jpg" alt="Profile" class="profile-pic">
            <div class="dropdown" id="dropdownMenu">
                <a href="logout.php">Logout</a>
                <a href="editprofile.php">Edit Profile</a>
            </div>
        </div>
    </header>
    <div class="detail-card">
        <img src="foto/<?= htmlspecialchars($bruder['foto'] ?? 'default.png') ?>" alt="Foto">
        <h2><?= htmlspecialchars($bruder['nama_bruder']) ?></h2>
        <p><strong>TTL:</strong> <?= htmlspecialchars($bruder['ttl_bruder']) ?></p>
        <p><strong>Alamat:</strong> <?= htmlspecialchars($bruder['alamat_bruder']) ?></p>
        <p><strong>Tahun Masuk Postulan:</strong> <?= htmlspecialchars($bruder['tahun_masuk_postulan']) ?></p>
        <p><strong>Tahun Prasetia Pertama:</strong> <?= htmlspecialchars($bruder['tahun_prasetia_pertama']) ?></p>
        <p><strong>Tahun Kaul Kekal:</strong> <?= htmlspecialchars($bruder['tahun_kaul_kekal']) ?></p>
        <p><strong>Riwayat Tugas:</strong> <?= htmlspecialchars($bruder['riwayat_tugas']) ?></p>
    </div>
</body>
</html>
