<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CNO NutriMap | Terms of Use</title>
    <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png">
    
    <!-- Tailwind CSS CDN -->
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
    
    <!-- Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Override all teal/cyan colors to nutrigreen */
        .text-teal-600, .text-teal-500, .text-teal-400, .text-teal-700,
        .text-cyan-600, .text-cyan-500, .text-cyan-400 {
            color: #3e9a3e !important;
        }
        .bg-teal-600, .bg-teal-700, .bg-teal-500,
        .bg-cyan-600, .bg-cyan-700, .bg-cyan-500 {
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
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #eef2f6 100%);
            font-family: 'Inter', sans-serif;
        }
        
        /* Terms section styling */
        .terms-section {
            transition: all 0.2s ease;
        }
        
        .terms-section:hover {
            transform: translateX(5px);
        }
        
        .terms-icon {
            transition: all 0.2s ease;
        }
        
        .terms-section:hover .terms-icon {
            transform: scale(1.05);
            background-color: #3e9a3e;
        }
        
        .terms-section:hover .terms-icon i {
            color: white !important;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen text-gray-800">

  <!-- Header (Updated to green theme) -->
  <header class="header flex justify-between items-center px-6 md:px-10 h-14 bg-white shadow relative">
    <!-- Logo -->
    <div class="flex items-center font-bold text-2xl text-gray-700">
        <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
        <img src="../../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
        <span class="text-nutrigreen-500">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <!-- Desktop nav -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
        <a href="../../../index.php" class="hover:text-nutrigreen-500">Home</a>
        <a href="../../map.php" class="hover:text-nutrigreen-500">Map</a>
        
        <!-- Dropdown Parent -->
        <div class="relative">
            <button id="aboutBtn" class="flex items-center gap-1 font-semibold text-gray-700 hover:text-nutrigreen-500 cursor-pointer focus:outline-none">
                About CNO
                <svg class="w-4 h-4 transition-transform" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                </svg>
            </button>

            <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-gray-100 shadow-lg rounded hidden z-50">
                <a href="../about_us/about.php" class="block px-4 py-2 hover:bg-gray-200">About</a>
                <a href="../about_us/profile.php" class="block px-4 py-2 hover:bg-gray-200">Profile</a>
                <a href="../about_us/vision.php" class="block px-4 py-2 hover:bg-gray-200">Vision</a>
                <a href="../about_us/mission.php" class="block px-4 py-2 hover:bg-gray-200">Mission</a>
            </div>
        </div>

        <a href="../contact_us/contact.php" class="text-nutrigreen-500">Contact Us</a>
        <a href="../../../login.php" class="bg-nutrigreen-500 text-white px-4 py-2 rounded hover:bg-nutrigreen-600 transition">Login</a>
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

  <!-- ====== MAIN CONTENT - NO CARD ====== -->
  <main class="container mx-auto px-4 md:px-6 py-8 max-w-4xl">
    
    <!-- Page Header -->
    <div class="text-center mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
            Terms of <span class="text-nutrigreen-500">Use</span>
        </h1>
        <div class="w-16 h-0.5 bg-nutrigreen-500 mx-auto rounded-full mb-4"></div>
        <p class="text-gray-500 text-sm">Maligayang pagdating sa CNO NutriMap. Bago gamitin ang aming website, pakibasa ang sumusunod na mga tuntunin.</p>
    </div>

    <!-- Terms Sections -->
    <div class="space-y-5">
        
        <!-- Section 1 -->
        <div class="terms-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="terms-icon w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-scale-balanced text-nutrigreen-500 text-sm"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2">1. Pangkalahatang Panuntunan</h2>
                    <p class="text-gray-600 leading-relaxed text-sm md:text-base">
                        Ang pag-access at paggamit sa CNO NutriMap ay nakasalalay sa inyong pagsunod sa mga panuntunan na ito. Kung hindi kayo sumasang-ayon, huwag ipagpatuloy ang paggamit ng website.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Section 2 -->
        <div class="terms-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="terms-icon w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fa-regular fa-copyright text-nutrigreen-500 text-sm"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2">2. Paggamit ng Nilalaman</h2>
                    <p class="text-gray-600 leading-relaxed text-sm md:text-base">
                        Ang lahat ng nilalaman, kabilang ang teksto, graphics, at data, ay pag-aari ng City Nutrition Office at protektado ng copyright. Maaari lamang itong gamitin para sa personal at non-commercial na layunin.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Section 3 -->
        <div class="terms-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="terms-icon w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-user-shield text-nutrigreen-500 text-sm"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2">3. Responsibilidad ng User</h2>
                    <p class="text-gray-600 leading-relaxed text-sm md:text-base">
                        Hindi kayo dapat magpadala ng anumang nakakasirang materyal o impormasyon na lumalabag sa batas. Titiyakin ninyo na ang lahat ng ibibigay na impormasyon ay tama at totoo.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 4 -->
        <div class="terms-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="terms-icon w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-handcuffs text-nutrigreen-500 text-sm"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2">4. Limitasyon ng Pananagutan</h2>
                    <p class="text-gray-600 leading-relaxed text-sm md:text-base">
                        Hindi mananagot ang CNO NutriMap para sa anumang direktang, hindi direkta, o consequential na pinsala na maaaring magmula sa paggamit o kawalan ng kakayahan na gamitin ang website.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Section 5 -->
        <div class="terms-section p-5 rounded-lg bg-white shadow-sm hover:shadow-md transition-all">
            <div class="flex items-start gap-4">
                <div class="terms-icon w-10 h-10 bg-nutrigreen-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-pen-to-square text-nutrigreen-500 text-sm"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-2">5. Pagbabago sa Tuntunin</h2>
                    <p class="text-gray-600 leading-relaxed text-sm md:text-base">
                        May karapatan ang CNO NutriMap na baguhin ang mga tuntunin na ito anumang oras nang walang paunang abiso. Ang patuloy na paggamit ng website ay nangangahulugang tinatanggap ninyo ang mga pagbabago.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Acknowledgment Section -->
    <div class="mt-8 p-5 rounded-lg" style="background: rgba(62, 154, 62, 0.05);">
        <div class="flex items-start gap-3">
            <i class="fa-regular fa-circle-check text-nutrigreen-500 text-xl mt-0.5"></i>
            <div>
                <h3 class="font-semibold text-gray-800 mb-1">By using CNO NutriMap, you agree to:</h3>
                <ul class="text-gray-600 text-sm leading-relaxed space-y-1 mt-2">
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-nutrigreen-500 text-xs"></i> Comply with all terms and conditions stated above</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-nutrigreen-500 text-xs"></i> Use the website only for lawful purposes</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-nutrigreen-500 text-xs"></i> Respect the intellectual property rights of CNO</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Back to Legal Links -->
    <div class="mt-8 flex flex-wrap gap-3 justify-center">
        <a href="terms.php" class="inline-flex items-center gap-2 text-nutrigreen-500 text-sm transition-colors">
            <i class="fa-regular fa-file-lines"></i>
            Terms of Use
        </a>
        <span class="text-gray-300">|</span>
        <a href="privacy.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-nutrigreen-500 text-sm transition-colors">
            <i class="fa-regular fa-lock"></i>
            Privacy Policy
        </a>
        <span class="text-gray-300">|</span>
        <a href="cookies.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-nutrigreen-500 text-sm transition-colors">
            <i class="fa-solid fa-cookie-bite"></i>
            Cookies Policy
        </a>
    </div>
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

  <!-- JS for Dropdowns -->
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
            burgerBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        if (aboutBtn) {
            aboutBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                aboutDropdown.classList.toggle('hidden');
                aboutArrow.classList.toggle('rotate-180');
            });
        }

        if (mobileAboutBtn) {
            mobileAboutBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                mobileAboutDropdown.classList.toggle('hidden');
                mobileAboutArrow.classList.toggle('rotate-180');
            });
        }

        document.addEventListener('click', (e) => {
            if (aboutDropdown && !aboutDropdown.classList.contains('hidden') && aboutBtn && !aboutBtn.contains(e.target)) {
                aboutDropdown.classList.add('hidden');
                aboutArrow.classList.remove('rotate-180');
            }
            if (mobileMenu && !mobileMenu.contains(e.target) && mobileAboutDropdown && !mobileAboutDropdown.classList.contains('hidden')) {
                mobileAboutDropdown.classList.add('hidden');
                mobileAboutArrow.classList.remove('rotate-180');
            }
        });
    });
  </script>
</body>
</html>