<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | About CNO</title>
  <link rel="icon" type="image/png" href="../../img/CNO_Logo.png">
  
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
    /* Override all teal colors to nutrigreen */
    .text-teal-500, .text-teal-600, .text-teal-400, .text-teal-700,
    .text-cyan-600, .text-cyan-500, .text-cyan-400 {
      color: #3e9a3e !important;
    }
    .bg-teal-500, .bg-teal-600, .bg-teal-700, .bg-teal-400,
    .bg-cyan-600, .bg-cyan-700, .bg-cyan-500 {
      background-color: #3e9a3e !important;
    }
    .bg-teal-500:hover, .bg-teal-600:hover, .bg-teal-700:hover,
    .bg-cyan-600:hover, .bg-cyan-700:hover {
      background-color: #2e7d32 !important;
    }
    .hover\:text-teal-400:hover, .hover\:text-teal-500:hover, .hover\:text-teal-600:hover,
    .hover\:text-cyan-600:hover, .hover\:text-cyan-500:hover {
      color: #3e9a3e !important;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #eef2f6 100%);
      font-family: 'Inter', sans-serif;
    }
    
    /* Info card styling */
    .info-card {
      transition: all 0.3s ease;
    }
    
    .info-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .icon-circle {
      transition: all 0.2s ease;
    }
    
    .info-card:hover .icon-circle {
      transform: scale(1.05);
      background-color: #3e9a3e;
    }
    
    .info-card:hover .icon-circle i {
      color: white !important;
    }
    
    .objective-item {
      transition: all 0.2s ease;
    }
    
    .objective-item:hover {
      transform: translateX(5px);
      background: rgba(62, 154, 62, 0.05);
    }
  </style>
</head>

