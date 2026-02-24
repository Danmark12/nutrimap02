<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';

// Only CNO
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}
$userId = $_SESSION['user_id'];

/**
 * FILTERS: Year & Quarter
 */
$yearsStmt = $pdo->query("SELECT DISTINCT CAST(`year` AS UNSIGNED) AS yr FROM bns_reports ORDER BY yr DESC");
$yearOptions = $yearsStmt->fetchAll(PDO::FETCH_COLUMN, 0);

$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$selectedQuarter = isset($_GET['quarter']) ? (int)$_GET['quarter'] : (int)ceil(date('n')/3);
if ($selectedQuarter < 1 || $selectedQuarter > 4) $selectedQuarter = (int)ceil(date('n')/3);

$qStartMonth = ($selectedQuarter - 1) * 3 + 1;
$qEndMonth = $qStartMonth + 2;
$qStartDate = sprintf('%04d-%02d-01', $selectedYear, $qStartMonth);
$qEndDate = date('Y-m-d', strtotime(sprintf('%04d-%02d-01', $selectedYear, $qEndMonth) . ' +1 month -1 day'));

$excludeArchivedCondition = "NOT EXISTS (
    SELECT 1 FROM report_archives a
    WHERE a.report_id = r.id
      AND (a.is_archived = 1 OR a.is_deleted = 1)
)";

// Users counts
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalAdmins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE user_type='CNO'")->fetchColumn();
$totalBNS = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE user_type='BNS'")->fetchColumn();

// Total reports (based on bns_reports.year)
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

// Approved / Pending / Rejected counts
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

// Monthly trend
$months = $monthLabels = $monthCounts = [];
for ($m = $qStartMonth; $m <= $qEndMonth; $m++) {
    $months[] = $m;
    $monthLabels[] = date('M', mktime(0,0,0,$m,1,$selectedYear));
}
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
foreach ($months as $m) $monthCounts[] = $monthlyMap[$m] ?? 0;

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
    LIMIT 3
");
$barangayStmt->execute([':year' => $selectedYear]);
$barangayRows = $barangayStmt->fetchAll(PDO::FETCH_ASSOC);
$barangayLabels = [];
$barangayCounts = [];
foreach ($barangayRows as $br) {
    $barangayLabels[] = $br['barangay'];
    $barangayCounts[] = (int)$br['total'];
}

// Pagination for main table
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalRowsStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reports r
    JOIN bns_reports b ON r.id = b.report_id
    LEFT JOIN report_archives a ON r.id = a.report_id 
        AND (a.is_deleted = 0 OR a.is_deleted IS NULL) 
        AND (a.is_archived = 0 OR a.is_archived IS NULL)
    WHERE r.status IN ('Pending','Rejected') 
      AND r.is_submitted = 1
      AND b.year = :year
      AND {$excludeArchivedCondition}
");
$totalRowsStmt->execute([':year' => $selectedYear]);
$totalRows = (int)$totalRowsStmt->fetchColumn();
$totalPages = ($totalRows > 0) ? (int)ceil($totalRows / $limit) : 1;

