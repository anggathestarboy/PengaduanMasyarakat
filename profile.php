<?php
session_start();
require_once "config/db.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Variabel notifikasi
$notification = null;

// ================== FETCH DATA USER ==================
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));

// ================== UPDATE PROFILE ==================
if (isset($_POST['update'])) {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // jika password diisi
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE users SET 
            nik='$nik',
            username='$username',
            fullname='$fullname',
            email='$email',
            password='$password'
            WHERE id=$user_id";
    } else {
        $query = "UPDATE users SET 
            nik='$nik',
            username='$username',
            fullname='$fullname',
            email='$email'
            WHERE id=$user_id";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['notification'] = ['type' => 'success', 'message' => 'Profile berhasil diupdate!'];
        header("Location: profile.php");
        exit;
    } else {
        $_SESSION['notification'] = ['type' => 'error', 'message' => 'Gagal mengupdate profile. Silakan coba lagi.'];
        header("Location: profile.php");
        exit;
    }
}

// Ambil notifikasi dari session
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    unset($_SESSION['notification']);
}

$admin_name = $user['fullname'] ?? $user['username'] ?? 'Pengguna';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
$admin_role = $_SESSION['role'] ?? 'Masyarakat';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Saya - Pengaduan Masyarakat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 900: '#0A1628', 800: '#0F2044', 700: '#142B5C', 600: '#1A3570' },
                        cobalt: { DEFAULT: '#1B4FD8', light: '#2E63E8', pale: '#EBF0FD' },
                        gold: { DEFAULT: '#C9963A', light: '#E8B44F' },
                    },
                    fontFamily: {
                        display: ['Playfair Display', 'serif'],
                        body: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #F7F9FF 0%, #EBF0FD 100%);
            min-height: 100vh;
        }
        
        /* Navbar styling */
        .navbar-scrolled { background: rgba(255,255,255,0.97) !important; box-shadow: 0 2px 24px rgba(27,79,216,0.10) !important; }
        #mobile-menu { max-height: 0; overflow: hidden; transition: max-height 0.4s cubic-bezier(0.4,0,0.2,1), opacity 0.3s; opacity: 0; }
        #mobile-menu.open { max-height: 480px; opacity: 1; }
        
        /* Profile Card Animation */
        .profile-card {
            animation: fadeInUp 0.6s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-input {
            transition: all 0.3s ease;
            border: 1px solid #E2E8F0;
        }
        .form-input:focus {
            border-color: #1B4FD8;
            box-shadow: 0 0 0 3px rgba(27,79,216,0.1);
            outline: none;
        }
        
        .btn-update {
            background: linear-gradient(135deg, #1B4FD8 0%, #2E63E8 100%);
            transition: all 0.3s ease;
        }
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(27,79,216,0.4);
        }
        
        .avatar-large {
            background: linear-gradient(135deg, #1B4FD8, #C9963A);
            box-shadow: 0 10px 25px -5px rgba(27,79,216,0.3);
        }
        
        /* Notification Toast */
        .toast-notification {
            position: fixed;
            top: 90px;
            right: 20px;
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
            max-width: 380px;
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .toast-hide { animation: slideOutRight 0.3s ease-out forwards; }
        
        /* Floating decoration */
        .floating-bg {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 0;
        }
        .floating-bg::before {
            content: '';
            position: absolute;
            top: 20%;
            right: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(27,79,216,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .floating-bg::after {
            content: '';
            position: absolute;
            bottom: 10%;
            left: -5%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(201,150,58,0.06) 0%, transparent 70%);
            border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="floating-bg"></div>

<!-- NAVBAR COMPONENT -->
<?php include "components/navbar.php" ?>

<!-- Notification Popup -->
<?php if ($notification): ?>
<div id="notificationToast" class="toast-notification">
    <div class="<?= $notification['type'] == 'success' ? 'bg-emerald-500' : 'bg-rose-500' ?> rounded-xl shadow-2xl p-4 min-w-[300px] border-l-8 <?= $notification['type'] == 'success' ? 'border-emerald-700' : 'border-rose-700' ?>">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <?php if ($notification['type'] == 'success'): ?>
                    <i class="fa-regular fa-circle-check text-white text-2xl"></i>
                <?php else: ?>
                    <i class="fa-regular fa-circle-exclamation text-white text-2xl"></i>
                <?php endif; ?>
            </div>
            <div class="flex-1">
                <p class="text-white font-semibold text-sm">
                    <?= $notification['type'] == 'success' ? 'Berhasil!' : 'Gagal!' ?>
                </p>
                <p class="text-white/90 text-sm"><?= htmlspecialchars($notification['message']) ?></p>
            </div>
            <button onclick="closeNotification()" class="text-white/70 hover:text-white transition">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
    </div>
</div>
<script>
    function closeNotification() {
        const toast = document.getElementById('notificationToast');
        toast.classList.add('toast-hide');
        setTimeout(() => toast.remove(), 300);
    }
    setTimeout(() => {
        const toast = document.getElementById('notificationToast');
        if (toast) {
            toast.classList.add('toast-hide');
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
</script>
<?php endif; ?>

<!-- MAIN CONTENT - CENTERED -->
<main class="pt-28 pb-20 px-4 relative z-10">
    <div class="max-w-4xl mx-auto">
        <!-- Profile Card -->
        <div class="profile-card bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Header Banner -->
            <div class="relative h-32 bg-gradient-to-r from-navy-900 via-cobalt to-gold"></div>
            
            <!-- Avatar Section -->
            <div class="relative px-6 sm:px-8">
                <div class="absolute -top-12 left-6 sm:left-8">
                    <div class="avatar-large w-24 h-24 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                        <?= $admin_initial ?>
                    </div>
                </div>
                <div class="pt-16 pb-4 text-left">
                    <h1 class="font-display text-2xl sm:text-3xl font-bold text-navy-900"><?= htmlspecialchars($user['fullname']) ?></h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-cobalt-pale text-cobalt">
                            <i class="fa-regular fa-user text-xs"></i>
                            <?= ucfirst(htmlspecialchars($user['role'] ?? 'Masyarakat')) ?>
                        </span>
                        <span class="text-gray-400 text-sm">Member sejak <?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Form Section -->
            <div class="px-6 sm:px-8 pb-8">
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex items-center gap-2 mb-6">
                        <i class="fa-regular fa-pen-to-square text-cobalt text-lg"></i>
                        <h2 class="font-display font-semibold text-xl text-navy-900">Edit Profile</h2>
                    </div>
                    
                    <form method="POST" onsubmit="return validateForm(this)">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- NIK -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-regular fa-id-card mr-2 text-cobalt"></i>NIK
                                </label>
                                <input type="text" id="nik" name="nik" maxlength="16" required 
                                       value="<?= htmlspecialchars($user['nik']) ?>"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white transition-all"
                                       placeholder="Masukkan 16 digit NIK">
                                <p class="text-xs text-gray-400 mt-1">* NIK harus 16 digit angka</p>
                            </div>
                            
                            <!-- Username -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-regular fa-user mr-2 text-cobalt"></i>Username
                                </label>
                                <input type="text" name="username" required 
                                       value="<?= htmlspecialchars($user['username']) ?>"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white transition-all"
                                       placeholder="Username">
                            </div>
                            
                            <!-- Fullname -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-regular fa-address-card mr-2 text-cobalt"></i>Nama Lengkap
                                </label>
                                <input type="text" name="fullname" required 
                                       value="<?= htmlspecialchars($user['fullname']) ?>"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white transition-all"
                                       placeholder="Nama lengkap">
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-regular fa-envelope mr-2 text-cobalt"></i>Email
                                </label>
                                <input type="email" name="email" required 
                                       value="<?= htmlspecialchars($user['email']) ?>"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white transition-all"
                                       placeholder="email@example.com">
                            </div>
                            
                            <!-- Password -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fa-solid fa-lock mr-2 text-cobalt"></i>Password Baru
                                </label>
                                <input type="password" name="password" 
                                       class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 focus:bg-white transition-all"
                                       placeholder="Kosongkan jika tidak ingin mengubah password">
                                <p class="text-xs text-gray-400 mt-1">* Minimal 4 karakter jika diisi</p>
                            </div>
                        </div>
                        
                        <!-- Button Actions -->
                        <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-4 border-t border-gray-100">
                            <button type="submit" name="update" class="btn-update flex-1 px-6 py-3 rounded-xl text-white font-semibold flex items-center justify-center gap-2 transition-all">
                                <i class="fa-regular fa-floppy-disk"></i>
                                Simpan Perubahan
                            </button>
                            <a href="index.php" class="flex-1 px-6 py-3 rounded-xl border border-gray-300 text-gray-600 font-semibold flex items-center justify-center gap-2 hover:bg-gray-50 transition-all">
                                <i class="fa-solid fa-arrow-left"></i>
                                Kembali ke Beranda
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Tambahan -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-400">
                <i class="fa-regular fa-shield-check mr-1"></i> 
                Data Anda aman dan terenkripsi
            </p>
        </div>
    </div>
</main>

<script>
    // NIK Validation - hanya angka, max 16 digit
    const nikInput = document.getElementById("nik");
    if (nikInput) {
        nikInput.addEventListener("input", function () {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 16) {
                this.value = this.value.slice(0, 16);
            }
        });
    }
    
    function validateForm(form) {
        const nik = document.getElementById("nik").value;
        if (nik.length !== 16) {
            showValidationError("NIK harus 16 digit angka!");
            return false;
        }
        
        const password = form.querySelector('input[name="password"]');
        if (password.value && password.value.length < 4) {
            showValidationError("Password minimal 4 karakter!");
            return false;
        }
        return true;
    }
    
    function showValidationError(message) {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.innerHTML = `
            <div class="bg-rose-500 rounded-xl shadow-2xl p-4 min-w-[300px] border-l-8 border-rose-700">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <i class="fa-regular fa-circle-exclamation text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-semibold text-sm">Validasi Gagal!</p>
                        <p class="text-white/90 text-sm">${message}</p>
                    </div>
                    <button onclick="this.closest('.toast-notification').remove()" class="text-white/70 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            if (toast) toast.remove();
        }, 5000);
    }
    
    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('navbar-scrolled', window.scrollY > 20);
        });
    }
    
    // Hamburger menu toggle
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobile-menu');
    if (hamburger) {
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
        });
    }
</script>

</body>
</html>