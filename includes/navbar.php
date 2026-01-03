<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="../dashboard.php">
        <img src="../assets/logo.png" 
             alt="Logo" 
             style="height:40px; width:auto; margin-right:10px; object-fit:contain;">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="../pages/cuti.php"><ion-icon name="document-text-outline"></ion-icon> Cuti</a></li>
        <li class="nav-item"><a class="nav-link" href="../pages/lembur.php"><ion-icon name="time-outline"></ion-icon> Lembur</a></li>
        <li class="nav-item"><a class="nav-link" href="../pages/absensi.php"><ion-icon name="finger-print-outline"></ion-icon> Absensi</a></li>
        <li class="nav-item"><a class="nav-link" href="../pages/laporan_harian.php"><ion-icon name="reader-outline"></ion-icon> Laporan Harian</a></li>
        <li class="nav-item"><a class="nav-link" href="../pages/riwayat.php"><ion-icon name="timer-outline"></ion-icon> Riwayat</a></li>
        <li class="nav-item"><a class="nav-link" href="../pages/chat.php"><ion-icon name="chatbox-ellipses-outline"></ion-icon> Chat</a></li>
        <li class="nav-item"><a class="nav-link" href="../pages/notifikasi.php"><ion-icon name="notifications-outline"></ion-icon> Notifikasi</a></li>
      </ul>

      <!-- User dropdown dengan foto profil -->
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?= !empty($_SESSION['user']['foto']) ? '../uploads/' . htmlspecialchars($_SESSION['user']['foto']) : '../assets/default-user.png'; ?>"
                 alt="User" class="rounded-circle me-2"
                 width="35" height="35" style="object-fit: cover; border: 2px solid #fff;">
            <span><?= htmlspecialchars($_SESSION['user']['nama']); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow">
            <li><a class="dropdown-item" href="../edit_profil.php"><ion-icon name="person-circle-outline"></ion-icon> Edit Profil</a></li>
            <?php if ($_SESSION['user']['role'] == 'admin') { ?>
              <li><a class="dropdown-item" href="../admin/admin.php"><ion-icon name="person-outline"></ion-icon> Admin Panel</a></li>
              <li><a class="dropdown-item" href="../admin/chat_admin.php"><ion-icon name="chatbubble-ellipses-outline"></ion-icon> Chat Admin</a></li>
            <?php } ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../logout.php"><ion-icon name="log-out-outline"></ion-icon> Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Ionicons CDN -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<style>
  .navbar {
    transition: all 0.3s ease;
  }

  .navbar-brand {
    font-size: 1.25rem;
  }

  .nav-link:hover {
    color: #0d6efd !important;
    text-shadow: 0 0 8px rgba(13,110,253,0.5);
  }

  .dropdown-menu {
    border-radius: 12px;
  }
</style>
