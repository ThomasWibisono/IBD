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
<title>EVALUASI</title>
<style>
    html, body {
        height: 100%;
    }
    body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: #f4f4f4;
    display: flex;
    }
    .sidebar {
        width: 220px;
        background: #d32f2f;
        color: white;
        height: 100vh; /* ganti min-height jadi height */
        padding: 15px 0;
        position: fixed;   /* biar nempel kiri */
        top: 0;
        left: 0;
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
        margin-left: 220px; /* supaya konten tidak ketutup sidebar */
    }
    /* Header */
    header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: white;
        padding: 10px 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .logo {
        height: 60px;
    }

    /* Navigation */
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
    }

    nav a.active {
        background: orange;
        color: black;
    }

    /* Profile */
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

    /* Main content */
    main {
        padding: 20px;
        max-width: 1100px;
        margin: auto;
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

    /* Table */
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
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="anggaran_eco.php">Data</a>
        <a href="anggaran_eco_perkiraan.php">Perkiraan</a>
        <a href="anggaran_eco_kas.php">Kas Harian</a>
        <a href="anggaran_eco_bank.php">Bank</a>
        <a href="anggaran_eco_bruder.php">Bruder</a>
        <a href="anggaran_eco_lu.php">LU Komunitas</a>
        <a href="anggaran_eco_evaluasi.php">Evaluasi</a>
        <a href="anggaran_eco_buku.php" class="active">Buku Besar</a>
        <a href="anggaran_eco_opname.php">Kas Opname</a>
    </div>
    <div class="main">
        <header>
            <img src="foto/logo.png" alt="Logo" class="logo">
            <nav>
                <a href="dashboard_eco.php">Home</a>
                <a href="anggota.php">Daftar Anggota</a>
                <a href="anggaran_eco.php">Anggaran</a>

            </nav>
            <div class="profile-wrapper" onclick="toggleDropdown()">
                <img src="foto/<?= htmlspecialchars($foto) ?>" alt="Profile" class="profile-pic">
                <div class="dropdown" id="dropdownMenu">
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>
        <main>
            <h1>KOMUNITAS FIC CANDI<br>LAPORAN BUKU BESAR KAS HARIAN</h1>
            <div class="card">
                    
            </div>
            <h1>KOMUNITAS FIC CANDI<br>LAPORAN BUKU BESAR BANK HARIAN</h1>
            <div class="card">
                    
            </div>
        </main>
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