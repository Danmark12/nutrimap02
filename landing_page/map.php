<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CNO | NutriMap</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

<style>
  *, *::before, *::after { box-sizing: border-box; }

  :root {
    --green-dark:   #014d24;
    --green-mid:    #017432;
    --green-light:  #02a046;
    --green-soft:   #d1f0df;
    --green-border: #a8dfc2;
    --font-ui:      'DM Sans', sans-serif;
    --font-display: 'Space Grotesk', sans-serif;
    --gray-50:  #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-700: #374151;
    --gray-900: #111827;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.07), 0 1px 2px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
    --shadow-lg: 0 10px 30px rgba(0,0,0,0.11), 0 4px 10px rgba(0,0,0,0.06);
    --radius:    10px;
    --sidebar-w: 250px;
  }

  body {
    margin: 0;
    font-family: var(--font-ui);
    background: #eef2ef;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    color: var(--gray-900);
  }

  /* ────────────────────────────
     PAGE SHELL
  ──────────────────────────── */
  .page-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 16px 20px 24px;
    max-width: 1560px;
    width: 100%;
    margin: 0 auto;
    gap: 12px;
  }

  /* ── Title area ── */
  .page-header-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
  }

  .page-header-left { display: flex; flex-direction: column; gap: 5px; }

  .data-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--green-dark);
    background: var(--green-soft);
    border: 1px solid var(--green-border);
    border-radius: 5px;
    padding: 2px 8px;
    width: fit-content;
  }

  .data-badge .live-dot {
    width: 6px;
    height: 6px;
    background: var(--green-light);
    border-radius: 50%;
    animation: blink 2s ease-in-out infinite;
  }

  @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

