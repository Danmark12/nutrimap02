// ===================== INITIALIZE MAP =====================
const map = L.map('map', {
  center: [8.4760268, 124.4809540],
  zoom: 12,
  zoomControl: true,
  dragging: false,
  scrollWheelZoom: false,
  doubleClickZoom: false,
  boxZoom: false,
  touchZoom: false
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: 'Map data © OpenStreetMap contributors'
}).addTo(map);

// ===================== VARIABLES =====================
let geoLayer, geoData;
let activeField = null, activeColor = null, activeLabel = null;
let activeYear = 'All';
let miniChart = null;
let activeGradientRange = null;
const legendItems = Array.from(document.querySelectorAll('#legend-buttons li'));

let fullChart = null; // full chart instance
const mapContainer = document.getElementById('mapContainer');
const chartContainer = document.getElementById('chartContainer');
const realMap = document.getElementById('map'); // the actual map

// ===================== LOAD GEOJSON DATA =====================
fetch('bns_map_data.php')
  .then(r => r.json())
  .then(data => {
    geoData = data;

    // Populate Year dropdown
    const years = [...new Set(geoData.features.map(f => f.properties.YEAR).filter(y => y))].sort((a,b)=>b-a);
    const yearSelect = document.getElementById('yearFilter');
    yearSelect.innerHTML = '';
    const allOpt = document.createElement('option');
    allOpt.value = 'All';
    allOpt.textContent = 'All Years';
    yearSelect.appendChild(allOpt);
    years.forEach(y => {
      const opt = document.createElement('option');
      opt.value = y;
      opt.textContent = y;
      yearSelect.appendChild(opt);
    });

    activeYear = 'All';
    yearSelect.value = 'All';
    drawLayer(activeYear);

    yearSelect.addEventListener('change', e => {
      activeYear = e.target.value;
      drawLayer(activeYear);
      if(!chartContainer.classList.contains('hidden')) renderFullChart();
    });
  })
  .catch(err => console.error('Error loading map data:', err));

// ===================== DRAW LAYER =====================
function drawLayer(selectedYear) {
  if (!geoData) return;
  if (!selectedYear) selectedYear = activeYear;
  if (geoLayer) map.removeLayer(geoLayer);

  let mergedFeatures = [];

  // ===========================
  // 1) FILTER BY YEAR
  // ===========================
  if (selectedYear === 'All') {
    const barangayMap = new Map();

    geoData.features.forEach(f => {
      const b = f.properties.BARANGAY?.toUpperCase();
      const year = parseInt(f.properties.YEAR || 0);

      // keep only the latest year per barangay
      if (!barangayMap.has(b) || year > (barangayMap.get(b).properties.YEAR || 0)) {
        barangayMap.set(b, f);
      }
    });

    mergedFeatures = Array.from(barangayMap.values());
  } else {
    mergedFeatures = geoData.features.filter(
      f => f.properties.YEAR == selectedYear
    );
  }

  // =====================================
  // 2) ADD BARANGAYS WITH NO DATA (ONCE)
  // =====================================
  const barangayWithData = new Set(
    mergedFeatures.map(f => f.properties.BARANGAY?.toUpperCase())
  );

  const allBarangays = [
    ...new Set(geoData.features.map(f => f.properties.BARANGAY?.toUpperCase()))
  ];

  allBarangays.forEach(b => {
    if (!barangayWithData.has(b)) {
      const base = geoData.features.find(
        f => f.properties.BARANGAY?.toUpperCase() === b
      );

      if (base) {
        const clone = JSON.parse(JSON.stringify(base));
        clone.properties.NO_DATA = true;
        mergedFeatures.push(clone);
      }
    }
  });

  // ===============================
  // 3) FILTER ONLY LOGGED-IN USER'S BARANGAY
  // ===============================
  const userOnlyFeatures = mergedFeatures.filter(f =>
    f.properties.BARANGAY?.toUpperCase() === USER_BARANGAY
  );

  // If the barangay is missing (should not happen), fallback to empty
  const userGeoJSON = {
    type: "FeatureCollection",
    features: userOnlyFeatures
  };

  // ===============================
  // 4) DRAW THE BARANGAY POLYGON
  // ===============================
  geoLayer = L.geoJSON(userGeoJSON, {
    style: styleFeature,
    onEachFeature: featureHandler
  }).addTo(map);

  // ===============================
  // 5) AUTO ZOOM
  // ===============================
  if (geoLayer.getLayers().length > 0) {
    map.fitBounds(geoLayer.getBounds());
  }
}


