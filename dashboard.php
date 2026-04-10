<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// cek role
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Koneksi database
require_once "config/db.php";

// Ambil data statistik dari database
$total_pengaduan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan"))['total'];
$total_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'selesai'"))['total'];
$total_proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'diproses'"))['total'];
$total_baru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'menunggu'"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];

// Ambil data admin dari session
$admin_name = $_SESSION['fullname'] ?? 'Administrator';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
$admin_role = $_SESSION['role'] ?? 'Super Admin';

// Hitung persentase penyelesaian
$persentase_selesai = $total_pengaduan > 0 ? round(($total_selesai / $total_pengaduan) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Admin — Pengaduan Masyarakat</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>

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
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; }
    body {
      font-family: 'DM Sans', sans-serif;
      background: #EEF2FF;
      color: #112038;
      overflow-x: hidden;
    }

    /* ── Sidebar ── */
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
      content:'';
      position:absolute; inset:0;
      background: radial-gradient(ellipse at 20% 10%, rgba(29,92,255,0.18) 0%, transparent 60%),
                  radial-gradient(ellipse at 80% 90%, rgba(29,92,255,0.10) 0%, transparent 50%);
      pointer-events:none;
    }
    #sidebar-overlay {
      display:none;
      position:fixed; inset:0;
      background:rgba(6,12,26,0.55);
      z-index:40;
      backdrop-filter:blur(3px);
    }

    /* Sidebar grid texture */
    #sidebar::after {
      content:'';
      position:absolute; inset:0;
      background-image: linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                        linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
      background-size: 32px 32px;
      pointer-events:none;
    }

    /* Nav items */
    .nav-item {
      display:flex; align-items:center; gap:12px;
      padding:10px 18px;
      border-radius:12px;
      font-size:0.875rem;
      font-weight:500;
      color:rgba(197,211,255,0.7);
      cursor:pointer;
      transition: all 0.2s ease;
      position:relative;
      text-decoration:none;
    }
    .nav-item:hover { background:rgba(29,92,255,0.15); color:#fff; }
    .nav-item.active {
      background: linear-gradient(90deg, rgba(29,92,255,0.35), rgba(29,92,255,0.12));
      color:#fff;
      box-shadow: inset 3px 0 0 #1D5CFF;
    }
    .nav-item .icon-wrap {
      width:34px; height:34px;
      border-radius:10px;
      display:flex; align-items:center; justify-content:center;
      flex-shrink:0;
      background:rgba(255,255,255,0.07);
      font-size:0.8rem;
      transition:background 0.2s;
    }
    .nav-item.active .icon-wrap { background:rgba(29,92,255,0.5); }
    .nav-item:hover .icon-wrap  { background:rgba(29,92,255,0.3); }

    /* ── Topbar ── */
    #topbar {
      position:fixed;
      top:0; left:260px; right:0;
      height:64px;
      background:rgba(238,242,255,0.92);
      backdrop-filter:blur(14px);
      border-bottom:1px solid #DDE4F5;
      display:flex; align-items:center;
      padding:0 28px;
      z-index:30;
      transition: left 0.35s cubic-bezier(0.4,0,0.2,1);
    }

    /* ── Main ── */
    #main {
      margin-left:260px;
      padding-top:64px;
      min-height:100vh;
      transition: margin-left 0.35s cubic-bezier(0.4,0,0.2,1);
    }

    /* ── Stat Cards ── */
    .stat-card {
      position:relative;
      border-radius:20px;
      padding:28px 28px 24px;
      overflow:hidden;
      cursor:default;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .stat-card:hover { transform:translateY(-5px); }
    .stat-card .card-bg-icon {
      position:absolute;
      right:-12px; bottom:-12px;
      font-size:6rem;
      opacity:0.08;
      pointer-events:none;
    }
    .stat-card .badge {
      display:inline-flex; align-items:center; gap:5px;
      padding:3px 10px;
      border-radius:99px;
      font-size:0.7rem;
      font-weight:600;
    }

    /* Card themes */
    .card-total {
      background: linear-gradient(135deg, #1D5CFF 0%, #1140C8 100%);
      box-shadow: 0 12px 40px rgba(29,92,255,0.35);
      color:#fff;
    }
    .card-proses {
      background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
      box-shadow: 0 12px 40px rgba(245,158,11,0.30);
      color:#fff;
    }
    .card-selesai {
      background: linear-gradient(135deg, #10B981 0%, #059669 100%);
      box-shadow: 0 12px 40px rgba(16,185,129,0.30);
      color:#fff;
    }
    .card-users {
      background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%);
      box-shadow: 0 12px 40px rgba(139,92,246,0.30);
      color:#fff;
    }

    /* Count-up animation */
    .counter { display:inline-block; }

    /* ── Sparkline ── */
    .sparkline-bar {
      width:5px;
      border-radius:3px;
      background:rgba(255,255,255,0.3);
      transition:height 0.8s cubic-bezier(0.4,0,0.2,1);
    }
    .sparkline-bar.active { background:rgba(255,255,255,0.85); }

    /* ── Page fade-in ── */
    @keyframes fadeUp {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }
    .fade-up { animation: fadeUp 0.5s ease both; }

    /* ── Avatar ring ── */
    .avatar-ring {
      width:38px; height:38px; border-radius:50%;
      background: linear-gradient(135deg, #1D5CFF, #8B5CF6);
      display:flex; align-items:center; justify-content:center;
      font-weight:700; font-size:0.8rem; color:#fff;
      box-shadow:0 0 0 3px #C5D3FF;
    }

    /* ── Notification dot ── */
    .notif-dot {
      width:8px; height:8px;
      border-radius:50%;
      background:#EF4444;
      border:2px solid #EEF2FF;
      position:absolute; top:6px; right:6px;
    }

    /* Nav label */
    .nav-label { white-space:nowrap; }

    /* ── Mobile responsive ── */
    @media (max-width:768px) {
      #sidebar { transform: translateX(-100%); }
      #sidebar.open { transform: translateX(0); }
      #sidebar-overlay.show { display:block; }
      #topbar { left:0 !important; }
      #main   { margin-left:0 !important; }
    }

    /* Custom scrollbar */
    #sidebar-nav::-webkit-scrollbar { width:4px; }
    #sidebar-nav::-webkit-scrollbar-thumb { background:rgba(29,92,255,0.4); border-radius:4px; }
    ::-webkit-scrollbar { width:6px; }
    ::-webkit-scrollbar-thumb { background:#C5D3FF; border-radius:6px; }
  </style>
</head>
<body>

<!-- ════════════════════════ SIDEBAR ════════════════════════ -->
<div id="sidebar-overlay" onclick="closeSidebar()"></div>



<!-- ════════════════════════ TOPBAR ════════════════════════ -->
<?php include "components/sidebar.php" ?>
<?php include "components/navbarAdmin.php" ?>

<!-- ════════════════════════ MAIN CONTENT ════════════════════════ -->
<main id="main">
  <div class="p-6 lg:p-8 max-w-6xl">

    <!-- Welcome Banner -->
    <div class="fade-up mb-8 relative overflow-hidden rounded-2xl px-7 py-6"
         style="background:linear-gradient(120deg,#0C1829 0%,#172848 50%,#1D5CFF 100%);animation-delay:0.05s">
      <div class="absolute inset-0" style="background-image:linear-gradient(rgba(255,255,255,0.03)1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03)1px,transparent 1px);background-size:40px 40px;"></div>
      <div class="absolute right-0 top-0 bottom-0 w-48 flex items-center justify-center opacity-5">
        <i class="fa-solid fa-landmark text-white" style="font-size:9rem"></i>
      </div>
      <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <p class="text-azure-dim text-xs font-semibold tracking-widest uppercase mb-1">Selamat Datang Kembali 👋</p>
          <h2 class="font-display text-white text-xl font-bold"><?= htmlspecialchars($admin_name) ?></h2>
          <p class="text-blue-300 text-sm mt-1 font-light">Berikut ringkasan aktivitas pengaduan hari ini.</p>
        </div>
        <div class="bg-white/10 rounded-full px-4 py-2 backdrop-blur">
          <span class="text-white text-sm font-medium">Tingkat Penyelesaian: <?= $persentase_selesai ?>%</span>
        </div>
      </div>
    </div>

    <!-- ── STAT CARDS ── -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

      <!-- Card 1: Total Pengaduan -->
      <div class="stat-card card-total fade-up" style="animation-delay:0.1s">
        <i class="fa-solid fa-inbox card-bg-icon"></i>
        <div class="flex items-start justify-between mb-5">
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:rgba(255,255,255,0.18)">
            <i class="fa-solid fa-inbox text-white text-lg"></i>
          </div>
        </div>
        <div class="font-display font-bold text-4xl text-white mb-1 leading-none">
          <span class="counter" data-target="<?= $total_pengaduan ?>" data-duration="1800">0</span>
        </div>
        <div class="text-white/70 text-sm font-medium mt-1">Total Pengaduan</div>
        <div class="flex items-end gap-1 mt-4 h-8">
          <?php
          // Generate random heights for sparkline based on actual data
          $heights = [40, 60, 45, 75, 55, 80, 100];
          foreach ($heights as $i => $h) {
            $activeClass = $i === count($heights)-1 ? 'active' : '';
            echo "<div class='sparkline-bar $activeClass' style='height:{$h}%'></div>";
          }
          ?>
        </div>
      </div>

      <!-- Card 2: Sedang Diproses -->
      <div class="stat-card card-proses fade-up" style="animation-delay:0.18s">
        <i class="fa-solid fa-hourglass-half card-bg-icon"></i>
        <div class="flex items-start justify-between mb-5">
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:rgba(255,255,255,0.18)">
            <i class="fa-solid fa-hourglass-half text-white text-lg"></i>
          </div>
        </div>
        <div class="font-display font-bold text-4xl text-white mb-1 leading-none">
          <span class="counter" data-target="<?= $total_proses ?>" data-duration="1600">0</span>
        </div>
        <div class="text-white/70 text-sm font-medium mt-1">Sedang Diproses</div>
        <div class="flex items-end gap-1 mt-4 h-8">
          <?php foreach ($heights as $i => $h): ?>
            <div class="sparkline-bar <?= $i === count($heights)-1 ? 'active' : '' ?>" style="height:<?= $h ?>%"></div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Card 3: Pengaduan Selesai -->
      <div class="stat-card card-selesai fade-up" style="animation-delay:0.26s">
        <i class="fa-solid fa-circle-check card-bg-icon"></i>
        <div class="flex items-start justify-between mb-5">
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:rgba(255,255,255,0.18)">
            <i class="fa-solid fa-circle-check text-white text-lg"></i>
          </div>
        </div>
        <div class="font-display font-bold text-4xl text-white mb-1 leading-none">
          <span class="counter" data-target="<?= $total_selesai ?>" data-duration="2000">0</span>
        </div>
        <div class="text-white/70 text-sm font-medium mt-1">Pengaduan Selesai</div>
        <div class="flex items-end gap-1 mt-4 h-8">
          <?php foreach ($heights as $i => $h): ?>
            <div class="sparkline-bar <?= $i === count($heights)-1 ? 'active' : '' ?>" style="height:<?= $h ?>%"></div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Card 4: Total Pengguna -->
      <div class="stat-card card-users fade-up" style="animation-delay:0.34s">
        <i class="fa-solid fa-users card-bg-icon"></i>
        <div class="flex items-start justify-between mb-5">
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:rgba(255,255,255,0.18)">
            <i class="fa-solid fa-users text-white text-lg"></i>
          </div>
        </div>
        <div class="font-display font-bold text-4xl text-white mb-1 leading-none">
          <span class="counter" data-target="<?= $total_users ?>" data-duration="2200">0</span>
        </div>
        <div class="text-white/70 text-sm font-medium mt-1">Total Pengguna</div>
        <div class="flex items-end gap-1 mt-4 h-8">
          <?php foreach ($heights as $i => $h): ?>
            <div class="sparkline-bar <?= $i === count($heights)-1 ? 'active' : '' ?>" style="height:<?= $h ?>%"></div>
          <?php endforeach; ?>
        </div>
      </div>

    </div><!-- /grid -->

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-border">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-display font-bold text-navy-900">Pengaduan Baru</h3>
          <i class="fa-solid fa-chart-line text-cobalt"></i>
        </div>
        <div class="flex items-baseline gap-2">
          <span class="font-display text-3xl font-bold text-navy-900"><?= number_format($total_baru) ?></span>
          <span class="text-sm text-gray-500">pengaduan</span>
        </div>
        <p class="text-xs text-gray-400 mt-2">Menunggu verifikasi admin</p>
        <div class="mt-4 h-2 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full w-[<?= $total_pengaduan > 0 ? round(($total_baru / $total_pengaduan) * 100) : 0 ?>%] bg-gradient-to-r from-blue-400 to-blue-500 rounded-full"></div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-border">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-display font-bold text-navy-900">Tingkat Penyelesaian</h3>
          <i class="fa-solid fa-chart-pie text-cobalt"></i>
        </div>
        <div class="flex items-baseline gap-2">
          <span class="font-display text-3xl font-bold text-navy-900"><?= $persentase_selesai ?>%</span>
          <span class="text-sm text-gray-500">selesai</span>
        </div>
        <p class="text-xs text-gray-400 mt-2">Dari <?= number_format($total_pengaduan) ?> total pengaduan</p>
        <div class="mt-4 h-2 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full w-[<?= $persentase_selesai ?>%] bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full"></div>
        </div>
      </div>
    </div>

    <div class="h-8"></div>
  </div>
</main>

<script>
  // ── Date ────────────────────────────────────────────────────────
  const hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const bln  = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  const now  = new Date();
  document.getElementById('current-date').textContent =
    `${hari[now.getDay()]}, ${now.getDate()} ${bln[now.getMonth()]} ${now.getFullYear()}`;

  // ── Sidebar Toggle ──────────────────────────────────────────────
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebar-overlay').classList.toggle('show');
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').classList.remove('show');
  }

  // ── Nav item active ─────────────────────────────────────────────
  

  // ── Counter animation ───────────────────────────────────────────
  function animateCounter(el) {
    const target   = parseInt(el.dataset.target, 10);
    const duration = parseInt(el.dataset.duration, 10) || 1500;
    const start    = performance.now();
    function step(now) {
      const p = Math.min((now - start) / duration, 1);
      const ease = 1 - Math.pow(1 - p, 3); // easeOutCubic
      el.textContent = Math.floor(ease * target).toLocaleString('id-ID');
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  // Trigger on load
  window.addEventListener('load', () => {
    document.querySelectorAll('.counter').forEach(el => {
      setTimeout(() => animateCounter(el), 400);
    });
  });

  // ── Dropdown Toggle ─────────────────────────────────────────────
  function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
      dropdown.classList.toggle('hidden');
    }
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const avatarButton = document.querySelector('.avatar-ring')?.parentElement;
    
    if (dropdown && !dropdown.classList.contains('hidden')) {
      if (avatarButton && !avatarButton.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
      }
    }
  });
</script>
</body>
</html>