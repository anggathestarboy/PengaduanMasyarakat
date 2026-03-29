<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daftar Akun — Pengaduan Masyarakat</title>
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

    /* Custom form styling */
    .input-group {
      transition: all 0.2s ease;
    }
    .input-group:focus-within {
      transform: translateY(-1px);
    }
    .form-input-custom {
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input-custom:focus {
      border-color: #1B4FD8;
      box-shadow: 0 0 0 3px rgba(27,79,216,0.1);
      outline: none;
    }
    .btn-primary {
      position: relative;
      overflow: hidden;
    }
    .btn-primary::after {
      content: '';
      position: absolute; inset: 0;
      background: rgba(255,255,255,0.15);
      transform: scaleX(0); transform-origin: left;
      transition: transform 0.3s ease;
    }
    .btn-primary:hover::after { transform: scaleX(1); }

    /* Floating decoration */
    .floating-shape {
      animation: floatSlow 6s ease-in-out infinite;
    }
    @keyframes floatSlow {
      0%,100%{transform:translateY(0) rotate(0deg);}
      50%{transform:translateY(-20px) rotate(2deg);}
    }
    .ring-deco {
      position: absolute;
      border-radius: 9999px;
      border: 1.5px solid rgba(255,255,255,0.10);
      animation: ringPulse 4s ease-in-out infinite;
    }
    @keyframes ringPulse { 0%,100%{opacity:0.5;transform:scale(1)} 50%{opacity:1;transform:scale(1.04)} }

    /* Scroll reveal */
    .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }
    
    /* Custom checkbox */
    .checkbox-custom {
      appearance: none;
      width: 18px;
      height: 18px;
      border: 2px solid #CBD5E1;
      border-radius: 4px;
      background: white;
      transition: all 0.2s;
      cursor: pointer;
      position: relative;
    }
    .checkbox-custom:checked {
      background-color: #1B4FD8;
      border-color: #1B4FD8;
    }
    .checkbox-custom:checked::after {
      content: '\f00c';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      font-size: 10px;
      color: white;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
  </style>
</head>
<body>

<!-- ═══════════════════════════ NAVBAR (IDENTIK DENGAN TEMA) ═══════════════════════════ -->
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

<!-- ═══════════════════════════ HALAMAN DAFTAR (2 SISI) ═══════════════════════════ -->
<section class="min-h-screen pt-28 pb-20 bg-gradient-to-b from-[#F7F9FF] to-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-start">
      
      <!-- SISI KIRI: KONTEN TEKS (INFORMASI & BENEFIT) -->
      <div class="reveal sticky top-32 space-y-6">
        <div class="inline-flex items-center gap-2 bg-cobalt/10 text-cobalt rounded-full px-4 py-1.5 text-xs font-bold mb-2 tracking-widest uppercase">
          <i class="fa-solid fa-user-plus"></i> Bergabung Sekarang
        </div>
        <h1 class="font-display text-4xl sm:text-5xl font-bold text-navy-900 leading-tight">
          Daftar Akun <span class="text-cobalt">Pengadu</span> Digital
        </h1>
        <p class="text-gray-500 text-base leading-relaxed border-l-4 border-cobalt pl-5 italic">
          “Suara Anda adalah langkah pertama menuju perubahan nyata. Bergabunglah dalam ekosistem layanan publik yang transparan dan responsif.”
        </p>
        
        <div class="space-y-6 pt-4">
          <div class="flex gap-4 items-start">
            <div class="w-10 h-10 rounded-xl bg-cobalt/10 flex items-center justify-center flex-shrink-0 mt-1">
              <i class="fa-solid fa-shield-halved text-cobalt text-lg"></i>
            </div>
            <div>
              <h3 class="font-bold text-navy-900 text-lg">Akses Aman & Terenkripsi</h3>
              <p class="text-gray-500 text-sm">Data pribadi Anda dilindungi dengan standar keamanan tinggi dan tidak akan disalahgunakan.</p>
            </div>
          </div>
          <div class="flex gap-4 items-start">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0 mt-1">
              <i class="fa-solid fa-timeline text-emerald-600 text-lg"></i>
            </div>
            <div>
              <h3 class="font-bold text-navy-900 text-lg">Pantau Pengaduan Real-time</h3>
              <p class="text-gray-500 text-sm">Dapatkan notifikasi status terbaru, dari proses verifikasi hingga selesai ditangani.</p>
            </div>
          </div>
          <div class="flex gap-4 items-start">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0 mt-1">
              <i class="fa-solid fa-chart-simple text-amber-500 text-lg"></i>
            </div>
            <div>
              <h3 class="font-bold text-navy-900 text-lg">Statistik & Laporan Publik</h3>
              <p class="text-gray-500 text-sm">Lihat dampak pengaduan Anda melalui dashboard analitik yang informatif.</p>
            </div>
          </div>
        </div>

        <!-- Testimonial mini -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-blue-100 mt-6">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cobalt to-navy-700 flex items-center justify-center text-white font-bold">AN</div>
            <div>
              <div class="font-bold text-navy-900 text-sm">Andi Nugroho</div>
              <div class="flex text-gold text-xs"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
            </div>
          </div>
          <p class="text-gray-600 text-sm italic">"Proses pendaftaran mudah, dan setelah bergabung saya bisa langsung melaporkan masalah lingkungan. Kurang dari 3 hari, laporan saya ditindaklanjuti. Luar biasa!"</p>
        </div>
        
        <!-- Tambahan statistik ringan -->
        <div class="flex gap-6 pt-4 text-sm text-gray-400">
          <div><i class="fa-regular fa-circle-check text-cobalt mr-1"></i> Gratis selamanya</div>
          <div><i class="fa-regular fa-clock text-cobalt mr-1"></i> Aktivasi instan</div>
        </div>
      </div>
      
      <!-- SISI KANAN: FORM INPUTAN (Email, Username, NIK, Nama Lengkap, Password) -->
      <div class="reveal" style="transition-delay:0.1s">
        <div class="bg-white rounded-3xl shadow-2xl border border-blue-100 overflow-hidden">
          <div class="bg-gradient-to-r from-cobalt to-navy-700 px-8 py-6">
            <h2 class="text-white font-display text-2xl font-bold">Buat Akun Baru</h2>
            <p class="text-blue-200 text-sm mt-1">Isi formulir di bawah untuk mendaftar sebagai pengadu aktif</p>
          </div>
          
          <form id="registrationForm" class="p-8 space-y-5">
            <!-- Email -->
            <div class="input-group">
              <label class="block text-navy-900 text-sm font-semibold mb-1.5 flex items-center gap-2">
                <i class="fa-regular fa-envelope text-cobalt text-xs"></i> Alamat Email
              </label>
              <input type="email" id="email" required
                     class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm"
                     placeholder="contoh: nama@email.com">
              <p class="text-xs text-gray-400 mt-1 hidden" id="emailError">Email harus valid</p>
            </div>
            
            <!-- Username -->
            <div class="input-group">
              <label class="block text-navy-900 text-sm font-semibold mb-1.5 flex items-center gap-2">
                <i class="fa-regular fa-user text-cobalt text-xs"></i> Username
              </label>
              <input type="text" id="username" required
                     class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm"
                     placeholder="pilih username unik">
              <p class="text-xs text-gray-400 mt-1">Minimal 4 karakter, hanya huruf/angka/garis bawah</p>
            </div>
            
            <!-- NIK (Nomor Induk Kependudukan) -->
            <div class="input-group">
              <label class="block text-navy-900 text-sm font-semibold mb-1.5 flex items-center gap-2">
                <i class="fa-regular fa-id-card text-cobalt text-xs"></i> NIK (16 digit)
              </label>
              <input type="text" id="nik" required maxlength="16"
                     class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm"
                     placeholder="Nomor Induk Kependudukan (16 angka)">
              <p class="text-xs text-gray-400 mt-1">Masukkan 16 digit NIK sesuai KTP</p>
            </div>
            
            <!-- Nama Lengkap -->
            <div class="input-group">
              <label class="block text-navy-900 text-sm font-semibold mb-1.5 flex items-center gap-2">
                <i class="fa-regular fa-address-card text-cobalt text-xs"></i> Nama Lengkap
              </label>
              <input type="text" id="fullname" required
                     class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm"
                     placeholder="Sesuai dengan KTP">
            </div>
            
            <!-- Password -->
            <div class="input-group">
              <label class="block text-navy-900 text-sm font-semibold mb-1.5 flex items-center gap-2">
                <i class="fa-solid fa-lock text-cobalt text-xs"></i> Kata Sandi
              </label>
              <div class="relative">
                <input type="password" id="password" required
                       class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm pr-10"
                       placeholder="Minimal 8 karakter">
                <button type="button" id="togglePassword" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-cobalt">
                  <i class="fa-regular fa-eye"></i>
                </button>
              </div>
              <p class="text-xs text-gray-400 mt-1">Kombinasi huruf, angka, dan simbol untuk keamanan ekstra</p>
            </div>
            
            <!-- Confirm Password (opsional tapi user friendly) -->
            <div class="input-group">
              <label class="block text-navy-900 text-sm font-semibold mb-1.5 flex items-center gap-2">
                <i class="fa-solid fa-check-circle text-cobalt text-xs"></i> Konfirmasi Kata Sandi
              </label>
              <input type="password" id="confirmPassword" required
                     class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm"
                     placeholder="Ulangi kata sandi">
            </div>
            
            <!-- Terms & Conditions -->
            <div class="flex items-start gap-3 pt-2">
              <input type="checkbox" id="termsCheckbox" class="checkbox-custom mt-0.5">
              <label for="termsCheckbox" class="text-xs text-gray-500 leading-relaxed">
                Saya menyetujui <a href="#" class="text-cobalt font-semibold hover:underline">Syarat & Ketentuan</a> serta 
                <a href="#" class="text-cobalt font-semibold hover:underline">Kebijakan Privasi</a> yang berlaku.
              </label>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" id="submitBtn" 
                    class="btn-primary w-full bg-gradient-to-r from-cobalt to-navy-700 text-white font-bold py-3.5 rounded-xl mt-4 hover:shadow-xl hover:shadow-cobalt/20 transition-all duration-300 flex items-center justify-center gap-2 text-sm">
              <i class="fa-solid fa-arrow-right-to-bracket"></i> Daftar Sekarang
            </button>
            
            <!-- Divider -->
            <div class="relative my-5">
              <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
              <div class="relative flex justify-center text-xs"><span class="bg-white px-3 text-gray-400">Atau daftar dengan</span></div>
            </div>
            
            <!-- Social buttons (opsional) -->
            <div class="grid grid-cols-2 gap-3">
              <button type="button" class="flex items-center justify-center gap-2 py-2.5 border border-gray-200 rounded-xl hover:bg-gray-50 transition text-sm font-medium text-gray-600">
                <i class="fa-brands fa-google text-red-500"></i> Google
              </button>
              <button type="button" class="flex items-center justify-center gap-2 py-2.5 border border-gray-200 rounded-xl hover:bg-gray-50 transition text-sm font-medium text-gray-600">
                <i class="fa-brands fa-facebook text-blue-600"></i> Facebook
              </button>
            </div>
            
            <p class="text-center text-xs text-gray-400 pt-2">
              Sudah punya akun? <a href="#" class="text-cobalt font-bold hover:underline">Masuk di sini</a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════ FOOTER SEDERHANA (SELARAS TEMA) ═══════════════════════════ -->
<footer class="footer-bg bg-gradient-to-r from-navy-900 to-navy-800 text-white mt-10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 border-t border-white/10 pt-8">
      <p class="text-blue-200 text-xs text-center">© 2025 Pengaduan Masyarakat — Layanan Publik Digital. Seluruh hak cipta dilindungi.</p>
      <div class="flex gap-4 text-xs text-blue-300">
        <a href="#" class="hover:text-white">Privasi</a>
        <a href="#" class="hover:text-white">Ketentuan</a>
        <a href="#" class="hover:text-white">Bantuan</a>
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

  // Smooth scroll untuk anchor navbar (opsional, jaga konsistensi)
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target && a.getAttribute('href') !== '#') { 
        e.preventDefault(); 
        target.scrollIntoView({ behavior: 'smooth', block: 'start' }); 
      }
    });
  });

  // ─── Show/Hide Password ─────────────────────────────────────────
  const togglePassBtn = document.getElementById('togglePassword');
  const passwordField = document.getElementById('password');
  if (togglePassBtn) {
    togglePassBtn.addEventListener('click', () => {
      const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordField.setAttribute('type', type);
      togglePassBtn.innerHTML = type === 'password' ? '<i class="fa-regular fa-eye"></i>' : '<i class="fa-regular fa-eye-slash"></i>';
    });
  }

  // ─── Validasi Form dan Simulasi Submit ──────────────────────────
  const form = document.getElementById('registrationForm');
  const emailInput = document.getElementById('email');
  const usernameInput = document.getElementById('username');
  const nikInput = document.getElementById('nik');
  const fullnameInput = document.getElementById('fullname');
  const passwordInput = document.getElementById('password');
  const confirmInput = document.getElementById('confirmPassword');
  const termsCheck = document.getElementById('termsCheckbox');
  const submitBtn = document.getElementById('submitBtn');

  // fungsi validasi realtime sederhana
  function validateForm() {
    let isValid = true;
    
    // email basic check
    const emailVal = emailInput.value.trim();
    const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
    if (!emailRegex.test(emailVal)) {
      document.getElementById('emailError')?.classList.remove('hidden');
      isValid = false;
    } else {
      document.getElementById('emailError')?.classList.add('hidden');
    }
    
    // username minimal 4 chars, alphanumeric + underscore
    const userVal = usernameInput.value.trim();
    const usernameRegex = /^[a-zA-Z0-9_]{4,}$/;
    if (!usernameRegex.test(userVal)) isValid = false;
    
    // NIK harus 16 digit angka
    const nikVal = nikInput.value.trim();
    const nikRegex = /^\d{16}$/;
    if (!nikRegex.test(nikVal)) isValid = false;
    
    // nama lengkap tidak boleh kosong
    if (fullnameInput.value.trim() === "") isValid = false;
    
    // password minimal 8 karakter
    if (passwordInput.value.length < 8) isValid = false;
    
    // konfirmasi password cocok
    if (passwordInput.value !== confirmInput.value) isValid = false;
    
    // checkbox setuju
    if (!termsCheck.checked) isValid = false;
    
    return isValid;
  }
  
  // realtime styling & button disabled/enable
  function updateSubmitButton() {
    const valid = validateForm();
    submitBtn.disabled = !valid;
    if (valid) {
      submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
      submitBtn.classList.add('hover:shadow-xl');
    } else {
      submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
      submitBtn.classList.remove('hover:shadow-xl');
    }
  }
  
  // event listeners untuk semua input
  const allInputs = [emailInput, usernameInput, nikInput, fullnameInput, passwordInput, confirmInput, termsCheck];
  allInputs.forEach(inp => {
    inp.addEventListener('input', updateSubmitButton);
    if (inp === termsCheck) inp.addEventListener('change', updateSubmitButton);
  });
  
  // custom indicator error untuk NIK agar batasan 16 digit secara visual (maxlength sudah di HTML)
  nikInput.addEventListener('input', (e) => {
    nikInput.value = nikInput.value.replace(/\D/g, '').slice(0,16);
    updateSubmitButton();
  });
  
  // password confirmation inline feedback (tambahan UI)
  confirmInput.addEventListener('input', () => {
    if (passwordInput.value !== confirmInput.value && confirmInput.value.length > 0) {
      confirmInput.style.borderColor = "#f87171";
    } else {
      confirmInput.style.borderColor = "#e2e8f0";
    }
    updateSubmitButton();
  });
  
  passwordInput.addEventListener('input', () => {
    if (confirmInput.value.length > 0 && passwordInput.value !== confirmInput.value) {
      confirmInput.style.borderColor = "#f87171";
    } else if (confirmInput.value.length > 0) {
      confirmInput.style.borderColor = "#10b981";
    } else {
      confirmInput.style.borderColor = "#e2e8f0";
    }
    updateSubmitButton();
  });
  
  // handle submit form
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!validateForm()) {
      alert("Mohon lengkapi data dengan benar dan centang persetujuan syarat & ketentuan.");
      return;
    }
    // Simulasi pendaftaran sukses
    alert(`Pendaftaran berhasil!\n\nSelamat datang, ${fullnameInput.value.trim()}!\nAkun dengan username "${usernameInput.value.trim()}" telah aktif. Silakan login untuk mengajukan pengaduan.`);
    // Optional: reset form? tidak direset biar user puas melihat data.
    // Bisa redirect ke halaman login (simulasi)
    // form.reset(); 
    // submitBtn.disabled = true;
  });
  
  // initial button state
  updateSubmitButton();
  
  // scroll reveal observer
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