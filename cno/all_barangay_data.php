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

// Fetch barangays for selected year
$barangayStmt = $pdo->prepare("SELECT DISTINCT barangay FROM bns_reports WHERE year = ? ORDER BY barangay ASC");
$barangayStmt->execute([$selectedYear]);
$barangayOptions = $barangayStmt->fetchAll(PDO::FETCH_COLUMN);

// Determine selected barangays for consolidated report only (no auto-select)
$selectedBarangays = isset($_GET['barangays']) && is_array($_GET['barangays']) ? $_GET['barangays'] : [];

// Prepare placeholders for IN clause
$placeholders = !empty($selectedBarangays) ? implode(',', array_fill(0, count($selectedBarangays), '?')) : 'NULL';

// Consolidated report (latest across selected barangays)
$consolidatedSql = "
    SELECT r.id, r.report_date
    FROM reports r
    JOIN bns_reports b ON r.id = b.report_id
    WHERE b.year = ? AND b.barangay IN ($placeholders)
    ORDER BY r.report_date DESC
    LIMIT 1
";
$consolidatedStmt = $pdo->prepare($consolidatedSql);
if (!empty($selectedBarangays)) {
    $consolidatedStmt->execute(array_merge([$selectedYear], $selectedBarangays));
} else {
    $consolidatedStmt->execute([$selectedYear]);
}
$consolidated = $consolidatedStmt->fetch(PDO::FETCH_ASSOC);

