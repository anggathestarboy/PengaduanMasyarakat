<?php
// Pastikan session sudah dimulai di halaman utama
// session_start() sudah dipanggil di file utama yang meng-include file ini
?>

<header id="topbar">
  <!-- Hamburger -->
  <button id="hamburger" class="md:hidden flex flex-col justify-center gap-1.5 w-9 h-9 rounded-lg hover:bg-azure-pale transition-colors mr-4" onclick="toggleSidebar()">
    <span class="block h-0.5 w-5 bg-ink-800 rounded"></span>
    <span class="block h-0.5 w-5 bg-ink-800 rounded"></span>
    <span class="block h-0.5 w-4 bg-ink-800 rounded"></span>
  </button>

  <!-- Page Title -->
  <div class="flex-1">
    <h1 class="font-display font-bold text-ink-900 text-lg leading-none">
      <?php 
        // Tentukan judul halaman berdasarkan file yang sedang dibuka
        $current_file = basename($_SERVER['PHP_SELF']);
        $page_title = 'Dashboard';
        
        if ($current_file == 'dashboard.php') {
            $page_title = 'Dashboard';
        } elseif ($current_file == 'pengaduanku.php') {
            $page_title = 'Pengaduan Saya';
        } elseif ($current_file == 'buat-pengaduan.php') {
            $page_title = 'Buat Pengaduan';
        } elseif ($current_file == 'profil.php') {
            $page_title = 'Profil Saya';
        } elseif ($current_file == 'pengaturan.php') {
            $page_title = 'Pengaturan';
        }
        
        echo htmlspecialchars($page_title);
      ?>
    </h1>
    <p class="text-xs text-slate-400 font-medium mt-0.5">
      <i class="fa-regular fa-calendar mr-1 text-azure"></i>
      <span id="current-date"></span>
    </p>
  </div>

  <!-- Right Actions -->
  <div class="flex items-center gap-3">

    <!-- Avatar with Dropdown -->
    <div class="relative">
      <div class="flex items-center gap-2.5 pl-3 border-l border-slate-border cursor-pointer group" onclick="toggleDropdown()">
        <div class="avatar-ring text-xs">
          <?php 
            // Ambil inisial dari username session
            $username = $_SESSION['username'] ?? 'admin';
            $initial = strtoupper(substr($username, 0, 1));
            echo htmlspecialchars($initial);
          ?>
        </div>
        <div class="hidden sm:block">
          <div class="text-[10px] text-slate-400">
            <?php 
              // Tampilkan role berdasarkan session (default: Pengguna)
              $role = $_SESSION['role'] ?? 'Pengguna';
              echo htmlspecialchars($role);
            ?>
          </div>
          <div class="text-xs font-medium text-ink-800"><?= htmlspecialchars($username) ?></div>
        </div>
        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1 group-hover:text-azure transition-colors"></i>
      </div>
      
      <!-- Dropdown Menu -->
      <div id="userDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-border overflow-hidden z-50 hidden">
        <div class="py-2">
          <!-- User Info -->
          <div class="px-4 py-3 border-b border-slate-border">
            <p class="text-xs text-slate-400">Masuk sebagai</p>
            <p class="text-sm font-semibold text-ink-900"><?= htmlspecialchars($username) ?></p>
            <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></p>
          </div>
          
          <!-- Menu Items -->
          <a href="profile.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-azure-pale hover:text-azure transition-colors">
            <i class="fa-regular fa-user w-4 text-azure"></i>
            <span>Profil Saya</span>
          </a>
         
         
          
          <!-- Divider -->
          <div class="border-t border-slate-border my-1"></div>
          
          <!-- Logout -->
          <form action="controller/LogoutController.php" method="POST">
            <button type="submit" name="logout" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
              <i class="fa-solid fa-right-from-bracket w-4"></i>
              <span>Keluar</span>
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</header>

<script>
  // Set current date
  const dateElement = document.getElementById('current-date');
  if (dateElement) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const today = new Date().toLocaleDateString('id-ID', options);
    dateElement.textContent = today;
  }

  // Toggle dropdown function
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