// ===================== STYLING =====================
function styleFeature(feature) {
  const props = feature.properties;

if (activeField && activeColor) {
  let val = props[activeField.toUpperCase()];

  if (val === 0 || val == null || props.NO_DATA === true) {
    return {
      color: '#444',
      weight: 3,
      fillOpacity: 0,
      fillColor: 'transparent',
      dashArray: '2,2'
    };
  }

  // Convert raw percent to bin 1–10
let step = Math.floor(val / 2);
if (step < 0) step = 0;
if (step > 9) step = 9;

  return {
    color: '#000',
    weight: 2,
    fillOpacity: 0.8,
    fillColor: getGradientColor(activeColor, step + 1)
  };
}

  const hasData = legendItems.some(li => li.dataset.field !== 'all' && (props[li.dataset.field.toUpperCase()] ?? 0) > 0);

  return hasData
    ? { color: '#333', weight: 2, fillOpacity: 0.8, fillColor: '#000' }
    : { color: '#444', weight: 3, fillOpacity: 0, fillColor: 'transparent', dashArray: '2,2' };
}

// ===================== TOOLTIP + MINI CHART =====================
function featureHandler(feature, layer) {
  const tooltip = document.getElementById('chart-tooltip');
  const barangayName = feature.properties.BARANGAY || 'Unknown';

  layer.on({
    mouseover(e) {
      const isMobile = window.innerWidth < 768;
      if (isMobile) return; // skip tooltip on mobile

      tooltip.style.display = 'block';
      tooltip.style.opacity = 1;
      tooltip.innerHTML = '';
      tooltip.style.padding = '8px';

      // ===== TITLE =====
      const title = document.createElement('div');
      title.className = 'tooltip-title';
      title.textContent = barangayName;
      title.style.fontWeight = 'bold';
      title.style.marginBottom = '6px';
      tooltip.appendChild(title);

      // ===== INDICATORS =====
      const indicatorsToShow = activeField && activeField !== 'all'
        ? legendItems.filter(li => li.dataset.field.toUpperCase() === activeField)
        : legendItems.filter(li => li.dataset.field !== 'all');

      // ===== CHART TYPE =====
      const chartType = (activeYear === 'All') ? 'line' : 'bar';

      // ===== LABELS =====
      const labels = (activeYear === 'All')
        ? [...new Set(geoData.features.filter(f => f.properties.BARANGAY === barangayName).map(f => f.properties.YEAR))].sort((a,b)=>a-b)
        : [activeYear];

      // ===== DATASETS =====
      const datasets = indicatorsToShow.map(li => {
        const data = labels.map(y => getValue(barangayName, y, li.dataset.field));
        return {
          label: li.dataset.label,
          data,
          borderColor: li.dataset.color,
          backgroundColor: li.dataset.color,
          fill: chartType === 'bar',
          tension: 0.3,
          borderWidth: 2,
          spanGaps: true,
          pointRadius: 3
        };
      });

      // ===== CREATE MINI CHART =====
      createChart('300px', '150px', labels, datasets, chartType);

      // ===== INDICATOR LIST =====
      const indicatorList = document.createElement('ul');
      indicatorList.style.listStyle = 'none';
      indicatorList.style.padding = '0';
      indicatorList.style.marginTop = '6px';

      indicatorsToShow.forEach(li => {
        const value = getValue(
          barangayName,
          activeYear === 'All' ? labels[labels.length - 1] : activeYear,
          li.dataset.field
        );

        const liItem = document.createElement('li');
        liItem.style.display = 'flex';
        liItem.style.alignItems = 'center';
        liItem.style.marginBottom = '4px';

        const colorBox = document.createElement('span');
        colorBox.style.width = '12px';
        colorBox.style.height = '12px';
        colorBox.style.background = li.dataset.color;
        colorBox.style.display = 'inline-block';
        colorBox.style.marginRight = '6px';

        const text = document.createElement('span');
        text.textContent = `${li.dataset.label}: ${value.toFixed(2)}%`;

        liItem.appendChild(colorBox);
        liItem.appendChild(text);
        indicatorList.appendChild(liItem);
      });

      tooltip.appendChild(indicatorList);

      // ===== HELPER FUNCTIONS =====
      function getValue(barangay, year, field) {
        const f = geoData.features.find(ff =>
          ff.properties.BARANGAY === barangay &&
          String(ff.properties.YEAR) === String(year)
        );
        return f ? Number(f.properties[field.toUpperCase()] ?? 0) : 0;
      }

      function createChart(width, height, labels, datasets, type) {
        const chartWrapper = document.createElement('div');
        chartWrapper.style.width = width;
        chartWrapper.style.height = height;
        chartWrapper.style.marginTop = '4px';
        tooltip.appendChild(chartWrapper);

        const canvas = document.createElement('canvas');
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        chartWrapper.appendChild(canvas);

        if (miniChart) miniChart.destroy();
        miniChart = new Chart(canvas, {
          type: type,
          data: { labels, datasets },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
              legend: { display: false },
              tooltip: { enabled: false },
              datalabels: { display: false }
            },
            scales: {
              x: { display: true },
              y: { 
                beginAtZero: true, 
                max: 20,
                ticks: { callback: val => val + '%', stepSize: 2 }
              }
            }
          },
          plugins: [ChartDataLabels]
        });
      }
    },

    mouseout(e) {
      const isMobile = window.innerWidth < 768;
      if (isMobile) return;

      tooltip.style.opacity = 0;
      tooltip.style.display = 'none';
      tooltip.innerHTML = '';
      if (miniChart) miniChart.destroy();
    },

    click(e) {
      // Just highlight clicked barangay (no dropdown)
      geoLayer.eachLayer(l => {
        const name = l.feature.properties.BARANGAY?.toLowerCase();
        l.setStyle({
          ...styleFeature(l.feature),
          opacity: name === barangayName.toLowerCase() ? 1 : 0.3,
          fillOpacity: name === barangayName.toLowerCase() ? 0.7 : 0.1,
          weight: name === barangayName.toLowerCase() ? 3 : 1
        });
      });
    }
  });
}

