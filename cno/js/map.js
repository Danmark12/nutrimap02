// ===================== INITIALIZE MAP =====================
const map = L.map('map', {
  center: [8.4760268, 124.4809540],
  zoom: 11,
  zoomControl: true,
  dragging: true,
  scrollWheelZoom: true,
  doubleClickZoom: true,
  boxZoom: true,
  touchZoom: true
});

// Define layers
const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: 'Map data © OpenStreetMap contributors'
});

const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
  maxZoom: 18,
  attribution: 'Tiles © Esri'
});

// Add default layer (street view)
streetLayer.addTo(map);
// Add layer control button (disabled - no functionality)
var layerControl = L.control.layers({
  "Street Map": streetLayer,
  "Satellite": satelliteLayer
}).addTo(map);

// Remove the click/hover functionality
setTimeout(() => {
    const toggle = document.querySelector('.leaflet-control-layers-toggle');
    if (toggle) {
        toggle.style.pointerEvents = 'none';
        toggle.style.cursor = 'default';
        toggle.style.opacity = '0.6';
    }
}, 100);

// ===================== VARIABLES =====================
let geoLayer, geoData;  
let activeField = null, activeColor = null, activeLabel = null;
let activeYear = 'All', activeBarangay = 'all';
let activeYearFrom = null;
let activeYearTo = null;
let isYearRange = false;
let miniChart = null;
let activeGradientRange = null;
let currentView = 'percentage';
let currentHighlightedLayer = null; // Track currently highlighted polygon

const legendItems = Array.from(document.querySelectorAll('#legend-buttons li'));
const mapContainer = document.getElementById('mapContainer');
const chartContainer = document.getElementById('chartContainer');
const realMap = document.getElementById('map');
const pageTitle = document.getElementById('pageTitle');
let fullChart = null;

// ===================== HELPER FUNCTION =====================
function formatPercentage(value) {
  if (value === null || value === undefined || isNaN(value)) return '';
  return value.toFixed(1) + '%';
}

// ===================== GRADIENT HIGHLIGHT FUNCTIONS =====================
function highlightGradientCellByValue(value) {
  // Remove any existing highlight first
  removeGradientHighlight();
  
  const cells = document.querySelectorAll('.gradient-cell');
  if (!cells.length) return;
  
  // Check for No Data
  if (value === null || value === undefined || isNaN(value)) {
    // Highlight the ND cell (first cell)
    if (cells[0]) {
      cells[0].classList.add('active-gradient-cell');
    }
    return;
  }
  
  // Calculate which gradient range the value falls into
  // Ranges are: 0-2%, 2-4%, 4-6%, 6-8%, 8-10%, 10-12%, 12-14%, 14-16%, 16-18%, 18-20%
  const rangeIndex = Math.floor(value / 2);
  
  // Valid range is 0-9 (for values 0-20)
  if (rangeIndex >= 0 && rangeIndex <= 9) {
    // +1 because first cell is ND cell
    const cellIndex = rangeIndex + 1;
    if (cells[cellIndex]) {
      cells[cellIndex].classList.add('active-gradient-cell');
    }
  }
}

function removeGradientHighlight() {
  const cells = document.querySelectorAll('.gradient-cell');
  cells.forEach(cell => {
    cell.classList.remove('active-gradient-cell');
  });
}

// ===================== HELPER: GET CITY OVERALL TOTAL (WEIGHTED AVERAGE) =====================
function getCityOverallTotal(field, year = null) {
  let features = geoData.features;
  
  if (year && year !== 'All') {
    features = features.filter(f => String(f.properties.YEAR) === String(year));
  } else if (year === null && activeYear !== 'All') {
    features = features.filter(f => String(f.properties.YEAR) === String(activeYear));
  }
  
  let totalCount = 0;
  let totalMeasured = 0;
  
  features.forEach(feature => {
    if (feature.properties.NO_APPROVED_DATA === true) return;
    
    let countField = '';
    let measuredField = '';
    
    // Check which population mode we're in
    if (currentPopulation === 'preschool') {
      switch(field.toUpperCase()) {
        case 'UNDERWEIGHT':
          countField = 'UNDERWEIGHT_COUNT';
          measuredField = 'TOTAL_MEASURED';
          break;
        case 'WASTED':
          countField = 'WASTED_COUNT';
          measuredField = 'TOTAL_MEASURED';
          break;
        case 'OVERWEIGHT_OBESE':
          countField = 'OVERWEIGHT_OBESE_COUNT';
          measuredField = 'TOTAL_MEASURED';
          break;
        case 'STUNTED':
          countField = 'STUNTED_COUNT';
          measuredField = 'TOTAL_MEASURED';
          break;
        default:
          return;
      }
    } else {
      // School population mode
      switch(field.toUpperCase()) {
        case 'WASTED':
          countField = 'WASTED_COUNT';
          measuredField = 'SCHOOL_TOTAL_MEASURED';
          break;
        case 'STUNTED':
          countField = 'STUNTED_COUNT';
          measuredField = 'SCHOOL_TOTAL_MEASURED';
          break;
        case 'OVERWEIGHT_OBESE':
          countField = 'OVERWEIGHT_OBESE_COUNT';
          measuredField = 'SCHOOL_TOTAL_MEASURED';
          break;
        default:
          return;
      }
    }
    
    const count = feature.properties[countField] || 0;
    const measured = feature.properties[measuredField] || 0;
    
    totalCount += count;
    totalMeasured += measured;
  });
  
  return totalMeasured > 0 ? (totalCount / totalMeasured) * 100 : 0;
}

// ===================== MODERN TIMELINE SLIDER =====================
function initTimelineSlider(minYear, maxYear, availableYears) {
  const track = document.getElementById('timelineTrack');
  const handleLeft = document.getElementById('timelineHandleLeft');
  const handleRight = document.getElementById('timelineHandleRight');
  const fill = document.getElementById('timelineFill');
  const yearLabelsContainer = document.getElementById('yearLabels');
  
  let currentMin = maxYear;
  let currentMax = maxYear;
  let activeHandle = null;
  
  // Populate year labels
  if (yearLabelsContainer) {
    yearLabelsContainer.innerHTML = '';
    for (let year = minYear; year <= maxYear; year++) {
      const span = document.createElement('span');
      span.textContent = year;
      if (availableYears.includes(year)) {
        span.classList.add('has-data');
        span.style.cursor = 'pointer';
        span.addEventListener('click', () => {
          currentMin = year;
          currentMax = year;
          updateFromPosition();
        });
      } else {
        span.classList.add('no-data');
        span.style.cursor = 'not-allowed';
      }
      yearLabelsContainer.appendChild(span);
    }
  }
  
  function updateFromPosition() {
    const fromPercent = ((currentMin - minYear) / (maxYear - minYear)) * 100;
    const toPercent = ((currentMax - minYear) / (maxYear - minYear)) * 100;
    
    handleLeft.style.left = `${fromPercent}%`;
    handleRight.style.left = `${toPercent}%`;
    fill.style.left = `${fromPercent}%`;
    fill.style.width = `${toPercent - fromPercent}%`;
    
    activeYearFrom = currentMin;
    activeYearTo = currentMax;
    isYearRange = (currentMin !== currentMax);
    activeYear = isYearRange ? 'All' : currentMin;
    
    // Highlight active years in labels
    if (yearLabelsContainer) {
      const spans = yearLabelsContainer.querySelectorAll('span');
      spans.forEach((span, idx) => {
        const year = minYear + idx;
        if (year >= currentMin && year <= currentMax && availableYears.includes(year)) {
          span.style.backgroundColor = '#e0e7ff';
          span.style.color = '#4f46e5';
          span.style.fontWeight = 'bold';
        } else {
          span.style.backgroundColor = '';
          span.style.color = '';
          span.style.fontWeight = availableYears.includes(year) ? '600' : 'normal';
        }
      });
    }
    
    drawLayer(activeYearFrom, activeYearTo, isYearRange, activeBarangay);
    renderFullChart();
  }
  
  function getPositionFromClientX(clientX) {
    const rect = track.getBoundingClientRect();
    let percent = (clientX - rect.left) / rect.width;
    percent = Math.max(0, Math.min(1, percent));
    return percent;
  }
  
  function getYearFromPercent(percent) {
    return Math.round(minYear + (percent * (maxYear - minYear)));
  }
  
  function onMouseMove(e) {
    if (!activeHandle) return;
    const percent = getPositionFromClientX(e.clientX);
    let newYear = getYearFromPercent(percent);
    newYear = Math.max(minYear, Math.min(maxYear, newYear));
    
    if (activeHandle === 'left' && newYear <= currentMax) {
      currentMin = newYear;
    } else if (activeHandle === 'right' && newYear >= currentMin) {
      currentMax = newYear;
    }
    updateFromPosition();
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
    const percent = getPositionFromClientX(e.clientX);
    const clickedYear = getYearFromPercent(percent);
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
      updateFromPosition();
    }
  });
  
  updateFromPosition();
}


