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

} catch (PDOException $e) {
    die("Koneksi atau query gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Anggota</title>
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
        }
        .logo { height: 60px; }
        nav { display: flex; gap: 15px; }
        nav a {
            padding: 10px 20px;
            border-radius: 25px;
            background: #1e90ff;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        nav a.active { background: #0b75d1; }
        .search-box {
            margin: 20px auto;
            width: 60%;
            display: flex;
            align-items: center;
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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
        .search-box input {
            flex: 1;
            padding: 10px;
            border-radius: 25px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
        }
        .card:hover {
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .card p {
            margin: 0;
            font-weight: bold;
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
            <img src="foto/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Bruder" class="profile-pic">
            <div class="dropdown" id="dropdownMenu">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari nama Bruder...">
    </div>

    <div class="grid" id="anggotaGrid">
        <?php foreach ($bruders as $b): ?>
            <a href="detail_bruder.php?id=<?= $b['ID_bruder'] ?>" class="card">
                <img src="foto/<?= htmlspecialchars($b['foto']) ?>" alt="Foto Bruder">
                <p><?= htmlspecialchars($b['nama_bruder']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const anggotaGrid = document.getElementById('anggotaGrid');
        searchInput.addEventListener('keyup', function() {
            let filter = searchInput.value.toLowerCase();
            let cards = anggotaGrid.getElementsByTagName('a');
            for (let i = 0; i < cards.length; i++) {
                let name = cards[i].querySelector("p").textContent.toLowerCase();
                cards[i].style.display = name.includes(filter) ? "" : "none";
            }
        });
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
