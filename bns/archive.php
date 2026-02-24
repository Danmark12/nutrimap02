  <?php
  session_start();
  require '../db/config.php';

  // ✅ Require login
  if (!isset($_SESSION['user_id'])) {
      header("Location: ../auth/login.php");
      exit();
  }

$userId   = $_SESSION['user_id'];
$userType = $_SESSION['user_type']; // 'BNS' or 'CNO'

// ✅ Activity log function
function logActivity($pdo, $user_id, $action) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->execute([$user_id, $action]);
}

// ✅ Handle bulk actions (Delete All only)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_all'])) {
        // 🔹 STEP 1: Mark all as deleted for this user
        $delete = $pdo->prepare("
            UPDATE report_archives 
            SET is_deleted = 1, deleted_at = NOW()
            WHERE user_id = ? AND user_type = ? AND is_archived = 1
        ");
        $delete->execute([$userId, $userType]);

        // 🔹 STEP 2: Check which reports are now deleted by BOTH users
        $checkBoth = $pdo->query("
            SELECT report_id
            FROM report_archives
            GROUP BY report_id
            HAVING SUM(CASE WHEN user_type='BNS' AND is_deleted=1 THEN 1 ELSE 0 END) > 0
               AND SUM(CASE WHEN user_type='CNO' AND is_deleted=1 THEN 1 ELSE 0 END) > 0
        ")->fetchAll(PDO::FETCH_COLUMN);

        // 🔹 STEP 3: Permanently delete those reports from all tables
        if ($checkBoth) {
            $in = str_repeat('?,', count($checkBoth) - 1) . '?';
            $pdo->prepare("DELETE FROM bns_reports WHERE report_id IN ($in)")->execute($checkBoth);
            $pdo->prepare("DELETE FROM reports WHERE id IN ($in)")->execute($checkBoth);
            $pdo->prepare("DELETE FROM report_archives WHERE report_id IN ($in)")->execute($checkBoth);
        }

        logActivity($pdo, $userId, "Deleted all archived reports (checked for both sides)");
        header("Location: archive.php?msg=deleted_all");
        exit();
    }
}

// ✅ Handle optional messages
$message = $_GET['msg'] ?? '';

