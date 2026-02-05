<?php include("includes/header.php"); ?>

<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: url('assets/bg.jpg') no-repeat center center fixed;
    background-size: cover;
}

/* Wrapper full layar */
.page-wrapper {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Area konten (card di tengah atas) */
.content {
    flex: 1;
    display: flex;
    justify-content: center;   /* tengah horizontal */
    align-items: flex-start;   /* posisi atas */
    padding-top: 80px;         /* jarak dari atas */
}

/* Card */
.container-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    padding: 40px 30px;
    width: 100%;
    max-width: 420px;
    animation: fadeIn 0.8s ease;
}

/* Footer di tengah bawah */
.footer {
    text-align: center;
    color: #fff;
    padding: 30px 10px;
    background: rgba(0, 0, 0, 0.35);
}

.separator {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 20px 0;
    color: #000000;
}

.separator::before,
.separator::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(0,0,0,0.3);
}

.separator::before {
    margin-right: 10px;
}

.separator::after {
    margin-left: 10px;
}

</style>

<body>
    <div class="page-wrapper">
        <div class="content">
            <div class="container-card text-center">
                <h1>Sistem Pengajuan Cuti & Lembur</h1>
                <p>Untuk Mahasiswa Kerja Praktik</p>
                <a href="login.php" class="btn btn-primary mt-3">Login</a>
                <div class="separator">atau</div>
                <a href="register.php" class="btn btn-primary mt-3">Register</a>
            </div>
        </div>

        <footer class="footer text-center">
            <img src="assets/logo.png" alt="Logo" width="60"><br>
            <strong>LEVER</strong>
            <p>Sistem Pengajuan Cuti, Lembur, Absensi & Laporan Harian</p>
            <small>© 2026 - Semua Hak Dilindungi</small>
        </footer>
    </div>
</body>