// ===================== DRAW LAYER =====================
function drawLayer(yearFrom, yearTo, isRange, selectedBarangay) {
  if (!geoData) return;
  if (geoLayer) map.removeLayer(geoLayer);

  let features = geoData.features;

  if (isRange && yearFrom && yearTo) {
    features = features.filter(f => {
      const year = parseInt(f.properties.YEAR);
      return year >= yearFrom && year <= yearTo;
    });
  } else if (yearFrom && !isRange) {
    features = features.filter(f => String(f.properties.YEAR) === String(yearFrom));
  }

  let isCityTotal = false;
  if (selectedBarangay === 'city_total') {
    isCityTotal = true;
  } else if (selectedBarangay !== 'all') {
    features = features.filter(f => (f.properties.BARANGAY || '').trim().toLowerCase() === selectedBarangay);
  }

  geoLayer = L.geoJSON({ type: "FeatureCollection", features }, {
    style: function(feature) {
      if (isCityTotal) {
        return { color: '#000', weight: 3, fillOpacity: 0.3, fillColor: '#FFD700', dashArray: '5,5' };
      }
      return styleFeature(feature);
    },
    onEachFeature: featureHandler
  }).addTo(map);

  applyLegendFilter();
}

// ===================== STYLE FEATURES =====================
function styleFeature(feature) {
  const props = feature.properties;

  if (activeField && activeColor) {
    let val = props[activeField.toUpperCase()];
    if (val === 0 || val == null || props.NO_DATA === true) {
      return { color:'#444', weight:.5, fillOpacity:0, fillColor:'transparent', dashArray:'2,2' };
    }
    let step = Math.floor(val / 2);
    if (step < 0) step = 0;
    if (step > 9) step = 9;
    return { color:'#000', weight:1, fillOpacity:0.8, fillColor:getGradientColor(activeColor, step+1) };
  }

  const hasData = legendItems.some(li => (props[li.dataset.field.toUpperCase()] ?? 0) > 0);
  return hasData ? { color:'#333', weight:1, fillOpacity:0.8, fillColor:'#000' } 
                 : { color:'#444', weight:.5, fillOpacity:0, fillColor:'transparent', dashArray:'2,2' };
}

