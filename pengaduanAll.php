

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

    /* Sparkline */
    .sparkline-bar {
      width:5px;
      border-radius:3px;
      background:rgba(255,255,255,0.3);
      transition:height 0.8s cubic-bezier(0.4,0,0.2,1);
    }
    .sparkline-bar.active { background:rgba(255,255,255,0.85); }

    /* Page fade-in */
    @keyframes fadeUp {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }
    .fade-up { animation: fadeUp 0.5s ease both; }

    /* Avatar ring */
    .avatar-ring {
      width:38px; height:38px; border-radius:50%;
      background: linear-gradient(135deg, #1D5CFF, #8B5CF6);
      display:flex; align-items:center; justify-content:center;
      font-weight:700; font-size:0.8rem; color:#fff;
      box-shadow:0 0 0 3px #C5D3FF;
    }

    /* Notification dot */
    .notif-dot {
      width:8px; height:8px;
      border-radius:50%;
      background:#EF4444;
      border:2px solid #EEF2FF;
      position:absolute; top:6px; right:6px;
    }

    /* Nav label */
    .nav-label { white-space:nowrap; }

    /* Mobile responsive */
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
    
    /* modal detail styles */
    .modal-detail-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(4px);
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }
    .modal-detail-container {
      background: white;
      border-radius: 24px;
      max-width: 600px;
      width: 100%;
      max-height: 85vh;
      overflow-y: auto;
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
      animation: modalSlideUp 0.25s ease-out;
    }
    @keyframes modalSlideUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .detail-image {
      max-height: 280px;
      object-fit: cover;
      width: 100%;
      border-radius: 20px;
    }
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
  <div class="p-6 lg:p-8 max-w-7xl">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="font-display text-2xl font-bold text-ink-900">Manajemen Pengaduan</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola dan pantau seluruh pengaduan masyarakat</p>
      </div>
      <div class="flex gap-2">
       
      
      </div>
    </div>

    <!-- Tabs Status -->
    <div class="flex gap-1 border-b border-slate-border mb-6">
      <button class="tab-btn active px-5 py-2.5 text-sm font-medium rounded-t-xl bg-white text-azure border-b-2 border-azure transition-all">Semua</button>
      <button class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-xl text-slate-500 hover:text-ink-900 transition-all">Menunggu</button>
      <button class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-xl text-slate-500 hover:text-ink-900 transition-all">Diproses</button>
      <button class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-xl text-slate-500 hover:text-ink-900 transition-all">Selesai</button>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="pengaduanContainer">
      <?php 
      // Ambil data pengaduan lengkap dengan join users
      $query_pengaduan = "SELECT p.*, u.username, u.fullname 
                          FROM pengaduan p 
                          JOIN users u ON p.user_id = u.id 
                          ORDER BY p.date DESC";
      $result_pengaduan = mysqli_query($conn, $query_pengaduan);
      
      if (mysqli_num_rows($result_pengaduan) > 0):
        while ($row = mysqli_fetch_assoc($result_pengaduan)):
          $status = $row['status'];
          $statusColor = match($status) {
            'selesai' => 'bg-emerald-100 text-emerald-700',
            'diproses' => 'bg-amber-100 text-amber-700',
            'ditolak' => 'bg-red-600 text-white',
            default => 'bg-blue-100 text-blue-700'
          };
          $statusIcon = match($status) {
            'selesai' => 'fa-circle-check',
            'diproses' => 'fa-hourglass-half',
            'ditolak' => 'fa-solid fa-xmark',
            default => 'fa-clock'
          };
          $statusText = match($status) {
            'selesai' => 'Selesai',
            'diproses' => 'Diproses',
            'ditolak' => 'Ditolak',
            default => 'Menunggu'
          };
          $imagePath = !empty($row['img']) && file_exists('uploads/' . $row['img']) ? 'uploads/' . $row['img'] : null;
          
          // Escape untuk keperluan modal detail
          $detail_id = $row['id'];
          $detail_title = htmlspecialchars($row['title']);
          $detail_desc = htmlspecialchars($row['description']);
          $detail_username = htmlspecialchars($row['username']);
          $detail_fullname = htmlspecialchars($row['fullname']);
          $detail_date = date('d M Y, H:i', strtotime($row['date']));
          $detail_location = htmlspecialchars($row['location'] ?? '');
          $detail_status = $statusText;
          $detail_status_badge = $statusColor;
          $detail_image = $imagePath ? 'uploads/' . urlencode($row['img']) : null;
          $detail_admin_note = htmlspecialchars($row['admin_note'] ?? '');
      ?>
      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-border overflow-hidden hover:shadow-md transition-shadow" data-status="<?= $status ?>">
        <!-- Image Section -->
        <?php if ($imagePath): ?>
        <div class="relative h-48 bg-gray-100">
          <img src="<?= htmlspecialchars($imagePath) ?>" alt="Gambar Pengaduan" class="w-full h-full object-cover">
          <div class="absolute top-3 right-3">
            <span class="<?= $statusColor ?> text-xs font-semibold px-2.5 py-1 rounded-full shadow-sm">
              <i class="fa-regular <?= $statusIcon ?> mr-1"></i><?= $statusText ?>
            </span>
          </div>
        </div>
        <?php else: ?>
        <div class="px-5 pt-5 pb-2 flex items-center justify-between">
          <span class="<?= $statusColor ?> text-xs font-semibold px-2.5 py-1 rounded-full">
            <i class="fa-regular <?= $statusIcon ?> mr-1"></i><?= $statusText ?>
          </span>
        </div>
        <?php endif; ?>
        
        <!-- Content -->
        <div class="p-5">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-azure to-purple-500 flex items-center justify-center text-white text-xs font-bold">
              <?= strtoupper(substr($row['username'], 0, 1)) ?>
            </div>
            <div>
              <p class="text-sm font-medium text-ink-900"><?= htmlspecialchars($row['username']) ?></p>
              <p class="text-xs text-slate-400"><?= date('d M Y, H:i', strtotime($row['date'])) ?></p>
            </div>
          </div>
          
          <h3 class="font-display font-bold text-ink-900 text-lg mb-2 line-clamp-2"><?= htmlspecialchars($row['title']) ?></h3>
          <p class="text-slate-500 text-sm leading-relaxed line-clamp-3 mb-4"><?= htmlspecialchars($row['description']) ?></p>
          
          <?php if (!empty($row['location'])): ?>
          <div class="flex items-center gap-2 text-xs text-slate-400 mb-4">
            <i class="fa-solid fa-location-dot text-azure"></i>
            <span><?= htmlspecialchars($row['location']) ?></span>
          </div>
          <?php endif; ?>
          
          <!-- Action Buttons -->
