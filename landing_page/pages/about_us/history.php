<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | History</title>
  <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">

 <!-- Header -->
  <header class="bg-white shadow flex justify-between items-center px-6 md:px-10 py-4 relative z-50">
    <!-- Logo -->
    <div class="flex items-center font-bold text-2xl text-gray-700">
      <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
      <span class="text-teal-600">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <!-- Desktop nav -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../../../index.php" class="hover:text-teal-600">Home</a>
      <a href="../../map.php" class="hover:text-teal-600">Map</a>

      <!-- Dropdown Parent -->
      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 text-gray-700 text-teal-600 focus:outline-none">
          About CNO
          <svg class="w-4 h-4 transition-transform" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
          </svg>
        </button>
        <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-gray-100 shadow-lg rounded hidden z-50">
          <a href="about.php" class="block px-4 py-2 hover:bg-gray-200">About</a>
          <a href="profile.php" class="block px-4 py-2 hover:bg-gray-200">Profile</a>
          <a href="history.php" class="block px-4 py-2 hover:bg-gray-200 text-teal-600">History</a>
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

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden absolute top-full left-0 w-full bg-white shadow-md z-40 flex flex-col">
      <a href="../../../index.php" class="px-6 py-3 border-b hover:bg-gray-100">Home</a>
      <a href="../../map.php" class="px-6 py-3 border-b hover:bg-gray-100">Map</a>

      <!-- Mobile About CNO Dropdown -->
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
          <a href="history.php" class="px-8 py-2 hover:bg-gray-200">History</a>
          <a href="vision.php" class="px-8 py-2 hover:bg-gray-200">Vision</a>
          <a href="mission.php" class="px-8 py-2 hover:bg-gray-200">Mission</a>
        </div>
      </div>

      <a href="../contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-100">Contact Us</a>
      <a href="../../../login.php" class="px-6 py-3 hover:bg-gray-100">Login</a>
    </div>
  </header>

  <!-- Scripts for dropdowns -->
  <script>
    const burgerBtn = document.getElementById('burgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    burgerBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));

    const mobileAboutBtn = document.getElementById('mobileAboutBtn');
    const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
    const mobileAboutArrow = document.getElementById('mobileAboutArrow');
    mobileAboutBtn.addEventListener('click', e => {
      e.stopPropagation();
      mobileAboutDropdown.classList.toggle('hidden');
      mobileAboutArrow.classList.toggle('rotate-180');
    });
    document.addEventListener('click', e => {
      if (!mobileMenu.contains(e.target)) {
        mobileAboutDropdown.classList.add('hidden');
        mobileAboutArrow.classList.remove('rotate-180');
      }
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
  </script>

  <!-- Main Content -->
  <main class="flex-grow flex flex-col items-center p-5 lg:p-10">
    <h1 class="text-3xl lg:text-4xl font-bold mb-8 text-center">Our History</h1>

        <!-- ===== Timeline Section ===== -->
    <div class="relative my-16">
      <!-- vertical line -->
      <div class="absolute left-1/2 transform -translate-x-1/2 bg-[#00bfa6] w-1 h-full rounded-lg hidden md:block"></div>

      <!-- Timeline items -->
      <div class="space-y-12">
        <!-- Item 1 -->
        <div class="relative md:w-1/2 md:pr-10 md:text-right md:ml-0">
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:-translate-y-1 hover:shadow-2xl transition">
            <h3 class="text-[#00bfa6] text-xl font-semibold mb-3">2016 - Initial Establishment</h3>
            <p class="text-gray-700 leading-relaxed text-justify">
              The CNO was lodged at the City Health Office (CHO), occupying a shared room with PopCom (2 staff) and Nutrition (CNAO & 2 staff). 
              Space was limited and visibility for the nutrition program was low.
            </p>
          </div>
        </div>

        <!-- Item 2 -->
        <div class="relative md:w-1/2 md:pl-10 md:ml-auto">
          <div class="bg-white rounded-2xl shadow-lg p-6 hover:-translate-y-1 hover:shadow-2xl transition">
            <h3 class="text-[#00bfa6] text-xl font-semibold mb-3">2018 - Request for a Separate Office</h3>
            <p class="text-gray-700 leading-relaxed text-justify">
              CNAO requested Dr. Tangcalagan of CHO for a dedicated office. Nutrition staff were then transferred 
              to Laboratory Room 1 (Admin) to address operational needs.
            </p>
          </div>
        </div>

        <!-- Item 3 -->
        <div class="relative md:w-1/2 md:pr-10 md:text-right md:ml-0">
          <div class="bg-white rounded-2xl shadow-lg p-6 hover:-translate-y-1 hover:shadow-2xl transition">
            <h3 class="text-[#00bfa6] text-xl font-semibold mb-3">2021 - Space Challenges</h3>
            <p class="text-gray-700 leading-relaxed text-justify">
              Laboratory operations required the current nutrition office. CNAO lobbied the Local Chief Executive (LCE) 
              to transfer the Nutrition Office to the vacated Tourism Office to ensure proper space and recognition.
            </p>
          </div>
        </div>

        <!-- Item 4 -->
        <div class="relative md:w-1/2 md:pl-10 md:ml-auto">
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:-translate-y-1 hover:shadow-2xl transition">
            <h3 class="text-[#00bfa6] text-xl font-semibold mb-3">1st Attempt - Verbal Lobbying</h3>
            <p class="text-gray-700 leading-relaxed text-justify">
              In January 2021, CNAO Clapano visited the Mayor to verbally request a separate office. 
              This initial effort helped bring attention to the need, and the office was temporarily moved to the Tourism Office.
            </p>
          </div>
        </div>

        <!-- Item 5 -->
        <div class="relative md:w-1/2 md:pr-10 md:text-right md:ml-0">
          <div class="bg-white rounded-2xl shadow-lg p-6 hover:-translate-y-1 hover:shadow-2xl transition">
            <h3 class="text-[#00bfa6] text-xl font-semibold mb-3">2nd Attempt - Written Request</h3>
            <p class="text-gray-700 leading-relaxed text-justify">
              On February 21, 2021, CNAO Clapano submitted a written request to Mayor Lignes for a dedicated Nutrition Office. 
              With the Mayor’s approval, the request was forwarded to the City Engineering Office for a Program of Works, 
              with an approved budget of 1.6 million pesos, marking a major milestone in institutional recognition of the nutrition program.
            </p>
          </div>
        </div>

        <!-- Item 6 -->
        <div class="relative md:w-1/2 md:pl-10 md:ml-auto">
            <div class="bg-white rounded-2xl shadow-lg p-6 hover:-translate-y-1 hover:shadow-2xl transition">
            <h3 class="text-[#00bfa6] text-xl font-semibold mb-3">Today and Beyond</h3>
            <p class="text-gray-700 leading-relaxed text-justify">
              The City Nutrition Office now continues its mission with proper facilities, integrating technology and data-driven 
              solutions to provide sustainable nutrition programs for the community.
            </p>
          </div>
        </div>
      </div>
    </div>
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
            <li><a href="history.php" class="hover:text-cyan-600">History</a></li>
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
