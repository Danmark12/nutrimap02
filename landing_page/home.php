<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CNO NutriMap | Home</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'nutrigreen': {
              50: '#e6f7e6',
              100: '#c3e8c3',
              200: '#9ed89e',
              300: '#7ac87a',
              400: '#5cb85c',
              500: '#3e9a3e',
              600: '#2e7d32',
              700: '#1b5e20',
              800: '#0c4d0c',
              900: '#053005',
            }
          }
        }
      }
    }
  </script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* ── Original green overrides ── */
    .highlight-green { color: #3e9a3e !important; }
    .bg-highlight-green { background-color: #3e9a3e !important; }
    .border-highlight-green { border-color: #3e9a3e !important; }
    a, button, .transition-green { transition: all 0.2s ease; }
    *:focus-visible { outline: 2px solid #3e9a3e; outline-offset: 2px; }
    .text-teal-600,.text-teal-500,.text-teal-400,.text-teal-700 { color: #3e9a3e !important; }
    .bg-teal-600,.bg-teal-700,.bg-teal-500 { background-color: #3e9a3e !important; }
    .bg-teal-700:hover,.bg-teal-600:hover { background-color: #2e7d32 !important; }
    .hover\:text-teal-600:hover,.hover\:text-teal-500:hover,.hover\:text-teal-400:hover { color: #3e9a3e !important; }
    .border-teal-600 { border-color: #3e9a3e !important; }
    .from-teal-600,.to-teal-600 { --tw-gradient-from: #3e9a3e !important; --tw-gradient-to: #2e7d32 !important; }
    .text-teal-400 { color: #5cb85c !important; }
    footer .text-teal-500 { color: #3e9a3e !important; }
    footer .hover\:text-teal-400:hover { color: #5cb85c !important; }


    /* ══════════════════════════════
       HERO (LIGHTER & BRIGHTER)
    ══════════════════════════════ */
    .hero {
      position: relative;
      min-height: 88vh;
      display: flex;
      align-items: center;
      background: url('../img/bg_img.jpg') center/cover no-repeat;
      overflow: hidden;
      padding-bottom: 100px;
    }
    /* REMOVED the dark green overlay - now using a much lighter overlay */
    .hero-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(120deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.15) 100%);
    }
    
    .hero-content {
      position: relative; z-index: 2;
      padding: 3.5rem 5vw 2rem;
      max-width: 640px;
    }
    .hero-eyebrow {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.3);
      color: #ffffff;
      font-size: 11px; font-weight: 500; letter-spacing: 2px; text-transform: uppercase;
      padding: 5px 14px; border-radius: 100px;
      margin-bottom: 1.3rem;
    }
    .hero-eyebrow span {
      width: 6px; height: 6px; border-radius: 50%;
      background: #5cb85c; display: inline-block;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%,100% { opacity:1; } 50% { opacity:.4; }
    }
    .hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.2rem, 4.5vw, 3.5rem);
      font-weight: 700; color: #fff;
      line-height: 1.12; margin-bottom: 1rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    .hero h1 em { font-style: italic; color: #ffd700; }
    .hero-desc {
      font-size: 1rem; line-height: 1.75; color: rgba(255,255,255,0.95);
      max-width: 460px; margin-bottom: 2rem;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }
    .hero-cta {
      display: inline-flex; align-items: center; gap: 10px;
      background: #3e9a3e; color: #fff;
      font-family: 'DM Sans', sans-serif;
      font-weight: 500; font-size: 0.92rem;
      padding: 13px 26px; border-radius: 100px;
      text-decoration: none;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
    }
    .hero-cta:hover { background: #2e7d32; transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.4); }
    .hero-cta svg { width: 17px; height: 17px; flex-shrink: 0; }

    /* stat bar — LIGHTER background */
    .hero-stats {
      position: absolute; bottom: 0; left: 0; right: 0; z-index: 2;
      display: flex;
      background: rgba(0,0,0,0.25); 
      backdrop-filter: blur(12px);
      border-top: 1px solid rgba(255,255,255,0.2);
    }
    .hero-stat {
      flex: 1; padding: 1rem 1.2rem; text-align: center;
      border-right: 1px solid rgba(255,255,255,0.15);
    }
    .hero-stat:last-child { border-right: none; }
    .hero-stat-num {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem; font-weight: 700; color: #ffd700;
    }
    .hero-stat-lbl {
      font-size: 0.65rem; color: rgba(255,255,255,0.85);
      text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;
    }

    /* ══════════════════════════════
       SHARED
    ══════════════════════════════ */
    .nm-section { padding: 4rem 5vw; }
    .nm-tag {
      font-size: 10px; font-weight: 600; letter-spacing: 2.5px; text-transform: uppercase;
      color: #3e9a3e; margin-bottom: .5rem;
    }
    .nm-title {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.5rem, 2.5vw, 2.1rem);
      font-weight: 700; color: #1a2e1a; line-height: 1.22; margin-bottom: .8rem;
    }
    .nm-sub { font-size: .95rem; color: #5a6a5a; max-width: 500px; line-height: 1.7; }

    /* ══════════════════════════════
       SERVICES — compact pin grid
    ══════════════════════════════ */
    .services-bg { background: #f2f5f0; }
    .services-header {
      display: flex; justify-content: space-between;
      align-items: flex-end; flex-wrap: wrap;
      gap: 1rem; margin-bottom: 2.5rem;
    }

    .pin-grid { columns: 3; column-gap: 14px; }
    @media (max-width: 700px) { .pin-grid { columns: 2; } }
    @media (max-width: 440px) { .pin-grid { columns: 1; } }

    .pin-card {
      break-inside: avoid; margin-bottom: 14px;
      border-radius: 16px; overflow: hidden;
      background: #fff;
      box-shadow: 0 1px 8px rgba(0,0,0,0.07);
      transition: transform 0.22s, box-shadow 0.22s;
    }
    .pin-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.11);
    }
    .pin-img {
      width: 100%; display: flex;
      align-items: center; justify-content: center;
    }
    /* SMALLER heights */
    .h-s  { height: 110px; }
    .h-m  { height: 140px; }
    .h-l  { height: 170px; }

    .bg-leaf  { background: #d4edda; }
    .bg-sky   { background: #d1ecf1; }
    .bg-warm  { background: #fff3cd; }
    .bg-peach { background: #fde2d4; }
    .bg-lav   { background: #e8d5f5; }
    .bg-rose  { background: #fde0e8; }

    .pin-body { padding: 12px 14px 14px; }
    .pin-tag {
      font-size: 9px; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase;
      color: #3e9a3e; margin-bottom: 4px;
    }
    .pin-title {
      font-family: 'Playfair Display', serif;
      font-size: .88rem; font-weight: 600;
      color: #1a2e1a; line-height: 1.3; margin-bottom: 4px;
    }
    .pin-desc { font-size: .75rem; color: #6a7a6a; line-height: 1.5; }

    /* ══════════════════════════════
       PILLARS
    ══════════════════════════════ */
    .pillars-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px; margin-top: 2.5rem;
    }
    .pillar-card {
      border-radius: 18px; padding: 1.5rem 1.2rem;
      position: relative; overflow: hidden;
      transition: transform 0.2s;
    }
    .pillar-card:hover { transform: translateY(-3px); }
    .pillar-card.c1 { background: #eaf7ea; }
    .pillar-card.c2 { background: #e8f4fd; }
    .pillar-card.c3 { background: #fff8e6; }
    .pillar-card.c4 { background: #fde8f3; }

    .pillar-icon { font-size: 1.7rem; margin-bottom: .8rem; }
    .pillar-num {
      position: absolute; top: 12px; right: 14px;
      font-family: 'Playfair Display', serif;
      font-size: 2.4rem; font-weight: 700;
      opacity: 0.06; line-height: 1; color: #000;
    }
    .pillar-title {
      font-family: 'Playfair Display', serif;
      font-size: 1rem; font-weight: 700;
      color: #1a2e1a; margin-bottom: .4rem;
    }
    .pillar-desc { font-size: .75rem; color: #5a6a5a; line-height: 1.55; }

    /* ══════════════════════════════
       COMMUNITY BANNER (LIGHTER)
    ══════════════════════════════ */
    .community-banner {
      margin: 0 5vw 4rem;
      border-radius: 24px;
      background: linear-gradient(135deg, #4caf50 0%, #388e3c 55%, #2e7d32 100%);
      padding: 3rem 2.5rem;
      display: flex; align-items: center;
      justify-content: space-between;
      flex-wrap: wrap; gap: 1.5rem;
      position: relative; overflow: hidden;
    }
    .community-banner::before {
      content: ''; position: absolute; top: -50px; right: -50px;
      width: 260px; height: 260px; border-radius: 50%;
      background: rgba(255,255,255,0.08);
    }
    .community-banner::after {
      content: ''; position: absolute; bottom: -70px; right: 110px;
      width: 180px; height: 180px; border-radius: 50%;
      background: rgba(255,255,255,0.05);
    }
    .cb-text { position: relative; z-index: 1; }
    .cb-text h2 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.3rem, 2.5vw, 1.9rem);
      font-weight: 700; color: #fff;
      margin-bottom: .6rem; line-height: 1.25;
    }
    .cb-text p {
      font-size: .9rem; color: rgba(255,255,255,0.85);
      max-width: 400px; line-height: 1.6;
    }
    .cb-action { position: relative; z-index: 1; }
    .cb-btn {
      display: inline-flex; align-items: center; gap: 9px;
      background: #fff; color: #2e7d32;
      font-family: 'DM Sans', sans-serif;
      font-weight: 600; font-size: .88rem;
      padding: 13px 26px; border-radius: 100px;
      text-decoration: none; white-space: nowrap;
      transition: transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 4px 18px rgba(0,0,0,0.15);
    }
    .cb-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 26px rgba(0,0,0,0.2); }
  </style>
</head>
<body class="text-gray-800">

   <header class="flex justify-between items-center px-6 md:px-10 h-14 bg-white shadow relative">
    <!-- Logo -->
    <div class="flex items-center font-bold text-2xl text-gray-700">
      <img src="../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
      <img src="../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
      <span class="text-nutrigreen-500">CNO</span><span class="ml-2">NutriMap</span>
    </div>

    <!-- Desktop Nav -->
    <nav class="hidden md:flex items-center space-x-6 font-semibold">
      <a href="../index.php" class="text-nutrigreen-500">Home</a>
      <a href="map.php" class="hover:text-nutrigreen-500 transition-colors">Map</a>

      <!-- Dropdown -->
      <div class="relative">
        <button id="aboutBtn" class="flex items-center gap-1 text-gray-700 hover:text-nutrigreen-500 focus:outline-none transition-colors">
          About CNO
          <svg class="w-4 h-4 transition-transform" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
          </svg>
        </button>
        <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-white shadow-lg rounded hidden z-50">
          <a href="pages/about_us/about.php" class="block px-4 py-2 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">About</a>
          <a href="pages/about_us/profile.php" class="block px-4 py-2 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Profile</a>
          <a href="pages/about_us/vision.php" class="block px-4 py-2 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Vision</a>
          <a href="pages/about_us/mission.php" class="block px-4 py-2 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Mission</a>
        </div>
      </div>

      <a href="pages/contact_us/contact.php" class="hover:text-nutrigreen-500 transition-colors">Contact Us</a>
      <a href="../login.php" class="bg-nutrigreen-500 text-white px-4 py-2 rounded hover:bg-nutrigreen-600 transition-colors shadow-sm">Login</a>
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
      <a href="../index.php" class="px-6 py-3 border-b hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Home</a>
      <a href="map.php" class="px-6 py-3 border-b hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Map</a>

      <!-- Mobile About -->
      <div class="flex flex-col">
        <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 border-b hover:bg-gray-100 focus:outline-none transition-colors">
          About CNO
          <svg id="mobileAboutArrow" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
          </svg>
        </button>
        <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
          <a href="pages/about_us/about.php" class="px-8 py-2 hover:bg-gray-200 hover:text-nutrigreen-500 transition-colors">About</a>
          <a href="pages/about_us/profile.php" class="px-8 py-2 hover:bg-gray-200 hover:text-nutrigreen-500 transition-colors">Profile</a>
          <a href="pages/about_us/vision.php" class="px-8 py-2 hover:bg-gray-200 hover:text-nutrigreen-500 transition-colors">Vision</a>
          <a href="pages/about_us/mission.php" class="px-8 py-2 hover:bg-gray-200 hover:text-nutrigreen-500 transition-colors">Mission</a>
        </div>
      </div>

      <a href="pages/contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Contact Us</a>
      <a href="../login.php" class="px-6 py-3 hover:bg-gray-100 hover:text-nutrigreen-500 transition-colors">Login</a>
    </div>
  </header>

  <!-- ════════════════════════════════════════
       HERO (LIGHTER & BRIGHTER)
  ════════════════════════════════════════ -->
  <section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <div class="hero-eyebrow"><span></span>El Salvador, Misamis Oriental</div>
      <h1>Welcome to <em>NutriMap</em></h1>
      <p class="hero-desc">
        Your community's gateway to nutrition awareness, health monitoring, and data-driven wellness for every child in El Salvador City.
      </p>
      <a href="pages/kmau.php" class="hero-cta">
        Know More About Us
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
      </a>
    </div>

    <!-- 3-item stat bar -->
    <div class="hero-stats">
      <div class="hero-stat">
        <div class="hero-stat-num">15</div>
        <div class="hero-stat-lbl">Barangays Covered</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-num">100%</div>
        <div class="hero-stat-lbl">Free Services</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-num">City-Wide</div>
        <div class="hero-stat-lbl">Health Monitoring</div>
      </div>
    </div>
  </section>

  <!-- ════════════════════════════════════════
       SERVICES — compact pin grid
  ════════════════════════════════════════ -->
  <section class="nm-section services-bg">
    <div class="services-header">
      <div>
        <div class="nm-tag">What We Do</div>
        <h2 class="nm-title">Health &amp; Nutrition<br>For Everyone</h2>
      </div>
      <p class="nm-sub">The City Nutrition Office delivers programs that go beyond food — supporting the full health and well-being of our community.</p>
    </div>

    <div class="pin-grid">

      <div class="pin-card">
        <div class="pin-img h-l bg-leaf">
          <svg width="60" height="60" viewBox="0 0 80 80" fill="none">
            <ellipse cx="40" cy="40" r="36" fill="#c8e6c9"/>
            <path d="M20 55 Q30 20 60 25 Q35 30 40 55Z" fill="#3e9a3e" opacity=".9"/>
            <circle cx="40" cy="42" r="8" fill="#fff" opacity=".6"/>
          </svg>
        </div>
        <div class="pin-body">
          <div class="pin-tag">Nutrition</div>
          <div class="pin-title">Child Nutrition Monitoring</div>
          <div class="pin-desc">Regular weighing and assessment of Preschool and School-age children to track growth and flag malnutrition early.</div>
        </div>
      </div>

      <div class="pin-card">
        <div class="pin-img h-s bg-sky">
          <svg width="54" height="54" viewBox="0 0 70 70" fill="none">
            <rect x="10" y="18" width="50" height="36" rx="8" fill="#b2dfdb"/>
            <rect x="18" y="26" width="14" height="10" rx="2" fill="#3e9a3e"/>
            <rect x="38" y="26" width="14" height="3" rx="1.5" fill="#80cbc4"/>
            <rect x="38" y="32" width="10" height="3" rx="1.5" fill="#80cbc4"/>
            <circle cx="50" cy="54" r="10" fill="#3e9a3e"/>
            <path d="M46 54l2.5 2.5L54 49" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="pin-body">
          <div class="pin-tag">Data</div>
          <div class="pin-title">NutriMap Dashboard</div>
          <div class="pin-desc">Interactive maps visualizing nutrition data across barangays, updated every quarter.</div>
        </div>
      </div>

      <div class="pin-card">
        <div class="pin-img h-m bg-warm">
          <svg width="58" height="58" viewBox="0 0 75 75" fill="none">
            <circle cx="37" cy="30" r="18" fill="#ffe082"/>
            <circle cx="37" cy="26" r="8" fill="#fb8c00"/>
            <path d="M20 60 Q37 45 54 60" stroke="#fb8c00" stroke-width="3" stroke-linecap="round" fill="none"/>
          </svg>
        </div>
        <div class="pin-body">
          <div class="pin-tag">Education</div>
          <div class="pin-title">Nutrition Counseling</div>
          <div class="pin-desc">One-on-one and group sessions helping families build healthy habits and balanced diets.</div>
        </div>
      </div>

      <div class="pin-card">
        <div class="pin-img h-m bg-peach">
          <svg width="58" height="58" viewBox="0 0 75 75" fill="none">
            <rect x="12" y="30" width="51" height="30" rx="6" fill="#ffccbc"/>
            <rect x="22" y="18" width="31" height="18" rx="6" fill="#ff8a65"/>
            <circle cx="37" cy="27" r="6" fill="#fff" opacity=".6"/>
            <path d="M22 44h31M22 52h20" stroke="#bf360c" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="pin-body">
          <div class="pin-tag">Programs</div>
          <div class="pin-title">Supplementary Feeding & AMUMA Clinic</div>
          <div class="pin-desc">Meals and micronutrient supplements provided to at-risk children and lactating mothers.</div>
        </div>
      </div>

      <div class="pin-card">
        <div class="pin-img h-l bg-lav">
          <svg width="60" height="68" viewBox="0 0 80 90" fill="none">
            <ellipse cx="40" cy="70" rx="28" ry="10" fill="#ce93d8" opacity=".4"/>
            <path d="M40 10 L60 40 L52 40 L52 70 L28 70 L28 40 L20 40Z" fill="#9c27b0" opacity=".7"/>
            <circle cx="40" cy="10" r="8" fill="#e1bee7"/>
            <path d="M28 50h24M28 58h18" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="pin-body">
          <div class="pin-tag">Health</div>
          <div class="pin-title">Community Health Outreach</div>
          <div class="pin-desc">Mobile health teams visiting barangays to provide checkups, vitamins, and deworming.</div>
        </div>
      </div>

      <div class="pin-card">
        <div class="pin-img h-s bg-rose">
          <svg width="54" height="46" viewBox="0 0 70 60" fill="none">
            <path d="M35 50 C10 35 5 15 20 10 C28 7 35 18 35 18 C35 18 42 7 50 10 C65 15 60 35 35 50Z" fill="#f48fb1"/>
            <path d="M35 44 C16 32 12 18 24 14 C30 12 35 20 35 20 C35 20 40 12 46 14 C58 18 54 32 35 44Z" fill="#e91e63" opacity=".6"/>
          </svg>
        </div>
        <div class="pin-body">
          <div class="pin-tag">Wellness</div>
          <div class="pin-title">Maternal &amp; Child Care</div>
          <div class="pin-desc">Dedicated support for pregnant mothers, newborns, and young families throughout El Salvador City.</div>
        </div>
      </div>

    </div>
  </section>

  <!-- ════════════════════════════════════════
       PILLARS
  ════════════════════════════════════════ -->
  <section class="nm-section" style="background:#fff;">
    <div class="nm-tag">Our Approach</div>
    <h2 class="nm-title">Four Pillars of<br>Community Well-Being</h2>
    <div class="pillars-grid">
      <div class="pillar-card c1">
        <div class="pillar-num">01</div>
        <div class="pillar-icon">🥦</div>
        <div class="pillar-title">Nourishment</div>
        <div class="pillar-desc">Ensuring every child and mother has access to balanced, adequate nutrition every day.</div>
      </div>
      <div class="pillar-card c2">
        <div class="pillar-num">02</div>
        <div class="pillar-icon">🗺️</div>
        <div class="pillar-title">Data Mapping</div>
        <div class="pillar-desc">Geospatial data to guide resources where they are needed most.</div>
      </div>
      <div class="pillar-card c3">
        <div class="pillar-num">03</div>
        <div class="pillar-icon">📚</div>
        <div class="pillar-title">Education</div>
        <div class="pillar-desc">Community-based learning that empowers families to make healthy choices.</div>
      </div>
      <div class="pillar-card c4">
        <div class="pillar-num">04</div>
        <div class="pillar-icon">🤝</div>
        <div class="pillar-title">Collaboration</div>
        <div class="pillar-desc">Partnering with barangay leaders, schools, and health workers for lasting impact.</div>
      </div>
    </div>
  </section>

  <!-- ════════════════════════════════════════
       COMMUNITY BANNER
  ════════════════════════════════════════ -->
  <div class="community-banner">
    <div class="cb-text">
      <h2>Building a Healthier<br>El Salvador, Together.</h2>
      <p>The City Nutrition Office is here for every family — visit us, explore the map, or reach out to find the program nearest you.</p>
    </div>
    <div class="cb-action">
      <a href="map.php" class="cb-btn">
        Explore the Map
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
      </a>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="bg-gray-800 text-gray-300 py-10 mt-10">
    <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-5 gap-8">
      <!-- Logo -->
      <div class="md:col-span-2">
        <div class="flex items-center mb-4">
          <img src="../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2 rounded-lg" />
          <span class="text-nutrigreen-500 text-xl font-bold">CNO</span>
          <span class="text-white text-xl font-bold ml-1">NutriMap</span>
        </div>
        <p class="text-sm">A tool to visualize health and nutrition data for children in El Salvador City.</p>
      </div>

      <!-- About -->
      <div>
        <h3 class="text-white font-semibold text-lg mb-3">About Us</h3>
        <ul class="space-y-2">
          <li><a href="pages/about_us/mission.php" class="hover:text-nutrigreen-400 transition-colors">Our Mission</a></li>
          <li><a href="pages/about_us/vision.php" class="hover:text-nutrigreen-400 transition-colors">Our Vision</a></li>
        </ul>
      </div>

      <!-- Quick Links -->
      <div>
        <h3 class="text-white font-semibold text-lg mb-3">Quick Links</h3>
        <ul class="space-y-2">
          <li><a href="map.php" class="hover:text-nutrigreen-400 transition-colors">Map</a></li>
          <li><a href="pages/contact_us/contact.php" class="hover:text-nutrigreen-400 transition-colors">Contact Us</a></li>
        </ul>
      </div>

      <!-- Legal -->
      <div>
        <h3 class="text-white font-semibold text-lg mb-3">Legal & Support</h3>
        <ul class="space-y-2">
          <li><a href="pages/legal_and_support/terms.php" class="hover:text-nutrigreen-400 transition-colors">Terms of Use</a></li>
          <li><a href="pages/legal_and_support/privacy.php" class="hover:text-nutrigreen-400 transition-colors">Privacy Policy</a></li>
          <li><a href="pages/legal_and_support/cookies.php" class="hover:text-nutrigreen-400 transition-colors">Cookies</a></li>
          <li><a href="pages/help_and_support/help.php" class="hover:text-nutrigreen-400 transition-colors">Help</a></li>
          <li><a href="pages/help_and_support/faqs.php" class="hover:text-nutrigreen-400 transition-colors">FAQs</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400 text-sm">
      <p>Copyright &copy; 2025 CNO NutriMap. All Rights Reserved.<br>Developed By NBSC ICS 4th Year Student.</p>
    </div>
  </footer>


  <!-- ════════════════════════════════════════
       JS
  ════════════════════════════════════════ -->
  <script>
    const aboutBtn = document.getElementById('aboutBtn');
    const aboutDropdown = document.getElementById('aboutDropdown');
    const aboutArrow = document.getElementById('aboutArrow');
    const burgerBtn = document.getElementById('burgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileAboutBtn = document.getElementById('mobileAboutBtn');
    const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
    const mobileAboutArrow = document.getElementById('mobileAboutArrow');

    if (aboutBtn && aboutDropdown) {
      aboutBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        aboutDropdown.classList.toggle('hidden');
        if (aboutArrow) aboutArrow.style.transform = aboutDropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
      });
    }
    document.addEventListener('click', function(event) {
      if (aboutBtn && aboutDropdown && !aboutBtn.contains(event.target) && !aboutDropdown.contains(event.target)) {
        aboutDropdown.classList.add('hidden');
        if (aboutArrow) aboutArrow.style.transform = 'rotate(0deg)';
      }
    });
    if (burgerBtn && mobileMenu) {
      burgerBtn.addEventListener('click', () => { mobileMenu.classList.toggle('hidden'); });
    }
    if (mobileAboutBtn && mobileAboutDropdown) {
      mobileAboutBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        mobileAboutDropdown.classList.toggle('hidden');
        if (mobileAboutArrow) mobileAboutArrow.style.transform = mobileAboutDropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
      });
    }

    /* green reinforcement */
    const style = document.createElement('style');
    style.textContent = `
      a:focus-visible, button:focus-visible { outline: 2px solid #3e9a3e; outline-offset: 2px; border-radius: 4px; }
      .bg-nutrigreen-600 { background-color: #2e7d32; }
      .text-nutrigreen-300 { color: #7ac87a; }
      .text-nutrigreen-400 { color: #5cb85c; }
      .text-nutrigreen-500 { color: #3e9a3e !important; }
      .border-nutrigreen-500 { border-color: #3e9a3e; }
      .bg-teal-600, .bg-teal-700 { background-color: #3e9a3e !important; }
      .bg-teal-600:hover, .bg-teal-700:hover { background-color: #2e7d32 !important; }
      footer a:hover { color: #5cb85c !important; }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>