<?php
session_start();
require_once "config/db.php";

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// cek role
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Variabel untuk notifikasi
$notification = null;

// ================== CREATE ==================
if (isset($_POST['add'])) {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $query = "INSERT INTO users (nik, username, fullname, email, password, role)
        VALUES ('$nik','$username','$fullname','$email','$password','$role')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['notification'] = ['type' => 'success', 'message' => 'User berhasil ditambahkan!'];
        header("Location: users.php");
        exit;
    } else {
        $_SESSION['notification'] = ['type' => 'error', 'message' => 'Gagal menambahkan user. Silakan coba lagi.'];
        header("Location: users.php");
        exit;
    }
}

// ================== DELETE ==================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM users WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['notification'] = ['type' => 'success', 'message' => 'User berhasil dihapus!'];
        header("Location: users.php");
        exit;
    } else {
        $_SESSION['notification'] = ['type' => 'error', 'message' => 'Gagal menghapus user. Silakan coba lagi.'];
        header("Location: users.php");
        exit;
    }
}

// ================== UPDATE ==================
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // cek password diubah atau tidak
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE users SET 
            nik='$nik',
            username='$username',
            fullname='$fullname',
            email='$email',
            password='$password',
            role='$role'
            WHERE id=$id";
    } else {
        $query = "UPDATE users SET 
            nik='$nik',
            username='$username',
            fullname='$fullname',
            email='$email',
            role='$role'
            WHERE id=$id";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['notification'] = ['type' => 'success', 'message' => 'User berhasil diupdate!'];
        header("Location: users.php");
        exit;
    } else {
        $_SESSION['notification'] = ['type' => 'error', 'message' => 'Gagal mengupdate user. Silakan coba lagi.'];
        header("Location: users.php");
        exit;
    }
}

// Ambil notifikasi dari session
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    unset($_SESSION['notification']);
}

// ================== FETCH ==================
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");

// ================== EDIT DATA ==================
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $editData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$id"));
    if (!$editData) {
        $_SESSION['notification'] = ['type' => 'error', 'message' => 'Data user tidak ditemukan!'];
        header("Location: users.php");
        exit;
    }
}

