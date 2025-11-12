<?php
session_start();
if (!isset($_SESSION['ID_bruder'])) {
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
<title>Dashboard Econom</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(to bottom right, #9be2ff, #c4f1ff);
        color: #333;
    }
    header {
        background: linear-gradient(145deg, #b3e5ff, #d9f6ff);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        padding: 12px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 1000;
        border-radius: 25px;
        margin: 20px auto;
        width: 90%;
    }

    .logo {
        height: 60px;
    }

    nav {
        display: flex;
        gap: 25px;
    }

    nav a {
        color: #0077ff;
        font-weight: 600;
        text-decoration: none;
        padding: 10px 22px;
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    nav a.active {
        background: white;
        color: #0077ff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    nav a:hover {
        background: rgba(255,255,255,0.8);
        color: #004fa3;
    }

    /* ===== Profil & Dropdown ===== */
    .profile-wrapper {
        position: relative;
        cursor: pointer;
    }

    .profile-pic {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #0077ff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .dropdown {
        position: absolute;
        top: 65px;
        right: 0;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 10px;
        display: none;
        flex-direction: column;
        min-width: 180px;
    }

    .dropdown a {
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        transition: background 0.3s;
    }

    .dropdown a:hover {
        background: #e0f3ff;
    }

    /* ===== Main Section ===== */
    main {
        padding: 40px 20px;
        max-width: 1200px;
        margin: auto;
        text-align: center;
    }

    .welcome {
        font-size: 26px;
        font-weight: 700;
        color: #004fa3;
        margin-bottom: 25px;
    }

    .banner {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }

    .banner img {
        width: 100%;
        height: 280px;
        object-fit: cover;
    }

    /* ===== Grid Cards ===== */
    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
        gap: 25px;
        width: 100%;
    }

    .card {
        background: white;
        border-radius: 20px;
        padding: 25px 20px;
        box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        text-align: left;
        text-decoration: none;
    }

    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .card h2, .card h3 {
        color: #0077ff;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .card p {
        font-weight: 500;
        font-size: 15px;
        color: #333;
        line-height: 1.6;
    }

    /* ===== Footer ===== */
    footer {
        margin-top: 50px;
        text-align: center;
        color: #004fa3;
        opacity: 0.9;
        font-size: 14px;
    }

    /* ===== Responsive ===== */
    @media(max-width: 768px) {
        nav {
            gap: 10px;
        }
        .cards {
            grid-template-columns: 1fr;
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
            <?php if ($_SESSION['status'] === 'econom'): ?>
                <a href="anggaran_eco.php">Anggaran</a>
            <?php else: ?>
                <a href="#" onclick="alert('Anggaran hanya bisa diakses oleh Ekonom!'); return false;">Anggaran</a>
            <?php endif; ?>
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