<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';

// Only CNO
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}

// Fetch available years
$yearsStmt = $pdo->query("SELECT DISTINCT CAST(year AS UNSIGNED) AS yr FROM bns_reports ORDER BY yr DESC");
$years = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);

// Determine selected year
$currentYear = (int)date('Y');
$latestYear = !empty($years) ? max($years) : $currentYear;
$selectedYear = isset($_GET['year']) && in_array((int)$_GET['year'], $years) ? (int)$_GET['year'] : $latestYear;

// Get active section from URL parameter (default to 'consolidated')
$activeSection = isset($_GET['section']) && $_GET['section'] === 'barangay' ? 'barangay' : 'consolidated';

// Fetch barangays that have approved reports for selected year
// MODIFICATION 1: Added NOT EXISTS to exclude CNO-archived reports only
$barangayStmt = $pdo->prepare("
    SELECT DISTINCT bns.barangay 
    FROM bns_reports bns
    JOIN reports r ON bns.report_id = r.id
    WHERE bns.year = ? AND r.status = 'approved'
    AND NOT EXISTS (
        SELECT 1 FROM report_archives ra 
        WHERE ra.report_id = r.id 
        AND ra.user_type = 'CNO'
        AND ra.is_archived = 1
    )
    ORDER BY bns.barangay ASC
");
$barangayStmt->execute([$selectedYear]);
$barangayOptions = $barangayStmt->fetchAll(PDO::FETCH_COLUMN);

// Determine selected barangays for consolidated report
$selectedBarangays = isset($_GET['barangays']) && is_array($_GET['barangays']) ? $_GET['barangays'] : [];

// Prepare placeholders for IN clause
$placeholders = !empty($selectedBarangays) ? implode(',', array_fill(0, count($selectedBarangays), '?')) : 'NULL';

// Check if consolidated data exists for selected barangays
// MODIFICATION 2: Added NOT EXISTS to exclude CNO-archived reports only
$checkConsolidatedSql = "
    SELECT COUNT(*) as count
    FROM (
        SELECT b.barangay, r.user_id
        FROM reports r
        JOIN bns_reports b ON r.id = b.report_id
        WHERE b.year = ? 
        AND r.status = 'approved'
        AND NOT EXISTS (
            SELECT 1 FROM report_archives ra 
            WHERE ra.report_id = r.id 
            AND ra.user_type = 'CNO'
            AND ra.is_archived = 1
        )
    ";
if (!empty($selectedBarangays)) {
    $checkConsolidatedSql .= " AND b.barangay IN ($placeholders)";
}
$checkConsolidatedSql .= " GROUP BY b.barangay, r.user_id
    ) AS user_reports
";
$checkStmt = $pdo->prepare($checkConsolidatedSql);
if (!empty($selectedBarangays)) {
    $checkStmt->execute(array_merge([$selectedYear], $selectedBarangays));
} else {
    $checkStmt->execute([$selectedYear]);
}
$hasConsolidated = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

// Barangay reports (latest per user per barangay for selected year)
// MODIFICATION 3: Added NOT EXISTS to both outer and subquery to exclude CNO-archived reports only
$barangayReports = [];
foreach ($barangayOptions as $barangay) {
    $stmt = $pdo->prepare("
        SELECT DISTINCT r.id AS report_id, b.barangay, r.report_date AS latest_date, r.user_id
        FROM reports r
        JOIN bns_reports b ON r.id = b.report_id
        WHERE b.year = ?
        AND b.barangay = ?
        AND r.status = 'approved'
        AND NOT EXISTS (
            SELECT 1 FROM report_archives ra 
            WHERE ra.report_id = r.id 
            AND ra.user_type = 'CNO'
            AND ra.is_archived = 1
        )
        AND r.id = (
            SELECT r2.id
            FROM reports r2
            JOIN bns_reports b2 ON r2.id = b2.report_id
            WHERE b2.year = ?
            AND b2.barangay = ?
            AND r2.user_id = r.user_id
            AND r2.status = 'approved'
            AND NOT EXISTS (
                SELECT 1 FROM report_archives ra2 
                WHERE ra2.report_id = r2.id 
                AND ra2.user_type = 'CNO'
                AND ra2.is_archived = 1
            )
            ORDER BY r2.report_date DESC
            LIMIT 1
        )
        ORDER BY r.report_date DESC
    ");
    $stmt->execute([$selectedYear, $barangay, $selectedYear, $barangay]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $barangayReports[] = [
            'barangay' => $barangay,
            'users' => $rows,
            'user_count' => count($rows),
            'latest_date' => max(array_column($rows, 'latest_date'))
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CNO | Health and Nutrition Data</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<style>
:root {
  --nutrimap-green-dark: #1B5E20;
  --nutrimap-green: #2E7D32;
  --nutrimap-green-light: #4CAF50;
  --nutrimap-green-pale: #A5D6A7;
  --nutrimap-green-bg: #E8F5E9;
}

body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f7fa; margin: 0; padding: 0; color: #333; }
.container { max-width: 1130px; margin: 20px auto; background: #fff; padding: 30px 35px; border-radius: 9px; }
h1 { font-size: 22px; font-weight: 600; color: #1a1a1a; margin-bottom: 25px; }
select, button, input { font-family: inherit; font-size: 14px; border-radius: 6px; border: 1px solid #ccc; padding: 8px 12px; }
button { background: var(--nutrimap-green); color: #fff; border: none; cursor: pointer; margin-left: 8px; transition: background 0.2s, transform 0.1s; }
button:hover { background: var(--nutrimap-green-dark); transform: scale(1.03); }
.choices { min-width: 250px; max-width: 400px; white-space: nowrap; }
.choices__list--multiple .choices__item { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; }
.list-item { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border: 1px solid #e5e7eb; border-radius: 10px; margin-bottom: 12px; background: #fafafa; transition: all 0.2s ease-in-out; cursor: pointer; text-decoration: none; color: inherit; }
.list-item:hover { background: var(--nutrimap-green-bg); transform: scale(1.01); box-shadow: 0 2px 6px rgba(46,125,50,0.1); }
.filters { display: flex; gap: 10px; margin: 20px 0; flex-wrap: wrap; }
.filters input, .filters select { padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; flex: 1; min-width: 150px; }
.actions { display: flex; align-items: center; gap: 15px; }
.export-link { color: var(--nutrimap-green); text-decoration: none; font-weight: 500; }
.export-link:hover { text-decoration: underline; color: var(--nutrimap-green-dark); }
.info-note { background: var(--nutrimap-green-bg); padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; color: var(--nutrimap-green-dark); border-left: 4px solid var(--nutrimap-green); }
.toggle-buttons { display: flex; gap: 15px; margin-bottom: 25px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
.toggle-btn { background: #f0f0f0; color: #333; border: none; padding: 10px 25px; font-size: 16px; font-weight: 600; cursor: pointer; border-radius: 6px; transition: all 0.2s; }
.toggle-btn.active { background: var(--nutrimap-green); color: #fff; }
.toggle-btn:hover:not(.active) { background: #e0e0e0; }
.section { display: none; }
.section.active-section { display: block; }
.barangay-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.barangay-header h3 { margin: 0; color: var(--nutrimap-green-dark); }
.barangay-link { text-decoration: none; color: inherit; flex: 1; display: flex; justify-content: space-between; align-items: center; }
.barangay-link:hover { text-decoration: none; }
.consolidated-filter { margin-bottom: 20px; }
@media (max-width: 768px) { 
    .list-item { flex-direction: column; align-items: flex-start; } 
    .actions { margin-top: 10px; width: 100%; justify-content: space-between; } 
    .barangay-link { flex-direction: column; width: 100%; }
}
</style>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="container">


  <!-- Toggle Buttons -->
  <div class="toggle-buttons">
    <button class="toggle-btn <?= $activeSection === 'consolidated' ? 'active' : '' ?>" data-section="consolidated">📊 Consolidated Data</button>
    <button class="toggle-btn <?= $activeSection === 'barangay' ? 'active' : '' ?>" data-section="barangay">📋 Barangay Data</button>
  </div>

  <!-- ==================== CONSOLIDATED SECTION ==================== -->
  <div id="consolidated-section" class="section <?= $activeSection === 'consolidated' ? 'active-section' : '' ?>">
    
    <!-- Consolidated Filter Form -->
    <form method="get" class="consolidated-filter" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
      <input type="hidden" name="section" value="consolidated">
      <label><strong>Year:</strong></label>
      <select name="year" onchange="this.form.submit()">
          <?php foreach ($years as $y): ?>
              <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
          <?php endforeach; ?>
      </select>

      <label><strong>Barangays:</strong></label>
      <div style="display:flex; align-items:center; gap:10px;">
          <input type="checkbox" id="selectAll"> Select All
          <select id="barangays" name="barangays[]" multiple>
              <?php foreach ($barangayOptions as $b): ?>
                  <option value="<?= htmlspecialchars($b) ?>" <?= in_array($b, $selectedBarangays) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($b) ?>
                  </option>
              <?php endforeach; ?>
          </select>
      </div>
      <button type="submit">Confirm</button>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectEl = document.getElementById('barangays');
        const selectAll = document.getElementById('selectAll');

        if (selectEl && selectAll) {
            const choices = new Choices('#barangays', {
                removeItemButton: true,
                searchEnabled: true,
                placeholderValue: 'Select barangays',
                shouldSort: false
            });

            function updateSelectAllCheckbox() {
                const selectedCount = choices.getValue(true).length;
                selectAll.checked = selectedCount === selectEl.options.length && selectedCount > 0;
            }

            selectAll.addEventListener('change', function() {
                if (this.checked) {
                    choices.setChoiceByValue(Array.from(selectEl.options).map(o => o.value));
                } else {
                    choices.removeActiveItems();
                }
            });

            selectEl.addEventListener('change', updateSelectAllCheckbox);
            updateSelectAllCheckbox();
        }
    });
    </script>

    <!-- Consolidated Report Link -->
    <div id="consolidated-data">
      <a href="view_consolidated.php?year=<?= urlencode($selectedYear) ?><?= empty($selectedBarangays) ? '' : '&' . http_build_query(['barangays' => $selectedBarangays]) ?>" class="list-item">
        <strong>📊 Consolidated Health and Nutrition Data (<?= htmlspecialchars($selectedYear) ?>)</strong>
        <div class="actions">
          <span><?= $hasConsolidated ? 'Data available' : 'No data available' ?></span>
          <?php if ($hasConsolidated): ?>
            <a href="export_consolidated.php?year=<?= urlencode($selectedYear) ?><?= empty($selectedBarangays) ? '' : '&' . http_build_query(['barangays' => $selectedBarangays]) ?>&format=pdf" target="_blank" class="export-link" onclick="event.stopPropagation()">Export PDF</a>
            <a href="export_consolidated.php?year=<?= urlencode($selectedYear) ?><?= empty($selectedBarangays) ? '' : '&' . http_build_query(['barangays' => $selectedBarangays]) ?>&format=csv" target="_blank" class="export-link" onclick="event.stopPropagation()">Export CSV</a>
          <?php endif; ?>
        </div>
      </a>
    </div>
  </div>

  <!-- ==================== BARANGAY DATA SECTION ==================== -->
  <div id="barangay-section" class="section <?= $activeSection === 'barangay' ? 'active-section' : '' ?>">
    
    <!-- Year Selection for Barangay Data -->
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
      <label><strong>Year:</strong></label>
      <select id="barangayYearSelect">
          <?php foreach ($years as $y): ?>
              <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
          <?php endforeach; ?>
      </select>
    </div>

    <div class="barangay-header">
      <h3>Barangay Reports</h3>
    </div>

    <!-- Filters for Barangay Data Section -->
    <div class="filters">
      <input type="text" id="search" placeholder="Search barangay...">
      <select id="sortBy">
        <option value="name">Sort by: Barangay</option>
        <option value="users">Sort by: Number of BNS Users</option>
        <option value="date">Sort by: Latest Report Date</option>
      </select>
    </div>

    <!-- Barangay Reports List -->
    <div id="reportList">
      <?php if (empty($barangayReports)): ?>
        <p>No records found for this year.</p>
      <?php else: ?>
        <?php foreach ($barangayReports as $report): ?>
          <div class="list-item" data-barangay="<?= htmlspecialchars($report['barangay']) ?>" 
               data-users="<?= $report['user_count'] ?>" 
               data-date="<?= $report['latest_date'] ?>">
            <a href="view_barangay_consolidated.php?barangay=<?= urlencode($report['barangay']) ?>&year=<?= $selectedYear ?>" class="barangay-link">
              <div>
                <strong style="color: var(--nutrimap-green-dark);"><?= htmlspecialchars($report['barangay']) ?></strong>
                <?php if ($report['user_count'] > 1): ?>
                  <span style="font-size: 12px; color: var(--nutrimap-green); margin-left: 10px;">
                    (<?= $report['user_count'] ?> BNS users)
                  </span>
                <?php endif; ?>
              </div>
              <div class="actions">
                <span style="color: var(--nutrimap-green);">Latest: <?= htmlspecialchars($report['latest_date']) ?></span>
                <a href="export_barangay_consolidated.php?barangay=<?= urlencode($report['barangay']) ?>&year=<?= $selectedYear ?>&format=pdf" target="_blank" class="export-link" onclick="event.stopPropagation()">Export PDF</a>
                <a href="export_barangay_consolidated.php?barangay=<?= urlencode($report['barangay']) ?>&year=<?= $selectedYear ?>&format=csv" target="_blank" class="export-link" onclick="event.stopPropagation()">Export CSV</a>     
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
// Handle Barangay Data Year Selection
const barangayYearSelect = document.getElementById('barangayYearSelect');
if (barangayYearSelect) {
    barangayYearSelect.addEventListener('change', function() {
        const selectedYear = this.value;
        window.location.href = '?year=' + selectedYear + '&section=barangay';
    });
}

// Toggle between Consolidated and Barangay Data sections
const toggleBtns = document.querySelectorAll('.toggle-btn');
const sections = {
    consolidated: document.getElementById('consolidated-section'),
    barangay: document.getElementById('barangay-section')
};

toggleBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const sectionName = this.dataset.section;
        const currentYear = '<?= $selectedYear ?>';
        let url = '?year=' + currentYear + '&section=' + sectionName;
        
        // Preserve selected barangays for consolidated section
        if (sectionName === 'consolidated') {
            const selectEl = document.getElementById('barangays');
            if (selectEl) {
                const selectedValues = Array.from(selectEl.selectedOptions).map(opt => opt.value);
                selectedValues.forEach(val => {
                    url += '&barangays[]=' + encodeURIComponent(val);
                });
            }
        }
        
        window.location.href = url;
    });
});

// Search and Filter functions for barangay section
const searchInput = document.getElementById('search');
const sortSelect = document.getElementById('sortBy');
const reportList = document.getElementById('reportList');

function filterAndSortReports() {
  const search = searchInput ? searchInput.value.toLowerCase() : '';
  const sortBy = sortSelect ? sortSelect.value : 'name';
  const reports = reportList ? Array.from(reportList.children).filter(el => el.classList.contains('list-item')) : [];

  reports.forEach(r => {
    const text = r.textContent.toLowerCase();
    let visible = true;
    if (search && !text.includes(search)) visible = false;
    r.style.display = visible ? 'flex' : 'none';
  });

  const visibleReports = reports.filter(r => r.style.display !== 'none');
  
  if (sortBy === 'date') {
    visibleReports.sort((a, b) => new Date(b.dataset.date) - new Date(a.dataset.date));
  } else if (sortBy === 'users') {
    visibleReports.sort((a, b) => parseInt(b.dataset.users) - parseInt(a.dataset.users));
  } else {
    visibleReports.sort((a, b) => a.dataset.barangay.localeCompare(b.dataset.barangay));
  }
  
  visibleReports.forEach(r => reportList.appendChild(r));
}

if (searchInput) searchInput.addEventListener('keyup', filterAndSortReports);
if (sortSelect) sortSelect.addEventListener('change', filterAndSortReports);

// Initial filter and sort
filterAndSortReports();
</script>
</body>
</html>