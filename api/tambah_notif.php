<?php
include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $tipe = $_POST['tipe']; // cuti/lembur
    $pesan = $_POST['pesan'];

    $stmt = $conn->prepare("INSERT INTO notifikasi (user_id, tipe, pesan) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $tipe, $pesan);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
