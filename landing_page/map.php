<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>CNO NutriMap | El Salvador Nutrition Dashboard</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <style>
    /* ===== DESIGN SYSTEM ===== */
    :root {
      --primary: #017432;
      --primary-dark: #005a28;
      --primary-light: #e8f5e9;
      --primary-glow: rgba(1, 116, 50, 0.1);
      --secondary: #0891b2;
      --secondary-light: #ecfdf5;
      --warning: #f59e0b;
      --danger: #ef4444;
      --info: #3b82f6;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-800: #1f2937;
      --gray-900: #111827;
      --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
      --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
      --radius-sm: 0.5rem;
      --radius-md: 0.75rem;
      --radius-lg: 1rem;
      --radius-xl: 1.5rem;
      --transition-fast: 150ms ease;
      --transition-base: 250ms ease;
      --transition-slow: 350ms ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, var(--gray-50) 0%, #ffffff 100%);
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      min-height: 100vh;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: var(--gray-100);
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
      background: var(--gray-400);
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: var(--gray-500);
    }

    /* ===== HEADER STYLES ===== */
    .header {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      box-shadow: var(--shadow-sm);
      position: sticky;
      top: 0;
      z-index: 100;
      transition: var(--transition-base);
    }

    .header:hover {
      box-shadow: var(--shadow-md);
    }

    /* ===== MAP STYLES ===== */
    #map {
      height: 450px;
      width: 100%;
      border-radius: var(--radius-lg);
      transition: var(--transition-base);
    }

    #mapContainer {
      position: relative;
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-lg);
      transition: var(--transition-base);
      background: var(--gray-100);
    }

    #mapContainer:hover {
      box-shadow: var(--shadow-xl);
      transform: translateY(-2px);
    }

    /* Map Controls Customization */
    .leaflet-control-zoom {
      border: none !important;
      box-shadow: var(--shadow-md) !important;
      border-radius: var(--radius-md) !important;
      overflow: hidden;
    }

    .leaflet-control-zoom a {
      background: white !important;
      color: var(--gray-700) !important;
      transition: var(--transition-fast) !important;
    }

    .leaflet-control-zoom a:hover {
      background: var(--primary-light) !important;
      color: var(--primary) !important;
    }

    /* ===== CHART CONTAINER ===== */
    #chartContainer {
      background: white;
      border-radius: var(--radius-lg);
      padding: 1.5rem;
      box-shadow: var(--shadow-lg);
      transition: var(--transition-base);
      min-height: 450px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    #fullChartCanvas {
      max-height: 400px;
      width: 100% !important;
    }

    /* ===== VIEW TOGGLE BUTTONS ===== */
    .view-toggle-container {
      display: inline-flex;
      background: var(--gray-100);
      padding: 0.25rem;
      border-radius: 2rem;
      gap: 0.25rem;
    }

    .view-toggle-btn {
      padding: 0.5rem 1.5rem;
      border-radius: 1.5rem;
      font-weight: 500;
      font-size: 0.875rem;
      transition: var(--transition-fast);
      cursor: pointer;
      border: none;
      background: transparent;
      color: var(--gray-600);
    }

    .view-toggle-btn i {
      margin-right: 0.5rem;
    }

    .view-toggle-btn.active {
      background: var(--primary);
      color: white;
      box-shadow: var(--shadow-sm);
    }

    .view-toggle-btn:not(.active):hover {
      background: white;
      color: var(--gray-900);
    }

    /* ===== FORM CONTROLS ===== */
    .form-select, .form-control {
      border: 1.5px solid var(--gray-200);
      border-radius: var(--radius-md);
      padding: 0.5rem 2rem 0.5rem 1rem;
      font-size: 0.875rem;
      transition: var(--transition-fast);
      background: white;
      cursor: pointer;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
      background-position: right 0.5rem center;
      background-repeat: no-repeat;
      background-size: 1.25rem;
    }

    .form-select:focus, .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px var(--primary-glow);
    }

    /* ===== LEGEND SIDEBAR ===== */
    #legend-buttons {
      background: white;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-md);
      padding: 1.25rem;
      position: sticky;
      top: 5rem;
      transition: var(--transition-base);
    }

    .legend-item {
      padding: 0.75rem;
      border-radius: var(--radius-md);
      transition: var(--transition-fast);
      cursor: pointer;
      margin-bottom: 0.5rem;
    }

    .legend-item:hover {
      background: var(--gray-50);
      transform: translateX(4px);
    }

    .legend-item.active {
      background: var(--primary-light);
      border-left: 3px solid var(--primary);
    }

    .legend-color {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 0.75rem;
    }

    /* Progress bar for indicators */
    .progress-bar {
      height: 4px;
      background: var(--gray-200);
      border-radius: 2px;
      overflow: hidden;
      margin-top: 0.5rem;
    }

    .progress-fill {
      height: 100%;
      border-radius: 2px;
      transition: width 0.5s ease;
    }

    /* ===== GRADIENT LEGEND ===== */
    .gradient-wrapper {
      background: var(--gray-50);
      border-radius: var(--radius-md);
      padding: 1rem;
      margin-top: 1.5rem;
    }

    .gradient-grid {
      display: flex;
      gap: 0;
      border-radius: 0.5rem;
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      margin: 0.75rem 0;
    }

    .gradient-cell {
      height: 32px;
      flex: 1;
      transition: var(--transition-fast);
      cursor: pointer;
    }

    .gradient-cell:hover {
      transform: scale(1.02);
      filter: brightness(0.95);
    }

    .active-gradient-cell {
      outline: 3px solid var(--gray-900);
      outline-offset: 2px;
      z-index: 1;
    }

    /* ===== TOOLTIP ===== */
    #chart-tooltip {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      background: white;
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-xl);
      padding: 1rem;
      max-width: 320px;
      z-index: 1000;
      border: 1px solid var(--gray-200);
      display: none;
      animation: slideIn 0.2s ease;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* ===== STATS CARDS ===== */
    .stat-card {
      background: white;
      border-radius: var(--radius-md);
      padding: 0.75rem 1rem;
      box-shadow: var(--shadow-sm);
      transition: var(--transition-fast);
      border: 1px solid var(--gray-200);
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    /* ===== DATA SOURCE ===== */
    .data-source {
      background: var(--gray-50);
      border-left: 3px solid var(--primary);
      padding: 0.75rem 1rem;
      border-radius: var(--radius-md);
      margin-top: 1.5rem;
      font-size: 0.75rem;
      color: var(--gray-600);
    }

    /* ===== LOADING STATE ===== */
    .loading-overlay {
      position: absolute;
      inset: 0;
      background: rgba(255, 255, 255, 0.9);
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: var(--radius-lg);
      z-index: 10;
    }

    .loading-spinner {
      width: 40px;
      height: 40px;
      border: 3px solid var(--gray-200);
      border-top-color: var(--primary);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 1024px) {
      #map {
        height: 400px;
      }
      
      #legend-buttons {
        position: relative;
        top: 0;
        margin-top: 1.5rem;
      }
    }

    @media (max-width: 768px) {
      .view-toggle-container {
        width: 100%;
        justify-content: stretch;
      }
      
      .view-toggle-btn {
        flex: 1;
        text-align: center;
        padding: 0.5rem 1rem;
      }
      
      #map {
        height: 350px;
      }
      
      .gradient-cell {
        height: 24px;
      }
      
      .stat-card {
        padding: 0.5rem 0.75rem;
      }
      
      .stat-card .text-2xl {
        font-size: 1.25rem;
      }
      
      #chart-tooltip {
        bottom: 1rem;
        right: 1rem;
        left: 1rem;
        max-width: none;
      }
    }

    @media (max-width: 640px) {
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
      }
      
      .flex.justify-between.items-center.flex-wrap {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
      }
      
      .controls-group {
        flex-direction: column;
        width: 100%;
      }
      
      .controls-group select {
        width: 100%;
      }
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fade-in-up {
      animation: fadeInUp 0.5s ease forwards;
    }

    /* Breadcrumb */
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      color: var(--gray-500);
      margin-bottom: 1.5rem;
    }

    .breadcrumb a {
      color: var(--gray-500);
      text-decoration: none;
      transition: var(--transition-fast);
    }

    .breadcrumb a:hover {
      color: var(--primary);
    }

    /* Footer */
    .footer {
      background: var(--gray-900);
      color: var(--gray-400);
      margin-top: 3rem;
      padding: 3rem 0 1.5rem;
    }

    /* Mobile Menu */
    #mobileMenu {
      transition: var(--transition-base);
    }
  </style>
