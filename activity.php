<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// cek role
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activity Log - Dashboard Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            ink:    { 950:'#060C1A', 900:'#0C1829', 800:'#112038', 700:'#172848' },
            azure:  { DEFAULT:'#1D5CFF', soft:'#3B74FF', pale:'#E8EEFF', dim:'#C5D3FF' },
            slate:  { panel:'#F0F4FF', border:'#DDE4F5' },
          },
          fontFamily: {
            display: ['Syne', 'sans-serif'],
            body:    ['DM Sans', 'sans-serif'],
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
    /* Sidebar styling (sama seperti referensi) */
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
      #main   { margin-left:0 !important; }
    }
    .avatar-ring {
      width:38px; height:38px; border-radius:50%;
      background: linear-gradient(135deg, #1D5CFF, #8B5CF6);
      display:flex; align-items:center; justify-content:center;
      font-weight:700; font-size:0.8rem; color:#fff;
      box-shadow:0 0 0 3px #C5D3FF;
    }
    /* table modern */
    .activity-table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 20px;
      overflow: hidden;
      background: white;
      box-shadow: 0 4px 14px rgba(0,0,0,0.02), 0 1px 3px rgba(0,0,0,0.05);
    }
    .activity-table th {
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
    .activity-table td {
      padding: 14px 20px;
      border-bottom: 1px solid #F0F2F8;
      font-size: 0.9rem;
      color: #1E293B;
      vertical-align: middle;
    }
    .btn-delete-all {
      background: #EF4444; color: white; border: none; padding: 10px 20px; border-radius: 40px;
      font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px;
      transition: all 0.2s; cursor: pointer; box-shadow: 0 2px 6px rgba(239,68,68,0.2);
    }
    .btn-delete-all:hover { background: #DC2626; transform: translateY(-1px); }
    .empty-state {
      text-align: center; padding: 60px 20px; background: white; border-radius: 24px;
      border: 1px dashed #D0DAF0; color: #5B6E8C;
    }
    .card-header {
      background: white; border-radius: 24px; padding: 20px 28px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #E9EEF8;
      margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;
      flex-wrap: wrap;
    }
    .fade-up { animation: fadeUp 0.4s ease both; }
    @keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
  </style>
</head>
<body>

<?php

require_once "config/db.php";


$admin_name = $_SESSION['username'] ?? 'Administrator';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
$admin_role = $_SESSION['role'] ?? 'Super Admin';

// DELETE ALL DATA (activity)
if (isset($_POST['delete_all'])) {
    $delete_query = "DELETE FROM activity";
    mysqli_query($conn, $delete_query);
    header("Location: activity.php");
    exit;
}

// FETCH ACTIVITY DATA
$result = mysqli_query($conn, "SELECT * FROM activity ORDER BY id DESC");
?>

<!-- SIDEBAR OVERLAY (mobile) -->
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR (sama persis dengan referensi) -->
<?php include "components/sidebar.php" ?>
<?php include "components/navbarAdmin.php" ?>

<!-- MAIN CONTENT -->
<main id="main">
  <div class="p-6 lg:p-8 max-w-7xl mx-auto fade-up">
    <!-- Card header + delete button -->
    <div class="card-header">
      <div>
        <h3 class="font-display text-xl font-bold text-ink-900">Data Activity</h3>
        <p class="text-sm text-slate-500 mt-1">Semua perubahan, log, dan riwayat aksi tercatat di sini.</p>
      </div>
      <form method="POST" onsubmit="return confirm('Yakin ingin menghapus SEMUA data activity? Aksi ini permanen.')">
       
      </form>
    </div>

    <!-- Tabel Activity dengan styling modern -->
    <div class="bg-white rounded-2xl border border-slate-border overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="activity-table">
          <thead>
            <tr>
              <th style="width: 60px;">No</th>
              <th>Nama Tabel</th>
              <th>Deskripsi / Aktivitas</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
              <?php $no = 1; ?>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-slate-panel/40 transition">
                  <td class="font-medium text-ink-800"><?= $no++ ?></td>
                  <td>
                    <div class="flex items-center gap-2">
                      <div class="w-7 h-7 rounded-lg bg-azure-pale/30 text-azure flex items-center justify-center">
                        <i class="fa-solid fa-table-cells-large text-xs"></i>
                      </div>
                      <span class="font-mono text-sm font-medium"><?= htmlspecialchars($row['table_name']) ?></span>
                    </div>
                  </td>
                  <td class="text-slate-700"><?= htmlspecialchars($row['description']) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="!p-0">
                  <div class="empty-state">
                    <i class="fa-regular fa-clipboard text-4xl text-slate-300 mb-3"></i>
                    <p class="font-medium text-slate-500">Belum ada data aktivitas</p>
                    <p class="text-xs text-slate-400 mt-1">Setiap perubahan pada data pengaduan akan tercatat disini</p>
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
</script>
</body>
</html>