<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Jika sudah login langsung ke dashboard
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>HALO FIC</title>
    <style>
        /* === LATAR DAN POSISI UTAMA === */
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

        /* === KOTAK LOGIN TRANSPARAN === */
        .wrapper {
            display: flex;
            width: 850px;
            height: 520px;
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: 0.4s ease;
        }

        .wrapper:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.5);
        }

        /* === PANEL KIRI (FORM LOGIN) === */
        .left-panel {
            flex: 1.1;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.3);
            border-right: 1px solid rgba(255, 255, 255, 0.4);
        }

        .left-panel h2 {
            margin-bottom: 10px;
            color: #0059ff;
            font-size: 30px;
            font-weight: bold;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .left-panel p {
            color: #222;
            font-size: 15px;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin: 12px 0 6px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.6);
            font-size: 15px;
            transition: all 0.3s;
            color: #000;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #1e90ff;
            outline: none;
            box-shadow: 0 0 10px rgba(30, 144, 255, 0.4);
        }

        .checkbox {
            margin: 12px 0;
            font-size: 13px;
            color: #000;
        }

        .checkbox input {
            margin-right: 6px;
        }

        button {
            margin-top: 18px;
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1e90ff, #00bfff);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.3s;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(30, 144, 255, 0.6);
        }

        .error {
            color: #ff4d4d;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.3);
        }

        /* === PANEL KANAN (LOGO FIC) === */
        .right-section {
            flex: 0.9;
            background: rgba(30, 144, 255, 0.25);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding: 20px;
            position: relative;
            backdrop-filter: blur(25px);
            border-left: 1px solid rgba(255, 255, 255, 0.4);
        }

        .right-section h1 {
            padding-top: 35%;
            font-size: 42px;
            font-weight: bold;
            margin: 0 0 10px 0;
            color: white;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .right-section img {
            width: 220px;
            height: auto;
            display: block;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));
        }

        .small-link {
            display: block;
            margin-top: 15px;
            font-size: 13px;
            color: #0059ff;
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
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form method="post" action="">
                <label for="nama_bruder">Pengguna</label>
                <input type="text" id="nama_bruder" name="nama_bruder" required>

                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required>

                <div class="checkbox">
                    <input type="checkbox" onclick="togglePassword()"> Tampilkan sandi
                </div>
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
            let pass = document.getElementById("password");
            pass.type = (pass.type === "password") ? "text" : "password";
        }
    </script>
</body>
</html>
