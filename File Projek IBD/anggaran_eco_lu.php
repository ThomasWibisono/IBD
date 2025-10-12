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
<title>LU KOMUNITAS</title>
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

    .sidebar {
        width: 220px;
        background: linear-gradient(180deg, #4facfe, #00f2fe); /* biru muda lembut */
        color: white;
        min-height: 100vh;
        padding: 40px 0;
        flex-shrink: 0;
        margin-top: 10px;
        border-top-right-radius: 50px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .sidebar a {
        display: block;
        padding: 12px 20px;
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .sidebar a:hover {
        background: rgba(255,255,255,0.2);
    }

    .sidebar a.active {
        background: white;
        color: #004b8d;
        font-weight: bold;
    }

    /* Main content */
    .main {
        flex: 1;
    }

    /* Dropdown */
    .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 60px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-radius: 10px;
        overflow: hidden;
        min-width: 260px;
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

    /* Card */
    .card {
        background: rgba(255, 255, 255, 0.95);
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    /* Judul */
    h1, h2 {
        text-align: center;
        margin: 20px 0;
        color: #003366;
    }

    /* Table */
    .table-header {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; 
        margin-top: 10px;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    th, td {
        border: 1px solid #e0e0e0;
        padding: 8px;
        text-align: center;
        font-size: 14px;
        word-wrap: break-word; 
    }

    th {
        background: #f9fbff;
        color: #004b8d;
    }

    /* Input cell */
    .input-cell input {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 6px;
        text-align: right;
        padding: 5px;
        outline: none;
    }

    /* Tombol simpan */
    .btn-simpan {
        padding: 8px 18px;
        background: linear-gradient(90deg, #007bff, #00c6ff);
        color: #fff;
        border-radius: 25px;
        font-size: 14px;
        font-weight: bold;
        border: none;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-simpan:hover {
        background: linear-gradient(90deg, #0062cc, #0099ff);
    }

    /* Container utama */
    .container {
        display: flex;         
        min-height: 100vh;     
    }

    /* Kolom tabel */
    th:nth-child(1), td:nth-child(1) { width: 8%; }   
    th:nth-child(2), td:nth-child(2) { width: 12%; }  
    th:nth-child(3), td:nth-child(3) { width: 40%; }  
    th:nth-child(4), td:nth-child(4) { width: 20%; }  
    th:nth-child(5), td:nth-child(5) { width: 20%; }  
</style>
</head>
<body>
    <header>
            <img src="foto/logo.png" alt="Logo" class="logo">
            <nav>
                <a href="dashboard_eco.php">Home</a>
                <a href="anggota.php">Anggota</a>
                <a href="anggaran_eco.php" class="active">Anggaran</a>

            </nav>
            <div class="profile-wrapper" onclick="toggleDropdown()">
                <img src="foto/<?= htmlspecialchars($foto) ?>" alt="Profile" class="profile-pic">
                <div class="dropdown" id="dropdownMenu">
                    <a href="logout.php">Logout</a>
                    <a href="editprofile.php">Edit Profile</a>
                </div>
            </div>
        </header>
    <div class="container">
        <div class="sidebar">
            <a href="anggaran_eco.php">Data</a>
            <a href="anggaran_eco_perkiraan.php">Perkiraan</a>
            <a href="anggaran_eco_kas.php">Kas Harian</a>
            <a href="anggaran_eco_bank.php">Bank</a>
            <a href="anggaran_eco_bruder.php">Bruder</a>
            <a href="anggaran_eco_lu.php" class="active">LU Komunitas</a>
            <a href="anggaran_eco_evaluasi.php">Evaluasi</a>
            <a href="anggaran_eco_opname.php">Kas Opname</a>
        </div>
        <div class="main">
            <main>
                <h1>KOMUNITAS FIC CANDI<br>LAPORAN KEUANGAN<br>BULAN JANUARI 2025</h1>
                <div class="card">
                        <?php
// Query gabungan data laporan
$query = "
    SELECT 
        p.kode AS kode_perkiraan,
        p.akun AS nama_perkiraan,
        COALESCE(SUM(l.nominal_pemasukan), 0) AS penerimaan,
        COALESCE(SUM(l.nominal_pengeluaran), 0) AS pengeluaran
    FROM 2_perkiraan p
    LEFT JOIN 6_lu_komunitas l ON p.ID_pos = l.id_pos
    GROUP BY p.ID_pos, p.kode, p.akun
    ORDER BY CAST(p.kode AS UNSIGNED)
";
$stmt = $pdo->query($query);
$dataLaporan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil nama pemimpin & bendahara komunitas
$stmt2 = $pdo->query("SELECT nama_pemimpinlokal, nama_bendaharakomunitas FROM 1_data WHERE nama_kota='Semarang' LIMIT 1");
$kom = $stmt2->fetch(PDO::FETCH_ASSOC);
?>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Perkiraan</th>
            <th>Nama Perkiraan</th>
            <th>Penerimaan</th>
            <th>Pengeluaran</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1; 
        $total_penerimaan = 0; 
        $total_pengeluaran = 0; 
        foreach ($dataLaporan as $row): 
            $total_penerimaan += $row['penerimaan'];
            $total_pengeluaran += $row['pengeluaran'];
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['kode_perkiraan']) ?></td>
            <td><?= htmlspecialchars($row['nama_perkiraan']) ?></td>
            <td><?= number_format($row['penerimaan'], 0, ',', '.') ?></td>
            <td><?= number_format($row['pengeluaran'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
                <!-- Total baris -->
        <tr style="font-weight:bold; background:#f9f9f9;">
            <td colspan="3">Jumlah</td>
            <td><?= number_format($total_penerimaan, 0, ',', '.') ?></td>
            <td><?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
        </tr>
        <tr style="font-weight:bold; background:#fff;">
            <td colspan="3">Saldo Kas dan Bank</td>
            <td><?= number_format(0, 0, ',', '.') ?></td>
            <td><?= number_format($total_penerimaan - $total_pengeluaran, 0, ',', '.') ?></td>
        </tr>
        <tr style="font-weight:bold; background:#f1f1f1;">
            <td colspan="3">Jumlah Semua</td>
            <td><?= number_format($total_penerimaan, 0, ',', '.') ?></td>
            <td><?= number_format($total_penerimaan, 0, ',', '.') ?></td>
        </tr>

    </tbody>
</table>

<div style="margin-top:40px; display:flex; justify-content:space-between;">
    <div>
        Mengetahui:<br>
        Pemimpin Komunitas<br><br><br>
        <strong><?= htmlspecialchars($kom['nama_pemimpinlokal'] ?? '-') ?></strong>
    </div>
    <div>
        Semarang, 31 Januari 2025<br>
        Bendahara Komunitas<br><br><br>
        <strong><?= htmlspecialchars($kom['nama_bendaharakomunitas'] ?? '-') ?></strong>
    </div>
</div>

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