<?php
session_start();
require_once "../config/db.php";



// Ambil data POST
$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;
$admin_note = $_POST['admin_note'] ?? null;

// Validasi
$allowed_status = ['diproses', 'ditolak', 'selesai'];

if (!$id || !in_array($status, $allowed_status)) {
    header("Location: ../admin.php?msg=invalid");
    exit;
}

// Escape input
$admin_note = mysqli_real_escape_string($conn, $admin_note);

// Update status + note
$query = "UPDATE pengaduan 
          SET status = '$status', admin_note = '$admin_note' 
          WHERE id = '$id'";

$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: ../pengaduanAll.php");
} else {
    header("Location: ../pengaduanAll.php");
}
exit;