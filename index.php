<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi database
require_once "config/db.php";

// Ambil data pengaduan dari database dengan JOIN users
$query = "SELECT pengaduan.*, users.username, users.fullname 
          FROM pengaduan 
          JOIN users ON pengaduan.user_id = users.id
          WHERE pengaduan.status IN ('selesai', 'diproses')
          ORDER BY pengaduan.date DESC";

$result = mysqli_query($conn, $query);

// Hitung statistik
$total_pengaduan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan"))['total'];
$total_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'selesai'"))['total'];
$total_proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE status = 'diproses'"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];

// Cek status login
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true;

// Fungsi helper
function getStatusBadgeClass($status) {
    switch(strtolower($status)) {
        case 'selesai':
            return 'bg-emerald-100 text-emerald-700';
        case 'diproses':
            return 'bg-amber-100 text-amber-700';
        case 'baru':
            return 'bg-blue-100 text-blue-700';
        default:
            return 'bg-gray-100 text-gray-700';
    }
}

function getStatusDotClass($status) {
    switch(strtolower($status)) {
        case 'selesai':
            return 'bg-emerald-500';
        case 'diproses':
            return 'bg-amber-500';
        case 'baru':
            return 'bg-blue-500';
        default:
            return 'bg-gray-500';
    }
}

function getStatusText($status) {
    switch(strtolower($status)) {
        case 'selesai':
            return 'Selesai';
        case 'diproses':
            return 'Diproses';
        case 'baru':
            return 'Baru';
        default:
            return ucfirst($status);
    }
}

function getProgressWidth($status) {
    switch(strtolower($status)) {
        case 'selesai':
            return '100%';
        case 'diproses':
            return '60%';
        case 'baru':
            return '10%';
        default:
            return '0%';
    }
}

function getGradientColor($status) {
    switch(strtolower($status)) {
        case 'selesai':
            return 'from-emerald-400 to-emerald-500';
        case 'diproses':
            return 'from-amber-400 to-amber-500';
        case 'baru':
            return 'from-blue-400 to-blue-500';
        default:
            return 'from-gray-400 to-gray-500';
    }
}

function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    return substr($initials, 0, 2);
}

