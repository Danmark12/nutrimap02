<?php
// view_barangay_consolidated.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db/config.php';

// ✅ Require login & check CNO role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}

$barangay = isset($_GET['barangay']) ? $_GET['barangay'] : '';
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

if (empty($barangay)) {
    die("Barangay not specified!");
}

// Helper function for clean display
function val($arr, $k, $fmt = null) {
    if (!isset($arr[$k]) || $arr[$k] === null || $arr[$k] === '') return '—';
    $v = $arr[$k];
    if ($fmt === 'int') return (int)$v;
    if ($fmt === 'pct') return number_format((float)$v, 2) . '%';
    if ($fmt === 'dec2') return number_format((float)$v, 2);
    return htmlspecialchars($v);
}

function calc_pct($numerator, $denominator) {
    if ($denominator > 0) {
        return ($numerator / $denominator) * 100;
    }
    return 0;
}

function getBarangayLogo($barangay) {
    $logos = [
        'CNO' => 'CNO.png',
        'Amoros' => 'Amoros.png',
        'Bolisong' => 'Bolisong.png',
        'Cogon' => 'Cogon.png',
        'Himaya' => 'Himaya.png',
        'Hinigdaan' => 'Hinigdaan.png',
        'Kalabaylabay' => 'Kalabaylabay.png',
        'Molugan' => 'Molugan.png',
        'Bolobolo' => 'Bolobolo.png',
        'Poblacion' => 'Poblacion.png',
        'Kibonbon' => 'Kibonbon.png',
        'Sambulawan' => 'Sambulawan.png',
        'Calongonan' => 'Calongonan.png',
        'Sinaloc' => 'Sinaloc.png',
        'Taytay' => 'Taytay.png',
        'Ulaliman' => 'Ulaliman.png'
    ];
    return isset($logos[$barangay]) ? $logos[$barangay] : 'default.png';
}

