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
let activeYear = 'All', activeBarangay = 'all';
let miniChart = null;
let activeGradientRange = null;

const legendItems = Array.from(document.querySelectorAll('#legend-buttons li'));
const mapContainer = document.getElementById('mapContainer');
const chartContainer = document.getElementById('chartContainer');

// ===================== LOAD GEOJSON DATA =====================
fetch('../landing_page/get_map_data.php')
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

    // Populate Barangay dropdown
    const barangaySelect = document.getElementById('barangayFilter');
    const barangays = [...new Set(geoData.features.map(f => f.properties.BARANGAY))].sort();
    barangaySelect.innerHTML = '';
    const allBOpt = document.createElement('option');
    allBOpt.value = 'all';
    allBOpt.textContent = 'All Barangays';
    barangaySelect.appendChild(allBOpt);

    barangays.forEach(b => {
      const opt = document.createElement('option');
      opt.value = b;
      opt.textContent = b;
      barangaySelect.appendChild(opt);
    });

    activeBarangay = 'all';
    drawLayer();

    // Year filter change
    yearSelect.addEventListener('change', e => {
      activeYear = e.target.value;
      drawLayer();
    });

    // Barangay filter change
    barangaySelect.addEventListener('change', e => {
      activeBarangay = e.target.value.toLowerCase();
      drawLayer();
    });
  })
  .catch(err => console.error('Error loading map data:', err));

// ===================== DRAW LAYER =====================
function drawLayer() {
  if (!geoData) return;
  if (geoLayer) map.removeLayer(geoLayer);

  // Filter features by activeYear and activeBarangay
  let filteredFeatures = geoData.features.filter(f => {
    const yearMatch = activeYear === 'All' || String(f.properties.YEAR) === String(activeYear);
    const barangayMatch = activeBarangay === 'all' || (f.properties.BARANGAY || '').toLowerCase() === activeBarangay;
    return yearMatch && barangayMatch;
  });

  // Ensure missing barangays show as NO_DATA
  const allBarangays = [...new Set(geoData.features.map(f => f.properties.BARANGAY))];
  allBarangays.forEach(b => {
    if (!filteredFeatures.find(f => f.properties.BARANGAY === b) && 
        (activeBarangay === 'all' || activeBarangay === b.toLowerCase())) {
      const base = geoData.features.find(f => f.properties.BARANGAY === b);
      if (base) {
        const clone = JSON.parse(JSON.stringify(base));
        clone.properties.NO_DATA = true;
        filteredFeatures.push(clone);
      }
    }
  });

  const finalData = { type: "FeatureCollection", features: filteredFeatures };
  geoLayer = L.geoJSON(finalData, { style: styleFeature, onEachFeature: featureHandler }).addTo(map);

  // Update legend based on visible data
  updateLegend();
}

// ===================== STYLING =====================
function styleFeature(feature) {
  const props = feature.properties;

  if (activeField && activeColor) {
    let val = props[activeField.toUpperCase()];
    if (val === 0 || val == null || props.NO_DATA) {
      return { color:'#444', weight:1, fillOpacity:0, fillColor:'transparent', dashArray:'2,2' };
    }
    let step = Math.floor(val/2);
    if (step < 0) step = 0;
    if (step > 9) step = 9;
    return { color:'#000', weight:2, fillOpacity:0.8, fillColor:getGradientColor(activeColor, step+1) };
  }

  // Default "All" styling
  const hasData = legendItems.some(li => li.dataset.field !== 'all' && (props[li.dataset.field.toUpperCase()] ?? 0) > 0);
  return hasData ? { color:'#333', weight:1, fillOpacity:0.8, fillColor:'#000' } :
                   { color:'#444', weight:1, fillOpacity:0, fillColor:'transparent', dashArray:'2,2' };
}