// ===================== LEGEND + FILTERS =====================
const defaultLegend = legendItems.find(li => li.dataset.field === 'all');
if (defaultLegend) {
  defaultLegend.classList.add('active'); // highlight it
  activeField = null;                     // "All" means no specific field
  activeLabel = defaultLegend.dataset.label;
  activeColor = defaultLegend.dataset.color;
  if (geoLayer) geoLayer.setStyle(styleFeature);
}

// ===== Click handlers =====
legendItems.forEach(item => {
  item.addEventListener('click', () => {
    // Remove "active" class from all legend items
    legendItems.forEach(li => li.classList.remove('active'));

    // Add "active" class to clicked item
    item.classList.add('active');

    // Set active indicators
    activeField = item.dataset.field === 'all' ? null : item.dataset.field.toUpperCase();
    activeLabel = item.dataset.label;
    activeColor = item.dataset.color;

    // Update map colors
    if (geoLayer) geoLayer.setStyle(styleFeature);

    // Update gradient scale or clear it
    if (activeField) updateGradientScale(activeColor);
    else document.getElementById('gradient-grid').innerHTML = '';

    // If chart is visible, update full chart
    if (!chartContainer.classList.contains('hidden')) renderFullChart();
  });
});

// ===================== FULL CHART =====================
mapContainer.classList.remove('flipped');
chartContainer.classList.add('hidden');

