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

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: 'Map data © OpenStreetMap contributors'
}).addTo(map);

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

const legendItems = Array.from(document.querySelectorAll('#legend-buttons li'));
const mapContainer = document.getElementById('mapContainer');
const chartContainer = document.getElementById('chartContainer');
const realMap = document.getElementById('map');
let fullChart = null;

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
    switch(field.toUpperCase()) {
      case 'UNDERWEIGHT':
        countField = 'UNDERWEIGHT_COUNT';
        break;
      case 'WASTED':
        countField = 'WASTED_COUNT';
        break;
      case 'OVERWEIGHT_OBESE':
        countField = 'OVERWEIGHT_OBESE_COUNT';
        break;
      case 'STUNTED':
        countField = 'STUNTED_COUNT';
        break;
      default:
        return;
    }
    
    const count = feature.properties[countField] || 0;
    const measured = feature.properties.TOTAL_MEASURED || 0;
    
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
      return { color:'#444', weight:1, fillOpacity:0, fillColor:'transparent', dashArray:'2,2' };
    }
    let step = Math.floor(val / 2);
    if (step < 0) step = 0;
    if (step > 9) step = 9;
    return { color:'#000', weight:2, fillOpacity:0.8, fillColor:getGradientColor(activeColor, step+1) };
  }

  const hasData = legendItems.some(li => (props[li.dataset.field.toUpperCase()] ?? 0) > 0);
  return hasData ? { color:'#333', weight:1, fillOpacity:0.8, fillColor:'#000' } 
                 : { color:'#444', weight:1, fillOpacity:0, fillColor:'transparent', dashArray:'2,2' };
}

