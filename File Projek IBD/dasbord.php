<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['nama_bruder'])) {
    header('Location: login.php');
    exit;
}

$page_title = "Dashboard";
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <!-- Card sambutan -->
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body text-center p-5" style="background: linear-gradient(135deg, #6fb1fc, #4364f7, #0052d4); color: white;">
                    <h2 class="fw-bold mb-3">Selamat Datang 👋</h2>
                    <h4 class="mb-4">
                        <?= htmlspecialchars($_SESSION['nama_bruder'], ENT_QUOTES, 'UTF-8') ?>
                    </h4>
                    <p class="lead">Selamat Datang! Silakan gunakan menu navigasi di atas untuk mengelola data dan fitur yang tersedia.</p>
                    
                    <a href="profile.php" class="btn btn-light btn-lg mt-3 me-2 shadow-sm">
                        <i class="bi bi-person-circle"></i> Profil
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-lg mt-3 shadow-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Section info tambahan -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-journal-text display-5 text-primary"></i>
                            <h5 class="mt-3">Data Keuangan</h5>
                            <p class="text-muted">Kelola laporan dan catatan keuangan dengan mudah.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="bi bi-gear-fill display-5 text-success"></i>
                            <h5 class="mt-3">Pengaturan</h5>
                            <p class="text-muted">Sesuaikan preferensi akun dan sistem Anda.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
