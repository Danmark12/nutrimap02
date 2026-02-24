<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CNO NutriMap Privacy Policy</title>
  <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body class="flex flex-col min-h-screen bg-gray-100 text-gray-800">

  <!-- ====== HEADER ====== -->
  <header class="flex justify-between items-center px-6 md:px-10 py-4 bg-white shadow-md relative z-50">
    <!-- Logo -->
    <div class="flex items-center space-x-2">
      <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 rounded-lg" />
      <img src="../../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 rounded-lg" />
      <h1 class="text-2xl font-bold text-gray-700">
        <span class="text-teal-600">CNO</span> NutriMap
      </h1>
    </div>

    <!-- Desktop Navigation -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../../../index.php" class="hover:text-teal-600 transition">Home</a>
      <a href="../../map.php" class="hover:text-teal-600 transition">Map</a>

      <!-- About Dropdown -->
      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 hover:text-teal-600 transition">
          About CNO
          <svg id="aboutArrow" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z" />
          </svg>
        </button>

        <div id="aboutDropdown" class="absolute left-0 mt-2 w-44 bg-white border border-gray-200 shadow-lg rounded-lg hidden">
          <a href="../about_us/about.php" class="block px-4 py-2 hover:bg-gray-100">About</a>
          <a href="../about_us/profile.php" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
          <a href="../about_us/vision.php" class="block px-4 py-2 hover:bg-gray-100">Vision</a>
          <a href="../about_us/mission.php" class="block px-4 py-2 hover:bg-gray-100">Mission</a>
        </div>
      </div>

      <a href="../contact_us/contact.php" class="hover:text-teal-600 transition">Contact</a>
      <a href="../../../login.php" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700 transition">Login</a>
    </nav>

    <!-- Mobile Menu Button -->
    <button id="burgerBtn" class="md:hidden text-gray-700 focus:outline-none">
      <i class="fas fa-bars text-2xl"></i>
    </button>
  </header>

  <!-- ====== MOBILE MENU ====== -->
  <div id="mobileMenu" class="hidden flex flex-col bg-white shadow-md md:hidden absolute top-[72px] left-0 w-full border-t border-gray-200 z-40">
    <a href="../../../index.php" class="px-6 py-3 border-b hover:bg-gray-100">Home</a>
    <a href="../../map.php" class="px-6 py-3 border-b hover:bg-gray-100">Map</a>

    <!-- Mobile About Dropdown -->
    <div class="flex flex-col border-b">
      <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 hover:bg-gray-100 focus:outline-none">
        About CNO
        <svg id="mobileAboutArrow" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z" />
        </svg>
      </button>
      <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
        <a href="../about_us/about.php" class="px-8 py-2 hover:bg-gray-200">About</a>
        <a href="../about_us/profile.php" class="px-8 py-2 hover:bg-gray-200">Profile</a>
        <a href="../about_us/vision.php" class="px-8 py-2 hover:bg-gray-200">Vision</a>
        <a href="../about_us/mission.php" class="px-8 py-2 hover:bg-gray-200">Mission</a>
      </div>
    </div>

    <a href="../contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-100">Contact</a>
    <a href="../../../login.php" class="px-6 py-3 hover:bg-gray-100">Login</a>
  </div>

  <!-- ====== MAIN CONTENT ====== -->
  <main class="flex-grow flex flex-col items-center px-4 py-10 lg:px-20 text-center">
    <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-8 text-left">
      <h1 class="text-3xl font-bold text-gray-800 mb-4 text-center">Privacy Policy</h1>
      <p class="text-gray-600 mb-6 text-center">
        Mahalaga sa amin ang inyong privacy. Ipinapaliwanag ng policy na ito kung paano namin kinokolekta, ginagamit, at pinoprotektahan ang inyong personal na impormasyon.
      </p>

      <h2 class="text-2xl font-semibold text-gray-700 mb-2">1. Impormasyong Kinokolekta Namin</h2>
      <p class="text-gray-600 mb-4">
        Kapag gumagamit kayo ng CNO NutriMap, maaari kaming mangolekta ng impormasyon na ibinigay ninyo nang kusa, tulad ng pangalan, email address, at iba pang contact details na isinumite sa pamamagitan ng forms.
      </p>

      <h2 class="text-2xl font-semibold text-gray-700 mb-2">2. Paano Namin Ginagamit ang Impormasyon</h2>
      <p class="text-gray-600 mb-4">
        Ginagamit namin ang impormasyon upang mapabuti ang serbisyo, tumugon sa inyong mga katanungan, at magbigay ng updates. Hindi namin ipagbebenta, ibabahagi, o ilalabas ang inyong impormasyon sa mga third parties maliban na lang kung kinakailangan ng batas.
      </p>

      <h2 class="text-2xl font-semibold text-gray-700 mb-2">3. Seguridad ng Datos</h2>
      <p class="text-gray-600 mb-4">
        Ginagamit namin ang iba't ibang security measures upang mapanatiling ligtas ang inyong personal na datos. Gayunpaman, walang method ng electronic storage na 100% ligtas.
      </p>

      <h2 class="text-2xl font-semibold text-gray-700 mb-2">4. Pagbabago sa Policy</h2>
      <p class="text-gray-600">
        Maaari naming i-update ang privacy policy na ito anumang oras. Inaabisuhan namin kayo na regular na suriin ang page na ito para sa mga pagbabago.
      </p>
    </section>
  </main>

  <!-- ====== FOOTER ====== -->
  <footer class="bg-gray-800 text-gray-300 py-10 mt-auto">
    <div class="max-w-7xl mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
        <!-- Logo -->
        <div class="md:col-span-2">
          <div class="flex items-center mb-4">
            <img src="../../../img/CNO_Logo.png" alt="CNO Logo" class="h-10 mr-2 rounded-lg" />
            <span class="text-2xl font-bold text-teal-500">CNO</span>
            <span class="text-2xl font-bold text-white ml-1">NutriMap</span>
          </div>
          <p class="text-sm">
            A tool to visualize health and nutrition data for children in El Salvador City.
          </p>
        </div>

        <div>
          <h3 class="text-lg font-semibold text-white mb-4">About Us</h3>
          <ul class="space-y-2">
            <li><a href="../about_us/mission.php" class="hover:text-teal-400">Our Mission</a></li>
            <li><a href="../about_us/vision.php" class="hover:text-teal-400">Our Vision</a></li>
          </ul>
        </div>

        <div>
          <h3 class="text-lg font-semibold text-white mb-4">Quick Links</h3>
          <ul class="space-y-2">
            <li><a href="../../map.php" class="hover:text-teal-400">Map</a></li>
            <li><a href="../contact_us/contact.php" class="hover:text-teal-400">Contact Us</a></li>
          </ul>
        </div>

        <div>
          <h3 class="text-lg font-semibold text-white mb-4">Legal & Support</h3>
          <ul class="space-y-2">
            <li><a href="terms.php" class="hover:text-teal-400">Terms of Use</a></li>
            <li><a href="privacy.php" class="hover:text-teal-400">Privacy Policy</a></li>
            <li><a href="cookies.php" class="hover:text-teal-400">Cookies</a></li>
            <li><a href="../help_and_support/help.php" class="hover:text-teal-400">Help</a></li>
            <li><a href="../help_and_support/faqs.php" class="hover:text-teal-400">FAQs</a></li>
          </ul>
        </div>
      </div>

      <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
        <p>Copyright © 2025 CNO NutriMap. All Rights Reserved. Developed By NBSC ICS 4th Year Student.</p>
      </div>
    </div>
  </footer>

  <!-- ====== JS for Dropdowns ====== -->
  <script>
    const aboutBtn = document.getElementById('aboutBtn');
    const aboutDropdown = document.getElementById('aboutDropdown');
    const aboutArrow = document.getElementById('aboutArrow');
    const burgerBtn = document.getElementById('burgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileAboutBtn = document.getElementById('mobileAboutBtn');
    const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
    const mobileAboutArrow = document.getElementById('mobileAboutArrow');

    aboutBtn.addEventListener('click', e => {
      e.stopPropagation();
      aboutDropdown.classList.toggle('hidden');
      aboutArrow.classList.toggle('rotate-180');
    });

    burgerBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });

    mobileAboutBtn.addEventListener('click', e => {
      e.stopPropagation();
      mobileAboutDropdown.classList.toggle('hidden');
      mobileAboutArrow.classList.toggle('rotate-180');
    });

    document.addEventListener('click', e => {
      if (!aboutBtn.contains(e.target)) {
        aboutDropdown.classList.add('hidden');
        aboutArrow.classList.remove('rotate-180');
      }
    });
  </script>

</body>
</html>
