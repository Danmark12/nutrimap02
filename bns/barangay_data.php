<?php
// barangay_data.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'BNS') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user info
$stmtUser = $pdo->prepare("SELECT barangay, user_type FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);
$barangay = $user['barangay'];

// Sorting
$sort = $_GET['sort'] ?? 'new';
$orderSQL = ($sort === 'az') ? " ORDER BY b.title ASC " : " ORDER BY r.report_date DESC, r.report_time DESC ";

// Fetch available years
$stmtYears = $pdo->prepare("SELECT DISTINCT year FROM bns_reports WHERE barangay = ? ORDER BY year DESC");
$stmtYears->execute([$barangay]);
$availableYears = $stmtYears->fetchAll(PDO::FETCH_COLUMN);
$selectedYear = $_GET['year'] ?? ($availableYears[0] ?? null);

// Fetch reports for selected year
$reportsByQuarter = [
    'Q1' => [], 'Q2' => [], 'Q3' => [], 'Q4' => []
];

if ($selectedYear) {
    $stmt = $pdo->prepare("
        SELECT r.id, r.report_date, b.title AS report_title, QUARTER(r.report_date) AS quarter
        FROM bns_reports b
        JOIN reports r ON r.id = b.report_id
        LEFT JOIN report_archives ra ON r.id = ra.report_id AND ra.is_archived = 1
        WHERE r.status='Approved' AND r.user_id=? AND b.year=? AND ra.id IS NULL
        $orderSQL
    ");
    $stmt->execute([$userId, $selectedYear]);
    $allReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allReports as $rep) {
        $q = 'Q' . $rep['quarter'];
        $reportsByQuarter[$q][] = $rep;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>BNS | Barangay Reports</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'DM Sans', Arial, Helvetica, sans-serif;
    background: #f5f5f5;
}

.layout {
    display: flex;
    height: 100vh;
    flex-direction: column;
}

.body-layout {
    flex: 1;
    display: flex;
    overflow: hidden;
}

.content {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
}

/* Top Bar */
.top-bar {
    background: white;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.top-bar h2 {
    font-size: 18px;
    color: #333;
    font-weight: normal;
}

.top-bar h2 i {
    color: #009688;
    margin-right: 8px;
}

.controls {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.controls select {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    background: white;
    cursor: pointer;
}

.controls label {
    font-size: 14px;
    color: #555;
}

.add-btn {
    background: #009688;
    color: white;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.add-btn:hover {
    background: #00796b;
}

/* Year Slider Styles */
.year-slider-container {
    background: white;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.year-slider-container label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: #666;
    margin-bottom: 10px;
    display: block;
}

.year-labels {
    display: flex;
    justify-content: space-between;
    font-size: 10.5px;
    color: #9ca3af;
    margin-bottom: 8px;
    font-weight: 500;
    flex-wrap: wrap;
}

.year-labels span {
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 4px;
    transition: all 0.2s;
    font-size: 10px;
}

.year-labels span.has-data {
    color: #374151;
    font-weight: 600;
}

.year-labels span.has-data:hover {
    background: #e0e7ff;
    color: #4f46e5;
}

.year-labels span.active-year {
    background: #e0e7ff;
    color: #4f46e5;
    font-weight: bold;
}

.timeline-track-outer {
    position: relative;
    height: 6px;
    background: #e5e7eb;
    border-radius: 99px;
    cursor: pointer;
    margin: 15px 0 10px;
}

#timelineFill {
    position: absolute;
    height: 6px;
    background: linear-gradient(90deg, #017432, #02a046);
    border-radius: 99px;
    pointer-events: none;
}

.timeline-handle {
    position: absolute;
    width: 16px;
    height: 16px;
    background: #fff;
    border: 2.5px solid #017432;
    border-radius: 50%;
    top: -5px;
    cursor: grab;
    box-shadow: 0 1px 5px rgba(0,0,0,0.15);
    transition: transform 0.1s, box-shadow 0.1s;
}

.timeline-handle:hover {
    transform: scale(1.2);
    box-shadow: 0 2px 8px rgba(1,116,50,0.3);
}

/* Population Toggle */
.population-toggle {
    background: white;
    padding: 10px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    gap: 8px;
}

.population-toggle button {
    flex: 1;
    border: 1.5px solid #e5e7eb;
    background: #f9fafb;
    color: #6b7280;
    font-family: 'DM Sans', Arial, sans-serif;
    font-size: 13px;
    font-weight: 500;
    padding: 8px 0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.population-toggle button:hover {
    border-color: #d1d5db;
    background: #fff;
    color: #111827;
}

.population-toggle button.active {
    background: #017432;
    border-color: #017432;
    color: #fff;
    box-shadow: 0 2px 8px rgba(1,116,50,0.25);
}

/* Legend Gradient */
.legend-section {
    background: white;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.legend-header {
    padding: 12px 16px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #6b7280;
    letter-spacing: 0.05em;
}

.legend-list {
    list-style: none;
    padding: 8px;
    margin: 0;
}

.legend-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    transition: background 0.12s, border-color 0.12s;
    border: 1.5px solid transparent;
    user-select: none;
    margin-bottom: 4px;
}

.legend-list li:hover {
    background: #f9fafb;
}

.legend-list li.active {
    background: #f9fafb;
    border-color: #e5e7eb;
    color: #111827;
    font-weight: 600;
}

.legend-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    flex-shrink: 0;
    border: 2px solid rgba(255,255,255,0.6);
    box-shadow: 0 0 0 1.5px rgba(0,0,0,0.1);
}

/* Gradient Bar */
.gradient-bar-wrap {
    padding: 10px 16px;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
}

.gradient-grid {
    display: grid;
    grid-template-columns: repeat(11, 1fr);
    gap: 3px;
}

.gradient-cell {
    height: 18px;
    border-radius: 3px;
    cursor: pointer;
    transition: transform 0.1s;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8px;
    font-weight: bold;
    color: #475569;
}

.gradient-cell:hover {
    transform: scaleY(1.25);
}

.gradient-label {
    font-size: 9px;
    color: #6b7280;
    text-align: center;
    margin-top: 6px;
}

/* Quarter Sections */
.quarter {
    background: white;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-bottom: 15px;
    overflow: hidden;
}

.quarter-header {
    padding: 15px 20px;
    cursor: pointer;
    background: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
}

.quarter-header:hover {
    background: #f9f9f9;
}

.quarter-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.quarter-header h3 i {
    color: #009688;
    margin-right: 8px;
}

.badge {
    background: #e0e0e0;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
    color: #555;
}

.quarter-body {
    display: none;
    padding: 0;
}

.quarter-body.open {
    display: block;
}

/* Report Items */
.report {
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.report:last-child {
    border-bottom: none;
}

.report-info {
    flex: 1;
}

.report-title {
    font-weight: 600;
    margin-bottom: 5px;
}

.report-title a {
    color: #333;
    text-decoration: none;
    font-size: 14px;
}

.report-title a:hover {
    color: #009688;
    text-decoration: underline;
}

.report-date {
    font-size: 12px;
    color: #888;
}

.report-date i {
    margin-right: 4px;
}

.report-actions {
    display: flex;
    gap: 12px;
}

.report-actions a {
    color: #007bff;
    text-decoration: none;
    font-size: 13px;
}

.report-actions a:hover {
    text-decoration: underline;
}

.empty {
    padding: 30px;
    text-align: center;
    color: #999;
    font-size: 14px;
}

.empty i {
    font-size: 40px;
    margin-bottom: 10px;
    display: block;
}

/* Simple scrollbar */
.content::-webkit-scrollbar {
    width: 8px;
}

.content::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.content::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
}

/* Mobile */
@media (max-width: 768px) {
    .content {
        padding: 15px;
    }
    
    .report {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .report-actions {
        margin-top: 5px;
    }
    
    .top-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .controls {
        justify-content: space-between;
    }
    
    .year-labels {
        gap: 4px;
    }
    
    .year-labels span {
        font-size: 8px;
    }
    
    .gradient-grid {
        gap: 2px;
    }
}
</style>
</head>
<body>
<div class="layout">
<?php include 'header.php'; ?>
<div class="body-layout">
<main class="content">

<!-- Simple Top Bar -->
<div class="top-bar">
    <h2><i class="fa fa-folder"></i> Barangay Reports - <span id="selectedYearDisplay"><?= $selectedYear ?></span></h2>
    <div class="controls">
        <label>Sort:</label>
        <select id="sortSelect">
            <option value="new" <?= ($sort === 'new') ? 'selected' : '' ?>>Newest First</option>
            <option value="az" <?= ($sort === 'az') ? 'selected' : '' ?>>A to Z</option>
        </select>
        
        <a href="add_report.php" class="add-btn"><i class="fa fa-plus"></i> Add Report</a>
    </div>
</div>

<!-- Year Slider (Timeline) -->
<div class="year-slider-container">
    <label>Year Range</label>
    <div class="year-labels" id="yearLabels"></div>
    <div class="timeline-track-outer" id="timelineTrack">
        <div id="timelineFill"></div>
        <div class="timeline-handle" id="timelineHandleLeft" style="left:0%"></div>
        <div class="timeline-handle" id="timelineHandleRight" style="left:100%"></div>
    </div>
</div>

<!-- Population Toggle (Preschool / School Age) -->
<div class="population-toggle">
    <button id="preschoolBtn" class="active">
        <i class="fa fa-child"></i> Preschool
    </button>
    <button id="schoolBtn">
        <i class="fa fa-users"></i> School Age
    </button>
</div>

<!-- Legend with Gradient Colors -->
<div class="legend-section">
    <div class="legend-header">
        <i class="fa fa-chart-line"></i> Indicators
    </div>
    <ul class="legend-list" id="legendList">
        <!-- Will be populated by JavaScript -->
    </ul>
    <div class="gradient-bar-wrap">
        <div class="gradient-grid" id="gradientGrid"></div>
        <div class="gradient-label">Low → High Prevalence</div>
    </div>
</div>

<!-- Quarter Sections -->
<div id="quartersContainer">
<?php
$quarters = [
    'Q1' => 'First Quarter (Jan - Mar)',
    'Q2' => 'Second Quarter (Apr - Jun)',
    'Q3' => 'Third Quarter (Jul - Sep)',
    'Q4' => 'Fourth Quarter (Oct - Dec)'
];

foreach ($quarters as $q => $title):
    $reports = $reportsByQuarter[$q] ?? [];
?>
<div class="quarter">
    <div class="quarter-header" onclick="toggleQuarter(this)">
        <h3><i class="fa fa-calendar"></i> <?= $title ?></h3>
        <span class="badge"><?= count($reports) ?> reports</span>
    </div>
    <div class="quarter-body">
        <?php if (count($reports) > 0): ?>
            <?php foreach ($reports as $report): ?>
                <div class="report">
                    <div class="report-info">
                        <div class="report-title">
                            <a href="view_approved_report.php?id=<?= $report['id'] ?>">
                                <?= htmlspecialchars($report['report_title']) ?>
                            </a>
                        </div>
                        <div class="report-date">
                            <i class="fa fa-calendar"></i> <?= date("M d, Y", strtotime($report['report_date'])) ?>
                        </div>
                    </div>
                    <div class="report-actions">
                        <a href="view_approved_report.php?id=<?= $report['id'] ?>"><i class="fa fa-eye"></i> View</a>
                        <a href="./export_report.php?id=<?= $report['id'] ?>&format=pdf"><i class="fa fa-file-pdf"></i> PDF</a>
                        <a href="./export_report.php?id=<?= $report['id'] ?>&format=csv"><i class="fa fa-table"></i> CSV</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty">
                <i class="fa fa-inbox"></i>
                <p>No reports for this quarter</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
</div>

</main>
</div>
</div>

<script>
// Data storage
let geoData = null;
let availableYears = <?= json_encode($availableYears) ?>;
let minYear = availableYears.length > 0 ? Math.min(...availableYears) : 2020;
let maxYear = availableYears.length > 0 ? Math.max(...availableYears) : 2026;
let activeYearFrom = minYear;
let activeYearTo = maxYear;
let isYearRange = true;
let activePopulation = 'preschool';
let activeIndicator = 'ALL';

// Legend configuration
const legendConfig = {
    preschool: [
        { field: 'ALL', label: 'All Indicators', color: '#888888' },
        { field: 'UNDERWEIGHT', label: 'Underweight', color: '#d4a800' },
        { field: 'WASTED', label: 'Wasted', color: '#F97316' },
        { field: 'OVERWEIGHT_OBESE', label: 'Overweight/Obese', color: '#3B82F6' },
        { field: 'STUNTED', label: 'Stunted', color: '#EF4444' }
    ],
    school: [
        { field: 'ALL', label: 'All Indicators', color: '#888888' },
        { field: 'WASTED', label: 'Wasted', color: '#F97316' },
        { field: 'STUNTED', label: 'Stunted', color: '#EF4444' },
        { field: 'OVERWEIGHT_OBESE', label: 'Overweight/Obese', color: '#3B82F6' }
    ]
};

// ===================== TIMELINE SLIDER FUNCTIONS =====================
function initTimelineSlider() {
    const track = document.getElementById('timelineTrack');
    const handleLeft = document.getElementById('timelineHandleLeft');
    const handleRight = document.getElementById('timelineHandleRight');
    const fill = document.getElementById('timelineFill');
    const yearLabelsContainer = document.getElementById('yearLabels');
    
    let currentMin = activeYearFrom;
    let currentMax = activeYearTo;
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
                    applyYearFilter();
                });
            } else {
                span.classList.add('no-data');
                span.style.cursor = 'not-allowed';
                span.style.opacity = '0.5';
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
        
        // Highlight active years in labels
        if (yearLabelsContainer) {
            const spans = yearLabelsContainer.querySelectorAll('span');
            spans.forEach((span, idx) => {
                const year = minYear + idx;
                if (year >= currentMin && year <= currentMax && availableYears.includes(year)) {
                    span.classList.add('active-year');
                } else {
                    span.classList.remove('active-year');
                }
            });
        }
        
        document.getElementById('selectedYearDisplay').textContent = 
            isYearRange ? `${currentMin} - ${currentMax}` : currentMin;
        
        applyYearFilter();
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
    
    if (handleLeft && handleRight && track) {
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
    }
    
    updateFromPosition();
}

// ===================== LEGEND & GRADIENT FUNCTIONS =====================
function hexToRgb(hex) {
    const c = parseInt(hex.slice(1), 16);
    return { r: (c >> 16) & 255, g: (c >> 8) & 255, b: c & 255 };
}

function getGradientColor(baseColor, value) {
    if (value == null) return '#999';
    const ratio = Math.min(1, value / 9);
    const rgb = hexToRgb(baseColor);
    const start = { r: 240, g: 240, b: 240 };
    const r = Math.round(start.r + (rgb.r - start.r) * ratio);
    const g = Math.round(start.g + (rgb.g - start.g) * ratio);
    const b = Math.round(start.b + (rgb.b - start.b) * ratio);
    return `rgb(${r}, ${g}, ${b})`;
}

function updateLegend() {
    const legendList = document.getElementById('legendList');
    const config = legendConfig[activePopulation];
    
    if (!legendList) return;
    
    legendList.innerHTML = '';
    config.forEach(item => {
        const li = document.createElement('li');
        li.dataset.field = item.field;
        li.dataset.label = item.label;
        li.dataset.color = item.color;
        li.className = (activeIndicator === item.field) ? 'active' : '';
        
        const dot = document.createElement('span');
        dot.className = 'legend-dot';
        dot.style.background = item.color;
        
        const text = document.createTextNode(item.label);
        
        li.appendChild(dot);
        li.appendChild(text);
        
        li.addEventListener('click', () => {
            document.querySelectorAll('#legendList li').forEach(l => l.classList.remove('active'));
            li.classList.add('active');
            activeIndicator = item.field;
            updateGradientScale(item.color);
            // Filter reports based on selected indicator
            filterReportsByIndicator();
        });
        
        legendList.appendChild(li);
    });
    
    const activeConfig = config.find(c => c.field === activeIndicator) || config[0];
    updateGradientScale(activeConfig.color);
}

function updateGradientScale(baseColor) {
    const grid = document.getElementById('gradientGrid');
    if (!grid) return;
    grid.innerHTML = '';
    
    // NO DATA cell
    const noDataCell = document.createElement('div');
    noDataCell.className = 'gradient-cell';
    noDataCell.style.background = '#e5e7eb';
    noDataCell.style.backgroundImage = 'repeating-linear-gradient(45deg, #cbd5e1 0px, #cbd5e1 2px, #f1f5f9 2px, #f1f5f9 6px)';
    noDataCell.style.border = '1px solid #cbd5e1';
    noDataCell.title = 'No Data';
    noDataCell.textContent = 'ND';
    grid.appendChild(noDataCell);
    
    // 10 gradient cells
    for (let i = 0; i < 10; i++) {
        const min = i * 2;
        const max = min + 2;
        const cell = document.createElement('div');
        cell.className = 'gradient-cell';
        cell.style.background = getGradientColor(baseColor, i + 1);
        cell.title = `${min}% – ${max}%`;
        cell.textContent = `${min}-${max}`;
        grid.appendChild(cell);
    }
}

// ===================== FILTER FUNCTIONS =====================
function applyYearFilter() {
    // This function would filter the reports based on selected year range
    // For now, we'll reload the page with the selected year(s)
    // Since the backend supports single year, we'll use the first selected year
    let selectedYear = isYearRange ? activeYearFrom : activeYearFrom;
    if (selectedYear && availableYears.includes(selectedYear)) {
        let url = new URL(window.location.href);
        url.searchParams.set('year', selectedYear);
        window.location.href = url.toString();
    }
}

function filterReportsByIndicator() {
    // Filter reports based on selected indicator
    // This would highlight or filter reports that contain data for the selected indicator
    const reports = document.querySelectorAll('.report');
    if (activeIndicator === 'ALL') {
        reports.forEach(report => report.style.display = 'flex');
    } else {
        // For now, just show all reports - in production, you'd filter based on report content
        reports.forEach(report => report.style.display = 'flex');
    }
}

// ===================== POPULATION TOGGLE =====================
function initPopulationToggle() {
    const preschoolBtn = document.getElementById('preschoolBtn');
    const schoolBtn = document.getElementById('schoolBtn');
    
    if (preschoolBtn && schoolBtn) {
        preschoolBtn.addEventListener('click', () => {
            if (activePopulation !== 'preschool') {
                activePopulation = 'preschool';
                preschoolBtn.classList.add('active');
                schoolBtn.classList.remove('active');
                updateLegend();
            }
        });
        
        schoolBtn.addEventListener('click', () => {
            if (activePopulation !== 'school') {
                activePopulation = 'school';
                schoolBtn.classList.add('active');
                preschoolBtn.classList.remove('active');
                updateLegend();
            }
        });
    }
}

// ===================== QUARTER TOGGLE =====================
function toggleQuarter(header) {
    let body = header.nextElementSibling;
    body.classList.toggle('open');
}

// ===================== INITIALIZATION =====================
document.addEventListener('DOMContentLoaded', function() {
    // Open first quarter automatically
    let firstQuarter = document.querySelector('.quarter-body');
    if (firstQuarter) {
        firstQuarter.classList.add('open');
    }
    
    // Initialize timeline slider
    if (availableYears.length > 0) {
        initTimelineSlider();
    }
    
    // Initialize population toggle
    initPopulationToggle();
    
    // Initialize legend
    updateLegend();
    
    // Sort change
    document.getElementById('sortSelect').addEventListener('change', function() {
        let url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });
});
</script>
</body>
</html>