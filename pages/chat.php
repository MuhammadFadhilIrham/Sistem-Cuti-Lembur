<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$admin_id = 1;

// =============== KIRIM PESAN ===============
if (isset($_POST['send'])) {

    $msg = trim($_POST['message']);
    $file_name = "";

    // Jika ada file upload
    if (!empty($_FILES['file']['name'])) {
        $folder = "../uploads/chat/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $file_name = time() . "_" . basename($_FILES['file']['name']);
        $path = $folder . $file_name;

        move_uploaded_file($_FILES['file']['tmp_name'], $path);
    }

    mysqli_query($conn,
        "INSERT INTO chat (sender_id, receiver_id, message, file) 
         VALUES ('$user_id', '$admin_id', '$msg', '$file_name')"
    );
}

$q = mysqli_query($conn, "
    SELECT * FROM chat WHERE 
    (sender_id='$user_id' AND receiver_id='$admin_id') 
    OR (sender_id='$admin_id' AND receiver_id='$user_id') 
    ORDER BY timestamp ASC
");

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
  max-width: 700px;
  margin: 100px auto;
  background: rgba(255,255,255,0.12);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  color: #fff;
}

.chat-box {
  height: 400px;
  overflow-y: auto;
  padding: 20px;
}

.bubble {
  max-width: 70%;
  padding: 10px 14px;
  border-radius: 18px;
  margin-bottom: 10px;
}

.user-bubble {
  background: #007bff;
  color: #fff;
  margin-left: auto;
}

.admin-bubble {
  background: #e9ecef;
  color: #000;
}

/* Emoji Picker */
.emoji-picker {
  display: none;
  background: #fff;
  border-radius: 10px;
  padding: 10px;
  position: absolute;
  bottom: 60px;
  width: 250px;
  height: 150px;
  overflow-y: auto;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.emoji-picker span {
  font-size: 22px;
  cursor: pointer;
  padding: 5px;
}
</style>

<div class="chat-container">
  <h4 class="text-center p-3 fw-bold">Chat dengan Admin</h4>

  <div class="chat-box" id="chat-box">

    <?php while($row = mysqli_fetch_assoc($q)): ?>
      <?php
      $isUser = $row['sender_id'] == $user_id;
      $bubbleClass = $isUser ? "user-bubble" : "admin-bubble";
      ?>

      <div class="d-flex <?= $isUser ? 'justify-content-end' : 'justify-content-start' ?>">
        <div class="bubble <?= $bubbleClass ?>">

          <!-- Pesan -->
          <?= nl2br(htmlspecialchars($row['message'])) ?><br>

          <!-- File Upload -->
          <?php if (!empty($row['file'])): ?>
            <?php
              $ext = strtolower(pathinfo($row['file'], PATHINFO_EXTENSION));
              $filePath = "../uploads/chat/" . $row['file'];
            ?>

            <?php if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
              <img src="<?= $filePath ?>" 
                   style="max-width:150px; border-radius:10px; margin-top:8px;">
            
            <?php elseif ($ext == 'pdf'): ?>
              <a href="<?= $filePath ?>" target="_blank" class="text-warning">
                📄 Lihat PDF
              </a>
            <?php endif; ?>

          <?php endif; ?>

          <small class="text-light-50 d-block mt-1"><?= $row['timestamp'] ?></small>
        </div>
      </div>

    <?php endwhile; ?>
  </div>

  <!-- EMOJI PICKER -->
  <div class="emoji-picker" id="emoji-picker">
    <?php
    $emojiList = ["😀","😃","😄","😁","😆","😂","🤣","😊","😍","😘","😎","🤩","👍","🙏","🔥","✨","🎉","❤️","😢","🤔"];
    foreach ($emojiList as $e) echo "<span>$e</span>";
    ?>
  </div>

  <div class="card-footer p-3 position-relative">
    <form method="POST" enctype="multipart/form-data" class="d-flex">

      <button type="button" id="emoji-btn" class="btn btn-light me-2">😊</button>

      <input type="text" name="message" id="message" 
             class="form-control me-2" placeholder="Ketik pesan..." required>

      <input type="file" name="file" class="form-control me-2" accept="image/*,application/pdf">

      <button name="send" class="btn btn-primary px-4">Kirim</button>

    </form>
  </div>
</div>

<script>
// Auto scroll
var box = document.getElementById("chat-box");
box.scrollTop = box.scrollHeight;

// Emoji toggle
document.getElementById("emoji-btn").onclick = function() {
  let picker = document.getElementById("emoji-picker");
  picker.style.display = picker.style.display === "block" ? "none" : "block";
};

// Insert emoji to message
document.querySelectorAll("#emoji-picker span").forEach(e => {
  e.onclick = function() {
    document.getElementById("message").value += this.textContent;
  };
});
</script>

<?php include("../includes/footer.php"); ?>