<div class="flex gap-2 mt-4 pt-4 border-t border-slate-border">

<?php if ($status === 'menunggu'): ?>

  <!-- Proses -->
  <button onclick="openModal(<?= $row['id'] ?>, 'diproses')"
    class="flex-1 bg-amber-50 hover:bg-amber-100 text-amber-700 font-medium py-2 rounded-xl text-sm flex items-center justify-center gap-2">
    <i class="fa-solid fa-gear"></i> Proses
  </button>

  <!-- Tolak -->
  <button onclick="openModal(<?= $row['id'] ?>, 'ditolak')"
    class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 font-medium py-2 rounded-xl text-sm flex items-center justify-center gap-2">
    <i class="fa-solid fa-ban"></i> Tolak
  </button>

<?php elseif ($status === 'diproses'): ?>

  <!-- Selesai -->
  <button onclick="openModal(<?= $row['id'] ?>, 'selesai')"
    class="w-full bg-green-600 hover:bg-green-600 text-white font-medium py-2 rounded-xl text-sm flex items-center justify-center gap-2">
 Tandai Selesai
  </button>

<?php else: ?>

  <!-- Sudah selesai / ditolak -->
  <div class="w-full text-center text-xs text-slate-400 py-2">
    Tidak ada aksi tersedia
  </div>

<?php endif; ?>

</div>
          
          <!-- Admin Note (if any) -->
          <?php if (!empty($row['admin_note'])): ?>
          <div class="mt-3 p-2 bg-amber-50 rounded-lg border-l-4 border-amber-400">
            <p class="text-xs text-amber-700"><i class="fa-solid fa-note-sticky mr-1"></i> Catatan: <?= htmlspecialchars($row['admin_note']) ?></p>
          </div>
          <?php endif; ?>
        </div>
        
        <!-- ID Footer with Detail Button (fixed: opens modal popup) -->
        <div class="px-5 py-3 bg-slate-panel border-t border-slate-border flex justify-between items-center">
          <span class="text-xs text-slate-400"><i class="fa-solid fa-hashtag mr-1"></i>ADU-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></span>
          <button onclick='openDetailModal({
            id: <?= $detail_id ?>,
            title: <?= json_encode($detail_title) ?>,
            description: <?= json_encode($detail_desc) ?>,
            username: <?= json_encode($detail_username) ?>,
            fullname: <?= json_encode($detail_fullname) ?>,
            date: <?= json_encode($detail_date) ?>,
            location: <?= json_encode($detail_location) ?>,
            status: <?= json_encode($detail_status) ?>,
            statusBadge: <?= json_encode($detail_status_badge) ?>,
            image: <?= json_encode($detail_image) ?>,
            adminNote: <?= json_encode($detail_admin_note) ?>
          })' 
          class="text-xs text-azure hover:underline font-medium cursor-pointer">
            <i class="fa-regular fa-eye mr-1"></i> Lihat Detail
          </button>
        </div>
      </div>
      <?php 
        endwhile;
      else: 
      ?>
      <div class="col-span-full text-center py-16">
        <div class="w-24 h-24 bg-slate-panel rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fa-solid fa-inbox text-4xl text-slate-400"></i>
        </div>
        <h3 class="font-display text-xl font-bold text-ink-900 mb-2">Belum Ada Pengaduan</h3>
        <p class="text-slate-500">Belum ada pengaduan yang masuk ke sistem.</p>
      </div>
      <?php endif; ?>
    </div>
    
   
  
