<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CNO NutriMap | Privacy Policy</title>
  <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png" />
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Tailwind Config for Green Theme -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'nutrigreen': {
              500: '#3e9a3e',
              600: '#2e7d32',
              700: '#1b5e20',
            }
          }
        }
      }
    }
  </script>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  
  <style>
    /* Override all teal/cyan colors to nutrigreen */
    .text-teal-600, .text-teal-500, .text-teal-400, .text-teal-700,
    .text-cyan-600, .text-cyan-500, .text-cyan-400 {
      color: #3e9a3e !important;
    }
    .bg-teal-600, .bg-teal-700, .bg-teal-500,
    .bg-cyan-600, .bg-cyan-700, .bg-cyan-500 {
      background-color: #3e9a3e !important;
    }
    .bg-teal-600:hover, .bg-teal-700:hover,
    .bg-cyan-600:hover, .bg-cyan-700:hover {
      background-color: #2e7d32 !important;
    }
    .hover\:text-teal-600:hover, .hover\:text-teal-500:hover,
    .hover\:text-cyan-600:hover, .hover\:text-cyan-500:hover {
      color: #3e9a3e !important;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #eef2f6 100%);
      font-family: 'Inter', sans-serif;
    }
    
    /* Policy section styling */
    .policy-section {
      transition: all 0.2s ease;
    }
    
    .policy-section:hover {
      transform: translateX(5px);
    }
    
    .policy-icon {
      transition: all 0.2s ease;
    }
    
    .policy-section:hover .policy-icon {
      transform: scale(1.05);
      background-color: #3e9a3e;
    }
    
    .policy-section:hover .policy-icon i {
      color: white !important;
    }
  </style>
</head>

