<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | Contact Us</title>
  <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png">

  <!-- Tailwind CSS -->
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

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    /* Force all teal/cyan to green */
    .text-teal-600, .text-teal-500, .text-teal-400, .text-teal-700,
    .text-cyan-600, .text-cyan-500, .text-cyan-400 {
      color: #3e9a3e !important;
    }
    .bg-teal-600, .bg-teal-700, .bg-teal-500,
    .bg-cyan-600, .bg-cyan-700, .bg-cyan-500 {
      background-color: #3e9a3e !important;
    }
    .bg-teal-600:hover, .bg-teal-700:hover,
    .bg-cyan-600:hover, .bg-cyan-700:hover, .bg-cyan-500:hover {
      background-color: #2e7d32 !important;
    }
    .hover\:text-teal-600:hover, .hover\:text-teal-500:hover,
    .hover\:text-cyan-600:hover, .hover\:text-cyan-500:hover {
      color: #3e9a3e !important;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #eef2f6 100%);
    }
    
    /* Compact card styling */
    .compact-card {
      transition: all 0.3s ease;
    }
    .compact-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.12);
    }
    
    /* Form input styling */
    .form-input {
      transition: all 0.2s ease;
      border: 1px solid #e2e8f0;
    }
    .form-input:focus {
      border-color: #3e9a3e;
      box-shadow: 0 0 0 2px rgba(62, 154, 62, 0.1);
      outline: none;
    }
    
    /* Info item hover */
    .info-item {
      transition: all 0.2s ease;
    }
    .info-item:hover {
      background: rgba(62, 154, 62, 0.04);
      transform: translateX(4px);
    }
  </style>
</head>

<body class="flex flex-col min-h-screen font-inter text-gray-800">

<?php 
  require_once '../../../otp/mailer.php';

  $successMsg = "";
  $errorMsg = "";

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
      $name = trim($_POST['name']);
      $email = trim($_POST['email']);
      $message = trim($_POST['message']);

      if (!empty($name) && !empty($email) && !empty($message)) {
          $to = "citynutritionoffice@elsalvadorcity.gov.phs";
          $subject = "New Message from visitor - $name";

          $body = "
              <h3>Message from the guest user of CNO NutriMap</h3>
              <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
              <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
              <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
              <hr>
              <p>This message was sent via the CNO NutriMap Contact Form.</p>
          ";

          if (sendEmailNotification($to, $subject, $body)) {
              $successMsg = "✅ Message sent successfully!";
          } else {
              $errorMsg = "❌ Failed to send message. Please try again later.";
          }
      } else {
          $errorMsg = "⚠️ All fields are required.";
      }
  }
