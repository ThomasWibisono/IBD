<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ambil foto user (tetap)
    $stmt = $pdo->prepare("
        SELECT db.foto 
        FROM login_bruder lb 
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder 
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $foto = !empty($user['foto']) ? $user['foto'] : 'default.png';

    // HANDLE POST (simpan transaksi)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_transaction') {
        // buat ID_tabel_bank unik (varchar(10))
        $id_tabel_bank = 'B' . substr(uniqid(), -9);

        // ambil input (basic sanitasi)
        $nama_bank = $_POST['nama_bank'] ?? '';
        $no_rek_bank = $_POST['no_rek_bank'] ?? '';
        $atas_nama_bank = $_POST['atas_nama_bank'] ?? '';
        $tgl_transaksi = $_POST['tgl_transaksi'] ?? null;
        $ID_pos = $_POST['ID_pos'] ?? '';
        $keterangan_bank = $_POST['keterangan_bank'] ?? null;
        $type = $_POST['type'] ?? 'in'; // 'in' atau 'out'
        $nominal = isset($_POST['nominal']) && $_POST['nominal'] !== '' ? (float) str_replace(',', '', $_POST['nominal']) : 0;

        // tentukan kolom penerimaan/pengeluaran
        $nominal_penerimaan = ($type === 'in') ? $nominal : 0;
        $nominal_pengeluaran = ($type === 'out') ? $nominal : 0;

        $insert = $pdo->prepare("
            INSERT INTO `4_bank` 
            (ID_tabel_bank, nama_bank, no_rek_bank, atas_nama_bank, tgl_transaksi, ID_pos, keterangan_bank, nominal_penerimaan, nominal_pengeluaran)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->execute([
            $id_tabel_bank,
            $nama_bank,
            $no_rek_bank,
            $atas_nama_bank,
            $tgl_transaksi,
            $ID_pos,
            $keterangan_bank,
            $nominal_penerimaan,
            $nominal_pengeluaran
        ]);

        // redirect supaya form tidak submit ulang (PRG)
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }$stmt = $pdo->query("
    SELECT kh.*, p.kode, p.akun
    FROM `3_kas_harian` kh
    LEFT JOIN `2_perkiraan` p ON kh.ID_pos = p.ID_pos
    ORDER BY kh.tgl_kas_harian ASC
");
$transaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Ambil data bank dengan join ke 2_perkiraan untuk kode & akun (hindari N+1 query)
    $banks = $pdo->query("
        SELECT b.*, p.kode AS kode_perkiraan, p.akun AS akun_perkiraan
        FROM `4_bank` b
        LEFT JOIN `2_perkiraan` p ON b.ID_pos = p.ID_pos
        ORDER BY b.tgl_transaksi ASC, b.ID_tabel_bank ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Ambil daftar POS dari 2_perkiraan (untuk dropdown di modal)
    $perkiraan = $pdo->query("SELECT ID_pos, kode, akun FROM `2_perkiraan` ORDER BY ID_pos ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Koneksi atau query gagal: " . $e->getMessage());
}
$total_in = 0;
$total_out = 0;
foreach ($banks as $b) {
    $total_in += $b['nominal_penerimaan'];
    $total_out += $b['nominal_pengeluaran'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>BANK</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
    #bankForm .form-group {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    #bankForm .form-group label {
        width: 180px; /* lebar label tetap */
        font-weight: 500;
        font-size: 14px;
        color: #004b8d;
        text-align: right; /* label rata kanan */
        display: block;
    }

    #bankForm .form-group input,
    #bankForm .form-group select {
        flex: 1;
        max-width: 250px;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
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
    /* ====== MODAL WRAPPER ====== */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }
    .modal.show {
        display: flex !important;
    }
    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 20px 50px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        position: relative;
    }

    /* ====== HEADER ====== */
    .modal-header {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        border-bottom: 2px solid #eee;
        padding-bottom: 8px;
    }
    .modal-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
    }
    .btn-close {
        background: none;
        border: none;
        font-size: 1.2rem;
        font-weight: bold;
        color: #888;
        cursor: pointer;
    }
    .btn-close:hover {
        color: #e74c3c;
    }

    /* ====== STEP 1 ====== */
    #step1 p {
        margin-bottom: 10px;
        font-weight: 500;
    }
    #step1 .d-flex {
        display: flex;
        gap: 10px;
    }
    #btnIncome,
    #btnExpense {
        font-family: 'Poppins', sans-serif;
        flex: 1;
        padding: 10px;
        font-weight: 600;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.25s;
    }
    #btnIncome {
        background-color: #007bff; /* biru */
    }
    #btnExpense {
        background-color: #0056b3; /* biru tua */
    }
    #btnIncome:hover {
        background-color: #f1c40f; /* kuning */
        color: #000;
    }
    #btnExpense:hover {
        background-color: #e74c3c; /* merah */
    }

    /* ====== STEP 2 FORM ====== */
    #step2 form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #step2 .mb-2 {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    #step2 label {
        width: 40%;
        font-weight: 500;
        color: #333;
    }

    #step2 input,
    #step2 select,
    #step2 textarea {
        width: 60%;
        padding: 6px 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 0.95rem;
    }

    #step2 textarea {
        resize: vertical;
    }

    #step2 .form-text {
        font-size: 0.8rem;
        color: #777;
        margin-left: 40%;
        width: 60%;
    }

    /* ====== BUTTON GROUP (Kembali & Simpan) ====== */
    #step2 .d-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        gap: 10px; /* jarak antar tombol */
    }

    #backBtn,
    #step2 .btn-primary {
        flex: 1;
        height: 40px;
        font-size: 0.95rem;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.25s;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 0.2px;
    }

    /* Kembali = merah */
    #backBtn {
        background-color: #e74c3c;
    }
    #backBtn:hover {
        background-color: #c0392b;
    }

    /* Simpan = kuning */
   /* === STEP 2 BUTTONS (Bootstrap version styled to match your theme) === */
    #step2 .btn {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 15px;
        border-radius: 30px;
        padding: 12px 40px;
        min-width: 150px; /* 🔹 Samain lebar tombol */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    /* Tombol Kembali */
    #step2 .btn-secondary {
        background: linear-gradient(to right, #e74c3c, #f09292ff);
        color: white;
        border: 1px solid #bcdfff;
    }
    #step2 .btn-secondary:hover {
        background-color: #e74c3c;
        transform: translateY(-2px);
    }

    /* Tombol Simpan */
    #step2 .btn-primary {
        background: linear-gradient(to right, #f1c40f, #ecdd9eff);
        border: none;
        color: white;
    }
    #step2 .btn-primary:hover {
        background-color: #f1c40f;
        transform: translateY(-2px);
    }
    .row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    }
    .flex-col {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
    }
    .row label {
        min-width: 140px;
        font-weight: bold;
    }

    .row input,
    .row select {
        padding: 6px;
        border: 1px solid #ddd;
        border-radius: 6px;
    }
    .half {
        flex: 1;
    }
    .btn-delete{
        font-family: 'Poppins', sans-serif;
        padding: 3px 6px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
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
            <a href="anggaran_eco.php">Data</a>
            <a href="anggaran_eco_perkiraan.php">Perkiraan</a>
            <a href="anggaran_eco_kas.php">Kas Harian</a>
            <a href="anggaran_eco_bank.php" class="active">Bank</a>
            <a href="anggaran_eco_bruder.php">Bruder</a>
            <a href="anggaran_eco_lu.php">LU Komunitas</a>
            <a href="anggaran_eco_evaluasi.php">Evaluasi</a>
            <a href="anggaran_eco_opname.php">Kas Opname</a>
        </div>
        <div class="main">
            <main>
                <h1>KOMUNITAS FIC CANDI<br>LAPORAN BANK HARIAN<br>BULAN JANUARI 2025</h1>
                <div class="card">
                    <form id="bankForm" method="POST" action="simpan_bank.php">
                        <div class="form-group">
                            <label for="nama_bank">Nama Bank:</label>
                            <input type="text" name="nama_bank" id="nama_bank" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="no_rek_bank">No Rekening:</label>
                            <input type="text" name="no_rek_bank" id="no_rek_bank" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="atas_nama_bank">Atas Nama:</label>
                            <input type="text" name="atas_nama_bank" id="atas_nama_bank" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="nominal_penerimaan">Nominal Saldo Awal:</label>
                            <input type="number" step="0.01" name="nominal_penerimaan" id="nominal_penerimaan" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="tgl_transaksi">Tanggal Transaksi:</label>
                            <input type="date" name="tgl_transaksi" id="tgl_transaksi" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="table-header">
                            <button type="submit" class="btn-simpan">Simpan</button>
                        </div>
                    </form>
                    <form method="post">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:60px">Tgl</th>
                                    <th style="width:80px">Pos</th>
                                    <th>Kode Perkiraan</th>
                                    <th>Akun</th>
                                    <th>Keterangan</th>
                                    <th>Penerimaan</th>
                                    <th>Pengeluaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tbody id="tableBody">
                        <!-- SALDO AWAL -->
                        <tr>
                            <td></td>
                            <td style="text-align:left; font-weight:bold;">Saldo Awal</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"><?= number_format($total_in, 2, ',', '.') ?></td>
                            <td align="right"><?= number_format($total_out, 2, ',', '.') ?></td>
                            <td></td>
                        </tr>
                        <!-- LOOP DATA BANK -->
                        <?php if (!empty($banks)): ?>
                            <?php foreach ($banks as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d/m/Y', strtotime($b['tgl_transaksi']))) ?></td>
                                <td><?= htmlspecialchars($b['ID_pos']) ?></td>
                                <td><?= htmlspecialchars($b['kode_perkiraan']) ?></td>
                                <td><?= htmlspecialchars($b['akun_perkiraan']) ?></td>
                                <td><?= htmlspecialchars($b['keterangan_bank']) ?></td>
                                <td align="right"><?= number_format($b['nominal_penerimaan'], 2, ',', '.') ?></td>
                                <td align="right"><?= number_format($b['nominal_pengeluaran'], 2, ',', '.') ?></td>
                                <td>
                                    <button 
                        onclick="if (confirm('Yakin ingin hapus transaksi ini?')) { 
                            window.location.href='hapus_bank.php?id=<?= $b['ID_tabel_bank'] ?>'; 
                        }"
                        class="btn-delete">
                        Hapus
                            </button>
                        </td>
                            </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                <td colspan="8" align="center">Belum ada data</td>
                                </tr>
                                    <?php endif; ?>

                                    <!-- BARIS TAMBAH + -->
                                    <tr id="fixedRow">
                                    <td>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">+</button>
                                </td>
                            <td colspan="7"></td>
                        </tr>
                        </tbody>                                                    
                            </tbody>
                            <tfoot>
                                <tr>
                                <td colspan="5" style="text-align:center; font-weight:bold;">JUMLAH SEMUA</td>
                                <td><span id="total">0</span></td>
                                <td><span id="total">0</span></td>
                                <td></td>
                                </tr>
                            </tfoot>
                            <tfoot>
                                <tr>
                                <td colspan="5" style="text-align:center; font-weight:bold;">JUMLAH</td>
                                <td><span id="total">0</span></td>
                                <td><span id="total">0</span></td>
                                <td></td>
                                </tr>
                            </tfoot>
                            <tfoot>
                                <tr>
                                <td colspan="5" style="text-align:center; font-weight:bold;">SALDO</td>
                                <td><span id="total">0</span></td>
                                <td><span id="total">0</span></td>
                                <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="addModalLabel">Tambah Transaksi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup">X</button>
                                </div>
                                <div class="modal-body">
                                <!-- STEP 1: pilih jenis -->
                                <div id="step1">
                                    <p>Pilih jenis transaksi:</p>
                                    <div class="d-flex gap-2">
                                    <button id="btnIncome" class="btn btn-warning flex-fill">Pemasukkan</button>
                                    <button id="btnExpense" class="btn btn-danger flex-fill">Pengeluaran</button>
                                    </div>
                                </div>

                                <!-- STEP 2: form transaksi (tersembunyi awalnya) -->
                                <div id="step2" style="display:none;">
                                    <form id="txForm" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                                    <!-- action flag -->
                                    <input type="hidden" name="action" value="save_transaction">
                                    <input type="hidden" name="type" id="txType" value="in"> <!-- in / out -->

                                    <div class="mb-2">
                                        <label class="form-label">Tanggal</label>
                                        <input type="date" name="tgl_transaksi" class="form-control" required value="<?= date('Y-m-d') ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Pilih POS</label>
                                        <select name="ID_pos" id="ID_pos_select" class="form-select" required>
                                        <option value="">-- Pilih POS --</option>
                                        <?php foreach ($perkiraan as $p): ?>
                                            <option value="<?= htmlspecialchars($p['ID_pos']) ?>" data-kode="<?= htmlspecialchars($p['kode']) ?>" data-akun="<?= htmlspecialchars($p['akun']) ?>">
                                            <?= htmlspecialchars($p['ID_pos']) ?> - <?= htmlspecialchars($p['kode']) ?> - <?= htmlspecialchars($p['akun']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Keterangan</label>
                                        <textarea name="keterangan_bank" class="form-control" rows="2" placeholder="Tulis keterangan..."></textarea>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Nominal</label>
                                        <input type="number" name="nominal" id="nominalInput" class="form-control" min="0" step="1" placeholder="Masukkan angka tanpa desimal" required>
                                    </div>
                                        <div class="d-flex justify-content-between">
                                            <button type="button" id="backBtn" class="btn btn-secondary">Kembali</button>
                                            <div>
                                            <button type="submit" id="btnSaveTx" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                    </form>
                                        </div> 
                                        </div>
                                        </div>
                                    </div>
                                </div>
                        <div class="row">
                            <div class="flex-col half">
                                <label>Pemimpin Komunitas:</label>
                                <input type="file" name="pemimpin_ttd" accept="image/*" onchange="previewImage(this, 'pemimpinPreview')"><br>
                                <img id="pemimpinPreview" class="preview-img" style="display:none"><br>
                                <input type="text" name="pemimpin_nama" placeholder="Nama Pemimpin">
                            </div>
                        <!-- Bendahara -->
                            <div class="flex-col half">
                                <label>Bendahara Komunitas:</label>
                                <input type="file" name="bendahara_ttd" accept="image/*" onchange="previewImage(this, 'bendaharaPreview')"><br>
                                <img id="bendaharaPreview" class="preview-img" style="display:none"><br>
                                <input type="text" name="bendahara_nama" placeholder="Nama Bendahara">
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

  const txForm = document.getElementById('txForm');
  const tableBody = document.getElementById('tableBody');
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  const txType = document.getElementById('txType');
  const btnIncome = document.getElementById('btnIncome');
  const btnExpense = document.getElementById('btnExpense');
  const backBtn = document.getElementById('backBtn');
  const addModalEl = document.getElementById('addModal');
  const addModal = new bootstrap.Modal(addModalEl, { keyboard: true });

  // === SUBMIT FORM ===
  txForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const res = await fetch('', { method: 'POST', body: formData });

    if (res.ok) {
      const tgl = formData.get('tgl_transaksi');
      const pos = document.querySelector('#ID_pos_select option:checked').textContent;
      const ket = formData.get('keterangan_bank');
      const nominal = parseFloat(formData.get('nominal')) || 0;
      const type = formData.get('type');

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${tgl}</td>
        <td>${pos.split(' - ')[0]}</td>
        <td>${pos.split(' - ')[1]}</td>
        <td>${pos.split(' - ')[2]}</td>
        <td>${ket}</td>
        <td>${type === 'in' ? nominal.toLocaleString() : ''}</td>
        <td>${type === 'out' ? nominal.toLocaleString() : ''}</td>
        <td><button class="btn btn-danger btn-sm btn-delete">Hapus</button></td>
      `;
      tableBody.appendChild(tr);

      updateTotals();
      addModal.hide();
      txForm.reset();
      showStep(1);
    }
  });

  // === PERHITUNGAN TOTAL DAN SALDO ===
  function updateTotals() {
    let totalIn = 0, totalOut = 0;

    tableBody.querySelectorAll('tr').forEach(row => {
      if (row.id === 'fixedRow') return;
      const inVal = parseFloat(row.cells[5]?.textContent.replace(/,/g, '')) || 0;
      const outVal = parseFloat(row.cells[6]?.textContent.replace(/,/g, '')) || 0;
      totalIn += inVal;
      totalOut += outVal;
    });

    const saldoAwalIn = parseFloat(document.getElementById('saldoAwalIn')?.textContent.replace(/,/g, '')) || 0;
    const saldoAwalOut = parseFloat(document.getElementById('saldoAwalOut')?.textContent.replace(/,/g, '')) || 0;

    const [totalInEl, totalOutEl] = document.querySelectorAll('tfoot tr:nth-child(1) td span');
    const [saldoInEl, saldoOutEl] = document.querySelectorAll('tfoot tr:nth-child(2) td span');
    const [akhirInEl, akhirOutEl] = document.querySelectorAll('tfoot tr:nth-child(3) td span');

    totalInEl.textContent = totalIn.toLocaleString();
    totalOutEl.textContent = totalOut.toLocaleString();

    const saldoIn = totalIn + saldoAwalIn;
    const saldoOut = totalOut + saldoAwalOut;

    saldoInEl.textContent = saldoIn.toLocaleString();
    saldoOutEl.textContent = saldoOut.toLocaleString();

    akhirInEl.textContent = (saldoIn - saldoOut).toLocaleString();
    akhirOutEl.textContent = '';
  }

  // === DOUBLE CLICK EDIT ===
  tableBody.addEventListener('dblclick', e => {
    const cell = e.target;
    if (cell.tagName !== 'TD' || cell.cellIndex < 4 || cell.cellIndex > 7) return;

    const oldVal = cell.textContent.trim();
    const input = document.createElement('input');
    input.type = 'text';
    input.value = oldVal;
    input.style.width = '100%';
    cell.textContent = '';
    cell.appendChild(input);
    input.focus();

    input.addEventListener('blur', () => {
      cell.textContent = input.value;
      updateTotals();
    });
  });

  // === HANDLE STEP BUTTONS ===
  btnIncome.addEventListener('click', () => {
    txType.value = 'in';
    showStep(2, 'in');
  });

  btnExpense.addEventListener('click', () => {
    txType.value = 'out';
    showStep(2, 'out');
  });

  backBtn.addEventListener('click', () => showStep(1));

  function showStep(step, type = null) {
    if (step === 1) {
      step2.style.display = 'none';
      step1.style.display = 'block';
    } else {
      step1.style.display = 'none';
      step2.style.display = 'block';
      const submitBtn = txForm.querySelector('button[type="submit"]');
      submitBtn.className = `btn ${type === 'in' ? 'btn-primary' : 'btn-danger'}`;
      submitBtn.className = `btn ${type === 'out' ? 'btn-primary' : 'btn-danger'}`;
    }
  }

  // === RESET MODAL SAAT DITUTUP ===
  addModalEl.addEventListener('hidden.bs.modal', () => {
    txForm.reset();
    showStep(1);
  });

  // === CEGAH ENTER SUBMIT OTOMATIS ===
  document.querySelectorAll('form').forEach(f => {
    f.addEventListener('keydown', e => {
      if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        e.target.blur();
      }
    });
  });

  // === HAPUS BARIS ===
  document.addEventListener('click', e => {
    if (e.target.classList.contains('btn-delete')) {
      const row = e.target.closest('tr');
      if (row.id === 'fixedRow') return alert('Baris ini tidak bisa dihapus!');
      if (confirm('Yakin mau hapus baris ini?')) {
        row.remove();
        updateTotals();
      }
    }
  });

});
</script>
</body>
</html>
