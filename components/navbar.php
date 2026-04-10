<?php
// JANGAN panggil session_start() di sini karena sudah dipanggil di halaman utama
// Session sudah dimulai di halaman utama (index.php, register.php, dll)
?>

<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-blue-50 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16 lg:h-18">

      <!-- Brand -->
      <a href="/" class="flex items-center gap-2.5 flex-shrink-0">
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
        <a href="index.php" class="nav-link px-4 py-2 text-sm font-semibold text-navy-900 rounded-lg hover:text-cobalt hover:bg-cobalt/5 transition-all duration-200">
          <i class="fa-solid fa-house mr-1.5 text-cobalt text-xs"></i>Home
        </a>
        <a href="index.php#pengaduan" class="nav-link px-4 py-2 text-sm font-semibold text-navy-900 rounded-lg hover:text-cobalt hover:bg-cobalt/5 transition-all duration-200">
          <i class="fa-solid fa-file-lines mr-1.5 text-cobalt text-xs"></i>Pengaduan
        </a>
        <a href="index.php#tentang" class="nav-link px-4 py-2 text-sm font-semibold text-navy-900 rounded-lg hover:text-cobalt hover:bg-cobalt/5 transition-all duration-200">
          <i class="fa-solid fa-circle-info mr-1.5 text-cobalt text-xs"></i>Tentang Kami
        </a>
      </div>

      <!-- Auth Buttons Desktop - Dynamic based on session -->
      <div class="hidden md:flex items-center gap-3" id="desktopAuthButtons">
        <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
          <!-- Profile Dropdown for Logged In User -->
          <div class="relative" id="profileDropdown">
            <button id="profileButton" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-gradient-to-r from-cobalt/10 to-navy-700/10 hover:from-cobalt/20 hover:to-navy-700/20 transition-all duration-200 border border-cobalt/20">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cobalt to-navy-700 flex items-center justify-center text-white font-bold text-sm shadow-md">
                <?php 
                  $initial = strtoupper(substr($_SESSION['username'], 0, 1));
                  echo htmlspecialchars($initial);
                ?>
              </div>
              <span class="text-sm font-semibold text-navy-900"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
              <i class="fa-solid fa-chevron-down text-cobalt text-xs transition-transform duration-200" id="dropdownIcon"></i>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="dropdownMenu" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden opacity-0 invisible transform -translate-y-2 transition-all duration-200 z-50">
              <div class="py-2">
                <div class="px-4 py-3 border-b border-gray-100">
                  <p class="text-xs text-gray-500">Masuk sebagai</p>
                  <p class="text-sm font-semibold text-navy-900"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                </div>
                <a href="profile.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-cobalt/5 hover:text-cobalt transition-colors">
                  <i class="fa-regular fa-user w-4 text-cobalt"></i>
                  <span>Profil Saya</span>
                </a>
                <a href="pengaduanku.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-cobalt/5 hover:text-cobalt transition-colors">
                  <i class="fa-regular fa-file-lines w-4 text-cobalt"></i>
                  <span>Pengaduanku</span>
                </a>
               
                <div class="border-t border-gray-100 mt-1 pt-1">
                  <form action="controller/LogoutController.php" method="POST" id="logoutForm">
                    <button type="submit" name="logout" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                      <i class="fa-solid fa-right-from-bracket w-4"></i>
                      <span>Keluar</span>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php else: ?>
          <!-- Login & Register Buttons for Guest -->
          <button onclick="window.location.href = 'login.php'" class="px-5 py-2 text-sm font-semibold text-cobalt border-2 border-cobalt rounded-xl hover:bg-cobalt hover:text-white transition-all duration-200">
            <i class="fa-solid fa-right-to-bracket mr-1.5"></i>Masuk
          </button>
          <button onclick="window.location.href = 'register.php'" class="btn-primary px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-cobalt to-navy-700 rounded-xl hover:shadow-lg hover:shadow-cobalt/30 transition-all duration-200">
            <i class="fa-solid fa-user-plus mr-1.5"></i>Daftar
          </button>
        <?php endif; ?>
      </div>

      <!-- Hamburger Mobile -->
      <button id="hamburger" class="md:hidden flex flex-col justify-center items-center w-10 h-10 rounded-xl hover:bg-cobalt/10 transition-colors" aria-label="Toggle Menu">
        <span id="h-line1" class="block w-5 h-0.5 bg-navy-900 rounded transition-all duration-300"></span>
        <span id="h-line2" class="block w-5 h-0.5 bg-navy-900 rounded mt-1.5 transition-all duration-300"></span>
        <span id="h-line3" class="block w-5 h-0.5 bg-navy-900 rounded mt-1.5 transition-all duration-300"></span>
      </button>
    </div>

    <!-- Mobile Menu - Dynamic based on session -->
    <div id="mobile-menu" class="md:hidden border-t border-blue-50">
      <div class="py-4 space-y-1">
        <a href="/" class="mobile-link flex items-center gap-3 px-4 py-3 text-sm font-semibold text-navy-900 rounded-xl hover:bg-cobalt/5 hover:text-cobalt transition-all">
          <i class="fa-solid fa-house w-5 text-cobalt text-center"></i>Home
        </a>
        <a href="/#pengaduan" class="mobile-link flex items-center gap-3 px-4 py-3 text-sm font-semibold text-navy-900 rounded-xl hover:bg-cobalt/5 hover:text-cobalt transition-all">
          <i class="fa-solid fa-file-lines w-5 text-cobalt text-center"></i>Pengaduan
        </a>
        <a href="/#tentang" class="mobile-link flex items-center gap-3 px-4 py-3 text-sm font-semibold text-navy-900 rounded-xl hover:bg-cobalt/5 hover:text-cobalt transition-all">
          <i class="fa-solid fa-circle-info w-5 text-cobalt text-center"></i>Tentang Kami
        </a>
        
        <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
          <!-- Mobile Profile Section for Logged In User -->
          <div class="pt-3 border-t border-blue-50 mt-2">
            <div class="flex items-center gap-3 px-4 py-3 mb-2">
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cobalt to-navy-700 flex items-center justify-center text-white font-bold shadow-md">
                <?php 
                  $initial = strtoupper(substr($_SESSION['username'], 0, 1));
                  echo htmlspecialchars($initial);
                ?>
              </div>
              <div>
                <p class="text-sm font-bold text-navy-900"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Pengguna'); ?></p>
              </div>
            </div>
            <a href="profile.php" class="mobile-link flex items-center gap-3 px-4 py-3 text-sm font-semibold text-navy-900 rounded-xl hover:bg-cobalt/5 hover:text-cobalt transition-all">
              <i class="fa-regular fa-user w-5 text-cobalt text-center"></i>Profil Saya
            </a>
            <a href="my-reports.php" class="mobile-link flex items-center gap-3 px-4 py-3 text-sm font-semibold text-navy-900 rounded-xl hover:bg-cobalt/5 hover:text-cobalt transition-all">
              <i class="fa-regular fa-file-lines w-5 text-cobalt text-center"></i>Pengaduanku
            </a>
            <a href="settings.php" class="mobile-link flex items-center gap-3 px-4 py-3 text-sm font-semibold text-navy-900 rounded-xl hover:bg-cobalt/5 hover:text-cobalt transition-all">
              <i class="fa-solid fa-gear w-5 text-cobalt text-center"></i>Pengaturan
            </a>
            <form action="controller/LogoutController.php" method="POST" class="mt-2">
              <button type="submit" name="logout" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-red-600 rounded-xl hover:bg-red-50 transition-all">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>Keluar
              </button>
            </form>
          </div>
        <?php else: ?>
          <!-- Mobile Auth Buttons for Guest -->
          <div class="pt-3 border-t border-blue-50 flex gap-3 px-4">
            <button onclick="window.location.href = 'login.php'" class="flex-1 py-2.5 text-sm font-semibold text-cobalt border-2 border-cobalt rounded-xl hover:bg-cobalt hover:text-white transition-all">
              <i class="fa-solid fa-right-to-bracket mr-1.5"></i>Masuk
            </button>
            <button onclick="window.location.href = 'register.php'" class="flex-1 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-cobalt to-navy-700 rounded-xl">
              <i class="fa-solid fa-user-plus mr-1.5"></i>Daftar
            </button>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<script>
