<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}

$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

/* Helper for clean display */
function val($a, $k, $fmt='int') {
    if(!isset($a[$k]) || $a[$k] === '') return '—';
    if($fmt === 'int')  return (int)$a[$k];
    if($fmt === 'pct')  return number_format((float)$a[$k],2).'%';
    if($fmt === 'dec2') return number_format((float)$a[$k],2);
    return htmlspecialchars($a[$k]);
}

/* Calculate percentage helper */
function calc_pct($numerator, $denominator) {
    if ($denominator > 0) {
        return number_format(($numerator / $denominator) * 100, 2);
    }
    return '0.00';
}

/* Base fields to SUM */
$base_fields = [
    'ind1','ind_male','ind_female','ind2','ind3','ind4','ind5','ind6a','ind6b',
    'ind7','ind8','ind9','ind10','ind11','ind12','ind13','ind14','ind15',
    'ind16','ind17a_public','ind17a_private','ind17b_public','ind17b_private',
    'ind18','ind19','ind20','ind23','ind24','ind25','ind26',
    'ind32','ind33','ind34','ind35','ind36','ind37a','ind37b','ind38'
];

/* Fields that need _no and _pct */
$group_fields = [
    '9b' => ['ind9b1','ind9b2','ind9b3','ind9b4','ind9b5','ind9b6','ind9b7','ind9b8','ind9b9'],
    '22' => ['ind22a','ind22b','ind22c','ind22d','ind22e','ind22f','ind22g'],
    '27' => ['ind27a','ind27b','ind27c','ind27d','ind27e'],
    '28' => ['ind28a','ind28b','ind28c','ind28d'],
    '29' => ['ind29a','ind29b','ind29c','ind29d','ind29e','ind29f','ind29g'],
    '30' => ['ind30a','ind30b','ind30c','ind30d'],
    '31' => ['ind31a','ind31b','ind31c','ind31d','ind31e','ind31f'],
];

/* Build parameters */
$params = [$selectedYear];
$barangayFilter = '';
if (!empty($_GET['barangays'])) {
    $barangays = $_GET['barangays'];
    $placeholders = implode(',', array_fill(0, count($barangays), '?'));
    $barangayFilter = "AND bns.barangay IN ($placeholders)";
    $params = array_merge($params, $barangays);
}

/* SQL: Get latest approved report per user, then aggregate */
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
        $barangayFilter
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
        /* Sum all base fields */
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
        /* Sum all _no fields for percentages */
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
        SUM(ind31d_no) AS ind31d_no, SUM(ind31e_no) AS ind31e_no, SUM(ind31f_no) AS ind31f_no
    FROM latest_per_user
    WHERE rn = 1
)
SELECT * FROM aggregated
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$totals = $stmt->fetch(PDO::FETCH_ASSOC);
$has_data = !empty($totals) && $totals['ind1'] !== null;

