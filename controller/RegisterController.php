<?php
require_once "../config/db.php";
session_start();

if (isset($_POST['register'])) {
    $nik      = $_POST['nik'];
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username atau Email sudah digunakan');</script>";
        exit;
    }

    $query = "INSERT INTO users (nik, username, fullname, email, password)
              VALUES ('$nik', '$username', '$fullname', '$email', '$passwordHash')";

    if (mysqli_query($conn, $query)) {

        $user_id = mysqli_insert_id($conn);

        //  SESSION
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['login'] = true;

        //  TAMBAH ACTIVITY LOG
      $table = "users";

// escape username dulu
$username_safe = mysqli_real_escape_string($conn, $username);

$description = "User baru dengan username '$username_safe' berhasil register";

// escape description juga (lebih aman)
$description_safe = mysqli_real_escape_string($conn, $description);

mysqli_query($conn, "INSERT INTO activity (`table_name`, description) 
                     VALUES ('$table', '$description_safe')");

        header("Location: ../index.php");
    exit;
    } else {
        echo "<script>alert('Register gagal!'); </script>";
    }
}