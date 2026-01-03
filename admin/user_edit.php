<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = $_GET['id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));

include("../includes/header.php");
include("../includes/navbar.php");
?>

<style>
    body {
        background: url('../assets/bg-dashboard.jpg') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Poppins', sans-serif;
    }

    .edit-container {
        max-width: 600px;
        margin: 120px auto;
        background: rgba(255, 255, 255, 0.13);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        padding: 30px;
        color: white;
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }

    .form-label {
        font-weight: 600;
        color: #fff;
    }

    .form-control, .form-select {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.4);
        color: #fff;
        border-radius: 10px;
    }

    .form-control::placeholder {
        color: #ddd;
    }

    .profile-preview {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        margin-bottom: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }

    .btn-primary {
        border-radius: 10px;
    }
</style>

<div class="edit-container">
    <h3 class="text-center mb-4">Edit Pengguna</h3>

    <form action="user_update.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?= $user['id']; ?>">

        <div class="text-center">
            <img src="<?= !empty($user['foto']) ? '../uploads/'.$user['foto'] : '../assets/default.png'; ?>" 
                 class="profile-preview" id="preview">
        </div>

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= $user['nama']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="username" name="username" class="form-control" value="<?= $user['username']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Role</label>
            <select class="form-select" name="role" required>
                <option value="user" <?= ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto Profil (Opsional)</label>
            <input type="file" class="form-control" name="foto" accept="image/*" onchange="previewImage(event)">
        </div>

        <button class="btn btn-primary w-100 mt-3">Perbarui Pengguna</button>
    </form>
</div>

<script>
function previewImage(event) {
    const img = document.getElementById('preview');
    img.src = URL.createObjectURL(event.target.files[0]);
}
</script>

<?php include("../includes/footer.php"); ?>
