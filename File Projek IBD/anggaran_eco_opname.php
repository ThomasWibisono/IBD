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
    $stmt = $pdo->prepare("
        SELECT db.foto
        FROM login_bruder lb
        LEFT JOIN data_bruder db ON lb.ID_bruder = db.ID_bruder
        WHERE lb.ID_bruder = ?
    ");
    $stmt->execute([$_SESSION['ID_bruder']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $foto = !empty($user['foto']) ? $user['foto'] : 'default.png';
} catch (PDOException $e) {
    die("Koneksi atau query gagal: " . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hari       = $_POST['hari'];
    $tanggal    = $_POST['tanggal'];
    $waktu      = $_POST['waktu'];
    $tempat     = $_POST['tempat'];
    $lokasi     = $_POST['lokasi'];
    $saldo_catatan  = $_POST['saldo_catatan'];
    $kas_kecil      = $_POST['kas_kecil'];
    $saldo_bendahara= $_POST['saldo_bendahara'];
    $jumlah_hasil   = $_POST['jumlah_hasil'];
    $selisih        = $_POST['selisih'];

    $pemimpin_nama  = $_POST['pemimpin_nama'] ?? '';
    $bendahara_nama = $_POST['bendahara_nama'] ?? '';

    // Upload tanda tangan
    $pemimpin_file = null;
    $bendahara_file = null;

    $uploadDir = "uploads/";

    if (!empty($_FILES['pemimpin_ttd']['name'])) {
        $pemimpin_file = time() . "_pemimpin_" . basename($_FILES['pemimpin_ttd']['name']);
        move_uploaded_file($_FILES['pemimpin_ttd']['tmp_name'], $uploadDir . $pemimpin_file);
    }
    if (!empty($_FILES['bendahara_ttd']['name'])) {
        $bendahara_file = time() . "_bendahara_" . basename($_FILES['bendahara_ttd']['name']);
        move_uploaded_file($_FILES['bendahara_ttd']['tmp_name'], $uploadDir . $bendahara_file);
    }

    // Simpan ke tabel 8_kas_opname
    $stmt = $pdo->prepare("
        INSERT INTO `8_kas_opname`
        (hari, tanggal, waktu, tempat, lokasi, saldo_catatan, kas_kecil, saldo_bendahara, jumlah_hasil, selisih, pemimpin_nama, pemimpin_ttd, bendahara_nama, bendahara_ttd)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");
    $stmt->execute([
        $hari, $tanggal, $waktu, $tempat, $lokasi, $saldo_catatan, $kas_kecil, $saldo_bendahara, $jumlah_hasil, $selisih,
        $pemimpin_nama, $pemimpin_file, $bendahara_nama, $bendahara_file
    ]);

    echo "<script>alert('Data berhasil disimpan ke kas opname');</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kas Opname</title>
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
            <a href="anggaran_eco_kas.php">Kas Harian</a>
            <a href="anggaran_eco_bank.php">Bank</a>
            <a href="anggaran_eco_bruder.php">Bruder</a>
            <a href="anggaran_eco_lu.php">LU Komunitas</a>
            <a href="anggaran_eco_evaluasi.php">Evaluasi</a>
            <a href="anggaran_eco_opname.php" class="active">Kas Opname</a>
        </div>
        <div class="main">
            <main>
                <h1>BERITA ACARA PERHITUNGAN KAS</h1>
                <div class="card">
                    <form method="post" enctype="multipart/form-data">
                        <!-- Hari Tanggal Waktu -->
                        <div class="row">
                            <label>Hari</label>
                            <select name="hari"><option>Senin</option><option>Selasa</option><option>Rabu</option>
                                <option>Kamis</option><option>Jumat</option><option>Sabtu</option><option>Minggu</option></select>
                            <label>Tanggal</label>
                            <input type="date" name="tanggal">
                            <label>Waktu</label>
                            <input type="time" name="waktu">
                        </div>

                        <p>Telah diadakan perhitungan saldo kas harian milik Bruderan FIC 
                        <select name="tempat"><option>Candi</option><option>Jakarta</option><option>Semarang</option></select>
                        </p>
                        <p>
                        yang terletak di 
                        <input type="text" name="lokasi" style="width:40%;">
                        </p>

                        <!-- Saldo Kas -->
                        <div class="row" style="display: flex; justify-content: center; gap: 60px; margin-top: 10px; flex-wrap: wrap;">

                        <!-- Kolom kiri -->
                        <div style="display: flex; flex-direction: column; text-align: left;">
                            <label>Saldo Kas Menurut Catatan (Rp)</label>
                            <input type="number" id="saldo_catatan" name="saldo_catatan" style="width: 220px; margin-bottom: 10px;">
                            
                            <label>Kas Kecil (Rp)</label>
                            <input type="number" id="kas_kecil" name="kas_kecil" style="width: 220px;">
                        </div>

                        <!-- Kolom kanan -->
                        <div style="display: flex; flex-direction: column; text-align: left;">
                            <label>Saldo Kas di Bendahara (Rp)</label>
                            <input type="text" id="saldo_bendahara" name="saldo_bendahara" readonly style="width: 220px;">
                        </div>

                        </div>
                        <!-- Uang Kertas -->
                        <h4>Uang Kertas</h4>
                        <table>
                            <tr><th>Nominal</th><th>Jumlah</th><th>Subtotal</th></tr>
                            <?php foreach([100000,50000,20000,10000,5000,2000,1000] as $d): ?>
                            <tr>
                                <td>Rp <?=number_format($d,0,',','.')?></td>
                                <td><input type="number" min="0" value="0" class="kqty" data-denom="<?=$d?>"></td>
                                <td class="ktot">0</td>
                            </tr>
                            <?php endforeach;?>
                            <tr><th colspan="2">Total Kertas</th><th id="total_kertas">0</th></tr>
                        </table>

                        <!-- Uang Logam -->
                        <h4>Uang Logam</h4>
                        <table>
                            <tr><th>Nominal</th><th>Jumlah</th><th>Subtotal</th></tr>
                            <?php foreach([1000,500,200,100] as $d): ?>
                            <tr>
                                <td>Rp <?=number_format($d,0,',','.')?></td>
                                <td><input type="number" min="0" value="0" class="lqty" data-denom="<?=$d?>"></td>
                                <td class="ltot">0</td>
                            </tr>
                            <?php endforeach;?>
                            <tr><th colspan="2">Total Logam</th><th id="total_logam">0</th></tr>
                        </table>

                        <!-- Jumlah Hasil & Selisih -->
                        <div class="row">
                            <div class="flex-col half">
                                <label>Jumlah Hasil Perhitungan (Rp)</label>
                                <input type="text" id="jumlah_hasil" name="jumlah_hasil" readonly>
                                <label>Selisih (Rp)</label>
                                <input type="text" id="selisih" name="selisih" readonly>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 40px; width: 100%;">
                            
                            <!-- Pemimpin Komunitas (pojok kiri) -->
                            <div style="width: 45%; text-align: left;">
                                <label style="font-weight: bold;">Pemimpin Komunitas:</label><br>
                                <input type="file" name="pemimpin_ttd" accept="image/*" onchange="previewImage(this, 'pemimpinPreview')"><br>
                                <img id="pemimpinPreview" style="display:none; margin-top: 8px; width: 120px; border: 1px solid #ccc; border-radius: 8px;"><br>
                                <input type="text" name="pemimpin_nama" placeholder="Nama Pemimpin" style="width: 100%; max-width: 260px; margin-top: 6px;">
                            </div>

                            <!-- Bendahara Komunitas (pojok kanan tapi teks tetap rata kiri) -->
                            <div style="width: 45%; text-align: left; margin-left: auto;">
                                <div style="margin-left: auto; width: fit-content;">
                                    <label style="font-weight: bold;">Bendahara Komunitas:</label><br>
                                    <input type="file" name="bendahara_ttd" accept="image/*" onchange="previewImage(this, 'bendaharaPreview')"><br>
                                    <img id="bendaharaPreview" style="display:none; margin-top: 8px; width: 120px; border: 1px solid #ccc; border-radius: 8px;"><br>
                                    <input type="text" name="bendahara_nama" placeholder="Nama Bendahara" style="width: 100%; max-width: 260px; margin-top: 6px;">
                                </div>
                            </div>

                        </div>
                        <div style="text-align:center;margin-top:20px">
                            <button type="submit" class="btn">Simpan</button>
                        </div>
                    </form>
                </div>
            </main>
<footer>
    © <?= date('Y') ?> Komunitas Bruder FIC — All Rights Reserved.
</footer>
        </div>
    </div>
<script>
    function toggleDropdown(){
        let m=document.getElementById("dropdownMenu");
        m.style.display=(m.style.display==="block")?"none":"block";
    }
    window.onclick=function(e){
        if(!e.target.closest('.profile-wrapper')){
            document.getElementById("dropdownMenu").style.display="none";
        }
    }
    function fmt(n){return new Intl.NumberFormat('id-ID').format(n);}
    function calcAll(){
        let s=parseFloat(document.getElementById('saldo_catatan').value)||0;
        let k=parseFloat(document.getElementById('kas_kecil').value)||0;
        let sb=s+k;document.getElementById('saldo_bendahara').value=fmt(sb);
        let total_k=0;document.querySelectorAll('.kqty').forEach(el=>{
            let d=parseInt(el.dataset.denom);let q=parseInt(el.value)||0;let sub=d*q;
            el.closest('tr').querySelector('.ktot').innerText=fmt(sub);total_k+=sub;});
        document.getElementById('total_kertas').innerText=fmt(total_k);
        let total_l=0;document.querySelectorAll('.lqty').forEach(el=>{
            let d=parseInt(el.dataset.denom);let q=parseInt(el.value)||0;let sub=d*q;
            el.closest('tr').querySelector('.ltot').innerText=fmt(sub);total_l+=sub;});
        document.getElementById('total_logam').innerText=fmt(total_l);
        let jumlah=total_k+total_l;document.getElementById('jumlah_hasil').value=fmt(jumlah);
        document.getElementById('selisih').value=fmt(sb-jumlah);
    }
    document.getElementById('saldo_catatan').addEventListener('input',calcAll);
    document.getElementById('kas_kecil').addEventListener('input',calcAll);
    document.querySelectorAll('.kqty,.lqty').forEach(i=>i.addEventListener('input',calcAll));
    function toggleDropdown() {
        let menu = document.getElementById("dropdownMenu");
        menu.style.display = (menu.style.display === "block") ? "none" : "block";
    }
    window.onclick = function(event) {
        if (!event.target.closest('.profile-wrapper')) {
            document.getElementById("dropdownMenu").style.display = "none";
        }
    }


    function previewImage(input, previewId) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.getElementById(previewId);
                img.src = e.target.result;
                img.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    }
</script>
</body>
</html>
