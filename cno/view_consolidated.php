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

/* Base fields */
$base = [
 'ind1','ind_male','ind_female','ind2','ind3','ind4','ind5','ind6a','ind6b',
 'ind7','ind8','ind9','ind9a','ind10','ind11','ind12','ind13','ind14','ind15',
 'ind16','ind17a_public','ind17a_private','ind17b_public','ind17b_private',
 'ind18','ind19','ind20','ind21','ind23','ind24','ind25','ind26',
  'ind32','ind33','ind34','ind35','ind36',
'ind37a','ind37b','ind38'
];

/* Grouped fields (_no and _pct) */
$groups = [
 '9b' => ['ind9b1','ind9b2','ind9b3','ind9b4','ind9b5','ind9b6','ind9b7','ind9b8','ind9b9'],
 '22'=>['ind22a','ind22b','ind22c','ind22d','ind22e','ind22f','ind22g'],
 '27'=>['ind27a','ind27b','ind27c','ind27d','ind27e'],
 '28'=>['ind28a','ind28b','ind28c','ind28d'],
 '29'=>['ind29a','ind29b','ind29c','ind29d','ind29e','ind29f','ind29g'],
 '30'=>['ind30a','ind30b','ind30c','ind30d'],
 '31'=>['ind31a','ind31b','ind31c','ind31d','ind31e','ind31f'],
];

/* Build SUM select dynamically */
$sel = [];
foreach($base as $f) $sel[] = "SUM(lr.$f) AS $f";
foreach($groups as $arr){
    foreach($arr as $f){
        $sel[] = "SUM(lr.{$f}_no) AS {$f}_no";
        $sel[] = "SUM(lr.{$f}_pct) AS {$f}_pct";
    }
}

/* Barangay filter for CTE */
$barangayFilterCTE = '';
$params = [$selectedYear]; // Year parameter
if (!empty($_GET['barangays'])) {
    $barangays = $_GET['barangays'];
    $placeholders = implode(',', array_fill(0, count($barangays), '?'));
    $barangayFilterCTE = "AND bns.barangay IN ($placeholders)";
    $params = array_merge($params, $barangays);
}

