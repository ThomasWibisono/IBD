<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("
        SELECT db.foto
        FROM login_bruder lb
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $foto = !empty($user['foto']) ? $user['foto'] : 'default.png';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $pdo->prepare("
            INSERT INTO 1_data (
                nama_pemimpinlokal, nama_bendaharakomunitas, nama_kota,
                pos_A, pos_B, pos_C, pos_D, pos_E, pos_F, pos_G, pos_H, pos_I,
                pos_1, pos_2, pos_3, pos_4, pos_5, pos_6, pos_7, pos_8, pos_9,
                pos_10, pos_11, pos_12, pos_13, pos_14, pos_15, pos_16, pos_17, pos_18, pos_19,
                pos_20, pos_21, pos_22, pos_23, pos_24, pos_25, pos_26, pos_27, pos_28, pos_29,
                pos_30, pos_31, pos_32, pos_33, pos_34, pos_35, pos_36, pos_37, pos_38
            ) VALUES (
                :nama_pemimpinlokal, :nama_bendaharakomunitas, :nama_kota,
                :pos_A, :pos_B, :pos_C, :pos_D, :pos_E, :pos_F, :pos_G, :pos_H, :pos_I,
                :pos_1, :pos_2, :pos_3, :pos_4, :pos_5, :pos_6, :pos_7, :pos_8, :pos_9,
                :pos_10, :pos_11, :pos_12, :pos_13, :pos_14, :pos_15, :pos_16, :pos_17, :pos_18, :pos_19,
                :pos_20, :pos_21, :pos_22, :pos_23, :pos_24, :pos_25, :pos_26, :pos_27, :pos_28, :pos_29,
                :pos_30, :pos_31, :pos_32, :pos_33, :pos_34, :pos_35, :pos_36, :pos_37, :pos_38
            )
        ");

        $params = [
            ':nama_pemimpinlokal' => $_POST['nama_pemimpinlokal'] ?? '',
            ':nama_bendaharakomunitas' => $_POST['nama_bendaharakomunitas'] ?? '',
            ':nama_kota' => $_POST['nama_kota'] ?? '',
        ];

        // Tambahkan semua pos
        $fields = ['pos_A','pos_B','pos_C','pos_D','pos_E','pos_F','pos_G','pos_H','pos_I'];
        for ($i = 1; $i <= 38; $i++) $fields[] = "pos_$i";

        foreach ($fields as $f) {
            $params[":$f"] = $_POST[$f] ?? 0;
        }

        $stmt->execute($params);

        echo "<script>alert('Data berhasil disimpan!');</script>";
    }
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Anggaran</title>
    <style>
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
            min-height: 100vh;
            padding: 15px 0;
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
        main {
            padding: 20px;
            max-width: 1100px;
            margin: auto;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            margin: 20px 0;
        }
        .table-header {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
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
        .input-cell input {
            width: 100%;
            border: none;
            text-align: right;
            padding: 5px;
            outline: none;
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
</style>
</head>
<body>
    <div class="sidebar">
        <a href="anggaran_eco.php" class="active">Data</a>
        <a href="anggaran_eco_perkiraan.php">Perkiraan</a>
        <a href="anggaran_eco_kas.php">Kas Harian</a>
        <a href="anggaran_eco_bank.php">Bank</a>
        <a href="anggaran_eco_bruder.php">Bruder</a>
        <a href="anggaran_eco_lu.php">LU Komunitas</a>
        <a href="anggaran_eco_evaluasi.php">Evaluasi</a>
        <a href="anggaran_eco_buku.php">Buku Besar</a>
        <a href="anggaran_eco_opname.php">Kas Opname</a>
    </div>

    <div class="main">
        <header>
            <img src="foto/logo.png" alt="Logo" class="logo">
            <nav>
                <a href="dashboard_eco.php" class="active">Home</a>
                <a href="anggota.php">Daftar Anggota</a>
                <a href="anggaran_eco.php">Anggaran</a>
            </nav>
            <div class="profile-wrapper" onclick="toggleDropdown()">
                <img src="foto/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Bruder" class="profile-pic">
                <div class="dropdown" id="dropdownMenu">
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </header>
        <main>
            <h1>ANGGARAN PENDAPATAN DAN BELANJA <br>KOMUNITAS FIC CANDI<br>TAHUN 2025</h1>
            <div class="card">
                <form method="post">
                    <label>Pemimpin Lokal: <input type="text" name="nama_pemimpinlokal" required></label><br><br>
                    <label>Bendahara Komunitas: <input type="text" name="nama_bendaharakomunitas" required></label><br><br>
                    <label>Kota:
                        <select name="nama_kota">
                            <option value="Jakarta">Jakarta</option>
                            <option value="Bandung">Bandung</option>
                            <option value="Yogyakarta">Yogyakarta</option>
                            <option value="Semarang">Semarang</option>
                        </select>
                    </label>

                    <div class="table-header">
                        <button type="submit" class="btn-simpan">Simpan</button>
                    </div>

                    <table>
                        <thead>
                        <tr>
                            <th>POS</th>
                            <th>KODE</th>
                            <th>NAMA PERKIRAAN</th>
                            <th>ANGGARAN</th>
                            <th>KETERANGAN</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>A</td><td>410101</td><td>Gaji/Pendapatan Bruder</td>
                            <td class="input-cell"><input type="number" step="0.01" name="pos_A"></td>
                            <td>Per Bulan</td>
                        </tr>
                        <tr>
                            <td>B</td><td>410102</td><td>Pensiun Bruder</td>
                            <td class="input-cell"><input type="number" step="0.01" name="pos_B"></td>
                            <td>Per Bulan</td>
                        </tr>
                        <tr>
                            <td>C</td><td>430101</td><td>Hasil Kebun dan Piaraan</td>
                            <td class="input-cell"><input type="number" step="0.01" name="pos_C"></td>
                            <td>Per Bulan</td>
                        </tr>
                        <!-- lanjutkan sesuai kebutuhan sampai pos_38 -->
                        <tr>
                            <td>1</td><td>510101</td><td>Makanan</td>
                            <td class="input-cell"><input type="number" step="0.01" name="pos_1"></td>
                            <td>Per Bruder Per Bulan</td>
                        </tr>
                        <tr>
                            <td>2</td><td>510201</td><td>Pakaian</td>
                            <td class="input-cell"><input type="number" step="0.01" name="pos_2"></td>
                            <td>Per Bruder Per Bulan</td>
                        </tr>
                        </tbody>
                    </table>
                </form>
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
