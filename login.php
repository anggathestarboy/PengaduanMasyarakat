<?php
// START SESSION DI AWAL FILE, SEBELUM HTML APAPUN
session_start();

// Redirect if already logged in
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Masuk — Pengaduan Masyarakat</title>
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

    /* Scroll reveal */
    .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }
    
    /* Dropdown styles */
    #dropdownMenu {
      transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
    }
    #dropdownMenu.opacity-100 {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    #dropdownMenu.opacity-0 {
      opacity: 0;
      visibility: hidden;
      transform: translateY(-8px);
    }
  </style>
</head>
<body>

<!-- ═══════════════════════════ NAVBAR ═══════════════════════════ -->
<?php include "components/navbar.php"; ?>

<!-- ═══════════════════════════ HALAMAN LOGIN (KANAN KIRI SEJAJAR) ═══════════════════════════ -->
<section class="min-h-screen pt-28 pb-20 bg-gradient-to-b from-[#F7F9FF] to-white flex items-center">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
    <div class="grid lg:grid-cols-2 gap-8 xl:gap-12 items-stretch">
      
      <!-- SISI KIRI: KONTEN INFORMASI -->
      <div class="reveal flex flex-col justify-center h-full">
        <div class="bg-white/60 backdrop-blur-sm rounded-3xl p-6 lg:p-8 border border-blue-100 shadow-lg">
          <div class="inline-flex items-center gap-2 bg-cobalt/10 text-cobalt rounded-full px-4 py-1.5 text-xs font-bold mb-5 tracking-widest uppercase">
            <i class="fa-solid fa-right-to-bracket"></i> Selamat Datang Kembali
          </div>
          <h1 class="font-display text-3xl lg:text-4xl font-bold text-navy-900 leading-tight mb-4">
            Masuk ke <span class="text-cobalt">Akun Anda</span>
          </h1>
          <p class="text-gray-500 text-sm leading-relaxed border-l-4 border-cobalt pl-4 mb-6">
            “Akses dashboard pribadi Anda, pantau pengaduan, dan ikuti perkembangan terbaru dari laporan yang telah disampaikan.”
          </p>
          
          <div class="space-y-4">
            <div class="flex gap-3 items-start">
              <div class="w-8 h-8 rounded-lg bg-cobalt/10 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-chart-line text-cobalt text-sm"></i>
              </div>
              <div>
                <h3 class="font-bold text-navy-900 text-sm">Pantau Status Pengaduan</h3>
                <p class="text-gray-500 text-xs">Lihat perkembangan terbaru dari setiap laporan yang Anda buat.</p>
              </div>
            </div>
            <div class="flex gap-3 items-start">
              <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-bell text-emerald-600 text-sm"></i>
              </div>
              <div>
                <h3 class="font-bold text-navy-900 text-sm">Notifikasi Real-time</h3>
                <p class="text-gray-500 text-xs">Dapatkan pemberitahuan langsung saat ada perubahan status.</p>
              </div>
            </div>
            <div class="flex gap-3 items-start">
              <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-clock-rotate-left text-amber-500 text-sm"></i>
              </div>
              <div>
                <h3 class="font-bold text-navy-900 text-sm">Riwayat Lengkap</h3>
                <p class="text-gray-500 text-xs">Akses semua pengaduan yang pernah Anda laporkan kapan saja.</p>
              </div>
            </div>
          </div>
          
          <div class="flex gap-4 pt-5 mt-2 text-xs text-gray-400 border-t border-gray-100">
            <div><i class="fa-regular fa-circle-check text-cobalt mr-1"></i> Akses 24/7</div>
            <div><i class="fa-regular fa-shield-halved text-cobalt mr-1"></i> Aman & Terpercaya</div>
          </div>
        </div>
      </div>
      
      <!-- SISI KANAN: FORM LOGIN (USERNAME & PASSWORD SAJA) -->
      <div class="reveal" style="transition-delay:0.1s">
        <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
          <div class="bg-gradient-to-r from-cobalt to-navy-700 px-6 py-4">
            <h2 class="text-white font-display text-xl font-bold">Masuk ke Akun</h2>
            <p class="text-blue-200 text-xs mt-0.5">Gunakan username dan password Anda</p>
          </div>
          
          <form id="loginForm" action="controller/LoginController.php" method="POST" class="p-6 space-y-5">
            <!-- Username Field -->
            <div class="input-group">
              <label class="block text-navy-900 text-sm font-semibold mb-2 flex items-center gap-2">
                <i class="fa-regular fa-user text-cobalt text-sm"></i> Username
              </label>
              <input type="text" name="username" id="username" required
                     class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-base"
                     placeholder="Masukkan username Anda">
              <p class="text-xs text-gray-400 mt-1">Contoh: john_doe, admin123</p>
            </div>
            
            <!-- Password Field -->
            <div class="input-group">
              <label class="block text-navy-900 text-sm font-semibold mb-2 flex items-center gap-2">
                <i class="fa-solid fa-lock text-cobalt text-sm"></i> Kata Sandi
              </label>
              <div class="relative">
                <input type="password" name="password" id="password" required
                       class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-base pr-12"
                       placeholder="Masukkan kata sandi Anda">
                <button type="button" id="togglePassword" class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-cobalt transition-colors">
                  <i class="fa-regular fa-eye"></i>
                </button>
              </div>
              <p class="text-xs text-gray-400 mt-1">Password yang didaftarkan saat registrasi</p>
            </div>
            
            <!-- Forgot Password Link -->
            <div class="flex justify-end">
              <a href="forgot-password.php" class="text-xs text-cobalt hover:underline font-medium">Lupa kata sandi?</a>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" name="login" id="submitBtn" 
                    class="btn-primary w-full bg-gradient-to-r from-cobalt to-navy-700 text-white font-bold py-3.5 rounded-xl mt-2 hover:shadow-lg hover:shadow-cobalt/20 transition-all duration-300 flex items-center justify-center gap-2 text-base">
              <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sekarang
            </button>
            
            <!-- Divider -->
            <div class="relative my-6">
              <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
              <div class="relative flex justify-center text-xs"><span class="bg-white px-3 text-gray-400">Belum punya akun?</span></div>
            </div>
            
            <!-- Register Link -->
            <p class="text-center text-sm text-gray-500">
              <a href="register.php" class="text-cobalt font-bold hover:underline text-base">Daftar Sekarang</a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  // Toggle Password Visibility
  const togglePassBtn = document.getElementById('togglePassword');
  const passwordField = document.getElementById('password');
  if (togglePassBtn && passwordField) {
    togglePassBtn.addEventListener('click', () => {
      const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordField.setAttribute('type', type);
      togglePassBtn.innerHTML = type === 'password' ? '<i class="fa-regular fa-eye"></i>' : '<i class="fa-regular fa-eye-slash"></i>';
    });
  }

  // Form validation before submit
  const form = document.getElementById('loginForm');
  const usernameInput = document.getElementById('username');
  const passwordInput = document.getElementById('password');
  
  form.addEventListener('submit', (e) => {
    // Validate username tidak boleh kosong
    if (usernameInput && !usernameInput.value.trim()) {
      e.preventDefault();
      alert("⚠️ Username tidak boleh kosong!");
      usernameInput.focus();
      return false;
    }
    
    // Validate password tidak boleh kosong
    if (passwordInput && !passwordInput.value) {
      e.preventDefault();
      alert("⚠️ Kata sandi tidak boleh kosong!");
      passwordInput.focus();
      return false;
    }
    
    return true;
  });
  
  // Scroll reveal
  const revealEls = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        setTimeout(() => e.target.classList.add('visible'), 100);
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });
  revealEls.forEach(el => observer.observe(el));
  
  // URL params notifications (menampilkan pesan error jika ada)
  const urlParams = new URLSearchParams(window.location.search);
  if(urlParams.get('login_status') === 'failed') {
    alert("❌ Login gagal! Username atau password salah.");
  } else if(urlParams.get('login_status') === 'empty') {
    alert("⚠️ Username dan password harus diisi!");
  }
</script>
</body>
</html>