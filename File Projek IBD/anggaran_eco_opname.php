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
<style>
    html, body {
        height: 100%;
    }
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
    main {
        margin-top: 10px;
        padding: 20px;
        text-align: center;
    }
    .container {
        display: flex;         
        min-height: 100vh;     
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
    h1 {
        text-align: center;
        margin: 20px 0;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
    }

    table th,
    table td {
        border: 1px solid #eee;
        padding: 8px;
        text-align: center;
    }
    .btn {
        background: #1e90ff;
        color: white;
        padding: 8px 18px;
        border-radius: 20px;
        border: none;
        cursor: pointer;
    }
    .preview-img {
        max-width: 120px;
        max-height: 70px;
        border: 1px solid #ccc;
        padding: 4px;
        background: #fafafa;
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
            <a href="anggaran_eco_buku.php">Buku Besar</a>
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
                        <p>yang terletak di <input type="text" name="lokasi" style="width:70%"></p>

                        <!-- Saldo Kas -->
                        <div class="row">
                            <div class="flex-col half">
                                <label>Saldo Kas Menurut Catatan (Rp)</label>
                                <input type="number" id="saldo_catatan" name="saldo_catatan">
                                <label>Kas Kecil (Rp)</label>
                                <input type="number" id="kas_kecil" name="kas_kecil">
                            </div>
                            <div class="flex-col half">
                                <label>Saldo Kas di Bendahara (Rp)</label>
                                <input type="text" id="saldo_bendahara" name="saldo_bendahara" readonly>
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
                        <div style="text-align:center;margin-top:20px">
                            <button type="submit" class="btn">Simpan</button>
                        </div>
                    </form>
                </div>
            </main>
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