// ===================== FLIP HELPERS =====================
function flipToChart() {
  mapContainer.classList.add('flipped');
  chartContainer.classList.remove('hidden');
  chartContainer.classList.add('flipped');
  renderFullChart();
}

function flipToMap() {
  mapContainer.classList.remove('flipped');
  chartContainer.classList.add('hidden');
  chartContainer.classList.remove('flipped');
}

// ===================== BUTTON LISTENERS (UPDATED) =====================
document.addEventListener('DOMContentLoaded', () => {
  const btnChart = document.getElementById('btnShowChart');
  const btnMap = document.getElementById('btnBackToMap');

  if (btnChart) btnChart.addEventListener('click', flipToChart);
  if (btnMap) btnMap.addEventListener('click', flipToMap);
});

// ===================== RENDER FULL CHART =====================
function renderFullChart() {
  if (!geoData) return;

  const selectedYear = document.getElementById('yearFilter').value.trim();

  // Get the active indicators from the legend
  const indicators = activeField 
    ? legendItems.filter(li => li.dataset.field.toUpperCase() === activeField)
    : legendItems.filter(li => li.dataset.field !== 'all');

  // Filter features: only the logged-in user's barangay
  const filteredFeatures = geoData.features.filter(f =>
    f.properties.BARANGAY?.toUpperCase() === USER_BARANGAY &&
    (selectedYear === 'All' || String(f.properties.YEAR) === selectedYear)
  );

  let labels = [];
  let datasets = [];
  let chartType;

  if (selectedYear === 'All') {
    // Line chart for all years
    chartType = 'line';
    labels = [...new Set(filteredFeatures.map(f => f.properties.YEAR))].sort((a, b) => a - b);

    indicators.forEach(li => {
      const field = li.dataset.field.toUpperCase();
      const data = labels.map(y => {
        const f = filteredFeatures.find(f => String(f.properties.YEAR) == y);
        return f ? Number(f.properties[field] || 0) : 0;
      });

      datasets.push({
        label: li.dataset.label,
        data,
        borderColor: li.dataset.color,
        backgroundColor: li.dataset.color,
        borderWidth: 2,
        fill: false,
        tension: 0.3,
        pointRadius: 4,
        type: 'line'
      });
    });

  } else {
    // Bar chart for a single year
    chartType = 'bar';
    labels = [USER_BARANGAY];

    indicators.forEach(li => {
      const field = li.dataset.field.toUpperCase();
      const f = filteredFeatures[0];
      const value = f ? Number(f.properties[field] || 0) : 0;

      datasets.push({
        label: li.dataset.label,
        data: [value],
        borderColor: li.dataset.color,
        backgroundColor: li.dataset.color,
        borderWidth: 2,
        fill: true,
        type: 'bar'
      });
    });
  }

  // Render chart
  const ctx = document.getElementById('fullChart').getContext('2d');
  if (fullChart) fullChart.destroy();

  fullChart = new Chart(ctx, {
    type: chartType,
    data: { labels, datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: true },
        tooltip: {
          callbacks: {
            label: function(context) {
              return `${context.dataset.label}: ${context.raw.toFixed(2)}%`;
            }
          }
        }
      },
      scales: {
        y: { 
          beginAtZero: true,
          max: 20,
          ticks: { callback: val => val + '%', stepSize: 2 }
        }
      }
    }
  });
}

// ===================== HELPERS =====================
function hexToRgb(hex){ const c=parseInt(hex.slice(1),16); return {r:(c>>16)&255,g:(c>>8)&255,b:c&255}; }
function getGradientColor(baseColor,value){
  if(value==null) return '#999';
  const ratio = Math.min(1,value/9);
  const rgb = hexToRgb(baseColor);
  const start = { r: 240, g: 240, b: 240 };
  const r = Math.round(start.r+(rgb.r-start.r)*ratio);
  const g = Math.round(start.g+(rgb.g-start.g)*ratio);
  const b = Math.round(start.b+(rgb.b-start.b)*ratio);
  return `rgb(${r},${g},${b})`;
}

