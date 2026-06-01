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
let activeField = 'all';
let activeColor = '#4B5563';
let activeYear = null;
let activeYearFrom = null;
let activeYearTo = null;
let isYearRange = true;
let miniChart = null;
let activeGradientRange = null;
let currentPopulation = 'preschool';
let fullChart = null;
let hoverTimeout = null;

const mapContainer = document.getElementById('mapContainer');
const chartContainer = document.getElementById('chartContainer');

// ===================== HELPER FUNCTIONS =====================
function hexToRgb(hex) {
  const c = parseInt(hex.slice(1), 16);
  return { r: (c >> 16) & 255, g: (c >> 8) & 255, b: c & 255 };
}

function getGradientColor(baseColor, value, maxRange = 40) {
  if (value == null) return '#e5e7eb';
  const maxVal = maxRange;
  const ratio = Math.min(1, value / maxVal);
  const rgb = hexToRgb(baseColor);
  const start = { r: 240, g: 240, b: 240 };
  const r = Math.round(start.r + (rgb.r - start.r) * ratio);
  const g = Math.round(start.g + (rgb.g - start.g) * ratio);
  const b = Math.round(start.b + (rgb.b - start.b) * ratio);
  return `rgb(${r}, ${g}, ${b})`;
}

// Calculate SUM of all indicators for a feature (used when "All" is selected)
function getTotalPercentage(feature) {
  const props = feature.properties;
  let total = 0;
  
  if (currentPopulation === 'preschool') {
    total = (props.UNDERWEIGHT || 0) + 
            (props.WASTED || 0) + 
            (props.OVERWEIGHT_OBESE || 0) + 
            (props.STUNTED || 0);
  } else {
    total = (props.WASTED || 0) + 
            (props.OVERWEIGHT_OBESE || 0) + 
            (props.STUNTED || 0);
  }
  
  return Math.min(total, 40);
}

// Get current value for a feature based on active field
function getCurrentValue(feature) {
  const props = feature.properties;
  if (activeField === 'all') {
    return getTotalPercentage(feature);
  } else {
    let val = props[activeField.toUpperCase()];
    return (val === 0 || val == null || props.NO_DATA === true) ? null : val;
  }
}

// Highlight gradient cell based on value
function highlightGradientByValue(value) {
  if (value === null || value === undefined) {
    const noDataCell = document.querySelector('#gradient-grid .gradient-cell:first-child');
    if (noDataCell) {
      document.querySelectorAll('#gradient-grid .gradient-cell').forEach(cell => {
        cell.classList.remove('active-gradient-cell');
      });
      noDataCell.classList.add('active-gradient-cell');
    }
    return;
  }
  
  const maxRange = (activeField === 'all') ? 40 : 20;
  const stepSize = maxRange / 10;
  const cellIndex = Math.floor(value / stepSize);
  const safeIndex = Math.min(9, Math.max(0, cellIndex));
  
  const gradientCell = document.querySelector(`#gradient-grid .gradient-cell:nth-child(${safeIndex + 2})`);
  
  if (gradientCell) {
    document.querySelectorAll('#gradient-grid .gradient-cell').forEach(cell => {
      cell.classList.remove('active-gradient-cell');
    });
    gradientCell.classList.add('active-gradient-cell');
  }
}

// Clear gradient highlight
function clearGradientHighlight() {
  document.querySelectorAll('#gradient-grid .gradient-cell').forEach(cell => {
    cell.classList.remove('active-gradient-cell');
  });
}

