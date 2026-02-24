<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | Organizational Chart</title>
   <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">

  <!-- Header -->
  <header class="flex justify-between items-center px-6 md:px-10 py-4 bg-white shadow relative z-50">
    <div class="flex items-center font-bold text-2xl text-gray-700">
      <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
      <img src="../../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
      <span class="text-teal-600">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <!-- Desktop nav -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../../../index.php" class="hover:text-teal-600">Home</a>
      <a href="../../map.php" class="hover:text-teal-600">Map</a>

      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 font-semibold text-gray-700 text-teal-600 cursor-pointer focus:outline-none">
          About CNO
          <svg class="w-4 h-4 transition-transform" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
          </svg>
        </button>

        <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-gray-100 shadow-lg rounded hidden z-50">
          <a href="about.php" class="block px-4 py-2 hover:bg-gray-200">About</a>
          <a href="profile.php" class="block px-4 py-2 hover:bg-gray-200 text-teal-600">Profile</a>
          <a href="vision.php" class="block px-4 py-2 hover:bg-gray-200">Vision</a>
          <a href="mission.php" class="block px-4 py-2 hover:bg-gray-200">Mission</a>
        </div>
      </div>

      <a href="../contact_us/contact.php" class="hover:text-teal-600">Contact Us</a>
      <a href="../../../login.php" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">Login</a>
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

      <div class="flex flex-col">
        <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 border-b hover:bg-gray-100 focus:outline-none">
          About CNO
          <svg id="mobileAboutArrow" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
          </svg>
        </button>
        <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
          <a href="about.php" class="px-8 py-2 hover:bg-gray-200">About</a>
          <a href="profile.php" class="px-8 py-2 hover:bg-gray-200">Profile</a>
          <a href="vision.php" class="px-8 py-2 hover:bg-gray-200">Vision</a>
          <a href="mission.php" class="px-8 py-2 hover:bg-gray-200">Mission</a>
        </div>
      </div>

      <a href="../contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-100">Contact Us</a>
      <a href="../../../login.php" class="px-6 py-3 hover:bg-gray-100">Login</a>
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
      mobileAboutBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        mobileAboutDropdown.classList.toggle('hidden');
        mobileAboutArrow.classList.toggle('rotate-180');
      });

      document.addEventListener('click', (e) => {
        if(!mobileMenu.contains(e.target)){
          mobileAboutDropdown.classList.add('hidden');
          mobileAboutArrow.classList.remove('rotate-180');
        }
      });

      const aboutBtn = document.getElementById('aboutBtn');
      const aboutDropdown = document.getElementById('aboutDropdown');
      const aboutArrow = document.getElementById('aboutArrow');
      aboutBtn.addEventListener('click', (e) => {
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

  <!-- Organizational Chart -->
  <main class="flex-grow flex justify-center items-start p-4 md:p-8 overflow-x-auto bg-gray-200">
    <div class="bg-gradient-to-b from-green-100 to-green-200 rounded-xl p-6 md:p-10 shadow-lg flex flex-col items-center min-w-[300px] md:min-w-[900px] max-w-[1200px]">

      <!-- Logos -->
      <div class="flex justify-between w-full mb-6 px-4 md:px-12">
        <img src="../../../img/Ellipse_04.png" alt="Bagong Pilipinas Logo" class="h-16 object-contain">
        <img src="../../../img/Ellipse_02.png" alt="El Salvador City Logo" class="h-16 object-contain">
      </div>

      <!-- Chart Title -->
      <div class="text-center mb-6">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">ORGANIZATIONAL CHART</h2>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">CITY NUTRITION OFFICE</h2>
        <h3 class="text-sm md:text-lg text-gray-600 mt-2">El Salvador City, Misamis Oriental</h3>
      </div>

      <!-- Top Node -->
      <!-- ===== Chart Section ===== -->
  <section class="max-w-6xl mx-auto text-center py-16 px-6">

    <!-- Top Level -->
    <div class="relative inline-block mb-16">
      <div class="bg-white rounded-2xl border-t-4 border-teal-500 shadow-lg p-6 w-60 mx-auto hover:-translate-y-2 transition-all duration-300 hover:shadow-xl">
        <img src="../../../img/org/7.png" alt="Elma M. Clapano" class="w-28 h-28 object-cover rounded-full border-4 border-teal-500 mx-auto mb-3">
        <h3 class="text-lg font-semibold">Elma M. Clapano, RN</h3>
        <p class="text-gray-600 text-sm">City Nutrition Action Officer</p>
      </div>
      <div class="absolute left-1/2 transform -translate-x-1/2 w-0.5 h-10 bg-teal-500 bottom-[-40px]"></div>
    </div>

    <!-- Divider -->
    <div class="w-full h-px bg-teal-400 opacity-30 my-8"></div>

    <!-- ===== Technical Division ===== -->
    <h3 class="text-xl sm:text-2xl font-bold text-teal-600 uppercase tracking-wide mb-8 border-b-4 border-teal-500 inline-block pb-1">
      Technical Division
    </h3>

    <div class="flex flex-wrap justify-center items-start gap-6 mb-16">
      <!-- Person -->
      <div class="bg-white rounded-2xl border-t-4 border-teal-500 shadow-md p-6 w-56 hover:-translate-y-2 transition-all duration-300 hover:shadow-xl">
        <img src="../../../img/org/6.png" alt="Edgar B. Napilas" class="w-24 h-24 object-cover rounded-full border-4 border-teal-500 mx-auto mb-3">
        <h3 class="font-semibold text-base">Edgar B. Napilas</h3>
        <p class="text-gray-600 text-sm">City Nutrition Program Coordinator</p>
      </div>

      <div class="bg-white rounded-2xl border-t-4 border-teal-500 shadow-md p-6 w-56 hover:-translate-y-2 transition-all duration-300 hover:shadow-xl">
        <img src="../../../img/org/5.png" alt="Arlie Joy O. Damiles" class="w-24 h-24 object-cover rounded-full border-4 border-teal-500 mx-auto mb-3">
        <h3 class="font-semibold text-base">Arlie Joy O. Damiles, RND</h3>
        <p class="text-gray-600 text-sm">Nutritionist-Dietitian</p>
      </div>

      <div class="bg-white rounded-2xl border-t-4 border-teal-500 shadow-md p-6 w-56 hover:-translate-y-2 transition-all duration-300 hover:shadow-xl">
        <img src="../../../img/org/4.png" alt="Karen Jay B. Lagala" class="w-24 h-24 object-cover rounded-full border-4 border-teal-500 mx-auto mb-3">
        <h3 class="font-semibold text-base">Karen Jay B. Lagala, RND</h3>
        <p class="text-gray-600 text-sm">Nutritionist-Dietitian</p>
      </div>

      <div class="bg-white rounded-2xl border-t-4 border-teal-500 shadow-md p-6 w-56 hover:-translate-y-2 transition-all duration-300 hover:shadow-xl">
        <img src="../../../img/org/3.png" alt="Jay S. Boctot" class="w-24 h-24 object-cover rounded-full border-4 border-teal-500 mx-auto mb-3">
        <h3 class="font-semibold text-base">Jay S. Boctot, LPT</h3>
        <p class="text-gray-600 text-sm">City Nutrition Program Coordinator</p>
      </div>
    </div>

    <!-- Divider -->
    <div class="w-full h-px bg-teal-400 opacity-30 my-8"></div>

    <!-- ===== Administrative Division ===== -->
    <h3 class="text-xl sm:text-2xl font-bold text-teal-600 uppercase tracking-wide mb-8 border-b-4 border-teal-500 inline-block pb-1">
      Administrative Division
    </h3>

    <div class="flex flex-wrap justify-center items-start gap-6">
      <div class="bg-white rounded-2xl border-t-4 border-teal-500 shadow-md p-6 w-56 hover:-translate-y-2 transition-all duration-300 hover:shadow-xl">
        <img src="../../../img/org/2.png" alt="Honey Grace S. Magrifila" class="w-24 h-20 object-cover rounded-full border-4 border-teal-500 mx-auto mb-3">
        <h3 class="font-semibold text-base">Honey Grace S. Magrifila</h3>
        <p class="text-gray-600 text-sm">Office Clerk</p>
      </div>

      <div class="bg-white rounded-2xl border-t-4 border-teal-500 shadow-md p-6 w-56 hover:-translate-y-2 transition-all duration-300 hover:shadow-xl">
        <img src="../../../img/org/1.png" alt="Antonette E. Vilbar" class="w-24 h-26 object-cover rounded-full border-4 border-teal-500 mx-auto mb-3">
        <h3 class="font-semibold text-base">Antonette E. Vilbar</h3>
        <p class="text-gray-600 text-sm">Administrative Aide III</p>
      </div>
    </div>
  </section>


    </div>
  </main>

  <!-- FOOTER -->
  <footer class="footer mt-auto bg-gray-800 text-gray-300 py-10 relative z-10">
    <div class="footer-container max-w-7xl mx-auto px-4">
      <div class="footer-grid grid gap-8 md:grid-cols-5">
        <div class="footer-logo md:col-span-2 flex flex-col items-start">
          <div class="logo-text flex items-center mb-4">
            <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2 rounded-lg">
            <span class="logo-primary text-cyan-600 text-xl font-bold">CNO</span>
            <span class="logo-secondary text-white text-xl font-bold ml-1">NutriMap</span>
          </div>
          <p class="footer-desc text-sm">A tool to visualize health and nutrition data for children in El Salvador City.</p>
        </div>
        <div>
          <h3 class="footer-title text-white font-semibold mb-4">About Us</h3>
          <ul class="footer-links space-y-2">
            <li><a href="mission.php" class="hover:text-cyan-600">Our Mission</a></li>
            <li><a href="vision.php" class="hover:text-cyan-600">Our Vision</a></li>
          </ul>
        </div>
        <div>
          <h3 class="footer-title text-white font-semibold mb-4">Quick Links</h3>
          <ul class="footer-links space-y-2">
            <li><a href="../../map.php" class="hover:text-cyan-600">Map</a></li>
            <li><a href="../contact_us/contact.php" class="hover:text-cyan-600">Contact Us</a></li>
          </ul>
        </div>
        <div>
          <h3 class="footer-title text-white font-semibold mb-4">Legal & Support</h3>
          <ul class="footer-links space-y-2">
            <li><a href="../legal_and_support/terms.php" class="hover:text-cyan-600">Terms of Use</a></li>
            <li><a href="../legal_and_support/privacy.php" class="hover:text-cyan-600">Privacy Policy</a></li>
            <li><a href="../legal_and_support/cookies.php" class="hover:text-cyan-600">Cookies</a></li>
            <li><a href="../help_and_support/help.php" class="hover:text-cyan-600">Help</a></li>
            <li><a href="../help_and_support/faqs.php" class="hover:text-cyan-600">FAQs</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom mt-8 border-t border-gray-700 pt-8 text-center text-gray-400 text-sm">
        <p>Copyright&copy; 2025 CNO NutriMap All Rights Reserved. Developed By NBSC ICS 4th Year Student.</p>
      </div>
    </div>
  </footer>

</body>
</html>