// ===================== FEATURE HANDLER (UPDATED WITH FIXES) =====================
function featureHandler(feature, layer) {
  const tooltip = document.getElementById('chart-tooltip');
  const barangayName = feature.properties.BARANGAY || 'Unknown';

  // Helper function to get value for a specific barangay, year, and field
  function getValue(barangay, year, field) {
    const f = geoData.features.find(ff => ff.properties.BARANGAY === barangay && String(ff.properties.YEAR) === String(year));
    // Return null if no data exists (NO_APPROVED_DATA = true), otherwise return the value (could be 0)
    if (!f || f.properties.NO_APPROVED_DATA === true || f.properties.HAS_DATA === false) {
      return null;
    }
    const val = f.properties[field.toUpperCase()];
    return (val !== null && val !== undefined) ? Number(val) : null;
  }

  // Create chart helper function (SMALLER CHARTS, THINNER BARS)
  function createChart(width, height, labels, datasets, type) {
    const chartWrapper = document.createElement('div');
    chartWrapper.style.width = width;
    chartWrapper.style.height = height;
    chartWrapper.style.marginTop = '2px';
    tooltip.appendChild(chartWrapper);
    const canvas = document.createElement('canvas');
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    chartWrapper.appendChild(canvas);
    if (miniChart) miniChart.destroy();
    
    // Make bar charts transparent with thinner border
    if (type === 'bar') {
      datasets.forEach(ds => {
        ds.backgroundColor = 'transparent';
        ds.borderColor = ds.borderColor || ds.backgroundColor;
        ds.borderWidth = 1.5;
      });
    }
    
    miniChart = new Chart(canvas, { 
      type, 
      data: { labels, datasets }, 
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: false },
          datalabels: {
            display: true,
            formatter: (value) => {
              // Show datalabel only if value is not null
              if (value === null || value === undefined) return '';
              return value.toFixed(1) + '%';
            },
            color: '#1f2937',
            font: { size: 7, weight: 'bold' },
            backgroundColor: 'rgba(255,255,255,0.85)',
            padding: { left: 2, right: 2, top: 1, bottom: 1 },
            borderRadius: 2,
            align: 'top',
            offset: 4
          }
        },
        scales: {
          x: { 
            display: true,
            ticks: { font: { size: 7 } }
          },
          y: { 
            beginAtZero: true, 
            max: 20, 
            ticks: { 
              callback: val => val + '%', 
              stepSize: 5,
              font: { size: 6 }
            }
          }
        },
        elements: {
          bar: {
            backgroundColor: 'transparent',
            borderWidth: 1.5,
            barPercentage: 0.5,
            categoryPercentage: 0.6
          }
        }
      }, 
      plugins: [ChartDataLabels]
    });
  }

  // Mouse enter event - highlight polygon and gradient cell
  layer.on('mouseover', function(e) {
    // Store currently highlighted layer
    if (currentHighlightedLayer) {
      geoLayer.resetStyle(currentHighlightedLayer);
    }
    currentHighlightedLayer = layer;
    
    // Highlight the polygon with bright border
    layer.setStyle({
      weight: 2,
      color: '#107f02',
      fillOpacity: 0.9,
      opacity: 1
    });
    
    // Bring to front
    if (!L.Browser.ie && !L.Browser.opera) {
      layer.bringToFront();
    }
    
    // Get the current value for the active field and highlight gradient cell
    let currentValue = null;
    if (activeField && activeField !== 'ALL') {
      currentValue = feature.properties[activeField.toUpperCase()];
      // Check if value is valid
      if (currentValue === 0 || currentValue == null || feature.properties.NO_DATA === true) {
        currentValue = null;
      }
    } else if (activeField === 'ALL') {
      // For "All Indicators", we need to get a representative value
      // Let's use the first indicator that has data
      const indicators = ['UNDERWEIGHT', 'WASTED', 'OVERWEIGHT_OBESE', 'STUNTED'];
      for (let ind of indicators) {
        const val = feature.properties[ind];
        if (val > 0 && val != null) {
          currentValue = val;
          break;
        }
      }
    }
    
    highlightGradientCellByValue(currentValue);
  });

  // Mouse leave event - reset styles
  layer.on('mouseout', function(e) {
    if (currentHighlightedLayer === layer) {
      geoLayer.resetStyle(layer);
      currentHighlightedLayer = null;
      removeGradientHighlight();
    }
  });

  // Tooltip code (UPDATED FIXES FOR LINE CHART)
  layer.on({
    mouseover(e) {
      const isMobile = window.innerWidth < 768;
      tooltip.style.display = 'block';
      tooltip.style.opacity = 1;
      tooltip.innerHTML = '';
      tooltip.style.padding = '4px 6px';

      // === POSITION TOOLTIP INSIDE MAP CARD ONLY ===
      if (!isMobile) {
        const mapCard = document.querySelector('.map-card');
        const mapRect = mapCard.getBoundingClientRect();
        
        let mouseX = e.originalEvent.clientX;
        let mouseY = e.originalEvent.clientY;
        
        let leftPos = mouseX - mapRect.left + 10;
        let topPos = mouseY - mapRect.top + 10;
        
        // Prevent going outside edges (smaller boundaries)
        if (leftPos + 170 > mapRect.width) {
          leftPos = mouseX - mapRect.left - 175;
        }
        if (topPos + 140 > mapRect.height) {
          topPos = mouseY - mapRect.top - 150;
        }
        if (leftPos < 3) leftPos = 3;
        if (topPos < 3) topPos = 3;
        
        tooltip.style.left = leftPos + 'px';
        tooltip.style.top = topPos + 'px';
        tooltip.style.position = 'absolute';
      } else {
        // Mobile: fixed at bottom
        tooltip.style.position = 'fixed';
        tooltip.style.bottom = '10px';
        tooltip.style.left = '10px';
        tooltip.style.right = '10px';
      }

      const title = document.createElement('div');
      title.className = 'tooltip-title';
      title.textContent = barangayName;
      title.style.fontWeight = 'bold';
      title.style.fontSize = '10px';
      title.style.marginBottom = '3px';
      tooltip.appendChild(title);

      // Get all years in selected range
      let allYearsInRange = [];
      if (isYearRange && activeYearFrom && activeYearTo) {
        for (let y = activeYearFrom; y <= activeYearTo; y++) {
          allYearsInRange.push(y);
        }
      } else if (activeYear !== 'All') {
        allYearsInRange = [parseInt(activeYear)];
      } else {
        // Get unique years for this barangay across all data
        allYearsInRange = [...new Set(geoData.features
          .filter(f => f.properties.BARANGAY === barangayName)
          .map(f => parseInt(f.properties.YEAR)))].sort((a,b)=>a-b);
      }

      // Check if multiple years (2+ years)
      const isMultipleYears = allYearsInRange.length >= 2;

      // LINE CHART FOR MULTIPLE YEARS (2+ years) - SHOW EVEN WITH NO DATA (values become 0 or null)
      if (isMultipleYears) {
        
        let indicatorsToShowForLine;
        if (activeField === 'ALL') {
          indicatorsToShowForLine = legendItems.filter(li => li.dataset.field !== 'ALL' && li.dataset.field !== 'all');
        } else if (activeField) {
          indicatorsToShowForLine = legendItems.filter(li => li.dataset.field.toUpperCase() === activeField);
        } else {
          indicatorsToShowForLine = legendItems;
        }

        // Remove UNDERWEIGHT from School mode
        if (currentPopulation === 'school') {
          indicatorsToShowForLine = indicatorsToShowForLine.filter(li => li.dataset.field !== 'UNDERWEIGHT');
        }

        // Prepare data for all years in range (including years with no data - set to 0)
        const datasets = indicatorsToShowForLine.map(indicator => {
// Store BOTH value and whether it has data
const dataValues = allYearsInRange.map(year => {
  const f = geoData.features.find(ff => 
    ff.properties.BARANGAY === barangayName && 
    parseInt(ff.properties.YEAR) === year
  );
  
  // Check if approved report EXISTS for this year
  const hasApprovedReport = f && f.properties.NO_APPROVED_DATA !== true && f.properties.HAS_DATA !== false;
  
  if (!hasApprovedReport) {
    return { value: 0, hasData: false };  // 0 for line position, but flag as no data
  }
  
  const val = f.properties[indicator.dataset.field.toUpperCase()];
  const actualValue = (val !== null && val !== undefined && !isNaN(val)) ? Number(val) : 0;
  return { value: actualValue, hasData: true };
});
          
          // Check if there's any non-zero data (to decide if we should show the chart)
          const hasAnyNonZero = dataValues.some(v => v > 0);
          
return {
  label: indicator.dataset.label,
  data: dataValues.map(d => d.value),  // Extract just the value for the line
  hasDataFlags: dataValues.map(d => d.hasData), 
            borderColor: indicator.dataset.color,
            backgroundColor: 'transparent',
            borderWidth: 1.5,
            tension: 0.3,
            pointRadius: 3,
            pointBackgroundColor: indicator.dataset.color,
            pointBorderColor: '#fff',
            pointBorderWidth: 1,
datalabels: {
  display: (activeField === 'ALL') ? false : true,
  formatter: function(value, context) {
    if (activeField === 'ALL') return '';
    const hasData = context.dataset.hasDataFlags?.[context.dataIndex];
    
    // No data exists for this year
    if (hasData === false) return 'ND';
    
    // Has data but value is 0%
    if (value === 0) return '0.0%';
    
    // Has data with positive value
    if (value > 0) return value.toFixed(1) + '%';
    
    return '';
  },
              color: '#333',
              font: { size: 6, weight: 'bold' },
              backgroundColor: 'rgba(255,255,255,0.85)',
              padding: { left: 2, right: 2, top: 1, bottom: 1 },
              borderRadius: 2,
              align: 'top',
              offset: 3
            }
          };
        }).filter(ds => ds.data.some(v => v !== undefined));
        
        if (datasets.length > 0) {
          const chartWrapper = document.createElement('div');
          chartWrapper.style.width = isMobile ? '150px' : '160px';
          chartWrapper.style.height = '100px';
          chartWrapper.style.marginBottom = '2px';
          tooltip.appendChild(chartWrapper);
          
          const canvas = document.createElement('canvas');
          canvas.style.width = '100%';
          canvas.style.height = '100%';
          chartWrapper.appendChild(canvas);
          
          if (miniChart) miniChart.destroy();
          
          miniChart = new Chart(canvas, {
            type: 'line',
            data: {
              labels: allYearsInRange,
              datasets: datasets
            },
            options: {
              responsive: true,
              maintainAspectRatio: true,
              plugins: {
                legend: { display: false },
                tooltip: { 
                  callbacks: {
                    label: (context) => `${context.dataset.label}: ${context.raw.toFixed(1)}%`
                  }
                },
                datalabels: {
                  display: (context) => {
                    // Only show datalabel if value > 0
                    return context.raw > 0;
                  }
                }
              },
              scales: {
                x: { 
                  display: true,
                  ticks: { font: { size: 7 } }
                },
                y: { 
                  display: true,
                  beginAtZero: true,
                  max: 20,
                  ticks: { 
                    callback: val => val + '%',
                    font: { size: 6 },
                    stepSize: 5
                  }
                }
              }
            },
            plugins: [ChartDataLabels]
          });
        } else {
          const noDataMsg = document.createElement('div');
          noDataMsg.textContent = '⚠ No data available';
          noDataMsg.style.color = 'black';
          noDataMsg.style.fontSize = '9px';
          noDataMsg.style.textAlign = 'center';
          tooltip.appendChild(noDataMsg);
        }
        
      } else {
        // ===== SINGLE YEAR -> BAR CHART (Show 0% if approved report exists with 0%) =====
        let indicatorsToShow;
        if (activeField === 'ALL') {
          indicatorsToShow = legendItems.filter(li => li.dataset.field !== 'ALL' && li.dataset.field !== 'all');
        } else if (activeField) {
          indicatorsToShow = legendItems.filter(li => li.dataset.field.toUpperCase() === activeField);
        } else {
          indicatorsToShow = legendItems;
        }
        
        // Remove UNDERWEIGHT from School mode
        if (currentPopulation === 'school') {
          indicatorsToShow = indicatorsToShow.filter(li => li.dataset.field !== 'UNDERWEIGHT');
        }

        // Get the selected year
        const selectedYear = activeYear !== 'All' ? activeYear : (activeYearFrom || new Date().getFullYear());
        
        // Check if ANY indicator has data (approved report exists)
        let hasAnyApprovedReport = false;
        indicatorsToShow.forEach(li => {
          const f = geoData.features.find(ff => 
            ff.properties.BARANGAY === barangayName && 
            String(ff.properties.YEAR) === String(selectedYear)
          );
          if (f && f.properties.NO_APPROVED_DATA !== true && f.properties.HAS_DATA !== false) {
            hasAnyApprovedReport = true;
          }
        });

        // If NO approved report exists for this barangay/year, show "No data"
        if (!hasAnyApprovedReport) {
          const noDataMsg = document.createElement('div');
          noDataMsg.textContent = '⚠ No data';
          noDataMsg.style.color = 'black';
          noDataMsg.style.fontSize = '9px';
          noDataMsg.style.textAlign = 'center';
          noDataMsg.style.padding = '8px 4px';
          tooltip.appendChild(noDataMsg);
          return;
        }

        const chartType = 'bar';
        let labels = [selectedYear];

        const datasets = indicatorsToShow.map(li => {
          const data = labels.map(y => {
            const f = geoData.features.find(ff => 
              ff.properties.BARANGAY === barangayName && 
              String(ff.properties.YEAR) === String(y)
            );
            // If approved report exists, return the value (could be 0)
            if (f && f.properties.NO_APPROVED_DATA !== true && f.properties.HAS_DATA !== false) {
              const val = f.properties[li.dataset.field.toUpperCase()];
              return (val !== null && val !== undefined) ? Number(val) : 0;
            }
            return null;
          });
          return {
            label: li.dataset.label,
            data,
            borderColor: li.dataset.color,
            backgroundColor: 'transparent',
            borderWidth: 1.5,
            type: 'bar'
          };
        });

        createChart(isMobile ? '40vw' : '130px', isMobile ? '80px' : '85px', labels, datasets, chartType);
      }
    },
    mouseout(e){
      tooltip.style.opacity = 0;
      tooltip.style.display = 'none';
      tooltip.innerHTML = '';
      if (miniChart) miniChart.destroy();
    },
  });
}