<body class="flex flex-col min-h-screen text-gray-800">

  <!-- ====== HEADER (Updated to green theme) ====== -->
  <header class="flex justify-between items-center px-6 md:px-10 h-14 bg-white shadow-md relative z-50">
    <!-- Logo -->
    <div class="flex items-center space-x-2">
      <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 rounded-lg" />
      <img src="../../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 rounded-lg" />
      <h1 class="text-2xl font-bold text-gray-700">
        <span class="text-nutrigreen-500">CNO</span> NutriMap
      </h1>
    </div>

    <!-- Desktop Navigation -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../../../index.php" class="hover:text-nutrigreen-500 transition">Home</a>
      <a href="../../map.php" class="hover:text-nutrigreen-500 transition">Map</a>

      <!-- About Dropdown -->
      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 hover:text-nutrigreen-500 transition">
          About CNO
          <svg id="aboutArrow" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z" />
          </svg>
        </button>

        <div id="aboutDropdown" class="absolute left-0 mt-2 w-44 bg-white border border-gray-200 shadow-lg rounded-lg hidden z-50">
          <a href="../about_us/about.php" class="block px-4 py-2 hover:bg-gray-100">About</a>
          <a href="../about_us/profile.php" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
          <a href="../about_us/vision.php" class="block px-4 py-2 hover:bg-gray-100">Vision</a>
          <a href="../about_us/mission.php" class="block px-4 py-2 hover:bg-gray-100">Mission</a>
        </div>
      </div>

      <a href="../contact_us/contact.php" class="text-nutrigreen-500 transition">Contact</a>
      <a href="../../../login.php" class="bg-nutrigreen-500 text-white px-4 py-2 rounded-md hover:bg-nutrigreen-600 transition">Login</a>
    </nav>

    <!-- Mobile Menu Button -->
    <button id="burgerBtn" class="md:hidden text-gray-700 focus:outline-none">
      <i class="fas fa-bars text-2xl"></i>
    </button>
  </header>

  <!-- ====== MOBILE MENU ====== -->
  <div id="mobileMenu" class="hidden flex flex-col bg-white shadow-md md:hidden absolute top-[56px] left-0 w-full border-t border-gray-200 z-40">
    <a href="../../../index.php" class="px-6 py-3 border-b hover:bg-gray-100">Home</a>
    <a href="../../map.php" class="px-6 py-3 border-b hover:bg-gray-100">Map</a>

    <!-- Mobile About Dropdown -->
    <div class="flex flex-col border-b">
      <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 hover:bg-gray-100 focus:outline-none">
        About CNO
        <svg id="mobileAboutArrow" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z" />
        </svg>
      </button>
      <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
        <a href="../about_us/about.php" class="px-8 py-2 hover:bg-gray-200">About</a>
        <a href="../about_us/profile.php" class="px-8 py-2 hover:bg-gray-200">Profile</a>
        <a href="../about_us/vision.php" class="px-8 py-2 hover:bg-gray-200">Vision</a>
        <a href="../about_us/mission.php" class="px-8 py-2 hover:bg-gray-200">Mission</a>
      </div>
    </div>

    <a href="../contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-100">Contact</a>
    <a href="../../../login.php" class="px-6 py-3 hover:bg-gray-100">Login</a>
  </div>

  <!-- ====== MAIN CONTENT - NO CARD ====== -->
  <main class="container mx-auto px-4 md:px-6 py-8 max-w-4xl">
    
    <!-- Page Header (same style as other pages) -->
    <div class="text-center mb-10">
      <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
        Privacy <span class="text-nutrigreen-500">Policy</span>
      </h1>
      <div class="w-16 h-0.5 bg-nutrigreen-500 mx-auto rounded-full mb-4"></div>
      <p class="text-gray-500 text-sm">Mahalaga sa amin ang inyong privacy. Ipinapaliwanag ng policy na ito kung paano namin kinokolekta, ginagamit, at pinoprotektahan ang inyong personal na impormasyon.</p>
    </div>

    <!-- Policy Sections -->
    <div class="space-y-5">
      
      <!-- Section 1 -->
      <div class="policy-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
        <div class="flex items-start gap-4">
          <div class="policy-icon w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-regular fa-folder-open text-nutrigreen-500 text-sm"></i>
          </div>
          <div class="flex-1">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2">1. Impormasyong Kinokolekta Namin</h2>
            <p class="text-gray-600 leading-relaxed text-sm md:text-base">
              Kapag gumagamit kayo ng CNO NutriMap, maaari kaming mangolekta ng impormasyon na ibinigay ninyo nang kusa, tulad ng pangalan, email address, at iba pang contact details na isinumite sa pamamagitan ng forms.
            </p>
          </div>
        </div>
      </div>

      <!-- Section 2 -->
      <div class="policy-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
        <div class="flex items-start gap-4">
          <div class="policy-icon w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-chart-line text-nutrigreen-500 text-sm"></i>
          </div>
          <div class="flex-1">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2">2. Paano Namin Ginagamit ang Impormasyon</h2>
            <p class="text-gray-600 leading-relaxed text-sm md:text-base">
              Ginagamit namin ang impormasyon upang mapabuti ang serbisyo, tumugon sa inyong mga katanungan, at magbigay ng updates. Hindi namin ipagbebenta, ibabahagi, o ilalabas ang inyong impormasyon sa mga third parties maliban na lang kung kinakailangan ng batas.
            </p>
          </div>
        </div>
      </div>

      <!-- Section 3 -->
      <div class="policy-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
        <div class="flex items-start gap-4">
          <div class="policy-icon w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-shield-haltered text-nutrigreen-500 text-sm"></i>
          </div>
          <div class="flex-1">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2">3. Seguridad ng Datos</h2>
            <p class="text-gray-600 leading-relaxed text-sm md:text-base">
              Ginagamit namin ang iba't ibang security measures upang mapanatiling ligtas ang inyong personal na datos. Gayunpaman, walang method ng electronic storage na 100% ligtas.
            </p>
          </div>
        </div>
      </div>

      <!-- Section 4 -->
      <div class="policy-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
        <div class="flex items-start gap-4">
          <div class="policy-icon w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-regular fa-clock text-nutrigreen-500 text-sm"></i>
          </div>
          <div class="flex-1">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2">4. Pagbabago sa Policy</h2>
            <p class="text-gray-600 leading-relaxed text-sm md:text-base">
              Maaari naming i-update ang privacy policy na ito anumang oras. Inaabisuhan namin kayo na regular na suriin ang page na ito para sa mga pagbabago.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Your Rights Section -->
    <div class="mt-8 p-5 rounded-lg" style="background: rgba(62, 154, 62, 0.05);">
      <div class="flex items-start gap-3">
        <i class="fa-regular fa-hand-peace text-nutrigreen-500 text-xl mt-0.5"></i>
        <div>
          <h3 class="font-semibold text-gray-800 mb-1">Your Data Privacy Rights</h3>
          <p class="text-gray-600 text-sm leading-relaxed">Sa ilalim ng Data Privacy Act of 2012 (RA 10173), may karapatan kayong i-access, itama, o tanggalin ang inyong personal na impormasyon. Para sa mga katanungan, mangyaring makipag-ugnayan sa aming Data Protection Officer.</p>
        </div>
      </div>
    </div>

    <!-- Back to Legal Links -->
    <div class="mt-8 flex flex-wrap gap-3 justify-center">
      <a href="terms.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-nutrigreen-500 text-sm transition-colors">
        <i class="fa-regular fa-file-lines"></i>
        Terms of Use
      </a>
      <span class="text-gray-300">|</span>
      <a href="privacy.php" class="inline-flex items-center gap-2 text-nutrigreen-500 text-sm transition-colors">
        <i class="fa-regular fa-lock"></i>
        Privacy Policy
      </a>
      <span class="text-gray-300">|</span>
      <a href="cookies.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-nutrigreen-500 text-sm transition-colors">
        <i class="fa-solid fa-cookie-bite"></i>
        Cookies Policy
      </a>
    </div>
  </main>

  <!-- ====== FOOTER ====== -->
  <footer class="bg-gray-800 text-gray-300 py-10 mt-10">
    <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-5 gap-8">
      <!-- Logo -->
      <div class="md:col-span-2">
        <div class="flex items-center mb-4">
          <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2 rounded-lg" />
          <span class="text-nutrigreen-500 text-xl font-bold">CNO</span>
          <span class="text-white text-xl font-bold ml-1">NutriMap</span>
        </div>
        <p class="text-sm">A tool to visualize health and nutrition data for children in El Salvador City.</p>
      </div>

      <!-- About -->
      <div>
        <h3 class="text-white font-semibold text-lg mb-3">About Us</h3>
        <ul class="space-y-2">
          <li><a href="../about_us/mission.php" class="hover:text-nutrigreen-400 transition-colors">Our Mission</a></li>
          <li><a href="../about_us/vision.php" class="hover:text-nutrigreen-400 transition-colors">Our Vision</a></li>
        </ul>
      </div>

      <!-- Quick Links -->
      <div>
        <h3 class="text-white font-semibold text-lg mb-3">Quick Links</h3>
        <ul class="space-y-2">
          <li><a href="../../map.php" class="hover:text-nutrigreen-400 transition-colors">Map</a></li>
          <li><a href="../contact_us/contact.php" class="hover:text-nutrigreen-400 transition-colors">Contact Us</a></li>
        </ul>
      </div>

      <!-- Legal -->
      <div>
        <h3 class="text-white font-semibold text-lg mb-3">Legal & Support</h3>
        <ul class="space-y-2">
          <li><a href="../legal_and_support/terms.php" class="hover:text-nutrigreen-400 transition-colors">Terms of Use</a></li>
          <li><a href="../legal_and_support/privacy.php" class="hover:text-nutrigreen-400 transition-colors">Privacy Policy</a></li>
          <li><a href="../legal_and_support/cookies.php" class="hover:text-nutrigreen-400 transition-colors">Cookies</a></li>
          <li><a href="../help_and_support/help.php" class="hover:text-nutrigreen-400 transition-colors">Help</a></li>
          <li><a href="../help_and_support/faqs.php" class="hover:text-nutrigreen-400 transition-colors">FAQs</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400 text-sm">
      <p>Copyright &copy; 2025 CNO NutriMap. All Rights Reserved.<br>Developed By NBSC ICS 4th Year Student.</p>
    </div>
  </footer>

  <!-- ====== JS for Dropdowns ====== -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const aboutBtn = document.getElementById('aboutBtn');
      const aboutDropdown = document.getElementById('aboutDropdown');
      const aboutArrow = document.getElementById('aboutArrow');
      const burgerBtn = document.getElementById('burgerBtn');
      const mobileMenu = document.getElementById('mobileMenu');
      const mobileAboutBtn = document.getElementById('mobileAboutBtn');
      const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
      const mobileAboutArrow = document.getElementById('mobileAboutArrow');

      if (aboutBtn) {
        aboutBtn.addEventListener('click', e => {
          e.stopPropagation();
          aboutDropdown.classList.toggle('hidden');
          aboutArrow.classList.toggle('rotate-180');
        });
      }

      if (burgerBtn) {
        burgerBtn.addEventListener('click', () => {
          mobileMenu.classList.toggle('hidden');
        });
      }

      if (mobileAboutBtn) {
        mobileAboutBtn.addEventListener('click', e => {
          e.stopPropagation();
          mobileAboutDropdown.classList.toggle('hidden');
          mobileAboutArrow.classList.toggle('rotate-180');
        });
      }

      document.addEventListener('click', e => {
        if (aboutBtn && !aboutBtn.contains(e.target) && aboutDropdown && !aboutDropdown.classList.contains('hidden')) {
          aboutDropdown.classList.add('hidden');
          aboutArrow.classList.remove('rotate-180');
        }
      });
    });
  </script>
</body>
</html>