<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_SESSION['user']['id'];

// ambil daftar pengguna yang pernah chat
$users = mysqli_query($conn, "
    SELECT DISTINCT u.id, u.nama, u.foto 
    FROM users u 
    JOIN chat c ON (c.sender_id=u.id OR c.receiver_id=u.id)
    WHERE u.id != '$admin_id'
");

// tampilkan chat dengan user tertentu
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
$messages = [];
$selected_user = null;

if ($user_id) {
    $messages = mysqli_query($conn, "
        SELECT * FROM chat 
        WHERE (sender_id='$user_id' AND receiver_id='$admin_id') 
        OR (sender_id='$admin_id' AND receiver_id='$user_id') 
        ORDER BY timestamp ASC
    ");
    $selected_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama, foto FROM users WHERE id='$user_id'"));
}

// kirim pesan admin
if (isset($_POST['send']) && $user_id) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        mysqli_query($conn, "INSERT INTO chat (sender_id, receiver_id, message) VALUES ('$admin_id', '$user_id', '$msg')");
        header("Location: chat_admin.php?user_id=$user_id");
        exit;
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

  .chat-container {
    position: relative;
    z-index: 2;
    max-width: 1100px;
    margin: 100px auto;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    color: white;
  }

  .list-group-item {
    background: rgba(255,255,255,0.1);
    color: #fff;
    border: none;
    margin-bottom: 5px;
    border-radius: 10px;
  }

  .list-group-item.active {
    background: #0db1fdff;
    border: none;
  }

  .chat-header h6,
  .user-name {
    color: #000 !important;
    font-weight: 600;
  }

  /* Jika nama pengguna adalah link */
  a, a:hover, a:focus {
    color: #000;
    text-decoration: none;
  }

  .chat-box {
    height: 400px;
    overflow-y: auto;
    background-color: rgba(255,255,255,0.15);
    padding: 15px;
    border-radius: 10px;
    scroll-behavior: smooth;
  }

  .bubble {
    max-width: 70%;
    padding: 10px 14px;
    border-radius: 18px;
    word-wrap: break-word;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }

  .user-bubble {
    background: #007bff;
    color: #fff;
    border-radius: 18px 18px 0 18px;
  }

  .admin-bubble {
    background: #e9ecef;
    color: #333;
    border-radius: 18px 18px 18px 0;
  }

  .card-footer input {
    border-radius: 10px;
  }

  .btn-primary {
    border-radius: 10px;
  }

  .user-photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
  }

  .user-list-photo {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
  }

  .chat-header {
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    padding-bottom: 10px;
    margin-bottom: 15px;
  }
</style>

<div class="chat-container">
  <h4 class="text-center mb-4">Chat Pengguna</h4>
  <div class="row">
    <!-- Sidebar daftar pengguna -->
    <div class="col-md-4 border-end">
      <h6 class="mb-3">Daftar Pengguna</h6>
      <ul class="list-group">
        <?php while($u = mysqli_fetch_assoc($users)): ?>
          <li class="list-group-item <?= ($user_id == $u['id']) ? 'active' : ''; ?>">
            <a href="?user_id=<?= $u['id']; ?>" class="d-flex align-items-center <?= ($user_id == $u['id']) ? 'text-white' : ''; ?>" style="text-decoration:none;">
              <img src="<?= !empty($u['foto']) ? '../uploads/'.$u['foto'] : '../assets/default.png'; ?>" class="user-list-photo">
              <span><?= htmlspecialchars($u['nama']); ?></span>
            </a>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>

    <!-- Area chat -->
    <div class="col-md-8">
      <?php if($user_id && $selected_user): ?>
        <div class="card shadow-sm border-0">
          <div class="card-body bg-transparent">
            <div class="chat-header">
              <img src="<?= !empty($selected_user['foto']) ? '../uploads/'.$selected_user['foto'] : '../assets/default.png'; ?>" class="user-photo">
              <h6 class="mb-0 text-white"><?= htmlspecialchars($selected_user['nama']); ?></h6>
            </div>

            <div class="chat-box" id="chat-box">
              <?php while($row = mysqli_fetch_assoc($messages)): ?>
                <?php if ($row['sender_id'] == $admin_id): ?>
                  <div class="d-flex justify-content-end mb-3">
                    <div class="bubble user-bubble">
                      <div>
                          <?= htmlspecialchars($row['message']); ?>

                          <?php if (!empty($row['file'])): ?>
                              <div class="mt-2">
                                  <?php 
                                  $file = '../uploads/chat/' . $row['file'];
                                  $ext  = strtolower(pathinfo($row['file'], PATHINFO_EXTENSION));

                                  if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                                      echo "<img src='$file' class='img-fluid rounded mt-2' style='max-width:200px;'>";
                                  } else {
                                      echo "<a href='$file' target='_blank' class='btn btn-sm btn-warning mt-2'>Lihat File</a>";
                                  }
                                  ?>
                              </div>
                          <?php endif; ?>
                      </div>
                      <small class="text-light-50 d-block text-end"><?= $row['timestamp']; ?></small>
                    </div>
                  </div>
                <?php else: ?>
                  <div class="d-flex justify-content-start mb-3 align-items-start">
                    <img src="<?= !empty($selected_user['foto']) ? '../uploads/'.$selected_user['foto'] : '../assets/default.png'; ?>" class="user-photo">
                    <div class="bubble admin-bubble">
                      <div>
                          <?= htmlspecialchars($row['message']); ?>

                          <?php if (!empty($row['file'])): ?>
                              <div class="mt-2">
                                  <?php 
                                  $file = '../uploads/chat/' . $row['file'];
                                  $ext  = strtolower(pathinfo($row['file'], PATHINFO_EXTENSION));

                                  if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                                      echo "<img src='$file' class='img-fluid rounded mt-2' style='max-width:200px;'>";
                                  } else {
                                      echo "<a href='$file' target='_blank' class='btn btn-sm btn-warning mt-2'>Lihat File</a>";
                                  }
                                  ?>
                              </div>
                          <?php endif; ?>
                      </div>
                      <small class="text-muted d-block"><?= $row['timestamp']; ?></small>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endwhile; ?>
            </div>

            <div class="card-footer bg-white border-top">
              <form method="POST" class="d-flex">
                <input type="text" name="message" class="form-control me-2" placeholder="Ketik pesan..." required>
                <button name="send" class="btn btn-primary px-4">Kirim</button>
              </form>
            </div>
          </div>
        </div>
      <?php else: ?>
        <p class="text-light mt-3">Pilih pengguna untuk melihat chat.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  var box = document.getElementById("chat-box");
  if(box) box.scrollTop = box.scrollHeight;
});
</script>

<?php include("../includes/footer.php"); ?>
