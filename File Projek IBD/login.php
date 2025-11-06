<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

if (isset($_SESSION['ID_bruder'])) {
    header('Location: dashboard_eco.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_bruder = $_POST['nama_bruder'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($nama_bruder && $password) {
        $stmt = $pdo->prepare("SELECT * FROM login_bruder WHERE nama_bruder = ?");
        $stmt->execute([$nama_bruder]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password_bruder']) {
            $_SESSION['ID_bruder'] = $user['ID_bruder'];
            $_SESSION['nama_bruder'] = $user['nama_bruder'];
            $_SESSION['status'] = $user['status'];

            header('Location: dashboard_eco.php');
            exit;
        } else {
            $error = "Nama Bruder atau password salah.";
        }
    } else {
        $error = "Mohon isi nama dan password.";
    }
}

$page_title = "Login Bruder";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>HALO FIC</title>
<link rel="stylesheet" type="text/css" href="styleLogin.css">
</head>
<body>
    <div class="wrapper">
        <div class="left-panel">
            <h2>Masuk Akun</h2>
            <p>Masukkan kode pengguna dan kata sandi untuk mengakses akun anda</p>
            <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <form method="post" action="">
                <label for="nama_bruder">Pengguna</label>
                <input type="text" id="nama_bruder" name="nama_bruder" required>
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required>
                <div class="checkbox"><input type="checkbox" onclick="togglePassword()"> Tampilkan sandi</div>
                <button type="submit">Masuk Akun</button>
            </form>
        </div>
        <div class="right-section">
            <h1>HALO FIC</h1>
            <img src="foto/logo.png" alt="Logo FIC">
        </div>
    </div>
    <script>
        function togglePassword() {
            let pass=document.getElementById("password");
            pass.type=(pass.type==="password")?"text":"password";
        }
    </script>
</body>
</html>