/* Final SQL with CTE for latest report per barangay */
$sql = "
WITH latest_reports AS (
    SELECT bns.*, r.status,
           ROW_NUMBER() OVER (
               PARTITION BY bns.barangay 
               ORDER BY r.report_date DESC, r.report_time DESC
           ) AS rn
    FROM bns_reports bns
    JOIN reports r ON bns.report_id = r.id
    WHERE r.status = 'approved'
      AND bns.year = ?
      $barangayFilterCTE
)
SELECT ".implode(',', $sel)."
FROM latest_reports lr
WHERE rn = 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$totals = $stmt->fetch(PDO::FETCH_ASSOC);
$has_bns = !empty($totals);
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
.header-logos img{height:60px;margin-left:6px}
.report-info{text-align:center;margin-bottom:20px;font-size:12px}
table{width:100%;border-collapse:collapse;margin-bottom:15px;table-layout:fixed}
th,td{border:1px solid #000;padding:6px 8px;text-align:left;font-size:12px;vertical-align:top}
th{background:#ddd}
.indent{padding-left:20px}
.number-cell {display:flex;justify-content:space-between;text-align:center;}
.number-cell div {flex:1;padding:4px;border-left:1px solid #000;}
.number-cell div:first-child {border-left:none;}
.page-number{text-align:right;font-size:12px;color:#555;margin-top:10px}
table td:nth-child(2) {text-align: center;}
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
      <a href="javascript:history.back()" 
         style="background:#6c757d;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;">
         <i class="fa fa-arrow-left"></i> Back
      </a>
      <a href="export_consolidated.php?<?= http_build_query(['barangays' => $_GET['barangays'] ?? []]) ?>" target="_blank" 
         style="background:#198754;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;margin-left:5px;">
         Export PDF
      </a>
    </div>
</div>

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
<tbody>
<tr><td class="ind">1. Total Population</td><td><?=val($totals,'ind1')?></td></tr>
<tr class="indent"><td class="ind">Male</td><td><?=val($totals,'ind_male')?></td></tr>
<tr class="indent"><td class="ind">Female</td><td><?=val($totals,'ind_female')?></td></tr>
<tr><td class="ind">2. Number of households</td><td><?=val($totals,'ind2')?></td></tr>
<tr><td class="ind">3. Total number of families</td><td><?=val($totals,'ind3')?></td></tr>
<tr><td class="ind">4. Total Number of HHs More Than 5 Below Members</td><td><?=val($totals,'ind4')?></td></tr>
<tr><td class="ind">5. Total Number of HHs More Than 5 Above Members</td><td><?=val($totals,'ind5')?></td></tr>
<tr><td class="ind">6. Total number of women who are:</td><td></td></tr>
<tr class="indent"><td class="ind">a. Pregnant</td><td><?=val($totals,'ind6a')?></td></tr>
<tr class="indent"><td class="ind">b. Lactating</td><td><?=val($totals,'ind6b')?></td></tr>
<tr><td class="ind">7. Total households with preschool children aged 0–59 months</td><td><?=val($totals,'ind7')?></td></tr>
<tr><td class="ind">8. Actual population of preschool children 0–59 months</td><td><?=val($totals,'ind8')?></td></tr>
<tr><td class="ind">9. Total preschool children 0–50 months measured during OPT Plus</td><td><?=val($totals,'ind9')?></td></tr>
<tr><td class="ind">a. Percent (%) measured coverage (OPT Plus)</td><td><?=val($totals,'ind9a','dec2')?>%</td></tr>
<tr><td class="ind">b. Number and percent (%) of preschool children according to Nutritional Status</td>
    <td class="number-cell"><div>No.</div><div>%</div></td></tr>
<?php $nutri=['1. Severely underweight','2. Underweight','3. Normal weight','4. Severely wasted','5. Wasted','6. Overweight','7. Obese','8. Severely stunted','9. Stunted'];
for($i=1;$i<=9;$i++): ?>
<tr class="indent">
  <td class="ind"><?=$nutri[$i-1]?></td>
  <td class="number-cell">
    <div><?=val($totals,"ind9b{$i}_no")?></div>
    <div><?=val($totals,"ind9b{$i}_pct",'pct')?></div>
  </td>
</tr>
<?php endfor; ?>
<tr><td class="ind">10. Total number of infants 0–5 months old</td><td><?=val($totals,'ind10')?></td></tr>
<tr><td class="ind">11. Total number of infants 6–11 months old</td><td><?=val($totals,'ind11')?></td></tr>
<tr><td class="ind">12. Total preschool children 0–23 months old</td><td><?=val($totals,'ind12')?></td></tr>
<tr><td class="ind">13. Total preschool children 12–59 months old</td><td><?=val($totals,'ind13')?></td></tr>
<tr><td class="ind">14. Total preschool children 24–59 months old</td><td><?=val($totals,'ind14')?></td></tr>
<tr><td class="ind">15. Total families with wasted &amp; severely wasted preschool children</td><td><?=val($totals,'ind15')?></td></tr>
<tr><td class="ind">16. Total families with stunted &amp; severely stunted preschool children</td><td><?=val($totals,'ind16')?></td></tr>
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
<tr><td class="ind">17. Total number of Educational Institutions</td>
    <td class="number-cell"><div>No.</div><div>%</div></td></tr>
<tr class="indent"><td class="ind">a. Number of Day Care Centers – Public / Private</td>
  <td class="number-cell"><div><?=val($totals,'ind17a_public')?></div><div><?=val($totals,'ind17a_private')?></div></td></tr>
<tr><td class="ind">b. Number of Elementary Schools – Public / Private</td>
  <td class="number-cell"><div><?=val($totals,'ind17b_public')?></div><div><?=val($totals,'ind17b_private')?></div></td></tr>

  <tr><td class="ind">18. Total number of children enrolled in Kindergarten (DepEd supervised)</td><td><?=val($totals,'ind18')?></td></tr>
<tr><td class="ind">19. Total number of school children (Grades 1–6)</td><td><?=val($totals,'ind19')?></td></tr>
<tr><td class="ind">20. Total number of school children weighed at the start of the school year (K–Gr.6)</td><td><?=val($totals,'ind20')?></td></tr>
<tr><td class="ind">21. Percentage (%) coverage of school children measured</td><td><?=val($totals,'ind21','dec2')?>%</td></tr>
<tr><td class="ind">22. Number and percent (%) of school children according to Nutritional Status</td>
    <td class="number-cell"><div>No.</div><div>%</div></td></tr>
<?php foreach(['a. Severely Wasted','b. Wasted','c. Normal','d. Overweight','e. Obese'] as $c=>$lbl): ?>
<tr class="indent">
  <td class="ind"><?=$lbl?></td>
  <td class="number-cell">
    <div><?=val($totals,"ind22{$c}_no")?></div>
    <div><?=val($totals,"ind22{$c}_pct",'pct')?></div>
  </td>
</tr>
<?php endforeach; ?>
<tr><td class="ind">23. 0–5 months old children exclusively breastfed</td><td><?=val($totals,'ind23')?></td></tr>
<tr><td class="ind">24. Households with severely wasted and wasted school children</td><td><?=val($totals,'ind24')?></td></tr>
<tr><td class="ind">25. School children dewormed at start of school year</td><td><?=val($totals,'ind25')?></td></tr>
<tr><td class="ind">26. Fully immunized children</td><td><?=val($totals,'ind26')?></td></tr>
<tr><td class="ind">27. Households by type of toilet facility:</td><td class="number-cell"><div>No.</div><div>%</div></td></tr>
<?php foreach(['a. Water-sealed toilet','b. Antipolo (Unsanitary Toilet)','c. Open Pit/Shared','d. No Toilet'] as $c=>$lbl): ?>
<tr class="indent">
  <td class="ind"><?=$lbl?></td>
  <td class="number-cell">
    <div><?=val($totals,"ind27{$c}_no")?></div>
    <div><?=val($totals,"ind27{$c}_pct",'pct')?></div>
  </td>
</tr>
<?php endforeach; ?>
<tr><td class="ind">28. Households by type of garbage disposal:</td><td class="number-cell"><div>No.</div><div>%</div></td></tr>
<?php foreach(['a. Barangay/City garbage collection','b. Own compose pit','c. Burning','d. Dumping'] as $c=>$lbl): ?>
<tr class="indent">
  <td class="ind"><?=$lbl?></td>
  <td class="number-cell">
    <div><?=val($totals,"ind28{$c}_no")?></div>
    <div><?=val($totals,"ind28{$c}_pct",'pct')?></div>
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

<tr><td class="ind">29. Households by type of water source:</td><td class="number-cell"><div>No.</div><div>%</div></td></tr>
<?php foreach(['a. Pipe water system','b. Spring – Level II','c. Deep well with topstand communal source water system (Level II)','d. Deep Well With Individual Faucet (Level III)','e. Purified Station (Level III)','f. Open shallow dug well (Level I)', 'g. Artesian Well '] as $c=>$lbl): ?>
<tr class="indent">
  <td class="ind"><?=$lbl?></td>
  <td class="number-cell">
    <div><?=val($totals,"ind29{$c}_no")?></div>
    <div><?=val($totals,"ind29{$c}_pct",'pct')?></div>
  </td>
</tr>

<?php endforeach; ?>
<tr><td class="ind">30. Household with:</td><td class="number-cell"><div>No.</div><div>%</div></td></tr>
<?php foreach(['a. Vegetable garden','b. Livestock/poultry','c. Fishponds','d. No garden'] as $c=>$lbl): ?>
<tr class="indent">
  <td class="ind"><?=$lbl?></td>
  <td class="number-cell">
    <div><?=val($totals,"ind30{$c}_no")?></div>
    <div><?=val($totals,"ind30{$c}_pct",'pct')?></div>
  </td>
</tr>
<?php endforeach; ?>
<tr><td class="ind">31. Households according to type of dwelling unit</td><td class="number-cell"><div>No.</div><div>%</div></td></tr>
<?php foreach(['a. Concrete','b. Semi concrete','c. Wooden house','d. Nipa bamboo house','e. Barong-barong makeshift'] as $c=>$lbl): ?>
<tr class="indent">
  <td class="ind"><?=$lbl?></td>
  <td class="number-cell">
    <div><?=val($totals,"ind31{$c}_no")?></div>
    <div><?=val($totals,"ind31{$c}_pct",'pct')?></div>
  </td>
</tr>
<?php endforeach; ?>

<tr><td class="ind">32. Total number of households using iodized salt</td><td><?=val($totals,'ind32')?></td></tr>
<tr><td class="ind">33. Total number of eateries/carenderia</td><td><?=val($totals,'ind33')?></td></tr>
<tr><td class="ind">34. Total number of bakeries</td><td><?=val($totals,'ind34')?></td></tr>
<tr><td class="ind">35. Total number of sari-sari stores</td><td><?=val($totals,'ind35')?></td></tr>
<tr><td class="ind">36. Total Number of Bakery With Fortified Flour</td><td><?=val($totals,'ind36')?></td></tr>

<tr><td class="ind">37. Number of health and nutrition workers:</td><td class="number-cell"><div>No.</div><div>%</div></td></tr>
<tr class="indent"><td class="ind">a. Barangay Nutrition Scholar</td><td><?=val($totals,'ind37a')?></td></tr>
<tr class="indent"><td class="ind">b. Barangay Health Worker</td><td><?=val($totals,'ind37b')?></td></tr>
<tr><td class="ind">38. Total number of households beneficiaries of Pantawid Pamilyang Pilipino</td><td><?=val($totals,'ind38')?></td></tr>
</tbody>
  </div>
</table>
</div>
</body>
</html>