<body class="flex flex-col min-h-screen text-gray-800">

  <!-- HEADER (Updated to green theme) -->
  <header class="flex justify-between items-center px-6 md:px-10 h-14 bg-white shadow relative z-50">
    
    <!-- Logo -->
    <div class="flex items-center text-2xl font-bold text-gray-700">
      <img src="../../img/CNO_Logo.png" alt="CNO Logo" class="h-10 mr-2">
      <img src="../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
      <span class="text-nutrigreen-500">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <!-- Desktop Navigation -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../../index.php" class="hover:text-nutrigreen-500">Home</a>
      <a href="../map.php" class="hover:text-nutrigreen-500">Map</a>

      <!-- Dropdown -->
      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 text-gray-700 hover:text-nutrigreen-500 focus:outline-none">
          About CNO
          <svg class="w-4 h-4 transition-transform" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
          </svg>
        </button>
        <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-gray-100 shadow-lg rounded hidden z-50">
          <a href="../pages/about_us/about.php" class="block px-4 py-2 hover:bg-gray-200">About</a>
          <a href="../pages/about_us/profile.php" class="block px-4 py-2 hover:bg-gray-200">Profile</a>
          <a href="../pages/about_us/vision.php" class="block px-4 py-2 hover:bg-gray-200">Vision</a>
          <a href="../pages/about_us/mission.php" class="block px-4 py-2 hover:bg-gray-200">Mission</a>
        </div>
      </div>

      <a href="../pages/contact_us/contact.php" class="text-nutrigreen-500">Contact Us</a>
      <a href="../../login.php" class="bg-nutrigreen-500 text-white px-4 py-2 rounded hover:bg-nutrigreen-600 transition">Login</a>
    </nav>

    <!-- Mobile Burger -->
    <div class="md:hidden">
      <button id="burgerBtn" class="text-gray-700 focus:outline-none">
        <i class="fa-solid fa-bars text-2xl"></i>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden absolute top-full left-0 w-full bg-white shadow-md z-40 flex flex-col">
      <a href="../../index.php" class="px-6 py-3 border-b hover:bg-gray-100">Home</a>
      <a href="../map.php" class="px-6 py-3 border-b hover:bg-gray-100">Map</a>

      <div class="flex flex-col">
        <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 border-b hover:bg-gray-100 focus:outline-none">
          About CNO
          <i id="mobileAboutArrow" class="fa-solid fa-chevron-down transition-transform"></i>
        </button>
        <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
          <a href="../pages/about_us/about.php" class="px-8 py-2 hover:bg-gray-200">About</a>
          <a href="../pages/about_us/profile.php" class="px-8 py-2 hover:bg-gray-200">Profile</a>
          <a href="../pages/about_us/vision.php" class="px-8 py-2 hover:bg-gray-200">Vision</a>
          <a href="../pages/about_us/mission.php" class="px-8 py-2 hover:bg-gray-200">Mission</a>
        </div>
      </div>

      <a href="../pages/contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-100">Contact Us</a>
      <a href="../../login.php" class="px-6 py-3 hover:bg-gray-100">Login</a>
    </div>
  </header>

  <!-- MAIN CONTENT - IMPROVED, NO CARD -->
  <main class="flex-grow container mx-auto px-4 md:px-6 py-8 max-w-5xl">
    
    <!-- Page Header -->
    <div class="text-center mb-10">
      <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
        Our Journey and <span class="text-nutrigreen-500">Commitment</span>
      </h1>
      <div class="w-16 h-0.5 bg-nutrigreen-500 mx-auto rounded-full mb-4"></div>
      <p class="text-gray-500 text-sm max-w-2xl mx-auto">
        The City Nutrition Office of El Salvador, Misamis Oriental, is dedicated to building a healthier and stronger community.
      </p>
    </div>

    <!-- Introduction Text -->
    <div class="mb-10 p-6 rounded-lg" style="background: rgba(62, 154, 62, 0.05);">
      <p class="text-gray-700 leading-relaxed text-center">
        Our journey began with the goal of addressing malnutrition and promoting sustainable health practices across all barangays. 
        We believe that proper nutrition is the foundation of a productive and prosperous community.
      </p>
    </div>

    <!-- Mission, Vision, Goal Cards (3 column layout) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      
      <!-- Mission Card -->
      <div class="info-card bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="icon-circle w-12 h-12 bg-nutrigreen-100 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-bullseye text-nutrigreen-500 text-lg"></i>
          </div>
          <h2 class="text-xl font-bold text-gray-800">Our Mission</h2>
        </div>
        <p class="text-gray-600 leading-relaxed text-sm">
          Safeguard the nutrition integrity and well-being of Tagnipan-ons through pro-active nutrition program implementation.
        </p>
      </div>

      <!-- Vision Card -->
      <div class="info-card bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="icon-circle w-12 h-12 bg-nutrigreen-100 rounded-xl flex items-center justify-center">
            <i class="fa-regular fa-eye text-nutrigreen-500 text-lg"></i>
          </div>
          <h2 class="text-xl font-bold text-gray-800">Our Vision</h2>
        </div>
        <p class="text-gray-600 leading-relaxed text-sm">
          Healthy Tagnipan-ons through Committed, People-Centered and Excellent Nutrition Services.
        </p>
      </div>

      <!-- Goal Card -->
      <div class="info-card bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="icon-circle w-12 h-12 bg-nutrigreen-100 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-flag-checkered text-nutrigreen-500 text-lg"></i>
          </div>
          <h2 class="text-xl font-bold text-gray-800">Our Goal</h2>
        </div>
        <p class="text-gray-600 leading-relaxed text-sm">
          Improve and sustain at a low public health significance on malnutrition among all age groups.
        </p>
      </div>
    </div>

    <!-- Objectives Section -->
    <div class="bg-white rounded-xl shadow-md p-6">
      <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-list-check text-nutrigreen-500 text-sm"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Our Objectives</h2>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="objective-item flex items-start gap-3 p-3 rounded-lg">
          <i class="fa-solid fa-check-circle text-nutrigreen-500 mt-0.5 text-sm"></i>
          <span class="text-gray-600 text-sm">Reduce the prevalence of malnutrition, stunting, and wasting among children.</span>
        </div>
        <div class="objective-item flex items-start gap-3 p-3 rounded-lg">
          <i class="fa-solid fa-check-circle text-nutrigreen-500 mt-0.5 text-sm"></i>
          <span class="text-gray-600 text-sm">Promote healthy eating habits and lifestyles through public awareness campaigns.</span>
        </div>
        <div class="objective-item flex items-start gap-3 p-3 rounded-lg">
          <i class="fa-solid fa-check-circle text-nutrigreen-500 mt-0.5 text-sm"></i>
          <span class="text-gray-600 text-sm">Collaborate with local government units and non-profit organizations to expand our reach.</span>
        </div>
        <div class="objective-item flex items-start gap-3 p-3 rounded-lg">
          <i class="fa-solid fa-check-circle text-nutrigreen-500 mt-0.5 text-sm"></i>
          <span class="text-gray-600 text-sm">Provide nutritional counseling and support to vulnerable households.</span>
        </div>
        <div class="objective-item flex items-start gap-3 p-3 rounded-lg md:col-span-2">
          <i class="fa-solid fa-check-circle text-nutrigreen-500 mt-0.5 text-sm"></i>
          <span class="text-gray-600 text-sm">Establish community gardens and food security projects to ensure access to fresh produce.</span>
        </div>
      </div>
    </div>

    <!-- Call to Action -->
    <div class="mt-8 text-center p-6 rounded-lg" style="background: rgba(62, 154, 62, 0.05);">
      <i class="fa-regular fa-handshake text-nutrigreen-500 text-3xl mb-3 block"></i>
      <h3 class="text-lg font-semibold text-gray-800 mb-2">Join Us in Building a Healthier Community</h3>
      <p class="text-gray-500 text-sm mb-4">Together, we can make a difference in the lives of every Tagnipan-on.</p>
      <a href="../pages/contact_us/contact.php" class="inline-flex items-center gap-2 bg-nutrigreen-500 hover:bg-nutrigreen-600 text-white px-5 py-2 rounded-lg transition-all text-sm font-medium">
        <i class="fa-regular fa-paper-plane"></i>
        Get in Touch
      </a>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-gray-800 text-gray-300 mt-10">
    <div class="max-w-6xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-5 gap-8">
      <div class="md:col-span-2">
        <div class="flex items-center mb-4">
          <img src="../../img/CNO_Logo.png" alt="CNO Logo" class="h-10 mr-2 rounded-lg">
          <span class="text-nutrigreen-500 text-xl font-bold">CNO</span>
          <span class="text-white text-xl font-bold ml-1">NutriMap</span>
        </div>
        <p class="text-sm">A tool to visualize health and nutrition data for children in El Salvador City.</p>
      </div>

      <div>
        <h3 class="text-white font-semibold text-lg mb-3">About Us</h3>
        <ul class="space-y-2">
          <li><a href="../pages/about_us/mission.php" class="hover:text-nutrigreen-400 transition-colors">Our Mission</a></li>
          <li><a href="../pages/about_us/vision.php" class="hover:text-nutrigreen-400 transition-colors">Our Vision</a></li>
        </ul>
      </div>

      <div>
        <h3 class="text-white font-semibold text-lg mb-3">Quick Links</h3>
        <ul class="space-y-2">
          <li><a href="../map.php" class="hover:text-nutrigreen-400 transition-colors">Map</a></li>
          <li><a href="../pages/contact_us/contact.php" class="hover:text-nutrigreen-400 transition-colors">Contact Us</a></li>
        </ul>
      </div>

      <div>
        <h3 class="text-white font-semibold text-lg mb-3">Legal & Support</h3>
        <ul class="space-y-2">
          <li><a href="../pages/legal_and_support/terms.php" class="hover:text-nutrigreen-400 transition-colors">Terms of Use</a></li>
          <li><a href="../pages/legal_and_support/privacy.php" class="hover:text-nutrigreen-400 transition-colors">Privacy Policy</a></li>
          <li><a href="../pages/legal_and_support/cookies.php" class="hover:text-nutrigreen-400 transition-colors">Cookies</a></li>
          <li><a href="../pages/help_and_support/help.php" class="hover:text-nutrigreen-400 transition-colors">Help</a></li>
          <li><a href="../pages/help_and_support/faqs.php" class="hover:text-nutrigreen-400 transition-colors">FAQs</a></li>
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
      
      if (mobileAboutBtn) {
        mobileAboutBtn.addEventListener('click', () => {
          mobileAboutDropdown.classList.toggle('hidden');
          mobileAboutArrow.classList.toggle('rotate-180');
        });
      }
      
      document.addEventListener('click', (e) => {
        if (aboutDropdown && !aboutDropdown.classList.contains('hidden') && aboutBtn && !aboutBtn.contains(e.target)) {
          aboutDropdown.classList.add('hidden');
          aboutArrow.classList.remove('rotate-180');
        }
      });
    });
  </script>
</body>
</html>