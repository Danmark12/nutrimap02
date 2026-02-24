<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CNO NutriMap | Terms of Use</title>
    <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">

  <!-- Header -->
  <header class="header flex justify-between items-center px-6 md:px-10 py-4 bg-white shadow relative">
  <!-- Logo -->
  <div class="flex items-center font-bold text-2xl text-gray-700">
    <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
    <img src="../../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
    <span class="text-teal-600">CNO</span><span class="ml-2">NutriMap</span>
  </div>

  <!-- Desktop nav -->
  <nav class="hidden md:flex items-center space-x-6 font-semibold">
    <a href="../../../index.php" class="hover:text-teal-600">Home</a>
    <a href="../../map.php" class="hover:text-teal-600">Map</a>
<!-- Dropdown Parent -->
<div class="relative">
  <!-- Toggle Button -->
  <button id="aboutBtn" class="flex items-center gap-1 font-semibold text-gray-700 hover:text-teal-600 cursor-pointer focus:outline-none">
    About CNO
    <svg class="w-4 h-4 transition-transform" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
    </svg>
  </button>

  <!-- Dropdown Menu -->
  <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-gray-100 shadow-lg rounded hidden z-50">
    <a href="../about_us/about.php" class="block px-4 py-2 hover:bg-gray-200">About</a>
    <a href="../about_us/profile.php" class="block px-4 py-2 hover:bg-gray-200">Profile</a>
    <a href="../about_us/vision.php" class="block px-4 py-2 hover:bg-gray-200">Vision</a>
    <a href="../about_us/mission.php" class="block px-4 py-2 hover:bg-gray-200">Mission</a>
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
<div id="mobileMenu" class="hidden absolute top-full left-0 w-full bg-white shadow-md z-20 flex flex-col">
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

<script>
document.addEventListener('DOMContentLoaded', () => {
  const burgerBtn = document.getElementById('burgerBtn');
  const mobileMenu = document.getElementById('mobileMenu');

  burgerBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });

  // Mobile About CNO Dropdown
  const mobileAboutBtn = document.getElementById('mobileAboutBtn');
  const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
  const mobileAboutArrow = document.getElementById('mobileAboutArrow');

  mobileAboutBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    mobileAboutDropdown.classList.toggle('hidden');
    mobileAboutArrow.classList.toggle('rotate-180');
  });

  // Optional: close dropdown if clicked outside mobile menu
  document.addEventListener('click', (e) => {
    if (!mobileMenu.contains(e.target)) {
      mobileAboutDropdown.classList.add('hidden');
      mobileAboutArrow.classList.remove('rotate-180');
    }
  });
});
// Dropdown functionality for About CNO
 const aboutBtn = document.getElementById('aboutBtn');
  const aboutDropdown = document.getElementById('aboutDropdown');
  const aboutArrow = document.getElementById('aboutArrow');

  aboutBtn.addEventListener('click', (e) => {
    e.stopPropagation(); // prevent document click
    aboutDropdown.classList.toggle('hidden');
    aboutArrow.classList.toggle('rotate-180');
  });

  // Close dropdown if clicked outside
  document.addEventListener('click', () => {
    if(!aboutDropdown.classList.contains('hidden')) {
      aboutDropdown.classList.add('hidden');
      aboutArrow.classList.remove('rotate-180');
    }
  });
</script>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col items-center px-4 py-10 lg:px-20 text-center">
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-8 text-left">
            <h1 class="text-3xl font-bold text-gray-800 mb-4 text-center">Terms of Use</h1>
            <p class="text-gray-600 mb-6 text-center">
                Maligayang pagdating sa CNO NutriMap. Bago gamitin ang aming website, pakibasa ang sumusunod na mga tuntunin.
            </p>

            <h2 class="text-2xl font-semibold text-gray-700 mb-2">1. Pangkalahatang Panuntunan</h2>
            <p class="text-gray-600 mb-4">
                Ang pag-access at paggamit sa CNO NutriMap ay nakasalalay sa inyong pagsunod sa mga panuntunan na ito. Kung hindi kayo sumasang-ayon, huwag ipagpatuloy ang paggamit ng website.
            </p>
            
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">2. Paggamit ng Nilalaman</h2>
            <p class="text-gray-600 mb-4">
                Ang lahat ng nilalaman, kabilang ang teksto, graphics, at data, ay pag-aari ng City Nutrition Office at protektado ng copyright. Maaari lamang itong gamitin para sa personal at non-commercial na layunin.
            </p>
            
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">3. Responsibilidad ng User</h2>
            <p class="text-gray-600 mb-4">
                Hindi kayo dapat magpadala ng anumang nakakasirang materyal o impormasyon na lumalabag sa batas. Titiyakin ninyo na ang lahat ng ibibigay na impormasyon ay tama at totoo.
            </p>

            <h2 class="text-2xl font-semibold text-gray-700 mb-2">4. Limitasyon ng Pananagutan</h2>
            <p class="text-gray-600 mb-4">
                Hindi mananagot ang CNO NutriMap para sa anumang direktang, hindi direkta, o consequential na pinsala na maaaring magmula sa paggamit o kawalan ng kakayahan na gamitin ang website.
            </p>
            
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">5. Pagbabago sa Tuntunin</h2>
            <p class="text-gray-600">
                May karapatan ang CNO NutriMap na baguhin ang mga tuntunin na ito anumang oras nang walang paunang abiso. Ang patuloy na paggamit ng website ay nangangahulugang tinatanggap ninyo ang mga pagbabago.
            </p>
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
            <li><a href="../about_us/mission.php" class="hover:text-cyan-600">Our Mission</a></li>
            <li><a href="../about_us/vision.php" class="hover:text-cyan-600">Our Vision</a></li>
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
            <li><a href="terms.php" class="hover:text-cyan-600">Terms of Use</a></li>
            <li><a href="privacy.php" class="hover:text-cyan-600">Privacy Policy</a></li>
            <li><a href="cookies.php" class="hover:text-cyan-600">Cookies</a></li>
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