// ===================== LOAD INITIAL DATA =====================
fetch('../cno/get_map_data.php')
  .then(r => r.json())
  .then(data => {
    geoData = data;

    const years = [...new Set(geoData.features.map(f => f.properties.YEAR).filter(y => y))].sort((a,b)=>a-b);
    const minYear = years.length > 0 ? Math.min(...years) : 2020;
    const maxYear = years.length > 0 ? Math.max(...years) : 2026;
    
    initTimelineSlider(minYear, maxYear, years);
    activeBarangay = 'all';
    
    const allItem = legendItems.find(li => li.dataset.field === 'ALL');
    if (allItem) {
      allItem.classList.add('active');
      activeField = 'ALL';
      activeColor = '#888888';
      activeLabel = 'All Indicators';
      updateGradientScale(activeColor);
      applyLegendFilter();
    }
    
    // Force map to refresh after loading
    setTimeout(() => {
      map.invalidateSize();
    }, 300);
    
    // Also refresh on window resize
    window.addEventListener('resize', () => {
      setTimeout(() => {
        map.invalidateSize();
      }, 100);
    });
  })
  .catch(err => console.error('Error loading map data:', err));


// ===================== LEGEND =====================
legendItems.forEach(item => {
  item.addEventListener('click', () => {
    legendItems.forEach(li => li.classList.remove('active'));
    item.classList.add('active');
    activeField = item.dataset.field;
    activeLabel = item.dataset.label;
    activeColor = item.dataset.color;
    applyLegendFilter();
    updateGradientScale(activeColor);
    renderFullChart();
  });
});

// ===================== APPLY LEGEND FILTER =====================
function applyLegendFilter() {
  if(!geoLayer) return;
  geoLayer.eachLayer(layer => {
    const props = layer.feature.properties;
    let show = true;
    if(activeField){
      const val = props[activeField.toUpperCase()] ?? 0;
      show = val > 0;
    }
    layer.setStyle({...styleFeature(layer.feature),
      opacity: show?1:0.3,
      fillOpacity: show?0.7:0.1,
      weight: show?2:1
    });
  });
}

// ===================== BARANGAY SELECT =====================
document.getElementById('barangayFilter').addEventListener('change', e=>{
  activeBarangay = e.target.value.toLowerCase();
  drawLayer(activeYearFrom, activeYearTo, isYearRange, activeBarangay);
  renderFullChart();
});


function flipToChart() {
  mapContainer.classList.add('flipped');
  chartContainer.classList.remove('hidden');
  chartContainer.classList.add('flipped');
  const gradientWrapper = document.getElementById('gradient-wrapper');
  if (gradientWrapper) gradientWrapper.style.display = 'none';
  
  renderFullChart();
}


function flipToMap() {
  mapContainer.classList.remove('flipped');
  chartContainer.classList.add('hidden');
  chartContainer.classList.remove('flipped');
  const gradientWrapper = document.getElementById('gradient-wrapper');
  if (gradientWrapper) gradientWrapper.style.display = 'block';
  
  if (!activeField) {
    const allItem = legendItems.find(li => li.dataset.field === 'ALL');
    if (allItem) {
      legendItems.forEach(li => li.classList.remove('active'));
      allItem.classList.add('active');
      activeField = 'ALL';
      activeColor = '#888888';
      activeLabel = 'All Indicators';
      updateGradientScale(activeColor);
    }
  }
  
  if (geoLayer && activeField) {
    geoLayer.eachLayer(layer => layer.setStyle(styleFeature(layer.feature)));
    applyLegendFilter();
  }
  
  setTimeout(() => map.invalidateSize(), 100);
}