// Data untuk navbar
$admin_name = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Administrator';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
$admin_role = $_SESSION['role'] ?? 'Super Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Users - Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: { 950:'#060C1A', 900:'#0C1829', 800:'#112038', 700:'#172848' },
                        azure: { DEFAULT:'#1D5CFF', soft:'#3B74FF', pale:'#E8EEFF', dim:'#C5D3FF' },
                        slate: { panel:'#F0F4FF', border:'#DDE4F5' },
                    },
                    fontFamily: {
                        display: ['Syne', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #EEF2FF;
            color: #112038;
            overflow-x: hidden;
        }
        /* Sidebar styling */
        #sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(175deg, #060C1A 0%, #0C1829 55%, #112038 100%);
            position: fixed;
            left: 0; top: 0; bottom: 0;
            display: flex;
            flex-direction: column;
            transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
            z-index: 50;
            box-shadow: 4px 0 32px rgba(6,12,26,0.4);
        }
        #sidebar::before {
            content:''; position:absolute; inset:0;
            background: radial-gradient(ellipse at 20% 10%, rgba(29,92,255,0.18) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 90%, rgba(29,92,255,0.10) 0%, transparent 50%);
            pointer-events:none;
        }
        #sidebar-overlay {
            display:none; position:fixed; inset:0; background:rgba(6,12,26,0.55); z-index:40; backdrop-filter:blur(3px);
        }
        #sidebar::after {
            content:''; position:absolute; inset:0;
            background-image: linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events:none;
        }
        .nav-item {
            display:flex; align-items:center; gap:12px; padding:10px 18px; border-radius:12px;
            font-size:0.875rem; font-weight:500; color:rgba(197,211,255,0.7); cursor:pointer;
            transition: all 0.2s ease; text-decoration:none;
        }
        .nav-item:hover { background:rgba(29,92,255,0.15); color:#fff; }
        .nav-item.active {
            background: linear-gradient(90deg, rgba(29,92,255,0.35), rgba(29,92,255,0.12));
            color:#fff; box-shadow: inset 3px 0 0 #1D5CFF;
        }
        .nav-item .icon-wrap {
            width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center;
            flex-shrink:0; background:rgba(255,255,255,0.07); font-size:0.8rem;
        }
        .nav-item.active .icon-wrap { background:rgba(29,92,255,0.5); }
        /* Topbar */
        #topbar {
            position:fixed; top:0; left:260px; right:0; height:64px;
            background:rgba(238,242,255,0.92); backdrop-filter:blur(14px);
            border-bottom:1px solid #DDE4F5; display:flex; align-items:center;
            padding:0 28px; z-index:30; transition: left 0.35s cubic-bezier(0.4,0,0.2,1);
        }
        #main {
            margin-left:260px; padding-top:64px; min-height:100vh;
            transition: margin-left 0.35s cubic-bezier(0.4,0,0.2,1);
        }
        @media (max-width:768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #sidebar-overlay.show { display:block; }
            #topbar { left:0 !important; }
            #main { margin-left:0 !important; }
        }
        .avatar-ring {
            width:38px; height:38px; border-radius:50%;
            background: linear-gradient(135deg, #1D5CFF, #8B5CF6);
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:0.8rem; color:#fff;
            box-shadow:0 0 0 3px #C5D3FF;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 20px;
            overflow: hidden;
            background: white;
        }
        .users-table th {
            background: #F8FAFF;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e293b;
            border-bottom: 1px solid #E9EFF5;
            text-align: left;
        }
        .users-table td {
            padding: 14px 20px;
            border-bottom: 1px solid #F0F2F8;
            font-size: 0.9rem;
            color: #1E293B;
            vertical-align: middle;
        }
        .btn-action {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-edit {
            background: #FEF3C7;
            color: #D97706;
        }
        .btn-edit:hover {
            background: #FDE68A;
            transform: translateY(-1px);
        }
        .btn-delete {
            background: #FEE2E2;
            color: #DC2626;
        }
        .btn-delete:hover {
            background: #FECACA;
            transform: translateY(-1px);
        }
        .btn-submit {
            background: #1D5CFF;
            color: white;
            padding: 10px 24px;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-submit:hover {
            background: #1140C8;
            transform: translateY(-1px);
        }
        .btn-cancel {
            background: #F1F5F9;
            color: #475569;
            padding: 10px 24px;
            border-radius: 40px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .card-header {
            background: white;
            border-radius: 24px;
            padding: 20px 28px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            border: 1px solid #E9EEF8;
            margin-bottom: 24px;
        }
        .form-card {
            background: white;
            border-radius: 24px;
            padding: 24px 28px;
            border: 1px solid #E9EEF8;
            margin-bottom: 28px;
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            font-size: 0.9rem;
            transition: all 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #1D5CFF;
            box-shadow: 0 0 0 3px rgba(29,92,255,0.1);
        }
        .role-badge {
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .role-admin {
            background: #E8EEFF;
            color: #1D5CFF;
        }
        .role-masyarakat {
            background: #E0F2FE;
            color: #0284C7;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 24px;
            border: 1px dashed #D0DAF0;
            color: #5B6E8C;
        }
        .fade-up { animation: fadeUp 0.4s ease both; }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(10px); }
            to { opacity:1; transform:translateY(0); }
        }
        
        /* Notification Toast Styles */
        .toast-notification {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
            max-width: 380px;
        }
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        .toast-hide {
            animation: slideOutRight 0.3s ease-out forwards;
        }
    </style>
</head>
<body>

<!-- SIDEBAR OVERLAY (mobile) -->
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<?php include "components/sidebar.php" ?>

<!-- TOPBAR / NAVBAR -->
<?php include "components/navbarAdmin.php" ?>

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
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
    
    // Auto close after 5 seconds
    setTimeout(() => {
        const toast = document.getElementById('notificationToast');
        if (toast) {
            toast.classList.add('toast-hide');
            setTimeout(() => {
                if (toast) toast.remove();
            }, 300);
        }
    }, 5000);
</script>
<?php endif; ?>

<!-- MAIN CONTENT -->
<main id="main">
    <div class="p-6 lg:p-8 max-w-7xl mx-auto fade-up">
        
        <!-- Header -->
        <div class="card-header">
            <div>
                <h3 class="font-display text-xl font-bold text-ink-900">Manajemen Users</h3>
                <p class="text-sm text-slate-500 mt-1">Kelola data pengguna sistem, ubah role, atau hapus akun</p>
            </div>
        </div>

        <!-- Form Tambah/Edit User -->
        <div class="form-card">
            <h4 class="font-display font-semibold text-ink-900 text-lg mb-5">
                <?= $editData ? 'Edit User' : 'Tambah User Baru' ?>
            </h4>
            <form method="POST" onsubmit="return validateForm(this)">
                <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">NIK</label>
                       <input 
  type="text" 
  name="nik" 
  id="nik"
  class="form-input" 
  placeholder="Nomor Induk Kependudukan" 
  maxlength="16"
  required 
  value="<?= htmlspecialchars($editData['nik'] ?? '') ?>"
>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                        <input type="text" name="username" class="form-input" placeholder="Username" required value="<?= htmlspecialchars($editData['username'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="fullname" class="form-input" placeholder="Fullname" required value="<?= htmlspecialchars($editData['fullname'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" class="form-input" placeholder="Email" required value="<?= htmlspecialchars($editData['email'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" class="form-input" placeholder="<?= $editData ? 'Kosongkan jika tidak diubah' : 'Password' ?>" <?= !$editData ? 'required' : '' ?>>
                        <?php if($editData): ?>
                            <p class="text-xs text-slate-400 mt-1">*Kosongkan jika tidak ingin mengubah password</p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                        <select name="role" class="form-input" required>
                            <option value="masyarakat" <?= (isset($editData) && $editData['role']=='masyarakat') ? 'selected' : '' ?>>Masyarakat</option>
                            <option value="admin" <?= (isset($editData) && $editData['role']=='admin') ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex gap-3 mt-6">
                    <?php if ($editData): ?>
                        <button type="submit" name="update" class="btn-submit">
                            <i class="fa-regular fa-floppy-disk mr-2"></i> Update User
                        </button>
                        <a href="users.php" class="btn-cancel">
                            <i class="fa-solid fa-xmark mr-1"></i> Batal
                        </a>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn-submit">
                            <i class="fa-solid fa-user-plus mr-2"></i> Tambah User
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Users -->
        <div class="bg-white rounded-2xl border border-slate-border overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>NIK</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-panel/40 transition">
                                    <td class="font-medium"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nik']) ?></td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-r from-azure to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                                <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                            </div>
                                            <span class="font-medium"><?= htmlspecialchars($row['username']) ?></span>
                                        </div>
                                    </div>
                                    <td><?= htmlspecialchars($row['fullname']) ?></div>
                                    <td><?= htmlspecialchars($row['email']) ?></div>
                                    <td>
                                        <span class="role-badge <?= $row['role'] == 'admin' ? 'role-admin' : 'role-masyarakat' ?>">
                                            <?= $row['role'] == 'admin' ? 'Administrator' : 'Masyarakat' ?>
                                        </span>
                                    </div>
                                    <td>
                                        <div class="flex gap-2">
                                            <a href="?edit=<?= $row['id'] ?>" class="btn-action btn-edit">
                                                <i class="fa-regular fa-pen-to-square"></i> Edit
                                            </a>
                                            <a href="?delete=<?= $row['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                <i class="fa-regular fa-trash-can"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="!p-0">
                                    <div class="empty-state">
                                        <i class="fa-regular fa-user text-4xl text-slate-300 mb-3"></i>
                                        <p class="font-medium text-slate-500">Belum ada data user</p>
                                        <p class="text-xs text-slate-400 mt-1">Tambahkan user baru menggunakan form di atas</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    }
    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    }
    // Close sidebar saat klik link di mobile
    document.querySelectorAll('.nav-item').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });
    
    function validateForm(form) {
        const password = form.querySelector('input[name="password"]');
        <?php if (!$editData): ?>
        if (!password.value.trim()) {
            showValidationError('Password harus diisi!');
            return false;
        }
        if (password.value.length < 4) {
            showValidationError('Password minimal 4 karakter!');
            return false;
        }
        <?php endif; ?>
        return true;
    }
    
    function showValidationError(message) {
        const toast = document.createElement('div');
        toast.id = 'notificationToast';
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
</script>

</body>
</html>