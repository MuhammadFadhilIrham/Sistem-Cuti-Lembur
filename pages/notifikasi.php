<?php
session_start();
require_once("../config/db.php");

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Ambil notifikasi
$query = mysqli_query($conn, "
    SELECT * FROM notifications 
    WHERE user_id = $user_id 
    ORDER BY id DESC
");

// Set notifikasi jadi read
mysqli_query($conn, "
    UPDATE notifications 
    SET status = 'read' 
    WHERE user_id = $user_id
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

.notif-container {
    max-width: 750px;
    margin: 120px auto;
    padding: 30px;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    color: #fff;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.notif-title {
    font-weight: 700;
    text-align: center;
    margin-bottom: 25px;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
}

.notif-card {
    background: rgba(255, 255, 255, 0.9);
    padding: 18px 22px;
    border-radius: 15px;
    margin-bottom: 15px;
    color: #333;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transition: 0.2s ease;
}

.notif-card:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 16px rgba(0,0,0,0.3);
}

.notif-time {
    font-size: 13px;
    color: #666;
}

.badge-unread {
    background: #dc3545;
    color: white;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
}

.badge-read {
    background: #6c757d;
    color: white;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
}

.no-data {
    text-align: center;
    margin-top: 40px;
    color: #eee;
    font-size: 18px;
}
</style>

<div class="notif-container">
    <h3 class="notif-title">Notifikasi</h3>

    <?php if (mysqli_num_rows($query) === 0): ?>
        <div class="no-data">Tidak ada notifikasi.</div>
    <?php endif; ?>

    <?php while ($n = mysqli_fetch_assoc($query)): ?>
        <div class="notif-card">
            <div class="d-flex justify-content-between">
                <span class="notif-text"><?= htmlspecialchars($n['pesan']); ?></span>

                <?php if ($n['status'] == 'unread'): ?>
                    <span class="badge-unread">Baru</span>
                <?php else: ?>
                    <span class="badge-read">Dibaca</span>
                <?php endif; ?>
            </div>

            <div class="notif-time mt-2">
                <?= date("d M Y • H:i", strtotime($n['created_at'])); ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php include("../includes/footer.php"); ?>
