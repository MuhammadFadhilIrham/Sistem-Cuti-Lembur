<?php
include("../config/db.php");

if (!isset($_GET['id'])) {
    header("Location: user_manager.php?msg=error");
    exit();
}

$id = $_GET['id'];

// Hapus data
mysqli_query($conn, "DELETE FROM users WHERE id='$id'");

header("Location: user_manager.php?msg=deleted");
exit();
?>