// SQL: Get latest approved report per user for this barangay, then aggregate
/* MODIFICATION: Added NOT EXISTS to exclude CNO-archived reports only */
$sql = "
WITH latest_per_user AS (
    SELECT 
        bns.*,
        ROW_NUMBER() OVER (
            PARTITION BY bns.barangay, r.user_id 
            ORDER BY r.report_date DESC, r.report_time DESC
        ) AS rn
    FROM bns_reports bns
    JOIN reports r ON bns.report_id = r.id
    WHERE r.status = 'approved'
        AND bns.year = ?
        AND bns.barangay = ?
        -- Only exclude reports archived by CNO (BNS archives are ignored)
        AND NOT EXISTS (
            SELECT 1 FROM report_archives ra 
            WHERE ra.report_id = r.id 
            AND ra.user_type = 'CNO'
            AND ra.is_archived = 1
        )
),
aggregated AS (
    SELECT 
        SUM(ind1) AS ind1,
        SUM(ind_male) AS ind_male,
        SUM(ind_female) AS ind_female,
        SUM(ind2) AS ind2,
        SUM(ind3) AS ind3,
        SUM(ind4) AS ind4,
        SUM(ind5) AS ind5,
        SUM(ind6a) AS ind6a,
        SUM(ind6b) AS ind6b,
        SUM(ind7) AS ind7,
        SUM(ind8) AS ind8,
        SUM(ind9) AS ind9,
        SUM(ind10) AS ind10,
        SUM(ind11) AS ind11,
        SUM(ind12) AS ind12,
        SUM(ind13) AS ind13,
        SUM(ind14) AS ind14,
        SUM(ind15) AS ind15,
        SUM(ind16) AS ind16,
        SUM(ind17a_public) AS ind17a_public,
        SUM(ind17a_private) AS ind17a_private,
        SUM(ind17b_public) AS ind17b_public,
        SUM(ind17b_private) AS ind17b_private,
        SUM(ind18) AS ind18,
        SUM(ind19) AS ind19,
        SUM(ind20) AS ind20,
        SUM(ind23) AS ind23,
        SUM(ind24) AS ind24,
        SUM(ind25) AS ind25,
        SUM(ind26) AS ind26,
        SUM(ind32) AS ind32,
        SUM(ind33) AS ind33,
        SUM(ind34) AS ind34,
        SUM(ind35) AS ind35,
        SUM(ind36) AS ind36,
        SUM(ind37a) AS ind37a,
        SUM(ind37b) AS ind37b,
        SUM(ind38) AS ind38,
        SUM(ind9b1_no) AS ind9b1_no, SUM(ind9b2_no) AS ind9b2_no, SUM(ind9b3_no) AS ind9b3_no,
        SUM(ind9b4_no) AS ind9b4_no, SUM(ind9b5_no) AS ind9b5_no, SUM(ind9b6_no) AS ind9b6_no,
        SUM(ind9b7_no) AS ind9b7_no, SUM(ind9b8_no) AS ind9b8_no, SUM(ind9b9_no) AS ind9b9_no,
        SUM(ind22a_no) AS ind22a_no, SUM(ind22b_no) AS ind22b_no, SUM(ind22c_no) AS ind22c_no,
        SUM(ind22d_no) AS ind22d_no, SUM(ind22e_no) AS ind22e_no, SUM(ind22f_no) AS ind22f_no,
        SUM(ind22g_no) AS ind22g_no,
        SUM(ind27a_no) AS ind27a_no, SUM(ind27b_no) AS ind27b_no, SUM(ind27c_no) AS ind27c_no,
        SUM(ind27d_no) AS ind27d_no, SUM(ind27e_no) AS ind27e_no,
        SUM(ind28a_no) AS ind28a_no, SUM(ind28b_no) AS ind28b_no, SUM(ind28c_no) AS ind28c_no,
        SUM(ind28d_no) AS ind28d_no,
        SUM(ind29a_no) AS ind29a_no, SUM(ind29b_no) AS ind29b_no, SUM(ind29c_no) AS ind29c_no,
        SUM(ind29d_no) AS ind29d_no, SUM(ind29e_no) AS ind29e_no, SUM(ind29f_no) AS ind29f_no,
        SUM(ind29g_no) AS ind29g_no,
        SUM(ind30a_no) AS ind30a_no, SUM(ind30b_no) AS ind30b_no, SUM(ind30c_no) AS ind30c_no,
        SUM(ind30d_no) AS ind30d_no,
        SUM(ind31a_no) AS ind31a_no, SUM(ind31b_no) AS ind31b_no, SUM(ind31c_no) AS ind31c_no,
        SUM(ind31d_no) AS ind31d_no, SUM(ind31e_no) AS ind31e_no, SUM(ind31f_no) AS ind31f_no,
        COUNT(DISTINCT r.user_id) AS number_of_users
    FROM latest_per_user lpu
    JOIN reports r ON lpu.report_id = r.id
    WHERE lpu.rn = 1
)
SELECT * FROM aggregated
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$year, $barangay]);
$totals = $stmt->fetch(PDO::FETCH_ASSOC);

$has_data = !empty($totals) && $totals['ind1'] !== null;

// Recalculate percentages if data exists
if ($has_data) {
    $totals['ind9a'] = calc_pct($totals['ind9'], $totals['ind8']);
    
    for ($i = 1; $i <= 9; $i++) {
        $totals["ind9b{$i}_pct"] = calc_pct($totals["ind9b{$i}_no"], $totals['ind9']);
    }
    
    $total_school_children = $totals['ind18'] + $totals['ind19'];
    $totals['ind21'] = calc_pct($totals['ind20'], $total_school_children);
    
    $school_labels = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];
    foreach ($school_labels as $label) {
        $totals["ind22{$label}_pct"] = calc_pct($totals["ind22{$label}_no"], $totals['ind20']);
    }
    
    $household_sections = [
        '27' => ['a','b','c','d','e'],
        '28' => ['a','b','c','d'],
        '29' => ['a','b','c','d','e','f','g'],
        '30' => ['a','b','c','d'],
        '31' => ['a','b','c','d','e','f']
    ];
    
    foreach ($household_sections as $section => $labels) {
        foreach ($labels as $label) {
            $totals["ind{$section}{$label}_pct"] = calc_pct($totals["ind{$section}{$label}_no"], $totals['ind2']);
        }
    }
}

