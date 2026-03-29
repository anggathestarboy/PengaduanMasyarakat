<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Buat Pengaduan — Pengaduan Masyarakat</title>
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

    /* File input styling */
    .preview-image {
      transition: all 0.3s ease;
    }
    .preview-image:hover {
      transform: scale(1.02);
    }
    
    /* Textarea styling */
    textarea.form-input-custom {
      resize: vertical;
      min-height: 100px;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<?php include "components/navbar.php"; ?>

<!-- HALAMAN BUAT PENGADUAN - CENTERED WITH 2 COLUMNS -->
<section class="min-h-screen pt-28 pb-20 bg-gradient-to-b from-[#F7F9FF] to-white flex items-center justify-center">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
    <div class="reveal">
      <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
        <div class="bg-gradient-to-r from-cobalt to-navy-700 px-6 py-5">
          <h2 class="text-white font-display text-2xl font-bold">Buat Pengaduan Baru</h2>
          <p class="text-blue-200 text-sm mt-1">Isi formulir di bawah dengan lengkap dan benar</p>
        </div>
        
        <form id="pengaduanForm" action="controller/CreatePengaduanController.php" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-5">
          
          <!-- Row 1: Judul Pengaduan (Full Width) -->
          <div class="input-group">
            <label class="block text-navy-900 text-sm font-semibold mb-2 flex items-center gap-2">
              <i class="fa-solid fa-heading text-cobalt text-sm"></i> Judul Pengaduan
            </label>
            <input type="text" name="title" id="title" required
                   class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-base"
                   placeholder="Contoh: Jalan Berlubang di Perumahan Griya Indah">
            <p class="text-xs text-gray-400 mt-1">Buat judul yang singkat, jelas, dan mencerminkan inti masalah</p>
          </div>
          
          <!-- Row 2: Deskripsi (Full Width) -->
          <div class="input-group">
            <label class="block text-navy-900 text-sm font-semibold mb-2 flex items-center gap-2">
              <i class="fa-solid fa-align-left text-cobalt text-sm"></i> Deskripsi Lengkap
            </label>
            <textarea name="description" id="description" required
                      class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-base"
                      rows="5"
                      placeholder="Jelaskan secara detail masalah yang terjadi, kapan kejadiannya, dan dampak yang ditimbulkan..."></textarea>
            <p class="text-xs text-gray-400 mt-1">Semakin detail deskripsi, semakin cepat penanganan (minimal 20 karakter)</p>
          </div>
          
          <!-- Row 3: Lokasi & Tanggal (2 Kolom) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
           
            
            <div class="input-group">
              <label class="block text-navy-900 text-sm font-semibold mb-2 flex items-center gap-2">
                <i class="fa-regular fa-calendar text-cobalt text-sm"></i> Tanggal & Waktu Kejadian
              </label>
              <input type="datetime-local" name="date" id="date" required
                     class="form-input-custom w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white transition-all text-base">
              <p class="text-xs text-gray-400 mt-1">Pilih tanggal dan waktu kejadian yang dilaporkan</p>
            </div>
          </div>
          
          <!-- Row 4: Upload Gambar (Full Width) -->
          <div class="input-group">
            <label class="block text-navy-900 text-sm font-semibold mb-2 flex items-center gap-2">
              <i class="fa-solid fa-image text-cobalt text-sm"></i> Bukti Foto / Dokumentasi
            </label>
            <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-cobalt transition-colors bg-gray-50 cursor-pointer">
              <input type="file" name="img" id="img" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
              <div class="flex flex-col items-center gap-2">
                <i class="fa-solid fa-cloud-upload-alt text-4xl text-gray-400"></i>
                <p class="text-sm text-gray-500 font-medium">Klik untuk upload gambar</p>
                <p class="text-xs text-gray-400">Format: JPG, PNG, JPEG (Maks 5MB)</p>
              </div>
            </div>
            <div id="imagePreview" class="mt-3 hidden">
              <div class="relative inline-block">
                <img id="previewImg" class="preview-image rounded-xl max-h-48 object-cover border border-gray-200 shadow-sm" alt="Preview">
                <button type="button" id="removeImage" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors text-xs shadow-md">
                  <i class="fa-solid fa-times"></i>
                </button>
              </div>
            </div>
            <p class="text-xs text-gray-400 mt-1">*Wajib diisi. Unggah foto sebagai bukti pendukung pengaduan Anda</p>
          </div>
          
          <!-- Submit Button -->
          <button type="submit" name="submit" id="submitBtn" 
                  class="btn-primary w-full bg-gradient-to-r from-cobalt to-navy-700 text-white font-bold py-3.5 rounded-xl mt-4 hover:shadow-lg hover:shadow-cobalt/20 transition-all duration-300 flex items-center justify-center gap-2 text-base">
            <i class="fa-solid fa-paper-plane"></i> Kirim Pengaduan
          </button>
          
          <!-- Info Tambahan -->
          <div class="bg-blue-50 rounded-xl p-3 mt-2">
            <div class="flex items-start gap-2">
              <i class="fa-solid fa-circle-info text-cobalt text-sm mt-0.5"></i>
              <p class="text-xs text-gray-600">Setelah pengaduan dikirim, Anda akan mendapatkan nomor tracking untuk memantau perkembangan laporan Anda.</p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script>
  // Image Preview
  const imgInput = document.getElementById('img');
  const imagePreview = document.getElementById('imagePreview');
  const previewImg = document.getElementById('previewImg');
  const removeImageBtn = document.getElementById('removeImage');
  
  if (imgInput) {
    imgInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
          alert('❌ Format file tidak didukung. Gunakan format JPG, JPEG, atau PNG.');
          imgInput.value = '';
          return false;
        }
        
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
          alert('❌ Ukuran file terlalu besar. Maksimal 5MB.');
          imgInput.value = '';
          return false;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImg.src = e.target.result;
          imagePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
      }
    });
    
    if (removeImageBtn) {
      removeImageBtn.addEventListener('click', function() {
        imgInput.value = '';
        imagePreview.classList.add('hidden');
        previewImg.src = '';
      });
    }
  }
  
  // Set default date to current datetime
  const dateInput = document.getElementById('date');
  if (dateInput) {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    dateInput.value = now.toISOString().slice(0, 16);
  }
  
  // Form validation
  const form = document.getElementById('pengaduanForm');
  const titleInput = document.getElementById('title');
  const descriptionInput = document.getElementById('description');
  
  form.addEventListener('submit', (e) => {
    // Validate title (min 5 characters)
    if (titleInput && titleInput.value.trim().length < 5) {
      e.preventDefault();
      alert("❌ Judul pengaduan minimal 5 karakter!");
      titleInput.focus();
      return false;
    }
    
    // Validate description (min 20 characters)
    if (descriptionInput && descriptionInput.value.trim().length < 20) {
      e.preventDefault();
      alert("❌ Deskripsi pengaduan minimal 20 karakter untuk memudahkan penanganan!");
      descriptionInput.focus();
      return false;
    }
    
    // Validate image
    if (imgInput && !imgInput.files.length) {
      e.preventDefault();
      alert("❌ Harap upload foto bukti pengaduan!");
      return false;
    }
    
    // Validate date
    if (dateInput && !dateInput.value) {
      e.preventDefault();
      alert("❌ Harap pilih tanggal kejadian!");
      dateInput.focus();
      return false;
    }
    
    return true;
  });
  
  // Auto-expand textarea
  const textarea = document.getElementById('description');
  if (textarea) {
    textarea.addEventListener('input', function() {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 200) + 'px';
    });
  }
  
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
  if(urlParams.get('status') === 'success') {
    alert("✅ Pengaduan berhasil dikirim! Terima kasih atas laporan Anda.");
    window.location.href = "index.php";
  } else if(urlParams.get('status') === 'failed') {
    alert("❌ Gagal mengirim pengaduan. Silakan coba lagi.");
  } else if(urlParams.get('status') === 'file_error') {
    alert("❌ Gagal mengupload file. Pastikan file gambar tidak rusak dan ukuran di bawah 5MB.");
  }
</script>
</body>
</html>