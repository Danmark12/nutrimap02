<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | Vision</title>
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
    .hover\:bg-teal-700:hover, .hover\:bg-cyan-700:hover {
      background-color: #2e7d32 !important;
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
    /* Vision card enhancements */
    .vision-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .vision-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }
    .vision-icon {
      animation: fadeInUp 0.6s ease-out;
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body class="flex flex-col min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

  <!-- Header with Green Theme -->
  <header class="bg-white shadow-md flex justify-between items-center px-6 md:px-10 h-14 relative z-50">
    <!-- Logo -->
    <div class="flex items-center font-bold text-2xl text-gray-700">
      <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
      <img src="../../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
      <span class="text-nutrigreen-500">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <!-- Desktop nav -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../../../index.php" class="hover:text-nutrigreen-500 transition-colors duration-200">Home</a>
      <a href="../../map.php" class="hover:text-nutrigreen-500 transition-colors duration-200">Map</a>

      <!-- Dropdown Parent -->
      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 text-gray-700 text-nutrigreen-500 focus:outline-none transition-colors duration-200">
          About CNO
          <svg class="w-4 h-4 transition-transform duration-200" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
          </svg>
        </button>
        <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-white shadow-lg rounded-lg hidden z-50 overflow-hidden border border-gray-100">
          <a href="about.php" class="block px-4 py-2 hover:bg-nutrigreen-50 hover:text-nutrigreen-500 transition-colors duration-150">About</a>
          <a href="profile.php" class="block px-4 py-2 hover:bg-nutrigreen-50 hover:text-nutrigreen-500 transition-colors duration-150">Profile</a>
          <a href="vision.php" class="block px-4 py-2 bg-nutrigreen-50 text-nutrigreen-500 font-bold">Vision</a>
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

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden absolute top-full left-0 w-full bg-white shadow-lg z-40 flex flex-col">
      <a href="../../../index.php" class="px-6 py-3 border-b hover:bg-gray-50 transition-colors">Home</a>
      <a href="../../map.php" class="px-6 py-3 border-b hover:bg-gray-50 transition-colors">Map</a>

      <!-- Mobile About CNO Dropdown -->
      <div class="flex flex-col">
        <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 border-b hover:bg-gray-50 focus:outline-none transition-colors">
          About CNO
          <svg id="mobileAboutArrow" class="w-4 h-4 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
          </svg>
        </button>
        <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
          <a href="about.php" class="px-8 py-2 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">About</a>
          <a href="profile.php" class="px-8 py-2 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Profile</a>
          <a href="vision.php" class="px-8 py-2 bg-nutrigreen-50 text-nutrigreen-500 font-bold">Vision</a>
          <a href="mission.php" class="px-8 py-2 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Mission</a>
        </div>
      </div>

      <a href="../contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-50 transition-colors">Contact Us</a>
      <a href="../../../login.php" class="px-6 py-3 hover:bg-gray-50 transition-colors">Login</a>
    </div>
  </header>

  <!-- Scripts for dropdowns -->
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
        mobileAboutBtn.addEventListener('click', e => {
          e.stopPropagation();
          mobileAboutDropdown.classList.toggle('hidden');
          if (mobileAboutArrow) mobileAboutArrow.style.transform = mobileAboutDropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        });
      }

      const aboutBtn = document.getElementById('aboutBtn');
      const aboutDropdown = document.getElementById('aboutDropdown');
      const aboutArrow = document.getElementById('aboutArrow');
      if (aboutBtn && aboutDropdown) {
        aboutBtn.addEventListener('click', e => {
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
        if (mobileAboutDropdown && mobileAboutBtn && !mobileAboutBtn.contains(e.target) && !mobileAboutDropdown.contains(e.target)) {
          mobileAboutDropdown.classList.add('hidden');
          if (mobileAboutArrow) mobileAboutArrow.style.transform = 'rotate(0deg)';
        }
      });
    });
  </script>

  <!-- Main Content -->
  <main class="flex-grow flex flex-col items-center p-5 lg:p-10">
    <!-- Decorative top bar -->
    <div class="w-24 h-1 bg-nutrigreen-500 rounded-full mb-6"></div>
    
    <h1 class="text-4xl lg:text-5xl font-bold mb-4 text-gray-800 text-center">
      Our <span class="text-nutrigreen-500">Vision</span>
    </h1>
    <p class="text-gray-500 text-center mb-10 max-w-2xl">
      Aspiring for a healthier future through dedicated service
    </p>
    
    <!-- Vision Card with Icon -->
    <div class="vision-card bg-white rounded-2xl shadow-xl p-8 md:p-12 max-w-3xl w-full">
      <div class="vision-icon flex justify-center mb-6">
        <div class="w-20 h-20 bg-nutrigreen-100 rounded-full flex items-center justify-center">
          <svg class="w-10 h-10 text-nutrigreen-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
          </svg>
        </div>
      </div>
      
      <div class="relative">
        <!-- Quote decoration -->
        <svg class="absolute -top-4 -left-6 w-12 h-12 text-nutrigreen-200 opacity-50" fill="currentColor" viewBox="0 0 24 24">
          <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
        </svg>
        
        <p class="text-xl md:text-2xl leading-relaxed text-gray-700 text-center px-4 py-2">
          "Healthy Tagnipan-ons through Committed, People-Centered and Excellent Nutrition Services."
        </p>
        
        <svg class="absolute -bottom-4 -right-6 w-12 h-12 text-nutrigreen-200 opacity-50 transform rotate-180" fill="currentColor" viewBox="0 0 24 24">
          <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
        </svg>
      </div>
      
      <!-- Signature line -->
      <div class="mt-8 pt-6 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-400">— City Nutrition Office, El Salvador —</p>
      </div>
    </div>
    
    <!-- Key Pillars Section -->
    <div class="mt-12 max-w-3xl w-full">
      <!-- <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Our Key Pillars</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-md p-4 text-center hover:shadow-lg transition-shadow duration-200">
          <div class="w-10 h-10 bg-nutrigreen-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <svg class="w-5 h-5 text-nutrigreen-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-700">Committed Service</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 text-center hover:shadow-lg transition-shadow duration-200">
          <div class="w-10 h-10 bg-nutrigreen-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <svg class="w-5 h-5 text-nutrigreen-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-700">People-Centered</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-4 text-center hover:shadow-lg transition-shadow duration-200">
          <div class="w-10 h-10 bg-nutrigreen-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <svg class="w-5 h-5 text-nutrigreen-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <p class="text-sm font-medium text-gray-700">Excellent Services</p>
        </div>
      </div>
    </div> -->
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