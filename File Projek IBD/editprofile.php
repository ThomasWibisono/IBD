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

    // Proses update jika form disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = $_POST['nama_bruder'];
        $id = $_SESSION['ID_bruder'];

        if (!empty($_FILES['foto']['name'])) {
            $fotoName = basename($_FILES['foto']['name']);
            $targetPath = "foto/" . $fotoName;
            move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);

            $stmt = $pdo->prepare("UPDATE data_bruder SET nama_bruder = ?, foto = ? WHERE ID_bruder = ?");
            $stmt->execute([$nama, $fotoName, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE data_bruder SET nama_bruder = ? WHERE ID_bruder = ?");
            $stmt->execute([$nama, $id]);
        }

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
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            margin: 0;
            padding: 0;
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
        }
        input[type="text"],
        input[type="file"] {
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profil Bruder</h2>
        <div class="profile-preview">
            <img src="foto/<?= htmlspecialchars($bruder['foto']) ?>" alt="Foto Bruder">
        </div>
        <form method="POST" enctype="multipart/form-data">
            <label for="nama_bruder">Nama Bruder</label>
            <input type="text" name="nama_bruder" id="nama_bruder" value="<?= htmlspecialchars($bruder['nama_bruder']) ?>" required>

            <label for="foto">Ganti Foto</label>
            <input type="file" name="foto" id="foto" accept="image/*">

            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>
