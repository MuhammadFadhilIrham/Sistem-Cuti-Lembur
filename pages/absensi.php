<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) header("Location: ../login.php");

$id_user = $_SESSION['user']['id'];
$tanggal = date("Y-m-d");

// ===============================
// ABSEN MASUK
// ===============================
if (isset($_POST['absensi']) && $_POST['absensi'] == "Masuk") {

    // Cek apakah sudah absen masuk hari ini
    $cek = mysqli_query($conn, "SELECT * FROM absensi WHERE user_id='$id_user' AND tanggal='$tanggal'");

    if (mysqli_num_rows($cek) == 0) {
        // Simpan absen masuk
        $q = mysqli_query($conn, "
            INSERT INTO absensi (user_id, tanggal, jam_masuk, status)
            VALUES ('$id_user', '$tanggal', NOW(), 'hadir')
        ");

        $msg = $q ? "Absensi masuk berhasil!" : "Gagal menyimpan data absensi!";
    } else {
        $msg = "Anda sudah absen masuk hari ini!";
    }
}

// ===============================
// ABSEN PULANG
// ===============================
if (isset($_POST['absensi']) && $_POST['absensi'] == "Pulang") {

    // Cek apakah sudah ada absen masuk
    $cek = mysqli_query($conn, "
        SELECT * FROM absensi 
        WHERE user_id='$id_user' AND tanggal='$tanggal'
    ");

    if (mysqli_num_rows($cek) > 0) {

        $q = mysqli_query($conn, "
            UPDATE absensi 
            SET jam_pulang = NOW() 
            WHERE user_id='$id_user' AND tanggal='$tanggal'
        ");

        $msg = $q ? "Absensi pulang berhasil!" : "Gagal menyimpan data absensi!";
    } else {
        $msg = "Anda belum absen masuk!";
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
.absensi-box {
    max-width: 600px;
    margin: 120px auto;
    background: rgba(255, 255, 255, 0.15);
    padding: 35px;
    border-radius: 20px;
    backdrop-filter: blur(12px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    color: #fff;
    text-align: center;
}
.absensi-btn {
    font-size: 17px;
    padding: 15px;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    gap: 8px;
    align-items: center;
    font-weight: 600;
}
</style>

<div class="absensi-box">

  <h3 class="mb-4">Absensi Kehadiran</h3>

  <?php if (!empty($msg)) echo "<div class='alert alert-info'>$msg</div>"; ?>

  <form method="POST">

    <button name="absensi" value="Masuk" 
            class="btn btn-primary w-100 mb-3 absensi-btn">
        <ion-icon name="log-in-outline"></ion-icon>
        Absen Masuk
    </button>

    <button name="absensi" value="Pulang" 
            class="btn btn-danger w-100 absensi-btn">
        <ion-icon name="log-out-outline"></ion-icon>
        Absen Pulang
    </button>

  </form>
</div>

<?php include("../includes/footer.php"); ?>