?>
   <!-- HEADER - Original -->
  <header class="header flex justify-between items-center px-6 md:px-10 h-14 bg-white shadow relative">
    <div class="flex items-center font-bold text-2xl text-gray-700">
      <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
      <img src="../../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
      <span class="text-nutrigreen-500">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../../../index.php" class="hover:text-nutrigreen-500">Home</a>
      <a href="../../map.php" class="hover:text-nutrigreen-500">Map</a>

      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 font-semibold text-gray-700 hover:text-nutrigreen-500 focus:outline-none">
          About CNO
          <svg class="w-4 h-4 transition-transform" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
          </svg>
        </button>
        <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-gray-100 shadow-lg rounded hidden z-50">
          <a href="../about_us/about.php" class="block px-4 py-2 hover:bg-gray-200">About</a>
          <a href="../about_us/profile.php" class="block px-4 py-2 hover:bg-gray-200">Profile</a>
          <a href="../about_us/vision.php" class="block px-4 py-2 hover:bg-gray-200">Vision</a>
          <a href="../about_us/mission.php" class="block px-4 py-2 hover:bg-gray-200">Mission</a>
        </div>
      </div>

      <a href="contact.php" class="text-nutrigreen-500">Contact Us</a>
      <a href="../../../login.php" class="bg-nutrigreen-500 text-white px-4 py-2 rounded hover:bg-nutrigreen-600">Login</a>
    </nav>

    <div class="md:hidden flex items-center">
      <button id="burgerBtn" class="text-gray-700 focus:outline-none">
        <i class="fa-solid fa-bars text-2xl"></i>
      </button>
    </div>

    <div id="mobileMenu" class="hidden absolute top-full left-0 w-full bg-white shadow-md z-20 flex flex-col">
      <a href="../../../index.php" class="px-6 py-3 border-b hover:bg-gray-100">Home</a>
      <a href="../../map.php" class="px-6 py-3 border-b hover:bg-gray-100">Map</a>
      <div class="flex flex-col">
        <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 border-b hover:bg-gray-100 focus:outline-none">
          About CNO
          <i id="mobileAboutArrow" class="fa-solid fa-chevron-down transition-transform"></i>
        </button>
        <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
          <a href="../about_us/about.php" class="px-8 py-2 hover:bg-gray-200">About</a>
          <a href="../about_us/profile.php" class="px-8 py-2 hover:bg-gray-200">Profile</a>
          <a href="../about_us/vision.php" class="px-8 py-2 hover:bg-gray-200">Vision</a>
          <a href="../about_us/mission.php" class="px-8 py-2 hover:bg-gray-200">Mission</a>
        </div>
      </div>
      <a href="contact.php" class="px-6 py-3 border-b hover:bg-gray-100">Contact Us</a>
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
      mobileAboutBtn.addEventListener('click', () => {
        mobileAboutDropdown.classList.toggle('hidden');
        mobileAboutArrow.classList.toggle('rotate-180');
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

  <!-- MODERN COMPACT MAIN CONTENT -->
  <main class="container mx-auto px-4 md:px-6 py-8 max-w-6xl">
    
    <!-- Page Header -->
    <div class="text-center mb-8">
      <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Get In <span class="text-nutrigreen-500">Touch</span></h1>
      <div class="w-16 h-0.5 bg-nutrigreen-500 mx-auto rounded-full"></div>
      <p class="text-gray-500 text-sm mt-3">Have questions? We'd love to hear from you.</p>
    </div>

    <!-- Three Column Layout -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
      
      <!-- Contact Info Card -->
      <div class="bg-white rounded-xl shadow-md p-5 compact-card">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-9 h-9 bg-nutrigreen-100 rounded-lg flex items-center justify-center">
            <i class="fa-regular fa-building text-nutrigreen-500 text-sm"></i>
          </div>
          <h2 class="font-semibold text-gray-800">Office Info</h2>
        </div>
        <div class="space-y-3">
          <div class="info-item flex gap-3 text-sm p-2 rounded-lg">
            <i class="fa-solid fa-location-dot text-nutrigreen-500 mt-0.5"></i>
            <span class="text-gray-600">Poblacion, El Salvador, Philippines 9017</span>
          </div>
          <div class="info-item flex gap-3 text-sm p-2 rounded-lg">
            <i class="fa-regular fa-calendar text-nutrigreen-500 mt-0.5"></i>
            <span class="text-gray-600">Mon - Fri / 8:00 AM - 5:00 PM</span>
          </div>
        </div>
      </div>

      <!-- Phone Card -->
      <div class="bg-white rounded-xl shadow-md p-5 compact-card">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-9 h-9 bg-nutrigreen-100 rounded-lg flex items-center justify-center">
            <i class="fa-solid fa-phone text-nutrigreen-500 text-sm"></i>
          </div>
          <h2 class="font-semibold text-gray-800">Call Us</h2>
        </div>
        <div class="space-y-3">
          <div class="flex items-center gap-3 p-2">
            <i class="fa-solid fa-mobile-alt text-nutrigreen-500 text-lg"></i>
            <div>
              <p class="text-gray-800 font-medium text-sm">0917 713 2398</p>
              <span class="text-gray-400 text-xs">Mobile Number</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Email Card -->
      <div class="bg-white rounded-xl shadow-md p-5 compact-card">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-9 h-9 bg-nutrigreen-100 rounded-lg flex items-center justify-center">
            <i class="fa-regular fa-envelope text-nutrigreen-500 text-sm"></i>
          </div>
          <h2 class="font-semibold text-gray-800">Email Us</h2>
        </div>
        <div class="space-y-3">
          <div class="flex items-start gap-3 p-2">
            <i class="fa-regular fa-paper-plane text-nutrigreen-500 mt-0.5 text-sm"></i>
            <div class="break-all">
              <p class="text-gray-800 font-medium text-sm">citynutritionoffice@elsalvadorcity.gov.ph</p>
              <span class="text-gray-400 text-xs">Email Address</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Two Column Layout: Form + Map -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Message Form -->
      <div class="bg-white rounded-xl shadow-md p-5 compact-card">
        <div class="flex items-center gap-3 mb-5">
          <div class="w-9 h-9 bg-nutrigreen-100 rounded-lg flex items-center justify-center">
            <i class="fa-regular fa-paper-plane text-nutrigreen-500 text-sm"></i>
          </div>
          <h2 class="font-semibold text-gray-800">Send a Message</h2>
        </div>

        <?php if (!empty($successMsg)): ?>
          <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-5">
            <p id="successMsg" class="text-green-600 text-sm text-center"><?= $successMsg ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
          <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-5">
            <p class="text-red-600 text-sm text-center"><?= $errorMsg ?></p>
          </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
          <div>
            <input type="text" name="name" placeholder="Your Name" required 
                   class="form-input w-full rounded-lg px-4 py-2.5 text-sm bg-gray-50 focus:bg-white">
          </div>
          <div>
            <input type="email" name="email" placeholder="Email Address" required 
                   class="form-input w-full rounded-lg px-4 py-2.5 text-sm bg-gray-50 focus:bg-white">
          </div>
          <div>
            <textarea name="message" placeholder="Your Message" required 
                      class="form-input w-full rounded-lg px-4 py-2.5 text-sm bg-gray-50 focus:bg-white min-h-[100px] resize-y"></textarea>
          </div>
          <button type="submit" name="send_message" 
                  class="w-full bg-nutrigreen-500 hover:bg-nutrigreen-600 text-white font-medium py-2.5 rounded-lg transition-all text-sm shadow-sm hover:shadow">
            <i class="fa-regular fa-paper-plane mr-2"></i>Send Message
          </button>
        </form>
      </div>

      <!-- Map Card - Pointing to El Salvador City ECCD -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden compact-card">
        <div class="bg-nutrigreen-500 px-5 py-3">
          <h3 class="text-white font-semibold text-sm flex items-center gap-2">
            <i class="fa-solid fa-map-pin"></i>
            <span>El Salvador City - ECCD - Nutrition Building</span>
          </h3>
        </div>
        <div class="h-[280px] w-full">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3943.456!2d124.521!3d8.567!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x324e4b1e3e5e3e5e%3A0x3e3e3e3e3e3e3e3e!2sEl%20Salvador%20City%20Hall!5e0!3m2!1sen!2sph!4v1700000000000!5m2!1sen!2sph"
            class="w-full h-full"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
        <div class="px-4 py-2 bg-gray-50 border-t border-gray-100">
          <p class="text-xs text-gray-500 flex items-center gap-1">
            <i class="fa-solid fa-location-dot text-nutrigreen-500"></i>
            Poblacion, City of El Salvador, Misamis Oriental
          </p>
        </div>
      </div>
    </div>

    <!-- Small note -->
    <div class="text-center mt-6">
      <p class="text-xs text-gray-400">
        <i class="fa-regular fa-clock mr-1"></i> Response within 1-2 business days
      </p>
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
          <li><a href="contact.php" class="hover:text-nutrigreen-400 transition-colors">Contact Us</a></li>
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


  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const msg = document.getElementById("successMsg");
      if (msg) {
        setTimeout(() => {
          msg.style.opacity = "0";
          setTimeout(() => msg.remove(), 500);
        }, 10000);
      }
    });
  </script>
</body>
</html>