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
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>EVALUASI</title>
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
            
    th:nth-child(1), td:nth-child(1) { width: 8%; }   
    th:nth-child(2), td:nth-child(2) { width: 12%; }  
    th:nth-child(3), td:nth-child(3) { width: 40%; }  
    th:nth-child(4), td:nth-child(4) { width: 20%; }  
    th:nth-child(5), td:nth-child(5) { width: 20%; }  
    table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed; /* kolom fix */
}

th, td {
    border: 1px solid #ccc;
    padding: 6px;
    text-align: center;
    font-size: 13px;
    word-wrap: break-word;
    vertical-align: middle;
}

/* Atur lebar kolom */
th:nth-child(1), td:nth-child(1) { width: 5%; }   /* Pos */
th:nth-child(2), td:nth-child(2) { width: 30%; text-align:left; } /* Nama Perkiraan */
th:nth-child(3), td:nth-child(3) { width: 15%; }  /* Realisasi Bulan ini */
th:nth-child(4), td:nth-child(4) { width: 15%; }  /* Realisasi Semua */
th:nth-child(5), td:nth-child(5) { width: 15%; }  /* Anggaran */
th:nth-child(6), td:nth-child(6) { width: 10%; }  /* Saldo */
th:nth-child(7), td:nth-child(7) { width: 10%; }  /* % */

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
            <a href="anggaran_eco_kas.php">Kas Harian</a>
            <a href="anggaran_eco_bank.php">Bank</a>
            <a href="anggaran_eco_bruder.php">Bruder</a>
            <a href="anggaran_eco_lu.php">LU Komunitas</a>
            <a href="anggaran_eco_evaluasi.php" class="active">Evaluasi</a>
            <a href="anggaran_eco_opname.php">Kas Opname</a>
        </div>
        <div class="main">
            <main>
                <h1>KOMUNITAS FIC CANDI<br>EVALUASI KEUANGAN<br>BULAN JANUARI 2025</h1>
                <div class="card">
<?php
    $id_anggaran = 1; 
    $bulan = 1; // Januari
    $tahun = 2025;

    // Ambil anggaran
    $anggaranStmt = $pdo->prepare("SELECT * FROM 1_data WHERE id_anggaran = ?");
    $anggaranStmt->execute([$id_anggaran]);
    $anggaran = $anggaranStmt->fetch(PDO::FETCH_ASSOC);

    // Ambil data realisasi per pos
    $stmt = $pdo->prepare("
        SELECT p.ID_pos, p.akun,
               COALESCE(SUM(CASE WHEN MONTH(lu.tgl_transaksi)=? AND YEAR(lu.tgl_transaksi)=? THEN lu.nominal_pemasukan - lu.nominal_pengeluaran END),0) AS realisasi_bulan,
               COALESCE(SUM(lu.nominal_pemasukan - lu.nominal_pengeluaran),0) AS realisasi_total
        FROM 2_perkiraan p
        LEFT JOIN 6_lu_komunitas lu ON p.ID_pos = lu.id_pos
        GROUP BY p.ID_pos, p.akun
        ORDER BY FIELD(p.ID_pos,'C','D','E','F','G','H','I',
                                  '1','2','3','4','5','6','7','8','9','10',
                                  '11','12','13','14','15','16','17','18','19','20',
                                  '21','22','23','24','25','26','27','28','29','30',
                                  '31','32','33','34','35','36','37','38')
    ");
    $stmt->execute([$bulan,$tahun]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung jumlah bruder
    $bruderCount = $pdo->query("SELECT COUNT(*) FROM data_bruder")->fetchColumn();

    // fungsi ambil anggaran dari kolom pos_XX
    function getAnggaran($id, $anggaran) {
        $col = "pos_".$id;
        return isset($anggaran[$col]) ? $anggaran[$col] : 0;
    }

    // pisahkan penerimaan & pengeluaran
    $penerimaan = array_filter($rows, fn($r) => in_array($r['ID_pos'], ['C','D','E','F','G','H','I']));
    $pengeluaran = array_filter($rows, fn($r) => !in_array($r['ID_pos'], ['C','D','E','F','G','H','I']));

    // total penerimaan & pengeluaran
    $total_penerimaan = $total_pengeluaran = 0;
?>

<table>
  <tr>
    <th>Pos</th>
    <th>Nama Perkiraan</th>
    <th>Realisasi Bulan ini</th>
    <th>Realisasi Semua</th>
    <th>Anggaran</th>
    <th>Saldo</th>
    <th>%</th>
  </tr>

  <!-- Bagian Penerimaan -->
  <?php foreach($penerimaan as $r): 
        $id = $r['ID_pos'];
        $realisasi_bulan = $r['realisasi_bulan'];
        $realisasi_total = $r['realisasi_total'];
        $ang = getAnggaran($id, $anggaran);
        $saldo = $realisasi_total - $ang;
        $persen = $ang > 0 ? round(($realisasi_total / $ang) * 100,2) : 0;
        $total_penerimaan += $realisasi_total;
  ?>
  <tr>
    <td><?= $id ?></td>
    <td style="text-align:left;"><?= $r['akun'] ?></td>
    <td><?= number_format($realisasi_bulan,0,',','.') ?></td>
    <td><?= number_format($realisasi_total,0,',','.') ?></td>
    <td><?= number_format($ang,0,',','.') ?></td>
    <td><?= number_format($saldo,0,',','.') ?></td>
    <td><?= $persen ?>%</td>
  </tr>
  <?php endforeach; ?>

  <tr style="font-weight:bold; background:#f0f0f0;">
    <td colspan="2">JUMLAH PENERIMAAN</td>
    <td colspan="5"><?= number_format($total_penerimaan,0,',','.') ?></td>
  </tr>

  <!-- Bagian Pengeluaran -->
  <?php foreach($pengeluaran as $r): 
        $id = $r['ID_pos'];
        $realisasi_bulan = $r['realisasi_bulan'];
        $realisasi_total = $r['realisasi_total'];
        $ang = getAnggaran($id, $anggaran);
        $saldo = $realisasi_total - $ang;
        $persen = $ang > 0 ? round(($realisasi_total / $ang) * 100,2) : 0;
        $total_pengeluaran += $realisasi_total;
  ?>
  <tr>
    <td><?= $id ?></td>
    <td style="text-align:left;"><?= $r['akun'] ?></td>
    <td><?= number_format($realisasi_bulan,0,',','.') ?></td>
    <td><?= number_format($realisasi_total,0,',','.') ?></td>
    <td><?= number_format($ang,0,',','.') ?></td>
    <td><?= number_format($saldo,0,',','.') ?></td>
    <td><?= $persen ?> %</td>
  </tr>
  <?php endforeach; ?>

  <tr style="font-weight:bold; background:#f0f0f0;">
    <td colspan="2">JUMLAH PENGELUARAN</td>
    <td colspan="5"><?= number_format($total_pengeluaran,0,',','.') ?></td>
  </tr>
</table>

<br>
<!-- Bagian Jumlah Bruder -->
<table>
  <tr><td>Jumlah Anggota Komunitas</td><td><?= $bruderCount ?></td></tr>
  <tr><td>Jumlah s.d Bulan Lalu</td><td>-</td></tr>
  <tr><td>Penambahan/Pengurangan bln ini</td><td>-</td></tr>
  <tr><td>Jumlah s.d Bulan ini</td><td><?= $bruderCount ?></td></tr>
</table>
</div>

  
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
    </script>
</body>
</html>