// ===================== UPDATE GRADIENT SCALE =====================
function updateGradientScale() {
  const grid = document.getElementById('gradient-grid');
  if (!grid) return;
  grid.innerHTML = '';

  const noDataCell = document.createElement('div');
  noDataCell.className = 'gradient-cell';
  noDataCell.style.background = '#e5e7eb';
  noDataCell.style.backgroundImage = 'repeating-linear-gradient(45deg, #cbd5e1 0px, #cbd5e1 2px, #f1f5f9 2px, #f1f5f9 6px)';
  noDataCell.style.border = '1px solid #cbd5e1';
  noDataCell.title = 'No Data';
  noDataCell.textContent = 'ND';
  noDataCell.style.display = 'flex';
  noDataCell.style.alignItems = 'center';
  noDataCell.style.justifyContent = 'center';
  noDataCell.style.fontSize = '9px';
  noDataCell.style.fontWeight = 'bold';
  noDataCell.style.color = '#475569';
  noDataCell.addEventListener('mouseover', () => { activeGradientRange = 'nodata'; filterMapByGradient(); });
  noDataCell.addEventListener('mouseout', () => { activeGradientRange = null; filterMapByGradient(); });
  noDataCell.addEventListener('click', () => { activeGradientRange = 'nodata'; filterMapByGradient(); });
  grid.appendChild(noDataCell);

  const maxRange = (activeField === 'all') ? 40 : 20;
  const stepSize = maxRange / 10;
  
  for (let i = 0; i < 10; i++) {
    const min = i * stepSize;
    const max = min + stepSize;
    const cell = document.createElement('div');
    cell.className = 'gradient-cell';
    cell.style.background = getGradientColor(activeColor, (i + 1) * stepSize, maxRange);
    cell.title = `${min.toFixed(0)}% – ${max.toFixed(0)}%`;
    cell.dataset.min = min;
    cell.dataset.max = max;
    cell.addEventListener('mouseover', () => { cell.classList.add('active-gradient-cell'); activeGradientRange = { min, max }; filterMapByGradient(); });
    cell.addEventListener('mouseout', () => { cell.classList.remove('active-gradient-cell'); activeGradientRange = null; filterMapByGradient(); });
    cell.addEventListener('click', () => { activeGradientRange = { min, max }; filterMapByGradient(); });
    grid.appendChild(cell);
  }
}

function filterMapByGradient() {
  if (!geoLayer) return;
  geoLayer.eachLayer(layer => {
    const val = getCurrentValue(layer.feature);
    let inRange = false;
    if (activeGradientRange === 'nodata') inRange = val === null;
    else if (activeGradientRange) inRange = val !== null && val >= activeGradientRange.min && val <= activeGradientRange.max;
    else inRange = true;
    
    layer.setStyle({
      ...styleFeature(layer.feature),
      fillOpacity: inRange ? (val === null ? 0 : 0.8) : 0.1,
      opacity: inRange ? 1 : 0.3
    });
  });
}