.page-title {
  font-family: 'Times New Roman', Times, serif;
  font-size: 28px;
  font-weight: 600;
  color: #003215;  /* Green color to match your theme */
  margin: 0;
  max-width: 700px;
  line-height: 1.3;
}
  .source-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: var(--gray-500);
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: 7px;
    padding: 4px 11px;
    white-space: nowrap;
    align-self: flex-start;
    margin-top: 0;
  }

  .source-tag strong { color: var(--gray-700); font-weight: 600; }

  /* ────────────────────────────
     DASHBOARD LAYOUT
  ──────────────────────────── */
  .map-dashboard {
    display: flex;
    gap: 12px;
    flex: 1;
  }

  /* ── SIDEBAR - SINGLE UNIFIED CARD ── */
  .sidebar {
    width: var(--sidebar-w);
    flex-shrink: 0;
  }

  /* Single unified card */
  .control-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  /* Card sections with dividers */
  .card-section {
    padding: 12px 14px;
    border-bottom: 1px solid var(--gray-100);
  }

  .card-section:last-child {
    border-bottom: none;
  }

  .section-label {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--gray-400);
    margin-bottom: 8px;
  }

  /* Population toggle inside card */
  .population-toggle {
    display: flex;
    gap: 6px;
    background: #f3f4f6;
    padding: 3px;
    border-radius: 32px;
  }

  .population-toggle button {
    flex: 1;
    border: none;
    padding: 6px 0;
    border-radius: 28px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }

  .population-toggle button.active-toggle {
    background: var(--green-mid);
    color: white;
  }

  .population-toggle button:not(.active-toggle) {
    background: transparent;
    color: #6b7280;
  }

  /* view toggle */
  .view-toggle {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5px;
  }

  .view-toggle button {
    border: 1.5px solid var(--gray-200);
    background: var(--gray-50);
    color: var(--gray-500);
    font-family: var(--font-ui);
    font-size: 11px;
    font-weight: 500;
    padding: 6px 0;
    border-radius: 7px;
    cursor: pointer;
    transition: all 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
  }

  .view-toggle button:hover {
    border-color: var(--gray-300);
    background: #fff;
    color: var(--gray-900);
  }

  .view-toggle button.active {
    background: var(--green-mid);
    border-color: var(--green-mid);
    color: #fff;
    box-shadow: 0 2px 6px rgba(1,116,50,0.2);
  }

  /* barangay select */
  .styled-select {
    width: 100%;
    padding: 7px 30px 7px 10px;
    border: 1.5px solid var(--gray-200);
    border-radius: 7px;
    font-family: var(--font-ui);
    font-size: 12px;
    color: var(--gray-900);
    background: var(--gray-50);
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5' stroke-linecap='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s;
  }

  .styled-select:focus {
    outline: none;
    border-color: var(--green-mid);
    background-color: #fff;
  }

  /* year timeline */
  .year-labels {
    display: flex;
    justify-content: space-between;
    font-size: 9px;
    color: var(--gray-400);
    margin-bottom: 6px;
    font-weight: 500;
  }

  .timeline-track-outer {
    position: relative;
    height: 5px;
    background: var(--gray-200);
    border-radius: 99px;
    cursor: pointer;
    margin-bottom: 2px;
  }

  #timelineFill {
    position: absolute;
    height: 5px;
    background: linear-gradient(90deg, var(--green-mid), var(--green-light));
    border-radius: 99px;
    pointer-events: none;
  }

  .timeline-handle {
    position: absolute;
    width: 14px;
    height: 14px;
    background: #fff;
    border: 2.5px solid var(--green-mid);
    border-radius: 50%;
    top: -4.5px;
    cursor: grab;
    box-shadow: 0 1px 5px rgba(0,0,0,0.15);
    transition: transform 0.1s, box-shadow 0.1s;
  }

  .timeline-handle:hover {
    transform: scale(1.2);
    box-shadow: 0 2px 8px rgba(1,116,50,0.3);
  }

  /* legend list */
  .legend-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .legend-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 8px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 500;
    color: var(--gray-600);
    transition: background 0.12s, border-color 0.12s;
    border: 1px solid transparent;
    user-select: none;
  }

  .legend-list li:hover { background: var(--gray-50); }

  .legend-list li.active {
    background: var(--gray-50);
    border-color: var(--gray-200);
    color: var(--gray-900);
    font-weight: 600;
  }

  .legend-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    flex-shrink: 0;
    border: 2px solid rgba(255,255,255,0.6);
    box-shadow: 0 0 0 1.5px rgba(0,0,0,0.1);
  }

  /* ────────────────────────────
     MAP CARD
  ──────────────────────────── */
  .map-card {
    flex: 1;
    min-width: 0;
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
  }

  .map-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 14px;
    border-bottom: 1px solid var(--gray-100);
    flex-shrink: 0;
    background: var(--gray-50);
  }

  .map-card-title {
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-700);
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .live-dot-sm {
    width: 7px;
    height: 7px;
    background: #22c55e;
    border-radius: 50%;
    animation: blink 2s ease-in-out infinite;
    flex-shrink: 0;
  }

  .map-meta {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    color: var(--gray-400);
  }

  /* MAP + CHART - SAME SIZE */
#mapContainer {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 400px;
  height: 400px;
}

#map {
  flex: 1;
  width: 100%;
  height: 400px;
  min-height: auto;
}

#chartContainer {
  display: none;
  flex: 1;
  padding: 0;
  min-height: 400px;
  height: 400px;
  width: 100%;
}

#fullChartCanvas {
  width: 100% !important;
  height: 400px !important;
  max-height: 400px;
}

#chartContainer canvas {
  width: 100% !important;
  height: 400px !important;
  max-height: 400px;
}

  #chartContainer canvas {
    width: 100% !important;
    max-height: 100%;
  }

  /* gradient bar */
  .gradient-bar-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 14px;
    border-top: 1px solid var(--gray-100);
    flex-shrink: 0;
    background: var(--gray-50);
  }

  .gradient-grid {
    display: grid;
    grid-template-columns: repeat(11, 1fr);
    gap: 2px;
    flex: 1;
  }

  .gradient-cell {
    height: 14px;
    border-radius: 3px;
    cursor: pointer;
    transition: transform 0.1s, outline 0.1s;
  }

  .gradient-cell:hover { transform: scaleY(1.25); }
