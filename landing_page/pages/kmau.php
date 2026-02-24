<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | About CNO</title>
  <link rel="icon" type="image/png" href="../../img/CNO_Logo.png">
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">

  <!-- HEADER -->
  <header class="flex justify-between items-center px-6 md:px-10 py-4 bg-white shadow relative">
    
      <!-- Logo -->
      <div class="flex items-center text-2xl font-bold text-gray-700">
        <img src="../../img/CNO_Logo.png" alt="CNO Logo" class="h-10 mr-2">
        <img src="../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
        <span class="text-teal-500">CNO</span><span class="ml-2">NutriMap</span>
      </div>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center space-x-6 font-semibold">
        <a href="../../index.php" class="hover:text-teal-500">Home</a>
        <a href="../map.php" class="hover:text-teal-600">Map</a>

        <!-- Dropdown -->
        <div class="relative">
          <button id="aboutBtn" class="flex items-center gap-1 text-gray-700 hover:text-teal-600 focus:outline-none">
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

        <a href="../pages/contact_us/contact.php" class="hover:text-teal-600">Contact Us</a>
        <a href="../../login.php" class="bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">Login</a>
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

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const burgerBtn = document.getElementById('burgerBtn');
      const mobileMenu = document.getElementById('mobileMenu');
      burgerBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));

      const mobileAboutBtn = document.getElementById('mobileAboutBtn');
      const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
      const mobileAboutArrow = document.getElementById('mobileAboutArrow');
      mobileAboutBtn.addEventListener('click', () => {
        mobileAboutDropdown.classList.toggle('hidden');
        mobileAboutArrow.classList.toggle('rotate-180');
      });

      const aboutBtn = document.getElementById('aboutBtn');
      const aboutDropdown = document.getElementById('aboutDropdown');
      const aboutArrow = document.getElementById('aboutArrow');
      aboutBtn.addEventListener('click', e => {
        e.stopPropagation();
        aboutDropdown.classList.toggle('hidden');
        aboutArrow.classList.toggle('rotate-180');
      });
      document.addEventListener('click', () => {
        aboutDropdown.classList.add('hidden');
        aboutArrow.classList.remove('rotate-180');
      });
    });
  </script>

  <!-- MAIN CONTENT -->
  <main class="flex-grow container mx-auto px-6 py-10">
    <div class="flex justify-center">
      <div class="bg-white shadow-md rounded-lg p-8 w-full md:w-3/4 lg:w-2/3">
        <h1 class="text-3xl md:text-4xl font-bold text-teal-500 text-center mb-6">Our Journey and Commitment</h1>
        <p class="text-gray-700 text-justify mb-8">
          The City Nutrition Office of El Salvador, Misamis Oriental, is dedicated to building a healthier and stronger community. Our journey began with the goal of addressing malnutrition and promoting sustainable health practices across all barangays. We believe that proper nutrition is the foundation of a productive and prosperous community.
        </p>

        <!-- Mission Card -->
        <div class="bg-gray-50 p-6 rounded-lg shadow mb-6">
          <h2 class="text-2xl font-semibold text-teal-500 mb-3">Our Mission</h2>
          <p class="text-gray-700 text-justify">
            Safeguard the nutrition integrity and well-being of Tagnipan-ons through pro-active nutrition program implementation.
          </p>
        </div>

        <!-- Vision Card -->
        <div class="bg-gray-50 p-6 rounded-lg shadow mb-6">
          <h2 class="text-2xl font-semibold text-teal-500 mb-3">Our Vision</h2>
          <p class="text-gray-700 text-justify">
           Healthy Tagnipan-ons through Committed, People-Centered and Excellent Nutrition Services.
          </p>
        </div>

        <!-- Goal -->
        <div class="bg-gray-50 p-6 rounded-lg shadow mb-6">
          <h2 class="text-2xl font-semibold text-teal-500 mb-3">Our Goal</h2>
          <p class="text-gray-700 text-justify">
           Improve and sustain at a low public health significance on malnutrition among all age groups.
          </p>
        </div>

        <!-- Objectives Card -->
        <div class="bg-gray-50 p-6 rounded-lg shadow mb-6">
          <h2 class="text-2xl font-semibold text-teal-500 mb-3">Our Objectives</h2>
          <ul class="list-disc list-inside text-gray-700 space-y-2">
            <li>Reduce the prevalence of malnutrition, stunting, and wasting among children.</li>
            <li>Promote healthy eating habits and lifestyles through public awareness campaigns.</li>
            <li>Collaborate with local government units and non-profit organizations to expand our reach.</li>
            <li>Provide nutritional counseling and support to vulnerable households.</li>
            <li>Establish community gardens and food security projects to ensure access to fresh produce.</li>
          </ul>
        </div>
      </div>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-gray-800 text-gray-300 mt-10">
    <div class="max-w-6xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-5 gap-8">
      <div class="md:col-span-2">
        <div class="flex items-center mb-4">
          <img src="../../img/CNO_Logo.png" alt="CNO Logo" class="h-10 mr-2 rounded-lg">
          <span class="text-teal-500 text-xl font-bold">CNO</span>
          <span class="text-white text-xl font-bold ml-1">NutriMap</span>
        </div>
        <p class="text-sm">A tool to visualize health and nutrition data for children in El Salvador City.</p>
      </div>

      <div>
        <h3 class="text-white font-semibold text-lg mb-3">About Us</h3>
        <ul class="space-y-2">
          <li><a href="../pages/about_us/mission.php" class="hover:text-teal-400">Our Mission</a></li>
          <li><a href="../pages/about_us/vision.php" class="hover:text-teal-400">Our Vision</a></li>
        </ul>
      </div>

      <div>
        <h3 class="text-white font-semibold text-lg mb-3">Quick Links</h3>
        <ul class="space-y-2">
          <li><a href="../map.php" class="hover:text-teal-400">Map</a></li>
          <li><a href="../pages/contact_us/contact.php" class="hover:text-teal-400">Contact Us</a></li>
        </ul>
      </div>

      <div>
        <h3 class="text-white font-semibold text-lg mb-3">Legal & Support</h3>
        <ul class="space-y-2">
          <li><a href="../pages/legal_and_support/terms.php" class="hover:text-teal-400">Terms of Use</a></li>
          <li><a href="../pages/legal_and_support/privacy.php" class="hover:text-teal-400">Privacy Policy</a></li>
          <li><a href="../pages/legal_and_support/cookies.php" class="hover:text-teal-400">Cookies</a></li>
          <li><a href="../pages/help_and_support/help.php" class="hover:text-teal-400">Help</a></li>
          <li><a href="../pages/help_and_support/faqs.php" class="hover:text-teal-400">FAQs</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400 text-sm">
      <p>Copyright &copy; 2025 CNO NutriMap. All Rights Reserved.<br>Developed By NBSC ICS 4th Year Student.</p>
    </div>
  </footer>

</body>
</html>