/* Recalculate percentages based on summed values */
if ($has_data) {
    // Indicator 9a: Percent Measured Coverage (OPT Plus)
    $totals['ind9a'] = calc_pct($totals['ind9'], $totals['ind8']);
    
    // Preschool Children Percentages (ind9b1 to ind9b9)
    for ($i = 1; $i <= 9; $i++) {
        $totals["ind9b{$i}_pct"] = calc_pct($totals["ind9b{$i}_no"], $totals['ind9']);
    }
    
    // Indicator 21: Percentage Coverage of School Children Measured
    $total_school_children = $totals['ind18'] + $totals['ind19'];
    $totals['ind21'] = calc_pct($totals['ind20'], $total_school_children);
    
    // School Children Percentages (ind22a to ind22g)
    $school_labels = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];
    foreach ($school_labels as $label) {
        $totals["ind22{$label}_pct"] = calc_pct($totals["ind22{$label}_no"], $totals['ind20']);
    }
    
    // Household Percentages (ind27 to ind31) - using ind2 as denominator
    $household_sections = ['27', '28', '29', '30', '31'];
    $household_labels = [
        '27' => ['a','b','c','d','e'],
        '28' => ['a','b','c','d'],
        '29' => ['a','b','c','d','e','f','g'],
        '30' => ['a','b','c','d'],
        '31' => ['a','b','c','d','e','f']
    ];
    
    foreach ($household_labels as $section => $labels) {
        foreach ($labels as $label) {
            $totals["ind{$section}{$label}_pct"] = calc_pct($totals["ind{$section}{$label}_no"], $totals['ind2']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CNO | Consolidated Data</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f0f0f0;font-family:"Times New Roman",serif;font-size:12px;line-height:1.4}
.body-layout{display:flex;justify-content:center;padding:20px 0;}
.container{max-width:1000px;width:100%;margin:0 auto;}
.document{background:#fff;width:21cm;min-height:33cm;margin:0 auto 30px auto;padding:2.5cm;box-shadow:0 0 8px rgba(0,0,0,0.15);position:relative;page-break-after:always;}
@media print{body{background:#fff;}.document{box-shadow:none;margin:0;width:100%;min-height:auto;padding:2cm;}}
.header-table{width:100%;border-collapse:collapse;margin-bottom:20px}
.header-table td{border:none;padding:4px 6px;vertical-align:middle}
.header-left{font-weight:bold;font-size:14px}
.header-logos{text-align:right}
.header-logos img{height:60px;margin-left:10px}
.report-info{text-align:center;margin-bottom:20px;font-size:12px}
table{width:100%;border-collapse:collapse;margin-bottom:15px;table-layout:fixed}
th,td{border:1px solid #000;padding:6px 8px;text-align:left;font-size:12px;vertical-align:top}
th{background:#ddd}
.indent{padding-left:20px}
.number-cell {display:flex;justify-content:space-between;text-align:center;}
.number-cell div {flex:1;padding:4px;border-left:1px solid #000;}
.number-cell div:first-child {border-left:none;}
.page-number{text-align:right;font-size:12px;color:#555;margin-top:10px}
table th:nth-child(2),
table td:nth-child(2) {
    text-align: center;
}
.info-banner {
    background: #e7f3ff;
    padding: 10px 15px;
    margin-bottom: 20px;
    border-radius: 6px;
    font-size: 12px;
    color: #0066cc;
    border-left: 4px solid #007bff;
}
</style>
</head>
<body>
<div class="body-layout">
<div class="container">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
    <h2 style="font-size:18px;">
      <span style="font-weight:normal;">Consolidated Barangay Situation Analysis</span>
    </h2>
    <div>
      <a href="all_barangay_data.php?year=<?= urlencode($selectedYear) ?><?= !empty($_GET['barangays']) ? '&' . http_build_query(['barangays' => $_GET['barangays']]) : '' ?>" 
         style="background:#6c757d;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;">
         <i class="fa fa-arrow-left"></i> Back
      </a>
      <a href="export_consolidated.php?year=<?= urlencode($selectedYear) ?><?= !empty($_GET['barangays']) ? '&' . http_build_query(['barangays' => $_GET['barangays']]) : '' ?>&format=pdf" target="_blank" 
         style="background:#dc3545;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;margin-left:5px;">
         <i class="fa fa-file-pdf"></i> Export PDF
      </a>
      <a href="export_consolidated.php?year=<?= urlencode($selectedYear) ?><?= !empty($_GET['barangays']) ? '&' . http_build_query(['barangays' => $_GET['barangays']]) : '' ?>&format=csv" target="_blank" 
         style="background:#198754;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;margin-left:5px;">
         <i class="fa fa-file-csv"></i> Export CSV
      </a>
    </div>
</div>

<?php if (!$has_data): ?>
<div class="info-banner">
    <i class="fas fa-info-circle"></i> 
    No approved reports found for <?= htmlspecialchars($selectedYear) ?> 
    <?= !empty($_GET['barangays']) ? 'in selected barangays.' : 'in any barangay.' ?>
</div>
<?php else: ?>

<!-- SINGLE CONSOLIDATED REPORT DOCUMENT -->
<div class="document">
  <table class="header-table">
    <tr>
      <td class="header-left">BNS Form No. IC<br>Barangay Nutrition Profile</td>
      <td class="header-logos">
        <img src="../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png">
        <img src="../logos/fixed/National_Nutrition_Council__NNC_.svg-removebg-preview.png">
        <img src="../logos/fixed/Bagong-Pilipinas-logo.png">
      </td>
    </tr>
  </table>

  <div class="report-info">
      <h3>CONSOLIDATED BARANGAY SITUATIONAL ANALYSIS (BSA)</h3>
      <strong>Calendar Year:</strong> <?= htmlspecialchars($selectedYear) ?> &nbsp;
      <strong>City:</strong> EL SALVADOR CITY &nbsp;
      <strong>Province:</strong> MISAMIS ORIENTAL
  </div>

<!-- ================= PAGE 1 ================= -->
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
    <tr><td class="indent">1. Total Population</td><td><?=val($totals,'ind1')?></td></tr>
    <tr class="indent"><td class="indent">Male</td><td><?=val($totals,'ind_male')?></td></tr>
    <tr class="indent"><td class="indent">Female</td><td><?=val($totals,'ind_female')?></td></tr>
    <tr><td class="indent">2. Number of households</td><td><?=val($totals,'ind2')?></td></tr>
    <tr><td class="indent">3. Total number of families</td><td><?=val($totals,'ind3')?></td></tr>
    <tr><td class="indent">4. Total Number of HHs More Than 5 Below Members</td><td><?=val($totals,'ind4')?></td></tr>
    <tr><td class="indent">5. Total Number of HHs More Than 5 Above Members</td><td><?=val($totals,'ind5')?></td></tr>
    <tr><td class="indent">6. Total number of women who are:</td><td></td></tr>
    <tr class="indent"><td class="indent">a. Pregnant</td><td><?=val($totals,'ind6a')?></td></tr>
    <tr class="indent"><td class="indent">b. Lactating</td><td><?=val($totals,'ind6b')?></td></tr>
    <tr><td class="indent">7. Total households with preschool children aged 0–59 months</td><td><?=val($totals,'ind7')?></td></tr>
    <tr><td class="indent">8. Actual population of preschool children 0–59 months</td><td><?=val($totals,'ind8')?></td></tr>
    <tr><td class="indent">9. Total preschool children 0–50 months measured during OPT Plus</td><td><?=val($totals,'ind9')?></td></tr>
    <tr><td class="indent">a. Percent (%) measured coverage (OPT Plus)</td><td><?=val($totals,'ind9a','dec2')?>%</td></tr>
    <tr>
      <td class="indent">b. Number and percent (%) of preschool children according to Nutritional Status</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $nutri = ['1. Severely underweight','2. Underweight','3. Normal weight','4. Severely wasted','5. Wasted','6. Overweight','7. Obese','8. Severely stunted','9. Stunted'];
    for($i=1;$i<=9;$i++): ?>
    <tr class="indent">
      <td class="indent"><?=$nutri[$i-1]?></td>
      <td class="number-cell">
        <div><?=val($totals,"ind9b{$i}_no")?></div>
        <div><?=val($totals,"ind9b{$i}_pct",'pct')?></div>
      </td>
    </tr>
    <?php endfor; ?>
    <tr><td class="indent">10. Total number of infants 0–5 months old</td><td><?=val($totals,'ind10')?></td></tr>
    <tr><td class="indent">11. Total number of infants 6–11 months old</td><td><?=val($totals,'ind11')?></td></tr>
    <tr><td class="indent">12. Total preschool children 0–23 months old</td><td><?=val($totals,'ind12')?></td></tr>
    <tr><td class="indent">13. Total preschool children 12–59 months old</td><td><?=val($totals,'ind13')?></td></tr>
    <tr><td class="indent">14. Total preschool children 24–59 months old</td><td><?=val($totals,'ind14')?></td></tr>
    <tr><td class="indent">15. Total families with wasted &amp; severely wasted preschool children</td><td><?=val($totals,'ind15')?></td></tr>
    <tr><td class="indent">16. Total families with stunted &amp; severely stunted preschool children</td><td><?=val($totals,'ind16')?></td></tr>
  </tbody>
</table>

<div class="page-break"></div>
</div>

<!-- ================= PAGE 2 ================= -->
<div class="document">
<table>
  <colgroup>
    <col style="width: auto;">
    <col style="width: 180px;"> 
  </colgroup>
  <tbody>
    <tr>
      <td class="indent">17. Total number of Educational Institutions</td>
      <td class="number-cell"><div>Public</div><div>Private</div></td>
    </tr>
    <tr class="indent">
      <td class="indent">a. Number of Day Care Centers</td>
      <td class="number-cell"><div><?=val($totals,'ind17a_public')?></div><div><?=val($totals,'ind17a_private')?></div></td>
    </tr>
    <tr class="indent">
      <td class="indent">b. Number of Elementary Schools</td>
      <td class="number-cell"><div><?=val($totals,'ind17b_public')?></div><div><?=val($totals,'ind17b_private')?></div></td>
    </tr>
    <tr><td class="indent">18. Total number of children enrolled in Kindergarten</td><td><?=val($totals,'ind18')?></td></tr>
    <tr><td class="indent">19. Total number of school children (Grades 1–6)</td><td><?=val($totals,'ind19')?></td></tr>
    <tr><td class="indent">20. Actual number of school children weighed at the start of the school year</td><td><?=val($totals,'ind20')?></td></tr>
    <tr><td class="indent">21. Percentage (%) coverage of school children measured</td><td><?=val($totals,'ind21','dec2')?>%</td></tr>
    <tr>
      <td class="indent">22. Number and percent (%) of school children according to Nutritional Status</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $school_labels_display = [
        'a' => 'a. Severely Wasted',
        'b' => 'b. Wasted', 
        'c' => 'c. Severely Stunted',
        'd' => 'd. Stunted',
        'e' => 'e. Normal',
        'f' => 'f. Overweight',
        'g' => 'g. Obese'
    ];
    foreach($school_labels_display as $label => $display): ?>
    <tr class="indent">
      <td class="indent"><?=$display?></td>
      <td class="number-cell">
        <div><?=val($totals,"ind22{$label}_no")?></div>
        <div><?=val($totals,"ind22{$label}_pct",'pct')?></div>
      </td>
    </tr>
    <?php endforeach; ?>
    <tr><td class="indent">23. 0–5 months old children exclusively breastfed</td><td><?=val($totals,'ind23')?></td></tr>
    <tr><td class="indent">24. Households with severely wasted and wasted school children</td><td><?=val($totals,'ind24')?></td></tr>
    <tr><td class="indent">25. School children dewormed at start of school year</td><td><?=val($totals,'ind25')?></td></tr>
    <tr><td class="indent">26. Fully immunized children</td><td><?=val($totals,'ind26')?></td></tr>
    <tr>
      <td class="indent">27. Households by type of toilet facility:</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $toilet_labels = [
        'a' => 'a. Water-sealed toilet',
        'b' => 'b. Antipolo (Unsanitary Toilet)',
        'c' => 'c. Open Pit',
        'd' => 'd. Shared',
        'e' => 'e. No Toilet'
    ];
    foreach($toilet_labels as $label => $display): ?>
    <tr class="indent">
      <td class="indent"><?=$display?></td>
      <td class="number-cell">
        <div><?=val($totals,"ind27{$label}_no")?></div>
        <div><?=val($totals,"ind27{$label}_pct",'pct')?></div>
      </td>
    </tr>
    <?php endforeach; ?>
    <tr>
      <td class="indent">28. Households by type of garbage disposal:</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $garbage_labels = [
        'a' => 'a. Barangay/City garbage collection',
        'b' => 'b. Own compose pit',
        'c' => 'c. Burning',
        'd' => 'd. Dumping'
    ];
    foreach($garbage_labels as $label => $display): ?>
    <tr class="indent">
      <td class="indent"><?=$display?></td>
      <td class="number-cell">
        <div><?=val($totals,"ind28{$label}_no")?></div>
        <div><?=val($totals,"ind28{$label}_pct",'pct')?></div>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div class="page-break"></div>
</div>

<!-- ================= PAGE 3 ================= -->
<div class="document">
<table>
  <colgroup>
    <col style="width: auto;">
    <col style="width: 180px;"> 
  </colgroup>
  <tbody>
    <tr>
      <td class="indent">29. Households by type of water source:</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $water_labels = [
        'a' => 'a. Pipe Water System (Level III)',
        'b' => 'b. Spring (Level II)',
        'c' => 'c. Deep Well With Topstand Communal Source (Level II)',
        'd' => 'd. Deep Well With Individual Faucet (Level III)',
        'e' => 'e. Purified Station (Level III)',
        'f' => 'f. Open Shallow Dug Well (Level I)',
        'g' => 'g. Artesian Well'
    ];
    foreach($water_labels as $label => $display): ?>
    <tr class="indent">
      <td class="indent"><?=$display?></td>
      <td class="number-cell">
        <div><?=val($totals,"ind29{$label}_no")?></div>
        <div><?=val($totals,"ind29{$label}_pct",'pct')?></div>
      </td>
    </tr>
    <?php endforeach; ?>
    <tr>
      <td class="indent">30. Household with:</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $household_labels = [
        'a' => 'a. Vegetable Garden',
        'b' => 'b. Livestock/Poultry',
        'c' => 'c. Fishponds',
        'd' => 'd. Other Specify: No Garden'
    ];
    foreach($household_labels as $label => $display): ?>
    <tr class="indent">
      <td class="indent"><?=$display?></td>
      <td class="number-cell">
        <div><?=val($totals,"ind30{$label}_no")?></div>
        <div><?=val($totals,"ind30{$label}_pct",'pct')?></div>
      </td>
    </tr>
    <?php endforeach; ?>
    <tr>
      <td class="indent">31. Households according to type of dwelling unit:</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $dwelling_labels = [
        'a' => 'a. Concrete',
        'b' => 'b. Semi Concrete',
        'c' => 'c. Wooden House',
        'd' => 'd. Nipa Bamboo House',
        'e' => 'e. Barong-Barong Makeshift',
        'f' => 'f. Makeshift'
    ];
    foreach($dwelling_labels as $label => $display): ?>
    <tr class="indent">
      <td class="indent"><?=$display?></td>
      <td class="number-cell">
        <div><?=val($totals,"ind31{$label}_no")?></div>
        <div><?=val($totals,"ind31{$label}_pct",'pct')?></div>
      </td>
    </tr>
    <?php endforeach; ?>
    <tr><td class="indent">32. Total number of households using iodized salt</td><td><?=val($totals,'ind32')?></td></tr>
    <tr><td class="indent">33. Total number of eateries/carenderia</td><td><?=val($totals,'ind33')?></td></tr>
    <tr><td class="indent">34. Total number of sari-sari stores related to iodized salt</td><td><?=val($totals,'ind34')?></td></tr>
    <tr><td class="indent">35. Total number of sari-sari stores related to cooking oil</td><td><?=val($totals,'ind35')?></td></tr>
    <tr><td class="indent">36. Total Number of Bakery With Fortified Flour</td><td><?=val($totals,'ind36')?></td></tr>
    <tr><td class="indent">37. Number of health and nutrition workers:</td><td></td></tr>
    <tr class="indent"><td class="indent">a. Barangay Nutrition Scholar</td><td><?=val($totals,'ind37a')?></td></tr>
    <tr class="indent"><td class="indent">b. Barangay Health Worker</td><td><?=val($totals,'ind37b')?></td></tr>
    <tr><td class="indent">38. Total number of households beneficiaries of Pantawid Pamilyang Pilipino</td><td><?=val($totals,'ind38')?></td></tr>
  </tbody>
</table>
</div>
<?php endif; ?>
</div>
</div>
</body>
</html>