function truncateText($text, $limit = 150) {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }
    return $text;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pengaduan Masyarakat — Layanan Publik Digital</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

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
    * { box-sizing: border-box; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F7F9FF; color: #1a1a2e; }

    .navbar-scrolled { background: rgba(255,255,255,0.97) !important; box-shadow: 0 2px 24px rgba(27,79,216,0.10) !important; }
    #mobile-menu { max-height: 0; overflow: hidden; transition: max-height 0.4s cubic-bezier(0.4,0,0.2,1), opacity 0.3s; opacity: 0; }
    #mobile-menu.open { max-height: 480px; opacity: 1; }

    .hero-bg {
      background: linear-gradient(135deg, #0A1628 0%, #0F2044 40%, #1B4FD8 100%);
      position: relative;
      overflow: hidden;
    }
    .hero-bg::before {
      content: '';
      position: absolute; inset: 0;
      background: radial-gradient(ellipse at 70% 50%, rgba(27,79,216,0.45) 0%, transparent 65%),
                  radial-gradient(ellipse at 10% 80%, rgba(201,150,58,0.15) 0%, transparent 50%);
    }
    .hero-grid {
      position: absolute; inset: 0;
      background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                        linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
      background-size: 48px 48px;
    }

    .badge-float { animation: floatY 3.5s ease-in-out infinite; }
    @keyframes floatY { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }

    .complaint-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
    .complaint-card:hover { transform: translateY(-6px); box-shadow: 0 20px 48px rgba(27,79,216,0.14); }

    .stat-box { background: linear-gradient(135deg, #fff 0%, #EBF0FD 100%); }
    .wave-divider { line-height: 0; }

    .btn-primary { position: relative; overflow: hidden; }
    .btn-primary::after {
      content: '';
      position: absolute; inset: 0;
      background: rgba(255,255,255,0.15);
      transform: scaleX(0); transform-origin: left;
      transition: transform 0.3s ease;
    }
    .btn-primary:hover::after { transform: scaleX(1); }

    .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    .footer-bg { background: linear-gradient(160deg, #0A1628 0%, #0F2044 100%); }

    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #1B4FD8; border-radius: 9px; }

    .complaint-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .line-clamp-3 {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    /* Modal Styles - Tanpa bg-black */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }
    .modal-overlay.active {
      opacity: 1;
      visibility: visible;
    }
    .modal-container {
      background: white;
      border-radius: 24px;
      max-width: 600px;
      width: 90%;
      max-height: 85vh;
      overflow-y: auto;
      transform: scale(0.95);
      transition: transform 0.3s ease;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .modal-overlay.active .modal-container {
      transform: scale(1);
    }
    .modal-container::-webkit-scrollbar {
      width: 4px;
    }
    .modal-container::-webkit-scrollbar-track {
      background: #f1f5f9;
    }
    .modal-container::-webkit-scrollbar-thumb {
      background: #1B4FD8;
      border-radius: 4px;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<?php include "components/navbar.php" ?>

<!-- HERO -->
<section id="home" class="hero-bg min-h-screen flex items-center pt-16">
  <div class="hero-grid"></div>
  <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
      <div class="text-white">
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/20 rounded-full px-4 py-1.5 text-xs font-semibold text-blue-200 mb-6 tracking-wide">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          Sistem Aktif — Layanan 24 Jam
        </div>
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
          Suara Anda,<br/>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-gold-light">Perubahan Nyata</span>
        </h1>
        <p class="text-blue-100 text-base lg:text-lg leading-relaxed mb-8 max-w-lg font-light">
          Platform pengaduan masyarakat resmi yang transparan, aman, dan responsif. Laporkan masalah di lingkungan Anda dan pantau progres penanganannya secara real-time.
        </p>
        <div class="flex flex-wrap gap-4">
          <?php if ($isLoggedIn): ?>
            <a href="create_pengaduan.php" class="btn-primary group inline-flex items-center gap-2.5 bg-white text-cobalt font-bold px-7 py-3.5 rounded-2xl hover:shadow-2xl hover:shadow-white/20 transition-all duration-300 text-sm">
              <i class="fa-solid fa-pen-to-square"></i>
              Buat Pengaduan
              <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
            </a>
          <?php else: ?>
            <a href="login.php" class="btn-primary group inline-flex items-center gap-2.5 bg-white text-cobalt font-bold px-7 py-3.5 rounded-2xl hover:shadow-2xl hover:shadow-white/20 transition-all duration-300 text-sm">
              <i class="fa-solid fa-right-to-bracket"></i>
              Login Sekarang
              <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
            </a>
          <?php endif; ?>
        </div>
        <div class="flex flex-wrap gap-6 mt-10 pt-10 border-t border-white/10">
          <div>
            <div class="font-display text-2xl font-bold text-white"><?= number_format($total_pengaduan) ?></div>
            <div class="text-blue-300 text-xs font-medium mt-0.5">Pengaduan Masuk</div>
          </div>
          <div class="w-px bg-white/10"></div>
          <div>
            <div class="font-display text-2xl font-bold text-white"><?= number_format($total_selesai) ?></div>
            <div class="text-blue-300 text-xs font-medium mt-0.5">Kasus Diselesaikan</div>
          </div>
        </div>
      </div>
      <div class="hidden lg:flex justify-center relative">
        <div class="badge-float relative">
          <div class="bg-white rounded-full shadow-2xl p-6 w-32 h-32 flex items-center justify-center relative">
            <div class="w-16 h-16 bg-cobalt/10 rounded-full flex items-center justify-center">
              <i class="fa-solid fa-landmark text-cobalt text-3xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="wave-divider absolute bottom-0 left-0 right-0">
    <svg viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
      <path d="M0,48 C360,80 1080,0 1440,48 L1440,80 L0,80 Z" fill="#F7F9FF"/>
    </svg>
  </div>
</section>

<!-- STATS STRIP -->
<section class="py-8 bg-white border-y border-blue-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-cobalt/10 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-inbox text-cobalt text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900"><?= number_format($total_pengaduan) ?></div>
          <div class="text-gray-500 text-xs font-medium">Total Pengaduan</div>
        </div>
      </div>
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900"><?= number_format($total_selesai) ?></div>
          <div class="text-gray-500 text-xs font-medium">Kasus Selesai</div>
        </div>
      </div>
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-hourglass-half text-amber-500 text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900"><?= number_format($total_proses) ?></div>
          <div class="text-gray-500 text-xs font-medium">Sedang Diproses</div>
        </div>
      </div>
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-users text-cobalt text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900"><?= number_format($total_users) ?>+</div>
          <div class="text-gray-500 text-xs font-medium">Pengguna Terdaftar</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PENGADUAN LIST -->
<section id="pengaduan" class="py-20 bg-[#F7F9FF]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="reveal text-center mb-14">
      <div class="inline-flex items-center gap-2 bg-cobalt/10 text-cobalt rounded-full px-4 py-1.5 text-xs font-bold mb-4 tracking-widest uppercase">
        <i class="fa-solid fa-list-ul"></i> Daftar Pengaduan
      </div>
      <h2 class="font-display text-3xl sm:text-4xl font-bold text-navy-900 mb-4">Pengaduan Terkini</h2>
      <p class="text-gray-500 text-base max-w-lg mx-auto">Pantau seluruh laporan pengaduan masyarakat yang masuk, sedang diproses, maupun yang telah diselesaikan.</p>
    </div>

    <div class="reveal flex flex-wrap gap-2 justify-center mb-10">
      <button onclick="filterCards('all')" class="filter-btn px-4 py-2 text-sm font-semibold rounded-xl border transition-all duration-200">Semua</button>
      <button onclick="filterCards('diproses')" class="filter-btn px-4 py-2 text-sm font-semibold rounded-xl border border-amber-200 text-amber-600 hover:bg-amber-500 hover:text-white transition-all duration-200">Diproses</button>
      <button onclick="filterCards('selesai')" class="filter-btn px-4 py-2 text-sm font-semibold rounded-xl border border-emerald-200 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all duration-200">Selesai</button>
    </div>

    <div id="cards-container" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($pengaduan = mysqli_fetch_assoc($result)): 
          $status = strtolower($pengaduan['status'] ?? 'baru');
          $progressWidth = getProgressWidth($status);
          $gradientColor = getGradientColor($status);
          $initials = getInitials($pengaduan['fullname']);
          $imagePath = !empty($pengaduan['img']) ? 'uploads/' . $pengaduan['img'] : null;
          $shortDescription = truncateText($pengaduan['description'] ?? 'Deskripsi pengaduan', 150);
        ?>
          <div class="complaint-card reveal bg-white rounded-2xl border border-blue-50 shadow-sm overflow-hidden" data-status="<?= $status ?>">
            <div class="h-2 bg-gradient-to-r <?= $gradientColor ?>"></div>
            
            <?php if ($imagePath && file_exists($imagePath)): ?>
              <div class="relative">
                <img src="<?= htmlspecialchars($imagePath) ?>" alt="Gambar Pengaduan" class="complaint-img w-full h-48 object-cover">
                <div class="absolute top-3 right-3">
                  <span class="<?= getStatusBadgeClass($status) ?> text-xs font-bold px-3 py-1 rounded-full shadow-md">
                    <i class="fa-regular fa-clock mr-1"></i><?= getStatusText($status) ?>
                  </span>
                </div>
              </div>
            <?php endif; ?>
            
            <div class="p-6">
              <?php if (!$imagePath || !file_exists($imagePath)): ?>
                <div class="flex items-start justify-between mb-4">
                  <span class="<?= getStatusBadgeClass($status) ?> text-xs font-bold px-3 py-1 rounded-full">
                    <i class="fa-regular fa-clock mr-1"></i><?= getStatusText($status) ?>
                  </span>
                  <div class="flex items-center gap-1.5 text-xs font-semibold <?= $status == 'selesai' ? 'text-emerald-600' : ($status == 'diproses' ? 'text-amber-600' : 'text-blue-600') ?>">
                    <span class="w-2 h-2 rounded-full <?= getStatusDotClass($status) ?> <?= $status != 'selesai' ? 'animate-pulse' : '' ?>"></span>
                    <?= getStatusText($status) ?>
                  </div>
                </div>
              <?php endif; ?>
              
              <h3 class="font-display font-bold text-navy-900 text-lg mb-2 line-clamp-2"><?= htmlspecialchars($pengaduan['title'] ?? 'Judul Pengaduan') ?></h3>
              <p class="text-gray-500 text-sm leading-relaxed mb-4"><?= htmlspecialchars($shortDescription) ?></p>
              
              <?php if (!empty($pengaduan['location'])): ?>
                <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
                  <i class="fa-solid fa-location-dot text-cobalt"></i>
                  <span><?= htmlspecialchars($pengaduan['location']) ?></span>
                </div>
              <?php endif; ?>
              
              <div class="h-1.5 bg-gray-100 rounded-full mb-4 overflow-hidden">
                <div class="h-full w-[<?= $progressWidth ?>] bg-gradient-to-r <?= $gradientColor ?> rounded-full"></div>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-r <?= $gradientColor ?> rounded-full flex items-center justify-center">
                    <span class="text-white text-xs font-bold"><?= $initials ?></span>
                  </div>
                  <div>
                    <span class="text-xs text-gray-500 font-medium block"><?= htmlspecialchars($pengaduan['username'] ?? 'Pengguna') ?></span>
                  </div>
                </div>
                <span class="text-xs text-gray-400"><i class="fa-regular fa-calendar mr-1"></i><?= date('d M Y', strtotime($pengaduan['date'])) ?></span>
              </div>
            </div>
            
            <div class="px-6 py-3 border-t border-blue-50 bg-blue-50/40 flex justify-between items-center">
              <span class="text-xs text-gray-400"><i class="fa-solid fa-hashtag mr-1"></i>ADU-<?= str_pad($pengaduan['id'], 4, '0', STR_PAD_LEFT) ?></span>
              <button onclick="openModal(<?= htmlspecialchars(json_encode($pengaduan)) ?>)" class="text-xs font-bold text-cobalt hover:underline">
                <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Detail
              </button>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-span-full text-center py-12">
          <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
          <p class="text-gray-500">Belum ada pengaduan. Jadilah yang pertama melaporkan!</p>
          <?php if ($isLoggedIn): ?>
            <a href="create-pengaduan.php" class="inline-block mt-4 text-cobalt font-semibold hover:underline">Buat Pengaduan →</a>
          <?php else: ?>
            <a href="login.php" class="inline-block mt-4 text-cobalt font-semibold hover:underline">Login untuk membuat pengaduan →</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- MODAL POPUP DETAIL PENGADUAN (tanpa bg-black) -->
<div id="detailModal" class="modal-overlay">
  <div class="modal-container">
    <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl">
      <h3 class="font-display font-bold text-xl text-navy-900">Detail Pengaduan</h3>
      <button onclick="closeModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
        <i class="fa-solid fa-times text-gray-500"></i>
      </button>
    </div>
    <div class="p-6 space-y-4" id="modalContent">
      <!-- Content will be filled by JavaScript -->
    </div>
  </div>
</div>

<!-- TENTANG -->
<section id="tentang" class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-2 gap-16 items-center">
      <div class="reveal order-2 lg:order-1">
        <div class="relative">
          <div class="bg-gradient-to-br from-cobalt/10 to-navy-800/5 rounded-3xl p-8">
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-white rounded-2xl p-5 shadow-sm border border-blue-50">
                <i class="fa-solid fa-shield-check text-cobalt text-2xl mb-3"></i>
                <h4 class="font-bold text-navy-900 text-sm mb-1">Aman & Terenkripsi</h4>
                <p class="text-gray-500 text-xs">Data Anda terlindungi dengan enkripsi tingkat militer</p>
              </div>
              <div class="bg-white rounded-2xl p-5 shadow-sm border border-blue-50">
                <i class="fa-solid fa-eye text-emerald-500 text-2xl mb-3"></i>
                <h4 class="font-bold text-navy-900 text-sm mb-1">100% Transparan</h4>
                <p class="text-gray-500 text-xs">Setiap proses dapat dipantau secara real-time</p>
              </div>
              <div class="bg-white rounded-2xl p-5 shadow-sm border border-blue-50">
                <i class="fa-solid fa-bolt text-amber-500 text-2xl mb-3"></i>
                <h4 class="font-bold text-navy-900 text-sm mb-1">Respons Cepat</h4>
                <p class="text-gray-500 text-xs">Rata-rata respons dalam 1×24 jam kerja</p>
              </div>
              <div class="bg-gradient-to-br from-cobalt to-navy-700 rounded-2xl p-5 text-white">
                <i class="fa-solid fa-star text-yellow-300 text-2xl mb-3"></i>
                <div class="font-display text-3xl font-bold">4.9<span class="text-lg text-blue-200">/5</span></div>
                <p class="text-blue-200 text-xs mt-1">Rating Kepuasan Pengguna</p>
              </div>
            </div>
          </div>
          <div class="absolute -top-4 -right-4 w-16 h-16 bg-gold rounded-2xl flex items-center justify-center shadow-lg">
            <i class="fa-solid fa-award text-white text-xl"></i>
          </div>
        </div>
      </div>
      <div class="reveal order-1 lg:order-2">
        <div class="inline-flex items-center gap-2 bg-cobalt/10 text-cobalt rounded-full px-4 py-1.5 text-xs font-bold mb-4 tracking-widest uppercase">
          <i class="fa-solid fa-circle-info"></i> Tentang Kami
        </div>
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-navy-900 mb-5">Platform Pengaduan yang Bisa Dipercaya</h2>
        <p class="text-gray-500 leading-relaxed mb-6 text-base">Kami hadir sebagai jembatan antara masyarakat dan pemerintah. Dengan teknologi modern dan komitmen terhadap transparansi, setiap suara Anda akan didengar dan ditindaklanjuti.</p>
        <div class="space-y-4 mb-8">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-cobalt/10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
              <i class="fa-solid fa-check-double text-cobalt"></i>
            </div>
            <div>
              <h4 class="font-bold text-navy-900 text-sm mb-1">Proses Terstandarisasi</h4>
              <p class="text-gray-500 text-sm">Setiap pengaduan melewati verifikasi, penugasan, dan penyelesaian yang terstruktur.</p>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
              <i class="fa-solid fa-bell text-emerald-600"></i>
            </div>
            <div>
              <h4 class="font-bold text-navy-900 text-sm mb-1">Notifikasi Real-time</h4>
              <p class="text-gray-500 text-sm">Terima pembaruan status pengaduan melalui SMS atau email.</p>
            </div>
          </div>
        </div>
       
      </div>
    </div>
  </div>
</section>

<!-- CTA BANNER -->
<section class="py-16 bg-[#F7F9FF]">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="reveal relative overflow-hidden bg-gradient-to-r from-navy-900 to-cobalt rounded-3xl p-10 text-center">
      <div class="absolute inset-0 opacity-10" style="background-image:linear-gradient(rgba(255,255,255,0.1) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.1) 1px,transparent 1px);background-size:32px 32px;"></div>
      <div class="relative z-10">
        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-5 backdrop-blur">
          <i class="fa-solid fa-pen-to-square text-white text-2xl"></i>
        </div>
        <h2 class="font-display text-3xl font-bold text-white mb-3">Ada Masalah di Sekitar Anda?</h2>
        <p class="text-blue-200 mb-8 text-base max-w-lg mx-auto">Jangan diam. Laporkan sekarang dan biarkan kami bekerja untuk mewujudkan lingkungan yang lebih baik bersama.</p>
        <div class="flex flex-wrap gap-4 justify-center">
          <?php if ($isLoggedIn): ?>
            <a href="create_pengaduan.php" class="btn-primary bg-white text-navy-900 font-bold px-8 py-3.5 rounded-2xl hover:shadow-xl hover:shadow-white/20 transition-all duration-300 text-sm">
              <i class="fa-solid fa-plus mr-2"></i>Buat Pengaduan Sekarang
            </a>
          <?php else: ?>
            <a href="login.php" class="btn-primary bg-white text-navy-900 font-bold px-8 py-3.5 rounded-2xl hover:shadow-xl hover:shadow-white/20 transition-all duration-300 text-sm">
              <i class="fa-solid fa-right-to-bracket mr-2"></i>Login Sekarang
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer-bg text-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
      <div class="sm:col-span-2 lg:col-span-1">
        <div class="flex items-center gap-2.5 mb-4">
          <div class="w-10 h-10 rounded-xl bg-cobalt flex items-center justify-center">
            <i class="fa-solid fa-landmark text-white"></i>
          </div>
          <div>
            <div class="font-display font-bold text-white">Pengaduan</div>
            <div class="text-cobalt text-xs font-semibold tracking-widest uppercase">Masyarakat</div>
          </div>
        </div>
        <p class="text-blue-200 text-sm leading-relaxed mb-5">Platform resmi pengaduan masyarakat yang transparan, responsif, dan dapat dipercaya.</p>
        <div class="flex gap-3">
          <a href="#" class="w-9 h-9 bg-white/10 hover:bg-cobalt rounded-xl flex items-center justify-center transition-colors"><i class="fa-brands fa-facebook-f text-sm"></i></a>
          <a href="#" class="w-9 h-9 bg-white/10 hover:bg-cobalt rounded-xl flex items-center justify-center transition-colors"><i class="fa-brands fa-twitter text-sm"></i></a>
          <a href="#" class="w-9 h-9 bg-white/10 hover:bg-cobalt rounded-xl flex items-center justify-center transition-colors"><i class="fa-brands fa-instagram text-sm"></i></a>
        </div>
      </div>
      <div>
        <h4 class="font-bold text-white mb-4 text-sm uppercase tracking-widest">Layanan</h4>
        <ul class="space-y-2.5 text-sm text-blue-200">
          <li><a href="create_pengaduan.php" class="hover:text-white transition-colors">Buat Pengaduan</a></li>
          <li><a href="pengaduanku.php" class="hover:text-white transition-colors">Pengaduan Saya</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-bold text-white mb-4 text-sm uppercase tracking-widest">Informasi</h4>
        <ul class="space-y-2.5 text-sm text-blue-200">
          <li><a href="index.php" class="hover:text-white transition-colors">Tentang Platform</a></li>
          <li><a href="index.php" class="hover:text-white transition-colors">FAQ</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-bold text-white mb-4 text-sm uppercase tracking-widest">Kontak</h4>
        <ul class="space-y-3 text-sm text-blue-200">
          <li><i class="fa-solid fa-envelope text-cobalt mr-2"></i> pengaduan@kota.go.id</li>
          <li><i class="fa-solid fa-phone text-cobalt mr-2"></i> (0341) 340-0000</li>
        </ul>
      </div>
    </div>
    <div class="border-t border-white/10 pt-6 text-center">
      <p class="text-blue-300 text-xs">© 2025 Pengaduan Masyarakat. Seluruh hak cipta dilindungi.</p>
    </div>
  </div>
</footer>

<script>
  // Modal Functions
  function openModal(data) {
    const modal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');
    
    const statusClass = data.status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : 
                       (data.status === 'diproses' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700');
    const statusText = data.status === 'selesai' ? 'Selesai' : 
                      (data.status === 'diproses' ? 'Diproses' : 'Baru');
    const statusDot = data.status === 'selesai' ? 'bg-emerald-500' : 
                     (data.status === 'diproses' ? 'bg-amber-500' : 'bg-blue-500');
    
    const imageHtml = data.img && data.img !== '' ? 
      `<div class="mb-4">
        <img src="uploads/${data.img}" alt="Gambar Pengaduan" class="w-full rounded-xl max-h-64 object-cover">
       </div>` : '';
    
    modalContent.innerHTML = `
      <div class="flex items-center justify-between mb-4">
        <span class="${statusClass} text-xs font-bold px-3 py-1 rounded-full">
          <i class="fa-regular fa-clock mr-1"></i>${statusText}
        </span>
        <div class="flex items-center gap-1.5 text-xs font-semibold ${data.status === 'selesai' ? 'text-emerald-600' : (data.status === 'diproses' ? 'text-amber-600' : 'text-blue-600')}">
          <span class="w-2 h-2 rounded-full ${statusDot} ${data.status !== 'selesai' ? 'animate-pulse' : ''}"></span>
          ${statusText}
        </div>
      </div>
      
      <h2 class="font-display font-bold text-2xl text-navy-900 mb-3">${escapeHtml(data.title)}</h2>
      
      ${imageHtml}
      
      <div class="bg-gray-50 rounded-xl p-4 mb-4">
        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">${escapeHtml(data.description)}</p>
      </div>
      
      <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="flex items-center gap-2 text-sm">
          <i class="fa-regular fa-user text-cobalt w-5"></i>
          <span class="text-gray-600">${escapeHtml(data.username)}</span>
        </div>
        <div class="flex items-center gap-2 text-sm">
          <i class="fa-regular fa-user text-cobalt w-5"></i>
          <span class="text-gray-600">${escapeHtml(data.fullname)}</span>
        </div>
        <div class="flex items-center gap-2 text-sm">
          <i class="fa-regular fa-calendar text-cobalt w-5"></i>
          <span class="text-gray-600">${formatDate(data.date)}</span>
        </div>
        <div class="flex items-center gap-2 text-sm">
          <i class="fa-solid fa-hashtag text-cobalt w-5"></i>
          <span class="text-gray-600">ADU-${String(data.id).padStart(4, '0')}</span>
        </div>
      </div>
      
      ${data.location ? `
      <div class="flex items-center gap-2 text-sm border-t border-gray-100 pt-4">
        <i class="fa-solid fa-location-dot text-cobalt"></i>
        <span class="text-gray-600">${escapeHtml(data.location)}</span>
      </div>
      ` : ''}
      
      <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex justify-between items-center text-sm mb-2">
          <span class="text-gray-500">Progres Penanganan</span>
          <span class="font-semibold ${data.status === 'selesai' ? 'text-emerald-600' : (data.status === 'diproses' ? 'text-amber-600' : 'text-blue-600')}">${statusText}</span>
        </div>
        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full w-${data.status === 'selesai' ? 'full' : (data.status === 'diproses' ? '3/5' : '1/5')} bg-gradient-to-r ${data.status === 'selesai' ? 'from-emerald-400 to-emerald-500' : (data.status === 'diproses' ? 'from-amber-400 to-amber-500' : 'from-blue-400 to-blue-500')} rounded-full"></div>
        </div>
      </div>
    `;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
  
  function closeModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
  
  function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
  
  function formatDate(dateString) {
    const date = new Date(dateString);
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return `${days[date.getDay()]}, ${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
  }
  
  // Close modal on ESC key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeModal();
    }
  });
  
  // Close modal on overlay click
  document.getElementById('detailModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('detailModal')) {
      closeModal();
    }
  });
  
  // Hamburger Toggle
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobile-menu');
  let menuOpen = false;
  if (hamburger) {
    hamburger.addEventListener('click', () => {
      menuOpen = !menuOpen;
      mobileMenu.classList.toggle('open', menuOpen);
    });
  }
  
  // Navbar Scroll
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    if (navbar) navbar.classList.toggle('navbar-scrolled', window.scrollY > 20);
  });
  
  // Filter Cards
  function filterCards(status) {
    const cards = document.querySelectorAll('#cards-container [data-status]');
    cards.forEach(card => {
      const match = status === 'all' || card.dataset.status === status;
      card.style.display = match ? '' : 'none';
    });
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.classList.remove('bg-cobalt', 'text-white', 'border-cobalt');
    });
    if (event && event.target) {
      event.target.classList.add('bg-cobalt', 'text-white', 'border-cobalt');
    }
  }
  
  // Scroll Reveal
  const revealEls = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });
  revealEls.forEach(el => observer.observe(el));
</script>
</body>
</html> 