<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

if (isset($_SESSION['ID_bruder'])) {
    if ($_SESSION['status'] === 'econom') {
        header('Location: dashboard_eco.php');
    } else {
        header('Location: dashboard_brud.php');
    }
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

            if ($user['status'] === 'econom') {
                header('Location: dashboard_eco.php');
            } else {
                header('Location: dashboard_brud.php');
            }
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
    <title><?= htmlspecialchars($page_title) ?></title>
    <style>
    body {
        margin: 0;
        padding: 0;
        height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        background: url("foto/fic2.jpeg") no-repeat center center fixed;
        background-size: cover;
    }

    .wrapper {
        display: flex;
        width: 850px;
        height: 520px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        background: #fff;
    }

    .left-panel {
        flex: 1.1;
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .left-panel h2 {
        margin-bottom: 10px;
        color: #1e90ff;
        font-size: 28px;
    }

    .left-panel p {
        color: #555;
        font-size: 15px;
        margin-bottom: 30px;
    }

    label {
        display: block;
        margin: 12px 0 6px;
        font-weight: bold;
        color: #333;
        font-size: 14px;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 12px;
        border: 1px solid #cce0ff;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
        border-color: #1e90ff;
        outline: none;
        box-shadow: 0 0 6px rgba(30, 144, 255, 0.5);
    }

    .checkbox {
        margin: 12px 0;
        font-size: 13px;
        color: #333;
    }

    .checkbox input {
        margin-right: 6px;
    }

    button {
        margin-top: 18px;
        width: 100%;
        padding: 13px;
        background: linear-gradient(135deg, #1e90ff, #00c6ff);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.2s, background 0.3s;
    }

    button:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #0b75d1, #0096e0);
    }

    .error {
        color: #d9534f;
        margin-bottom: 15px;
        font-weight: bold;
        text-align: center;
    }

    .right-section {
        flex: 0.9;
        background: #1e90ff;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 10px;
        padding: 20px;
        position: relative;
    }

    .right-section h2 {
        font-size: 32px;
        font-weight: bold;
        letter-spacing: 1px;
        margin: 0;
    }

    .right-section img {
        width: 220px;
        height: auto;
        display: block;
    }

    .small-link {
        display: block;
        margin-top: 15px;
        font-size: 13px;
        color: #1e90ff;
        text-decoration: none;
        text-align: center;
    }

    .small-link:hover {
        text-decoration: underline;
    }
</style>
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
            <img src="foto/fic.png" alt="Logo FIC">
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
