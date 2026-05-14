<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}

$yearsStmt = $pdo->query("SELECT DISTINCT CAST(`year` AS UNSIGNED) AS yr FROM bns_reports ORDER BY yr DESC");
$yearOptions = $yearsStmt->fetchAll(PDO::FETCH_COLUMN, 0);
if (empty($yearOptions)) $yearOptions = [date('Y')];

$selectedYear    = isset($_GET['year'])    ? (int)$_GET['year']    : date('Y');
$selectedQuarter = isset($_GET['quarter']) ? (int)$_GET['quarter'] : (int)ceil(date('n') / 3);
if ($selectedQuarter < 1 || $selectedQuarter > 4) $selectedQuarter = (int)ceil(date('n') / 3);

$qStartMonth = ($selectedQuarter - 1) * 3 + 1;
$qEndMonth   = $qStartMonth + 2;

$excludeArchived = "NOT EXISTS (
    SELECT 1 FROM report_archives a
    WHERE a.report_id = r.id
      AND (a.is_archived = 1 OR a.is_deleted = 1)
)";

function q($pdo, $sql, $params = []) {
    $s = $pdo->prepare($sql);
    $s->execute($params);
    return $s;
}

// ── Users ────────────────────────────────────────────────────────────────────
$totalUsers  = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalAdmins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE user_type='CNO'")->fetchColumn();
$totalBNS    = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE user_type='BNS'")->fetchColumn();
$totalBarangays = (int)$pdo->query("SELECT COUNT(DISTINCT barangay) FROM users WHERE barangay NOT IN ('CNO') AND barangay != ''")->fetchColumn();

