<?php
// home.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CNO NutriMap | Home</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  />
</head>
<body class="text-gray-800">

  <!-- HEADER -->
  <header class="flex justify-between items-center px-6 md:px-10 py-4 bg-white shadow relative">
    <!-- Logo -->
    <div class="flex items-center font-bold text-2xl text-gray-700">
      <img src="../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
      <img src="../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
      <span class="text-teal-600">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <!-- Desktop Nav -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../index.php" class="text-teal-600">Home</a>
      <a href="map.php" class="hover:text-teal-600">Map</a>

      <!-- Dropdown -->
      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 text-gray-700 hover:text-teal-600 focus:outline-none">
          About CNO
          <svg class="w-4 h-4 transition-transform" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
          </svg>
        </button>
        <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-white shadow-lg rounded hidden z-50">
          <a href="pages/about_us/about.php" class="block px-4 py-2 hover:bg-gray-100">About</a>
          <a href="pages/about_us/profile.php" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
          <a href="pages/about_us/vision.php" class="block px-4 py-2 hover:bg-gray-100">Vision</a>
          <a href="pages/about_us/mission.php" class="block px-4 py-2 hover:bg-gray-100">Mission</a>
        </div>
      </div>

      <a href="pages/contact_us/contact.php" class="hover:text-teal-600">Contact Us</a>
      <a href="../login.php" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">Login</a>
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
      <a href="../index.php" class="px-6 py-3 border-b hover:bg-gray-100">Home</a>
      <a href="map.php" class="px-6 py-3 border-b hover:bg-gray-100">Map</a>

      <!-- Mobile About -->
      <div class="flex flex-col">
        <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 border-b hover:bg-gray-100 focus:outline-none">
          About CNO
          <svg id="mobileAboutArrow" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
          </svg>
        </button>
        <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
          <a href="pages/about_us/about.php" class="px-8 py-2 hover:bg-gray-200">About</a>
          <a href="pages/about_us/profile.php" class="px-8 py-2 hover:bg-gray-200">Profile</a>
          <a href="pages/about_us/vision.php" class="px-8 py-2 hover:bg-gray-200">Vision</a>
          <a href="pages/about_us/mission.php" class="px-8 py-2 hover:bg-gray-200">Mission</a>
        </div>
      </div>

      <a href="pages/contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-100">Contact Us</a>
      <a href="../login.php" class="px-6 py-3 hover:bg-gray-100">Login</a>
    </div>
  </header>

  <!-- HERO SECTION -->
  <main class="relative flex items-center justify-start text-white min-h-[89vh] bg-cover bg-center" style="background-image: url('../img/bg_img.jpg');">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50"></div>

    <!-- Text Content -->
    <div class="relative z-10 px-8 md:px-16 lg:px-24 max-w-4xl text-left">
      <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-4">
        Welcome to <br><span class="text-teal-400">NutriMap</span>
      </h1>
      <p class="text-lg md:text-xl mb-6">El Salvador, Misamis Oriental</p>
      <a href="pages/kmau.php" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md">
        Know More About Us!
      </a>
    </div>
  </main>
<section class="w-full max-w-5xl mx-auto bg-gray-100 rounded-2xl shadow-md mt-10 mb-20 sm:p-12 text-center">
    <h2 class="text-2xl sm:text-3xl font-bold text-teal-600 mb-4">Empowering Nutrition Awareness</h2>
    <p class="text-gray-600 text-base sm:text-lg leading-relaxed">
      The City Nutrition Office of El Salvador, Misamis Oriental, is committed to promoting a healthier community
      through education, data-driven decisions, and continuous collaboration with local partners and stakeholders.
    </p>
  </section>
  <!-- FOOTER -->
  <footer class="bg-gray-800 text-gray-300 py-10 mt-10">
    <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-5 gap-8">
      <!-- Logo -->
      <div class="md:col-span-2">
        <div class="flex items-center mb-4">
          <img src="../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2 rounded-lg" />
          <span class="text-teal-500 text-xl font-bold">CNO</span>
          <span class="text-white text-xl font-bold ml-1">NutriMap</span>
        </div>
        <p class="text-sm">A tool to visualize health and nutrition data for children in El Salvador City.</p>
      </div>

      <!-- About -->
      <div>
        <h3 class="text-white font-semibold text-lg mb-3">About Us</h3>
        <ul class="space-y-2">
          <li><a href="pages/about_us/mission.php" class="hover:text-teal-400">Our Mission</a></li>
          <li><a href="pages/about_us/vision.php" class="hover:text-teal-400">Our Vision</a></li>
        </ul>
      </div>

      <!-- Quick Links -->
      <div>
        <h3 class="text-white font-semibold text-lg mb-3">Quick Links</h3>
        <ul class="space-y-2">
          <li><a href="map.php" class="hover:text-teal-400">Map</a></li>
          <li><a href="pages/contact_us/contact.php" class="hover:text-teal-400">Contact Us</a></li>
        </ul>
      </div>

      <!-- Legal -->
      <div>
        <h3 class="text-white font-semibold text-lg mb-3">Legal & Support</h3>
        <ul class="space-y-2">
          <li><a href="pages/legal_and_support/terms.php" class="hover:text-teal-400">Terms of Use</a></li>
          <li><a href="pages/legal_and_support/privacy.php" class="hover:text-teal-400">Privacy Policy</a></li>
          <li><a href="pages/legal_and_support/cookies.php" class="hover:text-teal-400">Cookies</a></li>
          <li><a href="pages/help_and_support/help.php" class="hover:text-teal-400">Help</a></li>
          <li><a href="pages/help_and_support/faqs.php" class="hover:text-teal-400">FAQs</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400 text-sm">
      <p>Copyright &copy; 2025 CNO NutriMap. All Rights Reserved.<br>Developed By NBSC ICS 4th Year Student.</p>
    </div>
  </footer>

  <!-- JS DROPDOWN -->
  <script src="js/home.js"></script>

</body>
</html>
