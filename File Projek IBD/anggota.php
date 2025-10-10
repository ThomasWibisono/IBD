<?php
session_start();
if (!isset($_SESSION['ID_bruder'])) {
    header("Location: login.php");
    exit;
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("
        SELECT db.foto
        FROM login_bruder lb
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $foto = !empty($user['foto']) ? $user['foto'] : 'default.png';
    $stmt2 = $pdo->query("SELECT ID_bruder, nama_bruder, foto FROM data_bruder");
    $bruders = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $status = isset($_SESSION['status']) ? $_SESSION['status'] : '';
} catch (PDOException $e) {
    die("Koneksi atau query gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Anggota</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            color: #333;
        }
        header {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 10px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo {
            height: 50px;
        }
        nav {
            display: flex;
            gap: 30px;
        }
        nav a {
            color: #1e90ff;
            font-weight: 600;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: background 0.3s;
        }
        nav a.active {
            background: #1e90ff;
            color: white;
        }
        nav a:hover {
            background: #d0eaff;
        }
        .profile-wrapper {
            position: relative;
            cursor: pointer;
        }
        .profile-pic {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #1e90ff;
        }
        .dropdown {
            position: absolute;
            top: 60px;
            right: 0;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
            display: none;
            flex-direction: column;
            min-width: 180px;
        }
        .dropdown a {
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
        }
        .dropdown a:hover {
            background: #f0f0f0;
        }
        main {
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
        }
        .search-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        #searchInput {
            width: 350px;
            padding: 10px 20px;
            border-radius: 30px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        .btn-simpan {
            background: linear-gradient(to right, #1e90ff, #00bcd4);
            border: none;
            border-radius: 30px;
            padding: 10px 20px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-simpan:hover {
            background: linear-gradient(to right, #00bcd4, #1e90ff);
        }
        .btn-simpan a {
            color: white;
            text-decoration: none;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .card img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid #1e90ff;
        }
        .card p {
            font-weight: 600;
            font-size: 16px;
            color: #1e90ff;
        }
    </style>
</head>
<body>
    <header>
        <img src="foto/logo.png" alt="Logo" class="logo">
        <nav>
            <a href="dashboard_eco.php">Home</a>
            <a href="anggota.php" class="active">Daftar Anggota</a>
            <?php if ($status === 'econom'): ?>
                <a href="anggaran_eco.php">Anggaran</a>
            <?php else: ?>
                <a href="#" onclick="alert('Anggaran hanya bisa diakses oleh Ekonom!'); return false;">Anggaran</a>
            <?php endif; ?>
        </nav>
        <div class="profile-wrapper" onclick="toggleDropdown()">
            <img src="foto/<?= htmlspecialchars($foto) ?>" alt="Foto Bruder" class="profile-pic">
            <div class="dropdown" id="dropdownMenu">
                <a href="logout.php">Logout</a>
                <a href="editprofile.php">Edit Profile</a>
            </div>
        </div>
    </header>
    <main>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Cari nama Bruder...">
            <button class="btn-simpan"><a href="tambah.php">Tambah</a></button>
        </div>
        <div class="grid" id="anggotaGrid">
            <?php foreach ($bruders as $b): ?>
                <a href="detail_bruder.php?id=<?= $b['ID_bruder'] ?>" class="card">
                    <img src="foto/<?= htmlspecialchars($b['foto']) ?>" alt="Foto Bruder">
                    <p>Br. <?= htmlspecialchars($b['nama_bruder']) ?>, FIC.</p>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
    <script>
        const searchInput = document.getElementById('searchInput');
        const anggotaGrid = document.getElementById('anggotaGrid');
        searchInput.addEventListener('keyup', function() {
            let filter = searchInput.value.toLowerCase();
            let cards = anggotaGrid.getElementsByTagName('a');
            for (let i = 0; i < cards.length; i++) {
                let name = cards[i].querySelector("p").textContent.toLowerCase();
                cards[i].style.display = name.includes(filter) ? "block" : "none";
                cards[i].style.transition = "opacity 0.3s ease";
                cards[i].style.opacity = name.includes(filter) ? "1" : "0";
            }
        });
        function toggleDropdown() {
            let menu = document.getElementById("dropdownMenu");
            menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
        }
        window.onclick = function(event) {
            if (!event.target.closest('.profile-wrapper')) {
                document.getElementById("dropdownMenu").style.display = "none";
            }
        }
    </script>
</body>
</html>