// ✅ Fetch archived reports for the logged-in user only
$stmt = $pdo->prepare("
    SELECT r.*, u.username, b.title, b.barangay, a.archived_at
    FROM reports r
    JOIN users u ON r.user_id = u.id
    LEFT JOIN bns_reports b ON b.report_id = r.id
    INNER JOIN report_archives a 
      ON a.report_id = r.id 
      AND a.user_id = :uid 
      AND a.user_type = :utype
      AND a.is_archived = 1
      AND (a.is_deleted = 0 OR a.is_deleted IS NULL)
    ORDER BY a.archived_at DESC
");
$stmt->execute([
    'uid' => $userId,
    'utype' => $userType
]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>BNS | Archive</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { margin:0; font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; }
    .layout { display:flex; height:100vh; flex-direction:column; }
    .body-layout { flex:1; display:flex; }
    .content { flex:1; padding:15px; display:flex; flex-direction:column; }

    .card { background:#fff; border:1px solid #ccc; border-radius:8px; padding:15px; margin-bottom:15px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    .toolbar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .toolbar-left input { padding:8px 10px; border:1px solid #ccc; border-radius:6px; width:240px; }
    .toolbar-right select, .toolbar-right button { padding:8px; border:1px solid #ccc; border-radius:6px; margin-left:5px; cursor:pointer; }

    .archive-list { display:flex; flex-direction:column; gap:8px; }
    .archive-item {
      background:#fff; border:1px solid #ccc; border-radius:6px; padding:12px;
      display:flex; justify-content:space-between; align-items:center;
      position:relative; box-shadow:0 1px 3px rgba(0,0,0,0.08);
    }
    .archive-item:hover { background:#f0f8ff; cursor:pointer; }
    .archive-title { font-size:15px; font-weight:600; color:#333; }
    .archive-meta { font-size:13px; color:#555; margin-top:3px; }

    .menu-container { position:relative; }
    .menu-btn { background:none; border:none; cursor:pointer; font-size:18px; color:#555; }
    .menu-content {
      display:none; position:absolute; right:0; top:25px; background:#fff; border:1px solid #ccc;
      border-radius:4px; box-shadow:0 2px 6px rgba(0,0,0,0.15); z-index:10; min-width:160px;
    }
    .menu-content a { display:block; padding:8px 12px; font-size:14px; color:#333; text-decoration:none; }
    .menu-content a:hover { background:#f5f5f5; }
    .menu-container.active .menu-content { display:block; }

    .status { padding:3px 8px; border-radius:10px; font-size:12px; color:#fff; }
    .status.Pending { background:#ffc107; color:#000; }
    .status.Approved { background:#28a745; }
    .status.Rejected { background:#dc3545; }
    .status.Archived { background:#6c757d; }
  </style>
</head>
<body>
  <div class="layout">
    <?php include 'header.php'; ?>
    <div class="body-layout">
      <div class="content">
        <div class="card">
            <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px;">
                <h3 style="margin:0;">Archive</h3>
                <div class="toolbar-left" style="position:relative; margin-left:15px;">
                  <i class="fa fa-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#888;"></i>
                  <input type="text" id="search" placeholder="Search reports..." style="padding-left:30px;">
                </div>
                <div class="toolbar-right" style="margin-left:auto;">
                  <label for="sort">Sort By:</label>
                  <select id="sort">
                    <option value="title">A → Z</option>
                    <option value="date">Newest → Oldest</option>
                  </select>
              
                  <!-- ✅ Fixed Bulk Actions -->
                  <form style="display:inline;" method="post" onsubmit="return confirm('Are you sure you want to delete all archived reports?');">
                    <a href="archive/restore_all_report.php" 
                       onclick="return confirm('Are you sure you want to restore all archived reports?');" 
                       class="btn" 
                       style="padding:7px 8px; border:1px solid #ccc; border-radius:5px; text-decoration:none; color:#000; background:#f0f0f0; margin-right:0px;">
                       <i class="fa fa-undo"></i> Restore All
                    </a>
                    <button type="submit" name="delete_all">
                      <i class="fa fa-trash"></i> Delete All
                    </button>
                  </form>

                </div>
            </div>
        </div>

        <div class="archive-list" id="archiveList">
          <?php if ($message === 'no_archive'): ?>
            <p style="color:#555;">No archive report found.</p>
          <?php elseif ($reports): ?>
            <?php foreach ($reports as $r): ?>
              <div class="archive-item">
                <div>
                  <div class="archive-title"><?= htmlspecialchars($r['title'] ?? 'Untitled Report') ?></div>
                  <div class="archive-meta">
                    User: <?= htmlspecialchars($r['username']) ?> | 
                    Barangay: <?= htmlspecialchars($r['barangay'] ?? '-') ?> | 
                    <?= date("m-d-Y", strtotime($r['report_date'])) ?> <?= date("h:i a", strtotime($r['report_time'])) ?>
                  </div>
                </div>
                <div class="menu-container" onclick="event.stopPropagation();">
                  <button class="menu-btn"><i class="fa fa-ellipsis-v"></i></button>
                  <div class="menu-content">
                    <a href="view_report.php?id=<?= $r['id'] ?>" target="_blank"><i class="fa fa-eye"></i> View</a>
                    <a href="archive/restore_report.php?id=<?= $r['id'] ?>" onclick="return confirm('Restore this report?')"><i class="fa fa-undo"></i> Restore</a>
                    <a href="archive/delete_report.php?id=<?= $r['id'] ?>" onclick="return confirm('Delete this report permanently?')"><i class="fa fa-trash"></i> Delete Permanently</a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="color:#555;">No archived reports found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

<script>
document.querySelectorAll('.menu-btn').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.stopPropagation();
    this.parentElement.classList.toggle('active');
  });
});
document.addEventListener('click', () => {
  document.querySelectorAll('.menu-container').forEach(c => c.classList.remove('active'));
});

document.getElementById('search').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.archive-item').forEach(item => {
    const title = item.querySelector('.archive-title').textContent.toLowerCase();
    const meta = item.querySelector('.archive-meta').textContent.toLowerCase();
    item.style.display = title.includes(q) || meta.includes(q) ? '' : 'none';
  });
});
</script>
</body>
</html>