</main>

<!-- Modal untuk update status (catatan admin) - tetap seperti semula -->
<div id="modal" class="fixed inset-0 z-50 hidden">
  
  <!-- Overlay -->
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>

  <!-- Modal Box -->
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl transform scale-95 opacity-0 transition-all duration-200" id="modalBox">
      
      <h2 class="text-lg font-bold mb-3">Catatan Admin (opsional)</h2>

      <form method="POST" action="controller/update_status.php">
        <input type="hidden" name="id" id="modal_id">
        <input type="hidden" name="status" id="modal_status">

        <textarea name="admin_note" 
          class="w-full border border-slate-300 rounded-lg p-3 text-sm mb-4 focus:ring-2 focus:ring-azure focus:outline-none"
          placeholder="Masukkan pesan untuk user..."></textarea>

        <div class="flex justify-end gap-2">
          <button type="button" onclick="closeModal()"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
            Batal
          </button>

          <button type="submit"
            class="px-4 py-2 bg-azure hover:bg-azure-soft text-white rounded-lg text-sm">
            Simpan
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- NEW MODAL POPUP FOR DETAIL PENGADUAN (FULL DETAIL) -->
<div id="detailModal" class="fixed inset-0 z-[60] hidden">
  <div class="modal-detail-overlay" onclick="closeDetailModal()">
    <div class="modal-detail-container" onclick="event.stopPropagation()">
      <div class="relative">
        <!-- Tombol close -->
        <button onclick="closeDetailModal()" class="absolute top-4 right-4 bg-white/80 hover:bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-md z-10 text-gray-600 hover:text-gray-900 transition">
          <i class="fa-solid fa-times"></i>
        </button>
        
        <!-- Konten dinamis diisi via JS -->
        <div id="detailContent" class="p-5 md:p-6">
          <!-- loader atau konten akan diisi -->
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Tab filtering functionality (static for now)
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      // Update active tab style
      document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('active', 'bg-white', 'text-azure', 'border-b-2', 'border-azure');
        b.classList.add('text-slate-500');
      });
      this.classList.add('active', 'bg-white', 'text-azure', 'border-b-2', 'border-azure');
      this.classList.remove('text-slate-500');
      
      // Filter cards (static - just for UI demonstration)
      const status = this.textContent.trim().toLowerCase();
      const cards = document.querySelectorAll('#pengaduanContainer > div');
      
      cards.forEach(card => {
        if (status === 'semua') {
          card.style.display = '';
        } else {
          const cardStatus = card.getAttribute('data-status');
          if (cardStatus === status) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        }
      });
    });
  });


function openModal(id, status) {
  const modal = document.getElementById('modal');
  const box = document.getElementById('modalBox');

  modal.classList.remove('hidden');

  // delay biar animasi jalan
  setTimeout(() => {
    box.classList.remove('scale-95', 'opacity-0');
    box.classList.add('scale-100', 'opacity-100');
  }, 10);

  document.getElementById('modal_id').value = id;
  document.getElementById('modal_status').value = status;
}

function closeModal() {
  const modal = document.getElementById('modal');
  const box = document.getElementById('modalBox');

  box.classList.remove('scale-100', 'opacity-100');
  box.classList.add('scale-95', 'opacity-0');

  setTimeout(() => {
    modal.classList.add('hidden');
  }, 200);
}

