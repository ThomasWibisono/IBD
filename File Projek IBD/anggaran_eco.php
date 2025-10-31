<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    header("Location: login.php");
    echo "<script>alert('Anggaran hanya bisa diakses oleh Ekonom!'); window.location='dashboard_eco.php';</script>";
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
        $fields = ['pos_A','pos_B','pos_C','pos_D','pos_E','pos_F','pos_G','pos_H','pos_I','pos_1', 'pos_2','pos_3', 'pos_4', 'pos_5', 'pos_6', 'pos_7', 'pos_8', 'pos_9',
                'pos_10', 'pos_11', 'pos_12', 'pos_13', 'pos_14', 'pos_15', 'pos_16', 'pos_17', 'pos_18', 'pos_19',
                'pos_20', 'pos_21', 'pos_22', 'pos_23', 'pos_24', 'pos_25', 'pos_26', 'pos_27', 'pos_28', 'pos_29',
                'pos_30', 'pos_31', 'pos_32', 'pos_33', 'pos_34', 'pos_35', 'pos_36', 'pos_37', 'pos_38'];
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
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<!-- Font Quicksand -->
<style>
    /* ===== Global Reset ===== */
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

    /* Sidebar */
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
    /* ===== Form Sejajar Rapi ===== */
    form {
        display: flex;
        flex-direction: column;
        gap: 12px; /* jarak antar baris */
    }

    form .form-row {
        display: flex;
        align-items: center;
    }

    form .form-row label {
        width: 180px; /* lebar label tetap */
        font-weight: 500;
        font-size: 14px;
        color: #004b8d;
    }

    form .form-row input,
    form .form-row select {
    flex: 1; /* input mengisi sisa ruang */
    max-width: 250px; /* tapi tidak terlalu panjang */
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
    transition: all 0.3s ease;
    }

    form .form-row input:focus,
    form .form-row select:focus {
    border-color: #0077ff;
    box-shadow: 0 0 6px rgba(0, 119, 255, 0.3);
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
            <a href="anggaran_eco.php" class="active" >Anggaran</a>
        </nav>
        <div class="profile-wrapper" onclick="toggleDropdown()">
            <img src="foto/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Bruder" class="profile-pic">
            <div class="dropdown" id="dropdownMenu">
                <a href="logout.php">Logout</a>
                <a href="editprofile.php">Edit Profile</a>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="sidebar">
            <a href="anggaran_eco.php" class="active">Data</a>
            <a href="anggaran_eco_perkiraan.php">Perkiraan</a>
            <a href="anggaran_eco_kas.php">Kas Harian</a>
            <a href="anggaran_eco_bank.php">Bank</a>
            <a href="anggaran_eco_bruder.php">Bruder</a>
            <a href="anggaran_eco_lu.php">LU Komunitas</a>
            <a href="anggaran_eco_evaluasi.php">Evaluasi</a>
            <a href="anggaran_eco_opname.php">Kas Opname</a>
        </div>
        <div class="main">
            <main>
                <h1>ANGGARAN PENDAPATAN DAN BELANJA <br>KOMUNITAS FIC CANDI<br>TAHUN 2025</h1>
                <div class="card">
                    <form method="post">
                        <div class="form-row">
                            <label>Pemimpin Lokal:</label>
                            <input type="text" name="nama_pemimpinlokal" required>
                        </div>
                        <div class="form-row">
                            <label>Bendahara Komunitas:</label>
                            <input type="text" name="nama_bendaharakomunitas" required>
                        </div>
                        <div class="form-row">
                            <label>Kota:</label>
                            <select name="nama_kota">
                            <option value="Jakarta">Jakarta</option>
                            <option value="Bandung">Bandung</option>
                            <option value="Yogyakarta">Yogyakarta</option>
                            <option value="Semarang">Semarang</option>
                            </select>
                        </div>
                        <div class="table-header">
                            <button type="submit" class="btn-simpan">Simpan</button>
                        </div>
                        <h2>PERSERTUJUAN BUDGET</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>POS</th>
                                    <th>KODE</th>
                                    <th>NAMA PERKIRAAN</th>
                                    <th>ANGGARAN</th>
                                    <th>KETERANGAN</th>
                                </tr>
                                <tr>
                                    <th colspan="5" style="text-align:left; padding-left:20px;">PENERIMAAN :</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>C</td><td>410101</td><td>Gaji/Pendapatan Bruder</td>
                                    <td class="input-cell"><input type="number" step="0.01" class="penerimaan"></td>
                                    <td>Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>D</td><td>410102</td><td>Pensiun Bruder</td>
                                    <td class="input-cell"><input type="number" step="0.01" class="penerimaan"></td>
                                    <td>Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>E</td><td>430101</td><td>Hasil Kebun dan Piaraan</td>
                                    <td class="input-cell"><input type="number" step="0.01" class="penerimaan"></td>
                                    <td>Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>F</td><td>420101</td><td>Bunga Tabungan</td>
                                    <td class="input-cell"><input type="number" step="0.01" class="penerimaan"></td>
                                    <td>Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>G</td><td>410202</td><td>Sumbangan</td>
                                    <td class="input-cell"><input type="number" step="0.01" class="penerimaan"></td>
                                    <td>Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>H</td><td>430103</td><td>Penerimaan Lainnyan</td>
                                    <td class="input-cell"><input type="number" step="0.01" class="penerimaan"></td>
                                    <td>Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>I</td><td>610100</td><td>Penerimaan dari DP</td>
                                    <td class="input-cell"><input type="number" step="0.01" class="penerimaan"></td>
                                    <td>Per Bulan</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                <td colspan="3" style="text-align:right; font-weight:bold;">Jumlah Penerimaan :</td>
                                <td><span id="totalPenerimaan">0</span></td>
                                <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <table>
                            <thead>
                            <tbody>
                                <tr>
                                    <th>POS</th>
                                    <th>KODE</th>
                                    <th>NAMA PERKIRAAN</th>
                                    <th>ANGGARAN</th>
                                    <th>KETERANGAN</th>
                                </tr>
                                <tr>
                                    <th colspan="5" style="text-align:left; padding-left:20px;">BEBAN KOMUNITAS :</th>
                                </tr>
                                <tr>
                                    <th colspan="5" style="text-align:left; padding-left:20px;">REKENING PRIBADI</th>
                                </tr>
                                </thead>
                                <tr>
                                    <td>1</td><td>510101</td><td>Makanan</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>2</td><td>510201</td><td>Pakaian dan Perlengkapan Pribadi</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>3</td><td>510301</td><td>Pemeriksaan Dan Pengobatan</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>4</td><td>510303</td><td>Hiburan/Rekreasi</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>5</td><td>510501</td><td>Transport Harian</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <th colspan="5" style="text-align:left; padding-left:20px;">REKENING UMUM</th>
                                </tr>
                                <tr>
                                    <td>6</td><td>520401</td><td>Studi Pribadi</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>7</td><td>510102</td><td>Bahan Bakar Dapur</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>8</td><td>510103</td><td>Perlengkapan Cuci dan Kebersihan</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>9</td><td>510104</td><td>Perabot Rumah Tangga</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>10</td><td>510104</td><td>Iuran Hidup Bermasyarakat Dan Menggereja </td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>11</td><td>510105</td><td>Listrik</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>12</td><td>510401</td><td>Air</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>13</td><td>510403</td><td>Telepon Dan Internet</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>14</td><td>520201</td><td>Keperluan Ibadah</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>15</td><td>530303</td><td>Sumbangan </td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>16</td><td>540101</td><td>Insentif ART</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>17</td><td>540201</td><td>Pemeliharaan Rumah</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>18</td><td>540202</td><td>Pemeliharaan Kebun Dan Piaraan</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>19</td><td>540203</td><td>Pemeliharaan Kendaraan</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>20</td><td>540204</td><td>Pemeliharaan Mesin Dan Peralatan</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>21</td><td>550101</td><td>Administrasi Komunitas</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>22</td><td>550105</td><td>Legal dan Perijinan </td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>23</td><td>550106</td><td>Buku, Majalah, Koran</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>24</td><td>550107</td><td>Administrasi Bank</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>25</td><td>550201</td><td>Pajak Bunga Bank</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>26</td><td>550202</td><td>Pajak Kendaraan Dan PBB</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>27</td><td>510103</td><td>Perlengkapan Cuci dan Kebersihan</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>28</td><td>510103</td><td>Perlengkapan Cuci dan Kebersihan</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>29</td><td>520501</td><td>Penunjang Kesehatan Lansia</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>30</td><td>520502</td><td>Pemeliharaan Rohani Lansia</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <th colspan="5" style="text-align:left; padding-left:20px;">BUDGET KHUSUS</th>
                                </tr>
                                <tr>
                                    <td>31</td><td>520503</td><td>Kegiatan Bruder Lansia</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>32</td><td>130300</td><td>Mesin dan Peralatan</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>33</td><td>130400</td><td>Perabot Rumah Tangga</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>34</td><td>520100</td><td>Transport Pertemuan</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>35</td><td>520200</td><td>Perayaan Syukur</td>
                                    <td class="input-cell"><input type="number" step="0.01" name="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>36</td><td>510501</td><td>Kegiatan Lansia</td>
                                    <td class="input-cell"><input type="number" step="0.01" class="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>37</td><td>540501</td><td>Pemeliharaan Rumah</td>
                                    <td class="input-cell"><input type="number" step="0.01" class="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                                <tr>
                                    <td>38</td><td>540300</td><td>Budget Khusus Lainnya</td>
                                    <td class="input-cell"><input type="number" step="0.01" class="beban"></td>
                                    <td>Per Bruder Per Bulan</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                <td colspan="3" style="text-align:right; font-weight:bold;">Jumlah Beban :</td>
                                <td><span id="totalBeban">0</span></td>
                                <td></td>
                                </tr>
                            </tfoot>
                        </table>                
                    </form>
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
        function hitungTotal(className, idOutput) {
            let inputs = document.querySelectorAll("." + className);
            let total = 0;
            inputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById(idOutput).innerText = total.toLocaleString();
        }

        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener("input", () => {
                hitungTotal("penerimaan", "totalPenerimaan");
                hitungTotal("beban", "totalBeban");
            });
        });
    </script>
</body>
</html>
