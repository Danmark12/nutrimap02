<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';

// Only CNO
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'BNS') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BNS | NutriMap</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  body { margin: 0; }

/* MAP */
#map { 
  height: 640px; 
}

/* MAP & CHART CONTAINER FLIP */
#mapContainer{
  top: 0;
  left: 0;
  transition: transform 1s;
  backface-visibility: hidden;
}
 #chartContainer{
  position: absolute;
  top: 160px;
  transition: transform 1s;
  backface-visibility: hidden;
 }
#mapContainer.flipped {
    z-index: 1;
  transform: rotateY(180deg);
}

#chartContainer.flipped {
  transform: rotateY(0deg);
  display: block;
    z-index: 1;
}

/* FULL CHART */
#chartContainer {
  top: 250px;
  display: none;
  width: 100%;
  max-width: 700px;   /* desktop width */
  height: 500px;      /* desktop height */
  margin: auto;
}
@media (max-width: 768px) {
  #chartContainer {
    width: 340px;  /* mobile width */
    height: 300px;    /* mobile height */
  }
}
/* TOOLTIP + MINI CHART — FIXED TOP-LEFT */
#chart-tooltip {
  display: none;  /* hide by default */
  position: absolute;
  top: 200px;
  left: 80px;
  z-index: 1000;
  background: rgba(255,255,255,0.95);
  padding: 8px;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.25);
  max-width: 500px;
  max-height: 350px;
  overflow-y: auto;
  pointer-events: none;
  flex-direction: column;
  align-items: stretch;
}

/* Let the chart wrapper control size */
#chart-tooltip canvas {
  width: 100% !important;  /* fill wrapper width */
  height: 100% !important; /* fill wrapper height */
}

/* MOBILE ADJUSTMENTS */
@media (max-width: 768px) {
  #chart-tooltip {
    display: none; /* hide by default */
    position: fixed;
    bottom: 30px;
    left: 10px;
    right: 10px;
    max-width: calc(100vw - 20px);
    max-height: 160px;
  }
}


  #chart-tooltip canvas {
    height: auto !important;
  }

/* GRADIENT SCALE */
.gradient-wrapper {
  margin-top: 1rem;
}

.gradient-grid {
  display: grid;
  grid-template-columns: repeat(11, 1fr); /* 10 gradient cells + No Data */
  gap: 2px;
  max-width: 720px;
}

.gradient-cell {
  height: 25px;
  width: 100%;
  cursor: pointer;
  border-radius: 1px;
  transition: transform 0.1s, outline 0.1s;
}

.gradient-cell:hover {
  transform: scale(1.1);
}

.active-gradient-cell {
  outline: 2px solid #000;
}

#legend-buttons li.active {
  font-weight: bold;
  transform: scale(1.05);
}

</style>
</head>
<body class="bg-gray-50 font-sans flex flex-col min-h-screen">

  <!-- HEADER -->
  <?php include 'header.php'; ?>
  <?php include 'sidemenu.php'; ?>

  <!-- Main Content -->
 <main class="flex-1 max-w-7xl mx-auto px-6 pt-2 pb-6 mb-28 bg-white">
 <div class="bg-gray-200 py-2 px-4 mb-4 flex items-center justify-between">
  <span class="uppercase tracking-wide text-cyan-600 font-semibold">Data</span>

  <div class="space-x-2">
    <button id="btnShowChart" class="bg-cyan-600 text-white px-3 py-1 rounded hover:bg-cyan-700 transition">
      Show Chart
    </button>
    <button id="btnBackToMap" class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 transition">
      Show Map
    </button>
  </div>
</div>
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
      <h1 class="text-lg md:text-xl font-semibold">
        El Salvador Health and Nutrition Map: Share of children who are 0-59 months old measured during OPT Plus
      </h1>
      <div class="flex flex-wrap gap-4 mt-2 md:mt-0 items-center">
        <div id="chart-tooltip" class="absolute bottom-5 left-5 max-w-[340px]"></div>
      </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
      <div class="flex-1">
       <div id="mapContainer">
  <div id="map" class="rounded border border-gray-300 z-0"></div>
</div>
<div id="chartContainer" class="hidden">
  <canvas id="fullChart" width="800" height="500"></canvas>
</div>
      </div>
      <div id="legend-buttons" class="w-full lg:w-60 bg-gray-50 border border-gray-300 rounded p-4">
        <div>
          <label class="block text-sm font-medium text-gray-600">Select Year</label>
          <select id="yearFilter" class="mt-1 block w-32 rounded border border-gray-300 shadow-sm"></select>
        </div>
        <h2 class="text-md font-semibold mb-3">Legend</h2>
       <ul class="space-y-2 text-sm">
  <li data-field="all" data-label="All Indicators" data-color="#888" class="cursor-pointer">
    <span class="w-4 h-4 mr-2 bg-gray-400 inline-block"></span>All
  </li>
  <li data-field="UNDERWEIGHT" data-label="Underweight" data-color="#FFFF00" class="cursor-pointer">
    <span class="w-4 h-4 mr-2 bg-yellow-400 inline-block"></span>Underweight
  </li>
   <li data-field="WASTED" data-label="Wasted" data-color="#FFA500" class="cursor-pointer">
    <span class="w-4 h-4 mr-2 bg-orange-500 inline-block"></span>Wasted
  </li>
  <li data-field="OVERWEIGHT_OBESE" data-label="Overweight/Obese" data-color="#0000FF" class="cursor-pointer">
    <span class="w-4 h-4 mr-2 bg-blue-500  inline-block"></span>Overweight/Obese
  </li>
  <li data-field="STUNTED" data-label="Stunted" data-color="#FF0000" class="cursor-pointer">
    <span class="w-4 h-4 mr-2 bg-red-600 inline-block"></span>Stunted
  </li>
</ul>

      </div>
    </div>

    <div class="gradient-wrapper mt-6" id="gradient-wrapper">
      <div class="gradient-grid" id="gradient-grid"></div>
    </div>
    <h2 class="p-4">
    <span class="font-bold">Data Source:</span> 
    <span>Operation Timbang Plus</span>
</h2>
  </main>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>


  <?php
if (!isset($_SESSION['user_id'])) exit();

$stmt = $pdo->prepare("SELECT barangay FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userBarangay = strtoupper(trim($stmt->fetchColumn() ?? ''));
?>
<script>
  const USER_BARANGAY = "<?= $userBarangay ?>";
</script>

  <script src="js/bns_map.js"></script>
  
</body>
</html>