// ===================== GRADIENT SCALE =====================
function updateGradientScale(baseColor) {
  const grid = document.getElementById('gradient-grid');
  if (!grid) return;
  grid.innerHTML = '';
  let activeCellIndex = null;

  const steps = 10;  // 10 cells
  const rangeSize = 2; // each tooltip range = 2%

  for (let i = 0; i < steps; i++) {
    const minPercent = i * rangeSize;        // 0,2,4,6,...38
    const maxPercent = minPercent + rangeSize - 0; // 1,3,5,7,...39  

    const cell = document.createElement('div');
    cell.className = 'gradient-cell';

    // Send 1–20 to gradient color function
    cell.style.background = getGradientColor(baseColor, i + 1);

    // Tooltip: show 0–2%, 3–4%, ...
    cell.title = `${minPercent}% – ${maxPercent}%`;

    cell.addEventListener('mouseover', () => {
      cell.classList.add('active-gradient-cell');
      activeGradientRange = { min: minPercent, max: maxPercent };
      filterMapByGradient();
    });

    cell.addEventListener('mouseout', () => {
      cell.classList.remove('active-gradient-cell');
      activeGradientRange = null;
      filterMapByGradient();
    });

    cell.addEventListener('click', () => {
      if (activeCellIndex !== null && grid.children[activeCellIndex]) {
        grid.children[activeCellIndex].classList.remove('active-gradient-cell');
      }
      activeCellIndex = i;
      cell.classList.add('active-gradient-cell');

      activeGradientRange = { min: minPercent, max: maxPercent };
      filterMapByGradient();
    });

    grid.appendChild(cell);
  }

  // === NO DATA CELL ===
  const noDataCell = document.createElement('div');
  noDataCell.className = 'gradient-cell';
  noDataCell.style.background = 'transparent';
  noDataCell.style.border = '1px dashed #333';
  noDataCell.title = 'No Data';

  noDataCell.addEventListener('mouseover', () => {
    noDataCell.classList.add('active-gradient-cell');
    activeGradientRange = 'nodata';
    filterMapByGradient();
  });

  noDataCell.addEventListener('mouseout', () => {
    noDataCell.classList.remove('active-gradient-cell');
    activeGradientRange = null;
    filterMapByGradient();
  });

  noDataCell.addEventListener('click', () => {
    if (activeCellIndex !== null && grid.children[activeCellIndex]) {
      grid.children[activeCellIndex].classList.remove('active-gradient-cell');
    }
    activeCellIndex = steps;
    noDataCell.classList.add('active-gradient-cell');
    activeGradientRange = 'nodata';
    filterMapByGradient();
  });

  grid.appendChild(noDataCell);
}

// ===================== FILTER BY GRADIENT =====================
function filterMapByGradient() {
  if (!geoLayer) return;

  geoLayer.eachLayer(layer => {
    const props = layer.feature.properties;
    if (!activeField) return layer.setStyle(styleFeature(layer.feature));

    let val = props[activeField.toUpperCase()];
    val = (val === 0 || val == null || props.NO_DATA === true) ? null : val;

    let inRange = false;

    if (activeGradientRange === 'nodata') {
      inRange = val === null;

    } else if (activeGradientRange) {
      const min = activeGradientRange.min;
      const max = activeGradientRange.max;

      if (val !== null) {
        // Make upper bound exclusive for all bins except the last
        if (max === 20) { // last bin, inclusive upper bound
          inRange = val >= min && val <= max;
        } else {
          inRange = val >= min && val < max;
        }
      }

    } else {
      inRange = true;
    }

    layer.setStyle({
      ...styleFeature(layer.feature),
      fillOpacity: inRange ? (val === null ? 0 : 0.8) : 0.1,
      opacity: inRange ? 1 : 0.3
    });
  });
}