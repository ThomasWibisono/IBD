<?php
session_start();
if (!isset($_SESSION['ID_bruder'])) {
    header("Location: login.php");
    exit;
}

// 🔹 KONEKSI DATABASE (pindahkan ke atas biar $pdo bisa dipakai di semua bagian)
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// 🔹 PROSES SIMPAN DATA
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['simpan'])) {
    $nama = $_POST['nama_bruder'];
    $ttl = $_POST['ttl_bruder'];
    $alamat_bruder = $_POST['alamat_bruder'];
    $tahun_postulan = $_POST['tahun_masuk_postulan'];
    $tahun_prasetia = $_POST['tahun_prasetia_pertama'];
    $tahun_kaul = $_POST['tahun_kaul_kekal'];
    $riwayat = $_POST['riwayat_tugas'];
    $unit = $_POST['unit_kerja'];
    $alamat = $_POST['alamat'];
    $telp = $_POST['no_telp'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $password = password_hash($_POST['password_bruder'], PASSWORD_DEFAULT);

    // Upload foto
    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $targetDir = "foto/";
        $fotoName = time() . "_" . basename($_FILES["foto"]["name"]);
        $targetFile = $targetDir . $fotoName;
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
            $foto = $fotoName;
        }
    }

    // Insert ke tabel data_bruder
    $stmtInsert = $pdo->prepare("
        INSERT INTO data_bruder 
        (nama_bruder, gambar_bruder, ttl_bruder, alamat_bruder, tahun_masuk_postulan,
        tahun_prasetia_pertama, tahun_kaul_kekal, riwayat_tugas, unit_kerja, alamat, no_telp, email, foto)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtInsert->execute([$nama, $foto, $ttl, $alamat_bruder, $tahun_postulan, $tahun_prasetia, $tahun_kaul,
                         $riwayat, $unit, $alamat, $telp, $email, $foto]);

    // Ambil ID terakhir
    $lastID = $pdo->lastInsertId();

    // Insert juga ke login_bruder
    $stmtLogin = $pdo->prepare("
        INSERT INTO login_bruder (ID_bruder, nama_bruder, password_bruder, status)
        VALUES (?, ?, ?, ?)
    ");
    $stmtLogin->execute([$lastID, $nama, $password, $status]);

    echo "<script>alert('Data Bruder berhasil ditambahkan!'); window.location.href='anggota.php';</script>";
}

// 🔹 Ambil data user untuk header profile
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Anggota Baru</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4;
        }
        header {
            width: 100%;          
            display: flex;
            align-items: center;
            justify-content: space-between;
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
            margin-top: 50px;
            padding: 20px;
            text-align: center;
        }
        .btn-simpan {
            padding: 8px 18px;
            background: #1e90ff;
            color: #fff;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-simpan:hover {
            background: #0b75d1;
        }
        .form-tambah {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            width: 100%;
        }
        .form-group {
            width: 80%;
            text-align: left;
        }
        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
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
        <img src="foto/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Bruder" class="profile-pic">
        <div class="dropdown" id="dropdownMenu">
            <a href="logout.php">Logout</a>
            <a href="editprofile.php">Edit Profile</a>
        </div>
    </div>
</header>
<main>
    <div class="card">
        <h2>Tambah Anggota Bruder</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="form-tambah">
            <div class="form-group"><label>Nama Bruder:</label><input type="text" name="nama_bruder" required></div>
            <div class="form-group"><label>Tempat, Tanggal Lahir:</label><input type="date" name="ttl_bruder"></div>
            <div class="form-group"><label>Alamat Bruder:</label><textarea name="alamat_bruder" rows="3"></textarea></div>
            <div class="form-group"><label>Tahun Masuk Postulan:</label><input type="number" name="tahun_masuk_postulan" min="1900" max="2099"></div>
            <div class="form-group"><label>Tahun Prasetia Pertama:</label><input type="number" name="tahun_prasetia_pertama" min="1900" max="2099"></div>
            <div class="form-group"><label>Tahun Kaul Kekal:</label><input type="number" name="tahun_kaul_kekal" min="1900" max="2099"></div>
            <div class="form-group"><label>Riwayat Tugas:</label><textarea name="riwayat_tugas" rows="3"></textarea></div>
            <div class="form-group"><label>Unit Kerja:</label><input type="text" name="unit_kerja"></div>
            <div class="form-group"><label>Alamat:</label><input type="text" name="alamat"></div>
            <div class="form-group"><label>No. Telepon:</label><input type="text" name="no_telp"></div>
            <div class="form-group"><label>Email:</label><input type="email" name="email"></div>
            <div class="form-group"><label>Foto Bruder:</label><input type="file" name="foto" accept="image/*"></div>
            <div class="form-group"><label>Status:</label>
                <select name="status" required>
                    <option value="bruder">Bruder</option>
                    <option value="econom">Econom</option>
                </select>
            </div>
            <div class="form-group"><label>Password:</label><input type="password" name="password_bruder" required></div>
            <button type="submit" name="simpan" class="btn-simpan">Simpan</button>
        </form>
    </div>
</main>
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
