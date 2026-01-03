<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) header("Location: ../login.php");

$id_user = $_SESSION['user']['id'];

$alert = "";

// Ketika laporan dikirim
if (isset($_POST['submit'])) {

    $kegiatan = mysqli_real_escape_string($conn, $_POST['kegiatan']);
    $kendala  = mysqli_real_escape_string($conn, $_POST['kendala']);
    $tanggal  = date("Y-m-d");
    $bukti    = "";

    // =========================
    // UPLOAD BUKTI (PDF / IMAGE)
    // =========================
    if (!empty($_FILES['bukti']['name'])) {

        $folder = "../assets/uploads/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));

        // Izinkan PDF dan gambar
        $allowed = ["jpg", "jpeg", "png", "pdf"];

        if (in_array($ext, $allowed)) {

            $namaBaru = "LP_" . time() . "." . $ext;
            $path = $folder . $namaBaru;

            if (move_uploaded_file($_FILES['bukti']['tmp_name'], $path)) {
                $bukti = $namaBaru;
            }
        }
    }

    // =========================
    // SIMPAN KE DATABASE
    // =========================

    $query = "
        INSERT INTO laporan_harian 
        (user_id, tanggal, kegiatan, kendala, bukti, status, created_at)
        VALUES 
        ('$id_user', '$tanggal', '$kegiatan', '$kendala', '$bukti', 'Menunggu', NOW())
    ";

    mysqli_query($conn, $query);

    $alert = "Laporan berhasil dikirim!";
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
.card-form {
    max-width: 600px;
    margin: auto;
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
}
</style>

<div class="container mt-5 pt-5">
  <div class="card-form">

    <h3 class="mb-3">Laporan Harian</h3>

    <?php if (!empty($alert)) echo "<div class='alert alert-success'>$alert</div>"; ?>

    <form method="POST" enctype="multipart/form-data">

      <label class="mb-1 fw-bold">Kegiatan</label>
      <textarea name="kegiatan" class="form-control mb-3" rows="4" required></textarea>

      <label class="mb-1 fw-bold">Kendala (kosongkan jika tidak ada)</label>
      <textarea name="kendala" class="form-control mb-3" rows="2"></textarea>

      <label class="mb-1 fw-bold">Upload Bukti (gambar/PDF)</label>
      <input type="file" name="bukti" class="form-control mb-3"
        accept="image/*,application/pdf">

      <button name="submit" class="btn btn-primary w-100">
        <ion-icon name="send-outline"></ion-icon> Kirim Laporan
      </button>
    </form>

  </div>
</div>

<?php include("../includes/footer.php"); ?>