// ===================== DETAIL MODAL POPUP =====================
function openDetailModal(data) {
  const modal = document.getElementById('detailModal');
  const contentDiv = document.getElementById('detailContent');
  
  // Build HTML for detail
  let imageHtml = '';
  if (data.image) {
    imageHtml = `
      <div class="mb-5 rounded-xl overflow-hidden bg-gray-100">
        <img src="${escapeHtml(data.image)}" alt="Gambar pengaduan" class="detail-image w-full max-h-72 object-cover">
      </div>
    `;
  } else {
    imageHtml = `
      <div class="mb-5 rounded-xl bg-slate-panel flex items-center justify-center py-12 border border-dashed border-slate-border">
        <i class="fa-regular fa-image text-4xl text-slate-300"></i>
        <span class="ml-2 text-slate-400 text-sm">Tidak ada gambar</span>
      </div>
    `;
  }
  
  const statusBadgeClass = data.statusBadge || 'bg-blue-100 text-blue-700';
  const adminNoteHtml = data.adminNote && data.adminNote.trim() !== '' 
    ? `
      <div class="mt-5 p-4 bg-amber-50 rounded-xl border-l-4 border-amber-400">
        <div class="flex items-start gap-2">
          <i class="fa-solid fa-note-sticky text-amber-600 mt-0.5"></i>
          <div>
            <p class="text-xs font-semibold text-amber-800 uppercase tracking-wide">Catatan Admin</p>
            <p class="text-sm text-amber-800 mt-1">${escapeHtml(data.adminNote)}</p>
          </div>
        </div>
      </div>
    `
    : '';
  
  const locationHtml = data.location && data.location.trim() !== '' 
    ? `
      <div class="flex items-start gap-2 text-sm text-slate-600 mt-2">
        <i class="fa-solid fa-location-dot text-azure mt-0.5"></i>
        <span>${escapeHtml(data.location)}</span>
      </div>
    `
    : '';
  
  const fullHtml = `
    <div class="space-y-4">
      <!-- Header status badge -->
      <div class="flex justify-between items-start flex-wrap gap-2">
        <span class="${statusBadgeClass} text-xs font-semibold px-3 py-1.5 rounded-full inline-flex items-center gap-1">
          <i class="fa-regular ${getStatusIconByText(data.status)}"></i> ${escapeHtml(data.status)}
        </span>
        <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-full">
          <i class="fa-regular fa-calendar-alt mr-1"></i> ${escapeHtml(data.date)}
        </span>
      </div>
      
      <!-- Title -->
      <h2 class="font-display text-2xl font-bold text-ink-900 leading-tight">${escapeHtml(data.title)}</h2>
      
      <!-- User info -->
      <div class="flex items-center gap-3 py-2 border-y border-slate-border">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-azure to-purple-600 flex items-center justify-center text-white font-bold shadow-sm">
          ${escapeHtml((data.username.charAt(0) || 'U').toUpperCase())}
        </div>
        <div>
          <p class="font-semibold text-ink-900">${escapeHtml(data.fullname || data.username)}</p>
          <p class="text-xs text-slate-400">@${escapeHtml(data.username)}</p>
        </div>
      </div>
      
      ${imageHtml}
      
      <!-- Deskripsi -->
      <div>
        <h3 class="font-semibold text-ink-800 text-sm uppercase tracking-wide mb-2">Deskripsi Pengaduan</h3>
        <div class="bg-slate-panel rounded-xl p-4 text-slate-700 text-sm leading-relaxed whitespace-pre-wrap">
          ${escapeHtml(data.description) || '<span class="text-slate-400 italic">Tidak ada deskripsi</span>'}
        </div>
      </div>
      
      ${locationHtml ? `<div>${locationHtml}</div>` : ''}
      
      ${adminNoteHtml}
      
      <!-- ID Reference -->
      <div class="pt-3 text-right border-t border-slate-border">
        <span class="text-xs text-slate-400 font-mono"><i class="fa-regular fa-hashtag"></i> ID Pengaduan: ADU-${String(data.id).padStart(4, '0')}</span>
      </div>
    </div>
  `;
  
  contentDiv.innerHTML = fullHtml;
  modal.classList.remove('hidden');
  // prevent body scroll
  document.body.style.overflow = 'hidden';
}

function closeDetailModal() {
  const modal = document.getElementById('detailModal');
  if (modal) {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
  }
}

function getStatusIconByText(statusText) {
  switch(statusText.toLowerCase()) {
    case 'selesai': return 'fa-circle-check';
    case 'diproses': return 'fa-hourglass-half';
    case 'ditolak': return 'fa-solid fa-xmark';
    default: return 'fa-clock';
  }
}

function escapeHtml(str) {
  if (!str) return '';
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

// Fungsi untuk close sidebar (jika ada)
function closeSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  if (sidebar) sidebar.classList.remove('open');
  if (overlay) overlay.classList.remove('show');
}

// Agar tombol detail yang dibuat secara dinamis tetap berfungsi (sudah terpasang onclick pada setiap tombol)
// Tidak perlu tambahan lain karena button sudah memiliki event handler langsung
</script>

</body>
</html>