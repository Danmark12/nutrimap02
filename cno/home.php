<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';

// Only CNO
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}
$userId = $_SESSION['user_id'];

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Get user info for welcome message
$userStmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$userInfo = $userStmt->fetch(PDO::FETCH_ASSOC);
$userName = $userInfo ? htmlspecialchars($userInfo['first_name']) : 'User';

/**
 * FILTERS: Year Only
 */
$yearsStmt = $pdo->query("SELECT DISTINCT CAST(`year` AS UNSIGNED) AS yr FROM bns_reports ORDER BY yr DESC");
$yearOptions = $yearsStmt->fetchAll(PDO::FETCH_COLUMN, 0);

// Get current year
$currentYear = (int)date('Y');

// Set selected year: from URL, or current year if available in options, otherwise latest year
if (isset($_GET['year'])) {
    $selectedYear = (int)$_GET['year'];
} else {
    // Default to current year if it exists in options, otherwise use the latest year
    if (in_array($currentYear, $yearOptions)) {
        $selectedYear = $currentYear;
    } else {
        $selectedYear = !empty($yearOptions) ? max($yearOptions) : $currentYear;
    }
}

$excludeArchivedCondition = "NOT EXISTS (
    SELECT 1 FROM report_archives a
    WHERE a.report_id = r.id
      AND a.user_type = 'CNO'
      AND (a.is_archived = 1 OR a.is_deleted = 1)
)";

// Users counts
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalAdmins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE user_type='CNO'")->fetchColumn();
$totalBNS = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE user_type='BNS'")->fetchColumn();

// Total reports
$totalReportsStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reports r
    JOIN bns_reports b ON r.id = b.report_id
    WHERE r.is_submitted = 1
      AND b.year = :year
      AND {$excludeArchivedCondition}
");
$totalReportsStmt->execute([':year' => $selectedYear]);
$totalReports = (int)$totalReportsStmt->fetchColumn();

// Status counts
$statuses = ['Approved','Pending','Rejected'];
$reportCounts = [];
foreach ($statuses as $status) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM reports r
        JOIN bns_reports b ON r.id = b.report_id
        WHERE r.status = :status
          AND r.is_submitted = 1
          AND b.year = :year
          AND {$excludeArchivedCondition}
    ");
    $stmt->execute([':status' => $status, ':year' => $selectedYear]);
    $reportCounts[$status] = (int)$stmt->fetchColumn();
}
$approvedReports = $reportCounts['Approved'];
$pendingReports = $reportCounts['Pending'];
$rejectedReports = $reportCounts['Rejected'];

// Total distinct barangays
$totalBarangays = (int)$pdo->query("SELECT COUNT(DISTINCT barangay) FROM users WHERE barangay NOT IN ('CNO') AND barangay != ''")->fetchColumn();

// Monthly trend - all 12 months
$monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$monthlyStmt = $pdo->prepare("
    SELECT MONTH(r.report_date) AS m, COUNT(*) AS total
    FROM reports r
    JOIN bns_reports b ON r.id = b.report_id
    WHERE r.is_submitted = 1
      AND b.year = :year
      AND {$excludeArchivedCondition}
    GROUP BY MONTH(r.report_date)
");
$monthlyStmt->execute([':year' => $selectedYear]);
$monthlyRows = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);
$monthlyMap = [];
foreach ($monthlyRows as $row) $monthlyMap[(int)$row['m']] = (int)$row['total'];
$monthCounts = [];
for ($m = 1; $m <= 12; $m++) {
    $monthCounts[] = $monthlyMap[$m] ?? 0;
}

