<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Tambah Data Bank</title>

  <!-- Font Awesome untuk ikon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: linear-gradient(135deg, #dfefff, #ffffff);
      margin: 0;
      padding: 0;
    }

    .bank-form-card {
      background: white;
      border-radius: 20px;
      padding: 30px 40px;
      max-width: 500px;
      margin: 60px auto;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      transition: 0.3s;
    }

    .bank-form-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .bank-form-card h2 {
      text-align: center;
      margin-bottom: 25px;
      font-size: 1.5rem;
      color: #004b8d;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .input-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 18px;
    }

    .input-group label {
      font-weight: 600;
      color: #333;
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .input-group input {
      padding: 10px 14px;
      border: 1.5px solid #cce7ff;
      border-radius: 10px;
      font-size: 0.95rem;
      transition: all 0.25s ease;
    }

    .input-group input:focus {
      border-color: #007bff;
      box-shadow: 0 0 8px rgba(0,123,255,0.2);
      outline: none;
    }

    .btn-gradient {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 25px;
      font-size: 1rem;
      font-weight: bold;
      color: white;
      background: linear-gradient(90deg, #007bff, #00c6ff);
      cursor: pointer;
      transition: 0.3s;
    }

    .btn-gradient:hover {
      background: linear-gradient(90deg, #0056b3, #0099ff);
      transform: translateY(-2px);
    }

    .footer-text {
      text-align: center;
      margin-top: 20px;
      color: #555;
      font-size: 0.85rem;
    }

    @media (max-width: 600px) {
      .bank-form-card {
        padding: 20px;
        width: 90%;
      }
    }
  </style>
</head>
<body>

  <div class="bank-form-card">
    <h2>Tambah Data Bank</h2>

    <form method="POST" id="bankForm" action="">
      <div class="input-group">
        <label><i class="fa-solid fa-building-columns"></i> Nama Bank</label>
        <input type="text" name="nama_bank" placeholder="Contoh: BCA, Mandiri" required>
      </div>

      <div class="input-group">
        <label><i class="fa-solid fa-credit-card"></i> No Rekening</label>
        <input type="text" name="no_rek_bank" placeholder="Masukkan nomor rekening" required>
      </div>

      <div class="input-group">
        <label><i class="fa-solid fa-user"></i> Atas Nama</label>
        <input type="text" name="atas_nama_bank" placeholder="Nama pemilik rekening" required>
      </div>

      <div class="input-group">
        <label><i class="fa-solid fa-money-bill-wave"></i> Nominal Saldo Awal</label>
        <input type="number" step="0.01" name="nominal_penerimaan" placeholder="Masukkan saldo awal" required>
      </div>

      <div class="input-group">
        <label><i class="fa-solid fa-calendar-day"></i> Tanggal Transaksi</label>
        <input type="date" name="tgl_transaksi" value="<?php echo date('Y-m-d'); ?>" required>
      </div>

      <button type="submit" class="btn-gradient">💾 Simpan</button>
    </form>
  </div>

  <div class="footer-text">
    © 2025 Sistem Keuangan Bruder FIC
  </div>

</body>
</html>
