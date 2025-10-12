<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'bruder') {
    header("Location: login.php");
    exit;
}
$nama = $_SESSION['nama_bruder'];
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT lb.nama_bruder, db.unit_kerja, db.alamat, db.no_telp, db.email, db.foto
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
<title>Dashboard Bruder</title>

<!-- Font Quicksand -->
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        font-family: 'Quicksand', sans-serif;
        background: linear-gradient(135deg, #89f7fe, #66a6ff);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    header {
        position: fixed;
        top: 20px;
        width: 90%;
        max-width: 1200px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        padding: 10px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 10;
    }

    .logo {
        height: 60px;
    }

    nav {
        display: flex;
        gap: 25px;
    }

    nav a {
        color: white;
        text-decoration: none;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 15px;
        transition: 0.3s;
        background: rgba(255,255,255,0.15);
    }

    nav a:hover, nav a.active {
        background: white;
        color: #1e90ff;
    }

    .profile-wrapper {
        position: relative;
        cursor: pointer;
    }

    .profile-pic {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        transition: 0.3s;
    }

    .profile-pic:hover {
        transform: scale(1.1);
    }

    .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 70px;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        border-radius: 12px;
        overflow: hidden;
        min-width: 180px;
    }

    .dropdown a {
        display: block;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        font-weight: 500;
    }

    .dropdown a:hover {
        background: #f0f0f0;
    }

    main {
        margin-top: 130px;
        width: 90%;
        max-width: 1200px;
        color: white;
    }

    .welcome {
        font-size: 28px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 20px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }

    .banner {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }

    .banner img {
        width: 100%;
        height: 280px;
        object-fit: cover;
    }

    /* --- Bagian Dua Kolom --- */
    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
        gap: 30px;
        margin-top: 40px;
        width: 100%;
    }

    .card.glass {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(12px);
        border-radius: 25px;
        padding: 30px;
        color: #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        font-family: 'Quicksand', sans-serif;
        transition: all 0.35s ease;
        position: relative;
    }

    .card.glass:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.3);
    }

    .card.glass.left {
        background: linear-gradient(135deg, rgba(120,160,255,0.35), rgba(80,120,255,0.3));
        text-align: justify;
        font-size: 17px;
    }

    .card.glass.left h2 {
        font-weight: 700;
        font-size: 22px;
        color: #ffffff;
        margin-bottom: 10px;
        text-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .card.glass.right {
        background: linear-gradient(135deg, rgba(160,255,230,0.35), rgba(90,220,200,0.3));
        color: #002b3d;
    }

    .card.glass.right h3 {
        margin-bottom: 15px;
        color: #004e64;
        font-size: 20px;
        font-weight: 700;
    }

    .card.glass.right p {
        margin: 6px 0;
        font-weight: 500;
    }

    footer {
        margin-top: 40px;
        text-align: center;
        color: white;
        opacity: 0.8;
        font-size: 14px;
    }

    @media(max-width: 768px) {
        nav {
            gap: 10px;
        }
        .cards {
            flex-direction: column;
            align-items: center;
        }
        .card {
            width: 90%;
        }
    }
</style>
</head>
<body>

<header>
    <img src="foto/logo.png" alt="Logo" class="logo">
    <nav>
        <a href="dashboard_eco.php" class="active">Home</a>
        <a href="anggota.php">Anggota</a>
        <a href="anggaran_eco.php">Anggaran</a>
    </nav>
    <div class="profile-wrapper" onclick="toggleDropdown()">
        <img src="foto/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Bruder" class="profile-pic">
        <div class="dropdown" id="dropdownMenu">
            <a href="editprofile.php">Edit Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>

<main>
    <p class="welcome">Selamat Datang, Br. <?= htmlspecialchars($nama) ?> 👋</p>
    <div class="banner">
        <img src="foto/fic2.jpeg" alt="Banner">
    </div>

    <div class="cards">
        <!-- Kolom Kiri -->
        <div class="card glass left">
            <h2><b>HALO FIC</b></h2>
            <p>
                adalah platform khusus bagi para Bruder FIC untuk mengakses data pribadi, unit kerja, dan informasi internal komunitas
                secara mudah, aman, dan efisien.
            </p>
        </div>

        <!-- Kolom Kanan -->
        <div class="card glass right">
            <h3>📋 Informasi Bruder</h3>
            <p><b>Unit Kerja:</b> <?= htmlspecialchars($user['unit_kerja']) ?></p>
            <p><b>Alamat:</b> <?= htmlspecialchars($user['alamat']) ?></p>
            <p><b>No. Telepon:</b> <?= htmlspecialchars($user['no_telp']) ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
        </div>
    </div>
</main>

<footer>
    © <?= date('Y') ?> Komunitas Bruder FIC — All Rights Reserved.
</footer>

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
