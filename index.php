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

    /* Navbar scroll effect */
    .navbar-scrolled { background: rgba(255,255,255,0.97) !important; box-shadow: 0 2px 24px rgba(27,79,216,0.10) !important; }

    /* Mobile menu */
    #mobile-menu { max-height: 0; overflow: hidden; transition: max-height 0.4s cubic-bezier(0.4,0,0.2,1), opacity 0.3s; opacity: 0; }
    #mobile-menu.open { max-height: 480px; opacity: 1; }

    /* Hero gradient */
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

    /* Floating badge */
    .badge-float { animation: floatY 3.5s ease-in-out infinite; }
    @keyframes floatY { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }

    /* Card hover */
    .complaint-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
    .complaint-card:hover { transform: translateY(-6px); box-shadow: 0 20px 48px rgba(27,79,216,0.14); }

    /* Stat counter */
    .stat-box { background: linear-gradient(135deg, #fff 0%, #EBF0FD 100%); }

    /* Section divider */
    .wave-divider { line-height: 0; }

    /* Button pulse */
    .btn-primary { position: relative; overflow: hidden; }
    .btn-primary::after {
      content: '';
      position: absolute; inset: 0;
      background: rgba(255,255,255,0.15);
      transform: scaleX(0); transform-origin: left;
      transition: transform 0.3s ease;
    }
    .btn-primary:hover::after { transform: scaleX(1); }

    /* Tag colors */
    .tag-infrastruktur { background:#DBEAFE; color:#1D4ED8; }
    .tag-lingkungan    { background:#D1FAE5; color:#065F46; }
    .tag-keamanan      { background:#FEE2E2; color:#991B1B; }
    .tag-kesehatan     { background:#FCE7F3; color:#9D174D; }
    .tag-pendidikan    { background:#FEF9C3; color:#854D0E; }
    .tag-administrasi  { background:#E0E7FF; color:#3730A3; }

    /* Status dot */
    .dot-proses  { background: #F59E0B; }
    .dot-selesai { background: #10B981; }
    .dot-baru    { background: #3B82F6; }

    /* Scroll reveal */
    .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    /* Footer */
    .footer-bg { background: linear-gradient(160deg, #0A1628 0%, #0F2044 100%); }

    /* Custom scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #1B4FD8; border-radius: 9px; }

    /* Decorative ring */
    .ring-deco {
      position: absolute;
      border-radius: 9999px;
      border: 1.5px solid rgba(255,255,255,0.10);
      animation: ringPulse 4s ease-in-out infinite;
    }
    @keyframes ringPulse { 0%,100%{opacity:0.5;transform:scale(1)} 50%{opacity:1;transform:scale(1.04)} }
  </style>
</head>
<body>

<!-- ═══════════════════════════ NAVBAR ═══════════════════════════ -->
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-blue-50 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16 lg:h-18">

      <!-- Brand -->
      <a href="#" class="flex items-center gap-2.5 flex-shrink-0">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-cobalt to-navy-700 flex items-center justify-center shadow-lg">
          <i class="fa-solid fa-landmark text-white text-sm"></i>
        </div>
        <div class="leading-tight">
          <span class="font-display font-bold text-navy-900 text-base tracking-tight block">Pengaduan</span>
          <span class="text-cobalt font-semibold text-xs tracking-widest uppercase block -mt-0.5">Masyarakat</span>
        </div>
      </a>

      <!-- Desktop Nav -->
      <div class="hidden md:flex items-center gap-1">
        <a href="#home" class="nav-link px-4 py-2 text-sm font-semibold text-navy-900 rounded-lg hover:text-cobalt hover:bg-cobalt/5 transition-all duration-200">
          <i class="fa-solid fa-house mr-1.5 text-cobalt text-xs"></i>Home
        </a>
        <a href="#pengaduan" class="nav-link px-4 py-2 text-sm font-semibold text-navy-900 rounded-lg hover:text-cobalt hover:bg-cobalt/5 transition-all duration-200">
          <i class="fa-solid fa-file-lines mr-1.5 text-cobalt text-xs"></i>Pengaduan
        </a>
        <a href="#tentang" class="nav-link px-4 py-2 text-sm font-semibold text-navy-900 rounded-lg hover:text-cobalt hover:bg-cobalt/5 transition-all duration-200">
          <i class="fa-solid fa-circle-info mr-1.5 text-cobalt text-xs"></i>Tentang Kami
        </a>
      </div>

      <!-- Auth Buttons Desktop -->
      <div class="hidden md:flex items-center gap-3">
        <button class="px-5 py-2 text-sm font-semibold text-cobalt border-2 border-cobalt rounded-xl hover:bg-cobalt hover:text-white transition-all duration-200">
          <i class="fa-solid fa-right-to-bracket mr-1.5"></i>Masuk
        </button>
        <button class="btn-primary px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-cobalt to-navy-700 rounded-xl hover:shadow-lg hover:shadow-cobalt/30 transition-all duration-200">
          <i class="fa-solid fa-user-plus mr-1.5"></i>Daftar
        </button>
      </div>

      <!-- Hamburger Mobile -->
      <button id="hamburger" class="md:hidden flex flex-col justify-center items-center w-10 h-10 rounded-xl hover:bg-cobalt/10 transition-colors" aria-label="Toggle Menu">
        <span id="h-line1" class="block w-5 h-0.5 bg-navy-900 rounded transition-all duration-300"></span>
        <span id="h-line2" class="block w-5 h-0.5 bg-navy-900 rounded mt-1.5 transition-all duration-300"></span>
        <span id="h-line3" class="block w-5 h-0.5 bg-navy-900 rounded mt-1.5 transition-all duration-300"></span>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden border-t border-blue-50">
      <div class="py-4 space-y-1">
        <a href="#home" class="mobile-link flex items-center gap-3 px-4 py-3 text-sm font-semibold text-navy-900 rounded-xl hover:bg-cobalt/5 hover:text-cobalt transition-all">
          <i class="fa-solid fa-house w-5 text-cobalt text-center"></i>Home
        </a>
        <a href="#pengaduan" class="mobile-link flex items-center gap-3 px-4 py-3 text-sm font-semibold text-navy-900 rounded-xl hover:bg-cobalt/5 hover:text-cobalt transition-all">
          <i class="fa-solid fa-file-lines w-5 text-cobalt text-center"></i>Pengaduan
        </a>
        <a href="#tentang" class="mobile-link flex items-center gap-3 px-4 py-3 text-sm font-semibold text-navy-900 rounded-xl hover:bg-cobalt/5 hover:text-cobalt transition-all">
          <i class="fa-solid fa-circle-info w-5 text-cobalt text-center"></i>Tentang Kami
        </a>
        <div class="pt-3 border-t border-blue-50 flex gap-3 px-4">
          <button class="flex-1 py-2.5 text-sm font-semibold text-cobalt border-2 border-cobalt rounded-xl hover:bg-cobalt hover:text-white transition-all">
            <i class="fa-solid fa-right-to-bracket mr-1.5"></i>Masuk
          </button>
          <button class="flex-1 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-cobalt to-navy-700 rounded-xl">
            <i class="fa-solid fa-user-plus mr-1.5"></i>Daftar
          </button>
        </div>
      </div>
    </div>
  </div>
</nav>


<!-- ═══════════════════════════ HERO ═══════════════════════════ -->
<section id="home" class="hero-bg min-h-screen flex items-center pt-16">
  <div class="hero-grid"></div>

  <!-- Decorative rings -->
  <div class="ring-deco w-64 h-64 top-16 right-8 opacity-20 hidden lg:block"></div>
  <div class="ring-deco w-96 h-96 top-8 right-0 opacity-10 hidden lg:block" style="animation-delay:1.5s"></div>
  <div class="ring-deco w-40 h-40 bottom-24 left-12 opacity-15 hidden lg:block" style="animation-delay:0.75s"></div>

  <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

      <!-- Left Content -->
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
          <button class="btn-primary group inline-flex items-center gap-2.5 bg-white text-cobalt font-bold px-7 py-3.5 rounded-2xl hover:shadow-2xl hover:shadow-white/20 transition-all duration-300 text-sm">
            <i class="fa-solid fa-pen-to-square"></i>
            Buat Pengaduan
            <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
          </button>
          <button class="inline-flex items-center gap-2.5 border-2 border-white/30 text-white font-semibold px-7 py-3.5 rounded-2xl hover:bg-white/10 transition-all duration-300 text-sm backdrop-blur">
            <i class="fa-solid fa-magnifying-glass"></i>
            Lacak Pengaduan
          </button>
        </div>

        <!-- Mini stats -->
        <div class="flex flex-wrap gap-6 mt-10 pt-10 border-t border-white/10">
          <div>
            <div class="font-display text-2xl font-bold text-white">4.800+</div>
            <div class="text-blue-300 text-xs font-medium mt-0.5">Pengaduan Masuk</div>
          </div>
         
         
          <div class="w-px bg-white/10"></div>
          <div>
            <div class="font-display text-2xl font-bold text-white">3.200+</div>
            <div class="text-blue-300 text-xs font-medium mt-0.5">Kasus Diselesaikan</div>
          </div>
        </div>
      </div>

      <!-- Right — Floating Card UI -->
   <div class="hidden lg:flex justify-center relative">
  <div class="badge-float relative">
    <!-- Main card - simplified to circular icon -->
    <div class="bg-white rounded-full shadow-2xl p-6 w-32 h-32 flex items-center justify-center relative">
      <!-- Center icon - government building -->
      <div class="w-16 h-16 bg-cobalt/10 rounded-full flex items-center justify-center">
        <i class="fa-solid fa-landmark text-cobalt text-3xl"></i>
      </div>
      
      <!-- Progress ring indicator -->
      <div class="absolute inset-0 rounded-full">
        <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="44" fill="none" stroke="#e2e8f0" stroke-width="3"/>
          <circle cx="50" cy="50" r="44" fill="none" stroke="#3b82f6" stroke-width="3" 
                  stroke-dasharray="276.46" stroke-dashoffset="96.76" stroke-linecap="round"/>
        </svg>
      </div>
    </div>

    <!-- Floating notification - adjusted position -->
    <div class="absolute -top-4 -right-4 bg-white rounded-full shadow-xl p-2.5 border border-blue-50">
      <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
        <i class="fa-solid fa-bell text-emerald-600 text-sm"></i>
      </div>
    </div>

    <!-- Floating badge - adjusted position -->
    <div class="absolute -bottom-4 -left-4 bg-gradient-to-r from-cobalt to-navy-700 rounded-full shadow-xl p-2.5 text-white" style="animation-delay:0.5s">
      <div class="flex items-center justify-center">
        <i class="fa-solid fa-shield-halved text-blue-300 text-base"></i>
      </div>
    </div>
  </div>
</div>
    </div>
  </div>

  <!-- Wave -->
  <div class="wave-divider absolute bottom-0 left-0 right-0">
    <svg viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
      <path d="M0,48 C360,80 1080,0 1440,48 L1440,80 L0,80 Z" fill="#F7F9FF"/>
    </svg>
  </div>
</section>


<!-- ═══════════════════════════ STATS STRIP ═══════════════════════════ -->
<section class="py-8 bg-white border-y border-blue-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-cobalt/10 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-inbox text-cobalt text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900">4.821</div>
          <div class="text-gray-500 text-xs font-medium">Total Pengaduan</div>
        </div>
      </div>
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900">3.247</div>
          <div class="text-gray-500 text-xs font-medium">Kasus Selesai</div>
        </div>
      </div>
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-hourglass-half text-amber-500 text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900">1.204</div>
          <div class="text-gray-500 text-xs font-medium">Sedang Diproses</div>
        </div>
      </div>
      <div class="stat-box rounded-2xl p-4 flex items-center gap-4 border border-blue-100">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-users text-cobalt text-lg"></i>
        </div>
        <div>
          <div class="font-display font-bold text-2xl text-navy-900">12.500+</div>
          <div class="text-gray-500 text-xs font-medium">Pengguna Terdaftar</div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════ PENGADUAN LIST ═══════════════════════════ -->
<section id="pengaduan" class="py-20 bg-[#F7F9FF]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Section Header -->
    <div class="reveal text-center mb-14">
      <div class="inline-flex items-center gap-2 bg-cobalt/10 text-cobalt rounded-full px-4 py-1.5 text-xs font-bold mb-4 tracking-widest uppercase">
        <i class="fa-solid fa-list-ul"></i> Daftar Pengaduan
      </div>
      <h2 class="font-display text-3xl sm:text-4xl font-bold text-navy-900 mb-4">
        Pengaduan Terkini
      </h2>
      <p class="text-gray-500 text-base max-w-lg mx-auto">
        Pantau seluruh laporan pengaduan masyarakat yang masuk, sedang diproses, maupun yang telah diselesaikan.
      </p>
    </div>

    <!-- Filter Bar -->
    <div class="reveal flex flex-wrap gap-2 justify-center mb-10">
      <button onclick="filterCards('all')" class="filter-btn active-filter px-4 py-2 text-sm font-semibold rounded-xl border transition-all duration-200">Semua</button>
      <button onclick="filterCards('baru')" class="filter-btn px-4 py-2 text-sm font-semibold rounded-xl border border-blue-200 text-cobalt hover:bg-cobalt hover:text-white transition-all duration-200">Baru</button>
      <button onclick="filterCards('proses')" class="filter-btn px-4 py-2 text-sm font-semibold rounded-xl border border-amber-200 text-amber-600 hover:bg-amber-500 hover:text-white transition-all duration-200">Diproses</button>
      <button onclick="filterCards('selesai')" class="filter-btn px-4 py-2 text-sm font-semibold rounded-xl border border-emerald-200 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all duration-200">Selesai</button>
    </div>

    <!-- Cards Grid -->
    <div id="cards-container" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

      <!-- Card 1 -->
      <div class="complaint-card reveal bg-white rounded-2xl border border-blue-50 shadow-sm overflow-hidden" data-status="proses">
        <div class="h-2 bg-gradient-to-r from-amber-400 to-amber-500"></div>
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <span class="tag-infrastruktur text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-road mr-1"></i>Infrastruktur</span>
            <div class="flex items-center gap-1.5 text-xs font-semibold text-amber-600">
              <span class="w-2 h-2 rounded-full dot-proses animate-pulse"></span>Diproses
            </div>
          </div>
          <h3 class="font-display font-bold text-navy-900 text-base mb-2">Jalan Berlubang di Perumahan Griya Indah</h3>
          <p class="text-gray-500 text-sm leading-relaxed mb-4">Terdapat lubang besar berdiameter ±60 cm di Jalan Melati RT 03 yang membahayakan pengendara, terutama pada malam hari.</p>
          <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
            <i class="fa-solid fa-location-dot text-cobalt"></i>
            <span>Kec. Lowokwaru, Malang</span>
          </div>
          <div class="h-1.5 bg-gray-100 rounded-full mb-4 overflow-hidden">
            <div class="h-full w-[60%] bg-gradient-to-r from-amber-400 to-amber-300 rounded-full"></div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 bg-cobalt rounded-full flex items-center justify-center"><span class="text-white text-[10px] font-bold">AH</span></div>
              <span class="text-xs text-gray-500 font-medium">Ahmad Hidayat</span>
            </div>
            <span class="text-xs text-gray-400"><i class="fa-regular fa-calendar mr-1"></i>20 Mar 2025</span>
          </div>
        </div>
        <div class="px-6 py-3 border-t border-blue-50 bg-blue-50/40 flex justify-between items-center">
          <span class="text-xs text-gray-400"><i class="fa-solid fa-hashtag mr-1"></i>ADU-2025-0031</span>
          <button class="text-xs font-bold text-cobalt hover:underline"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Detail</button>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="complaint-card reveal bg-white rounded-2xl border border-blue-50 shadow-sm overflow-hidden" data-status="selesai" style="transition-delay:0.1s">
        <div class="h-2 bg-gradient-to-r from-emerald-400 to-emerald-500"></div>
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <span class="tag-lingkungan text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-leaf mr-1"></i>Lingkungan</span>
            <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
              <span class="w-2 h-2 rounded-full dot-selesai"></span>Selesai
            </div>
          </div>
          <h3 class="font-display font-bold text-navy-900 text-base mb-2">Tumpukan Sampah di Pinggir Sungai Brantas</h3>
          <p class="text-gray-500 text-sm leading-relaxed mb-4">Sampah menumpuk di bantaran sungai menimbulkan bau tidak sedap dan berpotensi menyebabkan banjir saat musim hujan.</p>
          <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
            <i class="fa-solid fa-location-dot text-cobalt"></i>
            <span>Kec. Blimbing, Malang</span>
          </div>
          <div class="h-1.5 bg-gray-100 rounded-full mb-4 overflow-hidden">
            <div class="h-full w-full bg-gradient-to-r from-emerald-400 to-emerald-300 rounded-full"></div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 bg-emerald-500 rounded-full flex items-center justify-center"><span class="text-white text-[10px] font-bold">SR</span></div>
              <span class="text-xs text-gray-500 font-medium">Siti Rahayu</span>
            </div>
            <span class="text-xs text-gray-400"><i class="fa-regular fa-calendar mr-1"></i>12 Mar 2025</span>
          </div>
        </div>
        <div class="px-6 py-3 border-t border-blue-50 bg-blue-50/40 flex justify-between items-center">
          <span class="text-xs text-gray-400"><i class="fa-solid fa-hashtag mr-1"></i>ADU-2025-0018</span>
          <button class="text-xs font-bold text-cobalt hover:underline"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Detail</button>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="complaint-card reveal bg-white rounded-2xl border border-blue-50 shadow-sm overflow-hidden" data-status="baru" style="transition-delay:0.2s">
        <div class="h-2 bg-gradient-to-r from-cobalt to-blue-400"></div>
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <span class="tag-keamanan text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-shield-halved mr-1"></i>Keamanan</span>
            <div class="flex items-center gap-1.5 text-xs font-semibold text-blue-600">
              <span class="w-2 h-2 rounded-full dot-baru animate-pulse"></span>Baru
            </div>
          </div>
          <h3 class="font-display font-bold text-navy-900 text-base mb-2">Lampu Jalan Mati Sejak 2 Minggu Lalu</h3>
          <p class="text-gray-500 text-sm leading-relaxed mb-4">Seluruh lampu jalan di Blok C Perumahan Sejahtera padam total, menyebabkan kerawanan kriminal pada malam hari.</p>
          <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
            <i class="fa-solid fa-location-dot text-cobalt"></i>
            <span>Kec. Sukun, Malang</span>
          </div>
          <div class="h-1.5 bg-gray-100 rounded-full mb-4 overflow-hidden">
            <div class="h-full w-[10%] bg-gradient-to-r from-cobalt to-blue-400 rounded-full"></div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 bg-blue-500 rounded-full flex items-center justify-center"><span class="text-white text-[10px] font-bold">BW</span></div>
              <span class="text-xs text-gray-500 font-medium">Budi Wibowo</span>
            </div>
            <span class="text-xs text-gray-400"><i class="fa-regular fa-calendar mr-1"></i>25 Mar 2025</span>
          </div>
        </div>
        <div class="px-6 py-3 border-t border-blue-50 bg-blue-50/40 flex justify-between items-center">
          <span class="text-xs text-gray-400"><i class="fa-solid fa-hashtag mr-1"></i>ADU-2025-0048</span>
          <button class="text-xs font-bold text-cobalt hover:underline"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Detail</button>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="complaint-card reveal bg-white rounded-2xl border border-blue-50 shadow-sm overflow-hidden" data-status="proses" style="transition-delay:0.05s">
        <div class="h-2 bg-gradient-to-r from-pink-400 to-pink-500"></div>
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <span class="tag-kesehatan text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-heart-pulse mr-1"></i>Kesehatan</span>
            <div class="flex items-center gap-1.5 text-xs font-semibold text-amber-600">
              <span class="w-2 h-2 rounded-full dot-proses animate-pulse"></span>Diproses
            </div>
          </div>
          <h3 class="font-display font-bold text-navy-900 text-base mb-2">Antrian Panjang Puskesmas Tidak Tertib</h3>
          <p class="text-gray-500 text-sm leading-relaxed mb-4">Sistem antrian di Puskesmas Klojen tidak teratur, pasien lansia dan ibu hamil tidak mendapat prioritas layanan.</p>
          <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
            <i class="fa-solid fa-location-dot text-cobalt"></i>
            <span>Kec. Klojen, Malang</span>
          </div>
          <div class="h-1.5 bg-gray-100 rounded-full mb-4 overflow-hidden">
            <div class="h-full w-[45%] bg-gradient-to-r from-pink-400 to-pink-300 rounded-full"></div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 bg-pink-500 rounded-full flex items-center justify-center"><span class="text-white text-[10px] font-bold">DP</span></div>
              <span class="text-xs text-gray-500 font-medium">Dewi Permata</span>
            </div>
            <span class="text-xs text-gray-400"><i class="fa-regular fa-calendar mr-1"></i>17 Mar 2025</span>
          </div>
        </div>
        <div class="px-6 py-3 border-t border-blue-50 bg-blue-50/40 flex justify-between items-center">
          <span class="text-xs text-gray-400"><i class="fa-solid fa-hashtag mr-1"></i>ADU-2025-0027</span>
          <button class="text-xs font-bold text-cobalt hover:underline"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Detail</button>
        </div>
      </div>

      <!-- Card 5 -->
      <div class="complaint-card reveal bg-white rounded-2xl border border-blue-50 shadow-sm overflow-hidden" data-status="selesai" style="transition-delay:0.15s">
        <div class="h-2 bg-gradient-to-r from-yellow-400 to-yellow-500"></div>
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <span class="tag-pendidikan text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-graduation-cap mr-1"></i>Pendidikan</span>
            <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
              <span class="w-2 h-2 rounded-full dot-selesai"></span>Selesai
            </div>
          </div>
          <h3 class="font-display font-bold text-navy-900 text-base mb-2">Atap Kelas Bocor di SDN 04 Blimbing</h3>
          <p class="text-gray-500 text-sm leading-relaxed mb-4">Atap beberapa ruang kelas bocor parah saat hujan, mengganggu kegiatan belajar mengajar dan merusak peralatan sekolah.</p>
          <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
            <i class="fa-solid fa-location-dot text-cobalt"></i>
            <span>Kec. Blimbing, Malang</span>
          </div>
          <div class="h-1.5 bg-gray-100 rounded-full mb-4 overflow-hidden">
            <div class="h-full w-full bg-gradient-to-r from-yellow-400 to-yellow-300 rounded-full"></div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 bg-yellow-500 rounded-full flex items-center justify-center"><span class="text-white text-[10px] font-bold">RN</span></div>
              <span class="text-xs text-gray-500 font-medium">Rini Novita</span>
            </div>
            <span class="text-xs text-gray-400"><i class="fa-regular fa-calendar mr-1"></i>05 Mar 2025</span>
          </div>
        </div>
        <div class="px-6 py-3 border-t border-blue-50 bg-blue-50/40 flex justify-between items-center">
          <span class="text-xs text-gray-400"><i class="fa-solid fa-hashtag mr-1"></i>ADU-2025-0009</span>
          <button class="text-xs font-bold text-cobalt hover:underline"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Detail</button>
        </div>
      </div>

      <!-- Card 6 -->
      <div class="complaint-card reveal bg-white rounded-2xl border border-blue-50 shadow-sm overflow-hidden" data-status="baru" style="transition-delay:0.25s">
        <div class="h-2 bg-gradient-to-r from-indigo-400 to-indigo-500"></div>
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <span class="tag-administrasi text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-file-signature mr-1"></i>Administrasi</span>
            <div class="flex items-center gap-1.5 text-xs font-semibold text-blue-600">
              <span class="w-2 h-2 rounded-full dot-baru animate-pulse"></span>Baru
            </div>
          </div>
          <h3 class="font-display font-bold text-navy-900 text-base mb-2">Pengurusan KTP Memakan Waktu Lebih 2 Bulan</h3>
          <p class="text-gray-500 text-sm leading-relaxed mb-4">Proses pembuatan KTP baru di Kelurahan Arjosari memakan waktu lebih dari 2 bulan tanpa informasi yang jelas kepada warga.</p>
          <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
            <i class="fa-solid fa-location-dot text-cobalt"></i>
            <span>Kel. Arjosari, Malang</span>
          </div>
          <div class="h-1.5 bg-gray-100 rounded-full mb-4 overflow-hidden">
            <div class="h-full w-[5%] bg-gradient-to-r from-indigo-400 to-indigo-300 rounded-full"></div>
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 bg-indigo-500 rounded-full flex items-center justify-center"><span class="text-white text-[10px] font-bold">FS</span></div>
              <span class="text-xs text-gray-500 font-medium">Fajar Santoso</span>
            </div>
            <span class="text-xs text-gray-400"><i class="fa-regular fa-calendar mr-1"></i>27 Mar 2025</span>
          </div>
        </div>
        <div class="px-6 py-3 border-t border-blue-50 bg-blue-50/40 flex justify-between items-center">
          <span class="text-xs text-gray-400"><i class="fa-solid fa-hashtag mr-1"></i>ADU-2025-0052</span>
          <button class="text-xs font-bold text-cobalt hover:underline"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Detail</button>
        </div>
      </div>

    </div>

    <!-- CTA Load More -->
    <div class="reveal text-center mt-10">
      <button class="inline-flex items-center gap-2 px-8 py-3.5 border-2 border-cobalt text-cobalt font-bold rounded-2xl hover:bg-cobalt hover:text-white transition-all duration-300 text-sm">
        <i class="fa-solid fa-layer-group"></i>Lihat Semua Pengaduan
        <i class="fa-solid fa-chevron-down text-xs"></i>
      </button>
    </div>
  </div>
</section>


<!-- ═══════════════════════════ TENTANG ═══════════════════════════ -->
<section id="tentang" class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-2 gap-16 items-center">

      <!-- Visual Side -->
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
          <!-- Floating accent -->
          <div class="absolute -top-4 -right-4 w-16 h-16 bg-gold rounded-2xl flex items-center justify-center shadow-lg">
            <i class="fa-solid fa-award text-white text-xl"></i>
          </div>
        </div>
      </div>

      <!-- Text Side -->
      <div class="reveal order-1 lg:order-2">
        <div class="inline-flex items-center gap-2 bg-cobalt/10 text-cobalt rounded-full px-4 py-1.5 text-xs font-bold mb-4 tracking-widest uppercase">
          <i class="fa-solid fa-circle-info"></i> Tentang Kami
        </div>
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-navy-900 mb-5">
          Platform Pengaduan<br/>yang Bisa Dipercaya
        </h2>
        <p class="text-gray-500 leading-relaxed mb-6 text-base">
          Kami hadir sebagai jembatan antara masyarakat dan pemerintah. Dengan teknologi modern dan komitmen terhadap transparansi, setiap suara Anda akan didengar dan ditindaklanjuti.
        </p>
        <div class="space-y-4 mb-8">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-cobalt/10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
              <i class="fa-solid fa-check-double text-cobalt"></i>
            </div>
            <div>
              <h4 class="font-bold text-navy-900 text-sm mb-1">Proses Terstandarisasi</h4>
              <p class="text-gray-500 text-sm">Setiap pengaduan melewati verifikasi, penugasan, dan penyelesaian yang terstruktur dan terdokumentasi.</p>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
              <i class="fa-solid fa-bell text-emerald-600"></i>
            </div>
            <div>
              <h4 class="font-bold text-navy-900 text-sm mb-1">Notifikasi Real-time</h4>
              <p class="text-gray-500 text-sm">Terima pembaruan status pengaduan melalui SMS, email, atau notifikasi dalam aplikasi.</p>
            </div>
          </div>
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
              <i class="fa-solid fa-chart-line text-amber-500"></i>
            </div>
            <div>
              <h4 class="font-bold text-navy-900 text-sm mb-1">Laporan & Analitik</h4>
              <p class="text-gray-500 text-sm">Akses dasbor statistik untuk memahami tren pengaduan dan efektivitas penanganan masalah.</p>
            </div>
          </div>
        </div>
        <button class="btn-primary inline-flex items-center gap-2 bg-gradient-to-r from-cobalt to-navy-700 text-white font-bold px-7 py-3.5 rounded-2xl hover:shadow-lg hover:shadow-cobalt/30 transition-all duration-300 text-sm">
          <i class="fa-solid fa-play-circle"></i>Pelajari Lebih Lanjut
        </button>
      </div>

    </div>
  </div>
</section>


<!-- ═══════════════════════════ CTA BANNER ═══════════════════════════ -->
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
          <button class="btn-primary bg-white text-navy-900 font-bold px-8 py-3.5 rounded-2xl hover:shadow-xl hover:shadow-white/20 transition-all duration-300 text-sm">
            <i class="fa-solid fa-plus mr-2"></i>Buat Pengaduan Sekarang
          </button>
          <button class="border-2 border-white/30 text-white font-semibold px-8 py-3.5 rounded-2xl hover:bg-white/10 transition-all duration-300 text-sm">
            <i class="fa-solid fa-phone mr-2"></i>Hubungi Kami
          </button>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════ FOOTER ═══════════════════════════ -->
<footer class="footer-bg text-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

      <!-- Brand -->
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
        <p class="text-blue-200 text-sm leading-relaxed mb-5">Platform resmi pengaduan masyarakat yang transparan, responsif, dan dapat dipercaya untuk layanan publik yang lebih baik.</p>
        <div class="flex gap-3">
          <a href="#" class="w-9 h-9 bg-white/10 hover:bg-cobalt rounded-xl flex items-center justify-center transition-colors"><i class="fa-brands fa-facebook-f text-sm"></i></a>
          <a href="#" class="w-9 h-9 bg-white/10 hover:bg-cobalt rounded-xl flex items-center justify-center transition-colors"><i class="fa-brands fa-twitter text-sm"></i></a>
          <a href="#" class="w-9 h-9 bg-white/10 hover:bg-cobalt rounded-xl flex items-center justify-center transition-colors"><i class="fa-brands fa-instagram text-sm"></i></a>
          <a href="#" class="w-9 h-9 bg-white/10 hover:bg-cobalt rounded-xl flex items-center justify-center transition-colors"><i class="fa-brands fa-youtube text-sm"></i></a>
        </div>
      </div>

      <!-- Layanan -->
      <div>
        <h4 class="font-bold text-white mb-4 text-sm uppercase tracking-widest">Layanan</h4>
        <ul class="space-y-2.5 text-sm text-blue-200">
          <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-cobalt text-[10px]"></i>Buat Pengaduan</a></li>
          <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-cobalt text-[10px]"></i>Lacak Status</a></li>
          <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-cobalt text-[10px]"></i>Riwayat Pengaduan</a></li>
          <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-cobalt text-[10px]"></i>Statistik Publik</a></li>
          <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-cobalt text-[10px]"></i>Panduan Penggunaan</a></li>
        </ul>
      </div>

      <!-- Informasi -->
      <div>
        <h4 class="font-bold text-white mb-4 text-sm uppercase tracking-widest">Informasi</h4>
        <ul class="space-y-2.5 text-sm text-blue-200">
          <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-cobalt text-[10px]"></i>Tentang Platform</a></li>
          <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-cobalt text-[10px]"></i>FAQ</a></li>
          <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-cobalt text-[10px]"></i>Kebijakan Privasi</a></li>
          <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-cobalt text-[10px]"></i>Syarat & Ketentuan</a></li>
          <li><a href="#" class="hover:text-white transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-cobalt text-[10px]"></i>Hubungi Kami</a></li>
        </ul>
      </div>

      <!-- Kontak -->
      <div>
        <h4 class="font-bold text-white mb-4 text-sm uppercase tracking-widest">Kontak</h4>
        <ul class="space-y-3 text-sm text-blue-200">
          <li class="flex items-start gap-3">
            <i class="fa-solid fa-location-dot text-cobalt mt-0.5 flex-shrink-0"></i>
            <span>Jl. Merdeka No. 1, Kota Malang, Jawa Timur 65119</span>
          </li>
          <li class="flex items-center gap-3">
            <i class="fa-solid fa-phone text-cobalt flex-shrink-0"></i>
            <a href="tel:+622134000000" class="hover:text-white transition-colors">(0341) 340-0000</a>
          </li>
          <li class="flex items-center gap-3">
            <i class="fa-solid fa-envelope text-cobalt flex-shrink-0"></i>
            <a href="mailto:pengaduan@kota.go.id" class="hover:text-white transition-colors">pengaduan@kota.go.id</a>
          </li>
          <li class="flex items-center gap-3">
            <i class="fa-solid fa-clock text-cobalt flex-shrink-0"></i>
            <span>Senin–Jumat: 08.00–16.00 WIB</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
      <p class="text-blue-300 text-xs text-center sm:text-left">
        © 2025 <span class="text-white font-semibold">Pengaduan Masyarakat</span>. Seluruh hak cipta dilindungi.
      </p>
      <div class="flex items-center gap-1.5 text-blue-300 text-xs">
        <i class="fa-solid fa-heart text-red-400"></i>
        <span>Dibangun untuk melayani masyarakat Indonesia</span>
        <img src="https://flagcdn.com/w20/id.png" alt="ID" class="w-5 h-3.5 rounded-sm ml-1 object-cover"/>
      </div>
    </div>
  </div>
</footer>


<script>
  // ─── Hamburger Toggle ───────────────────────────────────────────
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobile-menu');
  const l1 = document.getElementById('h-line1');
  const l2 = document.getElementById('h-line2');
  const l3 = document.getElementById('h-line3');
  let menuOpen = false;

  hamburger.addEventListener('click', () => {
    menuOpen = !menuOpen;
    mobileMenu.classList.toggle('open', menuOpen);
    if (menuOpen) {
      l1.style.cssText = 'transform:translateY(8px) rotate(45deg)';
      l2.style.cssText = 'opacity:0;transform:scaleX(0)';
      l3.style.cssText = 'transform:translateY(-8px) rotate(-45deg)';
    } else {
      l1.style.cssText = '';
      l2.style.cssText = '';
      l3.style.cssText = '';
    }
  });

  // Close mobile menu on link click
  document.querySelectorAll('.mobile-link').forEach(link => {
    link.addEventListener('click', () => {
      menuOpen = false;
      mobileMenu.classList.remove('open');
      l1.style.cssText = l2.style.cssText = l3.style.cssText = '';
    });
  });

  // ─── Navbar Scroll ───────────────────────────────────────────────
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('navbar-scrolled', window.scrollY > 20);
  });

  // ─── Smooth Scroll ───────────────────────────────────────────────
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
  });

  // ─── Filter Cards ────────────────────────────────────────────────
  function filterCards(status) {
    const cards = document.querySelectorAll('#cards-container [data-status]');
    cards.forEach(card => {
      const match = status === 'all' || card.dataset.status === status;
      card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
      if (match) {
        card.style.opacity = '1';
        card.style.transform = '';
        card.style.display = '';
      } else {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(() => { if (card.style.opacity === '0') card.style.display = 'none'; }, 300);
      }
    });

    // Update active filter button
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.classList.remove('active-filter', 'bg-cobalt', 'text-white', 'border-cobalt');
    });
    event.target.classList.add('active-filter', 'bg-cobalt', 'text-white', 'border-cobalt');
  }

  // Set initial active style
  document.querySelector('.filter-btn').classList.add('bg-cobalt', 'text-white', 'border-cobalt');

  // ─── Scroll Reveal ───────────────────────────────────────────────
  const revealEls = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        const delay = e.target.style.transitionDelay || '0s';
        setTimeout(() => e.target.classList.add('visible'), parseFloat(delay) * 1000);
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });

  revealEls.forEach(el => observer.observe(el));
</script>
</body>
</html>