// ===================== TIMELINE SLIDER =====================
function initTimelineSlider(minYear, maxYear, availableYears) {
  const track = document.getElementById('timelineTrack');
  const handleLeft = document.getElementById('timelineHandleLeft');
  const handleRight = document.getElementById('timelineHandleRight');
  const fill = document.getElementById('timelineFill');
  const yearLabelsContainer = document.getElementById('yearLabels');

  if (!track || !handleLeft || !handleRight || !fill || !yearLabelsContainer) return;

  const latestYear = Math.max(...availableYears);
  let currentMin = latestYear;
  let currentMax = latestYear;
  let activeHandle = null;
  
  activeYear = latestYear;
  activeYearFrom = latestYear;
  activeYearTo = latestYear;
  isYearRange = false;

  yearLabelsContainer.innerHTML = '';
  for (let year = minYear; year <= maxYear; year++) {
    const span = document.createElement('span');
    span.textContent = year;
    if (availableYears.includes(year)) {
      span.classList.add('has-data');
      span.style.cursor = 'pointer';
      span.addEventListener('click', (function(y) {
        return function() {
          currentMin = y;
          currentMax = y;
          isYearRange = false;
          activeYear = y;
          updateSliderPosition();
          drawLayer();
          if (!chartContainer.classList.contains('hidden')) renderFullChart();
        };
      })(year));
    } else {
      span.classList.add('no-data');
      span.style.cursor = 'not-allowed';
    }
    yearLabelsContainer.appendChild(span);
  }

  function updateSliderPosition() {
    const fromPercent = ((currentMin - minYear) / (maxYear - minYear)) * 100;
    const toPercent = ((currentMax - minYear) / (maxYear - minYear)) * 100;

    handleLeft.style.left = `${fromPercent}%`;
    handleRight.style.left = `${toPercent}%`;
    fill.style.left = `${fromPercent}%`;
    fill.style.width = `${toPercent - fromPercent}%`;

    activeYearFrom = currentMin;
    activeYearTo = currentMax;
    
    if (currentMin === currentMax) {
      isYearRange = false;
      activeYear = currentMin;
    } else {
      isYearRange = true;
      activeYear = null;
    }

    const spans = yearLabelsContainer.querySelectorAll('span');
    spans.forEach((span, idx) => {
      const year = minYear + idx;
      if (year >= currentMin && year <= currentMax && availableYears.includes(year)) {
        span.style.backgroundColor = '#d1fae5';
        span.style.color = '#065f46';
        span.style.fontWeight = 'bold';
      } else {
        span.style.backgroundColor = '';
        span.style.color = '';
        span.style.fontWeight = availableYears.includes(year) ? '600' : 'normal';
      }
    });
  }

  function getYearFromClientX(clientX) {
    const rect = track.getBoundingClientRect();
    const percent = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
    return Math.round(minYear + percent * (maxYear - minYear));
  }

  function onMouseMove(e) {
    if (!activeHandle) return;
    const newYear = getYearFromClientX(e.clientX);
    if (activeHandle === 'left' && newYear <= currentMax) currentMin = newYear;
    if (activeHandle === 'right' && newYear >= currentMin) currentMax = newYear;
    
    if (currentMin === currentMax) {
      isYearRange = false;
      activeYear = currentMin;
    } else {
      isYearRange = true;
      activeYear = null;
    }
    
    updateSliderPosition();
    drawLayer();
    if (!chartContainer.classList.contains('hidden')) renderFullChart();
  }

  function onMouseUp() {
    activeHandle = null;
    document.removeEventListener('mousemove', onMouseMove);
    document.removeEventListener('mouseup', onMouseUp);
  }

  handleLeft.addEventListener('mousedown', (e) => {
    e.stopPropagation();
    activeHandle = 'left';
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
  });

  handleRight.addEventListener('mousedown', (e) => {
    e.stopPropagation();
    activeHandle = 'right';
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
  });

  track.addEventListener('click', (e) => {
    const clickedYear = getYearFromClientX(e.clientX);
    if (availableYears.includes(clickedYear)) {
      const distToLeft = Math.abs(clickedYear - currentMin);
      const distToRight = Math.abs(clickedYear - currentMax);
      if (distToLeft < distToRight) {
        currentMin = clickedYear;
        if (currentMin > currentMax) currentMin = currentMax;
      } else {
        currentMax = clickedYear;
        if (currentMax < currentMin) currentMax = currentMin;
      }
      
      if (currentMin === currentMax) {
        isYearRange = false;
        activeYear = currentMin;
      } else {
        isYearRange = true;
        activeYear = null;
      }
      
      updateSliderPosition();
      drawLayer();
      if (!chartContainer.classList.contains('hidden')) renderFullChart();
    }
  });

  updateSliderPosition();
}

// ===================== LOAD DATA =====================
function loadData(populationType) {
  const dataUrl = populationType === 'preschool' ? 'bns_map_data.php' : 'bns_school_data.php';

  fetch(dataUrl)
    .then(r => r.json())
    .then(data => {
      geoData = data;

      const years = [...new Set(geoData.features.map(f => f.properties.YEAR).filter(y => y))].sort((a, b) => a - b);
      const minYear = years.length > 0 ? Math.min(...years) : 2020;
      const maxYear = years.length > 0 ? Math.max(...years) : 2026;

      initTimelineSlider(minYear, maxYear, years);

      drawLayer();
      if (!chartContainer.classList.contains('hidden')) renderFullChart();
    })
    .catch(err => console.error('Error loading data:', err));
}

// ===================== DRAW LAYER =====================
function drawLayer() {
  if (!geoData) return;
  if (geoLayer) map.removeLayer(geoLayer);

  let features = [...geoData.features];

  if (!isYearRange && activeYear !== null) {
    features = features.filter(f => parseInt(f.properties.YEAR) === activeYear);
  } else if (isYearRange && activeYearFrom !== null && activeYearTo !== null) {
    features = features.filter(f => {
      const year = parseInt(f.properties.YEAR);
      return year >= activeYearFrom && year <= activeYearTo;
    });
  }

  const userOnlyFeatures = features.filter(f =>
    f.properties.BARANGAY?.toUpperCase() === USER_BARANGAY
  );

  const userGeoJSON = { type: "FeatureCollection", features: userOnlyFeatures };

  geoLayer = L.geoJSON(userGeoJSON, {
    style: styleFeature,
    onEachFeature: featureHandler
  }).addTo(map);

  if (geoLayer.getLayers().length > 0) {
    map.fitBounds(geoLayer.getBounds());
  }
}