// ===================== GRADIENT =====================
function hexToRgb(hex){ const c=parseInt(hex.slice(1),16); return {r:(c>>16)&255,g:(c>>8)&255,b:c&255}; }

function getGradientColor(baseColor,value){
  if(value==null) return '#999';
  const ratio=Math.min(1,value/9);
  const rgb=hexToRgb(baseColor);
  const start={r:240,g:240,b:240};
  const r=Math.round(start.r+(rgb.r-start.r)*ratio);
  const g=Math.round(start.g+(rgb.g-start.g)*ratio);
  const b=Math.round(start.b+(rgb.b-start.b)*ratio);
  return `rgb(${r},${g},${b})`;
}

function updateGradientScale(baseColor){
  const grid=document.getElementById('gradient-grid');
  if(!grid) return;
  grid.innerHTML='';
  
  // Add NO DATA box FIRST (at the beginning / LOW end)
  const noDataCell=document.createElement('div');
  noDataCell.className='gradient-cell';
  noDataCell.style.background='#e5e7eb';
  noDataCell.style.backgroundImage='repeating-linear-gradient(45deg, #cbd5e1 0px, #cbd5e1 2px, #f1f5f9 2px, #f1f5f9 6px)';
  noDataCell.style.border='1px solid #cbd5e1';
  noDataCell.title='No Data';
  noDataCell.textContent = 'ND';
  noDataCell.style.display = 'flex';
  noDataCell.style.alignItems = 'center';
  noDataCell.style.justifyContent = 'center';
  noDataCell.style.fontSize = '9px';
  noDataCell.style.fontWeight = 'bold';
  noDataCell.style.color = '#475569';
  noDataCell.addEventListener('mouseover',()=>{activeGradientRange='nodata'; filterMapByGradient();});
  noDataCell.addEventListener('mouseout',()=>{activeGradientRange=null; filterMapByGradient();});
  noDataCell.addEventListener('click',()=>{activeGradientRange='nodata'; filterMapByGradient();});
  grid.appendChild(noDataCell);
  
  // Then add the 10 gradient cells (LOW to HIGH)
  for(let i=0;i<10;i++){
    const min = i * 2;
    const max = min + 2;
    const cell=document.createElement('div');
    cell.className='gradient-cell';
    cell.style.background=getGradientColor(baseColor,i+1);
    cell.title=`${min}% – ${max}%`;
    cell.addEventListener('mouseover',()=>{cell.classList.add('active-gradient-cell'); activeGradientRange={min,max}; filterMapByGradient();});
    cell.addEventListener('mouseout',()=>{cell.classList.remove('active-gradient-cell'); activeGradientRange=null; filterMapByGradient();});
    cell.addEventListener('click',()=>{activeGradientRange={min,max}; filterMapByGradient();});
    grid.appendChild(cell);
  }
}

function filterMapByGradient(){
  if(!geoLayer) return;
  geoLayer.eachLayer(layer=>{
    const props=layer.feature.properties;
    if(!activeField) return layer.setStyle(styleFeature(layer.feature));
    let val = props[activeField.toUpperCase()];
    val = (val===0 || val==null || props.NO_DATA===true)?null:val;
    let inRange = false;
    if(activeGradientRange==='nodata') inRange=val===null;
    else if(activeGradientRange) inRange = val!==null && val>=activeGradientRange.min && val<=activeGradientRange.max;
    else inRange=true;
    layer.setStyle({...styleFeature(layer.feature),fillOpacity:inRange?(val===null?0:0.8):0.1,opacity:inRange?1:0.3});
  });
}

