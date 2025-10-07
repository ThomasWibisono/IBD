<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    header("Location: login.php");
    exit;
}

try {
    // 🔹 Koneksi ke database pakai PDO
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 🔹 Ambil foto profil
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
    die("Koneksi gagal: " . $e->getMessage());
}

// 🔹 Fungsi tampil tabel
function tampilTabel($pdo, $kategori) {
    echo "<h3>$kategori</h3>";
    echo "<table>";
    echo "<tr>
            <th>POS</th>
            <th>KODE PERKIRAAN</th>
            <th>NAMA PERKIRAAN</th>
          </tr>";

    $stmt = $pdo->prepare("SELECT * FROM `2_perkiraan` WHERE posisi = ? ORDER BY ID_pos ASC");
    $stmt->execute([$kategori]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($data) {
        foreach ($data as $row) {
            echo "<tr>
                    <td>{$row['ID_pos']}</td>
                    <td>{$row['kode']}</td>
                    <td>{$row['akun']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>Tidak ada data untuk kategori ini</td></tr>";
    }

    echo "</table>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>PERKIRAAN</title>
<style>
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
    main {
        margin-top: 10px;
        padding: 20px;
        text-align: center;
    }
    .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }
    h1, h2 {
        text-align: center;
        margin: 20px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
        font-size: 14px;
    }
    th {
        background: #f9f9f9;
    }
    .container {
        display: flex;         
        min-height: 100vh;     
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
            <img src="foto/<?= htmlspecialchars($foto) ?>" alt="Foto Bruder" class="profile-pic">
            <div class="dropdown" id="dropdownMenu">
                <a href="logout.php">Logout</a>
                <a href="editprofile.php">Edit Profile</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="sidebar">
            <a href="anggaran_eco.php">Data</a>
            <a href="anggaran_eco_perkiraan.php" class="active">Perkiraan</a>
            <a href="anggaran_eco_kas.php">Kas Harian</a>
            <a href="anggaran_eco_bank.php">Bank</a>
            <a href="anggaran_eco_bruder.php">Bruder</a>
            <a href="anggaran_eco_lu.php">LU Komunitas</a>
            <a href="anggaran_eco_evaluasi.php">Evaluasi</a>
            <a href="anggaran_eco_buku.php">Buku Besar</a>
            <a href="anggaran_eco_opname.php">Kas Opname</a>
        </div>
        <div class="main">
            <main>
                <h1>KODE PERKIRAAN PEMBUKUAN<br>KOMUNITAS BRUDER FIC</h1>
                <div class="card">
                    <?php
                        tampilTabel($pdo, "PENERIMAAN");
                        tampilTabel($pdo, "PENGELUARAN");
                    ?>
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
