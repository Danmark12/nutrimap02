<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'BNS') {
    header("Location: ../login.php");
    exit();
}
$userId = $_SESSION['user_id'];

// Current user info
$userStmt = $pdo->prepare("SELECT first_name, last_name, barangay, profile_pic FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);
$displayName = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
$barangay    = htmlspecialchars($user['barangay']);

// Stat counts
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM reports r JOIN bns_reports b ON r.id = b.report_id WHERE r.user_id = ? AND r.is_submitted = 1");
$totalStmt->execute([$userId]); $totalReports = $totalStmt->fetchColumn();

$approvedStmt = $pdo->prepare("SELECT COUNT(*) FROM reports r JOIN bns_reports b ON r.id = b.report_id WHERE r.user_id = ? AND r.status = 'Approved' AND r.is_submitted = 1 AND NOT EXISTS (SELECT 1 FROM report_archives a WHERE a.report_id = r.id AND a.is_archived = 1)");
$approvedStmt->execute([$userId]); $approvedReports = $approvedStmt->fetchColumn();

$pendingStmt = $pdo->prepare("SELECT COUNT(*) FROM reports r JOIN bns_reports b ON r.id = b.report_id WHERE r.user_id = ? AND r.status = 'Pending' AND r.is_submitted = 1 AND NOT EXISTS (SELECT 1 FROM report_archives a WHERE a.report_id = r.id AND a.is_archived = 1)");
$pendingStmt->execute([$userId]); $pendingReports = $pendingStmt->fetchColumn();

$rejectedStmt = $pdo->prepare("SELECT COUNT(*) FROM reports r JOIN bns_reports b ON r.id = b.report_id WHERE r.user_id = ? AND r.status = 'Rejected' AND r.is_submitted = 1 AND NOT EXISTS (SELECT 1 FROM report_archives a WHERE a.report_id = r.id AND a.is_archived = 1)");
$rejectedStmt->execute([$userId]); $rejectedReports = $rejectedStmt->fetchColumn();

// Latest report for timeline
$latestStmt = $pdo->prepare("SELECT r.id, r.status, r.report_date, r.report_time, r.is_submitted, b.title FROM reports r JOIN bns_reports b ON r.id = b.report_id WHERE r.user_id = ? ORDER BY r.report_date DESC, r.report_time DESC LIMIT 1");
$latestStmt->execute([$userId]);
$latestReport = $latestStmt->fetch(PDO::FETCH_ASSOC);

// Get all approved reports for this BNS user (by report ID, not just year)
$reportsStmt = $pdo->prepare("
    SELECT r.id, r.report_date, r.status, b.year, b.title
    FROM reports r
    JOIN bns_reports b ON r.id = b.report_id
    WHERE r.user_id = ? AND r.status = 'Approved'
    ORDER BY r.report_date DESC
");
$reportsStmt->execute([$userId]);
$availableReports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get selected report ID from URL, default to latest report
$selectedReportId = isset($_GET['report_id']) ? (int)$_GET['report_id'] : ($availableReports[0]['id'] ?? null);

// Find the selected report data for display
$selectedReportInfo = null;
foreach ($availableReports as $rep) {
    if ($rep['id'] == $selectedReportId) {
        $selectedReportInfo = $rep;
        break;
    }
}

// Fetch nutrition snapshot for selected report ID
$nutriStmt = $pdo->prepare("
    SELECT b.*
    FROM bns_reports b
    JOIN reports r ON b.report_id = r.id
    WHERE r.user_id = ? AND r.status = 'Approved' AND r.id = ?
    LIMIT 1
");
$nutriStmt->execute([$userId, $selectedReportId]);
$n = $nutriStmt->fetch(PDO::FETCH_ASSOC);


// Recent activity - only approved reports
$activityStmt = $pdo->prepare("SELECT r.id, r.status, r.report_date, r.report_time, b.title, r.is_submitted FROM reports r JOIN bns_reports b ON r.id = b.report_id WHERE r.user_id = ? AND r.status = 'Approved' ORDER BY r.report_date DESC, r.report_time DESC LIMIT 5");
$activityStmt->execute([$userId]);
$recentActivity = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

// Paginated pending/rejected table
$limit  = 8;
$page   = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$totalRowsStmt = $pdo->prepare("SELECT COUNT(*) FROM reports r JOIN bns_reports b ON r.id = b.report_id WHERE r.user_id = ? AND (r.status='Pending' OR r.status='Rejected') AND r.is_submitted=1");
$totalRowsStmt->execute([$userId]); $totalRows = $totalRowsStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

$stmt = $pdo->prepare("SELECT r.id, u.profile_pic, u.username, b.title, r.status, r.report_time, r.report_date FROM reports r JOIN users u ON r.user_id = u.id JOIN bns_reports b ON r.id = b.report_id LEFT JOIN report_archives a ON r.id = a.report_id AND (a.is_deleted=0 OR a.is_deleted IS NULL) AND (a.is_archived=0 OR a.is_archived IS NULL) WHERE r.user_id=:userId AND (r.status='Pending' OR r.status='Rejected') AND r.is_submitted=1 ORDER BY r.report_date DESC, r.report_time DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':userId',$userId,PDO::PARAM_INT);
$stmt->bindValue(':limit',$limit,PDO::PARAM_INT);
$stmt->bindValue(':offset',$offset,PDO::PARAM_INT);
$stmt->execute();
$myReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

// Helper: safe int
function si($v){ return isset($v) ? (int)$v : 0; }
function sp($v){ return isset($v) ? (float)$v : 0; }

// ============================================================
// COMPLETE NUTRITION SNAPSHOT DATA MAPPING FROM DB COLUMNS
// ============================================================
if ($n) {
    $reportYear = $n['year'];
    $reportDate = $selectedReportInfo ? date('M d, Y', strtotime($selectedReportInfo['report_date'])) : '';
    $reportTitle = $selectedReportInfo ? htmlspecialchars($selectedReportInfo['title']) : '';
    $total_population = si($n['ind1']);
    $male_population = si($n['ind_male']);
    $female_population = si($n['ind_female']);
    $total_households = si($n['ind2']);
    $total_families = si($n['ind3']);
    $pregnant_women = si($n['ind6a']);
    $lactating_women = si($n['ind6b']);
    $est_preschool_pop = si($n['ind8']);
    $actual_measured = si($n['ind9']);
    $opt_coverage = sp($n['ind9a']);
    
    // Nutritional status
    $severely_underweight = si($n['ind9b1_no']);
    $underweight = si($n['ind9b2_no']);
    $normal_weight = si($n['ind9b3_no']);
    $severely_wasted = si($n['ind9b4_no']);
    $wasted = si($n['ind9b5_no']);
    $overweight = si($n['ind9b6_no']);
    $obese = si($n['ind9b7_no']);
    $severely_stunted = si($n['ind9b8_no']);
    $stunted = si($n['ind9b9_no']);
    
    // Age brackets
    $infants_0_5mo = si($n['ind10']);
    $infants_6_11mo = si($n['ind11']);
    $children_0_23mo = si($n['ind12']);
    $children_24_59mo = si($n['ind14']);
    
    // At-risk families
    $families_wasted = si($n['ind15']);
    $families_stunted = si($n['ind16']);
    
    // Educational institutions
    $daycare_public = si($n['ind17a_public']);
    $daycare_private = si($n['ind17a_private']);
    $elem_public = si($n['ind17b_public']);
    $elem_private = si($n['ind17b_private']);
    
    // School children
    $kindergarten_enrolled = si($n['ind18']);
    $total_school_children = si($n['ind19']);
    $school_children_weighed = si($n['ind20']);
    $school_coverage_pct = sp($n['ind21']);
    
    // School nutritional status
    $sa_severely_wasted = si($n['ind22a_no']);
    $sa_wasted = si($n['ind22b_no']);
    $sa_severely_stunted = si($n['ind22c_no']);
    $sa_stunted = si($n['ind22d_no']);
    $sa_normal = si($n['ind22e_no']);
    $sa_overweight = si($n['ind22f_no']);
    $sa_obese = si($n['ind22g_no']);
    $sa_total = $sa_severely_wasted + $sa_wasted + $sa_severely_stunted + $sa_stunted + $sa_normal + $sa_overweight + $sa_obese;
    
    // Health interventions
    $exclusive_bf = si($n['ind23']);
    $hh_with_severely_wasted_school = si($n['ind24']);
    $dewormed_school_children = si($n['ind25']);
    $fully_immunized = si($n['ind26']);
    $hh_iodized_salt = si($n['ind32']);
    
    // WASH
    $toilet_water_sealed = si($n['ind27a_no']);
    $toilet_antipolo = si($n['ind27b_no']);
    $toilet_open_pit = si($n['ind27c_no']);
    $toilet_shared = si($n['ind27d_no']);
    $toilet_none = si($n['ind27e_no']);
    $total_toilet_households = $toilet_water_sealed + $toilet_antipolo + $toilet_open_pit + $toilet_shared;
    $sanitation_coverage = $total_households > 0 ? round(($total_toilet_households / $total_households) * 100, 1) : 0;
    
    $water_piped = si($n['ind29a_no']);
    $water_spring = si($n['ind29b_no']);
    $water_deep_well_communal = si($n['ind29c_no']);
    $water_deep_well_individual = si($n['ind29d_no']);
    $water_purified = si($n['ind29e_no']);
    $water_shallow_well = si($n['ind29f_no']);
    $water_artesian = si($n['ind29g_no']);
    $safe_water_sources = $water_piped + $water_deep_well_individual + $water_purified;
    $safe_water_coverage = $total_households > 0 ? round(($safe_water_sources / $total_households) * 100, 1) : 0;
    
    $garbage_collection = si($n['ind28a_no']);
    $garbage_compost = si($n['ind28b_no']);
    $garbage_burning = si($n['ind28c_no']);
    $garbage_dumping = si($n['ind28d_no']);
    
    // Livelihood
    $has_veg_garden = si($n['ind30a_no']);
    $has_livestock = si($n['ind30b_no']);
    $has_fishpond = si($n['ind30c_no']);
    $no_garden = si($n['ind30d_no']);
    
    $dwelling_concrete = si($n['ind31a_no']);
    $dwelling_semi_concrete = si($n['ind31b_no']);
    $dwelling_wood = si($n['ind31c_no']);
    $dwelling_nipa = si($n['ind31d_no']);
    $dwelling_barong = si($n['ind31e_no']);
    $dwelling_makeshift = si($n['ind31f_no']);
    
    $eateries = si($n['ind33']);
    $stores_iodized_salt = si($n['ind34']);
    $stores_cooking_oil = si($n['ind35']);
    $bakeries_fortified = si($n['ind36']);
    $bns_workers = si($n['ind37a']);
    $bhw_workers = si($n['ind37b']);
    $hh_4ps_beneficiaries = si($n['ind38']);
    
    // Derived calculations
    $malnourished_count = $severely_underweight + $underweight + $severely_wasted + $wasted;
    $malnutrition_rate = $actual_measured > 0 ? round(($malnourished_count / $actual_measured) * 100, 1) : 0;
    $school_malnourished = $sa_severely_wasted + $sa_wasted + $sa_severely_stunted + $sa_stunted;
    $school_malnutrition_rate = $total_school_children > 0 ? round(($school_malnourished / $total_school_children) * 100, 1) : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>BNS | Dashboard</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<style>
:root {
  --teal-900:#003330;
  --teal-700:#005552;
  --teal-500:#008a85;
  --teal-400:#00b5ae;
  --teal-100:#d4f5f3;
  --teal-50: #edfaf9;
  --amber:   #f59e0b;
  --red:     #ef4444;
  --green:   #22c55e;
  --surface: #f0f4f4;
  --card:    #ffffff;
  --border:  #d8eceb;
  --text:    #0f2e2d;
  --muted:   #5a7878;
}
*{font-family:'Outfit',sans-serif;box-sizing:border-box;}
body{background:var(--surface);color:var(--text);}

@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.fu{animation:fadeUp .5s ease both;}
.fu1{animation-delay:.05s}.fu2{animation-delay:.10s}.fu3{animation-delay:.15s}.fu4{animation-delay:.20s}.fu5{animation-delay:.25s}

.card{background:var(--card);border:1px solid var(--border);border-radius:18px;}
.card-header{padding:0.75rem 1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
.ntab-panel{padding:0.875rem 1rem;}
.card-title{font-size:13px;font-weight:600;color:var(--text);letter-spacing:.02em;display:flex;align-items:center;gap:7px;}
.card-title i{color:var(--teal-500);font-size:14px;}

.stat{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.125rem 1.25rem;display:flex;align-items:center;gap:14px;transition:transform .2s,box-shadow .2s;}
.stat:hover{transform:translateY(-3px);box-shadow:0 10px 28px rgba(0,85,82,.10);}
.stat-icon{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.stat-lbl{font-size:11px;color:var(--muted);font-weight:500;text-transform:uppercase;letter-spacing:.06em;}
.stat-val{font-size:26px;font-weight:700;line-height:1.1;}

.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:600;}
.b-pending {background:#fffbeb;color:#92400e;border:1px solid #fcd34d;}
.b-approved{background:#f0fdf4;color:#166534;border:1px solid #86efac;}
.b-rejected{background:#fef2f2;color:#991b1b;border:1px solid #fca5a5;}

.greet{background:linear-gradient(135deg,var(--teal-900) 0%,var(--teal-700) 60%,var(--teal-500) 100%);border-radius:18px;padding:1.375rem 1.75rem;color:#fff;display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
.greet::before,.greet::after{content:'';position:absolute;border-radius:50%;background:rgba(255,255,255,.05);}
.greet::before{width:200px;height:200px;right:-50px;top:-80px;}
.greet::after{width:140px;height:140px;right:80px;bottom:-70px;}

.alert{border-radius:13px;padding:11px 15px;display:flex;align-items:center;gap:11px;font-size:13px;font-weight:500;margin-bottom:1.125rem;}
.alert-r{background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;}
.alert-p{background:#fffbeb;border:1px solid #fcd34d;color:#92400e;}
.alert-a{background:#f0fdf4;border:1px solid #86efac;color:#166534;}

.tl-wrap{display:flex;align-items:flex-start;padding:1.25rem 1rem 1rem;}
.tl-step{flex:1;display:flex;flex-direction:column;align-items:center;position:relative;}
.tl-step:not(:last-child)::after{content:'';position:absolute;top:15px;left:calc(50% + 15px);width:calc(100% - 30px);height:2px;background:var(--border);}
.tl-step.done:not(:last-child)::after{background:var(--teal-400);}
.tl-dot{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;border:2px solid var(--border);background:#fff;color:var(--muted);z-index:1;}
.tl-dot.done{background:var(--teal-500);border-color:var(--teal-500);color:#fff;}
.tl-dot.active{background:var(--amber);border-color:var(--amber);color:#fff;}
.tl-dot.rej{background:var(--red);border-color:var(--red);color:#fff;}
.tl-lbl{font-size:10px;margin-top:6px;text-align:center;font-weight:500;color:var(--muted);}
.tl-lbl.done{color:var(--teal-500);font-weight:600;}
.tl-lbl.active{color:var(--amber);font-weight:600;}
.tl-lbl.rej{color:var(--red);font-weight:600;}

.ntab{display:flex;gap:4px;padding:0 1.375rem 0;border-bottom:1px solid var(--border);flex-wrap:wrap;}
.ntab-btn{padding:9px 14px;font-size:12px;font-weight:500;color:var(--muted);border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;}
.ntab-btn.active{color:var(--teal-500);border-bottom-color:var(--teal-500);font-weight:600;}
.ntab-panel{display:none;padding:1.125rem 1.375rem;}
.ntab-panel.active{display:block;}

.pb-track{height:8px;background:var(--teal-100);border-radius:6px;overflow:hidden;margin-top:6px;}
.pb-fill{height:100%;border-radius:6px;transition:width 1s ease;}

.act-item{display:flex;align-items:flex-start;gap:11px;padding:9px 0;border-bottom:1px solid var(--border);}
.act-item:last-child{border:none;}
.act-dot{width:8px;height:8px;border-radius:50%;margin-top:5px;}

.dt thead th{background:var(--teal-900);color:#fff;font-size:11px;font-weight:600;text-transform:uppercase;padding:11px 14px;}
.dt tbody tr{border-bottom:1px solid var(--border);}
.dt tbody tr:hover{background:var(--teal-50);}
.dt tbody td{padding:10px 14px;font-size:13px;}

.btn-view{display:inline-flex;align-items:center;gap:4px;background:var(--teal-900);color:#fff;padding:5px 13px;border-radius:8px;font-size:12px;font-weight:500;text-decoration:none;}
.btn-view:hover{background:var(--teal-500);}
.search-wrap{position:relative;}
.search-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;}
.search-inp{border:1px solid var(--border);border-radius:10px;padding:7px 12px 7px 32px;font-size:13px;outline:none;background:var(--card);width:210px;}
.search-inp:focus{border-color:var(--teal-400);box-shadow:0 0 0 3px rgba(0,181,174,.12);}
.pg-btn{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;border:1px solid var(--border);border-radius:7px;font-size:12px;color:var(--text);text-decoration:none;padding:0 7px;}
.pg-btn:hover{background:var(--teal-50);}
.pg-btn.active{background:var(--teal-900);color:#fff;}
.pg-btn.off{opacity:.4;pointer-events:none;}

.donut-center{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;}
.info-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:1rem;}
.info-card{background:var(--teal-50);border:1px solid var(--teal-100);border-radius:14px;padding:0.875rem;text-align:center;}
.info-value{font-size:22px;font-weight:700;color:var(--teal-700);}

/* Report Selector */
.report-selector{padding:5px 12px;border-radius:10px;border:1px solid var(--border);font-size:13px;font-weight:500;background:white;color:var(--teal-700);cursor:pointer;outline:none;min-width:200px;}
.report-selector:focus{border-color:var(--teal-400);box-shadow:0 0 0 2px rgba(0,181,174,.2);}
</style>
</head>
<body>
<div class="flex flex-col h-screen">
<?php include 'header.php'; ?>
<div class="flex flex-1 overflow-hidden">
<main class="flex-1 overflow-y-auto p-5" style="display:flex;flex-direction:column;gap:1.125rem;">

  <!-- Greeting -->
  <div class="greet fu fu1" style="padding: 2.5rem 1.75rem;">
    <div style="z-index:1;">
      <p style="font-size:11px;opacity:.65;letter-spacing:.08em;text-transform:uppercase;margin-bottom:3px;"><?= $greeting ?></p>
      <h1 style="font-size:21px;font-weight:700;margin-bottom:2px;"><?= $displayName ?></h1>
      <p style="font-size:13px;opacity:.7;"><i class="fa fa-map-marker-alt" style="margin-right:5px;"></i>Barangay <?= $barangay ?></p>
    </div>
    <div style="text-align:right;z-index:1;">
      <p style="font-size:10px;opacity:.55;text-transform:uppercase;">Today</p>
      <p style="font-size:15px;font-weight:600;"><?= date('F j, Y') ?></p>
      <p style="font-size:12px;opacity:.6;" id="livetime"></p>
    </div>
  </div>

  <!-- Alert banner -->
  <?php if ($latestReport): ?>
    <?php if ($latestReport['status']==='Rejected'): ?>
    <div class="alert alert-r fu fu2"><i class="fa fa-exclamation-circle fa-lg"></i>
      <span>Your report "<strong><?= htmlspecialchars($latestReport['title']) ?></strong>" was <strong>rejected</strong>.
      <a href="view_report.php?id=<?= $latestReport['id'] ?>" style="text-decoration:underline;margin-left:6px;">View reason →</a></span>
    </div>
    <?php elseif ($latestReport['status']==='Pending' && $latestReport['is_submitted']): ?>
    <div class="alert alert-p fu fu2"><i class="fa fa-hourglass-half fa-lg"></i>
      <span>Your report is <strong>awaiting CNO review</strong> — submitted <?= htmlspecialchars($latestReport['report_date']) ?>.</span>
    </div>
    <?php elseif ($latestReport['status']==='Approved'): ?>
    <div class="alert alert-a fu fu2"><i class="fa fa-check-circle fa-lg"></i>
      <span>Your latest report was <strong>approved</strong> by CNO.</span>
    </div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- Stat cards -->
  <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;">
    <div class="stat fu fu1"><div class="stat-icon" style="background:#e6f5f4;color:var(--teal-700);"><i class="fa fa-file-alt"></i></div><div><p class="stat-lbl">Total</p><p class="stat-val" style="color:var(--teal-700);"><?= $totalReports ?></p></div></div>
    <div class="stat fu fu2"><div class="stat-icon" style="background:#f0fdf4;color:#166534;"><i class="fa fa-check-circle"></i></div><div><p class="stat-lbl">Approved</p><p class="stat-val" style="color:#166534;"><?= $approvedReports ?></p></div></div>
    <div class="stat fu fu3"><div class="stat-icon" style="background:#fffbeb;color:#92400e;"><i class="fa fa-clock"></i></div><div><p class="stat-lbl">Pending</p><p class="stat-val" style="color:#92400e;"><?= $pendingReports ?></p></div></div>
    <div class="stat fu fu4"><div class="stat-icon" style="background:#fef2f2;color:#991b1b;"><i class="fa fa-times-circle"></i></div><div><p class="stat-lbl">Rejected</p><p class="stat-val" style="color:#991b1b;"><?= $rejectedReports ?></p></div></div>
  </div>

  <!-- Row 2: Timeline + Activity -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
    <div class="card fu fu2">
      <div class="card-header"><span class="card-title"><i class="fa fa-route"></i>Latest Report Status</span><?php if($latestReport): ?><span style="font-size:11px;color:var(--muted);"><?= htmlspecialchars($latestReport['report_date']) ?></span><?php endif; ?></div>
      <?php 
      $ts = $latestReport ? $latestReport['status'] : null; 
      $sub = $latestReport && $latestReport['is_submitted'];
      $steps = [
          ['fa-pencil-alt', 'Created', $sub ? 'done' : ($latestReport ? 'active' : '')],
          ['fa-paper-plane', 'Submitted', $sub ? 'done' : ''],
          ['fa-search', 'In Review', ($sub && $ts === 'Pending') ? 'active' : (($ts === 'Approved' || $ts === 'Rejected') ? 'done' : '')],
          [($ts === 'Rejected' ? 'fa-times' : 'fa-check'), ($ts === 'Rejected' ? 'Rejected' : 'Approved'), ($ts === 'Approved' ? 'done' : ($ts === 'Rejected' ? 'rej' : ''))]
      ];
      ?>
      <div class="tl-wrap">
          <?php foreach($steps as $s): ?>
          <div class="tl-step <?= $s[2] ?>">
              <div class="tl-dot <?= $s[2] ?>"><i class="fa <?= $s[0] ?>" style="font-size:11px;"></i></div>
              <span class="tl-lbl <?= $s[2] ?>"><?= $s[1] ?></span>
          </div>
          <?php endforeach; ?>
      </div>
      <?php if(!$latestReport): ?><p style="text-align:center;font-size:13px;color:var(--muted);padding:1rem;">No reports submitted yet.</p><?php endif; ?>
    </div>

    <div class="card fu fu3">
      <div class="card-header"><span class="card-title"><i class="fa fa-history"></i>Recent Activity</span><a href="reports.php" style="font-size:11px;color:var(--teal-500);text-decoration:none;">View all →</a></div>
      <div style="padding:.5rem 1.375rem;"><?php if($recentActivity): foreach($recentActivity as $act): $dc=$act['status']==='Approved'?'#22c55e':($act['status']==='Rejected'?'#ef4444':'#f59e0b'); ?>
        <div class="act-item"><div class="act-dot" style="background:<?=$dc?>;"></div><div style="flex:1;"><p style="font-size:13px;font-weight:500;"><?= htmlspecialchars($act['title']) ?></p><p style="font-size:11px;color:var(--muted);margin-top:2px;"><span class="badge b-<?= strtolower($act['status']) ?>"><?= $act['status'] ?></span> <?= htmlspecialchars($act['report_date']) ?></p></div><a href="view_report.php?id=<?= $act['id'] ?>" style="font-size:11px;color:var(--teal-500);">View →</a></div>
      <?php endforeach; else: ?><p style="text-align:center;font-size:13px;color:var(--muted);padding:1.5rem 0;">No activity yet.</p><?php endif; ?></div>
    </div>
  </div>

  <!-- === BARANGAY SNAPSHOT === -->
  <?php if($n): ?>
  <div class="card fu fu4">
    <div class="card-header">
      <span class="card-title"><i class="fa fa-leaf"></i>Barangay <?= $barangay ?> — Nutrition Snapshot</span>
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <?php if(count($availableReports) > 0): ?>
        <select id="reportSelect" class="report-selector" onchange="window.location.href='?report_id='+this.value">
            <?php foreach($availableReports as $report): ?>
            <option value="<?= $report['id'] ?>" <?= $selectedReportId == $report['id'] ? 'selected' : '' ?>>
                <?= $report['year'] ?> - <?= date('M d, Y', strtotime($report['report_date'])) ?> (<?= htmlspecialchars($report['title']) ?>)
            </option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <span style="font-size:11px;color:var(--muted);">
            Showing: <?= $reportYear ?> · <?= $reportDate ?>
            <?php if($reportTitle): ?> · <?= $reportTitle ?><?php endif; ?>
        </span>
      </div>
    </div>

    <!-- Quick Stats Row (clean minimal) -->
    <div class="info-grid" style="padding:0 1.375rem 0 1.375rem;">
      <div class="info-card"><div class="info-value"><?= number_format($total_population) ?></div><div class="stat-lbl" style="font-size:10px;">Population</div></div>
      <div class="info-card"><div class="info-value"><?= number_format($total_households) ?></div><div class="stat-lbl" style="font-size:10px;">Households</div></div>
      <div class="info-card"><div class="info-value"><?= number_format($actual_measured) ?></div><div class="stat-lbl" style="font-size:10px;">Children Weighed</div></div>
      <div class="info-card"><div class="info-value"><?= $malnutrition_rate ?>%</div><div class="stat-lbl" style="font-size:10px;">Malnutrition Rate</div></div>
    </div>

    <!-- Tabs -->
    <div class="ntab" id="snapTabs">
      <button class="ntab-btn active" data-tab="overview">Weight-for-Age</button>
      <button class="ntab-btn" data-tab="schoolage">School-Age (BMI)</button>
      <button class="ntab-btn" data-tab="health">Health</button>
      <button class="ntab-btn" data-tab="watsan">WASH</button>
      <button class="ntab-btn" data-tab="livelihood">Livelihood</button>
    </div>

    <!-- TAB 1: Weight-for-Age with CHART -->
    <div class="ntab-panel active" id="tab-overview">
      <div style="display:grid;grid-template-columns:220px 1fr;gap:1.5rem;align-items:center;">
        <div style="position:relative;width:220px;height:220px;margin:0 auto;">
          <canvas id="donutChart" width="220" height="220"></canvas>
          <div class="donut-center"><p class="stat-lbl" style="font-size:10px;">Total Weighed</p><p style="font-size:22px;font-weight:700;color:var(--teal-700);"><?= number_format($actual_measured) ?></p><p style="font-size:9px;color:var(--muted);">0-59 months</p></div>
        </div>
        <div>
          <?php $bars=[['Normal',$normal_weight,'#22c55e'],['Underweight',$underweight,'#f59e0b'],['Severely Underweight',$severely_underweight,'#ef4444'],['Wasted',$wasted,'#f97316'],['Severely Wasted',$severely_wasted,'#dc2626'],['Overweight',$overweight,'#a78bfa'],['Obese',$obese,'#8b5cf6']];
          foreach($bars as $b): $pct=$actual_measured>0?round($b[1]/$actual_measured*100,1):0;?>
          <div style="margin-bottom:12px;"><div style="display:flex;justify-content:space-between;font-size:12px;"><span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:<?=$b[2]?>;margin-right:8px;"></span><?=$b[0]?></span><span><strong><?=number_format($b[1])?></strong> <span style="color:var(--muted);">(<?=$pct?>%)</span></span></div><div class="pb-track"><div class="pb-fill" style="width:<?=$pct?>%;background:<?=$b[2]?>;"></div></div></div>
          <?php endforeach; ?>
        </div>
      </div>
      <!-- Stunting row -->
      <div style="margin-top:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div style="background:#fef3c7;border-radius:12px;padding:0.75rem;text-align:center;"><p class="stat-lbl">Stunted</p><p style="font-size:24px;font-weight:700;color:#92400e;"><?= number_format($stunted) ?></p><p class="stat-lbl"><?= $actual_measured>0?round($stunted/$actual_measured*100,1):0?>% of weighed</p></div>
        <div style="background:#fed7aa;border-radius:12px;padding:0.75rem;text-align:center;"><p class="stat-lbl">Severely Stunted</p><p style="font-size:24px;font-weight:700;color:#9a3412;"><?= number_format($severely_stunted) ?></p><p class="stat-lbl"><?= $actual_measured>0?round($severely_stunted/$actual_measured*100,1):0?>% of weighed</p></div>
      </div>
    </div>

    <!-- TAB 2: School-Age with CHART -->
    <div class="ntab-panel" id="tab-schoolage">
      <?php if($total_school_children > 0): ?>
      <div style="display:grid;grid-template-columns:200px 1fr;gap:1.5rem;align-items:center;">
        <div style="position:relative;width:200px;height:200px;margin:0 auto;">
          <canvas id="saDonut" width="200" height="200"></canvas>
          <div class="donut-center"><p style="font-size:18px;font-weight:700;color:var(--teal-700);"><?= number_format($sa_total) ?></p><p style="font-size:9px;color:var(--muted);">school children</p></div>
        </div>
        <div>
          <?php $saBars=[['Normal',$sa_normal,'#22c55e'],['Wasted',$sa_wasted,'#f59e0b'],['Severely Wasted',$sa_severely_wasted,'#ef4444'],['Stunted',$sa_stunted,'#f97316'],['Severely Stunted',$sa_severely_stunted,'#dc2626'],['Overweight',$sa_overweight,'#a78bfa'],['Obese',$sa_obese,'#8b5cf6']];
          foreach($saBars as $b): $pct=$sa_total>0?round($b[1]/$sa_total*100,1):0; if($b[1]==0&&$pct==0) continue;?>
          <div style="margin-bottom:10px;"><div style="display:flex;justify-content:space-between;font-size:12px;"><span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:<?=$b[2]?>;margin-right:8px;"></span><?=$b[0]?></span><span><?=number_format($b[1])?> (<?=$pct?>%)</span></div><div class="pb-track"><div class="pb-fill" style="width:<?=$pct?>%;background:<?=$b[2]?>;"></div></div></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="info-grid" style="margin-top:1rem;">
        <div class="info-card"><div class="info-value"><?= number_format($kindergarten_enrolled + $total_school_children) ?></div><div class="stat-lbl">Total Enrolled (K-6)</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($school_coverage_pct,1) ?>%</div><div class="stat-lbl">Weighing Coverage</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($dewormed_school_children) ?></div><div class="stat-lbl">Dewormed</div></div>
        <div class="info-card"><div class="info-value"><?= $school_malnutrition_rate ?>%</div><div class="stat-lbl">Malnutrition Rate</div></div>
      </div>
      <?php else: ?>
      <p style="text-align:center;padding:2rem;color:var(--muted);">No school-age data recorded for <?= $reportYear ?>.</p>
      <?php endif; ?>
    </div>

    <!-- TAB 3: Health -->
    <div class="ntab-panel" id="tab-health">
      <div class="info-grid">
        <div class="info-card"><div class="info-value"><?= number_format($pregnant_women) ?></div><div class="stat-lbl">Pregnant Women</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($lactating_women) ?></div><div class="stat-lbl">Lactating Women</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($exclusive_bf) ?></div><div class="stat-lbl">Exclusive BF (0-5mo)</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($fully_immunized) ?></div><div class="stat-lbl">Fully Immunized</div></div>
      </div>
      <div class="info-grid">
        <div class="info-card"><div class="info-value"><?= number_format($hh_iodized_salt) ?></div><div class="stat-lbl">HH using Iodized Salt</div><div class="stat-lbl" style="font-size:10px;"><?= $total_households>0?round($hh_iodized_salt/$total_households*100,1):0?>% of HH</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($families_wasted) ?></div><div class="stat-lbl">HH with Wasted Children</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($families_stunted) ?></div><div class="stat-lbl">HH with Stunted Children</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($hh_with_severely_wasted_school) ?></div><div class="stat-lbl">HH w/ Severely Wasted School Child</div></div>
      </div>
    </div>

    <!-- TAB 4: WASH -->
    <div class="ntab-panel" id="tab-watsan">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div><p style="font-weight:600;margin-bottom:8px;"><i class="fa fa-toilet"></i> Toilet Facilities</p>
          <table style="width:100%;font-size:12px;"><?php $toilets=[['Water-sealed',$toilet_water_sealed],['Antipolo',$toilet_antipolo],['Open Pit',$toilet_open_pit],['Shared',$toilet_shared],['No Toilet',$toilet_none]]; foreach($toilets as $t):?><tr><td style="padding:4px 0;"><?=$t[0]?></td><td style="text-align:right;"><?=number_format($t[1])?></td><td style="text-align:right;color:var(--muted);">(<?=$total_households>0?round($t[1]/$total_households*100,1):0?>%)</td><?php endforeach;?><tr style="border-top:1px solid var(--border);"><td style="padding-top:6px;font-weight:600;">Sanitation Coverage</td><td style="text-align:right;font-weight:600;" colspan="2"><?=$sanitation_coverage?>%</td></tr></table></div>
        <div><p style="font-weight:600;margin-bottom:8px;"><i class="fa fa-water"></i> Water Sources</p>
          <table style="width:100%;font-size:12px;"><?php $waters=[['Piped Water',$water_piped],['Spring',$water_spring],['Deep Well (Communal)',$water_deep_well_communal],['Deep Well (Individual)',$water_deep_well_individual],['Purified Station',$water_purified],['Shallow Dug Well',$water_shallow_well],['Artesian Well',$water_artesian]]; foreach($waters as $w):?><tr><td style="padding:4px 0;"><?=$w[0]?></td><td style="text-align:right;"><?=number_format($w[1])?></td></tr><?php endforeach;?><tr style="border-top:1px solid var(--border);"><td style="padding-top:6px;font-weight:600;">Safe Water Coverage</td><td style="text-align:right;font-weight:600;"><?=$safe_water_coverage?>%</td></tr></table></div>
      </div>
      <div style="margin-top:1rem;"><p style="font-weight:600;margin-bottom:8px;"><i class="fa fa-trash"></i> Garbage Disposal</p><div class="info-grid"><?php $garbages=[['Collection',$garbage_collection],['Compost Pit',$garbage_compost],['Burning',$garbage_burning],['Dumping',$garbage_dumping]]; foreach($garbages as $g):?><div class="info-card"><div class="info-value"><?=number_format($g[1])?></div><div class="stat-lbl"><?=$g[0]?></div><div class="stat-lbl"><?=$total_households>0?round($g[1]/$total_households*100,1):0?>%</div></div><?php endforeach;?></div></div>
    </div>

    <!-- TAB 5: Livelihood -->
    <div class="ntab-panel" id="tab-livelihood">
      <div class="info-grid">
        <div class="info-card"><div class="info-value"><?= number_format($has_veg_garden) ?></div><div class="stat-lbl">With Vegetable Garden</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($has_livestock) ?></div><div class="stat-lbl">With Livestock/Poultry</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($has_fishpond) ?></div><div class="stat-lbl">With Fishpond</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($no_garden) ?></div><div class="stat-lbl">No Garden</div></div>
      </div>
      <div class="info-grid">
        <?php $dwellings=[['Concrete',$dwelling_concrete],['Semi-Concrete',$dwelling_semi_concrete],['Wooden',$dwelling_wood],['Nipa/Bamboo',$dwelling_nipa],['Barong-Barong',$dwelling_barong],['Makeshift',$dwelling_makeshift]]; foreach($dwellings as $d):?><div class="info-card"><div class="info-value"><?=number_format($d[1])?></div><div class="stat-lbl"><?=$d[0]?></div></div><?php endforeach;?>
      </div>
      <div class="info-grid">
        <div class="info-card"><div class="info-value"><?= number_format($eateries) ?></div><div class="stat-lbl">Eateries/Carenderia</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($stores_iodized_salt) ?></div><div class="stat-lbl">Sari-sari (Iodized Salt)</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($stores_cooking_oil) ?></div><div class="stat-lbl">Sari-sari (Cooking Oil)</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($bakeries_fortified) ?></div><div class="stat-lbl">Bakeries (Fortified Flour)</div></div>
      </div>
      <div style="margin-top:0.5rem;background:#f0fdf4;border-radius:14px;padding:1rem;display:flex;justify-content:space-between;align-items:center;"><div><i class="fa fa-users" style="color:#166534;"></i> <strong>4Ps Beneficiaries</strong></div><div style="font-size:28px;font-weight:700;color:#166534;"><?= number_format($hh_4ps_beneficiaries) ?></div></div>
      <div class="info-grid" style="margin-top:0.5rem;">
        <div class="info-card"><div class="info-value"><?= number_format($bns_workers) ?></div><div class="stat-lbl">BNS Workers</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($bhw_workers) ?></div><div class="stat-lbl">BHW Workers</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($daycare_public + $daycare_private) ?></div><div class="stat-lbl">Daycare Centers</div></div>
        <div class="info-card"><div class="info-value"><?= number_format($elem_public + $elem_private) ?></div><div class="stat-lbl">Elementary Schools</div></div>
      </div>
    </div>
  </div>

  <?php else: ?>
  <div class="card fu fu4" style="padding:2.5rem;text-align:center;">
    <i class="fa fa-chart-bar" style="font-size:36px;color:var(--border);"></i>
    <p style="font-size:14px;color:var(--muted);margin-top:10px;">No approved report data yet.</p>
    <p style="font-size:12px;color:var(--muted);">Submit and get a report approved to see your barangay snapshot.</p>
    <?php if(count($availableReports) > 0 && !$n): ?>
    <p style="font-size:12px;color:var(--teal-500);margin-top:8px;">Try selecting a different report from the dropdown above.</p>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <!-- Reports table -->
  <div class="card fu fu5">
    <div class="card-header"><span class="card-title"><i class="fa fa-table"></i>Pending &amp; Rejected Reports</span><div><div class="search-wrap"><i class="fa fa-search"></i><input id="tableSearch" type="text" placeholder="Search…" class="search-inp"></div></div></div>
    <div style="overflow-x:auto;"><table id="reportsTable" class="dt w-full"><thead><th>User</th><th>Report Title</th><th>Status</th><th>Date</th><th>Time</th><th>Action</th></thead><tbody><?php if($myReports): foreach($myReports as $r): $pic=(!empty($r['profile_pic'])&&file_exists("../uploads/".$r['profile_pic']))?"../uploads/".htmlspecialchars($r['profile_pic']):"../uploads/default.png";?><tr><td><div style="display:flex;align-items:center;gap:8px;"><img src="<?=$pic?>" style="width:28px;height:28px;border-radius:50%;object-fit:cover;"><span><?=htmlspecialchars($r['username'])?></span></div></td><td><?=htmlspecialchars($r['title'])?></td><td><span class="badge b-<?=strtolower($r['status'])?>"><?=$r['status']?></span></td><td><?=htmlspecialchars($r['report_date'])?></td><td><?=htmlspecialchars(substr($r['report_time'],0,5))?></td><td><a href="view_report.php?id=<?=$r['id']?>" class="btn-view"><i class="fa fa-eye"></i> View</a></td></tr><?php endforeach; else: ?><td><td colspan="6" style="text-align:center;padding:2rem;"><i class="fa fa-check-circle" style="font-size:28px;color:#86efac;"></i><p>No pending or rejected reports.</p><a href="add_report.php" style="color:var(--teal-500);">+ Submit a new report →</a></td></tr><?php endif; ?></tbody></table></div>
    <?php if($totalPages>1): ?><div style="display:flex;justify-content:center;gap:5px;padding:12px;"><?php for($i=1;$i<=$totalPages;$i++):?><a href="?page=<?=$i?>&report_id=<?=$selectedReportId?>" class="pg-btn <?=$i==$page?'active':''?>"><?=$i?></a><?php endfor;?></div><?php endif; ?>
  </div>

</main>
</div>
</div>

<script>
function updateClock(){ document.getElementById('livetime').textContent=new Date().toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit',second:'2-digit'}); }
updateClock(); setInterval(updateClock,1000);
document.querySelectorAll('.ntab-btn').forEach(btn=>{ btn.addEventListener('click',()=>{ document.querySelectorAll('.ntab-btn').forEach(b=>b.classList.remove('active')); document.querySelectorAll('.ntab-panel').forEach(p=>p.classList.remove('active')); btn.classList.add('active'); document.getElementById('tab-'+btn.dataset.tab).classList.add('active'); }); });
document.getElementById('tableSearch').addEventListener('keyup',function(){ const kw=this.value.toLowerCase(); document.querySelectorAll('#reportsTable tbody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(kw)?'':''); });

<?php if($n): ?>
new Chart(document.getElementById('donutChart'),{type:'doughnut',data:{labels:['Normal','Underweight','Severely Underweight','Wasted','Severely Wasted','Overweight','Obese'],datasets:[{data:[<?=$normal_weight?>,<?=$underweight?>,<?=$severely_underweight?>,<?=$wasted?>,<?=$severely_wasted?>,<?=$overweight?>,<?=$obese?>],backgroundColor:['#22c55e','#f59e0b','#ef4444','#f97316','#dc2626','#a78bfa','#8b5cf6'],borderWidth:0,hoverOffset:6}]},options:{responsive:true,maintainAspectRatio:false,cutout:'65%',plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>` ${ctx.label}: ${ctx.parsed.toLocaleString()} (${Math.round(ctx.parsed/<?=max(1,$actual_measured)?>*100)}%)`}}}}});
<?php if($sa_total>0): ?>
new Chart(document.getElementById('saDonut'),{type:'doughnut',data:{labels:['Normal','Wasted','Severely Wasted','Stunted','Severely Stunted','Overweight','Obese'],datasets:[{data:[<?=$sa_normal?>,<?=$sa_wasted?>,<?=$sa_severely_wasted?>,<?=$sa_stunted?>,<?=$sa_severely_stunted?>,<?=$sa_overweight?>,<?=$sa_obese?>],backgroundColor:['#22c55e','#f59e0b','#ef4444','#f97316','#dc2626','#a78bfa','#8b5cf6'],borderWidth:0,hoverOffset:6}]},options:{responsive:true,maintainAspectRatio:false,cutout:'60%',plugins:{legend:{display:false}}}});
<?php endif; ?>
<?php endif; ?>
</script>
</body>
</html>