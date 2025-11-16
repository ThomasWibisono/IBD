
<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ambil daftar bruder untuk dropdown
    $data_bruder = $pdo->query("SELECT ID_bruder, nama_bruder FROM data_bruder ORDER BY nama_bruder")->fetchAll(PDO::FETCH_ASSOC);

    // ambil foto login
    $stmt = $pdo->prepare("
        SELECT db.foto
        FROM login_bruder lb
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $foto = !empty($user['foto']) ? $user['foto'] : 'default.png';

    // simpan form
    if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "save_bruder") {
        $stmt = $pdo->prepare("INSERT INTO `5_bruder`
            (ID_bruder, tgl_datang_komunitas, tgl_pulang_komunitas, tgl_pergi_luarkota, tgl_pulang_luarKota, jumlah_hari, keterangan_pp)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['ID_bruder'],
            $_POST['tgl_datang_komunitas'] ?: null,
            $_POST['tgl_pulang_komunitas'] ?: null,
            $_POST['tgl_pergi_luarkota'] ?: null,
            $_POST['tgl_pulang_luarKota'] ?: null,
            $_POST['jumlah_hari'] ?: null,
            $_POST['keterangan_pp'] ?: null
        ]);
    }
    // ambil data dari tabel 5_bruder + nama dari data_bruder