// ===================== STYLING =====================
function styleFeature(feature) {
  const props = feature.properties;

  if (activeField === 'all') {
    let total = getTotalPercentage(feature);
    
    if (total === 0 || total == null || props.NO_DATA === true) {
      return { color: '#444', weight: 3, fillOpacity: 0, fillColor: 'transparent', dashArray: '2,2' };
    }
    
    return { color: '#000', weight: 2, fillOpacity: 0.8, fillColor: getGradientColor(activeColor, total, 40) };
  } 
  else {
    let val = props[activeField.toUpperCase()];

    if (val === 0 || val == null || props.NO_DATA === true) {
      return { color: '#444', weight: 3, fillOpacity: 0, fillColor: 'transparent', dashArray: '2,2' };
    }

    return { color: '#000', weight: 2, fillOpacity: 0.8, fillColor: getGradientColor(activeColor, val, 20) };
  }
}

// ===================== TOOLTIP WITH HOVER HIGHLIGHTING =====================
function featureHandler(feature, layer) {
  const tooltip = document.getElementById('chart-tooltip');
  const barangayName = feature.properties.BARANGAY || 'Unknown';

  layer.on({
    mouseover(e) {
      layer.setStyle({
        weight: 1,
        color: '#000',
        fillOpacity: 0.9,
        opacity: 1
      });
      
      const currentValue = getCurrentValue(feature);
      highlightGradientByValue(currentValue);
      
      if (window.innerWidth < 768) return;
      tooltip.style.display = 'block';
      tooltip.innerHTML = '';

      const title = document.createElement('div');
      title.innerHTML = `<strong>${barangayName}</strong>`;
      title.style.marginBottom = '6px';
      tooltip.appendChild(title);

      let indicators = [];
      if (activeField === 'all') {
        if (currentPopulation === 'preschool') {
          indicators = [
            { field: 'UNDERWEIGHT', label: 'Underweight', color: '#d4a800' },
            { field: 'WASTED', label: 'Wasted', color: '#F97316' },
            { field: 'OVERWEIGHT_OBESE', label: 'Overweight/Obese', color: '#3B82F6' },
            { field: 'STUNTED', label: 'Stunted', color: '#EF4444' }
          ];
        } else {
          indicators = [
            { field: 'WASTED', label: 'Wasted', color: '#F97316' },
            { field: 'OVERWEIGHT_OBESE', label: 'Overweight/Obese', color: '#3B82F6' },
            { field: 'STUNTED', label: 'Stunted', color: '#EF4444' }
          ];
        }
      } else {
        indicators = [{ field: activeField, label: activeField, color: activeColor }];
      }

      let years = [];
      if (isYearRange && activeYearFrom && activeYearTo) {
        years = [...new Set(geoData.features
          .filter(f => f.properties.BARANGAY === barangayName)
          .map(f => parseInt(f.properties.YEAR))
          .filter(y => y >= activeYearFrom && y <= activeYearTo))].sort((a, b) => a - b);
      } else if (!isYearRange && activeYear !== null) {
        years = [activeYear];
      } else {
        years = [activeYearFrom || new Date().getFullYear()];
      }

      const canvas = document.createElement('canvas');
      canvas.width = 260;
      canvas.height = 140;
      tooltip.appendChild(canvas);

      const datasets = indicators.map(ind => ({
        label: ind.label,
        data: years.map(y => {
          const f = geoData.features.find(ff => ff.properties.BARANGAY === barangayName && parseInt(ff.properties.YEAR) === y);
          return f ? (f.properties[ind.field] || 0) : 0;
        }),
        borderColor: ind.color,
        backgroundColor: 'transparent',
        borderWidth: 2,
        pointRadius: 4,
        pointBackgroundColor: ind.color,
        pointBorderColor: '#fff',
        tension: 0.3
      }));

      if (miniChart) miniChart.destroy();
      
      const isLineChartWithAll = (years.length > 1 && activeField === 'all');
      
      miniChart = new Chart(canvas, {
        type: years.length > 1 ? 'line' : 'bar',
        data: { labels: years, datasets },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { display: false },
            tooltip: { enabled: true },
            datalabels: {
              display: isLineChartWithAll ? false : true,
              formatter: (v) => v === 0 || !v ? '' : v.toFixed(1) + '%',
              font: { size: 9, weight: 'bold' },
              backgroundColor: 'rgba(255,255,255,0.8)',
              padding: 2,
              borderRadius: 2,
              align: 'top',
              offset: 4
            }
          },
          scales: { 
            y: { 
              min: 0,
              max: 20,
              beginAtZero: true, 
              ticks: { 
                stepSize: 4,
                callback: v => v + '%',
                autoSkip: false,
                font: { size: 8 }
              } 
            } 
          }
        },
        plugins: [ChartDataLabels]
      });
    },
    mouseout() {
      geoLayer.resetStyle(layer);
      clearGradientHighlight();
      tooltip.style.display = 'none';
      if (miniChart) { miniChart.destroy(); miniChart = null; }
    },
    click(e) {
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

// ===================== ATTACH LEGEND CLICK EVENTS =====================
function attachLegendEvents() {
  const legendLis = document.querySelectorAll('#legend-buttons ul li');
  
  legendLis.forEach(li => {
    li.removeEventListener('click', li._clickHandler);
    
    const handler = function(e) {
      document.querySelectorAll('#legend-buttons ul li').forEach(l => {
        l.classList.remove('active');
      });
      
      this.classList.add('active');
      
      activeField = this.dataset.field;
      activeColor = this.dataset.color;
      
      if (geoLayer) {
        geoLayer.eachLayer(layer => {
          layer.setStyle(styleFeature(layer.feature));
        });
        filterMapByGradient();
      }
      
      updateGradientScale();
      
      if (!chartContainer.classList.contains('hidden')) {
        renderFullChart();
      }
    };
    
    li._clickHandler = handler;
    li.addEventListener('click', handler);
  });
}

// ===================== UPDATE LEGEND LIST ONLY =====================
function updateLegendListOnly() {
  const legendListUl = document.querySelector('#legend-buttons ul');
  if (!legendListUl) return;

  if (currentPopulation === 'preschool') {
    legendListUl.innerHTML = `
      <li data-field="all" data-label="All Indicators" data-color="#4B5563" class="cursor-pointer active">
        <span class="w-4 h-4 mr-2 inline-block" style="background:#4B5563"></span>
        <span>All</span>
      </li>
      <li data-field="UNDERWEIGHT" data-label="Underweight" data-color="#d4a800" class="cursor-pointer">
        <span class="w-4 h-4 mr-2 inline-block" style="background:#d4a800"></span>
        <span>Underweight</span>
      </li>
      <li data-field="WASTED" data-label="Wasted" data-color="#F97316" class="cursor-pointer">
        <span class="w-4 h-4 mr-2 inline-block" style="background:#F97316"></span>
        <span>Wasted</span>
      </li>
      <li data-field="OVERWEIGHT_OBESE" data-label="Overweight/Obese" data-color="#3B82F6" class="cursor-pointer">
        <span class="w-4 h-4 mr-2 inline-block" style="background:#3B82F6"></span>
        <span>Overweight/Obese</span>
      </li>
      <li data-field="STUNTED" data-label="Stunted" data-color="#EF4444" class="cursor-pointer">
        <span class="w-4 h-4 mr-2 inline-block" style="background:#EF4444"></span>
        <span>Stunted</span>
      </li>
    `;
  } else {
    legendListUl.innerHTML = `
      <li data-field="all" data-label="All Indicators" data-color="#4B5563" class="cursor-pointer active">
        <span class="w-4 h-4 mr-2 inline-block" style="background:#4B5563"></span>
        <span>All</span>
      </li>
      <li data-field="WASTED" data-label="Wasted" data-color="#F97316" class="cursor-pointer">
        <span class="w-4 h-4 mr-2 inline-block" style="background:#F97316"></span>
        <span>Wasted</span>
      </li>
      <li data-field="OVERWEIGHT_OBESE" data-label="Overweight/Obese" data-color="#3B82F6" class="cursor-pointer">
        <span class="w-4 h-4 mr-2 inline-block" style="background:#3B82F6"></span>
        <span>Overweight/Obese</span>
      </li>
      <li data-field="STUNTED" data-label="Stunted" data-color="#EF4444" class="cursor-pointer">
        <span class="w-4 h-4 mr-2 inline-block" style="background:#EF4444"></span>
        <span>Stunted</span>
      </li>
    `;
  }

  activeField = 'all';
  activeColor = '#4B5563';
  
  attachLegendEvents();
  updateGradientScale();
  
  if (geoLayer) {
    geoLayer.eachLayer(layer => layer.setStyle(styleFeature(layer.feature)));
    filterMapByGradient();
  }
  
  if (!chartContainer.classList.contains('hidden')) {
    renderFullChart();
  }
}

// ===================== FULL CHART =====================
function flipToChart() {
  mapContainer.classList.add('flipped');
  chartContainer.classList.remove('hidden');
  chartContainer.classList.add('flipped');
  document.getElementById('gradient-wrapper').style.display = 'none';
  renderFullChart();
}

function flipToMap() {
  mapContainer.classList.remove('flipped');
  chartContainer.classList.add('hidden');
  chartContainer.classList.remove('flipped');
  document.getElementById('gradient-wrapper').style.display = 'block';
  setTimeout(() => map.invalidateSize(), 100);
}

function renderFullChart() {
  if (!geoData) return;

  let indicators = [];
  if (activeField === 'all') {
    if (currentPopulation === 'preschool') {
      indicators = [
        { field: 'UNDERWEIGHT', label: 'Underweight', color: '#d4a800' },
        { field: 'WASTED', label: 'Wasted', color: '#F97316' },
        { field: 'OVERWEIGHT_OBESE', label: 'Overweight/Obese', color: '#3B82F6' },
        { field: 'STUNTED', label: 'Stunted', color: '#EF4444' }
      ];
    } else {
      indicators = [
        { field: 'WASTED', label: 'Wasted', color: '#F97316' },
        { field: 'OVERWEIGHT_OBESE', label: 'Overweight/Obese', color: '#3B82F6' },
        { field: 'STUNTED', label: 'Stunted', color: '#EF4444' }
      ];
    }
  } else {
    const legendItem = Array.from(document.querySelectorAll('#legend-buttons ul li')).find(li => li.dataset.field === activeField);
    indicators = [{ 
      field: activeField, 
      label: legendItem ? legendItem.querySelector('span:last-child').innerText : activeField, 
      color: activeColor 
    }];
  }

  let filteredFeatures = geoData.features.filter(f => 
    f.properties.BARANGAY?.toUpperCase() === USER_BARANGAY
  );

  if (!isYearRange && activeYear !== null) {
    filteredFeatures = filteredFeatures.filter(f => parseInt(f.properties.YEAR) === activeYear);
  } else if (isYearRange && activeYearFrom !== null && activeYearTo !== null) {
    filteredFeatures = filteredFeatures.filter(f => {
      const year = parseInt(f.properties.YEAR);
      return year >= activeYearFrom && year <= activeYearTo;
    });
  }

  let years = [...new Set(filteredFeatures.map(f => parseInt(f.properties.YEAR)))].sort((a, b) => a - b);
  
  if (years.length === 0) {
    if (!isYearRange && activeYear !== null) {
      years = [activeYear];
    } else if (activeYearFrom !== null) {
      years = [activeYearFrom];
    }
  }

  const ctx = document.getElementById('fullChart').getContext('2d');
  if (fullChart) fullChart.destroy();

   if (years.length === 1) {
    const singleYear = years[0];
    const indicatorLabels = [];
    const indicatorValues = [];
    const indicatorColors = [];
    
    for (let i = 0; i < indicators.length; i++) {
      const ind = indicators[i];
      const f = filteredFeatures.find(f => parseInt(f.properties.YEAR) === singleYear);
      const value = f ? (f.properties[ind.field] || 0) : 0;
      
      indicatorLabels.push(ind.label);
      indicatorValues.push(value);
      indicatorColors.push(ind.color);
    }
    
    fullChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: indicatorLabels,
        datasets: [{
          label: `${singleYear}`,
          data: indicatorValues,
          backgroundColor: 'transparent',
          borderColor: indicatorColors,
          borderWidth: 1.5,
          borderRadius: 2,
          barPercentage: 0.8,
          categoryPercentage: 0.9
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { 
            callbacks: { 
              label: (ctx) => `${ctx.label}: ${ctx.raw.toFixed(1)}%` 
            } 
          },
          datalabels: {
            display: true,
            formatter: (value) => {
              if (value === 0 || value === null || value === undefined) return '';
              return value.toFixed(1) + '%';
            },
            font: { size: 10, weight: 'bold' },
            backgroundColor: 'rgba(255,255,255,0.8)',
            padding: { left: 2, right: 2, top: 1, bottom: 1 },
            borderRadius: 2,
            align: 'top',
            offset: 4,
            color: '#1f2937'
          }
        },
        scales: {
          y: {
            min: 0,
            max: 20,
            beginAtZero: true,
            ticks: {
              stepSize: 4,
              callback: (val) => val + '%',
              font: { size: 9 }
            },
            title: { display: false },
            grid: { display: true }
          },
          x: {
            ticks: {
              font: { size: 9, weight: 'bold' },
              maxRotation: 15,
              minRotation: 15
            },
            title: { display: false },
            grid: { display: false }
          }
        },
        layout: {
          padding: { top: 15, bottom: 5, left: 5, right: 5 }
        }
      },
      plugins: [ChartDataLabels]
    });
  } 
  // MULTIPLE YEARS - LINE CHART
  else {
    const datasets = indicators.map(ind => ({
      label: ind.label,
      data: years.map(year => {
        const f = filteredFeatures.find(f => parseInt(f.properties.YEAR) === year);
        return f ? (f.properties[ind.field] || 0) : 0;
      }),
      borderColor: ind.color,
      backgroundColor: 'transparent',
      borderWidth: 2,
      tension: 0.3,
      pointRadius: 5,
      pointBackgroundColor: ind.color,
      pointBorderColor: '#fff',
      pointBorderWidth: 1.5,
      fill: false
    }));

    fullChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: years,
        datasets: datasets
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: true, position: 'top', labels: { font: { size: 11 } } },
          tooltip: { 
            callbacks: { 
              label: (ctx) => `${ctx.dataset.label}: ${ctx.raw.toFixed(1)}%` 
            } 
          },
          datalabels: {
            display: activeField === 'all' ? false : true,
            formatter: (value) => {
              if (value === 0 || value === null || value === undefined) return '';
              return value.toFixed(1) + '%';
            },
            color: '#1f2937',
            font: { size: 10, weight: 'bold' },
            backgroundColor: 'rgba(255,255,255,0.85)',
            padding: { left: 4, right: 4, top: 2, bottom: 2 },
            borderRadius: 3,
            align: 'top',
            offset: 6
          }
        },
        scales: {
          y: { 
            min: 0, max: 20, beginAtZero: true,
            ticks: { stepSize: 2, callback: (val) => val + '%' },
            title: { display: true, text: 'Prevalence (%)', font: { size: 11 } }
          },
          x: { title: { display: true, text: 'Year', font: { size: 11 } } }
        }
      },
      plugins: [ChartDataLabels]
    });
  }
}

// ===================== POPULATION TOGGLE =====================
const preschoolBtn = document.getElementById('preschoolBtn');
const schoolBtn = document.getElementById('schoolBtn');

if (preschoolBtn && schoolBtn) {
  preschoolBtn.addEventListener('click', () => {
    if (currentPopulation === 'preschool') return;
    currentPopulation = 'preschool';
    preschoolBtn.classList.add('active');
    schoolBtn.classList.remove('active');
    updateLegendListOnly();
    loadData('preschool');
  });

  schoolBtn.addEventListener('click', () => {
    if (currentPopulation === 'school') return;
    currentPopulation = 'school';
    schoolBtn.classList.add('active');
    preschoolBtn.classList.remove('active');
    updateLegendListOnly();
    loadData('school');
  });
}

// ===================== VIEW TOGGLE =====================
document.getElementById('btnShowChart')?.addEventListener('click', flipToChart);
document.getElementById('btnBackToMap')?.addEventListener('click', flipToMap);

// ===================== INITIAL SETUP =====================
attachLegendEvents();
updateGradientScale();
loadData('preschool');