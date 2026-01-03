<?php
session_start();
include("config/db.php");

// Cek login
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user']['id'];
$user_role = $_SESSION['user']['role'];

// Ambil data user
$q = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$data = mysqli_fetch_assoc($q);

// Update profil
if (isset($_POST['update'])) {
  $nama = mysqli_real_escape_string($conn, $_POST['nama']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  // Upload foto jika ada
  $fotoBaru = $data['foto']; // default: foto lama
  if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $fileName = time() . "_" . basename($_FILES['foto']['name']);
    $targetFile = $targetDir . $fileName;

    // Validasi ekstensi
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];
    if (in_array($ext, $allowed)) {
      // Hapus foto lama jika ada
      if (!empty($data['foto']) && file_exists($targetDir . $data['foto'])) {
        unlink($targetDir . $data['foto']);
      }
      move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile);
      $fotoBaru = $fileName;
    } else {
      $error = "Format gambar tidak valid. Gunakan JPG, PNG, atau GIF.";
    }
  }

  if (!isset($error)) {
    if (!empty($password)) {
      $update = mysqli_query($conn, "UPDATE users SET nama='$nama', username='$username', password='$password', foto='$fotoBaru' WHERE id='$user_id'");
    } else {
      $update = mysqli_query($conn, "UPDATE users SET nama='$nama', username='$username', foto='$fotoBaru' WHERE id='$user_id'");
    }

    if ($update) {
      $_SESSION['user']['nama'] = $nama;
      $_SESSION['user']['username'] = $username;
      $_SESSION['user']['foto'] = $fotoBaru;
      $success = "Profil berhasil diperbarui!";
      $data['foto'] = $fotoBaru; // Refresh data foto
    } else {
      $error = "Gagal memperbarui profil.";
    }
  }
}

include("includes/header.php");
?>

<style>
  body {
    background: url('assets/bg-dashboard.jpg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', sans-serif;
  }

  .edit-profile-container {
    max-width: 500px;
    margin: 100px auto;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 40px;
    color: #fff;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
  }

  .profile-pic {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    margin: 0 auto 20px;
    display: block;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
  }

  .form-control {
    background-color: rgba(255,255,255,0.9);
  }

  .btn-primary {
    background-color: #007bff;
    border: none;
    border-radius: 10px;
  }

  .btn-primary:hover {
    background-color: #0056b3;
  }

  h3 {
    font-weight: 700;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
  }
</style>

<div class="edit-profile-container">
  <h3 class="text-center mb-4">Edit Profil</h3>

  <?php if(isset($error)) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>
  <?php if(isset($success)) echo "<div class='alert alert-success text-center'>$success</div>"; ?>

  <img src="<?= !empty($data['foto']) ? 'uploads/'.$data['foto'] : 'assets/default-user.png' ?>" class="profile-pic" id="preview">

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Foto Profil</label>
      <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(event)">
    </div>

    <div class="mb-3">
      <label>Nama Lengkap</label>
      <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Username</label>
      <input type="text" name="username" value="<?= htmlspecialchars($data['username']); ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Password (kosongkan jika tidak ingin mengganti)</label>
      <input type="password" name="password" class="form-control" placeholder="Masukkan password baru (opsional)">
    </div>

    <button name="update" class="btn btn-primary w-100">Simpan Perubahan</button>
  </form>

  <p class="text-center mt-3">
    <a href="<?= $user_role == 'admin' ? 'admin/admin.php' : 'dashboard.php' ?>" class="text-light">← Kembali ke Dashboard</a>
  </p>
</div>

<script>
function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function(){
    document.getElementById('preview').src = reader.result;
  }
  reader.readAsDataURL(event.target.files[0]);
}
</script>

<?php include("includes/footer.php"); ?>