// ===================== FULL CHART =====================
function renderFullChart() {
  if (!geoData) return;
  const ctx = document.getElementById('fullChartCanvas').getContext('2d');
  if (fullChart) fullChart.destroy();

  const allYears = [...new Set(geoData.features.map(f => f.properties.YEAR))].sort((a, b) => a - b);
  const isMultipleYears = (activeYear === "All" || isYearRange);
  const allBarangays = [...new Set(geoData.features.map(f => f.properties.BARANGAY))].sort();
  const isCityTotal = (activeBarangay === 'city_total');
  const isAllBarangays = (activeBarangay === 'all');
  const isSpecificBarangay = (activeBarangay !== 'all' && activeBarangay !== 'city_total');
  
  let barangaysToShow = allBarangays;
  if (isSpecificBarangay) barangaysToShow = barangaysToShow.filter(b => b.toLowerCase() === activeBarangay);

  let indicatorsToShow;
  if (activeField === 'ALL') {
    indicatorsToShow = legendItems.filter(li => li.dataset.field !== 'ALL' && li.dataset.field !== 'all');
  } else if (activeField) {
    indicatorsToShow = legendItems.filter(li => li.dataset.field.toUpperCase() === activeField);
  } else {
    indicatorsToShow = legendItems;
  }
  
  // Remove UNDERWEIGHT from School mode
  if (currentPopulation === 'school') {
    indicatorsToShow = indicatorsToShow.filter(li => li.dataset.field !== 'UNDERWEIGHT');
  }

  // Helper function to check if approved report exists for a barangay/year
  function hasApprovedReport(barangay, year) {
    const feature = geoData.features.find(f => 
      f.properties.BARANGAY === barangay && 
      String(f.properties.YEAR) === String(year)
    );
    return feature && feature.properties.NO_APPROVED_DATA !== true && feature.properties.HAS_DATA !== false;
  }

  // Helper function to get indicator value (returns null if no approved report)
  function getIndicatorValue(barangay, year, field) {
    const feature = geoData.features.find(f => 
      f.properties.BARANGAY === barangay && 
      String(f.properties.YEAR) === String(year)
    );
    
    // No approved report exists
    if (!feature || feature.properties.NO_APPROVED_DATA === true || feature.properties.HAS_DATA === false) {
      return null;
    }
    
    // Approved report exists, return value (could be 0)
    const val = feature.properties[field.toUpperCase()];
    return (val !== null && val !== undefined) ? Number(val) : 0;
  }

  // Determine which measured field to use based on population mode
  const measuredField = currentPopulation === 'preschool' ? 'TOTAL_MEASURED' : 'SCHOOL_TOTAL_MEASURED';

  if (isCityTotal) {
    const cityTotalData = indicatorsToShow.map(indicator => getCityOverallTotal(indicator.dataset.field, activeYear));
    fullChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: indicatorsToShow.map(ind => ind.dataset.label),
        datasets: [{
          label: `${activeYear !== 'All' ? activeYear : 'All Years'} City View`,
          data: cityTotalData,
          backgroundColor: 'transparent',
          borderColor: indicatorsToShow.map(ind => ind.dataset.color),
          borderWidth: 2,
          borderRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'point',
          intersect: true,
          axis: 'xy'
        },
        hover: {
          mode: 'point',
          intersect: true,
          animationDuration: 0
        },
        plugins: { 
          legend: { display: false }, 
          tooltip: { 
            callbacks: { label: (context) => `${context.raw.toFixed(2)}%` },
            position: 'nearest',
            caretSize: 5,
            caretPadding: 5,
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleColor: 'white',
            bodyColor: 'white',
            borderColor: '#017432',
            borderWidth: 1
          },
          datalabels: {
            display: true,
            formatter: (value) => formatPercentage(value),
            color: '#1f2937',
            font: { size: 10, weight: 'bold' },
            backgroundColor: 'rgba(255,255,255,0.85)',
            padding: { left: 4, right: 4, top: 2, bottom: 2 },
            borderRadius: 3,
            align: 'top',
            offset: 6
          }
        },
        scales: { y: { beginAtZero: true, max: 20, ticks: { callback: val => val + "%", stepSize: 2 } } }
      },
      plugins: [ChartDataLabels]
    });
  } 
  
  // ===== LINE CHART FOR MULTIPLE YEARS + ALL BARANGAYS =====
  else if (isMultipleYears && isAllBarangays) {
    const isAllSelected = (activeField === 'ALL');
    
    let yearsToShow = allYears;
    if (isYearRange && activeYearFrom && activeYearTo) {
      yearsToShow = allYears.filter(year => year >= activeYearFrom && year <= activeYearTo);
    } else if (activeYear !== 'All') {
      yearsToShow = [parseInt(activeYear)];
    }
    
    const datasets = indicatorsToShow.map(indicator => {
      // Store both value and hasData flag for each year
      const yearData = yearsToShow.map(year => {
        const yearFeatures = geoData.features.filter(f => String(f.properties.YEAR) === String(year));
        let total = 0, count = 0;
        let hasAnyData = false;
        
        yearFeatures.forEach(f => {
          // Check if approved report exists
          if (f.properties.NO_APPROVED_DATA !== true && f.properties.HAS_DATA !== false) {
            const val = f.properties[indicator.dataset.field.toUpperCase()];
            if (val !== null && !isNaN(val)) { 
              total += Number(val); 
              count++;
              hasAnyData = true;
            }
          }
        });
        
        if (!hasAnyData) {
          return { value: 0, hasData: false };  // No data - line at 0, label shows "ND"
        }
        return { value: count > 0 ? total / count : 0, hasData: true };
      });
      
      return {
        label: indicator.dataset.label,
        data: yearData.map(d => d.value),  // Values for line position
        hasDataFlags: yearData.map(d => d.hasData),  // Flags for labels
        borderColor: indicator.dataset.color,
        backgroundColor: 'transparent',
        borderWidth: 2,
        tension: 0.3,
        pointRadius: 6,
        pointHoverRadius: 8,
        pointBackgroundColor: indicator.dataset.color,
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        fill: false,
datalabels: {
  display: isAllSelected ? false : true,
formatter: function(value, context) {
  if (isAllSelected) return '';
  const hasData = context.dataset.hasDataFlags?.[context.dataIndex];
            
            // No data exists - show ND
            if (hasData === false) return 'ND';
            
            // Has data but value is 0%
            if (value === 0) return '0.0%';
            
            // Has data with positive value
            if (value > 0) return value.toFixed(1) + '%';
            
            return '';
          },
          color: '#333',
          font: { size: 10, weight: 'bold' },
          backgroundColor: 'rgba(255,255,255,0.9)',
          padding: { left: 3, right: 3, top: 2, bottom: 2 },
          borderRadius: 3,
          align: 'top',
          offset: 8
        }
      };
    });
    
    fullChart = new Chart(ctx, {
      type: 'line',
      data: { labels: yearsToShow, datasets }, 
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        interaction: {
          mode: 'point',
          intersect: true,
          axis: 'xy'
        },
        hover: {
          mode: 'point',
          intersect: true,
          animationDuration: 0
        },
        plugins: { 
          legend: { display: true, position: 'top', labels: { font: { size: 11 } } },
          tooltip: { 
            callbacks: { 
              label: (context) => {
                const hasData = context.dataset.hasDataFlags?.[context.dataIndex];
                if (hasData === false) return `${context.dataset.label}: No Data`;
                return `${context.dataset.label}: ${context.raw.toFixed(1)}%`;
              }
            },
            position: 'nearest',
            caretSize: 5,
            caretPadding: 5,
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleColor: 'white',
            bodyColor: 'white',
            borderColor: '#017432',
            borderWidth: 1
          },
          datalabels: { display: (context) => activeField !== 'ALL' }
        }, 
        scales: { y: { beginAtZero: true, max: 20, ticks: { callback: val => val + "%", stepSize: 2 } } }
      },
      plugins: [ChartDataLabels]
    });
  } 
  // ===== LINE CHART FOR MULTIPLE YEARS + SPECIFIC BARANGAY =====
  else if (isMultipleYears && isSpecificBarangay) {
    const selectedBarangayName = barangaysToShow[0];
    const isAllSelected = (activeField === 'ALL');
    
    let yearsToShow = allYears;
    if (isYearRange && activeYearFrom && activeYearTo) {
      yearsToShow = allYears.filter(year => year >= activeYearFrom && year <= activeYearTo);
    } else if (activeYear !== 'All') {
      yearsToShow = [parseInt(activeYear)];
    }
    
    // Prepare data for ALL years (including those with no report)
    const datasetsData = indicatorsToShow.map(indicator => ({
      label: indicator.dataset.label,
      color: indicator.dataset.color,
      values: [],
      hasDataFlags: []
    }));
    
    yearsToShow.forEach(year => {
      const hasReport = hasApprovedReport(selectedBarangayName, year);
      
      indicatorsToShow.forEach((indicator, idx) => {
        if (!hasReport) {
          // No report exists - add ND
          datasetsData[idx].values.push(0);  // 0 for line position
          datasetsData[idx].hasDataFlags.push(false);  // Flag as no data
        } else {
          // Has report - get value (could be 0)
          const value = getIndicatorValue(selectedBarangayName, year, indicator.dataset.field);
          datasetsData[idx].values.push(value !== null ? value : 0);
          datasetsData[idx].hasDataFlags.push(true);
        }
      });
    });
    
    const datasets = datasetsData.map(ds => ({
      label: ds.label,
      data: ds.values,
      hasDataFlags: ds.hasDataFlags,
      borderColor: ds.color,
      backgroundColor: 'transparent',
      borderWidth: 2,
      tension: 0.3,
      pointRadius: 6,
      pointHoverRadius: 8,
      pointBackgroundColor: ds.color,
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      fill: false,
datalabels: isAllSelected ? { display: false } : {
  display: true,
formatter: function(value, context) {
  if (isAllSelected) return '';
  const hasData = context.dataset.hasDataFlags?.[context.dataIndex];
          
          // No data exists - show ND
          if (hasData === false) return 'ND';
          
          // Has data but value is 0%
          if (value === 0) return '0.0%';
          
          // Has data with positive value
          if (value > 0) return value.toFixed(1) + '%';
          
          return '';
        },
        color: '#333',
        font: { size: 10, weight: 'bold' },
        backgroundColor: 'rgba(255,255,255,0.9)',
        padding: { left: 3, right: 3, top: 2, bottom: 2 },
        borderRadius: 3,
        align: 'top',
        offset: 8
      }
    }));
    
    fullChart = new Chart(ctx, {
      type: 'line',
      data: { labels: yearsToShow, datasets },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        interaction: {
          mode: 'point',
          intersect: true,
          axis: 'xy'
        },
        hover: {
          mode: 'point',
          intersect: true,
          animationDuration: 0
        },
        plugins: { 
          legend: { display: datasets.length > 1, position: 'top', labels: { font: { size: 11 } } },
          tooltip: { 
            callbacks: { 
              label: (context) => {
                const hasData = context.dataset.hasDataFlags?.[context.dataIndex];
                if (hasData === false) return `${context.dataset.label}: No Data`;
                return `${context.dataset.label}: ${context.raw.toFixed(1)}%`;
              }
            },
            position: 'nearest',
            caretSize: 5,
            caretPadding: 5,
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleColor: 'white',
            bodyColor: 'white',
            borderColor: '#017432',
            borderWidth: 1
          },
          datalabels: { display: (context) => activeField !== 'ALL' },
          title: {
            display: true,
            text: `${selectedBarangayName} - Trend Over Years`,
            position: 'top',
            font: { size: 12, weight: 'normal' },
            color: '#555'
          }
        }, 
        scales: { 
          y: { beginAtZero: true, max: 20, ticks: { callback: val => val + "%", stepSize: 2 } },
          x: { title: { display: true, text: 'Year', font: { size: 10 } } }
        }
      },
      plugins: [ChartDataLabels]
    });
  } 
  // ===== SINGLE YEAR + ALL BARANGAYS (BAR CHART) =====
  else if (!isMultipleYears && isAllBarangays) {
    const isAllLegendSelected = (activeField === 'ALL');
    const selectedYear = activeYear !== 'All' ? activeYear : (activeYearFrom || new Date().getFullYear());
    
    if (isAllLegendSelected) {
      const weightedData = indicatorsToShow.map(indicator => {
        const yearFeatures = geoData.features.filter(f => String(f.properties.YEAR) === String(selectedYear));
        let totalCount = 0, totalMeasured = 0;
        
        yearFeatures.forEach(feature => {
          // Skip if no approved report
          if (feature.properties.NO_APPROVED_DATA === true) return;
          
          let countField = '';
          switch(indicator.dataset.field.toUpperCase()) {
            case 'UNDERWEIGHT': countField = 'UNDERWEIGHT_COUNT'; break;
            case 'WASTED': countField = 'WASTED_COUNT'; break;
            case 'OVERWEIGHT_OBESE': countField = 'OVERWEIGHT_OBESE_COUNT'; break;
            case 'STUNTED': countField = 'STUNTED_COUNT'; break;
            default: return;
          }
          
          totalCount += feature.properties[countField] || 0;
          totalMeasured += feature.properties[measuredField] || 0;
        });
        
        return totalMeasured > 0 ? (totalCount / totalMeasured) * 100 : 0;
      });
      
      fullChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: indicatorsToShow.map(ind => ind.dataset.label),
          datasets: [{
            label: `${selectedYear} City Data (%)`,
            data: weightedData,
            backgroundColor: 'transparent',
            borderColor: indicatorsToShow.map(ind => ind.dataset.color),
            borderWidth: 2,
            borderRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            mode: 'point',
            intersect: true,
            axis: 'xy'
          },
          hover: {
            mode: 'point',
            intersect: true,
            animationDuration: 0
          },
          plugins: { 
            legend: { display: false },
            tooltip: { 
              callbacks: { label: (context) => `${context.raw.toFixed(2)}%` },
              position: 'nearest',
              caretSize: 5,
              caretPadding: 5,
              backgroundColor: 'rgba(0,0,0,0.8)',
              titleColor: 'white',
              bodyColor: 'white',
              borderColor: '#017432',
              borderWidth: 1
            },
            title: { display: true, text: `${selectedYear} City Data`, position: 'top', font: { size: 12 } },
            datalabels: {
              display: true,
              formatter: (value) => formatPercentage(value),
              color: '#1f2937',
              font: { size: 10, weight: 'bold' },
              backgroundColor: 'rgba(255,255,255,0.85)',
              padding: { left: 4, right: 4, top: 2, bottom: 2 },
              borderRadius: 3,
              align: 'top',
              offset: 6
            }
          },
          scales: { y: { beginAtZero: true, max: 20, ticks: { callback: val => val + "%", stepSize: 2 } } }
        },
        plugins: [ChartDataLabels]
      });
    } else {
      const yearFeatures = geoData.features.filter(f => String(f.properties.YEAR) === String(selectedYear));
      
      const datasets = indicatorsToShow.map(indicator => ({
        label: indicator.dataset.label,
        data: allBarangays.map(barangay => {
          const feature = yearFeatures.find(f => f.properties.BARANGAY === barangay);
          // Return null if no approved report, otherwise return value (could be 0)
          if (!feature || feature.properties.NO_APPROVED_DATA === true || feature.properties.HAS_DATA === false) {
            return null;
          }
          const val = feature.properties[indicator.dataset.field.toUpperCase()];
          return (val !== null && val !== undefined) ? Number(val) : 0;
        }),
        backgroundColor: 'transparent',
        borderColor: indicator.dataset.color,
        borderWidth: 2,
        borderRadius: 4,
        type: 'bar'
      }));
      
      fullChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: allBarangays, datasets },
        options: { 
          responsive: true, 
          maintainAspectRatio: false,
          interaction: {
            mode: 'point',
            intersect: true,
            axis: 'xy'
          },
          hover: {
            mode: 'point',
            intersect: true,
            animationDuration: 0
          },
          plugins: { 
            legend: { display: true },
            tooltip: { 
              callbacks: { 
                label: (context) => {
                  const value = context.raw;
                  if (value === null) return 'No data available';
                  return `${context.dataset.label}: ${value.toFixed(1)}%`;
                }
              },
              position: 'nearest',
              caretSize: 5,
              caretPadding: 5,
              backgroundColor: 'rgba(0,0,0,0.8)',
              titleColor: 'white',
              bodyColor: 'white',
              borderColor: '#017432',
              borderWidth: 1
            },
            datalabels: {
              display: true,
              formatter: (value) => {
                if (value === null) return 'ND';
                return value.toFixed(1) + '%';
              },
              color: '#1f2937',
              font: { size: 9, weight: 'bold' },
              backgroundColor: 'rgba(255,255,255,0.85)',
              padding: { left: 2, right: 2, top: 1, bottom: 1 },
              borderRadius: 2,
              align: 'top',
              offset: 4
            }
          }, 
          scales: { 
            y: { 
              beginAtZero: true, 
              max: 20, 
              ticks: { callback: val => val + "%", stepSize: 2 } 
            } 
          } 
        },
        plugins: [ChartDataLabels]
      });
    }
  } 
  // ===== SINGLE YEAR + SPECIFIC BARANGAY (BAR CHART) =====
  else if (!isMultipleYears && isSpecificBarangay) {
    const selectedBarangayName = barangaysToShow[0];
    const selectedYear = activeYear !== 'All' ? activeYear : (activeYearFrom || new Date().getFullYear());
    
    const hasReport = hasApprovedReport(selectedBarangayName, selectedYear);
    
    let barangayData;
    if (!hasReport) {
      // No report exists - all null
      barangayData = indicatorsToShow.map(() => null);
    } else {
      // Has report - get values (could be 0)
      barangayData = indicatorsToShow.map(indicator => 
        getIndicatorValue(selectedBarangayName, selectedYear, indicator.dataset.field)
      );
    }
    
    fullChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: indicatorsToShow.map(ind => ind.dataset.label),
        datasets: [{ 
          label: hasReport ? `${selectedBarangayName} (${selectedYear})` : `${selectedBarangayName} - No Data Available`, 
          data: barangayData, 
          backgroundColor: 'transparent',
          borderColor: indicatorsToShow.map(ind => ind.dataset.color),
          borderWidth: 2,
          borderRadius: 4 
        }]
      },
      options: { 
        responsive: true, 
        maintainAspectRatio: false,
        interaction: {
          mode: 'point',
          intersect: true,
          axis: 'xy'
        },
        hover: {
          mode: 'point',
          intersect: true,
          animationDuration: 0
        },
        plugins: { 
          legend: { display: true },
          tooltip: { 
            callbacks: { 
              label: (context) => {
                const value = context.raw;
                if (value === null) return 'No data available';
                return `${context.dataset.label}: ${value.toFixed(1)}%`;
              }
            },
            position: 'nearest',
            caretSize: 5,
            caretPadding: 5,
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleColor: 'white',
            bodyColor: 'white',
            borderColor: '#017432',
            borderWidth: 1
          },
          datalabels: {
            display: true,
            formatter: (value) => {
              if (value === null) return 'ND';
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
            beginAtZero: true, 
            max: 20, 
            ticks: { callback: val => val + "%", stepSize: 2 } 
          } 
        } 
      },
      plugins: [ChartDataLabels]
    });
  }
}

