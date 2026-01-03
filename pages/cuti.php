<?php
session_start();
require_once("../config/db.php");

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

// Proses pengajuan cuti
if (isset($_POST['ajukan'])) {
    $nama    = mysqli_real_escape_string($conn, $_SESSION['user']['nama']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $alasan  = mysqli_real_escape_string($conn, $_POST['alasan']);

    // Proses upload file (jika ada)
    $bukti = null;
    if (!empty($_FILES['bukti']['name'])) {
        $target_dir = "../assets/uploads/";
        $filename = time() . "_" . basename($_FILES['bukti']['name']);
        $target_file = $target_dir . $filename;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi ekstensi file
        if (in_array($file_type, ['pdf', 'jpg', 'jpeg', 'png'])) {
            if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file)) {
                $bukti = $filename;
            } else {
                $msg = "Gagal mengupload bukti dokumen.";
            }
        } else {
            $msg = "Format file tidak diizinkan. Hanya PDF, JPG, dan PNG.";
        }
    }

    // Simpan ke database
    $user_id = $_SESSION['user']['id'];

    $query = "INSERT INTO cuti (user_id, nama, tanggal, alasan, bukti, status) 
              VALUES ('$user_id', '$nama', '$tanggal', '$alasan', " . ($bukti ? "'$bukti'" : "NULL") . ", 'Menunggu')";

    if (mysqli_query($conn, $query)) {
        $msg = "Pengajuan cuti berhasil dikirim!";
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
}
.form-container h4 {
  font-weight: 700;
  margin-bottom: 20px;
  text-align: center;
}
.form-control {
  border-radius: 10px;
  border: none;
}
.btn-primary {
  border-radius: 10px;
  font-weight: 600;
}
</style>

<div class="overlay"></div>

<div class="form-container">
  <h4>Form Pengajuan Cuti</h4>
  <?php if(isset($msg)): ?>
    <div class="alert alert-info text-center"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Tanggal</label>
      <input type="date" name="tanggal" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Alasan</label>
      <textarea name="alasan" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
      <label>Upload Bukti (PDF/JPG/PNG)</label>
      <input type="file" name="bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
    </div>
    <button name="ajukan" class="btn btn-primary w-100 mt-3">Ajukan</button>
  </form>
</div>

<?php include("../includes/footer.php"); ?>
