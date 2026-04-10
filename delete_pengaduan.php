<?php
session_start();
require_once "config/db.php";

// cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $user_id = $_SESSION['user_id'];

    // pastikan hanya bisa hapus milik sendiri DAN status masih baru
    $cek = mysqli_query($conn, "SELECT * FROM pengaduan WHERE id='$id' AND user_id='$user_id' AND status='menunggu'");

    if (mysqli_num_rows($cek) > 0) {
        // ambil data dulu (untuk hapus gambar kalau ada)
        $data = mysqli_fetch_assoc($cek);

        // hapus gambar jika ada
        if (!empty($data['img']) && file_exists("uploads/" . $data['img'])) {
            unlink("uploads/" . $data['img']);
        }

        // hapus dari database
        mysqli_query($conn, "DELETE FROM pengaduan WHERE id='$id'");
    }
}

// kembali ke halaman sebelumnya
header("Location: pengaduanku.php");
exit;