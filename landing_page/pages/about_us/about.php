<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | About Us</title>
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
          <a href="about.php" class="block px-4 py-2 hover:bg-gray-200 text-teal-600">About</a>
          <a href="profile.php" class="block px-4 py-2 hover:bg-gray-200">Profile</a>
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

  <!-- Main Content -->
  <main class="flex-grow container mx-auto px-6 md:px-10 py-10 flex flex-col items-center">
    <h1 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-8">About CNO NutriMap</h1>

    <div class="bg-white rounded-xl shadow-lg p-6 md:p-10 max-w-3xl space-y-4 text-gray-700">
      <p>
        CNO NutriMap is a project developed by NBSC students in collaboration with the City Nutrition Office (CNO) of El Salvador, Misamis Oriental. Our mission is to provide an interactive and informative platform to help the community stay informed about local nutrition data and initiatives.
      </p>
      <p>
        This web application allows you to visualize key nutrition data on a map, providing insights into the health status of our barangays. We believe that by making this information accessible, we can empower community members and stakeholders to make informed decisions and work together towards a healthier future.
      </p>
      <p>
        Our goal is to promote transparency, support the CNO's mission, and encourage community engagement in addressing nutrition-related challenges.
      </p>
    </div>

    <section class="mt-12 max-w-3xl w-full">
      <h2 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Our Contribution to the SDGs</h2>
      <div class="flex flex-col gap-6 md:gap-8">
        <div class="bg-white p-6 rounded-xl shadow-lg">
          <h3 class="text-xl md:text-2xl font-bold text-teal-600 mb-2 text-center">SDG 2: Zero Hunger</h3>
          <p class="text-gray-700 text-justify">
            By providing real-time data on nutrition status, CNO NutriMap helps the City Nutrition Office and other organizations identify areas with high malnutrition rates. This allows for targeted interventions and the efficient allocation of resources to combat hunger and food insecurity in the community.
          </p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg">
          <h3 class="text-xl md:text-2xl font-bold text-teal-600 mb-2 text-center">SDG 3: Good Health and Well-being</h3>
          <p class="text-gray-700 text-justify">
            The project contributes to good health and well-being by raising public awareness of nutrition issues. By making health data transparent and easy to understand, we empower citizens to take an active role in their own health and support public health campaigns.
          </p>
        </div>
      </div>
    </section>
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