// ===================== FEATURE HANDLER =====================
function featureHandler(feature, layer) {
  const tooltip = document.getElementById('chart-tooltip');
  const barangayName = feature.properties.BARANGAY || 'Unknown';

  layer.on({
    mouseover(e) {
      const isMobile = window.innerWidth < 768;
      tooltip.style.display = 'block';
      tooltip.style.opacity = 1;
      tooltip.innerHTML = '';
      tooltip.style.padding = '8px';

      const title = document.createElement('div');
      title.className = 'tooltip-title';
      title.textContent = barangayName;
      title.style.fontWeight = 'bold';
      title.style.fontSize = '14px';
      title.style.marginBottom = '6px';
      tooltip.appendChild(title);

      let indicatorsToShow;
      if (activeField === 'ALL') {
        indicatorsToShow = legendItems.filter(li => li.dataset.field !== 'ALL' && li.dataset.field !== 'all');
      } else if (activeField) {
        indicatorsToShow = legendItems.filter(li => li.dataset.field.toUpperCase() === activeField);
      } else {
        indicatorsToShow = legendItems;
      }

      let hasData = indicatorsToShow.some(li => {
        const val = feature.properties[li.dataset.field.toUpperCase()] ?? 0;
        return val > 0;
      });

      if (!hasData) {
        const noDataMsg = document.createElement('div');
        noDataMsg.textContent = '⚠ No data available';
        noDataMsg.style.color = 'black';
        noDataMsg.style.fontWeight = 'normal';
        tooltip.appendChild(noDataMsg);
        return;
      }

      const chartType = (activeYear === 'All') ? 'line' : 'bar';
      let labels = (activeYear === 'All')
        ? [...new Set(geoData.features.filter(f => f.properties.BARANGAY === barangayName).map(f => f.properties.YEAR))].sort((a,b)=>a-b)
        : [activeYear];

      const datasets = indicatorsToShow.map(li => {
        const data = labels.map(y => getValue(barangayName, y, li.dataset.field));
        return {
          label: li.dataset.label,
          data,
          borderColor: li.dataset.color,
          backgroundColor: li.dataset.color,
          fill: chartType === 'bar',
          tension:0.3,
          borderWidth:2,
          spanGaps:true,
          pointRadius:3
        };
      });

      createChart(isMobile?'50vw':'200px', isMobile?'100px':'150px', labels, datasets, chartType);

      const indicatorList = document.createElement('ul');
      indicatorList.style.listStyle='none';
      indicatorList.style.padding='0';
      indicatorList.style.marginTop='6px';

      indicatorsToShow.forEach(li => {
        const value = getValue(barangayName, activeYear==='All'?labels[labels.length-1]:activeYear, li.dataset.field);
        const liItem = document.createElement('li');
        liItem.style.display='flex';
        liItem.style.alignItems='center';
        liItem.style.marginBottom='4px';
        const colorBox = document.createElement('span');
        colorBox.style.width='12px';
        colorBox.style.height='12px';
        colorBox.style.background=li.dataset.color;
        colorBox.style.display='inline-block';
        colorBox.style.marginRight='6px';
        const text = document.createElement('span');
        text.textContent=`${li.dataset.label}: ${value.toFixed(2)}%`;
        text.style.fontSize = '12px';
        liItem.appendChild(colorBox);
        liItem.appendChild(text);
        indicatorList.appendChild(liItem);
      });

      tooltip.appendChild(indicatorList);

      function getValue(barangay, year, field) {
        const f = geoData.features.find(ff => ff.properties.BARANGAY===barangay && String(ff.properties.YEAR)===String(year));
        return f ? Number(f.properties[field.toUpperCase()] ?? 0) : 0;
      }

      function createChart(width, height, labels, datasets, type) {
        const chartWrapper = document.createElement('div');
        chartWrapper.style.width=width;
        chartWrapper.style.height=height;
        chartWrapper.style.marginTop='4px';
        tooltip.appendChild(chartWrapper);
        const canvas = document.createElement('canvas');
        canvas.style.width='100%';
        canvas.style.height='100%';
        chartWrapper.appendChild(canvas);
        if (miniChart) miniChart.destroy();
        miniChart = new Chart(canvas, { type, data:{labels,datasets}, options:{
          responsive:true,
          maintainAspectRatio:false,
          plugins:{legend:{display:false},tooltip:{enabled:false},datalabels:{display:false}},
          scales:{x:{display:true},y:{beginAtZero:true,max:20,ticks:{callback:val=>val+'%',stepSize:2}}}
        }, plugins:[ChartDataLabels]});
      }
    },
    mouseout(e){
      tooltip.style.opacity=0;
      tooltip.style.display='none';
      tooltip.innerHTML='';
      if(miniChart) miniChart.destroy();
    },
  });
}

fetch('../landing_page/get_map_data.php')
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
// ===================== ADD VIEW BUTTONS =====================
const chartContainerDiv = document.getElementById('chartContainer');
const toggleButtonContainer = document.createElement('div');
toggleButtonContainer.className = 'view-toggle-container';
toggleButtonContainer.innerHTML = `
  <button id="btnPercentageView" class="view-toggle-btn active">Barangay View</button>
  <button id="btnCityTotalView" class="view-toggle-btn">City View</button>
`;
chartContainerDiv.insertBefore(toggleButtonContainer, chartContainerDiv.firstChild);

const btnPercentageView = document.getElementById('btnPercentageView');
const btnCityTotalView = document.getElementById('btnCityTotalView');

btnPercentageView.addEventListener('click', () => {
  btnPercentageView.classList.add('active');
  btnCityTotalView.classList.remove('active');
  
  const barangaySelect = document.getElementById('barangayFilter');
  barangaySelect.disabled = false;
  
  const previousBarangay = window.previousBarangay || 'all';
  if (previousBarangay === 'city_total' || previousBarangay === 'all') {
    barangaySelect.value = 'All';
    activeBarangay = 'all';
  } else {
    barangaySelect.value = previousBarangay.charAt(0).toUpperCase() + previousBarangay.slice(1);
    activeBarangay = previousBarangay;
  }
  
  currentView = 'percentage';
  
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
  
  drawLayer(activeYearFrom, activeYearTo, isYearRange, activeBarangay);
  applyLegendFilter();
  
  if (!mapContainer.classList.contains('flipped')) {
    flipToMap();
  } else {
    renderFullChart();
  }
});

