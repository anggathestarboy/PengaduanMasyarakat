<?php
session_start();
require_once "config/db.php";

// 🔒 cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ambil data pengaduan milik user login
$query = "SELECT * FROM pengaduan 
          WHERE user_id = '$user_id'
          ORDER BY date DESC";

$result = mysqli_query($conn, $query);

// Hitung statistik untuk user ini
$total_user_pengaduan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE user_id = '$user_id'"))['total'];
$total_user_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE user_id = '$user_id' AND status = 'selesai'"))['total'];
$total_user_proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE user_id = '$user_id' AND status = 'diproses'"))['total'];
$total_user_baru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengaduan WHERE user_id = '$user_id' AND status = 'baru'"))['total'];

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
    if (empty($name)) return '?';
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
  <title>Pengaduan Saya — Pengaduan Masyarakat</title>
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

    .complaint-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
    .complaint-card:hover { transform: translateY(-6px); box-shadow: 0 20px 48px rgba(27,79,216,0.14); }

    .stat-box { background: linear-gradient(135deg, #fff 0%, #EBF0FD 100%); }

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

    /* Modal Styles */
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
    
    .admin-note {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      border-left: 4px solid #f59e0b;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<?php include "components/navbar.php"; ?>

<!-- HEADER SECTION -->
<section class="pt-32 pb-12 bg-gradient-to-b from-[#F7F9FF] to-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center">
      <div class="inline-flex items-center gap-2 bg-cobalt/10 text-cobalt rounded-full px-4 py-1.5 text-xs font-bold mb-4 tracking-widest uppercase">
        <i class="fa-solid fa-folder-open"></i> Riwayat Saya
      </div>
      <h1 class="font-display text-4xl sm:text-5xl font-bold text-navy-900 mb-4">
        Pengaduan <span class="text-cobalt">Saya</span>
      </h1>
      <p class="text-gray-500 text-base max-w-2xl mx-auto">
        Lihat dan pantau seluruh pengaduan yang telah Anda laporkan beserta status penanganannya.
      </p>
    </div>
  </div>
</section>

<!-- STATS STRIP (User Specific) -->
<section class="py-6 bg-white border-y border-blue-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-cobalt/10 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-inbox text-cobalt text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900"><?= number_format($total_user_pengaduan) ?></div>
          <div class="text-gray-500 text-xs font-medium">Total Pengaduan</div>
        </div>
      </div>
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900"><?= number_format($total_user_selesai) ?></div>
          <div class="text-gray-500 text-xs font-medium">Kasus Selesai</div>
        </div>
      </div>
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-hourglass-half text-amber-500 text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900"><?= number_format($total_user_proses) ?></div>
          <div class="text-gray-500 text-xs font-medium">Sedang Diproses</div>
        </div>
      </div>
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-clock text-blue-500 text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900"><?= number_format($total_user_baru) ?></div>
          <div class="text-gray-500 text-xs font-medium">Menunggu</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PENGADUAN SAYA LIST -->
<section class="py-16 bg-[#F7F9FF]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Filter Bar -->
    <div class="reveal flex flex-wrap gap-2 justify-center mb-10">
      <button onclick="filterCards('all')" class="filter-btn px-5 py-2.5 text-sm font-semibold rounded-xl border transition-all duration-200 bg-cobalt text-white border-cobalt shadow-sm">Semua</button>
      <!-- <button onclick="filterCards('baru')" class="filter-btn px-5 py-2.5 text-sm font-semibold rounded-xl border border-blue-200 text-blue-600 hover:bg-blue-500 hover:text-white transition-all duration-200">Baru</button> -->
      <button onclick="filterCards('diproses')" class="filter-btn px-5 py-2.5 text-sm font-semibold rounded-xl border border-amber-200 text-amber-600 hover:bg-amber-500 hover:text-white transition-all duration-200">Diproses</button>
      <button onclick="filterCards('selesai')" class="filter-btn px-5 py-2.5 text-sm font-semibold rounded-xl border border-emerald-200 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all duration-200">Selesai</button>
    </div>

    <!-- Cards Grid -->
    <div id="cards-container" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($pengaduan = mysqli_fetch_assoc($result)): 
          $status = strtolower($pengaduan['status'] ?? 'baru');
          $progressWidth = getProgressWidth($status);
          $gradientColor = getGradientColor($status);
          $initials = getInitials($_SESSION['fullname'] ?? 'User');
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
              <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-3"><?= htmlspecialchars($shortDescription) ?></p>
              
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
                    <span class="text-xs text-gray-500 font-medium block">Anda</span>
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
        <div class="col-span-full text-center py-16">
          <div class="bg-white rounded-2xl p-12 max-w-md mx-auto shadow-sm border border-blue-100">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <i class="fa-solid fa-folder-open text-4xl text-gray-400"></i>
            </div>
            <h3 class="font-display text-xl font-bold text-navy-900 mb-2">Belum Ada Pengaduan</h3>
            <p class="text-gray-500 text-sm mb-6">Anda belum membuat pengaduan apapun. Mulai laporkan masalah di sekitar Anda sekarang!</p>
            <a href="/create_pengaduan.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-cobalt to-navy-700 text-white font-semibold px-6 py-3 rounded-xl hover:shadow-lg transition-all duration-300 text-sm">
              <i class="fa-solid fa-pen-to-square"></i> Buat Pengaduan Sekarang
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- MODAL POPUP DETAIL PENGADUAN -->
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

<!-- CTA BANNER -->
<section class="py-16 bg-[#F7F9FF]">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="reveal relative overflow-hidden bg-gradient-to-r from-navy-900 to-cobalt rounded-3xl p-10 text-center">
      <div class="absolute inset-0 opacity-10" style="background-image:linear-gradient(rgba(255,255,255,0.1) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.1) 1px,transparent 1px);background-size:32px 32px;"></div>
      <div class="relative z-10">
        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-5 backdrop-blur">
          <i class="fa-solid fa-pen-to-square text-white text-2xl"></i>
        </div>
        <h2 class="font-display text-3xl font-bold text-white mb-3">Ada Masalah Baru?</h2>
        <p class="text-blue-200 mb-8 text-base max-w-lg mx-auto">Laporkan masalah di lingkungan Anda dan kami akan segera menindaklanjutinya.</p>
        <a href="/create_pengaduan.php" class="btn-primary bg-white text-navy-900 font-bold px-8 py-3.5 rounded-2xl hover:shadow-xl hover:shadow-white/20 transition-all duration-300 text-sm inline-flex items-center gap-2">
          <i class="fa-solid fa-plus"></i> Buat Pengaduan Baru
        </a>
      </div>
    </div>
  </div>
</section>

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
    
    const adminNoteHtml = data.admin_note && data.admin_note !== '' ? 
      `<div class="admin-note rounded-xl p-4 mb-4">
        <div class="flex items-center gap-2 mb-2">
          <i class="fa-solid fa-user-tie text-amber-600"></i>
          <span class="text-xs font-semibold text-amber-700">Catatan Admin</span>
        </div>
        <p class="text-sm text-gray-700">${escapeHtml(data.admin_note)}</p>
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
        <div class="flex items-center gap-2 mb-2">
          <i class="fa-solid fa-align-left text-cobalt text-sm"></i>
          <span class="text-xs font-semibold text-gray-500">DESKRIPSI LENGKAP</span>
        </div>
        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">${escapeHtml(data.description)}</p>
      </div>
      
      ${adminNoteHtml}
      
      <div class="grid grid-cols-2 gap-3 mb-4">
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
  
  // Filter Cards
  function filterCards(status) {
    const cards = document.querySelectorAll('#cards-container [data-status]');
    cards.forEach(card => {
      const match = status === 'all' || card.dataset.status === status;
      card.style.display = match ? '' : 'none';
    });
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.classList.remove('bg-cobalt', 'text-white', 'border-cobalt');
      btn.classList.add('border', 'hover:bg-opacity-90');
    });
    if (event && event.target) {
      event.target.classList.add('bg-cobalt', 'text-white', 'border-cobalt');
      event.target.classList.remove('border');
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