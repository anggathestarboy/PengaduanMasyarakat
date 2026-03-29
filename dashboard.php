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

    /* Tooltip on sidebar icon (collapsed) */
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

    /* Progress ring */
    .ring-track { fill:none; stroke:rgba(255,255,255,0.18); stroke-width:5; }
    .ring-fill  { fill:none; stroke:rgba(255,255,255,0.85); stroke-width:5;
                  stroke-linecap:round; transition:stroke-dashoffset 1.2s cubic-bezier(0.4,0,0.2,1); }
  </style>
</head>
<body>

<!-- ════════════════════════ SIDEBAR ════════════════════════ -->
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<aside id="sidebar">
  <!-- Brand -->
  <div class="relative z-10 px-5 pt-6 pb-5 border-b border-white/10">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
           style="background:linear-gradient(135deg,#1D5CFF,#1140C8);box-shadow:0 4px 14px rgba(29,92,255,0.5)">
        <i class="fa-solid fa-landmark text-white text-sm"></i>
      </div>
      <div>
        <div class="font-display font-bold text-white text-sm leading-tight">Pengaduan</div>
        <div class="text-azure-dim text-[10px] font-semibold tracking-widest uppercase">Masyarakat</div>
      </div>
    </div>
  </div>

  <!-- Navigation -->
  <nav id="sidebar-nav" class="relative z-10 flex-1 px-3 pt-5 pb-4 space-y-1 overflow-y-auto">

    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30 px-3 mb-3">Menu Utama</p>

    <a href="#" class="nav-item active">
      <span class="icon-wrap"><i class="fa-solid fa-gauge-high"></i></span>
      <span class="nav-label">Dashboard</span>
    </a>
    <a href="#" class="nav-item">
      <span class="icon-wrap"><i class="fa-solid fa-file-lines"></i></span>
      <span class="nav-label">Pengaduan</span>
      <span class="ml-auto bg-azure/70 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">48</span>
    </a>
    <a href="#" class="nav-item">
      <span class="icon-wrap"><i class="fa-solid fa-chart-bar"></i></span>
      <span class="nav-label">Aktivitas</span>
    </a>
    <a href="#" class="nav-item">
      <span class="icon-wrap"><i class="fa-solid fa-users"></i></span>
      <span class="nav-label">Pengguna</span>
    </a>

   

   


</aside>


<!-- ════════════════════════ TOPBAR ════════════════════════ -->
<header id="topbar">
  <!-- Hamburger -->
  <button id="hamburger" class="md:hidden flex flex-col justify-center gap-1.5 w-9 h-9 rounded-lg hover:bg-azure-pale transition-colors mr-4" onclick="toggleSidebar()">
    <span class="block h-0.5 w-5 bg-ink-800 rounded"></span>
    <span class="block h-0.5 w-5 bg-ink-800 rounded"></span>
    <span class="block h-0.5 w-4 bg-ink-800 rounded"></span>
  </button>

  <!-- Page Title -->
  <div class="flex-1">
    <h1 class="font-display font-bold text-ink-900 text-lg leading-none">Dashboard</h1>
    <p class="text-xs text-slate-400 font-medium mt-0.5">
      <i class="fa-regular fa-calendar mr-1 text-azure"></i>
      <span id="current-date"></span>
    </p>
  </div>

  <!-- Right Actions -->
  <div class="flex items-center gap-3">


   

    <!-- Avatar -->
    <div class="flex items-center gap-2.5 pl-3 border-l border-slate-border cursor-pointer group">
      <div class="avatar-ring text-xs">SA</div>
      <div class="hidden sm:block">
        <div class="text-[10px] text-slate-400">Administrator</div>
      </div>
      <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1 group-hover:text-azure transition-colors"></i>
    </div>
  </div>
</header>


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
          <h2 class="font-display text-white text-xl font-bold">Super Admin</h2>
          <p class="text-blue-300 text-sm mt-1 font-light">Berikut ringkasan aktivitas pengaduan hari ini.</p>
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
          <span class="counter" data-target="4821" data-duration="1800">0</span>
        </div>
        <div class="text-white/70 text-sm font-medium mt-1">Total Pengaduan</div>
        <!-- Mini sparkline -->
        <div class="flex items-end gap-1 mt-4 h-8">
          <div class="sparkline-bar" style="height:40%"></div>
          <div class="sparkline-bar" style="height:60%"></div>
          <div class="sparkline-bar" style="height:45%"></div>
          <div class="sparkline-bar" style="height:75%"></div>
          <div class="sparkline-bar" style="height:55%"></div>
          <div class="sparkline-bar" style="height:80%"></div>
          <div class="sparkline-bar active" style="height:100%"></div>
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
          <span class="counter" data-target="1204" data-duration="1600">0</span>
        </div>
        <div class="text-white/70 text-sm font-medium mt-1">Sedang Diproses</div>
        <!-- Ring progress -->
         <div class="flex items-end gap-1 mt-4 h-8">
          <div class="sparkline-bar" style="height:40%"></div>
          <div class="sparkline-bar" style="height:60%"></div>
          <div class="sparkline-bar" style="height:45%"></div>
          <div class="sparkline-bar" style="height:75%"></div>
          <div class="sparkline-bar" style="height:55%"></div>
          <div class="sparkline-bar" style="height:80%"></div>
          <div class="sparkline-bar active" style="height:100%"></div>
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
          <span class="counter" data-target="3247" data-duration="2000">0</span>
        </div>
        <div class="text-white/70 text-sm font-medium mt-1">Pengaduan Selesai</div>
          <div class="flex items-end gap-1 mt-4 h-8">
          <div class="sparkline-bar" style="height:40%"></div>
          <div class="sparkline-bar" style="height:60%"></div>
          <div class="sparkline-bar" style="height:45%"></div>
          <div class="sparkline-bar" style="height:75%"></div>
          <div class="sparkline-bar" style="height:55%"></div>
          <div class="sparkline-bar" style="height:80%"></div>
          <div class="sparkline-bar active" style="height:100%"></div>
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
          <span class="counter" data-target="12580" data-duration="2200">0</span>
        </div>
        <div class="text-white/70 text-sm font-medium mt-1">Total Pengguna</div>
        <div class="flex items-end gap-1 mt-4 h-8">
          <div class="sparkline-bar" style="height:30%"></div>
          <div class="sparkline-bar" style="height:50%"></div>
          <div class="sparkline-bar" style="height:40%"></div>
          <div class="sparkline-bar" style="height:65%"></div>
          <div class="sparkline-bar" style="height:60%"></div>
          <div class="sparkline-bar" style="height:85%"></div>
          <div class="sparkline-bar active" style="height:100%"></div>
        </div>
      </div>

    </div><!-- /grid -->







    <!-- Spacer -->
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
  document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
      this.classList.add('active');
      if (window.innerWidth < 768) closeSidebar();
    });
  });

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
</script>
</body>
</html>