.active-gradient-cell { outline: 1px solid #107f02; transform: scaleY(1.3); z-index: 10; position: relative; }
  /* tooltip */
/* tooltip - smaller floating card */
/* tooltip - smaller floating card for both bar and line charts */
#chart-tooltip {
  display: none;
  position: absolute;
  z-index: 1000;
  background: rgba(255,255,255,0.97);
  padding: 4px 6px;
  border-radius: 6px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--gray-200);
  max-width: 180px;
  max-height: 230px;
  overflow-y: auto;
  pointer-events: none;
  flex-direction: column;
  align-items: stretch;
  font-size: 10px;
}

#chart-tooltip canvas {
  width: 100% !important;
  height: auto !important;
  max-height: 100px;
}

/* Line chart specific adjustments in tooltip */
#chart-tooltip .line-chart-wrapper {
  width: 160px;
  height: 100px;
}

#chart-tooltip .bar-chart-wrapper {
  width: 130px;
  height: 85px;
}

/* Chart wrapper sizing */
.tooltip-chart-wrapper {
  margin-top: 2px;
  margin-bottom: 2px;
}

.tooltip-chart-wrapper canvas {
  width: 100% !important;
  height: 100% !important;
}

  #chart-tooltip canvas { width: 100% !important; height: auto !important; }

  @media (max-width: 768px) {
    #chart-tooltip {
      position: fixed;
      bottom: 10px;
      left: 10px;
      right: 10px;
      max-width: calc(100vw - 20px);
      max-height: 250px;
    }
  }

 

  /* RESPONSIVE */
  @media (max-width: 960px) {
    .map-dashboard {
      flex-direction: column;
    }
    .sidebar {
      width: 100%;
    }
    #map { min-height: 380px; }
    #chartContainer { min-height: 380px; }
    .footer-grid { grid-template-columns: 1fr 1fr; }
  }

  @media (max-width: 580px) {
    .footer-grid { grid-template-columns: 1fr; }
    .page-header-row { flex-direction: column; }
    .page-title { font-size: 16px; }
  }

  .leaflet-interactive {
    transition: all 0.15s ease;
  }

  /* Footer green hover color */

</style>
</head>

<body class="bg-gray-50 font-sans flex flex-col min-h-screen">

<!-- HEADER -->
<header class="flex justify-between items-center px-6 md:px-10 h-14 bg-white shadow relative">
  <div class="flex items-center font-bold text-2xl text-gray-700">
    <img src="../img/CNO_Logo.png" alt="CNO NutriMap Logo" class="h-10 mr-2">
    <img src="../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="NutriMap Logo" class="h-8 mr-2">
    <span class="text-green-600">CNO</span><span class="ml-2">NutriMap</span>
  </div>

  <nav class="hidden md:flex items-center space-x-6 font-semibold">
    <a href="../index.php" class="hover:text-green-600">Home</a>
    <a href="map.php" class="text-green-600">Map</a>
    <div class="relative">
      <button id="aboutBtn" class="flex items-center gap-1 text-gray-700 hover:text-green-600 focus:outline-none">
        About CNO
        <svg class="w-4 h-4 transition-transform" id="aboutArrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
        </svg>
      </button>
      <div id="aboutDropdown" class="absolute left-0 mt-2 w-40 bg-white shadow-lg rounded hidden z-50">
        <a href="pages/about_us/about.php" class="block px-4 py-2 hover:bg-gray-100">About</a>
        <a href="pages/about_us/profile.php" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
        <a href="pages/about_us/vision.php" class="block px-4 py-2 hover:bg-gray-100">Vision</a>
        <a href="pages/about_us/mission.php" class="block px-4 py-2 hover:bg-gray-100">Mission</a>
      </div>
    </div>
    <a href="pages/contact_us/contact.php" class="hover:text-green-600">Contact Us</a>
    <a href="../login.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Login</a>
  </nav>

  <div class="md:hidden flex items-center">
    <button id="burgerBtn" class="text-gray-700 focus:outline-none">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </div>

  <div id="mobileMenu" class="hidden absolute top-full left-0 w-full bg-white shadow-md z-20 flex flex-col">
    <a href="../index.php" class="px-6 py-3 border-b hover:bg-gray-100">Home</a>
    <a href="map.php" class="px-6 py-3 border-b hover:bg-gray-100">Map</a>
    <div class="flex flex-col">
      <button id="mobileAboutBtn" class="flex justify-between items-center px-6 py-3 border-b hover:bg-gray-100 focus:outline-none">
        About CNO
        <svg id="mobileAboutArrow" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6z"/>
        </svg>
      </button>
      <div id="mobileAboutDropdown" class="hidden flex flex-col bg-gray-50">
        <a href="pages/about_us/about.php" class="px-8 py-2 hover:bg-gray-200">About</a>
        <a href="pages/about_us/profile.php" class="px-8 py-2 hover:bg-gray-200">Profile</a>
        <a href="pages/about_us/vision.php" class="px-8 py-2 hover:bg-gray-200">Vision</a>
        <a href="pages/about_us/mission.php" class="px-8 py-2 hover:bg-gray-200">Mission</a>
      </div>
    </div>
    <a href="pages/contact_us/contact.php" class="px-6 py-3 border-b hover:bg-gray-100">Contact Us</a>
    <a href="../login.php" class="px-6 py-3 hover:bg-gray-100">Login</a>
  </div>
