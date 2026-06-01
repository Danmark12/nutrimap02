<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | Organizational Chart</title>
  <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png">
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
              100: '#e8f5e9',
              200: '#c8e6c9',
              300: '#a5d6a7',
            }
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    /* Force green color on all teal/cyan elements */
    .text-teal-600, .text-teal-500, .text-teal-400, .text-teal-700,
    .text-cyan-600, .text-cyan-500, .text-cyan-400 {
      color: #3e9a3e !important;
    }
    .bg-teal-600, .bg-teal-700, .bg-teal-500,
    .bg-cyan-600, .bg-cyan-700 {
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
    .border-teal-500, .border-teal-600 {
      border-color: #3e9a3e !important;
    }
    /* Focus rings */
    *:focus-visible {
      outline: 2px solid #3e9a3e;
      outline-offset: 2px;
    }
    /* Footer link hover */
    .footer-links li a:hover {
      color: #3e9a3e !important;
    }
    /* Custom animations */
    .org-card {
      transition: all 0.3s ease;
      min-width: 160px;
      width: 160px;
      height: auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
    .org-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15);
    }
    .vacant-card {
      background: linear-gradient(135deg, #fff9e6, #fff);
      border-top-color: #f59e0b !important;
    }
    /* Uniform image styles */
    .org-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #3e9a3e;
      margin: 0 auto 8px auto;
      display: block;
    }
    .vacant-img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background-color: #e5e7eb;
      border: 3px solid #f59e0b;
      margin: 0 auto 8px auto;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .vacant-img i {
      font-size: 1.8rem;
      color: #9ca3af;
    }
    .top-node {
      position: relative;
    }
    .top-node::after {
      content: '';
      position: absolute;
      bottom: -15px;
      left: 50%;
      transform: translateX(-50%);
      width: 2px;
      height: 20px;
      background: linear-gradient(to bottom, #3e9a3e, transparent);
    }
    /* Vertical layout - no horizontal scroll */
    .vertical-container {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2rem;
    }
    .division-section {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.75rem;
    }
    .division-title {
      font-size: 0.8rem;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      padding: 0.3rem 1rem;
      border-radius: 20px;
      display: inline-block;
    }
    .division-title.technical {
      background: #e8f5e9;
      color: #2e7d32;
      border: 1px solid #a5d6a7;
    }
    .division-title.admin {
      background: #e3f2fd;
      color: #1565c0;
      border: 1px solid #90caf9;
    }
    .cards-row {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      justify-content: center;
      gap: 1rem;
    }
    /* Card content uniform styling */
    .org-card h3 {
      font-size: 0.75rem;
      font-weight: 700;
      line-height: 1.3;
      margin-bottom: 4px;
      color: #1f2937;
    }
    .org-card p {
      font-size: 0.65rem;
      font-weight: 500;
      line-height: 1.3;
      color: #3e9a3e;
    }
    .vacant-card p {
      color: #d97706;
    }
    /* Divider styling */
    .section-divider {
      width: 80%;
      max-width: 300px;
      height: 2px;
      background: linear-gradient(90deg, transparent, #a5d6a7, #3e9a3e, #a5d6a7, transparent);
      margin: 0.5rem 0;
    }
    /* Beautiful Background Pattern */
    .beautiful-bg {
      background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
      position: relative;
      overflow-x: hidden;
    }
    .beautiful-bg::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: 
        radial-gradient(circle at 10% 20%, rgba(62, 154, 62, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 90% 80%, rgba(62, 154, 62, 0.03) 0%, transparent 50%),
        repeating-linear-gradient(45deg, rgba(62, 154, 62, 0.02) 0px, rgba(62, 154, 62, 0.02) 2px, transparent 2px, transparent 8px);
      pointer-events: none;
      z-index: 0;
    }
    .beautiful-bg > * {
      position: relative;
      z-index: 1;
    }
    /* Subtle floating animation for decorative elements */
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
  </style>
</head>
<body class="flex flex-col min-h-screen beautiful-bg text-gray-800">

  <!-- Header with Green Theme - RESTORED OLD LAYOUT -->
  <header class="flex justify-between items-center px-6 md:px-10 h-14 bg-white/95 backdrop-blur-sm shadow-md relative z-50">
    <div class="flex items-center font-bold text-2xl text-gray-700">
      <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
      <img src="../../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
      <span class="text-nutrigreen-500">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <!-- Desktop nav -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../../../index.php" class="hover:text-nutrigreen-500 transition-colors duration-200">Home</a>
      <a href="../../map.php" class="hover:text-nutrigreen-500 transition-colors duration-200">Map</a>

      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 font-semibold text-gray-700 text-nutrigreen-500 cursor-pointer focus:outline-none transition-colors duration-200">
          About CNO
          <svg class="w-4 h-4 transition-transform duration-200" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
          </svg>
        </button>

        <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-white shadow-lg rounded-lg hidden z-50 overflow-hidden border border-gray-100">
          <a href="about.php" class="block px-4 py-2 hover:bg-nutrigreen-50 hover:text-nutrigreen-500 transition-colors duration-150">About</a>
          <a href="profile.php" class="block px-4 py-2 bg-nutrigreen-50 text-nutrigreen-500 font-bold transition-colors duration-150">Profile</a>
          <a href="vision.php" class="block px-4 py-2 hover:bg-nutrigreen-50 hover:text-nutrigreen-500 transition-colors duration-150">Vision</a>
          <a href="mission.php" class="block px-4 py-2 hover:bg-nutrigreen-50 hover:text-nutrigreen-500 transition-colors duration-150">Mission</a>
        </div>
      </div>

      <a href="../contact_us/contact.php" class="hover:text-nutrigreen-500 transition-colors duration-200">Contact Us</a>
      <a href="../../../login.php" class="bg-nutrigreen-500 text-white px-4 py-2 rounded-lg hover:bg-nutrigreen-600 transition-all duration-200 shadow-sm hover:shadow-md">Login</a>
    </nav>

    <!-- Mobile Burger -->
    <div class="md:hidden flex items-center">
      <button id="burgerBtn" class="text-gray-700 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden absolute top-full left-0 w-full bg-white shadow-lg z-20 flex flex-col">
      <a href="../../../index.php" class="px-6 py-3 border-b hover:bg-gray-50 transition-colors">Home</a>
      <a href="../../map.php" class="px-6 py-3 border-b hover:bg-gray-50 transition-colors">Map</a>

      <div class="flex flex-col">
        <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 border-b hover:bg-gray-50 focus:outline-none transition-colors">
          About CNO
          <svg id="mobileAboutArrow" class="w-4 h-4 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
          </svg>
        </button>
        <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
          <a href="about.php" class="px-8 py-2 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">About</a>
          <a href="profile.php" class="px-8 py-2 bg-nutrigreen-50 text-nutrigreen-500 font-bold">Profile</a>
          <a href="vision.php" class="px-8 py-2 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Vision</a>
          <a href="mission.php" class="px-8 py-2 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Mission</a>
        </div>
      </div>

      <a href="../contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-50 transition-colors">Contact Us</a>
      <a href="../../../login.php" class="px-6 py-3 hover:bg-gray-50 transition-colors">Login</a>
    </div>
  </header>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const burgerBtn = document.getElementById('burgerBtn');
      const mobileMenu = document.getElementById('mobileMenu');
      if (burgerBtn && mobileMenu) {
        burgerBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
      }

      const mobileAboutBtn = document.getElementById('mobileAboutBtn');
      const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
      const mobileAboutArrow = document.getElementById('mobileAboutArrow');
      if (mobileAboutBtn && mobileAboutDropdown) {
        mobileAboutBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          mobileAboutDropdown.classList.toggle('hidden');
          if (mobileAboutArrow) mobileAboutArrow.style.transform = mobileAboutDropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        });
      }

      document.addEventListener('click', (e) => {
        if (mobileMenu && !mobileMenu.contains(e.target) && burgerBtn && !burgerBtn.contains(e.target)) {
          if (mobileAboutDropdown) mobileAboutDropdown.classList.add('hidden');
          if (mobileAboutArrow) mobileAboutArrow.style.transform = 'rotate(0deg)';
        }
      });

      const aboutBtn = document.getElementById('aboutBtn');
      const aboutDropdown = document.getElementById('aboutDropdown');
      const aboutArrow = document.getElementById('aboutArrow');
      if (aboutBtn && aboutDropdown) {
        aboutBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          aboutDropdown.classList.toggle('hidden');
          if (aboutArrow) aboutArrow.style.transform = aboutDropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        });
      }
      document.addEventListener('click', (e) => {
        if (aboutDropdown && aboutBtn && !aboutBtn.contains(e.target) && !aboutDropdown.contains(e.target)) {
          aboutDropdown.classList.add('hidden');
          if (aboutArrow) aboutArrow.style.transform = 'rotate(0deg)';
        }
      });
    });
  </script>

  <!-- Organizational Chart - RESTORED OLD LAYOUT (Wider and bigger) -->
  <main class="flex-grow flex justify-center items-start p-6 md:p-10">
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl p-6 md:p-8 flex flex-col items-center w-full max-w-6xl border border-white/20">

      <!-- Decorative top bar -->
      <div class="w-20 h-1 bg-nutrigreen-500 rounded-full mb-6"></div>

      <!-- Logos -->
      <div class="flex justify-center items-center gap-8 md:gap-12 mb-6">
        <img src="../../../img/Ellipse_04.png" alt="Bagong Pilipinas Logo" class="h-12 md:h-14 object-contain">
        <img src="../../../img/Ellipse_02.png" alt="El Salvador City Logo" class="h-12 md:h-14 object-contain">
      </div>

      <!-- Chart Title -->
      <div class="text-center mb-8">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">ORGANIZATIONAL CHART</h1>
        <h1 class="text-xl md:text-2xl font-bold text-nutrigreen-500">CITY NUTRITION OFFICE</h1>
        <div class="flex items-center justify-center gap-2 mt-2">
          <i class="fas fa-map-marker-alt text-nutrigreen-500 text-sm"></i>
          <h3 class="text-sm text-gray-500">El Salvador City, Misamis Oriental</h3>
        </div>
      </div>

      <!-- Top Node - City Nutrition Action Officer -->
      <div class="relative mb-6 top-node">
        <div class="bg-gradient-to-br from-white to-nutrigreen-50 rounded-2xl border-t-4 border-nutrigreen-500 shadow-lg p-4 org-card" style="width: 180px;">
          <div class="relative inline-block mx-auto">
            <img src="../../../img/org/7.png" alt="Elma M. Clapano" class="org-img">
          </div>
          <h3 class="text-sm">Elma M. Clapano, RN</h3>
          <p class="text-xs">City Nutrition Action Officer</p>
        </div>
      </div>

      <!-- Connecting Line -->
      <div class="w-px h-5 bg-gradient-to-b from-nutrigreen-500 to-nutrigreen-300 mb-4"></div>

      <!-- Vacant Card - Nutrition Officer III -->
      <div class="mb-8">
        <div class="bg-gradient-to-br from-amber-50 to-white rounded-2xl border-t-4 border-amber-500 shadow-lg p-4 org-card vacant-card" style="width: 180px;">
          <div class="vacant-img">
            <i class="fas fa-user-slash"></i>
          </div>
          <h3 class="text-sm">Position Vacant</h3>
          <p class="text-xs">Nutrition Officer III</p>
        </div>
      </div>

      <!-- Decorative divider -->
      <div class="section-divider"></div>

      <!-- TECHNICAL DIVISION SECTION (Top) -->
      <div class="division-section">
        <div class="division-title technical">
          <i class="fas fa-chart-line mr-2"></i> TECHNICAL DIVISION
        </div>
        <div class="cards-row">
          <!-- Nutrition Officer II - Vacant -->
          <div class="bg-gradient-to-br from-amber-50 to-white rounded-2xl border-t-4 border-amber-500 shadow-lg p-4 org-card vacant-card">
            <div class="vacant-img">
              <i class="fas fa-user-slash"></i>
            </div>
            <h3 class="text-sm">Position Vacant</h3>
            <p class="text-xs">Nutrition Officer II</p>
          </div>

          <!-- Arlie Joy O. Damiles -->
          <div class="bg-white rounded-2xl border-t-4 border-nutrigreen-500 shadow-lg p-4 org-card">
            <img src="../../../img/org/5.png" alt="Arlie Joy O. Damiles" class="org-img">
            <h3 class="text-sm">Arlie Joy O. Damiles, RND</h3>
            <p class="text-xs">Nutritionist-Dietitian</p>
          </div>

          <!-- Karen Jay B. Lagala -->
          <div class="bg-white rounded-2xl border-t-4 border-nutrigreen-500 shadow-lg p-4 org-card">
            <img src="../../../img/org/4.png" alt="Karen Jay B. Lagala" class="org-img">
            <h3 class="text-sm">Karen Jay B. Lagala, RND</h3>
            <p class="text-xs">Nutritionist-Dietitian</p>
          </div>

          <!-- Jay S. Boctot -->
          <div class="bg-white rounded-2xl border-t-4 border-nutrigreen-500 shadow-lg p-4 org-card">
            <img src="../../../img/org/3.png" alt="Jay S. Boctot" class="org-img">
            <h3 class="text-sm">Jay S. Boctot, LPT</h3>
            <p class="text-xs">Senior Nutrition Supervisor</p>
          </div>

          <!-- City Nutrition Program Coordinator - Vacant -->
          <div class="bg-gradient-to-br from-amber-50 to-white rounded-2xl border-t-4 border-amber-500 shadow-lg p-4 org-card vacant-card">
            <div class="vacant-img">
              <i class="fas fa-user-slash"></i>
            </div>
            <h3 class="text-sm">Position Vacant</h3>
            <p class="text-xs">City Nutrition Program Coordinator</p>
          </div>
        </div>
      </div>

      <!-- Divider between divisions -->
      <div class="section-divider"></div>

      <!-- ADMINISTRATIVE DIVISION SECTION (Below) -->
      <div class="division-section">
        <div class="division-title admin">
          <i class="fas fa-users mr-2"></i> ADMINISTRATIVE DIVISION
        </div>
        <div class="cards-row">
          <!-- Antonette E. Vilbar -->
          <div class="bg-white rounded-2xl border-t-4 border-nutrigreen-500 shadow-lg p-4 org-card">
            <img src="../../../img/org/1.png" alt="Antonette E. Vilbar" class="org-img">
            <h3 class="text-sm">Antonette E. Vilbar</h3>
            <p class="text-xs">Administrative Aide III</p>
          </div>

          <!-- Edgar B. Napilas -->
          <div class="bg-white rounded-2xl border-t-4 border-nutrigreen-500 shadow-lg p-4 org-card">
            <img src="../../../img/org/6.png" alt="Edgar B. Napilas" class="org-img">
            <h3 class="text-sm">Edgar B. Napilas</h3>
            <p class="text-xs">Office Aide</p>
          </div>
        </div>
      </div>

      <!-- Footer note -->
      <div class="mt-8 pt-5 border-t border-gray-200 text-center">
        <p class="text-xs text-gray-400">
          <i class="fas fa-calendar-alt mr-1"></i> Updated as of 2025
        </p>
      </div>
    </div>
  </main>

 <!-- FOOTER -->
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

</body>
</html>