$stmt = $pdo->query("
    SELECT 
        b.ID_pp,
        db.nama_bruder,
        b.tgl_datang_komunitas,
        b.tgl_pulang_komunitas,
        b.tgl_pergi_luarkota,
        b.tgl_pulang_luarKota,
        b.jumlah_hari,
        b.keterangan_pp
    FROM `5_bruder` b
    LEFT JOIN data_bruder db ON b.ID_bruder = db.ID_bruder
    ORDER BY b.ID_pp DESC
");
$data_bruder_tabel = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi atau query gagal: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>BRUDER</title>
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
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
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
      /* 🔹 Menghapus underline */
      padding: 10px 22px;
      border-radius: 20px;
      transition: all 0.3s ease;
    }

    nav a.active {
      background: white;
      color: #0077ff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    nav a:hover {
      background: rgba(255, 255, 255, 0.8);
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
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .dropdown {
      position: absolute;
      top: 65px;
      right: 0;
      background: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      border-radius: 10px;
      display: none;
      flex-direction: column;
      min-width: 180px;
    }

    .dropdown a {
      padding: 12px 20px;
      color: #333;
      text-decoration: none;
      /* 🔹 Menghapus underline */
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
        text-align: left;
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
    .custom-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: transparent;
        border: none;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10;
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
            <a href="dashboard_eco.php">Home</a>
            <a href="anggota.php">Anggota</a>
            <a href="anggaran_eco.php" class="active">Anggaran</a>

        </nav>
        <div class="profile-wrapper" onclick="toggleDropdown()">
            <img src="foto/<?= htmlspecialchars($foto) ?>" alt="Profile" class="profile-pic">
            <div class="dropdown" id="dropdownMenu">
            <a href="editprofile.php">Edit Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="sidebar">
            <a href="anggaran_eco.php">Data</a>
            <a href="anggaran_eco_perkiraan.php">Perkiraan</a>
            <a href="anggaran_eco_kas.php">Kas Harian</a>
            <a href="anggaran_eco_bank.php">Bank</a>
            <a href="anggaran_eco_bruder.php" class="active">Bruder</a>
            <a href="anggaran_eco_lu.php">LU Komunitas</a>
            <a href="anggaran_eco_evaluasi.php">Evaluasi</a>
            <a href="anggaran_eco_opname.php">Kas Opname</a>
        </div>
        <div class="main">
            <main>
                <h1>KOMUNITAS FIC CANDI<br>PERUBAHAN JUMLAH BRUDER<br>BULAN JANUARI 2025</h1>
                <div class="card">
                    <form id="bankForm" method="post">
                    <div class="form-group">
                        <label>Pemimpin Lokal:</label>
                        <input type="text" name="nama_pemimpinlokal" required>
                    </div>
                    <div class="form-group">
                        <label>Bendahara Komunitas:</label>
                        <input type="text" name="nama_bendaharakomunitas" required>
                    </div>
                    <div class="form-group">
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
                        <table cellpadding="5" cellspacing="0">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Nama</th>
                                <th colspan="2">Tgl Penambahan</th>
                                <th colspan="2">Tgl Pengurangan</th>
                                <th rowspan="2">Jumlah</th>
                                <th rowspan="2">Keterangan</th>
                                <th rowspan="2">Aksi</th>
                            </tr>
                            <tr>
                                <th>Datang</th>
                                <th>Pergi</th>
                                <th>Pergi</th>
                                <th>Pulang</th>
                            </tr>
                        </thead>
                        <tbody id="bruderTableBody">
                            <?php if (!empty($data_bruder_tabel)): ?>
                                <?php foreach ($data_bruder_tabel as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($row['nama_bruder']) ?></td>
                                    <td><?= $row['tgl_datang_komunitas'] ?: '-' ?></td>
                                    <td><?= $row['tgl_pulang_komunitas'] ?: '-' ?></td>
                                    <td><?= $row['tgl_pergi_luarkota'] ?: '-' ?></td>
                                    <td><?= $row['tgl_pulang_luarKota'] ?: '-' ?></td>
                                    <td><?= $row['jumlah_hari'] ?></td>
                                    <td><?= htmlspecialchars($row['keterangan_pp']) ?></td>
                                    <td>
                                     <button class="btn-delete" data-id="<?= $row['ID_pp'] ?>">Hapus</button>
                                    </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9">Belum ada data Bruder</td></tr>
                        <?php endif; ?>

                        <!-- Baris tambah data -->
                        <tr id="addRow">
                            <td><button class="btn-plus" data-bs-toggle="modal" data-bs-target="#addModal">+</button></td>
                            <td></td>
                            <td colspan="6"></td>
                            <td><button class="btn-delete">Hapus</button></td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Tutup">X</button>
                            <div class="modal-header">
                                <h5 class="modal-title" id="addModalLabel">Tambah Data Bruder</h5>
                            </div>
                            <div class="modal-body">
                                <!-- STEP 1 -->
                                <div id="step1">
                                <p>Pilih jenis transaksi:</p>
                                    <div class="d-flex gap-2">
                                        <button id="btnIncome" class="btn btn-warning flex-fill">Tanggal Penambahan</button>
                                        <button id="btnExpense" class="flex-fill">Tanggal Pengurangan</button>
                                    </div>
                                </div>

                                <!-- STEP 2 -->
                                <div id="step2" style="display:none;">
                                <form id="bruderForm" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                                    <input type="hidden" name="action" value="save_bruder">
                                    <input type="hidden" name="type" id="txType" value="">

                                    <div class="mb-2">
                                        <label class="form-label">Nama Bruder</label>
                                        <select name="ID_bruder" id="ID_bruder_select" class="form-select" required>
                                            <option value="">-- Pilih Bruder --</option>
                                            <?php foreach ($data_bruder as $b): ?>
                                            <option value="<?= htmlspecialchars($b['ID_bruder']) ?>">
                                                <?= htmlspecialchars($b['nama_bruder']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Tanggal Datang Komunitas</label>
                                        <input type="date" name="tgl_datang_komunitas" id="tgl_datang_komunitas" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Tanggal Pulang Komunitas</label>
                                        <input type="date" name="tgl_pulang_komunitas" id="tgl_pulang_komunitas" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Tanggal Pergi Luar Kota</label>
                                        <input type="date" name="tgl_pergi_luarkota" id="tgl_pergi_luarkota" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Tanggal Pulang Luar Kota</label>
                                        <input type="date" name="tgl_pulang_luarKota" id="tgl_pulang_luarKota" class="form-control">
                                    </div>

                                    <div class="mb-2">
                                    <label class="form-label">Jumlah Hari</label>
                                    <input type="number" name="jumlah_hari" id="jumlah_hari" class="form-control" readonly>
                                    </div>

                                    <div class="mb-2">
                                    <label class="form-label">Keterangan</label>
                                    <textarea name="keterangan_pp" class="form-control" rows="2" placeholder="Tulis keterangan..."></textarea>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                    <button type="button" id="backBtn" class="btn btn-secondary">Kembali</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                                </div>

                            </div>
                            </div> <!-- tutup .modal-content -->
                        </div> <!-- tutup .modal-dialog -->
                    </div> <!-- tutup .modal -->
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
            </main>
<footer>
    © <?= date('Y') ?> Komunitas Bruder FIC — All Rights Reserved.
</footer>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const step1 = document.getElementById('step1');
const step2 = document.getElementById('step2');
const btnIncome = document.getElementById('btnIncome');
const btnExpense = document.getElementById('btnExpense');
const txType = document.getElementById('txType');
const backBtn = document.getElementById('backBtn');
const addModal = new bootstrap.Modal(document.getElementById('addModal'), { keyboard: true });

btnIncome.addEventListener('click', () => {
  txType.value = 'in';
  showStep2('in');
});

btnExpense.addEventListener('click', () => {
  txType.value = 'out';
  showStep2('out');
});

function showStep2(type) {
  step1.style.display = 'none';
  step2.style.display = 'block';
  const submitBtn = document.querySelector('#bruderForm button[type="submit"]');
  if (submitBtn) {
    submitBtn.classList.remove('btn-primary', '');
    submitBtn.classList.add(type === 'in' ? 'btn-primary' : '');
  }

  // 🔽 Sembunyikan/tampilkan field sesuai jenis transaksi
  const datang = document.querySelector('.mb-2 [name="tgl_datang_komunitas"]').closest('.mb-2');
  const pulangKom = document.querySelector('.mb-2 [name="tgl_pulang_komunitas"]').closest('.mb-2');
  const pergi = document.querySelector('.mb-2 [name="tgl_pergi_luarkota"]').closest('.mb-2');
  const pulangLuar = document.querySelector('.mb-2 [name="tgl_pulang_luarKota"]').closest('.mb-2');

  if (type === 'in') {
    datang.style.display = 'flex';
    pulangKom.style.display = 'flex';
    pergi.style.display = 'none';
    pulangLuar.style.display = 'none';
  } else {
    datang.style.display = 'none';
    pulangKom.style.display = 'none';
    pergi.style.display = 'flex';
    pulangLuar.style.display = 'flex';
  }
}


backBtn.addEventListener('click', () => {
  step2.style.display = 'none';
  step1.style.display = 'block';
});

document.getElementById('addModal').addEventListener('hidden.bs.modal', function () {
  step2.style.display = 'none';
  step1.style.display = 'block';
  document.getElementById('bruderForm')?.reset();
  txType.value = '';
});

// ==================== Logika tanggal & jumlah hari ====================
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("addModal");
    const txType = document.getElementById("txType");
    const jumlah = document.getElementById("jumlah_hari");

    const datang = document.getElementById("tgl_datang_komunitas");
    const pulangKom = document.getElementById("tgl_pulang_komunitas");
    const pergi = document.getElementById("tgl_pergi_luarkota");
    const pulangLuar = document.getElementById("tgl_pulang_luarKota");

    function aturTampilanTanggal() {
        if (txType.value === "in") {
            // mode penambahan
            datang.parentElement.style.display = "flex";
            pulangKom.parentElement.style.display = "flex";
            pergi.parentElement.style.display = "none";
            pulangLuar.parentElement.style.display = "none";
        } else if (txType.value === "out") {
            // mode pengurangan
            datang.parentElement.style.display = "none";
            pulangKom.parentElement.style.display = "none";
            pergi.parentElement.style.display = "flex";
            pulangLuar.parentElement.style.display = "flex";
        }
        hitungSelisih(); // langsung hitung ulang saat mode berubah
    }

    function hitungSelisih() {
        let start = null;
        let end = null;

        if (txType.value === "in") {
            // Penambahan = datang - pulang komunitas
            if (datang.value && pulangKom.value) {
                start = new Date(datang.value);
                end = new Date(pulangKom.value);
            }
        } else if (txType.value === "out") {
            // Pengurangan = pergi luar kota - pulang luar kota
            if (pergi.value && pulangLuar.value) {
                start = new Date(pergi.value);
                end = new Date(pulangLuar.value);
            }
        }

        if (start && end) {
            const diffTime = end - start;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            jumlah.value = diffDays > 0 ? diffDays : 0;
        } else {
            jumlah.value = "";
        }
    }

    // hitung otomatis saat tanggal berubah
    [datang, pulangKom, pergi, pulangLuar].forEach(el => {
        if (el) el.addEventListener("change", hitungSelisih);
    });

    // ubah tampilan sesuai jenis transaksi saat tombol diklik
    document.getElementById("btnIncome").addEventListener("click", function() {
        txType.value = "in";
        aturTampilanTanggal();
    });
    document.getElementById("btnExpense").addEventListener("click", function() {
        txType.value = "out";
        aturTampilanTanggal();
    });

    // juga panggil saat modal muncul (misal openAdd diklik)
    modal.addEventListener("shown.bs.modal", aturTampilanTanggal);
});


document.getElementById("bruderForm").addEventListener("submit", function(e) {
  e.preventDefault(); // cegah reload

  const formData = new FormData(this);

  fetch("<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>", {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(response => {
    // ✅ Setelah sukses simpan ke DB, tampilkan di tabel
    const namaSelect = document.getElementById("ID_bruder_select");
    const nama = namaSelect.options[namaSelect.selectedIndex].text;
    const tglDatang = document.getElementById("tgl_datang_komunitas").value || "-";
    const tglPulangKom = document.getElementById("tgl_pulang_komunitas").value || "-";
    const tglPergi = document.getElementById("tgl_pergi_luarkota").value || "-";
    const tglPulangLuar = document.getElementById("tgl_pulang_luarKota").value || "-";
    const jumlah = document.getElementById("jumlah_hari").value || "-";
    const ket = document.querySelector("textarea[name='keterangan_pp']").value || "-";

    // hitung nomor baru
    const tbody = document.getElementById("bruderTableBody");
    const rowCount = tbody.querySelectorAll("tr").length;
    const no = rowCount; // minus baris tombol plus

    // buat baris baru
    const newRow = document.createElement("tr");
    newRow.innerHTML = `
    <td>${no}</td>
    <td>${nama}</td>
    <td>${tglDatang}</td>
    <td>${tglPulangKom}</td>
    <td>${tglPergi}</td>
    <td>${tglPulangLuar}</td>
    <td>${jumlah}</td>
    <td>${ket}</td>
    <td><button class="btn-delete btn btn-danger btn-sm">Hapus</button></td>
  `;

    // sisipkan sebelum tombol +
    const addRow = document.getElementById("addRow");
    tbody.insertBefore(newRow, addRow);

    // tambahkan event hapus untuk baris baru
newRow.querySelector(".btn-delete").addEventListener("click", function() {
  if (confirm("Yakin ingin menghapus baris ini (belum tersimpan)?")) {
    tbody.removeChild(newRow);
    Array.from(tbody.querySelectorAll("tr")).forEach((tr, idx) => {
      if (tr.id !== "addRow") tr.cells[0].textContent = idx + 1;
    });
  }
});
    // tutup modal
    const modalEl = document.getElementById("addModal");
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();

    // reset form
    e.target.reset();
    step1.style.display = 'block';
    step2.style.display = 'none';
  })
  .catch(err => console.error(err));
});


// ==================== Dropdown Profile ====================
function toggleDropdown() {
            let menu = document.getElementById("dropdownMenu");
            menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
        }
        window.onclick = function (event) {
        if (!event.target.closest('.profile-wrapper')) {
            document.getElementById("dropdownMenu").style.display = "none";
        }
        }
// ==================== Hapus Data Bruder ====================
function aktifkanTombolHapus() {
  const tombolHapus = document.querySelectorAll('.btn-delete');
  console.log("Tombol hapus ditemukan:", tombolHapus.length); // debug
  
  tombolHapus.forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      if (!id) return alert("⚠️ ID data tidak ditemukan!");

      if (confirm("Yakin ingin menghapus data ini?")) {
        fetch("hapus_bruder.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "id_pp=" + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === "success") {
            alert("✅ Data berhasil dihapus dan disimpan ke tabel riwayat!");
            location.reload();
          } else {
            alert("❌ " + data.message);
          }
        })
        .catch(err => alert("Terjadi kesalahan: " + err));
      }
    });
  });
}
// ✅ panggil fungsinya setelah halaman siap
document.addEventListener('DOMContentLoaded', aktifkanTombolHapus);


</script>
</body>
</html>