// ===================== TOOLTIP + MINI CHART =====================
function featureHandler(feature, layer) {
  const tooltip = document.getElementById('chart-tooltip');

  layer.on({
    mouseover() {
      if (window.innerWidth < 768) return; // skip mobile
      tooltip.style.display = 'block';
      tooltip.style.opacity = 1;
      tooltip.style.padding = '8px';
      tooltip.innerHTML = '';

      const title = document.createElement('div');
      title.className = 'tooltip-title';
      title.textContent = feature.properties.BARANGAY || 'Unknown';
      title.style.fontWeight = 'bold';
      title.style.marginBottom = '6px';
      tooltip.appendChild(title);

      // Indicators
      const indicatorsToShow = activeField ? 
        legendItems.filter(li => li.dataset.field.toUpperCase() === activeField) :
        legendItems.filter(li => li.dataset.field !== 'all');

      // Labels & Datasets
      const labels = [activeYear === 'All' ? Math.max(...geoData.features.map(f => f.properties.YEAR || 0)) : activeYear];
      const datasets = indicatorsToShow.map(li => ({
        label: li.dataset.label,
        data: labels.map(() => Number(feature.properties[li.dataset.field.toUpperCase()] || 0)),
        borderColor: li.dataset.color,
        backgroundColor: li.dataset.color,
        fill: true,
        tension:0.3,
        borderWidth:2,
        pointRadius:3,
        spanGaps:true
      }));

      createMiniChart('300px','150px',labels,datasets,activeYear==='All'?'line':'bar');

      function createMiniChart(width,height,labels,datasets,type){
        const chartWrapper = document.createElement('div');
        chartWrapper.style.width=width;
        chartWrapper.style.height=height;
        chartWrapper.style.marginTop='4px';
        tooltip.appendChild(chartWrapper);

        const canvas = document.createElement('canvas');
        canvas.style.width='100%';
        canvas.style.height='100%';
        chartWrapper.appendChild(canvas);

        if(miniChart) miniChart.destroy();
        miniChart = new Chart(canvas,{
          type:type,
          data:{labels,datasets},
          options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{enabled:false},datalabels:{display:false}},scales:{x:{display:true},y:{beginAtZero:true,max:20,ticks:{callback:v=>v+'%',stepSize:2}}}},
          plugins:[ChartDataLabels]
        });
      }
    },

    mouseout() {
      tooltip.style.opacity=0;
      tooltip.style.display='none';
      tooltip.innerHTML='';
      if(miniChart){ miniChart.destroy(); miniChart=null; }
    },

    click() {
      if (!feature.properties.BARANGAY) return;
      document.getElementById('barangayFilter').value = feature.properties.BARANGAY;
      activeBarangay = feature.properties.BARANGAY.toLowerCase();
      drawLayer();
      flipToChart();
    }
  });
}

// ===================== LEGEND =====================
legendItems.forEach(item=>{
  item.addEventListener('click', ()=>{
    legendItems.forEach(li=>li.classList.remove('active'));
    item.classList.add('active');

    activeField = item.dataset.field==='all'?null:item.dataset.field.toUpperCase();
    activeLabel = item.dataset.label;
    activeColor = item.dataset.color;

    drawLayer();
  });
});

// Update legend to only show indicators present in current filtered data
function updateLegend(){
  legendItems.forEach(li=>{
    const field = li.dataset.field.toUpperCase();
    const hasVisible = geoLayer.getLayers().some(l=>{
      const val = l.feature.properties[field];
      return val && val>0;
    });
    li.style.display = (li.dataset.field==='all' || hasVisible) ? 'flex':'none';
  });
}

// ===================== FLIP HELPERS =====================
function flipToChart(){
  mapContainer.classList.add('flipped');
  chartContainer.classList.remove('hidden');
}

function flipToMap(){
  mapContainer.classList.remove('flipped');
  chartContainer.classList.add('hidden');
}

chartContainer.addEventListener('click', flipToMap);
chartContainer.addEventListener('touchstart', e=>{e.preventDefault(); flipToMap();},{passive:false});

// ===================== GRADIENT =====================
function hexToRgb(hex){const c=parseInt(hex.slice(1),16);return {r:(c>>16)&255,g:(c>>8)&255,b:c&255};}
function getGradientColor(baseColor,value){if(value==null) return '#999'; const ratio=Math.min(1,value/9);const rgb=hexToRgb(baseColor);const start={r:240,g:240,b:240};return `rgb(${Math.round(start.r+(rgb.r-start.r)*ratio)},${Math.round(start.g+(rgb.g-start.g)*ratio)},${Math.round(start.b+(rgb.b-start.b)*ratio)})`;}