// ===================== MAP / CHART BUTTONS =====================
const btnMapView = document.getElementById('btnMapView');
const btnChartView = document.getElementById('btnChartView');

// Function to handle button highlight
function setActiveViewButton(activeButton) {
  btnMapView.classList.remove('active');
  btnChartView.classList.remove('active');
  activeButton.classList.add('active');
}

btnMapView.addEventListener('click', () => {
  setActiveViewButton(btnMapView);
  const barangaySelect = document.getElementById('barangayFilter');
  barangaySelect.disabled = false;
  
  flipToMap();
  
  if (geoLayer && activeField) {
    geoLayer.eachLayer(layer => layer.setStyle(styleFeature(layer.feature)));
    applyLegendFilter();
  }
});

btnChartView.addEventListener('click', () => {
  setActiveViewButton(btnChartView);
  const barangaySelect = document.getElementById('barangayFilter');
  barangaySelect.disabled = false;
  
  flipToChart();
  renderFullChart();
});


// ===================== POPULATION TOGGLE (PRESCHOOL / SCHOOL) =====================
let currentPopulation = 'preschool';

const preschoolBtn = document.getElementById('preschoolBtn');
const schoolBtn = document.getElementById('schoolBtn');

function loadPreschoolData() {
    fetch('../cno/get_map_data.php')
        .then(r => r.json())
        .then(data => {
            geoData = data;
            
            // Reset years and timeline
            const years = [...new Set(geoData.features.map(f => f.properties.YEAR).filter(y => y))].sort((a,b)=>a-b);
            const minYear = years.length > 0 ? Math.min(...years) : 2020;
            const maxYear = years.length > 0 ? Math.max(...years) : 2026;
            
            // Re-initialize timeline with new data
            initTimelineSlider(minYear, maxYear, years);
            
            // Reset barangay filter
            activeBarangay = 'all';
            document.getElementById('barangayFilter').value = 'All';
            
            // Redraw map
            drawLayer(activeYearFrom, activeYearTo, isYearRange, activeBarangay);
            renderFullChart();
            
            // Update legend to Preschool
            updateLegendToPreschool();
        })
        .catch(err => console.error('Error loading preschool data:', err));
}

