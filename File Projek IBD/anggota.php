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
            position: fixed;     
            top: 0;
            left: 0;
            width: 100%;          
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
        }
        .logo {
            height: 60px;
            
        }
        nav {
            
            display: flex;
            justify-content: center;
            gap: 40px;
            background: #1e90ff;
            padding: 10px 0;
            border-radius: 50px;
            width: 60%;   
            max-width: 700px; 
            margin: 0 auto;   
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 20px;
            transition: 0.3s;
        }

        nav a.active {
            background: white;
            color: black;
            font-weight: bold;
        }

        nav a:hover {
            background: rgba(255, 255, 255, 0.2);
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
            margin-top: 50px;
            padding: 20px;
            text-align: center;
        }
        .search-box input {
            width: 50%;
            flex: 1;
            padding: 10px;
            border-radius: 25px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
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
            <a href="dashboard_eco.php">Home</a>
            <a href="anggota.php"class="active">Daftar Anggota</a>
            <a href="anggaran_eco.php">Anggaran</a>
        </nav>
        <div class="profile-wrapper" onclick="toggleDropdown()">
            <img src="foto/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Bruder" class="profile-pic">
            <div class="dropdown" id="dropdownMenu">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>
    <main>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari nama Bruder...">
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
