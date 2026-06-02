<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';

// Only allow CNO
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}
$userId   = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];

// Set Philippine Timezone
date_default_timezone_set('Asia/Manila');

// Handle archive action
if (isset($_GET['archive_id']) && is_numeric($_GET['archive_id'])) {
    $reportId = (int)$_GET['archive_id'];

    $stmt = $pdo->prepare("SELECT * FROM reports WHERE id = ? AND status = 'Approved'");
    $stmt->execute([$reportId]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($report) {
        $check = $pdo->prepare("
            SELECT * FROM report_archives 
            WHERE report_id = ? AND user_id = ? AND user_type = ?
        ");
        $check->execute([$reportId, $userId, $userType]);
        $exists = $check->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            $update = $pdo->prepare("
                UPDATE report_archives 
                SET is_archived = 1, is_deleted = 0, archived_at = NOW() 
                WHERE report_id = ? AND user_id = ? AND user_type = ?
            ");
            $update->execute([$reportId, $userId, $userType]);
        } else {
            $insert = $pdo->prepare("
                INSERT INTO report_archives (report_id, user_id, user_type, is_archived, archived_at)
                VALUES (?, ?, ?, 1, NOW())
            ");
            $insert->execute([$reportId, $userId, $userType]);
        }
    }

    header("Location: report_history.php?msg=Report archived successfully");
    exit();
}

// Filters
$search = $_GET['search'] ?? '';
$barangay_filter = $_GET['barangay'] ?? '';
$year_filter = $_GET['year'] ?? '';
$quarter_filter = $_GET['quarter'] ?? '';
$sort = $_GET['sort'] ?? 'date';

// Build query using positional placeholders
$sql = "
    SELECT r.id, r.report_date, r.report_time, b.title, b.barangay, b.year,
           u.first_name, u.last_name, u.username
    FROM reports r
    INNER JOIN bns_reports b ON b.report_id = r.id
    INNER JOIN users u ON r.user_id = u.id
    WHERE r.status = 'Approved'
    AND r.id NOT IN (
        SELECT report_id FROM report_archives 
        WHERE user_id = ? AND user_type = ? AND (is_archived = 1 OR is_deleted = 1)
    )
";

$params = [$userId, $userType];

if (!empty($search)) {
    $sql .= " AND (b.title LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if (!empty($barangay_filter)) {
    $sql .= " AND b.barangay = ?";
    $params[] = $barangay_filter;
}

if (!empty($year_filter)) {
    $sql .= " AND b.year = ?";
    $params[] = $year_filter;
}

if (!empty($quarter_filter)) {
    $sql .= " AND QUARTER(r.report_date) = ?";
    $params[] = $quarter_filter;
}

if ($sort === 'name') {
    $sql .= " ORDER BY b.title ASC";
} else {
    $sql .= " ORDER BY b.year DESC, r.report_date DESC, r.report_time DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($reports as &$report) {
    if (!empty($report['report_date']) && !empty($report['report_time'])) {
        $utc = new DateTime($report['report_date'] . ' ' . $report['report_time'], new DateTimeZone('UTC'));
        $utc->setTimezone(new DateTimeZone('Asia/Manila'));
        $report['formatted_datetime'] = $utc->format("M d, Y h:i A");
    } else {
        $report['formatted_datetime'] = 'Date not available';
    }
    $report['quarter'] = ceil((int)date('m', strtotime($report['report_date'])) / 3);
}
unset($report);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CNO | Approved Reports</title>
    <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-title {
            margin-bottom: 20px;
        }

        .page-title h2 {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Filter Card - Search and Dropdowns on same line */
        .filter-card {
            background: white;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .filter-header {
            background: #f8f9fa;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            color: #009688;
        }

        .filter-body {
            padding: 15px;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .search-box {
            flex: 2;
            min-width: 200px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 38px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #009688;
            box-shadow: 0 0 0 2px rgba(0,150,136,0.1);
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 14px;
        }

        .filter-select {
            flex: 1;
            min-width: 130px;
        }

        .filter-select select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-select select:focus {
            outline: none;
            border-color: #009688;
            box-shadow: 0 0 0 2px rgba(0,150,136,0.1);
        }

        .report-list {
            background: white;
            border-radius: 8px;
            border: 1px solid #ddd;
            overflow: hidden;
        }

        .barangay-group {
            border-bottom: 1px solid #eee;
        }

        .barangay-header {
            background: #f8f9fa;
            padding: 12px 15px;
            font-weight: 600;
            font-size: 16px;
            color: #009688;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .barangay-header:hover {
            background: #f0f0f0;
        }

        .barangay-count {
            font-size: 12px;
            color: #666;
            font-weight: normal;
        }

        .year-group {
            border-top: 1px solid #f0f0f0;
        }

        .year-header {
            padding: 10px 15px 10px 30px;
            background: #fafafa;
            font-weight: 600;
            font-size: 14px;
            color: #555;
        }

        .quarter-header {
            padding: 8px 15px 8px 45px;
            background: #fefefe;
            font-size: 13px;
            color: #888;
            font-weight: 500;
        }

        .report-row {
            padding: 12px 15px 12px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f5f5f5;
            flex-wrap: wrap;
            gap: 10px;
        }

        .report-row:hover {
            background: #f9f9f9;
        }

        .report-info {
            flex: 2;
        }

        .report-title {
            font-weight: 500;
            font-size: 14px;
            color: #333;
            margin-bottom: 4px;
        }

        .report-title i {
            color: #009688;
            font-size: 12px;
            margin-right: 5px;
        }

        .report-meta {
            font-size: 12px;
            color: #888;
        }

        .report-meta i {
            margin-right: 3px;
            width: 14px;
            color: #009688;
        }

        .bns-name {
            color: #009688;
            font-weight: 500;
        }

        .report-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 5px 12px;
            font-size: 12px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .btn-view {
            color: #009688;
            border: 1px solid #009688;
            background: white;
        }

        .btn-view:hover {
            background: #009688;
            color: white;
        }

        .btn-archive {
            color: #dc3545;
            border: 1px solid #dc3545;
            background: white;
        }

        .btn-archive:hover {
            background: #dc3545;
            color: white;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .toggle-icon {
            transition: transform 0.2s;
        }

        .barangay-content {
            display: block;
        }

        .barangay-content.collapsed {
            display: none;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box, .filter-select {
                width: 100%;
            }
            
            .report-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .report-actions {
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <div class="page-title">
        <h2><i class="fas fa-check-circle" style="color: #009688;"></i> Approved Reports</h2>
    </div>

    <!-- Filter Card with Search and Dropdowns on same line -->
    <div class="filter-card">
        <div class="filter-header">
            <i class="fas fa-filter"></i> Filter Reports
        </div>
        <div class="filter-body">
            <div class="filter-row">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search by title or BNS name...">
                </div>
                
                <div class="filter-select">
                    <select id="barangayFilter">
                        <option value="">All Barangays</option>
                        <?php
                        $barangays = ['Amoros','Bolisong','Bolobolo','Calongonan','Cogon','Himaya','Hinigdaan','Kalabaylabay','Molugan','Poblacion','Kibonbon','Sambulawan','Sinaloc','Taytay','Ulaliman'];
                        foreach ($barangays as $b) {
                            $sel = ($barangay_filter == $b) ? "selected" : "";
                            echo "<option value=\"$b\" $sel>$b</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="filter-select">
                    <select id="yearFilter">
                        <option value="">All Years</option>
                        <?php
                        $years = $pdo->query("SELECT DISTINCT year FROM bns_reports ORDER BY year DESC")->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($years as $y) {
                            $sel = ($year_filter == $y) ? "selected" : "";
                            echo "<option value=\"$y\" $sel>$y</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="filter-select">
                    <select id="quarterFilter">
                        <option value="">All Quarters</option>
                        <option value="1" <?= ($quarter_filter=="1")?"selected":"" ?>>Q1 (Jan-Mar)</option>
                        <option value="2" <?= ($quarter_filter=="2")?"selected":"" ?>>Q2 (Apr-Jun)</option>
                        <option value="3" <?= ($quarter_filter=="3")?"selected":"" ?>>Q3 (Jul-Sep)</option>
                        <option value="4" <?= ($quarter_filter=="4")?"selected":"" ?>>Q4 (Oct-Dec)</option>
                    </select>
                </div>

                <div class="filter-select">
                    <select id="sortFilter">
                        <option value="date" <?= $sort=="date"?"selected":"" ?>>Newest First</option>
                        <option value="name" <?= $sort=="name"?"selected":"" ?>>Title A-Z</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="report-list" id="reportList">
        <?php if (!empty($reports)): ?>
            <?php
            $grouped = [];
            foreach ($reports as $row) {
                $grouped[$row['barangay']][$row['year']][$row['quarter']][] = $row;
            }
            ?>

            <?php foreach ($grouped as $brgy => $years): ?>
                <div class="barangay-group" data-barangay="<?= strtolower(htmlspecialchars($brgy)) ?>">
                    <div class="barangay-header" onclick="toggleContent(this)">
                        <span><i class="fas fa-building"></i> <?= htmlspecialchars($brgy) ?></span>
                        <span class="barangay-count">
                            <?php 
                            $total = 0;
                            foreach ($years as $year_data) {
                                foreach ($year_data as $quarter_reports) {
                                    $total += count($quarter_reports);
                                }
                            }
                            echo $total . ' report' . ($total != 1 ? 's' : '');
                            ?>
                            <i class="fas fa-chevron-down toggle-icon" style="margin-left: 8px;"></i>
                        </span>
                    </div>
                    <div class="barangay-content">
                        <?php foreach ($years as $yr => $quarters): ?>
                            <div class="year-group" data-year="<?= $yr ?>">
                                <div class="year-header">
                                    <i class="far fa-calendar-alt"></i> Year <?= $yr ?>
                                </div>
                                <?php foreach ($quarters as $qtr => $rows): ?>
                                    <div class="quarter-header" data-quarter="<?= $qtr ?>">
                                        <i class="fas fa-chart-line"></i> Quarter <?= $qtr ?>
                                    </div>
                                    <?php foreach ($rows as $row): ?>
                                        <div class="report-row" 
                                             data-title="<?= strtolower(htmlspecialchars($row['title'])) ?>"
                                             data-bns="<?= strtolower(htmlspecialchars($row['first_name'] . ' ' . $row['last_name'])) ?>"
                                             data-barangay="<?= strtolower(htmlspecialchars($row['barangay'])) ?>"
                                             data-year="<?= $row['year'] ?>">
                                            <div class="report-info">
                                                <div class="report-title">
                                                    <i class="fas fa-file-alt"></i> <?= htmlspecialchars($row['title']) ?>
                                                </div>
                                                <div class="report-meta">
                                                    <i class="fas fa-user-check"></i> 
                                                    <strong>Submitted by:</strong> 
                                                    <span class="bns-name"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></span>
                                                    <span style="margin: 0 6px">•</span>
                                                    <i class="far fa-clock"></i> 
                                                    <?= $row['formatted_datetime'] ?>
                                                </div>
                                            </div>
                                            <div class="report-actions">
                                                <a href="view_report.php?id=<?= $row['id'] ?>" class="btn btn-view">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="export_barangay.php?id=<?= $row['id'] ?>&format=pdf" class="btn btn-view">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </a>
                                                <a href="export_barangay.php?id=<?= $row['id'] ?>&format=csv" class="btn btn-view">
                                                    <i class="fas fa-file-csv"></i> CSV
                                                </a>
                                                <a href="report_history.php?archive_id=<?= $row['id'] ?>" class="btn btn-archive" 
                                                   onclick="return confirm('Archive this report from <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>?')">
                                                    <i class="fas fa-archive"></i> Archive
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty">
                <i class="fas fa-folder-open" style="font-size: 48px; color: #ccc;"></i>
                <p style="margin-top: 10px;">No approved reports found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// ========== INSTANT SEARCH AND FILTER FUNCTIONS ==========
const searchInput = document.getElementById('searchInput');
const barangayFilter = document.getElementById('barangayFilter');
const yearFilter = document.getElementById('yearFilter');
const quarterFilter = document.getElementById('quarterFilter');
const sortFilter = document.getElementById('sortFilter');

// Function to filter reports instantly
function filterReports() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedBarangay = barangayFilter.value.toLowerCase();
    const selectedYear = yearFilter.value;
    const selectedQuarter = quarterFilter.value;
    const selectedSort = sortFilter.value;
    
    const barangayGroups = document.querySelectorAll('.barangay-group');
    let hasVisibleReports = false;
    
    barangayGroups.forEach(group => {
        const barangayName = group.getAttribute('data-barangay') || '';
        const barangayHeader = group.querySelector('.barangay-header span:first-child').textContent.toLowerCase();
        
        let barangayMatches = true;
        if (selectedBarangay && barangayName !== selectedBarangay && !barangayHeader.includes(selectedBarangay)) {
            barangayMatches = false;
        }
        
        const yearGroups = group.querySelectorAll('.year-group');
        let groupHasVisible = false;
        
        yearGroups.forEach(yearGroup => {
            const year = yearGroup.getAttribute('data-year') || '';
            
            let yearMatches = true;
            if (selectedYear && year !== selectedYear) {
                yearMatches = false;
            }
            
            const quarterHeaders = yearGroup.querySelectorAll('.quarter-header');
            const reportRows = yearGroup.querySelectorAll('.report-row');
            let yearHasVisible = false;
            
            reportRows.forEach(row => {
                const title = row.getAttribute('data-title') || '';
                const bnsName = row.getAttribute('data-bns') || '';
                const rowBarangay = row.getAttribute('data-barangay') || '';
                const rowYear = row.getAttribute('data-year') || '';
                
                let searchMatches = true;
                if (searchTerm) {
                    searchMatches = title.includes(searchTerm) || bnsName.includes(searchTerm);
                }
                
                let quarterMatches = true;
                if (selectedQuarter) {
                    const quarterHeader = row.closest('.quarter-header');
                    if (quarterHeader) {
                        const quarterText = quarterHeader.textContent;
                        if (!quarterText.includes(selectedQuarter)) {
                            quarterMatches = false;
                        }
                    }
                }
                
                const shouldShow = searchMatches && barangayMatches && yearMatches && quarterMatches;
                
                if (shouldShow) {
                    row.style.display = 'flex';
                    yearHasVisible = true;
                    groupHasVisible = true;
                    hasVisibleReports = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            quarterHeaders.forEach(quarter => {
                const nextRows = [];
                let nextElement = quarter.nextElementSibling;
                while (nextElement && nextElement.classList.contains('report-row')) {
                    nextRows.push(nextElement);
                    nextElement = nextElement.nextElementSibling;
                }
                const hasVisibleRow = nextRows.some(row => row.style.display === 'flex');
                quarter.style.display = hasVisibleRow ? 'block' : 'none';
            });
            
            yearGroup.style.display = yearHasVisible ? 'block' : 'none';
        });
        
        group.style.display = groupHasVisible ? 'block' : 'none';
    });
    
    let noResultsDiv = document.querySelector('.no-results');
    if (!hasVisibleReports && barangayGroups.length > 0) {
        if (!noResultsDiv) {
            noResultsDiv = document.createElement('div');
            noResultsDiv.className = 'no-results';
            noResultsDiv.innerHTML = '<i class="fas fa-search"></i> No matching reports found';
            document.getElementById('reportList').appendChild(noResultsDiv);
        }
    } else if (noResultsDiv) {
        noResultsDiv.remove();
    }
}

// Function to sort reports
function sortReports() {
    const sortValue = sortFilter.value;
    const reportList = document.getElementById('reportList');
    const barangayGroups = Array.from(document.querySelectorAll('.barangay-group'));
    
    if (sortValue === 'name') {
        barangayGroups.sort((a, b) => {
            const nameA = a.querySelector('.barangay-header span:first-child').textContent;
            const nameB = b.querySelector('.barangay-header span:first-child').textContent;
            return nameA.localeCompare(nameB);
        });
    } else {
        barangayGroups.forEach(group => {
            const yearGroups = Array.from(group.querySelectorAll('.year-group'));
            yearGroups.sort((a, b) => {
                const yearA = parseInt(a.getAttribute('data-year') || '0');
                const yearB = parseInt(b.getAttribute('data-year') || '0');
                return yearB - yearA;
            });
            yearGroups.forEach(yearGroup => group.querySelector('.barangay-content').appendChild(yearGroup));
        });
    }
    
    barangayGroups.forEach(group => reportList.appendChild(group));
}

// ========== EVENT LISTENERS ==========
searchInput.addEventListener('keyup', function() {
    filterReports();
});

barangayFilter.addEventListener('change', function() {
    filterReports();
});

yearFilter.addEventListener('change', function() {
    filterReports();
});

quarterFilter.addEventListener('change', function() {
    filterReports();
});

sortFilter.addEventListener('change', function() {
    sortReports();
    filterReports();
});

// ========== TOGGLE FUNCTION ==========
function toggleContent(header) {
    const content = header.nextElementSibling;
    const icon = header.querySelector('.toggle-icon');
    
    if (content.classList.contains('collapsed')) {
        content.classList.remove('collapsed');
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        content.classList.add('collapsed');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    filterReports();
});
</script>

</body>
</html>