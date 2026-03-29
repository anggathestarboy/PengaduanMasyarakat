<?php
session_start();

if (isset($_POST['logout'])) {

    // hapus semua session
    $_SESSION = [];

    // hancurkan session
    session_destroy();

    // redirect ke login / home
    header("Location: /");
    exit;
}