</header>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const burgerBtn = document.getElementById('burgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    burgerBtn?.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));

    const mobileAboutBtn = document.getElementById('mobileAboutBtn');
    const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
    const mobileAboutArrow = document.getElementById('mobileAboutArrow');
    mobileAboutBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      mobileAboutDropdown.classList.toggle('hidden');
      mobileAboutArrow.classList.toggle('rotate-180');
    });

    const aboutBtn = document.getElementById('aboutBtn');
    const aboutDropdown = document.getElementById('aboutDropdown');
    const aboutArrow = document.getElementById('aboutArrow');
    aboutBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      aboutDropdown.classList.toggle('hidden');
      aboutArrow.classList.toggle('rotate-180');
    });

    document.addEventListener('click', (e) => {
      if (!mobileMenu?.contains(e.target)) {
        mobileAboutDropdown?.classList.add('hidden');
        mobileAboutArrow?.classList.remove('rotate-180');
      }
      if (!aboutDropdown?.contains(e.target)) {
        aboutDropdown?.classList.add('hidden');
        aboutArrow?.classList.remove('rotate-180');
      }
    });

    const btnMap = document.getElementById('btnMapView');
    const btnChart = document.getElementById('btnChartView');
    const mapCont = document.getElementById('mapContainer');
    const chartCont = document.getElementById('chartContainer');

    btnMap?.addEventListener('click', () => {
      btnMap.classList.add('active');
      btnChart.classList.remove('active');
      mapCont.style.display = 'flex';
      chartCont.style.display = 'none';
    });

    btnChart?.addEventListener('click', () => {
      btnChart.classList.add('active');
      btnMap.classList.remove('active');
      mapCont.style.display = 'none';
      chartCont.style.display = 'block';
    });

    document.querySelectorAll('#legend-buttons li').forEach(li => {
      li.addEventListener('click', () => {
        document.querySelectorAll('#legend-buttons li').forEach(el => el.classList.remove('active'));
        li.classList.add('active');
      });
    });
  });
</script>

