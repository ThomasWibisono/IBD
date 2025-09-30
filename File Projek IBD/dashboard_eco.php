<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    header("Location: login.php");
    exit;
}
$nama = $_SESSION['nama_bruder'];
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT lb.nama_bruder, db.unit_kerja, db.alamat, db.no_telp, db.email
        FROM login_bruder lb
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Econom</title>
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
        main {
            padding: 20px;
            text-align: center;
        }
        .welcome {
            font-size: 22px;
            font-weight: bold;
            margin: 20px 0;
        }
        .banner img {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }
        .cards {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            gap: 20px;
        }
        .card {
            flex: 1;
            border-radius: 10px;
            padding: 30px;
            font-size: 18px;
            text-align: center;
        }
        .red {
            background: #e74c3c;
            color: #fff;
        }
        .yellow {
            background: #f1c40f;
            color: #000;
            text-align: left;
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
            </div>
        </div>
    </header>

    <main>
        <p class="welcome">Selamat Datang Br. <?= htmlspecialchars($nama) ?>!</p>
        <div class="banner">
            <img src="foto/fic2.jpeg" alt="Banner">
        </div>
        <div class="cards">
            <div class="card red">
                Penjelasan fungsi website
            </div>
            <div class="card yellow">
                <p>Unit Kerja : <?= htmlspecialchars($user['unit_kerja']) ?></p>
                <p>Alamat : <?= htmlspecialchars($user['alamat']) ?></p>
                <p>No telp : <?= htmlspecialchars($user['no_telp']) ?></p>
                <p>Email : <?= htmlspecialchars($user['email']) ?></p>
            </div>
        </div>
    </main>

    <script>
        function toggleDropdown() {
            let menu = document.getElementById("dropdownMenu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }
        window.onclick = function(event) {
            if (!event.target.closest('.profile-wrapper')) {
                document.getElementById("dropdownMenu").style.display = "none";
            }
        }
    </script>
</body>
</html>