$stmt = $pdo->prepare("
    SELECT r.id, u.profile_pic, u.username AS full_name, u.barangay, b.title, r.status, r.report_time, r.report_date
    FROM reports r
    JOIN users u ON r.user_id = u.id
    JOIN bns_reports b ON r.id = b.report_id
    LEFT JOIN report_archives a ON r.id = a.report_id 
        AND (a.is_deleted = 0 OR a.is_deleted IS NULL) 
        AND (a.is_archived = 0 OR a.is_archived IS NULL)
    WHERE r.status IN ('Pending','Rejected','Approved') 
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

/*
 * Utility function for pagination links
 */
function buildQuery($overrides = []) {
    $q = array_merge($_GET, $overrides);
    return http_build_query($q);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CNO | Dashboard</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex flex-col h-screen">
  <?php include 'header.php'; ?>

  <div class="flex flex-1 ">
    <!-- Main content -->
    <main class="flex-1 flex flex-col p-4 ">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Dashboard</h2>
        <div class="flex items-center gap-3">
          <form id="filterForm" method="get" class="flex items-center gap-2">
    <label for="year" class="mr-2 font-semibold">Year:</label>
    <select name="year" id="year" class="px-3 py-2 border rounded" onchange="this.form.submit()">
        <?php foreach ($yearOptions as $y): ?>
            <option value="<?= htmlspecialchars($y) ?>" <?= ((int)$y === $selectedYear) ? 'selected' : '' ?>>
                <?= htmlspecialchars($y) ?>
            </option>
        <?php endforeach; ?>
    </select>
             <label for="year" class="mr-2 font-semibold">Quarter:</label>
            <select name="quarter" class="px-3 py-2 border rounded" onchange="this.form.submit()">
              <?php for ($q = 1; $q <= 4; $q++):
                $sel = ($q == $selectedQuarter) ? 'selected' : '';
              ?>
                <option value="<?= $q ?>" <?= $sel ?>>Q<?= $q ?></option>
              <?php endfor; ?>
            </select>
          </form>
        </div>
      </div>

      <!-- Cards -->
      <div class="flex flex-wrap gap-4 mb-6">
        <!-- Users Card -->
        <div class="flex-1 min-w-[220px] bg-[#064e3b] text-white rounded-lg shadow relative">
          <div class="flex items-center h-28 gap-4 p-5 rounded-lg shadow bg-[#003d3c] text-white">
            <div class="text-5xl mr-4 text-[#06adb3] cursor-pointer" onclick="window.location.href='users.php'">
              <i class="fa fa-users"></i>
            </div>
            <div>
              <h3 class="font-semibold text-2xl">Total Users: <?= $totalUsers ?></h3>
              <p class="text-lg">CNO: <?= $totalAdmins ?> | BNS: <?= $totalBNS ?></p>
            </div>
          </div>
        </div>

        <!-- Reports Card -->
        <div class="flex-1 min-w-[220px] bg-[#0c4a6e] text-white rounded-lg shadow relative cursor-pointer">
          <div class="flex items-center h-28 gap-4 p-5 rounded-lg shadow bg-[#0c4a6e] text-white">
            <div class="text-5xl mr-4 text-[#e0e0e0] cursor-pointer" onclick="window.location.href='cno_reports.php?<?= htmlspecialchars(buildQuery()) ?>'">
              <i class="fa fa-file-alt"></i>
            </div>
            <div>
              <h3 class="font-semibold text-2xl">Total Reports: <?= $totalReports ?></h3>
              <p class="text-lg">Approved: <?= $approvedReports ?> | Pending: <?= $pendingReports ?> | Rejected: <?= $rejectedReports ?></p>
            </div>
          </div>
        </div>

        <!-- Barangays Card -->
        <div class="flex-1 min-w-[220px] bg-[#115e59] text-white rounded-lg shadow relative cursor-pointer">
          <div class="flex items-center h-28 gap-4 p-5 rounded-lg shadow bg-[#115e59] text-white">
            <div class="text-5xl mr-4 text-[#071d10] cursor-pointer" onclick="window.location.href='nutritional_map.php'">
              <i class="fa fa-map-marker-alt"></i>
            </div>
            <div>
              <h3 class="font-semibold text-2xl">Total Barangays: <?= $totalBarangays ?></h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts: grid 1x3 -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded shadow p-4">
          <h3 class="font-bold mb-2 text-center">Quarter Monthly Trend (<?= htmlspecialchars("Q{$selectedQuarter} {$selectedYear}") ?>)</h3>
          <canvas id="monthlyChart" style="max-height:220px;"></canvas>
        </div>

        <div class="bg-white rounded shadow p-4">
          <h3 class="font-bold mb-2 text-center">Report Status (<?= htmlspecialchars("{$selectedYear}") ?>)</h3>
          <canvas id="statusChart" style="max-height:220px;"></canvas>
        </div>

        <div class="bg-white rounded shadow p-4">
          <h3 class="font-bold mb-2 text-center">Top Barangays (<?= htmlspecialchars("{$selectedYear}") ?>)</h3>
          <canvas id="barangayChart" style="max-height:220px;"></canvas>
          <!-- small ranking list -->
          <div class="mt-3">
            <ol class="list-decimal list-inside text-sm text-gray-700">
              <?php if ($barangayRows): ?>
                <?php foreach ($barangayRows as $br): ?>
                  <li><?= htmlspecialchars($br['barangay']) ?> — <?= (int)$br['total'] ?> reports</li>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="text-gray-400">No barangay data for selected quarter</li>
              <?php endif; ?>
            </ol>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="flex-1 bg-white rounded shadow flex flex-col overflow-hidden">
        <div class="flex justify-between items-center py-3 px-4 font-bold border-b text-gray-700">
          <span>Reports</span> 
            <input id="tableSearch" type="text" placeholder="Search Reports"  class="px-3 py-2 w-60 border border-gray-300 rounded focus:ring-1 focus:ring-teal-500 focus:outline-none">
          <a href="cno_reports.php?<?= htmlspecialchars(buildQuery()) ?>" class="text-blue-700 text-sm hover:underline">View All</a>
        </div>
        <div class="flex-1 overflow-auto">
          <table id="reportsTable" class="w-full text-sm border-collapse">
            <thead class="bg-[#009688] text-white">
              <tr>
                <th class="px-4 py-2 text-left">Name</th>
                <th class="px-4 py-2 text-left">Title</th>
                <th class="px-4 py-2 text-left">Barangay</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-left">Time</th>
                <th class="px-4 py-2 text-left">Date</th>
                <th class="px-4 py-2 text-left">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            <?php if($allReports): ?>
              <?php foreach($allReports as $r): ?>
              <tr class="reports-row">
                <td class="px-4 py-2 flex items-center">
                  <?php $pic = (!empty($r['profile_pic']) && file_exists("../uploads/".$r['profile_pic'])) ? $r['profile_pic'] : 'default.png'; ?>
                  <img src="../uploads/<?= htmlspecialchars($pic) ?>" class="w-7 h-7 rounded-full mr-2 object-cover" alt="Profile">
                  <?= htmlspecialchars($r['full_name']) ?>
                </td>
                <td class="px-4 py-2"><?= htmlspecialchars($r['title']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r['barangay']) ?></td>
                <td class="px-4 py-2">
                  <span class="px-2 py-1 rounded-full text-white text-xs <?= $r['status']=='Pending'?'bg-cyan-500':($r['status']=='Approved'?'bg-green-500':'bg-red-500') ?>"><?= $r['status'] ?></span>
                </td>
                <td class="px-4 py-2"><?= htmlspecialchars($r['report_time']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($r['report_date']) ?></td>
                <td class="px-4 py-2"><a href="view_report.php?id=<?= (int)$r['id'] ?>" class="bg-blue-500 px-2 py-1 rounded text-white text-xs">View</a></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="px-4 py-2 text-center text-gray-400">No reports for selected quarter</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center items-center gap-2 py-3">
          <?php
            $maxLinks = 5;
            $start = max(1, $page - floor($maxLinks / 2));
            $end = min($totalPages, $start + $maxLinks - 1);
            if ($end - $start < $maxLinks - 1) $start = max(1, $end - $maxLinks + 1);
          ?>
          <?php if ($page>1): ?><a href="?<?= htmlspecialchars(buildQuery(['page' => $page-1])) ?>" class="px-3 py-1 border rounded">Prev</a><?php else:?><span class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed">Prev</span><?php endif; ?>
          <?php if ($start>1): ?><a href="?<?= htmlspecialchars(buildQuery(['page' => 1])) ?>" class="px-3 py-1 border rounded">1</a><?php if($start>2):?><span class="px-2">...</span><?php endif;?><?php endif;?>
          <?php for($i=$start;$i<=$end;$i++): ?>
          <a href="?<?= htmlspecialchars(buildQuery(['page' => $i])) ?>" class="px-3 py-1 border rounded <?= $i==$page?'bg-teal-500 text-white':'' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if($end<$totalPages):?><?php if($end<$totalPages-1):?><span class="px-2">...</span><?php endif;?><a href="?<?= htmlspecialchars(buildQuery(['page' => $totalPages])) ?>" class="px-3 py-1 border rounded"><?= $totalPages ?></a><?php endif;?>
          <?php if($page<$totalPages):?><a href="?<?= htmlspecialchars(buildQuery(['page' => $page+1])) ?>" class="px-3 py-1 border rounded">Next</a><?php else:?><span class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed">Next</span><?php endif;?>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Table search
document.getElementById("tableSearch").addEventListener("keyup", function() {
  const filter = this.value.toLowerCase();
  document.querySelectorAll(".reports-row").forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? "" : "none";
  });
});

// Chart data from PHP
const monthlyLabels = <?= json_encode($monthLabels) ?>;
const monthlyData = <?= json_encode($monthCounts) ?>;

const statusLabels = <?= json_encode($statusLabels) ?>;
const statusData = <?= json_encode($statusCounts) ?>;

const barangayLabels = <?= json_encode($barangayLabels) ?>;
const barangayData = <?= json_encode($barangayCounts) ?>;

// Monthly Line Chart (trend)
const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctxMonthly, {
  type: 'line', // or 'bar' if you prefer bars
  data: {
    labels: monthlyLabels,
    datasets: [{
      label: 'Reports',
      data: monthlyData,
      borderWidth: 2,
      tension: 0.3,
      fill: false,
      backgroundColor: '#06adb3',
      borderColor: '#06adb3',
      pointRadius: 5,
      pointHoverRadius: 7
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      tooltip: { enabled: true }, // disable tooltip on hover
    },
    scales: {
      x: {
        ticks: {
          callback: function(value, index) {
            // Show the report count below the month label
            return monthlyLabels[index] + ' (' + monthlyData[index] + ')';
          }
        }
      },
      y: {
        beginAtZero: true,
        stepSize: 1
      }
    }
  }
});

// Status Pie
// Map the colors based on status
const statusColors = statusLabels.map(label => {
  if(label === 'Approved') return '#009688'; // green
  if(label === 'Rejected') return '#ef4444'; // red
  if(label === 'Pending') return '#30c7ecff';  // teal-600
  return '#6b7280'; // fallback gray
});

const ctxStatus = document.getElementById('statusChart').getContext('2d');
new Chart(ctxStatus, {
  type: 'pie',
  data: {
    labels: statusLabels,
    datasets: [{
      data: statusData,
      backgroundColor: statusColors,
      borderWidth: 1
    }]
  },
  options: { 
    responsive: true, 
    maintainAspectRatio: false 
  }
});

// Barangay Bar
const ctxBarangay = document.getElementById('barangayChart').getContext('2d');
new Chart(ctxBarangay, {
  type: 'bar',
  data: {
    labels: barangayLabels,
    datasets: [{
      label: 'Reports',
      data: barangayData,
      borderWidth: 1
    }]
  },
  options: { responsive: true, maintainAspectRatio: false }
});
</script>
</body>
</html>
