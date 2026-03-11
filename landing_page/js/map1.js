// ===================== INITIALIZE MAP =====================
const map = L.map('map', {
  center: [8.4760268, 124.4809540],
  zoom: 11,
  zoomControl: true,
  dragging: true,         // now you can move the map
  scrollWheelZoom: true,  // zoom with mouse wheel
  doubleClickZoom: true,  // zoom by double click
  boxZoom: true,          // zoom by drawing rectangle
  touchZoom: true         // zoom on mobile pinch
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
const realMap = document.getElementById('map');
let fullChart = null;

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

    // Initial draw
    drawLayer(activeYear, activeBarangay);

    yearSelect.addEventListener('change', e => {
      activeYear = e.target.value;
      drawLayer(activeYear, activeBarangay);
    });
  })
  .catch(err => console.error('Error loading map data:', err));

// ===================== DRAW LAYER =====================
function drawLayer(selectedYear, selectedBarangay) {
  if (!geoData) return;

  const year = selectedYear || activeYear;
  const barangay = selectedBarangay || activeBarangay;

  if (geoLayer) map.removeLayer(geoLayer);

  let features = geoData.features;

  // Filter by year
  if (year !== 'All') {
    features = features.filter(f => String(f.properties.YEAR) === String(year));
  }

  // Filter by barangay
  if (barangay !== 'all') {
    features = features.filter(f => (f.properties.BARANGAY || '').trim().toLowerCase() === barangay);
  }

  geoLayer = L.geoJSON({ type: "FeatureCollection", features }, {
    style: styleFeature,
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

  const hasData = legendItems.some(li => li.dataset.field !== 'all' && (props[li.dataset.field.toUpperCase()] ?? 0) > 0);
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

      // Determine which indicators to show
      let indicatorsToShow = activeField
        ? legendItems.filter(li => li.dataset.field.toUpperCase() === activeField)
        : legendItems.filter(li => li.dataset.field !== 'all');

      // Check if there is any data
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
        return; // Stop drawing mini chart if no data
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
        liItem.appendChild(colorBox);
        liItem.appendChild(text);
        indicatorList.appendChild(liItem);
        text.style.fontSize = '12px';
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

    // Removed click handler to prevent switching to chart
  });
}
// ===================== LEGEND =====================
legendItems.forEach(item => {
  item.addEventListener('click', () => {
    legendItems.forEach(li => li.classList.remove('active'));
    item.classList.add('active');
    activeField = item.dataset.field==='all'?null:item.dataset.field.toUpperCase();
    activeLabel = item.dataset.label;
    activeColor = item.dataset.color;

    applyLegendFilter();
    if(activeField) updateGradientScale(activeColor); else document.getElementById('gradient-grid').innerHTML='';
    renderFullChart();
    updateLegendVisibility();
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
      show = val>0;
    }
    layer.setStyle({...styleFeature(layer.feature),
      opacity: show?1:0.3,
      fillOpacity: show?0.7:0.1,
      weight: show?2:1
    });
  });
}

// ===================== YEAR & BARANGAY SELECT =====================
document.getElementById('yearFilter').addEventListener('change', e=>{
  activeYear = e.target.value;
  drawLayer(activeYear, activeBarangay);
   renderFullChart(); 
});

document.getElementById('barangayFilter').addEventListener('change', e=>{
  activeBarangay = e.target.value.toLowerCase();
  drawLayer(activeYear, activeBarangay);
    renderFullChart();
});

// ===================== CHART FLIP =====================
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
    const min=i*2,max=min+1;
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

// ===================== FILTER BY GRADIENT =====================
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

  // Destroy previous chart if exists
  if (fullChart) {
    fullChart.destroy();
  }

  // Filter features by selected year and barangay
  let features = geoData.features;

  if (activeYear !== "All") {
    features = features.filter(f =>
      String(f.properties.YEAR) === String(activeYear)
    );
  }

  if (activeBarangay !== "all") {
    features = features.filter(f =>
      (f.properties.BARANGAY || "").toLowerCase() === activeBarangay
    );
  }

  // Determine if there is any data
  let hasData = features.some(f => {
    return legendItems.some(li => {
      if (li.dataset.field === "all") return false;
      const val = f.properties[li.dataset.field.toUpperCase()] ?? 0;
      return val > 0;
    });
  });

  // If no data, show a message instead of chart
  if (!hasData) {
    // Clear canvas
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);

    // Optional: draw a message in the canvas
    ctx.font = "13px Arial";
    ctx.fillStyle = "black";
    ctx.textAlign = "left";
    ctx.textBaseline = "left";
    ctx.fillText("⚠ No data available for the selected year/barangay", ctx.canvas.width/100, ctx.canvas.height/3);

    return;
  }

  

  // Prepare labels
  let labels;
  if (activeYear === "All") {
    labels = [...new Set(features.map(f => f.properties.YEAR))].sort((a,b)=>a-b);
  } else {
    labels = [activeYear];
  }

  // Determine which indicators to show
  let indicatorsToShow;
  if (activeField) {
    indicatorsToShow = legendItems.filter(li =>
      li.dataset.field.toUpperCase() === activeField
    );
  } else {
    indicatorsToShow = legendItems.filter(li =>
      li.dataset.field !== "all"
    );
  }

  // Build datasets
  const datasets = indicatorsToShow.map(li => {

    const data = labels.map(year => {
      const f = features.find(ff =>
        String(ff.properties.YEAR) === String(year)
      );

      if (!f) return 0;

      return Number(f.properties[li.dataset.field.toUpperCase()] ?? 0);
    });

    return {
      label: li.dataset.label,
      data: data,
      borderColor: li.dataset.color,
      backgroundColor: li.dataset.color,
      fill: false,
      tension: 0.3,
      borderWidth: 3,
      pointRadius: 4
    };

  });

  const chartType = (activeYear === "All") ? "line" : "bar";

  // Render chart
  fullChart = new Chart(ctx, {
    type: chartType,
    data: { labels: labels, datasets: datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: true },
        tooltip: { enabled: true }
      },
      scales: {
        y: {
          beginAtZero: true,
          max: 20,
          ticks: {
            callback: val => val + "%",
            stepSize: 2
          }
        }
      }
    }
  });

}
// ===================== MAP / CHART BUTTONS =====================
const btnMapView = document.getElementById('btnMapView');
const btnChartView = document.getElementById('btnChartView');

btnMapView.addEventListener('click', () => {
  flipToMap();  // show map
});

btnChartView.addEventListener('click', () => {

  flipToChart();

  renderFullChart();

});

function updateLegendVisibility() {

  const gradientLegend = document.getElementById("gradientLegend");

  if (!activeField || activeField === "ALL") {
    gradientLegend.style.display = "block";
  } else {
    gradientLegend.style.display = "none";
  }

}