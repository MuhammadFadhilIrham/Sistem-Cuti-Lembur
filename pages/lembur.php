<?php
session_start();
require_once("../config/db.php");

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

// Proses pengajuan lembur
if (isset($_POST['ajukan'])) {
    $nama        = mysqli_real_escape_string($conn, $_SESSION['user']['nama']);
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam         = (int) $_POST['jam'];
    $keterangan  = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Upload file bukti
    $bukti = null;
    if (!empty($_FILES['bukti']['name'])) {
        $target_dir = "../assets/uploads/";
        $filename = time() . "_" . basename($_FILES['bukti']['name']);
        $target_file = $target_dir . $filename;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_type, ['pdf', 'jpg', 'jpeg', 'png'])) {
            if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
                $bukti = $filename;
            } else {
                $msg = "Gagal mengupload file bukti.";
            }
        } else {
            $msg = "Format file tidak diizinkan. Gunakan PDF, JPG, atau PNG.";
        }
    }

    // Simpan data ke database
    $user_id = $_SESSION['user']['id'];

    $query = "INSERT INTO lembur (user_id, nama, tanggal, jam, keterangan, bukti, status)
              VALUES ('$user_id', '$nama', '$tanggal', '$jam', '$keterangan', " . ($bukti ? "'$bukti'" : "NULL") . ", 'Menunggu')";

    if (mysqli_query($conn, $query)) {
        $msg = "Pengajuan lembur berhasil dikirim!";
    } else {
        $msg = "Terjadi kesalahan: " . mysqli_error($conn);
    }
}

include("../includes/header.php");
include("../includes/navbar.php");
?>

<style>
body {
  background: url('../assets/bg-dashboard.jpg') no-repeat center center fixed;
  background-size: cover;
  font-family: 'Poppins', sans-serif;
}
.form-container {
  position: relative;
  z-index: 2;
  max-width: 600px;
  margin: 120px auto;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 40px;
  color: #fff;
  box-shadow: 0 8px 25px rgba(0,0,0,0.3);
  transition: all 0.3s ease-in-out;
}
.form-container:hover {
  transform: scale(1.02);
  box-shadow: 0 12px 30px rgba(0,0,0,0.4);
}
.form-container h4 {
  font-weight: 700;
  margin-bottom: 20px;
  text-align: center;
}
label {
  font-weight: 500;
}
.form-control {
  border-radius: 10px;
  border: none;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}
.btn-primary {
  border-radius: 10px;
  font-weight: 600;
}
</style>

<div class="overlay"></div>

<div class="form-container">
  <h4>Form Pengajuan Lembur</h4>

  <?php if(isset($msg)): ?>
    <div class="alert alert-info text-center"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Tanggal</label>
      <input type="date" name="tanggal" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Jumlah Jam</label>
      <input type="number" name="jam" class="form-control" min="1" required>
    </div>
    <div class="mb-3">
      <label>Keterangan</label>
      <textarea name="keterangan" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
      <label>Upload Bukti (PDF/JPG/PNG)</label>
      <input type="file" name="bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
    </div>
    <button name="ajukan" class="btn btn-primary w-100 mt-3">Ajukan</button>
  </form>
</div>

<?php include("../includes/footer.php"); ?>