// Dropdown menu functionality (hanya jika elemen ada di halaman)
document.addEventListener('DOMContentLoaded', function() {
  const profileButton = document.getElementById('profileButton');
  const dropdownMenu = document.getElementById('dropdownMenu');
  const dropdownIcon = document.getElementById('dropdownIcon');

  if (profileButton && dropdownMenu) {
    profileButton.addEventListener('click', (e) => {
      e.stopPropagation();
      const isVisible = dropdownMenu.classList.contains('opacity-100');
      
      if (isVisible) {
        dropdownMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
        dropdownMenu.classList.add('opacity-0', 'invisible', '-translate-y-2');
        if (dropdownIcon) dropdownIcon.style.transform = 'rotate(0deg)';
      } else {
        dropdownMenu.classList.remove('opacity-0', 'invisible', '-translate-y-2');
        dropdownMenu.classList.add('opacity-100', 'visible', 'translate-y-0');
        if (dropdownIcon) dropdownIcon.style.transform = 'rotate(180deg)';
      }
    });
    
    document.addEventListener('click', (e) => {
      if (!profileButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
        dropdownMenu.classList.add('opacity-0', 'invisible', '-translate-y-2');
        if (dropdownIcon) dropdownIcon.style.transform = 'rotate(0deg)';
      }
    });
  }

  // Mobile menu toggle (gunakan ID unik dari navbar)
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobile-menu');
  const l1 = document.getElementById('h-line1');
  const l2 = document.getElementById('h-line2');
  const l3 = document.getElementById('h-line3');
  let menuOpen = false;

  if (hamburger && mobileMenu) {
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
  }

  // Close mobile menu on link click
  document.querySelectorAll('.mobile-link').forEach(link => {
    link.addEventListener('click', () => {
      if (mobileMenu) {
        menuOpen = false;
        mobileMenu.classList.remove('open');
        if(l1 && l2 && l3) {
          l1.style.cssText = l2.style.cssText = l3.style.cssText = '';
        }
      }
    });
  });

  // Navbar scroll effect
  const navbar = document.getElementById('navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('navbar-scrolled', window.scrollY > 20);
    });
  }
});
</script>

<style>
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

/* Mobile menu styles */
#mobile-menu {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.4s cubic-bezier(0.4,0,0.2,1), opacity 0.3s;
  opacity: 0;
}
#mobile-menu.open {
  max-height: 480px;
  opacity: 1;
}

/* Navbar scroll effect */
.navbar-scrolled {
  background: rgba(255,255,255,0.97) !important;
  box-shadow: 0 2px 24px rgba(27,79,216,0.10) !important;
}
</style>