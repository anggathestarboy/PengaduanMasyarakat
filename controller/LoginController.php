<?php
// START SESSION DI AWAL
session_start();
require_once "../config/db.php";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Validasi tidak kosong
    if (empty($username) || empty($password)) {
        header("Location: ../login.php?login_status=empty");
        exit;
    }
    
    // Query cek username
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['nik'] = $user['nik'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['login'] = true;
            
            // Redirect ke halaman utama
          if ($user['role'] === 'admin') {
    header("Location: ../dashboard.php"); // halaman admin
    exit;
} else {
    header("Location: ../index.php"); // halaman masyarakat
    exit;
}
            exit;
        } else {
            // Password salah
            header("Location: ../login.php?login_status=failed");
            exit;
        }
    } else {
        // Username tidak ditemukan
        header("Location: ../login.php?login_status=failed");
        exit;
    }
} else {
    // Jika bukan POST request, redirect ke login
    header("Location: ../login.php");
    exit;
}
?>