$barangay_logo = getBarangayLogo($barangay);
$user_count = $has_data ? $totals['number_of_users'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CNO | <?= htmlspecialchars($barangay) ?> Consolidated Data</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{
  background:#f0f0f0;
  font-family:"Times New Roman",serif;
  font-size:12px;
  line-height:1.4
}
.body-layout{display:flex;justify-content:center;padding:20px 0;}
.container{max-width:1000px;width:100%;margin:0 auto;}
.document{
  background:#fff;
  width:21cm;
  min-height:33cm;
  margin:0 auto 30px auto;
  padding:2.5cm;
  box-shadow:0 0 8px rgba(0,0,0,0.15);
  position:relative;
  page-break-after:always;
}
@media print {
  body{background:#fff;}
  .document{box-shadow:none;margin:0;width:100%;min-height:auto;padding:2cm;}
}
.header-table{width:100%;border-collapse:collapse;margin-bottom:20px}
.header-table td{border:none;padding:4px 6px;vertical-align:middle}
.header-left{font-weight:bold;font-size:14px}
.header-logos{
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 10px;
}
.header-logos img{
    max-height: 60px;
    width: auto;
    display: inline-block;
}
.report-info{text-align:center;margin-bottom:20px;font-size:12px}
table{width:100%;border-collapse:collapse;margin-bottom:15px;table-layout:fixed}
th,td{border:1px solid #000;padding:6px 8px;text-align:left;font-size:12px;vertical-align:top}
th{background:#ddd}
.indent{padding-left:20px}
table td:nth-child(2),
table th:nth-child(2) {
  width: 180px;
  text-align: center;
}
.number-cell {
  display: flex;
  justify-content: space-between;
  text-align: center;
}
.number-cell div {
  flex: 1;
  padding: 4px;
  border-left: 1px solid #000;
}
.number-cell div:first-child {
  border-left: none;
}
.page-number{text-align:right;font-size:12px;color:#555;margin-top:10px}
.notice{background:#fff3cd;padding:10px;border:1px solid #ffeeba;margin-bottom:15px}
.info-banner{background:#e7f3ff;padding:10px 15px;margin-bottom:20px;border-radius:6px;font-size:12px;color:#0066cc;border-left:4px solid #007bff}
</style>
</head>
<body>
<div class="layout">
<div class="body-layout">
<div class="container">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
    <h2 style="font-size:18px;">
      <span style="font-weight:normal;">Barangay <?= htmlspecialchars($barangay) ?> - Consolidated Report</span>
    </h2>
    <div>
      <a href="javascript:history.back()" 
         style="background:#6c757d;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;">
         <i class="fa fa-arrow-left"></i> Back
      </a>
      <?php if ($has_data): ?>
      <a href="export_barangay_consolidated.php?barangay=<?= urlencode($barangay) ?>&year=<?= $year ?>&format=pdf" target="_blank" 
         style="background:#dc3545;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;margin-left:5px;">
         <i class="fa fa-file-pdf"></i> Export PDF
      </a>
      <a href="export_barangay_consolidated.php?barangay=<?= urlencode($barangay) ?>&year=<?= $year ?>&format=csv" target="_blank" 
         style="background:#198754;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;margin-left:5px;">
         <i class="fa fa-file-csv"></i> Export CSV
      </a>
      <?php endif; ?>
    </div>
</div>

<?php if (!$has_data): ?>
<div class="notice">
<strong>No approved reports found for Barangay <?= htmlspecialchars($barangay) ?> in year <?= $year ?>.</strong>
</div>
<?php else: ?>

<?php if ($user_count > 1): ?>
<div class="info-banner" style="background: #e8f5e9; border-left-color: #4caf50; margin-bottom: 15px;">
    <i class="fas fa-users"></i> 
    <strong><?= $user_count ?> BNS Users</strong> contributed to this consolidated report
    <br><small>Data consolidated from the latest approved report of each Barangay Nutrition Scholar</small>
</div>
<?php elseif ($user_count == 1): ?>
<div class="info-banner" style="background: #e3f2fd; border-left-color: #2196f3; margin-bottom: 15px;">
    <i class="fas fa-user-check"></i> 
    Data from 1 BNS User (latest approved report)
</div>
<?php endif; ?>

<div class="document">
<table class="header-table">
<tr>
<td class="header-left">BNS Form No. IC<br>Barangay Nutrition Profile</td>
<td class="header-logos">
<img src="../logos/barangays/<?= htmlspecialchars($barangay_logo) ?>" alt="Barangay Logo" onerror="this.src='../logos/barangays/default.png'">
<img src="../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="City Logo">
<img src="../logos/fixed/National_Nutrition_Council__NNC_.svg-removebg-preview.png" alt="NNC Logo">
<img src="../logos/fixed/Bagong-Pilipinas-logo.png" alt="Bagong Pilipinas Logo">
</td>
</tr>
</table>

<div class="report-info">
  <strong>Calendar Year:</strong> <?= $year ?> &nbsp;
  <strong>Barangay:</strong> <?= htmlspecialchars($barangay) ?> &nbsp;
  <strong>City:</strong> EL SALVADOR CITY &nbsp;
  <strong>Province:</strong> MISAMIS ORIENTAL
</div>

<!-- PAGE 1 -->
<table>
  <colgroup>
    <col style="width: auto;">
    <col style="width: 180px;"> 
  </colgroup>
  <thead>
    <tr>
      <th>Indicator</th>
      <th>No.</th>
    </tr>
  </thead>
  <tbody>
    <tr><td class="indent">1. Total Population</td><td><?= val($totals,'ind1') ?></td></tr>
    <tr class="indent"><td class="indent">Male</td><td><?= val($totals,'ind_male') ?></td></tr>
    <tr class="indent"><td class="indent">Female</td><td><?= val($totals,'ind_female') ?></td></tr>
    <tr><td class="indent">2. Number of Households</td><td><?= val($totals,'ind2') ?></td></tr>
    <tr><td class="indent">3. Total Number of Family</td><td><?= val($totals,'ind3') ?></td></tr>
    <tr><td class="indent">4. Total Number of HHs More Than 5 Below Members</td><td><?= val($totals,'ind4') ?></td></tr>
    <tr><td class="indent">5. Total Number of HHs more Than 5 Above Members</td><td><?= val($totals,'ind5') ?></td></tr>
    <tr><td class="indent">6. Total Number of Women Who Are:</td><td></td></tr>
    <tr class="indent"><td class="indent">a. Pregnant</td><td><?= val($totals,'ind6a') ?></td></tr>
    <tr class="indent"><td class="indent">b. Lactating</td><td><?= val($totals,'ind6b') ?></td></tr>
    <tr><td class="indent">7. Total Number of Households With Preschool Children 0-59 Months</td><td><?= val($totals,'ind7') ?></td></tr>
    <tr><td class="indent">8. Estimate Population of Preschool Children 0-59 Months</td><td><?= val($totals,'ind8') ?></td></tr>
    <tr><td class="indent">9. Actual Number of Preschool Children 0-50 Months Old Measured During OPT Plus</td><td><?= val($totals,'ind9') ?></td></tr>
    <tr><td class="indent">a. Percent (%) Measured Coverage (OPT Plus)</td><td><?= val($totals,'ind9a','dec2') ?>%</td></tr>
    <tr>
      <td class="indent">b. Number and Percent (%) of Preschool Children According to Nutritional Status</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>

    <?php 
    $nutri = ['Severely underweight','Underweight','Normal weight','Severely wasted','Wasted','Overweight','Obese','Severely stunted','Stunted'];
    for ($i=1;$i<=9;$i++): ?>
    <tr class="indent">
      <td class="indent"><?= $i.'. '.$nutri[$i-1] ?></td>
      <td class="number-cell">
        <div><?= val($totals,"ind9b{$i}_no") ?></div>
        <div><?= val($totals,"ind9b{$i}_pct",'pct') ?></div>
      </td>
    </tr>
    <?php endfor; ?>
    <tr><td class="indent">10. Total Number of Infants 0-5 Months Old</td><td><?= val($totals,'ind10') ?></td></tr>
    <tr><td class="indent">11. Total Number of Infants 6-11 Months Old</td><td><?= val($totals,'ind11') ?></td></tr>
    <tr><td class="indent">12. Total Number of Preschool Children 0-23 Months Old</td><td><?= val($totals,'ind12') ?></td></tr>
    <tr><td class="indent">13. Total Number of Preschool Children 12-59 Months Old</td><td><?= val($totals,'ind13') ?></td></tr>
    <tr><td class="indent">14. Total Number of Preschool Children 24-59 Months Old</td><td><?= val($totals,'ind14') ?></td></tr>
    <tr><td class="indent">15. Total Number of Families With Wasted and Severely Wasted Preschool Children</td><td><?= val($totals,'ind15') ?></td></tr>
    <tr><td class="indent">16. Total Number of Families With Stunted and Severely Stunted Preschool Children</td><td><?= val($totals,'ind16') ?></td></tr>
  </tbody>
</table>

<div class="page-number">Page 1</div>
</div>

<!-- PAGE 2 -->
<div class="document">
<table>
  <colgroup>
    <col style="width: auto;">
    <col style="width: 180px;"> 
  </colgroup>
  <tbody>
    <tr>
      <td class="indent">17. Total Number of Educational Institutions(Pub./Priv.)</td>
      <td class="number-cell"><div>Public</div><div>Private</div></td>
    </tr>
    <tr>
      <td class="indent">a. Day Care Centers (Public/Private)</td>
      <td class="number-cell">
        <div><?= val($totals,'ind17a_public') ?></div>
        <div><?= val($totals,'ind17a_private') ?></div>
      </td>
    </tr>
    <tr>
      <td class="indent">b. Elementary Schools (Public/Private)</td>
      <td class="number-cell">
        <div><?= val($totals,'ind17b_public') ?></div>
        <div><?= val($totals,'ind17b_private') ?></div>
      </td>
    </tr>
    <tr><td class="indent">18. Total Number of Children Enrolled in Kindergarten</td><td><?= val($totals,'ind18') ?></td></tr>
    <tr><td class="indent">19. Total Number of School Children (grades 1-6)</td><td><?= val($totals,'ind19') ?></td></tr>
    <tr><td class="indent">20. Actual Number of School Children Weighed at Start of School Year</td><td><?= val($totals,'ind20') ?></td></tr>
    <tr><td class="indent">21. Percentage (%) Coverage of School Children Measured</td><td><?= val($totals,'ind21','dec2') ?>%</td></tr>
    <tr>
      <td class="indent">22. Number and Percent (%) of School Children According to Nutritional Status Body Mass Index</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $school = ['a. Severely Wasted','b. Wasted','c. Severely Stunted','d. Stunted','e. Normal','f. Overweight','g. Obese'];
    $letters = ['a','b','c','d','e','f','g'];
    for($i=0;$i<count($school);$i++): ?>
    <tr class="indent">
      <td class="indent"><?= $school[$i] ?></td>
      <td class="number-cell">
        <div><?= val($totals,"ind22{$letters[$i]}_no") ?></div>
        <div><?= val($totals,"ind22{$letters[$i]}_pct",'pct') ?></div>
      </td>
    </tr>
    <?php endfor; ?>
    <tr><td class="indent">23. 0-5 Months Old Children Exclusively Breastfeed</td><td><?= val($totals,'ind23') ?></td></tr>
    <tr><td class="indent">24. Households with Severely Wasted School Children</td><td><?= val($totals,'ind24') ?></td></tr>
    <tr><td class="indent">25. School Children Dewormed at the Start of the School Year</td><td><?= val($totals,'ind25') ?></td></tr>
    <tr><td class="indent">26. Fully Immunized Children(FIC)</td><td><?= val($totals,'ind26') ?></td></tr>
    <tr>
      <td class="indent">27. Households, by Type of Toilet Facility</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $toilet = ['a. Water-sealed toilet','b. Antipolo (Unsanitary Toilet)','c. Open Pit','d. Shared','e. No Toilet'];
    $letters = ['a','b','c','d','e'];
    for($i=0;$i<count($toilet);$i++): ?>
    <tr class="indent">
      <td class="indent"><?= $toilet[$i] ?></td>
      <td class="number-cell">
        <div><?= val($totals,"ind27{$letters[$i]}_no") ?></div>
        <div><?= val($totals,"ind27{$letters[$i]}_pct",'pct') ?></div>
      </td>
    </tr>
    <?php endfor; ?>
    <tr>
      <td class="indent">28. Households, by Type of Garbage Disposal</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $garbage = ['a. Barangay/City Garbage Collection','b. Own Compose Pit','c. Burning','d. Dumping'];
    $letters = ['a','b','c','d'];
    for($i=0;$i<count($garbage);$i++): ?>
    <tr class="indent">
      <td class="indent"><?= $garbage[$i] ?></td>
      <td class="number-cell">
        <div><?= val($totals,"ind28{$letters[$i]}_no") ?></div>
        <div><?= val($totals,"ind28{$letters[$i]}_pct",'pct') ?></div>
      </td>
    </tr>
    <?php endfor; ?>
  </tbody>
</table>

<div class="page-number">Page 2</div>
</div>

<!-- PAGE 3 -->
<div class="document">
<table>
  <colgroup>
    <col style="width: auto;">
    <col style="width: 180px;"> 
  </colgroup>
  <tbody>
    <tr>
      <td class="indent">29. Household, by Type of Water Source</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $water = ['a. Pipe Water System(Level III)','b. Spring (Level II)','c. Deep Well With Topstand Communal Source Water System (Level II)','d. Deep Well With Individual Faucet (Level III)','e. Purified Station (Level III)','f. Open Shallow Dug Well (Level I)','g. Artesian Well'];
    $letters = ['a','b','c','d','e','f','g'];
    for($i=0;$i<count($water);$i++): ?>
    <tr class="indent">
      <td class="indent"><?= $water[$i] ?></td>
      <td class="number-cell">
        <div><?= val($totals,"ind29{$letters[$i]}_no") ?></div>
        <div><?= val($totals,"ind29{$letters[$i]}_pct",'pct') ?></div>
      </td>
    </tr>
    <?php endfor; ?>
    <tr>
      <td class="indent">30. Household with</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $household = ['a. Vegetable Garden','b. Livestock/Poultry','c. Fishponds','d. Other Specify: No Garden'];
    $letters = ['a','b','c','d'];
    for($i=0;$i<count($household);$i++): ?>
    <tr class="indent">
      <td class="indent"><?= $household[$i] ?></td>
      <td class="number-cell">
        <div><?= val($totals,"ind30{$letters[$i]}_no") ?></div>
        <div><?= val($totals,"ind30{$letters[$i]}_pct",'pct') ?></div>
      </td>
    </tr>
    <?php endfor; ?>
    <tr>
      <td class="indent">31. Households according to type of dwelling unit:</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $dwelling = ['a. Concrete','b. Semi Concrete','c. Wooden House','d. Nipa Bamboo House','e. Barong-Barong Makeshift','f. Makeshift'];
    $letters = ['a','b','c','d','e','f'];
    for($i=0;$i<count($dwelling);$i++): ?>
    <tr class="indent">
      <td class="indent"><?= $dwelling[$i] ?></td>
      <td class="number-cell">
        <div><?= val($totals,"ind31{$letters[$i]}_no") ?></div>
        <div><?= val($totals,"ind31{$letters[$i]}_pct",'pct') ?></div>
      </td>
    </tr>
    <?php endfor; ?>
    <tr><td class="indent">32. Total Number of Households Using Iodized Salt</td><td><?= val($totals,'ind32') ?></td></tr>
    <tr><td class="indent">33. Total Number of Eateries/Carenderia</td><td><?= val($totals,'ind33') ?></td></tr>
    <tr><td class="indent">34. Total Number of Sari-Sari Stores Related to Iodized Salt</td><td><?= val($totals,'ind34') ?></td></tr>
    <tr><td class="indent">35. Total Number of Sari-Sari Stores Related to Cooking Oil</td><td><?= val($totals,'ind35') ?></td></tr>
    <tr><td class="indent">36. Total Number of Bakery With Fortified Flour</td><td><?= val($totals,'ind36') ?></td></tr>
    <tr><td class="indent">37. Number of Health and Nutrition Workers:</td><td></td></tr>
    <tr class="indent"><td class="indent">a. Barangay Nutrition Scholar</td><td><?= val($totals,'ind37a') ?></td></tr>
    <tr class="indent"><td class="indent">b. Barangay Health Worker</td><td><?= val($totals,'ind37b') ?></td></tr>
    <tr><td class="indent">38. Total Number of Households Beneficiaries of Pantawid Pamilyang Pilipino Program</td><td><?= val($totals,'ind38') ?></td></tr>
  </tbody>
</table>

<div class="page-number">Page 3</div>
</div>

<?php endif; ?>

</div>
</div>
</div>
</body>
</html>