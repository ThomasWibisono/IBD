<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    header("Location: login.php");
    exit;
}
$nama = $_SESSION['nama_bruder'];
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil foto untuk header
    $stmt = $pdo->prepare("
        SELECT db.foto
        FROM login_bruder lb
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $foto = !empty($user['foto']) ? $user['foto'] : 'default.png';

    // Ambil data bruder yang sedang login
    $id = $_SESSION['ID_bruder'];
    $stmt = $pdo->prepare("SELECT * FROM data_bruder WHERE ID_bruder = ?");
    $stmt->execute([$id]);
    $bruder = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bruder) {
        die("Data tidak ditemukan!");
    }

} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Pastikan folder foto ada
if (!is_dir('foto')) {
    mkdir('foto', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $ttl = $_POST['ttl'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $postulan = $_POST['postulan'] ?? '';
    $prasetia = $_POST['prasetia'] ?? '';
    $kaul = $_POST['kaul'] ?? '';
    $riwayat = $_POST['riwayat'] ?? '';
    $password = $_POST['password'] ?? '';
    $unit_kerja = $_POST['unit_kerja'] ?? '';
    $no_telp = $_POST['no_telp'] ?? '';
    $email = $_POST['email'] ?? '';

    // Upload foto baru
    if (!empty($_FILES['foto']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (in_array($_FILES['foto']['type'], $allowed_types)) {
            $fotoName = time() . "_" . basename($_FILES['foto']['name']);
            $target = "foto/" . $fotoName;
            move_uploaded_file($_FILES['foto']['tmp_name'], $target);
        } else {
            die("Format foto tidak valid. Gunakan JPG atau PNG.");
        }
    } else {
        $fotoName = $bruder['foto'];
    }

    $stmt = $pdo->prepare("
        UPDATE data_bruder 
        SET 
            nama_bruder = ?, 
            ttl_bruder = ?, 
            alamat_bruder = ?, 
            tahun_masuk_postulan = ?, 
            tahun_prasetia_pertama = ?, 
            tahun_kaul_kekal = ?, 
            riwayat_tugas = ?, 
            unit_kerja = ?, 
            no_telp = ?, 
            email = ?, 
            foto = ?
        WHERE ID_bruder = ?
    ");

    $stmt->execute([
        $nama, 
        $ttl, 
        $alamat, 
        $postulan, 
        $prasetia, 
        $kaul, 
        $riwayat, 
        $unit_kerja, 
        $no_telp, 
        $email, 
        $fotoName, 
        $id
    ]);


    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt2 = $pdo->prepare("UPDATE login_bruder SET password = ? WHERE ID_bruder = ?");
        $stmt2->execute([$hashed, $id]);
    }

    $_SESSION['nama_bruder'] = $nama;
    header("Location: dashboard_eco.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Profil Bruder</title>
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
    form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 400px;
        margin: 20px auto;
        background: white;
        padding: 20px;
        border-radius: 10px;
    }
    input, textarea, button {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    button {
        background: #1e90ff;
        color: white;
        cursor: pointer;
    }
    button:hover {
        background: #0b75d1;
    }
</style>
</head>
<body>
    <header>
        <img src="foto/logo.png" alt="Logo" class="logo">
        <nav>
            <a href="dashboard_eco.php">Home</a>
            <a href="anggota.php">Daftar Anggota</a>
            <a href="anggaran_eco.php">Anggaran</a>
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
    <h2>Edit Profil Bruder</h2>
    <form method="POST" enctype="multipart/form-data">
        <img src="foto/<?= htmlspecialchars($bruder['foto'] ?: 'default.png') ?>" alt="Foto Profil" class="profile-pic">

        <label>Nama Bruder:</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($bruder['nama_bruder']) ?>" required>

        <label>Tanggal Lahir (TTL):</label>
        <input type="date" name="ttl" value="<?= htmlspecialchars($bruder['ttl_bruder']) ?>">

        <label>Alamat:</label>
        <textarea name="alamat"><?= htmlspecialchars($bruder['alamat']) ?></textarea>

        <label>Tahun Masuk Postulan:</label>
        <input type="text" name="tahun_masuk_postulan" value="<?= htmlspecialchars($bruder['tahun_masuk_postulan']) ?>">

        <label>Tahun Prasetia Pertama:</label>
        <input type="text" name="tahun_prasetia_pertama" value="<?= htmlspecialchars($bruder['tahun_prasetia_pertama'] ?? '') ?>">

        <label>Tahun Kaul Kekal:</label>
        <input type="text" name="tahun_kaul_kekal" value="<?= htmlspecialchars($bruder['tahun_kaul_kekal'] ?? '') ?>">

        <label>Riwayat Tugas:</label>
        <textarea name="riwayat_tugas"><?= htmlspecialchars($bruder['riwayat_tugas'] ?? '') ?></textarea>

        <label>Unit Kerja:</label>
        <input type="text" name="unit_kerja" value="<?= htmlspecialchars($bruder['unit_kerja'] ?? '') ?>">

        <label>No. Telepon:</label>
        <input type="text" name="no_telp" value="<?= htmlspecialchars($bruder['no_telp'] ?? '') ?>">

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($bruder['email'] ?? '') ?>">

        <label>Foto Profil:</label>
        <input type="file" name="foto" accept="image/*">

        <label>Password Baru (kosongkan jika tidak diubah):</label>
        <input type="password" name="password" placeholder="********">

        <button type="submit">Simpan Perubahan</button>
    </form>
</main>
</body>
</html>
