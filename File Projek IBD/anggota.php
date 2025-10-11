<?php
session_start();
if (!isset($_SESSION['ID_bruder'])) {
    header("Location: login.php");
    exit;
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil foto profil pengguna aktif
    $stmt = $pdo->prepare("
        SELECT db.foto
        FROM login_bruder lb
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $foto = !empty($user['foto']) ? $user['foto'] : 'default.png';

    // Ambil daftar semua bruder
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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom right, #9be2ff, #c4f1ff);
            color: #333;
        }
        /* ===== Header / Navbar ===== */
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
            text-decoration: none; /* 🔹 Menghapus underline */
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
            text-decoration: none; /* 🔹 Menghapus underline */
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
            background: white;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .btn-simpan {
            background: linear-gradient(to right, #007bff, #00c3ff);
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            transition: background 0.3s;
        }
        .btn-simpan:hover {
            background: linear-gradient(to right, #00c3ff, #007bff);
        }
        .btn-simpan a {
            color: white;
            text-decoration: none; /* 🔹 Menghapus underline */
        }

        /* ===== Grid Cards ===== */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 25px 20px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none; /* 🔹 Menghapus underline */
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .card img {
            width: 95px;
            height: 95px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #00bfff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }
        .card p {
            font-weight: 600;
            font-size: 16px;
            color: #0077ff;
            text-decoration: none; /* 🔹 Tambahan agar teks di kartu juga tidak bergaris */
        }
    </style>
</head>
<body>
    <header>
        <img src="foto/logo.png" alt="Logo" class="logo">
        <nav>
            <a href="dashboard_eco.php">Home</a>
            <a href="anggota.php" class="active">Anggota</a>
            <?php if ($status === 'econom'): ?>
                <a href="anggaran_eco.php">Anggaran</a>
            <?php else: ?>
                <a href="#" onclick="alert('Anggaran hanya bisa diakses oleh Ekonom!'); return false;">Anggaran</a>
            <?php endif; ?>
        </nav>
        <div class="profile-wrapper" onclick="toggleDropdown()">
            <img src="foto/<?= htmlspecialchars($foto) ?>" alt="Foto Bruder" class="profile-pic">
            <div class="dropdown" id="dropdownMenu">
                <a href="editprofile.php">Edit Profile</a>
                <a href="logout.php">Logout</a>
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
