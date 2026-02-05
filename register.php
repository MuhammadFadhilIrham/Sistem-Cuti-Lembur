<?php
include("config/db.php");

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek apakah username sudah terdaftar
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        // Simpan ke database
        $q = mysqli_query($conn, "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$password', 'user')");
        if ($q) {
            $success = "Pendaftaran berhasil! Silakan login.";
        } else {
            $error = "Terjadi kesalahan. Coba lagi.";
        }
    }
}
?>
<?php include("includes/header.php"); ?>

<style>
/* === Background dan layout utama === */
body {
    background: url('assets/bg-dashboard.jpg') no-repeat center center fixed;
    background-size: cover;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
}

/* === Card Registrasi === */
.register-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    padding: 40px 30px;
    width: 100%;
    max-width: 420px;
    animation: fadeIn 0.8s ease;
}

/* Animasi masuk */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* === Input dan tombol === */
.register-card input {
    border-radius: 10px;
    padding: 10px 12px;
}

.register-card button {
    background: linear-gradient(135deg, #007bff, #0056d2);
    border: none;
    border-radius: 10px;
    font-weight: bold;
    transition: 0.3s;
}

.register-card button:hover {
    background: linear-gradient(135deg, #0056d2, #0041a8);
    transform: scale(1.03);
}

/* === Link login === */
.register-card a {
    color: #0056d2;
    text-decoration: none;
}
.register-card a:hover {
    text-decoration: underline;
}
</style>

<div class="register-card text-center">
    <img src="assets/logo.png" 
         alt="Logo" 
         width="120" 
         class="mb-3"
         style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
    <h3 class="mb-4 fw-bold">Daftar Akun Baru</h3>

    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <form method="POST">
        <div class="mb-3 text-start">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
        </div>
        <div class="mb-3 text-start">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
        </div>
        <div class="mb-3 text-start">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <button name="register" class="btn btn-success w-100 mt-2">Daftar</button>
    </form>

    <p class="mt-3 mb-0">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </p>
</div>
