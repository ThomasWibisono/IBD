<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    header("Location: login.php");
    exit;
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nama = $_SESSION['nama_bruder'];
    $stmt = $pdo->prepare("
        SELECT db.foto
        FROM login_bruder lb
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $foto = !empty($user['foto']) ? $user['foto'] : 'default.png';
} catch (PDOException $e) {
    die("Koneksi atau query gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>BRUDER</title>
<style>
    html, body {
        height: 100%;
    }
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f4f4;
    }
    header {
        position: relative;     
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
    .sidebar {
        width: 220px;
        background: #d32f2f;
        color: white;
        min-height: 100vh;
        padding: 40px 0;
        flex-shrink: 0;
        margin-top: 10px;
        border-top-right-radius: 50px;
    }
    .sidebar a {
        display: block;
        padding: 12px 20px;
        color: white;
        text-decoration: none;
        font-weight: 500;
    }
    .sidebar a:hover {
        background: #b71c1c;
    }
    .sidebar a.active {
        background: yellow;
        color: black;
        font-weight: bold;
    }
    .main {
        flex: 1;
    }
    main {
        margin-top: 10px;
        padding: 20px;
        text-align: center;
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
    h1 {
        text-align: center;
        margin: 20px 0;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }
    .container {
        display: flex;         
        min-height: 100vh;     
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
    }

    table th,
    table td {
        border: 1px solid #eee;
        padding: 8px;
        text-align: center;
    }

    /* Button */
    .btn {
        background: #1e90ff;
        color: white;
        padding: 8px 18px;
        border-radius: 20px;
        border: none;
        cursor: pointer;
    }

    /* Image preview */
    .preview-img {
        max-width: 120px;
        max-height: 70px;
        border: 1px solid #ccc;
        padding: 4px;
        background: #fafafa;
    }

    /* Form layout */
    .row {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
    }

    .flex-col {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
    }
    .row label {
        min-width: 140px;
        font-weight: bold;
    }

    .row input,
    .row select {
        padding: 6px;
        border: 1px solid #ddd;
        border-radius: 6px;
    }
    .half {
        flex: 1;
    }

</style>
</head>
<body>
    <header>
        <img src="foto/logo.png" alt="Logo" class="logo">
        <nav>
            <a href="dashboard_eco.php">Home</a>
            <a href="anggota.php">Daftar Anggota</a>
            <a href="anggaran_eco.php" class="active">Anggaran</a>

        </nav>
        <div class="profile-wrapper" onclick="toggleDropdown()">
            <img src="foto/<?= htmlspecialchars($foto) ?>" alt="Profile" class="profile-pic">
            <div class="dropdown" id="dropdownMenu">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="sidebar">
            <a href="anggaran_eco.php">Data</a>
            <a href="anggaran_eco_perkiraan.php">Perkiraan</a>
            <a href="anggaran_eco_kas.php">Kas Harian</a>
            <a href="anggaran_eco_bank.php">Bank</a>
            <a href="anggaran_eco_bruder.php" class="active">Bruder</a>
            <a href="anggaran_eco_lu.php">LU Komunitas</a>
            <a href="anggaran_eco_evaluasi.php">Evaluasi</a>
            <a href="anggaran_eco_buku.php">Buku Besar</a>
            <a href="anggaran_eco_opname.php">Kas Opname</a>
        </div>
        <div class="main">
            <main>
                <h1>KOMUNITAS FIC CANDI<br>PERUBAHAN JUMLAH BRUDER<br>BULAN JANUARI 2025</h1>
                <div class="card">
                        
                </div>
            </main>
        </div>
    </div>
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