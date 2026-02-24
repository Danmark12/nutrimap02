<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO NutriMap | Contact Us</title>
  <link rel="icon" type="image/png" href="../../../img/CNO_Logo.png">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body class="flex flex-col min-h-screen font-inter bg-gray-100 text-gray-800">

<?php 

  require_once '../../../otp/mailer.php'; // ✅ Include PHPMailer configuration

  $successMsg = "";
  $errorMsg = "";

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
      $name = trim($_POST['name']);
      $email = trim($_POST['email']);
      $message = trim($_POST['message']);

      if (!empty($name) && !empty($email) && !empty($message)) {
          $to = "louizkylaspona@gmail.com"; // ✅ Your admin/CNO email
          $subject = "New Message from Guest User - $name";

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
   <!-- HEADER -->
  <header class="header flex justify-between items-center px-6 md:px-10 py-4 bg-white shadow relative">
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
        <button id="aboutBtn" class="flex items-center gap-1 font-semibold text-gray-700 hover:text-teal-600 focus:outline-none">
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

      <a href="contact.php" class="text-teal-600">Contact Us</a>
      <a href="../../../login.php" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">Login</a>
    </nav>

    <!-- Mobile Burger -->
    <div class="md:hidden flex items-center">
      <button id="burgerBtn" class="text-gray-700 focus:outline-none">
        <i class="fa-solid fa-bars text-2xl"></i>
      </button>
    </div>

    <!-- Mobile Menu -->
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

  <!-- JS for Navbar -->
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

  <!-- MAIN CONTENT -->
  <main class="container mx-auto px-6 py-10 flex flex-col gap-8">

    <!-- Contact Info + Map -->
    <div class="flex flex-col md:flex-row bg-white shadow rounded-lg overflow-hidden">
      <div class="md:w-1/2 p-8 flex flex-col gap-6">
        <h2 class="text-2xl font-semibold text-black">Contact Information</h2>

        <div class="flex items-start gap-4">
          <i class="fa-solid fa-location-dot text-cyan-500 text-xl mt-1"></i>
          <div>
            <h4 class="text-gray-900 font-medium">Address</h4>
            <p class="text-gray-600">Poblacion, El Salvador, Philippines, 9017</p>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <i class="fa-solid fa-map-location text-cyan-500 text-xl mt-1"></i>
          <div>
            <h4 class="text-gray-900 font-medium">Service area</h4>
            <p class="text-gray-600">El Salvador, Philippines</p>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <i class="fa-solid fa-calendar-days text-cyan-500 text-xl mt-1"></i>
          <div>
            <h4 class="text-gray-900 font-medium">Open Days</h4>
            <p class="text-gray-600">Monday to Friday</p>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <i class="fa-regular fa-clock text-cyan-500 text-xl mt-1"></i>
          <div>
            <h4 class="text-gray-900 font-medium">Open/Closing Hours</h4>
            <p class="text-gray-600">08:00 am - 17:00 pm</p>
          </div>
        </div>
      </div>

      <!-- Map -->
      <div class="md:w-1/2 min-h-[300px]">
        <iframe
          src="https://www.google.com/maps?q=El%20Salvador%20Misamis%20Oriental&output=embed"
          class="w-full h-full"
          allowfullscreen
          loading="lazy">
        </iframe>
      </div>
    </div>

    <!-- Contact Boxes -->
    <div class="flex flex-col md:flex-row gap-6">
      <div class="flex items-center gap-4 bg-white shadow rounded-lg p-4">
        <i class="fa-solid fa-phone text-cyan-500 text-2xl"></i>
        <div>
          <p class="text-gray-800 font-medium">0917 713 2398</p>
          <span class="text-gray-500 text-sm">Mobile</span>
        </div>
      </div>
      <div class="flex items-center gap-4 bg-white shadow rounded-lg p-4">
        <i class="fa-solid fa-envelope text-cyan-500 text-2xl"></i>
        <div>
          <p class="text-gray-800 font-medium">citynutritionoffice@elsalvadorcity.gov.ph</p>
          <span class="text-gray-500 text-sm">Email</span>
        </div>
      </div>
    </div>

    <!-- Message Form -->
    <div class="bg-white shadow rounded-lg p-8">
      <h2 class="text-2xl font-semibold mb-6 text-black">Send Us a Message</h2>

      <?php if (!empty($successMsg)): ?>
        <p id="successMsg" class="text-green-600 font-medium mb-4"><?= $successMsg ?></p>
      <?php endif; ?>

      <?php if (!empty($errorMsg)): ?>
        <p class="text-red-600 font-medium mb-4"><?= $errorMsg ?></p>
      <?php endif; ?>

      <form method="POST" action="" class="flex flex-col gap-4">
        <input type="text" name="name" placeholder="Your Name" required class="border border-gray-300 rounded px-4 py-2 focus:border-cyan-500 focus:ring focus:ring-cyan-200 outline-none">
        <input type="email" name="email" placeholder="Your Email" required class="border border-gray-300 rounded px-4 py-2 focus:border-cyan-500 focus:ring focus:ring-cyan-200 outline-none">
        <textarea name="message" placeholder="Write your message here..." required class="border border-gray-300 rounded px-4 py-2 focus:border-cyan-500 focus:ring focus:ring-cyan-200 outline-none min-h-[120px] resize-y"></textarea>
        <button type="submit" name="send_message" class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold px-6 py-2 rounded">Send</button>
      </form>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-gray-800 text-gray-300 py-10 relative z-10">
    <div class="max-w-7xl mx-auto px-4">
      <div class="grid gap-8 md:grid-cols-5">
        <div class="md:col-span-2">
          <div class="flex items-center mb-4">
            <img src="../../../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2 rounded-lg">
            <span class="text-cyan-600 text-xl font-bold">CNO</span>
            <span class="text-white text-xl font-bold ml-1">NutriMap</span>
          </div>
          <p class="text-sm">A tool to visualize health and nutrition data for children in El Salvador City.</p>
        </div>

        <div>
          <h3 class="text-white font-semibold mb-4">About Us</h3>
          <ul class="space-y-2">
            <li><a href="../about_us/mission.php" class="hover:text-cyan-600">Our Mission</a></li>
            <li><a href="../about_us/vision.php" class="hover:text-cyan-600">Our Vision</a></li>
          </ul>
        </div>

        <div>
          <h3 class="text-white font-semibold mb-4">Quick Links</h3>
          <ul class="space-y-2">
            <li><a href="../../map.php" class="hover:text-cyan-600">Map</a></li>
            <li><a href="contact.php" class="hover:text-cyan-600">Contact Us</a></li>
          </ul>
        </div>

        <div>
          <h3 class="text-white font-semibold mb-4">Legal & Support</h3>
          <ul class="space-y-2">
            <li><a href="../legal_and_support/terms.php" class="hover:text-cyan-600">Terms of Use</a></li>
            <li><a href="../legal_and_support/privacy.php" class="hover:text-cyan-600">Privacy Policy</a></li>
            <li><a href="../legal_and_support/cookies.php" class="hover:text-cyan-600">Cookies</a></li>
            <li><a href="../help_and_support/help.php" class="hover:text-cyan-600">Help</a></li>
            <li><a href="../help_and_support/faqs.php" class="hover:text-cyan-600">FAQs</a></li>
          </ul>
        </div>
      </div>

      <div class="mt-8 border-t border-gray-700 pt-8 text-center text-gray-400 text-sm">
        <p>&copy; 2025 CNO NutriMap | All Rights Reserved. Developed by NBSC ICS 4th Year Student.</p>
      </div>
    </div>
  </footer>

  <!-- JS: Auto-hide success message -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const msg = document.getElementById("successMsg");
      if (msg) {
        setTimeout(() => {
          msg.style.opacity = "0";
          setTimeout(() => msg.remove(), 500);
        }, 10000); // 10 seconds
      }
    });
  </script>
</body>
</html>
