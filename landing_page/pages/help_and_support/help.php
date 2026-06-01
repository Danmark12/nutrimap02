<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | Help & Support</title>
  <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png">
  
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
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
    
    /* Help section styling */
    .help-section {
      transition: all 0.2s ease;
    }
    
    .help-section:hover {
      transform: translateX(5px);
    }
    
    .icon-circle {
      transition: all 0.2s ease;
    }
    
    .help-section:hover .icon-circle {
      transform: scale(1.05);
      background-color: #3e9a3e;
    }
    
    .help-section:hover .icon-circle i {
      color: white !important;
    }
  </style>
</head>
<body class="flex flex-col min-h-screen text-gray-800">

  <!-- HEADER (Updated to green theme) -->
  <header class="flex justify-between items-center px-6 md:px-10 h-14 bg-white shadow relative z-50">
    <!-- Logo -->
    <div class="flex items-center font-bold text-2xl text-gray-700">
      <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
      <img src="../../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
      <span class="text-nutrigreen-500">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <!-- Desktop Navigation -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../../../index.php" class="hover:text-nutrigreen-500">Home</a>
      <a href="../../map.php" class="hover:text-nutrigreen-500">Map</a>

      <!-- Dropdown -->
      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 text-gray-700 hover:text-nutrigreen-500 focus:outline-none">
          About CNO
          <svg id="aboutArrow" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
          </svg>
        </button>

        <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-white border border-gray-200 shadow-md rounded hidden z-50">
          <a href="../about_us/about.php" class="block px-4 py-2 hover:bg-gray-100">About</a>
          <a href="../about_us/profile.php" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
          <a href="../about_us/vision.php" class="block px-4 py-2 hover:bg-gray-100">Vision</a>
          <a href="../about_us/mission.php" class="block px-4 py-2 hover:bg-gray-100">Mission</a>
        </div>
      </div>

      <a href="../contact_us/contact.php" class="text-nutrigreen-500">Contact Us</a>
      <a href="../../../login.php" class="bg-nutrigreen-500 text-white px-4 py-2 rounded hover:bg-nutrigreen-600 transition">Login</a>
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
    <div id="mobileMenu" class="hidden absolute top-full left-0 w-full bg-white shadow-md z-20 flex flex-col">
      <a href="../../../index.php" class="px-6 py-3 border-b hover:bg-gray-100">Home</a>
      <a href="../../map.php" class="px-6 py-3 border-b hover:bg-gray-100">Map</a>

      <!-- Mobile Dropdown -->
      <div class="flex flex-col">
        <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 border-b hover:bg-gray-100 focus:outline-none">
          About CNO
          <svg id="mobileAboutArrow" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
          </svg>
        </button>
        <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
          <a href="../about_us/about.php" class="px-8 py-2 hover:bg-gray-200">About</a>
          <a href="../about_us/profile.php" class="px-8 py-2 hover:bg-gray-200">Profile</a>
          <a href="../about_us/vision.php" class="px-8 py-2 hover:bg-gray-200">Vision</a>
          <a href="../about_us/mission.php" class="px-8 py-2 hover:bg-gray-200">Mission</a>
        </div>
      </div>

      <a href="../contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-100">Contact Us</a>
      <a href="../../../login.php" class="px-6 py-3 hover:bg-gray-100">Login</a>
    </div>
  </header>

  <!-- MAIN CONTENT - CLEAN, NO CARD -->
  <main class="container mx-auto px-4 md:px-6 py-8 max-w-4xl">
    
    <!-- Page Header (same style as contact page) -->
    <div class="text-center mb-10">
      <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
        Help & <span class="text-nutrigreen-500">Support</span>
      </h1>
      <div class="w-16 h-0.5 bg-nutrigreen-500 mx-auto rounded-full mb-4"></div>
      <p class="text-gray-500 text-sm">Your guide to navigating and using CNO NutriMap effectively</p>
    </div>

    <!-- Welcome message -->
    <div class="mb-10 p-5 rounded-lg" style="background: rgba(62, 154, 62, 0.05);">
      <div class="flex items-start gap-3">
        <i class="fa-regular fa-circle-question text-nutrigreen-500 text-xl mt-0.5"></i>
        <p class="text-gray-700 leading-relaxed text-sm md:text-base">Welcome to the Help & Support section of the CNO NutriMap system. This page is designed to guide visitors in exploring and using the Health and Nutrition Data Management System effectively.</p>
      </div>
    </div>

    <!-- Help Sections -->
    <div class="space-y-6">
      
      <!-- Section 1 - Navigating the System -->
      <div class="help-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
        <div class="flex items-start gap-4">
          <div class="icon-circle w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-compass text-nutrigreen-500 text-sm"></i>
          </div>
          <div class="flex-1">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-2">Navigating the System</h2>
            <p class="text-gray-600 leading-relaxed text-sm md:text-base mb-3">The website provides access to community nutrition data for El Salvador City, Misamis Oriental, managed by the City Nutrition Office. Key features include:</p>
            <ul class="space-y-2 pl-0">
              <li class="flex items-start gap-2 text-gray-600 text-sm">
                <i class="fa-solid fa-chart-line text-nutrigreen-500 mt-0.5 text-xs"></i>
                <span>Viewing aggregated nutrition and health statistics at the barangay and city level.</span>
              </li>
              <li class="flex items-start gap-2 text-gray-600 text-sm">
                <i class="fa-solid fa-map-location-dot text-nutrigreen-500 mt-0.5 text-xs"></i>
                <span>Interactive GIS maps for visualizing local trends in nutrition and health indicators.</span>
              </li>
              <li class="flex items-start gap-2 text-gray-600 text-sm">
                <i class="fa-regular fa-newspaper text-nutrigreen-500 mt-0.5 text-xs"></i>
                <span>Accessing reports and updates published by the City Nutrition Office.</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Section 2 - Troubleshooting and Assistance -->
      <div class="help-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
        <div class="flex items-start gap-4">
          <div class="icon-circle w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-screwdriver-wrench text-nutrigreen-500 text-sm"></i>
          </div>
          <div class="flex-1">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-2">Troubleshooting and Assistance</h2>
            <p class="text-gray-600 leading-relaxed text-sm md:text-base mb-3">If you encounter technical issues, such as pages not loading correctly or errors in data display, you can:</p>
            <ul class="space-y-2 pl-0">
              <li class="flex items-start gap-2 text-gray-600 text-sm">
                <i class="fa-regular fa-trash-can text-nutrigreen-500 mt-0.5 text-xs"></i>
                <span>Clear your browser cache and refresh the page.</span>
              </li>
              <li class="flex items-start gap-2 text-gray-600 text-sm">
                <i class="fa-solid fa-mobile-screen-button text-nutrigreen-500 mt-0.5 text-xs"></i>
                <span>Try accessing the website from a different device or browser.</span>
              </li>
              <li class="flex items-start gap-2 text-gray-600 text-sm">
                <i class="fa-regular fa-envelope text-nutrigreen-500 mt-0.5 text-xs"></i>
                <span>Contact the system administrator for further assistance.</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Section 3 - Contacting Support -->
      <div class="help-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
        <div class="flex items-start gap-4">
          <div class="icon-circle w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-regular fa-address-card text-nutrigreen-500 text-sm"></i>
          </div>
          <div class="flex-1">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-2">Contacting Support</h2>
            <p class="text-gray-600 leading-relaxed text-sm md:text-base mb-3">For questions, feedback, or reporting technical problems, you may reach out to us via email or visit the City Nutrition Office:</p>
            <ul class="space-y-2 pl-0">
              <li class="flex items-start gap-2 text-gray-600 text-sm">
                <i class="fa-regular fa-envelope text-nutrigreen-500 mt-0.5 text-xs"></i>
                <span>Email: <a href="mailto:citynutritionoffice@elsalvadorcity.gov.ph" class="text-nutrigreen-600 hover:underline font-medium">citynutritionoffice@elsalvadorcity.gov.ph</a></span>
              </li>
              <li class="flex items-start gap-2 text-gray-600 text-sm">
                <i class="fa-solid fa-location-dot text-nutrigreen-500 mt-0.5 text-xs"></i>
                <span>City Nutrition Office, El Salvador City, Misamis Oriental</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Section 4 - Additional Guidance -->
      <div class="help-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
        <div class="flex items-start gap-4">
          <div class="icon-circle w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-regular fa-lightbulb text-nutrigreen-500 text-sm"></i>
          </div>
          <div class="flex-1">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-2">Additional Guidance</h2>
            <p class="text-gray-600 leading-relaxed text-sm md:text-base">This Help page ensures that visitors can understand how to use the platform, access public data, and know where to seek support if needed.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Still need help? section (same as FAQs page) -->
    <div class="mt-10 text-center p-6 rounded-lg" style="background: rgba(62, 154, 62, 0.05);">
      <i class="fa-regular fa-circle-question text-nutrigreen-500 text-3xl mb-3 block"></i>
      <h3 class="text-lg font-semibold text-gray-800 mb-2">Still need help?</h3>
      <p class="text-gray-500 text-sm mb-4">Can't find what you're looking for? Reach out to our support team.</p>
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="../contact_us/contact.php" class="inline-flex items-center justify-center gap-2 bg-nutrigreen-500 hover:bg-nutrigreen-600 text-white px-5 py-2 rounded-lg transition-all text-sm font-medium">
          <i class="fa-regular fa-paper-plane"></i>
          Contact Us
        </a>
        <a href="faqs.php" class="inline-flex items-center justify-center gap-2 border border-nutrigreen-500 text-nutrigreen-600 hover:bg-nutrigreen-50 px-5 py-2 rounded-lg transition-all text-sm font-medium">
          <i class="fa-regular fa-circle-question"></i>
          View FAQs
        </a>
      </div>
    </div>
  </main>

  <!-- FOOTER (UNCHANGED) -->
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

  <!-- JS: Dropdown + Mobile Nav -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const burgerBtn = document.getElementById('burgerBtn');
      const mobileMenu = document.getElementById('mobileMenu');
      const aboutBtn = document.getElementById('aboutBtn');
      const aboutDropdown = document.getElementById('aboutDropdown');
      const aboutArrow = document.getElementById('aboutArrow');
      const mobileAboutBtn = document.getElementById('mobileAboutBtn');
      const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
      const mobileAboutArrow = document.getElementById('mobileAboutArrow');

      if (burgerBtn) {
        burgerBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
      }
      
      if (aboutBtn) {
        aboutBtn.addEventListener('click', e => {
          e.stopPropagation();
          aboutDropdown.classList.toggle('hidden');
          aboutArrow.classList.toggle('rotate-180');
        });
      }
      
      document.addEventListener('click', e => {
        if (aboutDropdown && !aboutDropdown.contains(e.target) && !aboutBtn.contains(e.target)) {
          aboutDropdown.classList.add('hidden');
          aboutArrow.classList.remove('rotate-180');
        }
      });
      
      if (mobileAboutBtn) {
        mobileAboutBtn.addEventListener('click', e => {
          e.stopPropagation();
          mobileAboutDropdown.classList.toggle('hidden');
          mobileAboutArrow.classList.toggle('rotate-180');
        });
      }
    });
  </script>
</body>
</html>