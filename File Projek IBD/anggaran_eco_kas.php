<?php
session_start();
if (!isset($_SESSION['ID_bruder']) || $_SESSION['status'] !== 'econom') {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=ibd_kelompok6_brd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nama = $_SESSION['nama_bruder'];

    // ambil foto
    $stmt = $pdo->prepare("
        SELECT db.foto
        FROM login_bruder lb
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $foto = !empty($user['foto']) ? $user['foto'] : 'default.png';

    // simpan transaksi jika ada POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_transaction') {
        $type = $_POST['type']; // 'in' atau 'out'
        $tgl = $_POST['tgl_transaksi'];
        $ID_pos = $_POST['ID_pos'];
        $keterangan = $_POST['keterangan_bank'];
        $nominal = floatval($_POST['nominal']);

        if ($type === 'out') $nominal = -abs($nominal); // pengeluaran = negatif
        else $nominal = abs($nominal); // pemasukan = positif

        $stmt = $pdo->prepare("
            INSERT INTO `3_kas_harian` (tgl_kas_harian, ID_pos, keterangan_kas, ID_bruder, nominal)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$tgl, $ID_pos, $keterangan, $_SESSION['ID_bruder'], $nominal]);

        // redirect supaya refresh tabel otomatis
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    $stmt = $pdo->query("
    SELECT * FROM `3_kas_harian` 
    ORDER BY tgl_kas_harian ASC
");
$transaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total pemasukan dan pengeluaran
$stmt = $pdo->query("
    SELECT 
        SUM(CASE WHEN nominal >= 0 THEN nominal ELSE 0 END) AS total_masuk,
        SUM(CASE WHEN nominal < 0 THEN -nominal ELSE 0 END) AS total_keluar
    FROM `3_kas_harian`
");
$totals = $stmt->fetch(PDO::FETCH_ASSOC);

$total_masuk = $totals['total_masuk'] ?? 0;
$total_keluar = $totals['total_keluar'] ?? 0;

} catch (PDOException $e) {
    die("Koneksi atau query gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>KAS</title>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f4f4;
    }
    header {
        position: relative;     
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
    .sidebar {
        width: 220px;
        background: #d32f2f;
        color: white;
        min-height: 100vh;
        padding: 40px 0;
        flex-shrink: 0;
        margin-top: 10px;
        border-top-right-radius: 50px;
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
    .dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 60px;
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        border-radius: 8px;
        overflow: hidden;
        min-width: 260px;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
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
    main {
        margin-top: 10px;
        padding: 20px;
        text-align: center;
    }
    .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }
    h1, h2 {
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
        table-layout: fixed; 
        margin-top: 10px;
    }

    th, td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
        font-size: 14px;
        word-wrap: break-word; 
    }
    .container {
        display: flex;         
        min-height: 100vh;     
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
    .input-cell input,
    .input-cell select {
        padding: 2px;
        border: 1px solid #ddd;
        border-radius: 6px;
    }
    form label {
        text-align: left;
        display: block;
        font-weight: 500;
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
        padding: 20px 24px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        position: relative;
    }

    /* ====== HEADER ====== */
    .modal-header {
        display: flex;
        justify-content: space-between;
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
    #step2 .btn-primary {
        background-color: #f1c40f;
        color: #000;
    }
    #step2 .btn-primary:hover {
        background-color: #d4ac0d;
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
</style>
</head>
<body>
    <header>
        <img src="foto/logo.png" alt="Logo" class="logo">
        <nav>
            <a href="dashboard_eco.php">Home</a>
            <a href="anggota.php">Daftar Anggota</a>
            <a href="anggaran_eco.php" class="active">Anggaran</a>

        </nav>
        <div class="profile-wrapper" onclick="toggleDropdown()">
            <img src="foto/<?= htmlspecialchars($foto) ?>" alt="Profile" class="profile-pic">
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
            <a href="anggaran_eco_kas.php" class="active">Kas Harian</a>
            <a href="anggaran_eco_bank.php">Bank</a>
            <a href="anggaran_eco_bruder.php">Bruder</a>
            <a href="anggaran_eco_lu.php">LU Komunitas</a>
            <a href="anggaran_eco_evaluasi.php">Evaluasi</a>
            <a href="anggaran_eco_opname.php">Kas Opname</a>
        </div>
        <div class="main">
            <main>
                <h1>KOMUNITAS FIC CANDI<br>LAPORAN KAS HARIAN<br>BULAN JANUARI 2025</h1>
                    <div class="card">
                    <div class="table-header">
                        <button type="submit" class="btn-simpan">Simpan</button>
                    </div>
                    </form>

                        <table>
                            <thead>
                                <tr>
                                    <th style="width:60px">Tgl</th>
                                    <th style="width:80px">Pos</th>
                                    <th>Kode Perkiraan</th>
                                    <th>Akun</th>
                                    <th>Keterangan</th>
                                    <th>Reff</th>
                                    <th>Penerimaan</th>
                                    <th>Pengeluaran</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <button class="btn-plus" data-bs-toggle="modal" data-bs-target="#addModal">+</button>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php foreach ($transaksi as $t): ?>
                            <tr>
                                <td><?= htmlspecialchars($t['tgl_kas_harian']) ?></td>
                                <td><?= htmlspecialchars($t['ID_pos']) ?></td>
                                <td><?= htmlspecialchars($t['kode'] ?? '') ?></td> <!-- kode perkiraan -->
                                <td><?= htmlspecialchars($t['akun'] ?? '') ?></td> <!-- akun -->
                                <td><?= htmlspecialchars($t['keterangan_kas']) ?></td>
                                <td></td> <!-- Reff, kosong dulu -->
                                <td><?= $t['nominal'] >= 0 ? number_format($t['nominal'], 2, ',', '.') : '' ?></td>
                                <td><?= $t['nominal'] < 0 ? number_format(abs($t['nominal']), 2, ',', '.') : '' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="6" style="text-align:center; font-weight:bold;">JUMLAH SEMUA</td>
                            <td><?= number_format($total_masuk,2,',','.') ?></td>
                            <td><?= number_format($total_keluar,2,',','.') ?></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align:center; font-weight:bold;">JUMLAH</td>
                            <td><?= number_format($total_masuk,2,',','.') ?></td>
                            <td><?= number_format($total_keluar,2,',','.') ?></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align:center; font-weight:bold;">SALDO</td>
                            <td colspan="2"><?= number_format($total_masuk - $total_keluar,2,',','.') ?></td>
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

            </main>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// === HANDLE STEP BUTTONS ===
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
  const submitBtn = document.querySelector('#txForm button[type="submit"]');
  submitBtn.classList.remove('btn-primary', 'btn-danger');
  submitBtn.classList.add(type === 'in' ? 'btn-primary' : 'btn-danger');
}

backBtn.addEventListener('click', () => {
  step2.style.display = 'none';
  step1.style.display = 'block';
});

document.getElementById('addModal').addEventListener('hidden.bs.modal', function () {
  step2.style.display = 'none';
  step1.style.display = 'block';
  document.getElementById('txForm')?.reset();
  txType.value = 'in';
});

document.getElementById('openAdd').addEventListener('click', () => {
  step2.style.display = 'none';
  step1.style.display = 'block';
  document.getElementById('txForm')?.reset();
  txType.value = 'in';
});

// === DROPDOWN PROFILE HANDLER ===
function toggleDropdown() {
  let menu = document.getElementById("dropdownMenu");
  menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

window.onclick = function(event) {
  if (!event.target.closest('.profile-wrapper')) {
    document.getElementById("dropdownMenu").style.display = "none";
  }
};
// === CEGAH ENTER DI FORM AGAR TAK SUBMIT ===
document.querySelectorAll('form').forEach(f => {
  f.addEventListener('keydown', e => {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
      e.preventDefault();
      e.target.blur();
    }
  });
});
</script>
</body>
</html>