// Barangay reports (latest per barangay for selected year) - always show all barangays
$barangayReports = [];
foreach ($barangayOptions as $barangay) {
    $stmt = $pdo->prepare("
        SELECT r.id AS report_id, b.barangay, r.report_date AS latest_date
        FROM reports r
        JOIN bns_reports b ON r.id = b.report_id
        WHERE b.year = ? AND b.barangay = ?
        ORDER BY r.report_date DESC
        LIMIT 1
    ");
    $stmt->execute([$selectedYear, $barangay]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) $barangayReports[] = $row;
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
body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f7fa; margin: 0; padding: 0; color: #333; }
.container { max-width: 1130px; margin: 20px auto; background: #fff; padding: 30px 35px; border-radius: 9px; }
h1 { font-size: 22px; font-weight: 600; color: #1a1a1a; margin-bottom: 25px; }
form { margin-bottom: 25px; }
select, button, input { font-family: inherit; font-size: 14px; border-radius: 6px; border: 1px solid #ccc; padding: 8px 12px; }
button { background: #007bff; color: #fff; border: none; cursor: pointer; margin-left: 8px; transition: background 0.2s, transform 0.1s; }
button:hover { background: #0056b3; transform: scale(1.03); }
.choices { min-width: 250px; max-width: 400px; white-space: nowrap; }
.choices__list--multiple .choices__item { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; }
.list-item { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border: 1px solid #e5e7eb; border-radius: 10px; margin-bottom: 12px; background: #fafafa; transition: all 0.2s ease-in-out; cursor: pointer; }
.list-item:hover { background: #edf5ff; transform: scale(1.01); box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
.filters { display: flex; gap: 10px; margin: 20px 0; flex-wrap: wrap; }
.filters input, .filters select { padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; flex: 1; min-width: 150px; }
.actions { display: flex; align-items: center; gap: 15px; }
.export-link { color: #007bff; text-decoration: none; font-weight: 500; }
.export-link:hover { text-decoration: underline; }
@media (max-width: 768px) { .list-item { flex-direction: column; align-items: flex-start; } .actions { margin-top: 10px; width: 100%; justify-content: space-between; } }
</style>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="container">
  <h1>All Barangay Data</h1>

  <!-- Filter form (checkboxes only affect consolidated report) -->
  <form method="get" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
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

      // Select All toggle
      selectAll.addEventListener('change', function() {
          if (this.checked) {
              choices.setChoiceByValue(Array.from(selectEl.options).map(o => o.value));
          } else {
              choices.removeActiveItems();
          }
      });

      selectEl.addEventListener('change', updateSelectAllCheckbox);
      updateSelectAllCheckbox();
  });
  </script>

  <!-- Consolidated Report -->
  <div id="consolidated-section">
    <a href="view_consolidated.php?year=<?= urlencode($selectedYear) ?><?= empty($selectedBarangays) ? '' : '&' . http_build_query(['barangays' => $selectedBarangays]) ?>" class="list-item">
      <strong>Consolidated Health and Nutrition Data (<?= htmlspecialchars($selectedYear) ?>)</strong>
      <div class="actions">
        <span><?= $consolidated ? htmlspecialchars($consolidated['report_date']) : 'Current Year Consolidated' ?></span>
        <?php if ($consolidated): ?>
          <a href="export_consolidated.php?year=<?= urlencode($selectedYear) ?><?= empty($selectedBarangays) ? '' : '&' . http_build_query(['barangays' => $selectedBarangays]) ?>&format=pdf" target="_blank" class="export-link" onclick="event.stopPropagation()">Export PDF</a>
          <a href="export_consolidated.php?year=<?= urlencode($selectedYear) ?><?= empty($selectedBarangays) ? '' : '&' . http_build_query(['barangays' => $selectedBarangays]) ?>&format=csv" target="_blank" class="export-link" onclick="event.stopPropagation()">Export CSV</a>
        <?php endif; ?>
      </div>
    </a>
  </div>

  <!-- Filters -->
  <div class="filters">
    <input type="text" id="search" placeholder="Search barangay...">
    <select id="barangayFilter">
      <option value="">All Barangays</option>
      <?php foreach ($barangayReports as $r): ?>
        <option value="<?= htmlspecialchars($r['barangay']) ?>"><?= htmlspecialchars($r['barangay']) ?></option>
      <?php endforeach; ?>
    </select>
    <select id="sortBy">
      <option value="name">Sort by: Barangay</option>
      <option value="date">Sort by: Date</option>
    </select>
  </div>

  <!-- Barangay Reports -->
  <div id="reportList">
    <?php if (empty($barangayReports)): ?>
      <p>No records found for this year.</p>
    <?php else: ?>
      <?php foreach ($barangayReports as $r): ?>
        <div class="list-item" data-barangay="<?= htmlspecialchars($r['barangay']) ?>">
          <strong><?= htmlspecialchars($r['barangay']) ?> Health and Nutrition Data</strong>
          <div class="actions">
            <span><?= htmlspecialchars($r['latest_date']) ?></span>
            <a href="export_barangay.php?id=<?= urlencode($r['report_id']) ?>&format=pdf" target="_blank">Export PDF</a>
            <a href="export_barangay.php?id=<?= urlencode($r['report_id']) ?>&format=csv" target="_blank">Export CSV</a>     
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
const searchInput = document.getElementById('search');
const filterSelect = document.getElementById('barangayFilter');
const sortSelect = document.getElementById('sortBy');
const reportList = document.getElementById('reportList');

function filterReports() {
  const search = searchInput.value.toLowerCase();
  const filter = filterSelect.value;
  const reports = Array.from(reportList.children).filter(el => el.classList.contains('list-item'));

  reports.forEach(r => {
    const text = r.textContent.toLowerCase();
    const barangay = r.dataset.barangay;
    let visible = true;
    if (search && !text.includes(search)) visible = false;
    if (filter && barangay !== filter) visible = false;
    r.style.display = visible ? 'flex' : 'none';
  });

  if (sortSelect.value === 'date') {
    reports.sort((a, b) => new Date(b.querySelector('.actions span').textContent) - new Date(a.querySelector('.actions span').textContent))
           .forEach(r => reportList.appendChild(r));
  } else {
    reports.sort((a, b) => a.dataset.barangay.localeCompare(b.dataset.barangay))
           .forEach(r => reportList.appendChild(r));
  }
}

searchInput.addEventListener('keyup', filterReports);
filterSelect.addEventListener('change', filterReports);
sortSelect.addEventListener('change', filterReports);
</script>
</body>
</html>