<?php
// START SESSION DI AWAL FILE, SEBELUM HTML APAPUN
session_start();

// Optional: Redirect if already logged in
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

<!-- ═══════════════════════════ HALAMAN DAFTAR (KANAN KIRI SEJAJAR) ═══════════════════════════ -->
<section class="min-h-screen pt-28 pb-20 bg-gradient-to-b from-[#F7F9FF] to-white flex items-center">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
    <div class="grid lg:grid-cols-2 gap-8 xl:gap-12 items-stretch">
      
      <!-- SISI KIRI: KONTEN INFORMASI (TIDAK MELEBAR) -->
      <div class="reveal flex flex-col justify-center h-full">
        <div class="bg-white/60 backdrop-blur-sm rounded-3xl p-6 lg:p-8 border border-blue-100 shadow-lg">
          <div class="inline-flex items-center gap-2 bg-cobalt/10 text-cobalt rounded-full px-4 py-1.5 text-xs font-bold mb-5 tracking-widest uppercase">
            <i class="fa-solid fa-user-plus"></i> Bergabung Sekarang
          </div>
          <h1 class="font-display text-3xl lg:text-4xl font-bold text-navy-900 leading-tight mb-4">
            Daftar Akun <span class="text-cobalt">Pengadu</span> Digital
          </h1>
          <p class="text-gray-500 text-sm leading-relaxed border-l-4 border-cobalt pl-4 mb-6">
            “Suara Anda adalah langkah pertama menuju perubahan nyata. Bergabunglah dalam ekosistem layanan publik yang transparan dan responsif.”
          </p>
          
          <div class="space-y-4">
            <div class="flex gap-3 items-start">
              <div class="w-8 h-8 rounded-lg bg-cobalt/10 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-shield-halved text-cobalt text-sm"></i>
              </div>
              <div>
                <h3 class="font-bold text-navy-900 text-sm">Akses Aman & Terenkripsi</h3>
                <p class="text-gray-500 text-xs">Data pribadi dilindungi dengan standar keamanan tinggi.</p>
              </div>
            </div>
            <div class="flex gap-3 items-start">
              <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-timeline text-emerald-600 text-sm"></i>
              </div>
              <div>
                <h3 class="font-bold text-navy-900 text-sm">Pantau Pengaduan Real-time</h3>
                <p class="text-gray-500 text-xs">Dapatkan notifikasi status terbaru dari pengaduan Anda.</p>
              </div>
            </div>
            <div class="flex gap-3 items-start">
              <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-chart-simple text-amber-500 text-sm"></i>
              </div>
              <div>
                <h3 class="font-bold text-navy-900 text-sm">Statistik & Laporan Publik</h3>
                <p class="text-gray-500 text-xs">Lihat dampak pengaduan melalui dashboard analitik.</p>
              </div>
            </div>
          </div>
          
          <div class="flex gap-4 pt-5 mt-2 text-xs text-gray-400 border-t border-gray-100">
            <div><i class="fa-regular fa-circle-check text-cobalt mr-1"></i> Gratis selamanya</div>
            <div><i class="fa-regular fa-clock text-cobalt mr-1"></i> Aktivasi instan</div>
          </div>
        </div>
      </div>
      
      <!-- SISI KANAN: FORM REGISTER (TIDAK MELEBAR) -->
      <div class="reveal" style="transition-delay:0.1s">
        <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
          <div class="bg-gradient-to-r from-cobalt to-navy-700 px-6 py-4">
            <h2 class="text-white font-display text-xl font-bold">Buat Akun Baru</h2>
            <p class="text-blue-200 text-xs mt-0.5">Isi formulir di bawah untuk mendaftar</p>
          </div>
          
          <form id="registrationForm" action="../controller/RegisterController.php" method="POST" class="p-6 space-y-4">
            <!-- Row 1: Email & Username (2 kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="input-group">
                <label class="block text-navy-900 text-xs font-semibold mb-1 flex items-center gap-1">
                  <i class="fa-regular fa-envelope text-cobalt text-[10px]"></i> Email
                </label>
                <input type="email" name="email" id="email" required
                       class="form-input-custom w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm"
                       placeholder="nama@email.com">
              </div>
              
              <div class="input-group">
                <label class="block text-navy-900 text-xs font-semibold mb-1 flex items-center gap-1">
                  <i class="fa-regular fa-user text-cobalt text-[10px]"></i> Username
                </label>
                <input type="text" name="username" id="username" required
                       class="form-input-custom w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm"
                       placeholder="username unik">
              </div>
            </div>
            
            <!-- Row 2: NIK & Fullname (2 kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="input-group">
                <label class="block text-navy-900 text-xs font-semibold mb-1 flex items-center gap-1">
                  <i class="fa-regular fa-id-card text-cobalt text-[10px]"></i> NIK (16 digit)
                </label>
                <input type="text" name="nik" id="nik" maxlength="16" required
                       class="form-input-custom w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm"
                       placeholder="16 digit NIK">
              </div>
              
              <div class="input-group">
                <label class="block text-navy-900 text-xs font-semibold mb-1 flex items-center gap-1">
                  <i class="fa-regular fa-address-card text-cobalt text-[10px]"></i> Nama Lengkap
                </label>
                <input type="text" name="fullname" id="fullname" required
                       class="form-input-custom w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm"
                       placeholder="Sesuai KTP">
              </div>
            </div>
            
            <!-- Row 3: Password & Confirm Password (2 kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="input-group">
                <label class="block text-navy-900 text-xs font-semibold mb-1 flex items-center gap-1">
                  <i class="fa-solid fa-lock text-cobalt text-[10px]"></i> Kata Sandi
                </label>
                <div class="relative">
                  <input type="password" name="password" id="password" required
                         class="form-input-custom w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm pr-8"
                         placeholder="Min. 8 karakter">
                  <button type="button" id="togglePassword" class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-cobalt text-xs">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
              </div>
              
              <div class="input-group">
                <label class="block text-navy-900 text-xs font-semibold mb-1 flex items-center gap-1">
                  <i class="fa-solid fa-check-circle text-cobalt text-[10px]"></i> Konfirmasi
                </label>
                <input type="password" id="confirmPassword"
                       class="form-input-custom w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 focus:bg-white transition-all text-sm"
                       placeholder="Ulangi kata sandi">
              </div>
            </div>
            
            <!-- Terms Checkbox -->
            <div class="flex items-start gap-2 pt-1">
              <input type="checkbox" id="termsCheckbox" class="checkbox-custom mt-0.5 w-4 h-4" required>
              <label for="termsCheckbox" class="text-[11px] text-gray-500 leading-relaxed">
                Saya menyetujui <a href="#" class="text-cobalt font-semibold hover:underline">Syarat & Ketentuan</a> & 
                <a href="#" class="text-cobalt font-semibold hover:underline">Kebijakan Privasi</a>.
              </label>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" name="register" id="submitBtn" 
                    class="btn-primary w-full bg-gradient-to-r from-cobalt to-navy-700 text-white font-bold py-3 rounded-xl mt-2 hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2 text-sm">
              <i class="fa-solid fa-arrow-right-to-bracket"></i> Daftar Sekarang
            </button>
            
            <!-- Login Link -->
            <p class="text-center text-[11px] text-gray-400 pt-2">
              Sudah punya akun? <a href="/login.php" class="text-cobalt font-bold hover:underline">Masuk di sini</a>
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

  // Confirm Password Validation
  const confirmInput = document.getElementById('confirmPassword');
  const passwordInput = document.getElementById('password');
  const form = document.getElementById('registrationForm');
  const termsCheckbox = document.getElementById('termsCheckbox');
  
  if (confirmInput && passwordInput) {
    const validateConfirm = () => {
      if (passwordInput.value !== confirmInput.value && confirmInput.value.length > 0) {
        confirmInput.style.borderColor = "#f87171";
      } else if (confirmInput.value.length > 0 && passwordInput.value === confirmInput.value) {
        confirmInput.style.borderColor = "#10b981";
      } else {
        confirmInput.style.borderColor = "#e2e8f0";
      }
    };
    confirmInput.addEventListener('input', validateConfirm);
    passwordInput.addEventListener('input', validateConfirm);
  }
  
  // NIK only numbers and max 16 digits
  const nikInput = document.getElementById('nik');
  if (nikInput) {
    nikInput.addEventListener('input', (e) => {
      nikInput.value = nikInput.value.replace(/\D/g, '').slice(0,16);
    });
  }

  // Form validation before submit
  form.addEventListener('submit', (e) => {
    if (passwordInput && confirmInput && passwordInput.value !== confirmInput.value) {
      e.preventDefault();
      alert("❌ Konfirmasi kata sandi tidak cocok!");
      confirmInput.focus();
      return false;
    }
    
    if (termsCheckbox && !termsCheckbox.checked) {
      e.preventDefault();
      alert("⚠️ Anda harus menyetujui Syarat & Ketentuan untuk melanjutkan.");
      termsCheckbox.focus();
      return false;
    }
    
    if (passwordInput && passwordInput.value.length < 8) {
      e.preventDefault();
      alert("🔐 Kata sandi minimal 8 karakter.");
      passwordInput.focus();
      return false;
    }
    
    if (nikInput && nikInput.value.length !== 16) {
      e.preventDefault();
      alert("📇 NIK harus 16 digit angka.");
      nikInput.focus();
      return false;
    }
    
    const usernameField = document.getElementById('username');
    if (usernameField) {
      const usernameVal = usernameField.value.trim();
      const usernameRegex = /^[a-zA-Z0-9_]{4,}$/;
      if (!usernameRegex.test(usernameVal)) {
        e.preventDefault();
        alert("👤 Username minimal 4 karakter, hanya huruf, angka, dan underscore.");
        usernameField.focus();
        return false;
      }
    }
    
    const emailField = document.getElementById('email');
    if (emailField) {
      const emailVal = emailField.value.trim();
      const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
      if (!emailRegex.test(emailVal)) {
        e.preventDefault();
        alert("📧 Alamat email tidak valid.");
        emailField.focus();
        return false;
      }
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
  
  // URL params notifications
  const urlParams = new URLSearchParams(window.location.search);
  if(urlParams.get('reg_status') === 'duplicate') {
    alert("Username atau Email sudah digunakan. Silakan gunakan yang lain.");
  } else if(urlParams.get('reg_status') === 'success') {
    alert("Pendaftaran berhasil! Anda akan diarahkan ke halaman utama.");
    window.location.href = "index.php";
  }
</script>
</body>
</html>