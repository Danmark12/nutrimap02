<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | FAQs</title>
  <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="flex flex-col min-h-screen font-inter bg-gray-100 text-gray-800">

  <!-- HEADER -->
  <header class="flex justify-between items-center px-6 md:px-10 py-4 bg-white shadow relative z-50">
    <!-- Logo -->
    <div class="flex items-center font-bold text-2xl text-gray-700">
      <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
      <img src="../../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
      <span class="text-teal-600">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <!-- Desktop Navigation -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../../../index.php" class="hover:text-teal-600">Home</a>
      <a href="../../map.php" class="hover:text-teal-600">Map</a>

      <!-- Dropdown -->
      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 text-gray-700 hover:text-teal-600 focus:outline-none">
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

      <a href="../contact_us/contact.php" class="hover:text-teal-600">Contact Us</a>
      <a href="../../../login.php" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700 transition">Login</a>
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

  <!-- MAIN CONTENT -->
  <main class="max-w-4xl mx-auto bg-white mt-12 mb-20 p-8 md:p-12 rounded-lg shadow-lg">
    <h1 class="text-3xl md:text-4xl font-bold text-teal-600 mb-6">Frequently Asked Questions (FAQs)</h1>

    <section class="space-y-8">
      <div>
        <h2 class="text-xl font-semibold text-teal-700 mb-2">1. What is this website for?</h2>
        <p class="text-gray-700 leading-relaxed">This web-based GIS system, CNO NutriMap, is developed to help the City Nutrition Office (CNO) manage and display health and nutrition data for public awareness, transparency, and informed decision-making.</p>
      </div>

      <div>
        <h2 class="text-xl font-semibold text-teal-700 mb-2">2. Who can use the system?</h2>
        <p class="text-gray-700 leading-relaxed">Visitors can explore selected barangay and city-level nutrition data. Authorized personnel from the City and Barangay Nutrition Offices have administrative access to manage and update the data.</p>
      </div>

      <div>
        <h2 class="text-xl font-semibold text-teal-700 mb-2">3. Does the system collect personal information?</h2>
        <p class="text-gray-700 leading-relaxed">No personal information is collected from visitors. The system only presents aggregated, non-identifiable health and nutrition data.</p>
      </div>

      <div>
        <h2 class="text-xl font-semibold text-teal-700 mb-2">4. Is my data safe while browsing?</h2>
        <p class="text-gray-700 leading-relaxed">Yes. The platform complies with the Data Privacy Act of 2012 (RA 10173) and implements standard web security measures to ensure your privacy and protect non-identifiable data.</p>
      </div>

      <div>
        <h2 class="text-xl font-semibold text-teal-700 mb-2">5. How can I report an issue or ask for assistance?</h2>
        <p class="text-gray-700 leading-relaxed">You may contact the City Nutrition Office directly or use the <strong>“Contact Us”</strong> form on the website to report technical issues, provide feedback, or request additional information:</p>
        <ul class="list-disc pl-6 mt-2 text-gray-700 space-y-1">
          <li>Email: <a href="mailto:cnonutrimap@gmail.com" class="text-teal-600 hover:underline">cnonutrimap@gmail.com</a></li>
          <li>City Nutrition Office, El Salvador City, Misamis Oriental</li>
        </ul>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="bg-gray-800 text-gray-300 py-10">
    <div class="max-w-7xl mx-auto px-6 grid gap-8 md:grid-cols-5">
      <!-- Logo -->
      <div class="md:col-span-2">
        <div class="flex items-center mb-4">
          <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2 rounded-lg">
          <span class="text-cyan-500 text-xl font-bold">CNO</span>
          <span class="text-white text-xl font-bold ml-1">NutriMap</span>
        </div>
        <p class="text-sm">A tool to visualize health and nutrition data for children in El Salvador City.</p>
      </div>

      <!-- About -->
      <div>
        <h3 class="text-white font-semibold mb-4">About Us</h3>
        <ul class="space-y-2">
          <li><a href="../about_us/mission.php" class="hover:text-cyan-500">Our Mission</a></li>
          <li><a href="../about_us/vision.php" class="hover:text-cyan-500">Our Vision</a></li>
        </ul>
      </div>

      <!-- Quick Links -->
      <div>
        <h3 class="text-white font-semibold mb-4">Quick Links</h3>
        <ul class="space-y-2">
          <li><a href="../../map.php" class="hover:text-cyan-500">Map</a></li>
          <li><a href="../contact_us/contact.php" class="hover:text-cyan-500">Contact Us</a></li>
        </ul>
      </div>

      <!-- Legal -->
      <div>
        <h3 class="text-white font-semibold mb-4">Legal & Support</h3>
        <ul class="space-y-2">
          <li><a href="../legal_and_support/terms.php" class="hover:text-cyan-500">Terms of Use</a></li>
          <li><a href="../legal_and_support/privacy.php" class="hover:text-cyan-500">Privacy Policy</a></li>
          <li><a href="../legal_and_support/cookies.php" class="hover:text-cyan-500">Cookies</a></li>
          <li><a href="help.php" class="hover:text-cyan-500">Help</a></li>
          <li><a href="faqs.php" class="hover:text-cyan-500">FAQs</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
      <p>© 2025 CNO NutriMap. All Rights Reserved. Developed by NBSC ICS 4th Year Student.</p>
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

      burgerBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
      aboutBtn.addEventListener('click', e => {
        e.stopPropagation();
        aboutDropdown.classList.toggle('hidden');
        aboutArrow.classList.toggle('rotate-180');
      });
      document.addEventListener('click', e => {
        if (!aboutDropdown.contains(e.target) && !aboutBtn.contains(e.target)) {
          aboutDropdown.classList.add('hidden');
          aboutArrow.classList.remove('rotate-180');
        }
      });
      mobileAboutBtn.addEventListener('click', e => {
        e.stopPropagation();
        mobileAboutDropdown.classList.toggle('hidden');
        mobileAboutArrow.classList.toggle('rotate-180');
      });
    });
  </script>

</body>
</html>