</head>

<body class="antialiased">
  <!-- HEADER -->
  <header class="header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center space-x-3">
          <img src="../img/CNO_Logo.png" alt="CNO Logo" class="h-10 w-10 object-contain">
          <div class="h-8 w-px bg-gray-300"></div>
          <div>
            <span class="text-primary font-bold text-xl">CNO</span>
            <span class="text-gray-700 font-semibold text-xl ml-1">NutriMap</span>
          </div>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <a href="../index.php" class="text-gray-600 hover:text-primary transition-colors">Home</a>
          <a href="map.php" class="text-primary font-semibold">Map</a>
          
          <div class="relative">
            <button id="aboutBtn" class="flex items-center gap-1 text-gray-600 hover:text-primary transition-colors">
              About CNO
              <i class="fas fa-chevron-down text-xs transition-transform" id="aboutArrow"></i>
            </button>
            <div id="aboutDropdown" class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden z-50 overflow-hidden">
              <a href="pages/about_us/about.php" class="block px-4 py-2 hover:bg-gray-50 transition-colors">About</a>
              <a href="pages/about_us/profile.php" class="block px-4 py-2 hover:bg-gray-50 transition-colors">Profile</a>
              <a href="pages/about_us/vision.php" class="block px-4 py-2 hover:bg-gray-50 transition-colors">Vision</a>
              <a href="pages/about_us/mission.php" class="block px-4 py-2 hover:bg-gray-50 transition-colors">Mission</a>
            </div>
          </div>
          
          <a href="pages/contact_us/contact.php" class="text-gray-600 hover:text-primary transition-colors">Contact Us</a>
          <a href="../login.php" class="bg-primary text-white px-5 py-2 rounded-lg hover:bg-primary-dark transition-all shadow-sm hover:shadow-md">Login</a>
        </nav>

        <!-- Mobile Menu Button -->
        <button id="burgerBtn" class="md:hidden text-gray-600">
          <i class="fas fa-bars text-xl"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-100">
      <div class="px-4 py-2 space-y-1">
        <a href="../index.php" class="block py-2 text-gray-600 hover:text-primary">Home</a>
        <a href="map.php" class="block py-2 text-primary font-semibold">Map</a>
        
        <button id="mobileAboutBtn" class="flex justify-between items-center w-full py-2 text-gray-600">
          About CNO
          <i class="fas fa-chevron-down text-xs transition-transform" id="mobileAboutArrow"></i>
        </button>
        <div id="mobileAboutDropdown" class="hidden pl-4 space-y-1">
          <a href="pages/about_us/about.php" class="block py-2 text-gray-500">About</a>
          <a href="pages/about_us/profile.php" class="block py-2 text-gray-500">Profile</a>
          <a href="pages/about_us/vision.php" class="block py-2 text-gray-500">Vision</a>
          <a href="pages/about_us/mission.php" class="block py-2 text-gray-500">Mission</a>
        </div>
        
        <a href="pages/contact_us/contact.php" class="block py-2 text-gray-600">Contact Us</a>
        <a href="../login.php" class="block py-2 text-primary font-semibold">Login</a>
      </div>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Breadcrumb -->
    <div class="breadcrumb animate-fade-in-up">
      <a href="../index.php">Home</a>
      <i class="fas fa-chevron-right text-xs"></i>
      <span class="text-gray-700 font-medium">Nutrition Map</span>
    </div>

    <!-- Header Section -->
    <div class="mb-8 animate-fade-in-up" style="animation-delay: 0.05s">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
            El Salvador Health & Nutrition Map
          </h1>
          <p class="text-gray-500 flex items-center gap-2">
            <i class="fas fa-children"></i>
            Children 0-59 months • Operation Timbang Plus Data
          </p>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-3">
          <div class="stat-card">
            <div class="text-2xl font-bold text-primary">1,234</div>
            <div class="text-xs text-gray-500">Children Measured</div>
          </div>
          <div class="stat-card">
            <div class="text-2xl font-bold text-secondary">15</div>
            <div class="text-xs text-gray-500">Barangays</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid lg:grid-cols-12 gap-6">
      <!-- Left Column: Map/Chart (8 columns on desktop) -->
      <div class="lg:col-span-8 space-y-4">
        <!-- Toolbar -->
        <div class="flex flex-wrap justify-between items-center gap-4">
          <div class="view-toggle-container">
            <button id="btnMapView" class="view-toggle-btn active">
              <i class="fas fa-map"></i> Map View
            </button>
            <button id="btnChartView" class="view-toggle-btn">
              <i class="fas fa-chart-bar"></i> Chart View
            </button>
          </div>
          
          <div class="controls-group flex gap-3">
            <select id="yearFilter" class="form-select">
              <option value="2024">2024</option>
              <option value="2023">2023</option>
              <option value="2022">2022</option>
            </select>
            <select id="barangayFilter" class="form-select">
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
        </div>

        <!-- Map Container -->
        <div id="mapContainer" class="relative">
          <div id="map"></div>
        </div>

        <!-- Chart Container (hidden by default) -->
        <div id="chartContainer" class="hidden">
          <canvas id="fullChartCanvas"></canvas>
        </div>

        <!-- Gradient Legend -->
        <div class="gradient-wrapper">
          <div class="flex justify-between items-center mb-2">
            <label class="text-sm font-medium text-gray-700">
              <i class="fas fa-chart-line text-primary mr-1"></i> Prevalence Rate
            </label>
            <span class="text-xs text-gray-400">Click on gradient for details</span>
          </div>
          <div class="gradient-grid" id="gradient-grid"></div>
          <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span>0%</span>
            <span>10%</span>
            <span>20%</span>
            <span>30%</span>
            <span>40%</span>
            <span>50%+</span>
          </div>
        </div>
      </div>

      <!-- Right Column: Legend Sidebar (4 columns on desktop) -->
      <div class="lg:col-span-4">
        <div id="legend-buttons">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">
              <i class="fas fa-chart-pie text-primary mr-2"></i>
              Nutrition Indicators
            </h3>
            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">2024</span>
          </div>
          
          <div class="space-y-2">
            <!-- Underweight -->
            <div class="legend-item" data-field="UNDERWEIGHT" data-label="Underweight">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <span class="legend-color" style="background: #eab308"></span>
                  <span class="text-sm font-medium">Underweight</span>
                </div>
                <span class="text-sm font-semibold text-gray-700">8.2%</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 8.2%; background: #eab308"></div>
              </div>
            </div>
            
            <!-- Wasted -->
            <div class="legend-item" data-field="WASTED" data-label="Wasted">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <span class="legend-color" style="background: #f97316"></span>
                  <span class="text-sm font-medium">Wasted</span>
                </div>
                <span class="text-sm font-semibold text-gray-700">5.7%</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 5.7%; background: #f97316"></div>
              </div>
            </div>
            
            <!-- Overweight/Obese -->
            <div class="legend-item" data-field="OVERWEIGHT_OBESE" data-label="Overweight/Obese">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <span class="legend-color" style="background: #3b82f6"></span>
                  <span class="text-sm font-medium">Overweight/Obese</span>
                </div>
                <span class="text-sm font-semibold text-gray-700">4.1%</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 4.1%; background: #3b82f6"></div>
              </div>
            </div>
            
            <!-- Stunted -->
            <div class="legend-item" data-field="STUNTED" data-label="Stunted">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <span class="legend-color" style="background: #ef4444"></span>
                  <span class="text-sm font-medium">Stunted</span>
                </div>
                <span class="text-sm font-semibold text-gray-700">12.3%</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: 12.3%; background: #ef4444"></div>
              </div>
            </div>
          </div>
          
          <!-- Insights Card -->
          <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="bg-gradient-to-r from-primary-light to-secondary-light rounded-lg p-4">
              <div class="flex items-start gap-3">
                <i class="fas fa-lightbulb text-primary text-lg mt-0.5"></i>
                <div>
                  <h4 class="text-sm font-semibold text-gray-800 mb-1">Key Insight</h4>
                  <p class="text-xs text-gray-600 leading-relaxed">
                    Stunting rate decreased by 5% compared to last year. Continued nutrition programs are showing positive results.
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Download Section -->
          <div class="mt-4">
            <button class="w-full py-2 text-sm text-gray-600 hover:text-primary transition-colors flex items-center justify-center gap-2">
              <i class="fas fa-download"></i> Export Report
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Source -->
    <div class="data-source">
      <div class="flex items-center gap-2 flex-wrap">
        <i class="fas fa-database text-primary"></i>
        <strong>Data Source:</strong> 
        <span>Operation Timbang Plus (OPT+) • National Nutrition Council</span>
        <span class="text-gray-400 text-xs">• Last updated: March 2025</span>
      </div>
    </div>
  </main>

  <!-- Tooltip (hidden by default) -->
  <div id="chart-tooltip"></div>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
        <div class="md:col-span-2">
          <div class="flex items-center mb-4">
            <img src="../img/CNO_Logo.png" alt="CNO Logo" class="h-10 mr-3 rounded-lg">
            <div>
              <span class="text-primary font-bold text-xl">CNO</span>
              <span class="text-white font-semibold text-xl ml-1">NutriMap</span>
            </div>
          </div>
          <p class="text-sm text-gray-400 leading-relaxed">
            A comprehensive tool to visualize health and nutrition data for children in El Salvador City, helping policymakers make data-driven decisions.
          </p>
        </div>
        
        <div>
          <h3 class="text-white font-semibold mb-4">About Us</h3>
          <ul class="space-y-2 text-sm">
            <li><a href="pages/about_us/mission.php" class="text-gray-400 hover:text-primary transition-colors">Our Mission</a></li>
            <li><a href="pages/about_us/vision.php" class="text-gray-400 hover:text-primary transition-colors">Our Vision</a></li>
            <li><a href="pages/about_us/profile.php" class="text-gray-400 hover:text-primary transition-colors">Our Team</a></li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-white font-semibold mb-4">Quick Links</h3>
          <ul class="space-y-2 text-sm">
            <li><a href="map.php" class="text-gray-400 hover:text-primary transition-colors">Interactive Map</a></li>
            <li><a href="pages/contact_us/contact.php" class="text-gray-400 hover:text-primary transition-colors">Contact Us</a></li>
            <li><a href="../index.php" class="text-gray-400 hover:text-primary transition-colors">Dashboard</a></li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-white font-semibold mb-4">Legal</h3>
          <ul class="space-y-2 text-sm">
            <li><a href="pages/legal_and_support/terms.php" class="text-gray-400 hover:text-primary transition-colors">Terms of Use</a></li>
            <li><a href="pages/legal_and_support/privacy.php" class="text-gray-400 hover:text-primary transition-colors">Privacy Policy</a></li>
            <li><a href="pages/legal_and_support/cookies.php" class="text-gray-400 hover:text-primary transition-colors">Cookies</a></li>
          </ul>
        </div>
      </div>
      
      <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-500 text-sm">
        <p>Copyright &copy; 2025 CNO NutriMap. All Rights Reserved. Developed by NBSC ICS 4th Year Students.</p>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
  
  <script>
    // ===== DOM Ready =====
    document.addEventListener('DOMContentLoaded', () => {
      // Mobile Menu Toggle
      const burgerBtn = document.getElementById('burgerBtn');
      const mobileMenu = document.getElementById('mobileMenu');
      if (burgerBtn && mobileMenu) {
        burgerBtn.addEventListener('click', () => {
          mobileMenu.classList.toggle('hidden');
        });
      }
      
      // Mobile About Dropdown
      const mobileAboutBtn = document.getElementById('mobileAboutBtn');
      const mobileAboutDropdown = document.getElementById('mobileAboutDropdown');
      const mobileAboutArrow = document.getElementById('mobileAboutArrow');
      if (mobileAboutBtn) {
        mobileAboutBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          mobileAboutDropdown.classList.toggle('hidden');
          mobileAboutArrow.classList.toggle('rotate-180');
        });
      }
      
      // Desktop About Dropdown
      const aboutBtn = document.getElementById('aboutBtn');
      const aboutDropdown = document.getElementById('aboutDropdown');
      const aboutArrow = document.getElementById('aboutArrow');
      if (aboutBtn && aboutDropdown) {
        aboutBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          aboutDropdown.classList.toggle('hidden');
          aboutArrow.classList.toggle('rotate-180');
        });
      }
      
      // Close dropdowns when clicking outside
      document.addEventListener('click', (e) => {
        if (aboutDropdown && !aboutDropdown.contains(e.target) && !aboutBtn?.contains(e.target)) {
          aboutDropdown.classList.add('hidden');
          aboutArrow?.classList.remove('rotate-180');
        }
        if (mobileAboutDropdown && !mobileAboutDropdown.contains(e.target) && !mobileAboutBtn?.contains(e.target)) {
          mobileAboutDropdown.classList.add('hidden');
          mobileAboutArrow?.classList.remove('rotate-180');
        }
      });
      
      // View Toggle (Map/Chart)
      const btnMapView = document.getElementById('btnMapView');
      const btnChartView = document.getElementById('btnChartView');
      const mapContainer = document.getElementById('mapContainer');
      const chartContainer = document.getElementById('chartContainer');
      
      if (btnMapView && btnChartView && mapContainer && chartContainer) {
        btnMapView.addEventListener('click', () => {
          btnMapView.classList.add('active');
          btnChartView.classList.remove('active');
          mapContainer.classList.remove('hidden');
          chartContainer.classList.add('hidden');
        });
        
        btnChartView.addEventListener('click', () => {
          btnChartView.classList.add('active');
          btnMapView.classList.remove('active');
          mapContainer.classList.add('hidden');
          chartContainer.classList.remove('hidden');
        });
      }
      
      // Legend Item Click Handler
      const legendItems = document.querySelectorAll('.legend-item');
      legendItems.forEach(item => {
        item.addEventListener('click', function() {
          legendItems.forEach(i => i.classList.remove('active'));
          this.classList.add('active');
          const field = this.dataset.field;
          const label = this.dataset.label;
          console.log('Selected indicator:', field, label);
          // Here you would update the map to highlight the selected indicator
        });
      });
      
      // Initialize Map (Leaflet)
      const map = L.map('map').setView([8.5667, 124.3167], 13);
      L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB',
        subdomains: 'abcd',
        maxZoom: 19,
        minZoom: 10
      }).addTo(map);
      
      // Add a sample marker (you would replace with actual barangay markers)
      const marker = L.marker([8.5667, 124.3167]).addTo(map);
      marker.bindPopup("<b>El Salvador City</b><br>Nutrition Hub");
      
      // Initialize Sample Chart
      const ctx = document.getElementById('fullChartCanvas')?.getContext('2d');
      if (ctx) {
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Underweight', 'Wasted', 'Overweight/Obese', 'Stunted'],
            datasets: [{
              label: 'Prevalence Rate (%)',
              data: [8.2, 5.7, 4.1, 12.3],
              backgroundColor: ['#eab308', '#f97316', '#3b82f6', '#ef4444'],
              borderRadius: 8,
              barPercentage: 0.6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
              legend: {
                position: 'top',
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return `${context.raw}% of children`;
                  }
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                max: 20,
                title: {
                  display: true,
                  text: 'Prevalence Rate (%)'
                },
                grid: {
                  color: '#e5e7eb'
                }
              },
              x: {
                title: {
                  display: true,
                  text: 'Nutrition Indicators'
                },
                grid: {
                  display: false
                }
              }
            }
          }
        });
      }
      
      // Initialize Gradient Legend
      const gradientGrid = document.getElementById('gradient-grid');
      if (gradientGrid) {
        const gradientColors = [
          '#fef3c7', '#fde68a', '#fcd34d', '#fbbf24', '#f59e0b',
          '#ea580c', '#dc2626', '#b91c1c', '#991b1b', '#7f1d1d', '#4a044e'
        ];
        
        gradientColors.forEach((color, index) => {
          const cell = document.createElement('div');
          cell.className = 'gradient-cell';
          cell.style.backgroundColor = color;
          cell.title = `${(index * 5)}-${(index + 1) * 5}% prevalence`;
          cell.addEventListener('click', () => {
            document.querySelectorAll('.gradient-cell').forEach(c => c.classList.remove('active-gradient-cell'));
            cell.classList.add('active-gradient-cell');
          });
          gradientGrid.appendChild(cell);
        });
      }
    });
  </script>
</body>
</html>