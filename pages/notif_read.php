<?php
include("../config/db.php");

$id = $_GET['id'];

$conn->query("UPDATE notifikasi SET status='read' WHERE id=$id");

header("Location: notifikasi.php");
exit;
?>