btnCityTotalView.addEventListener('click', function() {
  btnCityTotalView.classList.add('active');
  btnPercentageView.classList.remove('active');
  
  const barangaySelect = document.getElementById('barangayFilter');
  barangaySelect.disabled = true;
  barangaySelect.value = 'city_total';
  activeBarangay = 'city_total';
  window.previousBarangay = 'all';
  
  drawLayer(activeYearFrom, activeYearTo, isYearRange, activeBarangay);
  applyLegendFilter();
  flipToChart();
  renderFullChart();
});

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

// ===================== CHART FLIP =====================
function flipToChart() {
  mapContainer.classList.add('flipped');
  chartContainer.classList.remove('hidden');
  chartContainer.classList.add('flipped');
  const gradientWrapper = document.getElementById('gradient-wrapper');
  if (gradientWrapper) gradientWrapper.style.display = 'none';
  
  // Check if City View is active and disable barangay dropdown
  if (activeBarangay === 'city_total') {
    const barangaySelect = document.getElementById('barangayFilter');
    barangaySelect.disabled = true;
    barangaySelect.value = 'city_total';
  }
  
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
  const noDataCell=document.createElement('div');
  noDataCell.className='gradient-cell';
  noDataCell.style.background='transparent';
  noDataCell.style.border='1px dashed #333';
  noDataCell.title='No Data';
  noDataCell.addEventListener('mouseover',()=>{activeGradientRange='nodata'; filterMapByGradient();});
  noDataCell.addEventListener('mouseout',()=>{activeGradientRange=null; filterMapByGradient();});
  noDataCell.addEventListener('click',()=>{activeGradientRange='nodata'; filterMapByGradient();});
  grid.appendChild(noDataCell);
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
  const isMultipleYears = (activeYear === "All");
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

  if (isCityTotal) {
    const cityTotalData = indicatorsToShow.map(indicator => getCityOverallTotal(indicator.dataset.field, activeYear));
    fullChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: indicatorsToShow.map(ind => ind.dataset.label),
        datasets: [{
          label: `${activeYear !== 'All' ? activeYear : 'All Years'} City Total`,
          data: cityTotalData,
          backgroundColor: indicatorsToShow.map(ind => ind.dataset.color),
          borderColor: indicatorsToShow.map(ind => ind.dataset.color),
          borderWidth: 2,
          borderRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: true }, tooltip: { callbacks: { label: (context) => `${context.dataset.label}: ${context.raw.toFixed(2)}%` } } },
        scales: { y: { beginAtZero: true, max: 20, ticks: { callback: val => val + "%", stepSize: 2 } } }
      }
    });
  } else if (isMultipleYears && isAllBarangays) {
    const datasets = indicatorsToShow.map(indicator => ({
      label: indicator.dataset.label,
      data: allYears.map(year => {
        const yearFeatures = geoData.features.filter(f => String(f.properties.YEAR) === String(year));
        let total = 0, count = 0;
        yearFeatures.forEach(f => {
          const val = f.properties[indicator.dataset.field.toUpperCase()];
          if (val !== null && !isNaN(val) && f.properties.NO_DATA !== true) { total += Number(val); count++; }
        });
        return count > 0 ? total / count : 0;
      }),
      borderColor: indicator.dataset.color,
      backgroundColor: 'transparent',
      borderWidth: 3,
      tension: 0.3,
      fill: false
    }));
    fullChart = new Chart(ctx, {
      type: 'line',
      data: { labels: allYears, datasets },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true } }, scales: { y: { beginAtZero: true, max: 20, ticks: { callback: val => val + "%", stepSize: 2 } } } }
    });
  } else if (!isMultipleYears && isAllBarangays) {
    const yearFeatures = geoData.features.filter(f => String(f.properties.YEAR) === String(activeYear));
    const datasets = indicatorsToShow.map(indicator => ({
      label: indicator.dataset.label,
      data: allBarangays.map(barangay => {
        const feature = yearFeatures.find(f => f.properties.BARANGAY === barangay);
        return feature ? Number(feature.properties[indicator.dataset.field.toUpperCase()] ?? 0) : 0;
      }),
      backgroundColor: indicator.dataset.color,
      borderColor: indicator.dataset.color,
      borderRadius: 4
    }));
    fullChart = new Chart(ctx, {
      type: 'bar',
      data: { labels: allBarangays, datasets },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true } }, scales: { y: { beginAtZero: true, max: 20, ticks: { callback: val => val + "%", stepSize: 2 } } } }
    });
  } else if (!isMultipleYears && isSpecificBarangay) {
    const yearFeatures = geoData.features.filter(f => String(f.properties.YEAR) === String(activeYear));
    const feature = yearFeatures.find(f => f.properties.BARANGAY === barangaysToShow[0]);
    const barangayData = indicatorsToShow.map(indicator => feature ? Number(feature.properties[indicator.dataset.field.toUpperCase()] ?? 0) : 0);
    fullChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: indicatorsToShow.map(ind => ind.dataset.label),
        datasets: [{ label: `${barangaysToShow[0]} (${activeYear})`, data: barangayData, backgroundColor: indicatorsToShow.map(ind => ind.dataset.color), borderRadius: 4 }]
      },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true } }, scales: { y: { beginAtZero: true, max: 20, ticks: { callback: val => val + "%", stepSize: 2 } } } }
    });
  }
}