// ── Report counts (year) ─────────────────────────────────────────────────────
$totalReports = (int)q($pdo, "
    SELECT COUNT(*) FROM reports r
    JOIN bns_reports b ON r.id = b.report_id
    WHERE r.is_submitted=1 AND b.year=:y AND $excludeArchived
", [':y' => $selectedYear])->fetchColumn();

foreach (['Approved','Pending','Rejected'] as $st) {
    $reportCounts[$st] = (int)q($pdo, "
        SELECT COUNT(*) FROM reports r
        JOIN bns_reports b ON r.id = b.report_id
        WHERE r.status=:s AND r.is_submitted=1 AND b.year=:y AND $excludeArchived
    ", [':s' => $st, ':y' => $selectedYear])->fetchColumn();
}
$approvedReports = $reportCounts['Approved'];
$pendingReports  = $reportCounts['Pending'];
$rejectedReports = $reportCounts['Rejected'];
$approvalRate    = $totalReports > 0 ? round($approvedReports / $totalReports * 100) : 0;

// ── Population aggregates (SUM across all bns_reports for year) ──────────────
$popRow = q($pdo, "
    SELECT
      SUM(ind1)        AS pop_total,
      SUM(ind_male)    AS pop_male,
      SUM(ind_female)  AS pop_female,
      SUM(ind2)        AS households,
      SUM(ind3)        AS families,
      SUM(ind6a)       AS pregnant,
      SUM(ind6b)       AS lactating,
      SUM(ind8)        AS preschool_est,
      SUM(ind9)        AS preschool_measured,
      SUM(ind10)       AS infants_0_5,
      SUM(ind11)       AS infants_6_11,
      SUM(ind19)       AS school_children,
      SUM(ind20)       AS school_weighed,
      SUM(ind23)       AS breastfed,
      SUM(ind25)       AS dewormed,
      SUM(ind26)       AS fic,
      SUM(ind32)       AS iodized_salt_hh,
      SUM(ind38)       AS pantawid_hh,
      SUM(ind33)       AS eateries,
      SUM(ind37a)      AS bns_workers,
      SUM(ind37b)      AS bhw_workers
    FROM bns_reports b
    JOIN reports r ON r.id = b.report_id
    WHERE b.year=:y AND r.is_submitted=1 AND $excludeArchived
", [':y' => $selectedYear])->fetch(PDO::FETCH_ASSOC);

$popTotal         = (int)($popRow['pop_total'] ?? 0);
$popMale          = (int)($popRow['pop_male'] ?? 0);
$popFemale        = (int)($popRow['pop_female'] ?? 0);
$totalHH          = (int)($popRow['households'] ?? 0);
$totalFamilies    = (int)($popRow['families'] ?? 0);
$pregnant         = (int)($popRow['pregnant'] ?? 0);
$lactating        = (int)($popRow['lactating'] ?? 0);
$preschoolEst     = (int)($popRow['preschool_est'] ?? 0);
$preschoolMeasured= (int)($popRow['preschool_measured'] ?? 0);
$optCoverage      = $preschoolEst > 0 ? round($preschoolMeasured / $preschoolEst * 100) : 0;
$schoolChildren   = (int)($popRow['school_children'] ?? 0);
$schoolWeighed    = (int)($popRow['school_weighed'] ?? 0);
$schoolCoverage   = $schoolChildren > 0 ? round($schoolWeighed / $schoolChildren * 100) : 0;
$breastfed        = (int)($popRow['breastfed'] ?? 0);
$breastfedPct     = ($popRow['infants_0_5'] ?? 0) > 0 ? round($breastfed / $popRow['infants_0_5'] * 100) : 0;
$dewormed         = (int)($popRow['dewormed'] ?? 0);
$fic              = (int)($popRow['fic'] ?? 0);
$ficPct           = $schoolChildren > 0 ? round($fic / $schoolChildren * 100) : 0;
$iodizedSaltHH    = (int)($popRow['iodized_salt_hh'] ?? 0);
$iodizedPct       = $totalHH > 0 ? round($iodizedSaltHH / $totalHH * 100) : 0;
$pantawidHH       = (int)($popRow['pantawid_hh'] ?? 0);
$pantawidPct      = $totalHH > 0 ? round($pantawidHH / $totalHH * 100) : 0;

// ── Preschool nutritional status (ind9b1–ind9b9) ─────────────────────────────
$nutRow = q($pdo, "
    SELECT
      SUM(ind9b1_no) AS sev_uw,   SUM(ind9b2_no) AS uw,
      SUM(ind9b3_no) AS normal,   SUM(ind9b4_no) AS sev_wasted,
      SUM(ind9b5_no) AS wasted,   SUM(ind9b6_no) AS overweight,
      SUM(ind9b7_no) AS obese,    SUM(ind9b8_no) AS sev_stunted,
      SUM(ind9b9_no) AS stunted
    FROM bns_reports b
    JOIN reports r ON r.id = b.report_id
    WHERE b.year=:y AND r.is_submitted=1 AND $excludeArchived
", [':y' => $selectedYear])->fetch(PDO::FETCH_ASSOC);

$nutLabels = ['Sev. Underweight','Underweight','Normal','Sev. Wasted','Wasted','Overweight','Obese','Sev. Stunted','Stunted'];
$nutData   = [
    (int)($nutRow['sev_uw'] ?? 0),    (int)($nutRow['uw'] ?? 0),
    (int)($nutRow['normal'] ?? 0),    (int)($nutRow['sev_wasted'] ?? 0),
    (int)($nutRow['wasted'] ?? 0),    (int)($nutRow['overweight'] ?? 0),
    (int)($nutRow['obese'] ?? 0),     (int)($nutRow['sev_stunted'] ?? 0),
    (int)($nutRow['stunted'] ?? 0)
];
$sevWastedTotal  = (int)($nutRow['sev_wasted'] ?? 0) + (int)($nutRow['wasted'] ?? 0);
$sevStuntedTotal = (int)($nutRow['sev_stunted'] ?? 0) + (int)($nutRow['stunted'] ?? 0);

// ── School BMI (ind22a–ind22g) ───────────────────────────────────────────────
$bmiRow = q($pdo, "
    SELECT
      SUM(ind22a_no) AS sev_wasted, SUM(ind22b_no) AS wasted,
      SUM(ind22c_no) AS sev_stunted, SUM(ind22d_no) AS stunted,
      SUM(ind22e_no) AS normal,     SUM(ind22f_no) AS overweight,
      SUM(ind22g_no) AS obese
    FROM bns_reports b
    JOIN reports r ON r.id = b.report_id
    WHERE b.year=:y AND r.is_submitted=1 AND $excludeArchived
", [':y' => $selectedYear])->fetch(PDO::FETCH_ASSOC);

$bmiLabels = ['Sev. Wasted','Wasted','Sev. Stunted','Stunted','Normal','Overweight','Obese'];
$bmiData   = [
    (int)($bmiRow['sev_wasted'] ?? 0),  (int)($bmiRow['wasted'] ?? 0),
    (int)($bmiRow['sev_stunted'] ?? 0), (int)($bmiRow['stunted'] ?? 0),
    (int)($bmiRow['normal'] ?? 0),      (int)($bmiRow['overweight'] ?? 0),
    (int)($bmiRow['obese'] ?? 0)
];

// ── Toilet facility (ind27) ──────────────────────────────────────────────────
$toiletRow = q($pdo, "
    SELECT SUM(ind27a_no) AS water_sealed, SUM(ind27b_no) AS antipolo,
           SUM(ind27c_no) AS open_pit,     SUM(ind27d_no) AS shared,
           SUM(ind27e_no) AS no_toilet
    FROM bns_reports b JOIN reports r ON r.id=b.report_id
    WHERE b.year=:y AND r.is_submitted=1 AND $excludeArchived
", [':y' => $selectedYear])->fetch(PDO::FETCH_ASSOC);
$toiletTotal = array_sum(array_map('intval', $toiletRow ?: [])) ?: 1;
$toiletPcts  = [
    'Water-sealed' => round(($toiletRow['water_sealed'] ?? 0) / $toiletTotal * 100),
    'Shared'       => round(($toiletRow['shared'] ?? 0)       / $toiletTotal * 100),
    'Open pit'     => round(($toiletRow['open_pit'] ?? 0)     / $toiletTotal * 100),
    'Antipolo'     => round(($toiletRow['antipolo'] ?? 0)     / $toiletTotal * 100),
    'No toilet'    => round(($toiletRow['no_toilet'] ?? 0)    / $toiletTotal * 100),
];

// ── Garbage disposal (ind28) ─────────────────────────────────────────────────
$garbRow = q($pdo, "
    SELECT SUM(ind28a_no) AS collection, SUM(ind28b_no) AS compost,
           SUM(ind28c_no) AS burning,    SUM(ind28d_no) AS dumping
    FROM bns_reports b JOIN reports r ON r.id=b.report_id
    WHERE b.year=:y AND r.is_submitted=1 AND $excludeArchived
", [':y' => $selectedYear])->fetch(PDO::FETCH_ASSOC);
$garbTotal = array_sum(array_map('intval', $garbRow ?: [])) ?: 1;
$garbPcts  = [
    'Collection'  => round(($garbRow['collection'] ?? 0) / $garbTotal * 100),
    'Compost pit' => round(($garbRow['compost'] ?? 0)    / $garbTotal * 100),
    'Burning'     => round(($garbRow['burning'] ?? 0)    / $garbTotal * 100),
    'Dumping'     => round(($garbRow['dumping'] ?? 0)    / $garbTotal * 100),
];

// ── Water source (ind29) ─────────────────────────────────────────────────────
$waterRow = q($pdo, "
    SELECT SUM(ind29a_no) AS pipe,        SUM(ind29b_no) AS spring,
           SUM(ind29c_no) AS deep_comm,   SUM(ind29d_no) AS deep_ind,
           SUM(ind29e_no) AS purified,    SUM(ind29f_no) AS open_well,
           SUM(ind29g_no) AS artesian
    FROM bns_reports b JOIN reports r ON r.id=b.report_id
    WHERE b.year=:y AND r.is_submitted=1 AND $excludeArchived
", [':y' => $selectedYear])->fetch(PDO::FETCH_ASSOC);
$waterTotal = array_sum(array_map('intval', $waterRow ?: [])) ?: 1;
$level3 = round((($waterRow['pipe'] ?? 0) + ($waterRow['deep_ind'] ?? 0) + ($waterRow['purified'] ?? 0)) / $waterTotal * 100);
$level2 = round((($waterRow['spring'] ?? 0) + ($waterRow['deep_comm'] ?? 0)) / $waterTotal * 100);
$level1 = round((($waterRow['open_well'] ?? 0) + ($waterRow['artesian'] ?? 0)) / $waterTotal * 100);

// ── Dwelling (ind31) ─────────────────────────────────────────────────────────
$dwellRow = q($pdo, "
    SELECT SUM(ind31a_no) AS concrete,   SUM(ind31b_no) AS semi,
           SUM(ind31c_no) AS wooden,     SUM(ind31d_no) AS nipa,
           SUM(ind31e_no) AS makeshift1, SUM(ind31f_no) AS makeshift2
    FROM bns_reports b JOIN reports r ON r.id=b.report_id
    WHERE b.year=:y AND r.is_submitted=1 AND $excludeArchived
", [':y' => $selectedYear])->fetch(PDO::FETCH_ASSOC);
$dwellTotal = array_sum(array_map('intval', $dwellRow ?: [])) ?: 1;
$dwellPcts  = [
    'Concrete'     => round(($dwellRow['concrete']   ?? 0) / $dwellTotal * 100),
    'Semi-concrete'=> round(($dwellRow['semi']        ?? 0) / $dwellTotal * 100),
    'Wooden'       => round(($dwellRow['wooden']      ?? 0) / $dwellTotal * 100),
    'Nipa/bamboo'  => round(($dwellRow['nipa']        ?? 0) / $dwellTotal * 100),
    'Makeshift'    => round((($dwellRow['makeshift1'] ?? 0) + ($dwellRow['makeshift2'] ?? 0)) / $dwellTotal * 100),
];

// ── Food production (ind30) ──────────────────────────────────────────────────
$foodRow = q($pdo, "
    SELECT SUM(ind30a_no) AS vegetable, SUM(ind30b_no) AS livestock,
           SUM(ind30c_no) AS fishpond,  SUM(ind30d_no) AS no_garden
    FROM bns_reports b JOIN reports r ON r.id=b.report_id
    WHERE b.year=:y AND r.is_submitted=1 AND $excludeArchived
", [':y' => $selectedYear])->fetch(PDO::FETCH_ASSOC);
$foodTotal = array_sum(array_map('intval', $foodRow ?: [])) ?: 1;
$foodPcts  = [
    'Vegetable'  => round(($foodRow['vegetable']  ?? 0) / $foodTotal * 100),
    'Livestock'  => round(($foodRow['livestock']  ?? 0) / $foodTotal * 100),
    'Fishponds'  => round(($foodRow['fishpond']   ?? 0) / $foodTotal * 100),
    'No garden'  => round(($foodRow['no_garden']  ?? 0) / $foodTotal * 100),
];

// ── Monthly trend (quarter) ──────────────────────────────────────────────────
$monthLabels = $monthApproved = $monthPending = $monthRejected = [];
for ($m = $qStartMonth; $m <= $qEndMonth; $m++) {
    $monthLabels[] = date('M', mktime(0,0,0,$m,1,$selectedYear));
    foreach (['Approved' => &$monthApproved, 'Pending' => &$monthPending, 'Rejected' => &$monthRejected] as $st => &$arr) {
        $arr[] = (int)q($pdo, "
            SELECT COUNT(*) FROM reports r
            JOIN bns_reports b ON r.id=b.report_id
            WHERE r.status=:s AND r.is_submitted=1
              AND MONTH(r.report_date)=:m AND YEAR(r.report_date)=:y
              AND $excludeArchived
        ", [':s' => $st, ':m' => $m, ':y' => $selectedYear])->fetchColumn();
    }
    unset($arr);
}

// ── Top barangays ────────────────────────────────────────────────────────────
$topBrgyRows = q($pdo, "
    SELECT u.barangay, COUNT(*) AS total
    FROM reports r
    JOIN users u ON r.user_id=u.id
    JOIN bns_reports b ON r.id=b.report_id
    WHERE r.is_submitted=1 AND u.barangay!='' AND b.year=:y AND $excludeArchived
    GROUP BY u.barangay ORDER BY total DESC LIMIT 5
", [':y' => $selectedYear])->fetchAll(PDO::FETCH_ASSOC);

// ── Pending/Rejected table ───────────────────────────────────────────────────
$limit  = 8;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$totalRows = (int)q($pdo, "
    SELECT COUNT(*) FROM reports r
    JOIN bns_reports b ON r.id=b.report_id
    WHERE r.status IN ('Pending','Rejected') AND r.is_submitted=1
      AND b.year=:y AND $excludeArchived
", [':y' => $selectedYear])->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $limit));

$tableStmt = $pdo->prepare("
    SELECT r.id, u.profile_pic, u.username AS uname, u.barangay,
           b.title, r.status, r.report_date
    FROM reports r
    JOIN users u ON r.user_id=u.id
    JOIN bns_reports b ON r.id=b.report_id
    WHERE r.status IN ('Pending','Rejected') AND r.is_submitted=1
      AND b.year=:y AND $excludeArchived
    ORDER BY r.report_date DESC
    LIMIT :lim OFFSET :off
");
$tableStmt->bindValue(':y',   $selectedYear,  PDO::PARAM_INT);
$tableStmt->bindValue(':lim', $limit,         PDO::PARAM_INT);
$tableStmt->bindValue(':off', $offset,        PDO::PARAM_INT);
$tableStmt->execute();
$tableRows = $tableStmt->fetchAll(PDO::FETCH_ASSOC);

function bq($overrides = []) {
    return http_build_query(array_merge($_GET, $overrides));
}

function hbar(string $label, int $pct, string $color): string {
    return '<div class="hbar-row">
        <span class="hbar-lbl">'.htmlspecialchars($label).'</span>
        <div class="hbar-track"><div class="hbar-fill" style="width:'.min($pct,100).'%;background:'.$color.'"></div></div>
        <span class="hbar-val">'.$pct.'%</span>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CNO | Dashboard</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  :root {
    --teal:   #1D9E75; --teal-lt: #E1F5EE; --teal-dk: #0F6E56;
    --amber:  #EF9F27; --amber-lt:#FAEEDA; --amber-dk:#633806;
    --red:    #E24B4A; --red-lt:  #FCEBEB; --red-dk:  #791F1F;
    --blue:   #378ADD; --blue-lt: #E6F1FB; --blue-dk: #0C447C;
    --purple: #7F77DD; --purple-lt:#EEEDFE;--purple-dk:#3C3489;
    --green:  #639922; --green-lt: #EAF3DE;--green-dk: #27500A;
    --gray-border: rgba(0,0,0,.1);
  }
  * { box-sizing: border-box; }
  body { background: #f3f4f6; font-family: 'Segoe UI', system-ui, sans-serif; color: #1a1a1a; }

  /* ── layout ── */
  .dash        { padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; }
  .grid-2      { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .grid-3      { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
  .grid-32     { display: grid; grid-template-columns: 3fr 2fr; gap: 10px; }
  @media(max-width:900px){ .grid-3,.grid-32,.grid-2 { grid-template-columns:1fr; } }

  /* ── card ── */
  .card { background:#fff; border:0.5px solid var(--gray-border); border-radius:12px; padding:14px 16px; }
  .card-title { font-size:12px; font-weight:600; color:#6b7280; margin-bottom:10px; display:flex; align-items:center; justify-content:space-between; }
  .card-title i { font-size:14px; }

  /* ── kpi grid ── */
  .kpi-row { display: grid; grid-template-columns: repeat(auto-fit,minmax(130px,1fr)); gap: 8px; }
  .kpi     { background:#fff; border:0.5px solid var(--gray-border); border-radius:12px; padding:12px 14px; position:relative; overflow:hidden; }
  .kpi::before { content:''; position:absolute; left:0; top:0; bottom:0; width:3px; border-radius:3px 0 0 3px; }
  .kpi.teal::before   { background: var(--teal); }
  .kpi.amber::before  { background: var(--amber); }
  .kpi.red::before    { background: var(--red); }
  .kpi.blue::before   { background: var(--blue); }
  .kpi.purple::before { background: var(--purple); }
  .kpi.green::before  { background: var(--green); }
  .kpi-lbl  { font-size:11px; color:#6b7280; margin-bottom:6px; display:flex; align-items:center; gap:5px; }
  .kpi-lbl i{ font-size:13px; }
  .kpi-val  { font-size:22px; font-weight:700; line-height:1; color:#111; }
  .kpi-sub  { font-size:11px; color:#9ca3af; margin-top:5px; }
  .badge    { display:inline-block; font-size:10px; padding:2px 7px; border-radius:20px; margin-top:5px; font-weight:500; }
  .b-warn   { background:var(--amber-lt); color:var(--amber-dk); }
  .b-ok     { background:var(--teal-lt);  color:var(--teal-dk); }
  .b-danger { background:var(--red-lt);   color:var(--red-dk); }
  .b-info   { background:var(--blue-lt);  color:var(--blue-dk); }
  .b-purple { background:var(--purple-lt);color:var(--purple-dk); }

  /* ── section label ── */
  .sec-lbl { font-size:11px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px; display:flex; align-items:center; gap:6px; }

  /* ── chart canvas wrappers ── */
  .ch { position:relative; width:100%; }
  .h160 { height:160px; } .h180 { height:180px; }
  .h200 { height:200px; } .h220 { height:220px; }

  /* ── progress bars ── */
  .prog-row { margin-bottom:9px; }
  .prog-row:last-child { margin-bottom:0; }
  .prog-top  { display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px; }
  .prog-lbl  { color:#6b7280; }
  .prog-pct  { font-weight:600; }
  .prog-track{ height:7px; background:#f3f4f6; border-radius:4px; overflow:hidden; }
  .prog-fill { height:100%; border-radius:4px; }

  /* ── horizontal bars ── */
  .hbar-row  { display:flex; align-items:center; gap:8px; margin-bottom:7px; }
  .hbar-row:last-child { margin-bottom:0; }
  .hbar-lbl  { font-size:11px; color:#6b7280; width:92px; flex-shrink:0; text-align:right; }
  .hbar-track{ flex:1; height:7px; background:#f3f4f6; border-radius:4px; overflow:hidden; }
  .hbar-fill { height:100%; border-radius:4px; }
  .hbar-val  { font-size:11px; color:#6b7280; width:30px; text-align:right; flex-shrink:0; }

  /* ── donut legend ── */
  .dnut-wrap   { display:flex; align-items:center; gap:14px; }
  .dnut-canvas { position:relative; flex-shrink:0; }
  .dnut-leg    { flex:1; display:flex; flex-direction:column; gap:7px; }
  .dleg-row    { display:flex; align-items:center; justify-content:space-between; font-size:12px; }
  .dleg-left   { display:flex; align-items:center; gap:6px; }
  .dleg-dot    { width:8px; height:8px; border-radius:2px; flex-shrink:0; }
  .dleg-lbl    { color:#6b7280; }
  .dleg-val    { font-weight:600; }

  /* ── ranking ── */
  .rank-row { display:flex; align-items:center; padding:6px 0; border-bottom:0.5px solid #f3f4f6; font-size:13px; }
  .rank-row:last-child { border-bottom:none; }
  .rank-num  { font-size:10px; color:#d1d5db; width:18px; flex-shrink:0; }
  .rank-name { flex:1; padding:0 8px; }
  .rank-bar  { width:60px; height:5px; background:#f3f4f6; border-radius:3px; overflow:hidden; margin-right:8px; }
  .rank-bfill{ height:100%; border-radius:3px; background:var(--teal); }
  .rank-val  { font-weight:600; min-width:20px; text-align:right; }

  /* ── table ── */
  .tbl-card { background:#fff; border:0.5px solid var(--gray-border); border-radius:12px; overflow:hidden; }
  .tbl-top  { display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border-bottom:0.5px solid #f3f4f6; gap:10px; flex-wrap:wrap; }
  .tbl-title{ display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; color:#111; }
  .srch     { display:flex; align-items:center; gap:6px; background:#f9fafb; border:0.5px solid #e5e7eb; border-radius:8px; padding:0 8px; }
  .srch i   { font-size:13px; color:#9ca3af; }
  .srch input{ border:none; background:transparent; font-size:12px; color:#111; outline:none; padding:5px 0; width:150px; }
  .view-all { font-size:11px; color:#185FA5; text-decoration:none; white-space:nowrap; }
  .view-all:hover { text-decoration:underline; }
  table.main{ width:100%; border-collapse:collapse; table-layout:fixed; }
  table.main th { font-size:10px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; padding:7px 12px; text-align:left; background:#f9fafb; border-bottom:0.5px solid #f3f4f6; }
  table.main td { padding:9px 12px; font-size:12px; border-bottom:0.5px solid #f9fafb; vertical-align:middle; }
  table.main tbody tr:last-child td { border-bottom:none; }
  table.main tbody tr:hover td { background:#fafafa; }
  .av   { width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; color:var(--teal-dk); background:var(--teal-lt); flex-shrink:0; }
  .sp   { display:inline-flex; align-items:center; gap:3px; font-size:10px; padding:2px 7px; border-radius:20px; font-weight:500; }
  .sp-p { background:var(--amber-lt); color:var(--amber-dk); }
  .sp-r { background:var(--red-lt);   color:var(--red-dk); }
  .sp-a { background:var(--teal-lt);  color:var(--teal-dk); }
  .sp-dot{ width:4px; height:4px; border-radius:50%; background:currentColor; }
  .act-btn{ font-size:11px; padding:3px 10px; border:0.5px solid #e5e7eb; border-radius:6px; color:#374151; background:transparent; text-decoration:none; cursor:pointer; }
  .act-btn:hover { background:#f9fafb; }
  .pg   { display:flex; align-items:center; justify-content:space-between; padding:9px 14px; border-top:0.5px solid #f3f4f6; }
  .pg-info{ font-size:11px; color:#9ca3af; }
  .pg-btns{ display:flex; gap:3px; }
  .pg-btn { font-size:11px; padding:3px 10px; border:0.5px solid #e5e7eb; border-radius:6px; background:transparent; cursor:pointer; color:#374151; }
  .pg-btn.active { background:var(--teal); color:#fff; border-color:var(--teal); }
  .pg-btn:disabled{ color:#d1d5db; cursor:not-allowed; }
  hr.div { border:none; border-top:0.5px solid #f3f4f6; margin:12px 0; }
</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="dash">

  <!-- ── Top bar ──────────────────────────────────────────────────────────── -->
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-800">CNO Dashboard</h1>
      <p class="text-xs text-gray-400 mt-0.5">Barangay Nutrition Scholar — consolidated overview &nbsp;·&nbsp; <?= $selectedYear ?> Q<?= $selectedQuarter ?></p>
    </div>
    <form method="get" id="filterForm" class="flex flex-wrap items-center gap-2">
      <label class="text-xs text-gray-500">Year</label>
      <select name="year" onchange="this.form.submit()" class="text-xs px-2 py-1.5 border border-gray-200 rounded-lg bg-white">
        <?php foreach ($yearOptions as $y): ?>
          <option value="<?= $y ?>" <?= ((int)$y === $selectedYear) ? 'selected' : '' ?>><?= $y ?></option>
        <?php endforeach; ?>
      </select>
      <label class="text-xs text-gray-500">Quarter</label>
      <select name="quarter" onchange="this.form.submit()" class="text-xs px-2 py-1.5 border border-gray-200 rounded-lg bg-white">
        <?php for ($q=1;$q<=4;$q++): ?>
          <option value="<?= $q ?>" <?= $q===$selectedQuarter?'selected':'' ?>>Q<?= $q ?></option>
        <?php endfor; ?>
      </select>
    </form>
  </div>

  <!-- ── Report KPIs ──────────────────────────────────────────────────────── -->
  <div>
    <div class="sec-lbl"><i class="fa fa-file-alt"></i> Report overview</div>
    <div class="kpi-row">
      <div class="kpi teal">
        <div class="kpi-lbl"><i class="fa fa-file-check"></i>Total submitted</div>
        <div class="kpi-val"><?= number_format($totalReports) ?></div>
        <div class="kpi-sub"><?= $selectedYear ?> · all barangays</div>
      </div>
      <div class="kpi teal">
        <div class="kpi-lbl"><i class="fa fa-check-circle"></i>Approved</div>
        <div class="kpi-val"><?= number_format($approvedReports) ?></div>
        <span class="badge b-ok"><?= $approvalRate ?>% approval rate</span>
      </div>
      <div class="kpi amber">
        <div class="kpi-lbl"><i class="fa fa-clock"></i>Pending</div>
        <div class="kpi-val"><?= number_format($pendingReports) ?></div>
        <span class="badge b-warn"><?= $pendingReports > 0 ? 'Needs action' : 'All clear' ?></span>
      </div>
      <div class="kpi red">
        <div class="kpi-lbl"><i class="fa fa-times-circle"></i>Rejected</div>
        <div class="kpi-val"><?= number_format($rejectedReports) ?></div>
        <span class="badge b-danger"><?= $totalReports > 0 ? round($rejectedReports/$totalReports*100) : 0 ?>% of total</span>
      </div>
      <div class="kpi blue">
        <div class="kpi-lbl"><i class="fa fa-users"></i>BNS users</div>
        <div class="kpi-val"><?= $totalBNS ?></div>
        <div class="kpi-sub">CNO admins: <?= $totalAdmins ?></div>
      </div>
      <div class="kpi purple">
        <div class="kpi-lbl"><i class="fa fa-map-marker-alt"></i>Barangays</div>
        <div class="kpi-val"><?= $totalBarangays ?></div>
        <span class="badge b-purple">Active</span>
      </div>
    </div>
  </div>

  <!-- ── Population KPIs ──────────────────────────────────────────────────── -->
  <div>
    <div class="sec-lbl"><i class="fa fa-users"></i> Population snapshot (ind1–ind16)</div>
    <div class="kpi-row">
      <div class="kpi teal">
        <div class="kpi-lbl"><i class="fa fa-user"></i>Total population</div>
        <div class="kpi-val"><?= number_format($popTotal) ?></div>
        <div class="kpi-sub">M: <?= number_format($popMale) ?> · F: <?= number_format($popFemale) ?></div>
      </div>
      <div class="kpi blue">
        <div class="kpi-lbl"><i class="fa fa-home"></i>Households</div>
        <div class="kpi-val"><?= number_format($totalHH) ?></div>
        <div class="kpi-sub">Families: <?= number_format($totalFamilies) ?></div>
      </div>
      <div class="kpi purple">
        <div class="kpi-lbl"><i class="fa fa-baby"></i>Preschool children</div>
        <div class="kpi-val"><?= number_format($preschoolEst) ?></div>
        <div class="kpi-sub">0–59 months (est.)</div>
      </div>
      <div class="kpi teal">
        <div class="kpi-lbl"><i class="fa fa-heart"></i>Pregnant / lactating</div>
        <div class="kpi-val"><?= number_format($pregnant) ?></div>
        <div class="kpi-sub">Lactating: <?= number_format($lactating) ?></div>
      </div>
      <div class="kpi red">
        <div class="kpi-lbl"><i class="fa fa-exclamation-triangle"></i>Wasted children</div>
        <div class="kpi-val"><?= number_format($sevWastedTotal) ?></div>
        <span class="badge b-danger">Needs intervention</span>
      </div>
      <div class="kpi amber">
        <div class="kpi-lbl"><i class="fa fa-chart-line"></i>Stunted children</div>
        <div class="kpi-val"><?= number_format($sevStuntedTotal) ?></div>
        <span class="badge b-warn">Monitor closely</span>
      </div>
    </div>
  </div>

  <!-- ── Trend + Coverage + Top barangays ─────────────────────────────────── -->
  <div class="grid-3">

    <div class="card">
      <div class="card-title">
        <span>Monthly trend — Q<?= $selectedQuarter ?> <?= $selectedYear ?></span>
        <i class="fa fa-chart-bar text-gray-300"></i>
      </div>
      <div style="display:flex;gap:10px;margin-bottom:8px;flex-wrap:wrap">
        <?php foreach(['Approved'=>'#1D9E75','Pending'=>'#EF9F27','Rejected'=>'#E24B4A'] as $lbl=>$clr): ?>
        <span style="display:flex;align-items:center;gap:4px;font-size:11px;color:#6b7280">
          <span style="width:8px;height:8px;border-radius:2px;background:<?= $clr ?>;display:inline-block"></span><?= $lbl ?>
        </span>
        <?php endforeach; ?>
      </div>
      <div class="ch h180"><canvas id="trendChart" role="img" aria-label="Stacked bar chart of monthly submissions Q<?= $selectedQuarter ?> <?= $selectedYear ?>.">Monthly trend.</canvas></div>
    </div>

    <div class="card">
      <div class="card-title"><span>OPT Plus coverage (ind9a) &amp; health indicators</span></div>
      <div class="dnut-wrap">
        <div class="dnut-canvas" style="width:100px;height:100px">
          <canvas id="optChart" role="img" aria-label="Donut chart of OPT Plus coverage <?= $optCoverage ?>%.">OPT coverage <?= $optCoverage ?>%.</canvas>
        </div>
        <div class="dnut-leg">
          <div class="dleg-row"><div class="dleg-left"><span class="dleg-dot" style="background:#1D9E75"></span><span class="dleg-lbl">Measured</span></div><span class="dleg-val"><?= $optCoverage ?>%</span></div>
          <div class="dleg-row"><div class="dleg-left"><span class="dleg-dot" style="background:#D3D1C7"></span><span class="dleg-lbl">Not measured</span></div><span class="dleg-val"><?= 100-$optCoverage ?>%</span></div>
          <div style="font-size:10px;color:#9ca3af;margin-top:6px"><?= number_format($preschoolMeasured) ?> of <?= number_format($preschoolEst) ?><br>preschool children</div>
        </div>
      </div>
      <hr class="div">
      <div class="prog-row">
        <div class="prog-top"><span class="prog-lbl">Exclusively breastfed (ind23)</span><span class="prog-pct"><?= $breastfedPct ?>%</span></div>
        <div class="prog-track"><div class="prog-fill" style="width:<?= $breastfedPct ?>%;background:#1D9E75"></div></div>
      </div>
      <div class="prog-row">
        <div class="prog-top"><span class="prog-lbl">Fully immunized FIC (ind26)</span><span class="prog-pct"><?= $ficPct ?>%</span></div>
        <div class="prog-track"><div class="prog-fill" style="width:<?= $ficPct ?>%;background:#378ADD"></div></div>
      </div>
      <div class="prog-row">
        <div class="prog-top"><span class="prog-lbl">Dewormed (ind25)</span><span class="prog-pct"><?= $schoolChildren>0 ? round($dewormed/$schoolChildren*100) : 0 ?>%</span></div>
        <div class="prog-track"><div class="prog-fill" style="width:<?= $schoolChildren>0?round($dewormed/$schoolChildren*100):0 ?>%;background:#7F77DD"></div></div>
      </div>
    </div>

    <div class="card">
      <div class="card-title"><span>Top reporting barangays</span><i class="fa fa-trophy text-gray-300"></i></div>
      <?php
        $maxBrgy = !empty($topBrgyRows) ? (int)$topBrgyRows[0]['total'] : 1;
        foreach ($topBrgyRows as $i => $br):
          $pct = $maxBrgy > 0 ? round($br['total']/$maxBrgy*100) : 0;
      ?>
      <div class="rank-row">
        <span class="rank-num">#<?= $i+1 ?></span>
        <span class="rank-name"><?= htmlspecialchars($br['barangay']) ?></span>
        <div class="rank-bar"><div class="rank-bfill" style="width:<?= $pct ?>%"></div></div>
        <span class="rank-val"><?= $br['total'] ?></span>
      </div>
      <?php endforeach; ?>
      <?php if (empty($topBrgyRows)): ?>
        <p class="text-xs text-gray-400 text-center py-4">No data for selected year</p>
      <?php endif; ?>
      <hr class="div">
      <div class="card-title" style="margin-bottom:8px"><span>Program coverage (ind32, ind38)</span></div>
      <div class="prog-row">
        <div class="prog-top"><span class="prog-lbl">HHs using iodized salt</span><span class="prog-pct"><?= $iodizedPct ?>%</span></div>
        <div class="prog-track"><div class="prog-fill" style="width:<?= $iodizedPct ?>%;background:#1D9E75"></div></div>
      </div>
      <div class="prog-row">
        <div class="prog-top"><span class="prog-lbl">4Ps beneficiary HHs</span><span class="prog-pct"><?= $pantawidPct ?>%</span></div>
        <div class="prog-track"><div class="prog-fill" style="width:<?= $pantawidPct ?>%;background:#7F77DD"></div></div>
      </div>
    </div>
  </div>

  <!-- ── Nutritional status charts ────────────────────────────────────────── -->
  <div class="grid-2">
    <div class="card">
      <div class="card-title"><span>Preschool nutritional status 0–59 months (ind9b1–ind9b9)</span><i class="fa fa-baby text-gray-300"></i></div>
      <div class="ch h220"><canvas id="nutChart" role="img" aria-label="Horizontal bar chart of preschool nutritional status.">Preschool nutrition data.</canvas></div>
    </div>
    <div class="card">
      <div class="card-title"><span>School children BMI grades 1–6 (ind22a–ind22g)</span><i class="fa fa-school text-gray-300"></i></div>
      <div class="ch h220"><canvas id="bmiChart" role="img" aria-label="Horizontal bar chart of school BMI categories.">School BMI data.</canvas></div>
    </div>
  </div>

  <!-- ── Sanitation + Water + Housing ─────────────────────────────────────── -->
  <div class="grid-3">

    <div class="card">
      <div class="card-title"><span>Toilet facility (ind27)</span><i class="fa fa-building text-gray-300"></i></div>
      <?php
        $tColors = ['#1D9E75','#378ADD','#EF9F27','#D85A30','#E24B4A'];
        $ti = 0;
        foreach ($toiletPcts as $lbl => $pct):
      ?>
        <?= hbar($lbl, $pct, $tColors[$ti++]) ?>
      <?php endforeach; ?>
      <hr class="div">
      <div class="card-title" style="margin-bottom:8px"><span>Garbage disposal (ind28)</span></div>
      <?php
        $gColors = ['#1D9E75','#639922','#EF9F27','#E24B4A']; $gi = 0;
        foreach ($garbPcts as $lbl => $pct):
      ?>
        <?= hbar($lbl, $pct, $gColors[$gi++]) ?>
      <?php endforeach; ?>
    </div>

    <div class="card">
      <div class="card-title"><span>Water source (ind29)</span><i class="fa fa-tint text-gray-300"></i></div>
      <div class="dnut-wrap" style="margin-bottom:12px">
        <div class="dnut-canvas" style="width:100px;height:100px">
          <canvas id="waterChart" role="img" aria-label="Donut chart of household water sources.">Water source data.</canvas>
        </div>
        <div class="dnut-leg">
          <?php foreach(['Level III (pipe/purified)'=>['#1D9E75',$level3],'Level II (spring/communal)'=>['#378ADD',$level2],'Level I (open/artesian)'=>['#EF9F27',$level1]] as $lbl=>[$clr,$pct]): ?>
          <div class="dleg-row">
            <div class="dleg-left"><span class="dleg-dot" style="background:<?= $clr ?>"></span><span class="dleg-lbl" style="font-size:11px"><?= $lbl ?></span></div>
            <span class="dleg-val" style="font-size:11px"><?= $pct ?>%</span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <hr class="div">
      <div class="card-title" style="margin-bottom:8px"><span>Food production (ind30)</span></div>
      <?php
        $fColors = ['#639922','#1D9E75','#378ADD','#D3D1C7']; $fi = 0;
        foreach ($foodPcts as $lbl => $pct):
      ?>
        <?= hbar($lbl, $pct, $fColors[$fi++]) ?>
      <?php endforeach; ?>
    </div>

    <div class="card">
      <div class="card-title"><span>Dwelling type (ind31)</span><i class="fa fa-home text-gray-300"></i></div>
      <?php
        $dColors = ['#378ADD','#85B7EB','#EF9F27','#D85A30','#E24B4A']; $di = 0;
        foreach ($dwellPcts as $lbl => $pct):
      ?>
        <?= hbar($lbl, $pct, $dColors[$di++]) ?>
      <?php endforeach; ?>
      <hr class="div">
      <div class="card-title" style="margin-bottom:8px"><span>Health &amp; nutrition workers (ind37)</span></div>
      <div style="display:flex;gap:8px">
        <div style="flex:1;background:#f9fafb;border-radius:8px;padding:10px 12px;text-align:center">
          <div style="font-size:10px;color:#9ca3af;margin-bottom:4px">BNS workers</div>
          <div style="font-size:20px;font-weight:700;color:#1D9E75"><?= (int)($popRow['bns_workers']??0) ?></div>
        </div>
        <div style="flex:1;background:#f9fafb;border-radius:8px;padding:10px 12px;text-align:center">
          <div style="font-size:10px;color:#9ca3af;margin-bottom:4px">BHW workers</div>
          <div style="font-size:20px;font-weight:700;color:#378ADD"><?= (int)($popRow['bhw_workers']??0) ?></div>
        </div>
        <div style="flex:1;background:#f9fafb;border-radius:8px;padding:10px 12px;text-align:center">
          <div style="font-size:10px;color:#9ca3af;margin-bottom:4px">Eateries</div>
          <div style="font-size:20px;font-weight:700;color:#EF9F27"><?= (int)($popRow['eateries']??0) ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Pending/Rejected table ────────────────────────────────────────────── -->
  <div class="tbl-card">
    <div class="tbl-top">
      <div class="tbl-title">
        Pending &amp; rejected reports
        <?php if ($pendingReports > 0): ?><span class="badge b-warn"><?= $pendingReports ?> pending</span><?php endif; ?>
        <?php if ($rejectedReports > 0): ?><span class="badge b-danger"><?= $rejectedReports ?> rejected</span><?php endif; ?>
      </div>
      <div class="srch">
        <i class="fa fa-search"></i>
        <input type="text" id="tSearch" placeholder="Search name, barangay...">
      </div>
      <a href="cno_reports.php?<?= htmlspecialchars(bq()) ?>" class="view-all">View all reports <i class="fa fa-arrow-right" style="font-size:10px"></i></a>
    </div>
    <div style="overflow-x:auto">
      <table class="main">
        <thead>
          <tr>
            <th style="width:20%">Name</th>
            <th style="width:27%">Report title</th>
            <th style="width:13%">Barangay</th>
            <th style="width:11%">Status</th>
            <th style="width:13%">Date</th>
            <th style="width:9%">Action</th>
          </tr>
        </thead>
        <tbody id="tBody">
          <?php if ($tableRows): ?>
            <?php foreach ($tableRows as $row):
              $initials = strtoupper(substr($row['uname'], 0, 2));
              $spClass  = $row['status']==='Pending' ? 'sp-p' : 'sp-r';
            ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:7px">
                  <?php
                    $pic = !empty($row['profile_pic']) && file_exists("../uploads/".$row['profile_pic'])
                           ? '<img src="../uploads/'.htmlspecialchars($row['profile_pic']).'" style="width:26px;height:26px;border-radius:50%;object-fit:cover">'
                           : '<div class="av">'.htmlspecialchars($initials).'</div>';
                    echo $pic;
                  ?>
                  <span><?= htmlspecialchars($row['uname']) ?></span>
                </div>
              </td>
              <td style="color:#6b7280"><?= htmlspecialchars($row['title']) ?></td>
              <td><?= htmlspecialchars($row['barangay']) ?></td>
              <td><span class="sp <?= $spClass ?>"><span class="sp-dot"></span><?= $row['status'] ?></span></td>
              <td style="color:#9ca3af"><?= htmlspecialchars($row['report_date']) ?></td>
              <td><a href="view_report.php?id=<?= (int)$row['id'] ?>" class="act-btn">View</a></td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:24px">
              <i class="fa fa-check-circle" style="color:#1D9E75;font-size:20px;display:block;margin-bottom:6px"></i>
              No pending or rejected reports for <?= $selectedYear ?>
            </td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="pg">
      <span class="pg-info">Showing <?= min($offset+1,$totalRows) ?>–<?= min($offset+$limit,$totalRows) ?> of <?= $totalRows ?> results</span>
      <div class="pg-btns">
        <?php if ($page>1): ?>
          <a href="?<?= htmlspecialchars(bq(['page'=>$page-1])) ?>" class="pg-btn">Prev</a>
        <?php else: ?>
          <button class="pg-btn" disabled>Prev</button>
        <?php endif; ?>
        <?php for ($i=max(1,$page-2); $i<=min($totalPages,$page+2); $i++): ?>
          <a href="?<?= htmlspecialchars(bq(['page'=>$i])) ?>" class="pg-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page<$totalPages): ?>
          <a href="?<?= htmlspecialchars(bq(['page'=>$page+1])) ?>" class="pg-btn">Next</a>
        <?php else: ?>
          <button class="pg-btn" disabled>Next</button>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div><!-- /dash -->

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const Y = <?= $selectedYear ?>;

/* ── Monthly trend ── */
new Chart(document.getElementById('trendChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($monthLabels) ?>,
    datasets: [
      { label:'Approved', data:<?= json_encode($monthApproved) ?>, backgroundColor:'#1D9E75', stack:'s' },
      { label:'Pending',  data:<?= json_encode($monthPending)  ?>, backgroundColor:'#EF9F27', stack:'s' },
      { label:'Rejected', data:<?= json_encode($monthRejected) ?>, backgroundColor:'#E24B4A', stack:'s' }
    ]
  },
  options: {
    responsive:true, maintainAspectRatio:false,
    plugins:{ legend:{display:false}, tooltip:{mode:'index'} },
    scales:{
      x:{ stacked:true, grid:{display:false}, ticks:{font:{size:11}} },
      y:{ stacked:true, beginAtZero:true, grid:{color:'rgba(0,0,0,0.05)'}, ticks:{font:{size:11}, stepSize:1} }
    }
  }
});

/* ── OPT donut ── */
new Chart(document.getElementById('optChart'), {
  type:'doughnut',
  data:{ labels:['Measured','Not measured'], datasets:[{ data:[<?= $optCoverage ?>,<?= 100-$optCoverage ?>], backgroundColor:['#1D9E75','#e5e7eb'], borderWidth:0, hoverOffset:4 }] },
  options:{ responsive:true, maintainAspectRatio:false, cutout:'72%', plugins:{ legend:{display:false}, tooltip:{ callbacks:{ label:c => c.label+': '+c.raw+'%' } } } }
});

/* ── Preschool nutrition horizontal bar ── */
new Chart(document.getElementById('nutChart'), {
  type:'bar',
  data:{
    labels: <?= json_encode($nutLabels) ?>,
    datasets:[{ label:'Children', data:<?= json_encode($nutData) ?>,
      backgroundColor:['#E24B4A','#EF9F27','#1D9E75','#A32D2D','#D85A30','#378ADD','#7F77DD','#993C1D','#BA7517'],
      borderWidth:0, borderRadius:3 }]
  },
  options:{
    indexAxis:'y', responsive:true, maintainAspectRatio:false,
    plugins:{ legend:{display:false}, tooltip:{ callbacks:{ label:c => ' '+c.raw+' children' } } },
    scales:{
      x:{ beginAtZero:true, grid:{color:'rgba(0,0,0,0.05)'}, ticks:{font:{size:10}} },
      y:{ ticks:{font:{size:10}}, grid:{display:false} }
    }
  }
});

/* ── School BMI horizontal bar ── */
new Chart(document.getElementById('bmiChart'), {
  type:'bar',
  data:{
    labels: <?= json_encode($bmiLabels) ?>,
    datasets:[{ label:'Students', data:<?= json_encode($bmiData) ?>,
      backgroundColor:['#A32D2D','#D85A30','#993C1D','#BA7517','#1D9E75','#378ADD','#7F77DD'],
      borderWidth:0, borderRadius:3 }]
  },
  options:{
    indexAxis:'y', responsive:true, maintainAspectRatio:false,
    plugins:{ legend:{display:false}, tooltip:{ callbacks:{ label:c => ' '+c.raw+' students' } } },
    scales:{
      x:{ beginAtZero:true, grid:{color:'rgba(0,0,0,0.05)'}, ticks:{font:{size:10}} },
      y:{ ticks:{font:{size:10}}, grid:{display:false} }
    }
  }
});

/* ── Water source donut ── */
new Chart(document.getElementById('waterChart'), {
  type:'doughnut',
  data:{ labels:['Level III','Level II','Level I'], datasets:[{ data:[<?= $level3 ?>,<?= $level2 ?>,<?= $level1 ?>], backgroundColor:['#1D9E75','#378ADD','#EF9F27'], borderWidth:0, hoverOffset:4 }] },
  options:{ responsive:true, maintainAspectRatio:false, cutout:'68%', plugins:{ legend:{display:false}, tooltip:{ callbacks:{ label:c => c.label+': '+c.raw+'%' } } } }
});

/* ── Table search ── */
document.getElementById('tSearch').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#tBody tr').forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>
</body>
</html>