function loadSchoolData() {
    fetch('../cno/get_school_data.php')
        .then(r => r.json())
        .then(data => {
            geoData = data;
            
            // Reset years and timeline
            const years = [...new Set(geoData.features.map(f => f.properties.YEAR).filter(y => y))].sort((a,b)=>a-b);
            const minYear = years.length > 0 ? Math.min(...years) : 2020;
            const maxYear = years.length > 0 ? Math.max(...years) : 2026;
            
            // Re-initialize timeline with new data
            initTimelineSlider(minYear, maxYear, years);
            
            // Reset barangay filter
            activeBarangay = 'all';
            document.getElementById('barangayFilter').value = 'All';
            
            // Redraw map
            drawLayer(activeYearFrom, activeYearTo, isYearRange, activeBarangay);
            renderFullChart();
            
            // Update legend to School
            updateLegendToSchool();
        })
        .catch(err => console.error('Error loading school data:', err));
}

function updateLegendToPreschool() {
    const legendList = document.getElementById('legend-buttons');
    legendList.innerHTML = `
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
    `;
    reattachLegendEvents();
    
    // Reset to All Indicators
    activeField = 'ALL';
    activeColor = '#888888';
    activeLabel = 'All Indicators';
    updateGradientScale(activeColor);
    applyLegendFilter();
}

function updateLegendToSchool() {
    const legendList = document.getElementById('legend-buttons');
    legendList.innerHTML = `
        <li data-field="ALL" data-label="All Indicators" data-color="#888888" class="active">
            <span class="legend-dot" style="background:#888888"></span>
            All Indicators
        </li>
        <li data-field="WASTED" data-label="Wasted" data-color="#F97316">
            <span class="legend-dot" style="background:#F97316"></span>
            Wasted
        </li>
        <li data-field="STUNTED" data-label="Stunted" data-color="#EF4444">
            <span class="legend-dot" style="background:#EF4444"></span>
            Stunted
        </li>
        <li data-field="OVERWEIGHT_OBESE" data-label="Overweight/Obese" data-color="#3B82F6">
            <span class="legend-dot" style="background:#3B82F6"></span>
            Overweight / Obese
        </li>
    `;
    reattachLegendEvents();
    
    // Reset to All Indicators
    activeField = 'ALL';
    activeColor = '#888888';
    activeLabel = 'All Indicators';
    updateGradientScale(activeColor);
    applyLegendFilter();
}

function reattachLegendEvents() {
    const legendItems = Array.from(document.querySelectorAll('#legend-buttons li'));
    legendItems.forEach(item => {
        item.addEventListener('click', () => {
            legendItems.forEach(li => li.classList.remove('active'));
            item.classList.add('active');
            activeField = item.dataset.field;
            activeLabel = item.dataset.label;
            activeColor = item.dataset.color;
            applyLegendFilter();
            updateGradientScale(activeColor);
            renderFullChart();
        });
    });
}

// Add button event listeners if buttons exist
if (preschoolBtn && schoolBtn) {
    preschoolBtn.addEventListener('click', () => {
        if (currentPopulation === 'school') {
            currentPopulation = 'preschool';
            // Update button styles
            preschoolBtn.style.background = '#017432';
            preschoolBtn.style.color = 'white';
            schoolBtn.style.background = 'transparent';
            schoolBtn.style.color = '#6b7280';
            loadPreschoolData();
        }
    });
    
    schoolBtn.addEventListener('click', () => {
        if (currentPopulation === 'preschool') {
            currentPopulation = 'school';
            // Update button styles
            schoolBtn.style.background = '#017432';
            schoolBtn.style.color = 'white';
            preschoolBtn.style.background = 'transparent';
            preschoolBtn.style.color = '#6b7280';
            loadSchoolData();
        }
    });
}