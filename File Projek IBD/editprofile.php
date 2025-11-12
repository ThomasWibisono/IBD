<?php
session_start();
if (!isset($_SESSION['ID_bruder'])) {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil data Bruder
    $stmt = $pdo->prepare("SELECT * FROM data_bruder WHERE ID_bruder = ?");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $bruder = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ambil info login & foto untuk navbar
    $stmt = $pdo->prepare("
        SELECT lb.nama_bruder, db.unit_kerja, db.alamat, db.no_telp, db.email, db.foto
        FROM login_bruder lb
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Proses update data
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_SESSION['ID_bruder'];
        $data = [
            'nama_bruder' => $_POST['nama_bruder'],
            'ttl_bruder' => $_POST['ttl_bruder'],
            'alamat_bruder' => $_POST['alamat_bruder'],
            'tahun_masuk_postulan' => $_POST['tahun_masuk_postulan'],
            'tahun_prasetia_pertama' => $_POST['tahun_prasetia_pertama'],
            'tahun_kaul_kekal' => $_POST['tahun_kaul_kekal'],
            'riwayat_tugas' => $_POST['riwayat_tugas'],
            'unit_kerja' => $_POST['unit_kerja'],
            'alamat' => $_POST['alamat'],
            'no_telp' => $_POST['no_telp'],
            'email' => $_POST['email'],
        ];

        // Proses upload foto (jika ada)
        if (!empty($_FILES['foto']['name'])) {
            $fotoName = basename($_FILES['foto']['name']);
            $targetPath = "foto/" . $fotoName;
            move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);
            $data['foto'] = $fotoName;
        } else {
            $data['foto'] = $bruder['foto'];
        }

        // Update ke database
        $stmt = $pdo->prepare("
            UPDATE data_bruder 
            SET nama_bruder = :nama_bruder,
                ttl_bruder = :ttl_bruder,
                alamat_bruder = :alamat_bruder,
                tahun_masuk_postulan = :tahun_masuk_postulan,
                tahun_prasetia_pertama = :tahun_prasetia_pertama,
                tahun_kaul_kekal = :tahun_kaul_kekal,
                riwayat_tugas = :riwayat_tugas,
                unit_kerja = :unit_kerja,
                alamat = :alamat,
                no_telp = :no_telp,
                email = :email,
                foto = :foto
            WHERE ID_bruder = :id
        ");
        $data['id'] = $id;
        $stmt->execute($data);

        header("Location: anggota.php");
        exit;
    }

} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Profil Bruder</title>
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
        .container {
            max-width: 600px;
            margin: 60px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #1e90ff;
            margin-bottom: 30px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            text-align: left;
        }
        input, textarea, select{
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 14px;
        }
        .btn-submit {
            background: linear-gradient(to right, #1e90ff, #00bcd4);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 30px;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }
        .btn-submit:hover {
            background: linear-gradient(to right, #00bcd4, #1e90ff);
        }
        .profile-preview {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-preview img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #1e90ff;
        }
        footer {
        margin-top: 50px;
        text-align: center;
        color: #004fa3;
        opacity: 0.9;
        font-size: 14px;
    }

    /* ===== Responsive ===== */
    @media(max-width: 768px) {
        nav {
            gap: 10px;
        }
        .cards {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>
<body>
<header>
    <img src="foto/logo.png" alt="Logo" class="logo">
    <nav>
        <a href="dashboard_eco.php" class="active">Home</a>
        <a href="anggota.php">Anggota</a>
        <a href="anggaran_eco.php">Anggaran</a>
    </nav>
    <div class="profile-wrapper" onclick="toggleDropdown()">
        <img src="foto/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Bruder" class="profile-pic">
        <div class="dropdown" id="dropdownMenu">
            <a href="editprofile.php">Edit Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>
<main>
    <div class="container">
        <h2>Edit Profil Bruder</h2>
        <div class="profile-preview">
            <img src="foto/<?= htmlspecialchars($bruder['foto']) ?>" alt="Foto Bruder">
        </div>
        <form method="POST" enctype="multipart/form-data">
            <label>Nama Bruder</label>
            <input type="text" name="nama_bruder" value="<?= htmlspecialchars($bruder['nama_bruder']) ?>" required>

            <label>Tanggal Lahir</label>
            <input type="date" name="ttl_bruder" value="<?= htmlspecialchars($bruder['ttl_bruder']) ?>">

            <label>Alamat Bruder</label>
            <textarea name="alamat_bruder"><?= htmlspecialchars($bruder['alamat_bruder']) ?></textarea>

            <label>Tahun Masuk Postulan</label>
            <input type="number" name="tahun_masuk_postulan" min="1900" max="2100" value="<?= htmlspecialchars($bruder['tahun_masuk_postulan']) ?>">

            <label>Tahun Prasetia Pertama</label>
            <input type="number" name="tahun_prasetia_pertama" min="1900" max="2100" value="<?= htmlspecialchars($bruder['tahun_prasetia_pertama']) ?>">

            <label>Tahun Kaul Kekal</label>
            <input type="number" name="tahun_kaul_kekal" min="1900" max="2100" value="<?= htmlspecialchars($bruder['tahun_kaul_kekal']) ?>">

            <label>Riwayat Tugas</label>
            <textarea name="riwayat_tugas"><?= htmlspecialchars($bruder['riwayat_tugas']) ?></textarea>

            <label>Unit Kerja</label>
            <input type="text" name="unit_kerja" value="<?= htmlspecialchars($bruder['unit_kerja']) ?>">

            <label>Alamat Sekarang</label>
            <input type="text" name="alamat" value="<?= htmlspecialchars($bruder['alamat']) ?>">

            <label>No Telepon</label>
            <input type="text" name="no_telp" value="<?= htmlspecialchars($bruder['no_telp']) ?>">

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($bruder['email']) ?>">

            <label>Foto</label>
            <input type="file" name="foto" accept="image/*">

            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </form>
    </div>
</main>
<footer>
    © <?= date('Y') ?> Komunitas Bruder FIC — All Rights Reserved.
</footer>
<script>
function toggleDropdown() {
    document.getElementById("dropdownMenu").classList.toggle("show");
}
window.onclick = function(e) {
    if (!e.target.closest('.profile-wrapper')) {
        document.getElementById("dropdownMenu").classList.remove("show");
    }
}
</script>
</body>
</html>