// Status distribution
$statusStmt = $pdo->prepare("
    SELECT r.status, COUNT(*) AS total
    FROM reports r
    JOIN bns_reports b ON r.id = b.report_id
    WHERE r.is_submitted = 1
      AND b.year = :year
      AND {$excludeArchivedCondition}
    GROUP BY r.status
");
$statusStmt->execute([':year' => $selectedYear]);
$statusRows = $statusStmt->fetchAll(PDO::FETCH_ASSOC);
$statusLabels = $statusCounts = [];
foreach ($statusRows as $sr) {
    $statusLabels[] = $sr['status'];
    $statusCounts[] = (int)$sr['total'];
}

// Top barangays
$barangayStmt = $pdo->prepare("
    SELECT u.barangay, COUNT(*) AS total
    FROM reports r
    JOIN users u ON r.user_id = u.id
    JOIN bns_reports b ON r.id = b.report_id
    WHERE r.is_submitted = 1
      AND u.barangay != ''
      AND b.year = :year
      AND {$excludeArchivedCondition}
    GROUP BY u.barangay
    ORDER BY total DESC
    LIMIT 5
");
$barangayStmt->execute([':year' => $selectedYear]);
$barangayRows = $barangayStmt->fetchAll(PDO::FETCH_ASSOC);
$barangayLabels = [];
$barangayCounts = [];
foreach ($barangayRows as $br) {
    $barangayLabels[] = $br['barangay'];
    $barangayCounts[] = (int)$br['total'];
}

// Pagination
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalRowsStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reports r
    JOIN bns_reports b ON r.id = b.report_id
    WHERE r.status IN ('Pending','Rejected') 
      AND r.is_submitted = 1
      AND b.year = :year
      AND {$excludeArchivedCondition}
");
$totalRowsStmt->execute([':year' => $selectedYear]);
$totalRows = (int)$totalRowsStmt->fetchColumn();
$totalPages = ($totalRows > 0) ? (int)ceil($totalRows / $limit) : 1;

$stmt = $pdo->prepare("
    SELECT r.id, u.profile_pic, CONCAT(u.first_name, ' ', u.last_name) AS full_name, 
           u.barangay, b.title, r.status, r.report_time, r.report_date
    FROM reports r
    JOIN users u ON r.user_id = u.id
    JOIN bns_reports b ON r.id = b.report_id
    WHERE r.status IN ('Pending','Rejected') 
      AND r.is_submitted = 1
      AND b.year = :year
      AND {$excludeArchivedCondition}
    ORDER BY r.report_date DESC, r.report_time DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':year', $selectedYear, PDO::PARAM_INT);
$stmt->execute();
$allReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

function buildQuery($overrides = []) {
    $q = array_merge($_GET, $overrides);
    return http_build_query($q);
}

$approvalRate = $totalReports > 0 ? round(($approvedReports / $totalReports) * 100) : 0;

// Get greeting based on time of day
$currentHour = date('H');
if ($currentHour < 12) {
    $greeting = "Good Morning";
} elseif ($currentHour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CNO Dashboard</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    * { font-family: 'Inter', sans-serif; }
    body { background: #f0f2f5; }
    
    /* Header Card Styles */
    .header-card {
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        border-radius: 1rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(13, 148, 136, 0.25);
    }
    
    /* Welcome Section */
    .welcome-section h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: white;
        margin-bottom: 0.25rem;
    }
    .welcome-section p {
        font-size: 0.7rem;
        color: rgba(255,255,255,0.8);
    }
    .cno-badge {
        background: rgba(255,255,255,0.2);
        border-radius: 2rem;
        padding: 0.25rem 0.75rem;
        font-size: 0.65rem;
        font-weight: 500;
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    /* Simple Time Display */
    .time-simple {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        border-radius: 0.75rem;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .date-text {
        font-size: 0.7rem;
        font-weight: 500;
        color: rgba(255,255,255,0.9);
    }
    .time-text {
        font-size: 1rem;
        font-weight: 700;
        color: white;
        font-family: 'Inter', monospace;
        letter-spacing: 0.5px;
    }
    .separator {
        color: rgba(255,255,255,0.4);
        font-size: 0.8rem;
    }
    
    /* Year Select in Header */
    .year-select-header {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 0.5rem;
        padding: 0.375rem 1rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: white;
        cursor: pointer;
        outline: none;
    }
    .year-select-header option {
        background: #0d9488;
        color: white;
    }
    .year-select-header:hover {
        background: rgba(255,255,255,0.3);
    }
    
    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 0.75rem;
        transition: all 0.2s ease;
        border: 1px solid #e9ecef;
        cursor: pointer;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        border-color: #cbd5e1;
    }
    
    .chart-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e9ecef;
        padding: 0.875rem;
    }
    
    /* Table Styles */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th {
        padding: 0.75rem 1rem;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        border-bottom: 1px solid #e9ecef;
        background: #fafbfc;
        text-align: left;
    }
    .data-table td {
        padding: 0.75rem 1rem;
        font-size: 0.75rem;
        color: #495057;
        border-bottom: 1px solid #f1f3f5;
        vertical-align: middle;
    }
    .data-table tr:hover td {
        background: #f8f9fa;
    }
    
    /* Column specific alignments */
    .data-table th:nth-child(1), .data-table td:nth-child(1) { text-align: left; }  /* BNS Name */
    .data-table th:nth-child(2), .data-table td:nth-child(2) { text-align: left; }  /* Report Title */
    .data-table th:nth-child(3), .data-table td:nth-child(3) { text-align: left; }  /* Barangay */
    .data-table th:nth-child(4), .data-table td:nth-child(4) { text-align: center; } /* Status */
    .data-table th:nth-child(5), .data-table td:nth-child(5) { text-align: center; } /* Date */
    .data-table th:nth-child(6), .data-table td:nth-child(6) { text-align: center; } /* Action */
    
    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.65rem;
        font-weight: 600;
        line-height: 1.4;
    }
    .badge-approved { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fed7aa; color: #9a3412; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    
    /* Progress Bar */
    .progress-bar {
        height: 0.25rem;
        background: #e5e7eb;
        border-radius: 0.25rem;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 0.25rem;
    }
    
    /* Pagination */
    .pagination-btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.7rem;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        background: white;
        color: #4b5563;
        transition: all 0.15s;
        text-decoration: none;
        display: inline-block;
    }
    .pagination-btn:hover:not(.disabled) {
        background: #f3f4f6;
        border-color: #d1d5db;
    }
    .pagination-btn.active {
        background: #0d9488;
        border-color: #0d9488;
        color: white;
    }
    .pagination-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    
    /* Search Input */
    .search-input {
        padding: 0.375rem 0.625rem 0.375rem 2rem;
        font-size: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        width: 180px;
        outline: none;
    }
    .search-input:focus {
        outline: none;
        border-color: #0d9488;
        ring: 2px solid #0d9488;
    }
    
    /* Chart Container */
    .chart-container {
        height: 140px;
        position: relative;
    }
    
    /* Avatar */
    .avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #0d9488, #0f766e);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
        color: white;
    }
    
    /* Button Styles */
    .review-btn {
        background: #0d9488;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.7rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.15s;
        display: inline-block;
    }
    .review-btn:hover {
        background: #0f766e;
    }
    
    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>
</head>
<body class="antialiased">

<div class="flex flex-col h-screen">
    <?php include 'header.php'; ?>
    
    <div class="flex flex-1 overflow-hidden">
        <main class="flex-1 overflow-y-auto p-4">
            
            <!-- Unified Header Card -->
            <div class="header-card">
                <div class="flex flex-wrap justify-between items-center gap-3">
                    <!-- Left Side: Welcome & Title -->
                    <div class="welcome-section">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h2><?= $greeting ?>, <?= $userName ?>! 👋</h2>
                            <span class="cno-badge">
                                <i class="fas fa-user-check text-xs"></i>
                                City Nutrition Office
                            </span>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-medium">
                                <i class="fas fa-chalkboard-user mr-1 text-[10px]"></i>
                                Dashboard
                            </p>
                            <span class="text-[10px] text-white/50">•</span>
                            <p class="text-[10px] text-white/70">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <?= $selectedYear ?> Reporting Year
                            </p>
                        </div>
                    </div>
                    
                    <!-- Right Side: Date & Time + Year Selector -->
                    <div class="flex items-center gap-3">
                        <!-- Simple Date & Time with Seconds -->
                        <div class="time-simple">
                            <i class="fas fa-calendar-day text-white/70 text-xs"></i>
                            <span class="date-text"><?= date('M d, Y') ?></span>
                            <span class="separator">|</span>
                            <i class="fas fa-clock text-white/70 text-xs"></i>
                            <span class="time-text" id="liveClock"><?= date('h:i:s A') ?></span>
                        </div>
                        
                        <!-- Year Selector -->
                        <form method="get">
<select name="year" onchange="this.form.submit()" class="year-select-header">
    <?php 
    // Sort years descending and ensure current year is included
    $sortedYears = $yearOptions;
    rsort($sortedYears);
    
    // If current year is not in options, add it
    if (!in_array($currentYear, $sortedYears)) {
        $sortedYears[] = $currentYear;
        rsort($sortedYears);
    }
    
    foreach ($sortedYears as $y): 
    ?>
        <option value="<?= htmlspecialchars($y) ?>" <?= ((int)$y === $selectedYear) ? 'selected' : '' ?>>
            <?= htmlspecialchars($y) ?>
        </option>
    <?php endforeach; ?>
</select>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards - Compact Grid (4 cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                <!-- Users Card -->
                <div class="stat-card p-3" onclick="window.location.href='users.php'">
                    <div class="flex items-center justify-between mb-1">
                        <div class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center">
                            <i class="fa fa-users text-white text-xs"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-800"><?= $totalUsers ?></span>
                    </div>
                    <p class="text-xs font-medium text-gray-600">Total Users</p>
                    <p class="text-[10px] text-gray-400 mt-0.5"><?= $totalAdmins ?> CNO · <?= $totalBNS ?> BNS</p>
                </div>
                
                <!-- Reports Card -->
                <div class="stat-card p-3" onclick="window.location.href='cno_reports.php?<?= htmlspecialchars(buildQuery()) ?>'">
                    <div class="flex items-center justify-between mb-1">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fa fa-file-alt text-white text-xs"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-800"><?= $totalReports ?></span>
                    </div>
                    <p class="text-xs font-medium text-gray-600">Total Reports</p>
                    <div class="flex gap-1 mt-0.5">
                        <span class="text-[10px] text-green-600">✓ <?= $approvedReports ?></span>
                        <span class="text-[10px] text-orange-500">⏳ <?= $pendingReports ?></span>
                        <span class="text-[10px] text-red-500">✗ <?= $rejectedReports ?></span>
                    </div>
                </div>
                
                <!-- Approval Rate Card -->
                <div class="stat-card p-3">
                    <div class="flex items-center justify-between mb-1">
                        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fa fa-chart-line text-white text-xs"></i>
                        </div>
                        <span class="text-xl font-bold <?= $approvalRate >= 70 ? 'text-green-600' : ($approvalRate >= 50 ? 'text-orange-500' : 'text-red-500') ?>">
                            <?= $approvalRate ?>%
                        </span>
                    </div>
                    <p class="text-xs font-medium text-gray-600">Approval Rate</p>
                    <div class="progress-bar mt-1">
                        <div class="progress-fill <?= $approvalRate >= 70 ? 'bg-green-500' : ($approvalRate >= 50 ? 'bg-orange-500' : 'bg-red-500') ?>" 
                             style="width: <?= $approvalRate ?>%"></div>
                    </div>
                </div>
                
                <!-- Barangays Card -->
                <div class="stat-card p-3" onclick="window.location.href='nutritional_map.php'">
                    <div class="flex items-center justify-between mb-1">
                        <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fa fa-map-marker-alt text-white text-xs"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-800"><?= $totalBarangays ?></span>
                    </div>
                    <p class="text-xs font-medium text-gray-600">Barangays Covered</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Total with BNS</p>
                </div>
            </div>
            
            <!-- Charts Row - Compact (3 columns) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <!-- Monthly Trend -->
                <div class="chart-card">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-700">Monthly Trend</h3>
                            <p class="text-[10px] text-gray-400">Reports per month</p>
                        </div>
                        <i class="fa fa-chart-line text-teal-500 text-xs"></i>
                    </div>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
                
                <!-- Status Distribution -->
                <div class="chart-card">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-700">Report Status</h3>
                            <p class="text-[10px] text-gray-400">Distribution</p>
                        </div>
                        <i class="fa fa-chart-pie text-purple-500 text-xs"></i>
                    </div>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="flex justify-center gap-2 mt-2">
                        <div class="flex items-center gap-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                            <span class="text-[9px] text-gray-500">Approved</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                            <span class="text-[9px] text-gray-500">Pending</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                            <span class="text-[9px] text-gray-500">Rejected</span>
                        </div>
                    </div>
                </div>
                
                <!-- Top Barangays -->
                <div class="chart-card">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-700">Top Barangays</h3>
                            <p class="text-[10px] text-gray-400">Most submissions</p>
                        </div>
                        <i class="fa fa-trophy text-yellow-500 text-xs"></i>
                    </div>
                    <div class="space-y-2">
                        <?php if ($barangayRows): ?>
                            <?php foreach ($barangayRows as $index => $br): ?>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-full <?= $index == 0 ? 'bg-yellow-500' : ($index == 1 ? 'bg-gray-400' : 'bg-orange-400') ?> 
                                        flex items-center justify-center text-white text-[9px] font-bold">
                                        <?= $index + 1 ?>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between mb-0.5">
                                            <span class="text-[11px] font-medium text-gray-700"><?= htmlspecialchars($br['barangay']) ?></span>
                                            <span class="text-[11px] font-semibold text-teal-600"><?= (int)$br['total'] ?></span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill bg-teal-500" style="width: <?= ($br['total'] / max($barangayCounts)) * 100 ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-gray-400">
                                <i class="fa fa-inbox text-lg mb-1 block"></i>
                                <p class="text-[10px]">No data available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Pending & Rejected Reports Table -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-xs font-semibold text-gray-700">
                            <i class="fa fa-clock text-orange-500 mr-1 text-xs"></i>
                            Pending & Rejected Reports
                        </h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <i class="fa fa-search absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="tableSearch" placeholder="Search reports..." class="search-input pl-7">
                        </div>
                        <a href="cno_reports.php?<?= htmlspecialchars(buildQuery()) ?>" class="text-[11px] text-teal-600 hover:text-teal-700 font-medium">
                            View All →
                        </a>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>BNS Name</th>
                                <th>Report Title</th>
                                <th>Barangay</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($allReports): ?>
                                <?php foreach($allReports as $r): 
                                    $initial = strtoupper(substr($r['full_name'], 0, 1));
                                ?>
                                <tr class="reports-row">
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="avatar"><?= $initial ?></div>
                                            <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($r['full_name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="max-w-[200px]">
                                        <span class="text-sm text-gray-600 truncate block"><?= htmlspecialchars($r['title']) ?></span>
                                    </td>
                                    <td>
                                        <span class="text-sm text-gray-600"><?= htmlspecialchars($r['barangay']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $r['status'] == 'Pending' ? 'badge-pending' : 'badge-rejected' ?>">
                                            <?= $r['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-sm text-gray-500"><?= date('M d, Y', strtotime($r['report_date'])) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="view_report.php?id=<?= (int)$r['id'] ?>" class="review-btn">
                                            Review <i class="fas fa-arrow-right ml-1 text-[9px]"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-400">
                                        <i class="fa fa-check-circle text-2xl mb-2 block"></i>
                                        <p class="text-sm">No pending or rejected reports</p>
                                        <p class="text-[10px] mt-1">All reports have been reviewed</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if($totalPages > 1): ?>
                <div class="px-4 py-3 border-t border-gray-100 flex justify-between items-center bg-gray-50">
                    <p class="text-[10px] text-gray-500">
                        Showing <?= ($offset + 1) ?> to <?= min($offset + $limit, $totalRows) ?> of <?= $totalRows ?> entries
                    </p>
                    <div class="flex gap-1">
                        <?php if ($page > 1): ?>
                            <a href="?<?= htmlspecialchars(buildQuery(['page' => $page-1])) ?>" class="pagination-btn">
                                <i class="fas fa-chevron-left text-[9px]"></i> Prev
                            </a>
                        <?php else: ?>
                            <span class="pagination-btn disabled"><i class="fas fa-chevron-left text-[9px]"></i> Prev</span>
                        <?php endif; ?>
                        
                        <?php
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);
                            for ($i = $start; $i <= $end; $i++):
                        ?>
                            <a href="?<?= htmlspecialchars(buildQuery(['page' => $i])) ?>" 
                               class="pagination-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= htmlspecialchars(buildQuery(['page' => $page+1])) ?>" class="pagination-btn">
                                Next <i class="fas fa-chevron-right text-[9px]"></i>
                            </a>
                        <?php else: ?>
                            <span class="pagination-btn disabled">Next <i class="fas fa-chevron-right text-[9px]"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Real-time clock with seconds for Philippine Time
function updatePhilippineTime() {
    const now = new Date();
    const phTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Manila"}));
    
    const hours = phTime.getHours();
    const minutes = phTime.getMinutes().toString().padStart(2, '0');
    const seconds = phTime.getSeconds().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const hours12 = hours % 12 || 12;
    
    const timeString = hours12.toString().padStart(2, '0') + ':' + minutes + ':' + seconds + ' ' + ampm;
    
    const clockElement = document.getElementById('liveClock');
    if (clockElement) clockElement.textContent = timeString;
}

// Update time every second
setInterval(updatePhilippineTime, 1000);
updatePhilippineTime();

// Search functionality
document.getElementById("tableSearch").addEventListener("keyup", function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll(".reports-row").forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? "" : "none";
    });
});

// Chart data
const monthlyLabels = <?= json_encode($monthLabels) ?>;
const monthlyData = <?= json_encode($monthCounts) ?>;
const statusLabels = <?= json_encode($statusLabels) ?>;
const statusData = <?= json_encode($statusCounts) ?>;

// Monthly Line Chart
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: monthlyLabels,
        datasets: [{
            data: monthlyData,
            borderColor: '#0d9488',
            borderWidth: 2,
            fill: true,
            backgroundColor: 'rgba(13, 148, 136, 0.05)',
            tension: 0.3,
            pointRadius: 2,
            pointBackgroundColor: '#0d9488',
            pointBorderColor: '#fff',
            pointBorderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false }, 
            tooltip: { 
                callbacks: { 
                    label: (ctx) => `${ctx.parsed.y} reports` 
                } 
            } 
        },
        scales: { 
            y: { 
                beginAtZero: true, 
                ticks: { stepSize: 1, precision: 0, font: { size: 8 } }, 
                grid: { color: '#e9ecef' } 
            }, 
            x: { 
                ticks: { font: { size: 8 } }, 
                grid: { display: false } 
            } 
        }
    }
});

// Status Doughnut Chart
const statusColors = statusLabels.map(l => l === 'Approved' ? '#10b981' : (l === 'Pending' ? '#f97316' : '#ef4444'));
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { 
        labels: statusLabels, 
        datasets: [{ 
            data: statusData, 
            backgroundColor: statusColors, 
            borderWidth: 0 
        }] 
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false }, 
            tooltip: { 
                callbacks: { 
                    label: (ctx) => `${ctx.label}: ${ctx.parsed}` 
                } 
            } 
        },
        cutout: '65%'
    }
});
</script>
</body>
</html>