<!-- ═══ PAGE CONTENT ═══ -->
<div class="page-wrap">

  <!-- Title Row -->
  <div class="page-header-row">
    <div class="page-header-left">
      <div class="data-badge">
        <span class="live-dot"></span>
    Data
      </div>
      <h1 class="page-title">
        El Salvador City — Nutritional status preschool 0–59 months and school age children
      </h1>
    </div>
    <div class="source-tag">
      <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" d="M9 17H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v6"/>
        <path stroke-linecap="round" d="M13 17l4 4 4-4m-4 4V13"/>
      </svg>
      <strong>Source:</strong> Operation Timbang Plus
    </div>
  </div>

  <!-- Dashboard -->
  <div class="map-dashboard">

    <!-- ── LEFT SIDEBAR - SINGLE UNIFIED CARD ── -->
    <aside class="sidebar">
      <div class="control-card">

        <!-- Section 1: Population Toggle -->
        <div class="card-section">
          <div class="population-toggle">
            <button id="preschoolBtn" class="active-toggle">Preschool</button>
            <button id="schoolBtn">School Age</button>
          </div>
        </div>

        <!-- Section 2: View Mode -->
        <div class="card-section">
          <div class="section-label">VIEW MODE</div>
          <div class="view-toggle">
            <button id="btnMapView" class="active">
              <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <path d="M3 9h18M9 21V9"/>
              </svg>
              Map
            </button>
            <button id="btnChartView">
              <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 3v18h18"/>
                <path d="M7 16l4-5 4 3 4-6"/>
              </svg>
              Chart
            </button>
          </div>
        </div>

        <!-- Section 3: Year Range -->
        <div class="card-section">
          <div class="section-label">YEAR RANGE</div>
          <div class="year-labels" id="yearLabels"></div>
          <div class="timeline-track-outer" id="timelineTrack">
            <div id="timelineFill"></div>
            <div class="timeline-handle" id="timelineHandleLeft" style="left:0%"></div>
            <div class="timeline-handle" id="timelineHandleRight" style="left:100%"></div>
          </div>
        </div>

        <!-- Section 4: Barangay -->
        <div class="card-section">
          <div class="section-label">BARANGAY</div>
          <select id="barangayFilter" class="styled-select">
            <option value="All">All Barangays</option>
            <option value="Amoros">Amoros</option>
            <option value="Bolisong">Bolisong</option>
            <option value="Himaya">Himaya</option>
            <option value="Hinigdaan">Hinigdaan</option>
            <option value="Kalabaylabay">Kalabaylabay</option>
            <option value="Molugan">Molugan</option>
            <option value="Bolobolo">Bolobolo</option>
            <option value="Poblacion">Poblacion</option>
            <option value="Kibonbon">Kibonbon</option>
            <option value="Sambulawan">Sambulawan</option>
            <option value="Calongonan">Calongonan</option>
            <option value="Sinaloc">Sinaloc</option>
            <option value="Taytay">Taytay</option>
            <option value="Ulaliman">Ulaliman</option>
            <option value="Cogon">Cogon</option>
          </select>
        </div>

        <!-- Section 5: Indicators / Legend -->
        <div class="card-section">
          <div class="section-label">INDICATORS</div>
          <ul class="legend-list" id="legend-buttons">
            <li data-field="ALL" data-label="All Indicators" data-color="#888888" class="active">
              <span class="legend-dot" style="background:#888888"></span>
              All Indicators
            </li>
            <li data-field="UNDERWEIGHT" data-label="Underweight" data-color="#d4a800">
              <span class="legend-dot" style="background:#d4a800"></span>
              Underweight
            </li>
            <li data-field="WASTED" data-label="Wasted" data-color="#F97316">
              <span class="legend-dot" style="background:#F97316"></span>
              Wasted
            </li>
            <li data-field="OVERWEIGHT_OBESE" data-label="Overweight/Obese" data-color="#3B82F6">
              <span class="legend-dot" style="background:#3B82F6"></span>
              Overweight / Obese
            </li>
            <li data-field="STUNTED" data-label="Stunted" data-color="#EF4444">
              <span class="legend-dot" style="background:#EF4444"></span>
              Stunted
            </li>
          </ul>
        </div>

      </div>
    </aside>

    <!-- ── MAP CARD ── -->
    <div class="map-card">

      <div class="map-card-header">
        <div class="map-card-title">
          <span class="live-dot-sm"></span>
          El Salvador City, Misamis Oriental
        </div>
        <div class="map-meta">
          <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            <circle cx="12" cy="9" r="2.5"/>
          </svg>
          15 Barangays
        </div>
      </div>

      <div id="chart-tooltip"></div>

      <div id="mapContainer">
        <div id="map"></div>
      </div>

      <div id="chartContainer">
        <canvas id="fullChartCanvas"></canvas>
      </div>

      <div class="gradient-bar-wrap" id="gradient-wrapper">
        <div class="gradient-grid" id="gradient-grid"></div>
      </div>

    </div>

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


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="js/map.js"></script>

</body>
</html>