// ===================== MAP / CHART BUTTONS =====================
const btnMapView = document.getElementById('btnMapView');
const btnChartView = document.getElementById('btnChartView');

btnMapView.addEventListener('click', () => {
  const barangaySelect = document.getElementById('barangayFilter');
  
  if (activeBarangay === 'city_total') {
    barangaySelect.disabled = false;
    const previousBarangay = window.previousBarangay || 'all';
    barangaySelect.value = previousBarangay === 'all' ? 'All' : previousBarangay.charAt(0).toUpperCase() + previousBarangay.slice(1);
    activeBarangay = previousBarangay === 'all' ? 'all' : previousBarangay;
    drawLayer(activeYearFrom, activeYearTo, isYearRange, activeBarangay);
    
    // Reset button states
    btnPercentageView.classList.add('active');
    btnCityTotalView.classList.remove('active');
  } else {
    barangaySelect.disabled = false;
  }
  
  flipToMap();
  
  if (geoLayer && activeField) {
    geoLayer.eachLayer(layer => layer.setStyle(styleFeature(layer.feature)));
    applyLegendFilter();
  }
});

btnChartView.addEventListener('click', () => {
  // Check if City View button should be active
  if (activeBarangay === 'city_total') {
    btnCityTotalView.classList.add('active');
    btnPercentageView.classList.remove('active');
    const barangaySelect = document.getElementById('barangayFilter');
    barangaySelect.disabled = true;
  }
  
  flipToChart();
  renderFullChart();
});
// Force barangay dropdown to open downward
const barangayFilter = document.getElementById('barangayFilter');
if (barangayFilter) {
  barangayFilter.addEventListener('mousedown', function(e) {
    const rect = this.getBoundingClientRect();
    const spaceBelow = window.innerHeight - rect.bottom;
    const spaceAbove = rect.top;
    if (spaceBelow < 200 && spaceAbove > spaceBelow) {
      this.style.marginTop = '100px';
      this.style.marginBottom = '-100px';
      this.addEventListener('blur', function() { this.style.marginTop = ''; this.style.marginBottom = ''; }, { once: true });
    }
  });
}