<?php
require_once "../config/db.php";
session_start();

// cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

if (isset($_POST['submit'])) {

    $user_id    = $_SESSION['user_id'];
    $title      = $_POST['title'];
    $description= $_POST['description'];
    $date       = date('Y-m-d H:i:s');

    // ======================
    // VALIDASI GAMBAR
    // ======================
    if ($_FILES['img']['name'] == "") {
        echo "<script>alert('Gambar wajib diupload!'); window.history.back();</script>";
        exit;
    }

    $file_name = $_FILES['img']['name'];
    $tmp_name  = $_FILES['img']['tmp_name'];
    $file_size = $_FILES['img']['size'];
    $error     = $_FILES['img']['error'];

    // ekstensi
    $ext_valid = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($ext, $ext_valid)) {
        echo "<script>alert('Format gambar harus JPG, JPEG, PNG'); window.history.back();</script>";
        exit;
    }

    if ($file_size > 2000000) {
        echo "<script>alert('Ukuran gambar maksimal 2MB'); window.history.back();</script>";
        exit;
    }

    // rename biar unik
    $new_name = time() . "_" . $file_name;
    $upload_path = "../uploads/" . $new_name;

    move_uploaded_file($tmp_name, $upload_path);

    // ======================
    // INSERT DATABASE
    // ======================
    $query = "INSERT INTO pengaduan (user_id, title, description, img, date)
              VALUES ('$user_id', '$title', '$description', '$new_name', '$date')";

    if (mysqli_query($conn, $query)) {

        // 🔥 optional: log activity
       $desc = "User membuat pengaduan dengan judul '$title'";

$stmt = mysqli_prepare($conn, "INSERT INTO activity (table_name, description) VALUES (?, ?)");
$table = "pengaduan";

mysqli_stmt_bind_param($stmt, "ss", $table, $desc);
mysqli_stmt_execute($stmt);

       header("Location: ../index.php");
    exit;
    } else {
        echo "<script>alert('Gagal menyimpan pengaduan'); window.history.back();</script>";
    }
}