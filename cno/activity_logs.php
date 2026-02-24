<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';

// ✅ Only CNO
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}

// --- Pagination ---
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// --- Search & Sort ---
$search = $_GET['search'] ?? '';
$sortOpt = $_GET['sort'] ?? 'new';  // ’new’ default
// sortOpt can be: 'new', 'old', 'az', 'za'
$allowedSort = ['new', 'old', 'az', 'za'];
if (!in_array($sortOpt, $allowedSort)) {
    $sortOpt = 'new';
}

// Build ORDER BY clause
$orderClause = "ORDER BY al.created_at DESC";  // default new → old
if ($sortOpt === 'old') {
    $orderClause = "ORDER BY al.created_at ASC";
} elseif ($sortOpt === 'az') {
    $orderClause = "ORDER BY u.first_name ASC, u.last_name ASC";
} elseif ($sortOpt === 'za') {
    $orderClause = "ORDER BY u.first_name DESC, u.last_name DESC";
}

// --- Total rows for pagination (consider search)
$countSql = "
    SELECT COUNT(*) 
    FROM activity_logs al
    JOIN users u ON al.user_id = u.id
    WHERE 1=1
";
if ($search !== '') {
    $s = "%". $search ."%";
    $countSql .= " AND (
        u.first_name LIKE :search
        OR u.last_name LIKE :search
        OR al.action LIKE :search
        OR al.details LIKE :search
    )";
}
$countStmt = $pdo->prepare($countSql);
if ($search !== '') {
    $countStmt->bindValue(':search', $s, PDO::PARAM_STR);
}
$countStmt->execute();
$totalRows = $countStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// --- Fetch logs with sort, search, pagination ---
$sql = "
    SELECT al.id, al.action, al.details, al.created_at,
           u.first_name, u.last_name, u.user_type, u.barangay
    FROM activity_logs al
    JOIN users u ON al.user_id = u.id
    WHERE 1=1
";
if ($search !== '') {
    $sql .= " AND (
        u.first_name LIKE :search
        OR u.last_name LIKE :search
        OR al.action LIKE :search
        OR al.details LIKE :search
    )";
}
$sql .= " $orderClause LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if ($search !== '') {
    $stmt->bindValue(':search', $s, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CNO | Activity Logs</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 h-screen flex flex-col">
  <?php include 'header.php'; ?>
  <div class="flex flex-1 overflow-hidden">
    <main class="flex-1 p-4 flex flex-col">
      <h2 class="text-xl font-semibold mb-4">Activity Logs</h2>

      <!-- Filters -->
      <div class="flex flex-wrap gap-2 items-center mb-4">
        <input type="text" id="logSearch" placeholder="Search logs..." class="px-3 py-2 border border-gray-300 rounded-md w-64">
        <form method="get" class="flex items-center gap-2">
          <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md">
            <option value="new" <?= $sortOpt === 'new' ? 'selected' : '' ?>>New → Old</option>
            <option value="old" <?= $sortOpt === 'old' ? 'selected' : '' ?>>Old → New</option>
            <option value="az" <?= $sortOpt === 'az' ? 'selected' : '' ?>>A → Z</option>
            <option value="za" <?= $sortOpt === 'za' ? 'selected' : '' ?>>Z → A</option>
          </select>
          <input type="hidden" name="page" value="<?= $page ?>">
        </form>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-lg shadow flex-1 flex flex-col overflow-hidden">
        <div class="overflow-x-auto flex-1">
          <table class="min-w-full table-fixed">
            <thead class="bg-teal-600 text-white">
              <tr>
                <th class="px-4 py-2 text-left">Date & Time</th>
                <th class="px-4 py-2 text-left">User</th>
                <th class="px-4 py-2 text-left">Role</th>
                <th class="px-4 py-2 text-left">Barangay</th>
                <th class="px-4 py-2 text-left">Action</th>
                <th class="px-4 py-2 text-left">Details</th>
              </tr>
            </thead>
            <tbody class="text-gray-900">
              <?php if ($logs): ?>
                <?php foreach ($logs as $log): ?>
                  <tr class="border-b">
                    <td class="px-4 py-2"><?= htmlspecialchars($log['created_at']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($log['first_name'].' '.$log['last_name']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($log['user_type']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($log['barangay']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($log['action']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($log['details']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center py-4 text-gray-500">No logs found</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center flex-wrap gap-2">
          <?php
            $maxLinks = 5;
            $start = max(1, $page - floor($maxLinks / 2));
            $end = min($totalPages, $start + $maxLinks - 1);
            if ($end - $start < $maxLinks - 1) { $start = max(1, $end - $maxLinks + 1); }
          ?>
          <?php if ($page > 1): ?>
            <a href="?page=<?= $page-1 ?>&sort=<?= $sortOpt ?>" class="px-3 py-1 border rounded hover:bg-teal-600 hover:text-white">Prev</a>
          <?php else: ?>
            <span class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed">Prev</span>
          <?php endif; ?>

          <?php if ($start > 1): ?>
            <a href="?page=1&sort=<?= $sortOpt ?>" class="px-3 py-1 border rounded hover:bg-teal-600 hover:text-white">1</a>
            <?php if ($start > 2): ?><span class="px-2">...</span><?php endif; ?>
          <?php endif; ?>

          <?php for ($i = $start; $i <= $end; $i++): ?>
            <a href="?page=<?= $i ?>&sort=<?= $sortOpt ?>" class="px-3 py-1 border rounded <?= $i === $page ? 'bg-teal-600 text-white' : 'hover:bg-teal-600 hover:text-white' ?>"><?= $i ?></a>
          <?php endfor; ?>

          <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?><span class="px-2">...</span><?php endif; ?>
            <a href="?page=<?= $totalPages ?>&sort=<?= $sortOpt ?>" class="px-3 py-1 border rounded hover:bg-teal-600 hover:text-white"><?= $totalPages ?></a>
          <?php endif; ?>

          <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>&sort=<?= $sortOpt ?>" class="px-3 py-1 border rounded hover:bg-teal-600 hover:text-white">Next</a>
          <?php else: ?>
            <span class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed">Next</span>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>

  <script>
  // Client-side filter
  document.getElementById("logSearch").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("tbody tr");
    rows.forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(filter) ? "" : "none";
    });
  });
  </script>
</body>
</html>
