<?php
// ✅ view_report.php
session_start();
require '../../db/config.php'; // PDO connection

// ✅ Initialize defaults
$error = null;
$row   = null;
$meta  = null;

// ✅ Validate report ID
$report_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($report_id <= 0) {
    $error = "Report not found!";
}

// ✅ Get title & year of selected report first
if (!$error) {
    $stmt = $pdo->prepare("
        SELECT b.title, b.year
        FROM reports r
        JOIN bns_reports b ON b.report_id = r.id
        WHERE r.id = :id AND r.status = 'Approved'
        LIMIT 1
    ");
    $stmt->execute(['id' => $report_id]);
    $meta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$meta) {
        $error = "Report not found or not approved!";
    }
}

// ✅ Fetch the latest approved report with same title + year
if (!$error) {
    $stmt = $pdo->prepare("
        SELECT r.id AS reports_id, r.report_date, r.report_time, r.status, b.*
        FROM reports r
        LEFT JOIN bns_reports b ON b.report_id = r.id
        WHERE r.status = 'Approved'
          AND b.title = :title
          AND b.year  = :year
        ORDER BY r.report_date DESC, r.report_time DESC
        LIMIT 1
    ");
    $stmt->execute([
        'title' => $meta['title'],
        'year'  => $meta['year']
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $error = "No updated report found!";
    }
}

// ✅ Safe flag for checking if BNS data exists
$has_bns = !empty($row) && !empty($row['report_id']);

// ✅ Barangay logo fetcher (normalized keys)
function getBarangayLogo($barangay) {
    $map = [
        'cno' => 'CNO.png',
        'amoros' => 'Amoros.png',
        'bolisong' => 'Bolisong.png',
        'cogon' => 'Cogon.png',
        'himaya' => 'Himaya.png',
        'hinigdaan' => 'Hinigdaan.png',
        'kalabaylabay' => 'Kalabaylabay.png',
        'molugan' => 'Molugan.png',
        'pedro s. baculio' => 'Pedro_sa_Baculio.png',
        'pedro sa baculio' => 'Pedro_sa_Baculio.png',
        'poblacion' => 'Poblacion.png',
        'quibonbon' => 'Quibonbon.png',
        'sambulawan' => 'Sambulawan.png',
        'san francisco de asis' => 'San_Francisco_de_Asis.png',
        'sinaloc' => 'Sinaloc.png',
        'taytay' => 'Taytay.png',
        'ulaliman' => 'Ulaliman.png'
    ];

    $key = strtolower(trim($barangay ?? ''));
    return $map[$key] ?? 'default.png';
}

// ✅ Determine barangay logo
$barangay_name = $has_bns ? $row['barangay'] : '';
$barangay_logo = getBarangayLogo($barangay_name);

// ✅ Safe value formatter (centralized handling)
function val($arr, $k, $fmt = null) {
    if (!is_array($arr) || !isset($arr[$k]) || $arr[$k] === null || $arr[$k] === '') {
        return '—';
    }

    $v = $arr[$k];

    switch ($fmt) {
        case 'int': 
            return (int)$v;
        case 'pct': 
            return number_format((float)$v, 2) . '%';
        case 'dec2': 
            return number_format((float)$v, 2);
        default: 
            return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }
}


// ✅ Barangay logo fallback
$barangay_logo = $has_bns ? getBarangayLogo($row['barangay'] ?? '') : 'default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View BNS Report — CNO NutriMap</title>
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
  display:flex;
  justify-content:flex-start;
  align-items:right;
  gap:8px;
}
.header-logos img{height: 75px;object-fit:contain}
.report-info{text-align:center;margin-bottom:20px;font-size:12px}
table{width:100%;border-collapse:collapse;margin-bottom:15px;table-layout:fixed}
th,td{border:1px solid #000;padding:6px 8px;text-align:left;font-size:12px;vertical-align:top}
th{background:#ddd}
.indent{padding-left:20px}

/* ✅ FIX: second column uniform size */
table td:nth-child(2),
table th:nth-child(2) {
  width: 180px; 
  text-align: center;
}

/* ✅ Number-cell layout */
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
</style>
</head>
<body>

<div class="layout">

<div class="body-layout">
<div class="container">

<div class="body-layout">
  <main class="content">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
    <h2 style="font-size:18px;">
      <span style="font-weight:normal;">Title:</span>
      <?= $has_bns ? htmlspecialchars($row['title']) : 'Barangay Nutrition Report' ?>
    </h2>
    <div>
      <a href="javascript:history.back()" 
         style="background:#6c757d;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;">
         <i class="fa fa-arrow-left"></i> Back
      </a>
    </div>
</div>

<?php if (!$has_bns): ?>
<div class="notice">
<strong>Note:</strong> Report exists (ID: <?= htmlspecialchars($row['reports_id'] ?? $report_id) ?>) but no BNS data was found.
</div>
<?php endif; ?>

<div class="document">
<table class="header-table">
<tr>
<td class="header-left">BNS Form No. IC<br>Barangay Nutrition Profile</td>
<td class="header-logos">
<img src="../../logos/barangays/<?= urlencode($barangay_logo) ?>" alt="Barangay Logo">
  <img src="../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="El Salvador Seal">
  <img src="../../logos/fixed/National_Nutrition_Council__NNC_.svg-removebg-preview.png" alt="NNC Logo">
  <img src="../../logos/fixed/Bagong-Pilipinas-logo.png" alt="Bagong Pilipinas">
</td>
</tr>
</table>


    <div class="report-info">
        <h3>BARANGAY SITUATIONAL ANALYSIS (BSA)</h3>				
    <strong>Calendar Year:</strong> <?= $has_bns ? val($row,'year') : '—' ?> &nbsp;
    <strong>Barangay:</strong> <?= val($row,'barangay') ?> &nbsp;
    <strong>City:</strong> EL SALVADOR CITY &nbsp;
    <strong>Province:</strong> MISAMIS ORIENTAL
    </div>

    <table>
    <thead>
    <tr>
        <th>Indicator</th>
        <th>Number / %</th>
    </tr>
    </thead>
    <tbody>
  <tbody>
  <tr><td>1. Total Population</td><td><?= $has_bns ? val($row,'ind1','int') : '—' ?></td></tr>
    <tr class="indent"><td>Male</td><td><?= $has_bns ? val($row,'ind_male','int') : '—' ?></td></tr>
  <tr class="indent"><td>Female</td><td><?= $has_bns ? val($row,'ind_female','int') : '—' ?></td></tr>

  <tr><td>2. Number of Households</td><td><?= $has_bns ? val($row,'ind2','int') : '—' ?></td></tr>
  <tr><td>3. Total Number of Family</td><td><?= $has_bns ? val($row,'ind3','int') : '—' ?></td></tr>
  <tr><td>4. Total Number of HHs More Than 5 Below Members</td><td><?= $has_bns ? val($row,'ind4','int') : '—' ?></td></tr>
  <tr><td>5. Total Number of HHs more Than 5 Above Members</td><td><?= $has_bns ? val($row,'ind5','int') : '—' ?></td></tr>


  <tr><td>6. Total Number of Women Who Are:</td><td></td></tr>
  <tr class="indent"><td>a. Pregnant</td><td><?= $has_bns ? val($row,'ind6a','int') : '—' ?></td></tr>
  <tr class="indent"><td>b. Lactating</td><td><?= $has_bns ? val($row,'ind6b','int') : '—' ?></td></tr>

  <tr><td>7. Total Number of Households With Preschool Children 0-59 Months</td><td><?= $has_bns ? val($row,'ind7','int') : '—' ?></td></tr>
  <tr><td>8. Actual Population of Preschool Children 0-59 Months</td><td><?= $has_bns ? val($row,'ind8','int') : '—' ?></td></tr>
    <tr><td>9. Total Number of Preschool Children 0-50 Months Old Measured During OPT Plus</td><td><?= $has_bns ? val($row,'ind9','dec2') : '—' ?></td></tr>
  <tr><td>a. Percent (%) Measured Coverage (OPT Plus)</td><td><?= $has_bns ? val($row,'ind9a','dec2') : '—' ?></td></tr>
  <tr>
    <td>b. Number and Percent (%) of Preschool Children According to Nutritional Status</td>
    <td class="number-cell">
      <div>No.</div>
      <div>%</div>
    </td>
  </tr>
  </tr>
  <?php 
  $nutri = ['Severely underweight','Underweight','Normal weight','Severely wasted','Wasted','Overweight','Obese','Severely stunted','Stunted'];
  for ($i=1;$i<=9;$i++): ?>
  <tr class="indent">
    <td><?= $i.') '.$nutri[$i-1] ?></td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,"ind9b{$i}_no",'int') : '—' ?></div>
      <div><?= $has_bns ? val($row,"ind9b{$i}_pct",'pct') : '—' ?></div>
    </td>
  </tr>
  <?php endfor; ?>

  <tr><td>10. Total Number of Infants 0-5 Months Old</td><td><?= $has_bns ? val($row,'ind10','int') : '—' ?></td></tr>
  <tr><td>11. Total Number of Infants 6-11 Months Old</td><td><?= $has_bns ? val($row,'ind11','int') : '—' ?></td></tr>
  <tr><td>12. Total Number of Preschool Children 0-23 Months Old</td><td><?= $has_bns ? val($row,'ind12','int') : '—' ?></td></tr>
  <tr><td>13. Total Number of Preschool Children 12-59 Months Old</td><td><?= $has_bns ? val($row,'ind13','int') : '—' ?></td></tr>
  <tr><td>14. Total Number of Preschool Children 24-59 Months Old</td><td><?= $has_bns ? val($row,'ind14','int') : '—' ?></td></tr>
  <tr><td>15. Total Number of Families With Wasted and Severely Wasted Preschool Children</td><td><?= $has_bns ? val($row,'ind15','int') : '—' ?></td></tr>
  <tr><td>16. Total Number of Families With Stunted and Severely Stunted Preschool Children</td><td><?= $has_bns ? val($row,'ind16','int') : '—' ?></td></tr>
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
    <td>17. Total Bumber of Educational Institutions(Pub./Priv.)</td>
    <td class="number-cell">
      <div>Public</div>
      <div>Private</div>
    </td>
  </tr>
  <tr>
    <td>a. Day Care Centers (Public/Private)</td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,'ind17a_public','int') : '—' ?></div>
      <div><?= $has_bns ? val($row,'ind17a_private','int') : '—' ?></div>
    </td>
  </tr>
  <tr>
    <td>b. Elementary Schools (Public/Private)</td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,'ind17b_public','int') : '—' ?></div>
      <div><?= $has_bns ? val($row,'ind17b_private','int') : '—' ?></div>
    </td>
  </tr>
  <tr><td>18. Total Number of Children Enrolled in Kindergarten</td><td><?= $has_bns ? val($row,'ind18','int') : '—' ?></td></tr>
  <tr><td>19. Total Number of School Children (grades 1-6)</td><td><?= $has_bns ? val($row,'ind19','int') : '—' ?></td></tr>
  <tr><td>20. Total Number of School Children Weighed at Start of School Year</td><td><?= $has_bns ? val($row,'ind20','int') : '—' ?></td></tr>
  <tr><td>21. Percentage (%) Coverage of School Children Measured</td><td><?= $has_bns ? (isset($row['ind21'])?number_format((float)$row['ind21'],2).'%':'—') : '—' ?></td></tr>
  <tr>
    <td>22. Number and Percent (%) of School Children According to Nutritional Status Body Mas Index</td>
    <td class="number-cell">
      <div>No.</div>
      <div>%</div>
    </td>
  </tr>
  </tr>
  <tr class="indent">
    <td>a. Severely Wasted</td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,'ind22a_no','int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row['ind22a_pct']) ? number_format((float)$row['ind22a_pct'],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
  <tr class="indent">
    <td>b. Wasted</td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,'ind22b_no','int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row['ind22b_pct']) ? number_format((float)$row['ind22b_pct'],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
  <tr class="indent">
    <td>c. Severly Stunted</td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,'ind22c_no','int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row['ind22c_pct']) ? number_format((float)$row['ind22c_pct'],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
  <tr class="indent">
    <td>d. Stunted</td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,'ind22d_no','int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row['ind22d_pct']) ? number_format((float)$row['ind22d_pct'],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
  <tr class="indent">
    <td>e. Normal</td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,'ind22e_no','int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row['ind22e_pct']) ? number_format((float)$row['ind22e_pct'],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
    <tr class="indent">
    <td>f. Overweight</td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,'ind22f_no','int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row['ind22f_pct']) ? number_format((float)$row['ind22f_pct'],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
  <tr class="indent">
    <td>g. Obese</td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,'ind22g_no','int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row['ind22g_pct']) ? number_format((float)$row['ind22g_pct'],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>


  <tr><td>23. 0-5 Months Old Children Exclusively Breastfeed</td><td><?= $has_bns ? val($row,'ind23','int') : '—' ?></td></tr>
  <tr><td>24. Households with Severely Wasted School Children</td><td><?= $has_bns ? val($row,'ind24','int') : '—' ?></td></tr>
  <tr><td>25. School Children Dewormed at the Start of the School Year</td><td><?= $has_bns ? val($row,'ind25','int') : '—' ?></td></tr>
  <tr><td>26. Fully Immunized Children(FIC)</td><td><?= $has_bns ? val($row,'ind26','int') : '—' ?></td></tr>
  <tr>
    <td>27. Households, by Type of Toilet Facility</td>
    <td class="number-cell">
      <div>No.</div>
      <div>%</div>
    </td>
  </tr>
  <?php for($i='a';$i<='d';$i++): ?>
  <tr class="indent">
    <td><?= strtoupper($i) ?>. <?= ['Water-sealed','Antipolo (Unsanitary Toilet)','Open Pit','Shared', 'No Toilet'][ord($i)-97] ?></td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,"ind27{$i}_no",'int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row["ind27{$i}_pct"]) ? number_format((float)$row["ind27{$i}_pct"],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
  <?php endfor; ?>

  <tr>
    <td>28. Households, by Type of Garbage Disposal</td>
    <td class="number-cell">
      <div>No.</div>
      <div>%</div>
    </td>
  </tr>
  <?php $g=['a. Barangay/City Garbage Collection','b. Own Compose Pit','c. Burning','d. Dumping']; 
  $i='a'; foreach($g as $label): ?>
  <tr class="indent">
    <td><?= $label ?></td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,"ind28{$i}_no",'int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row["ind28{$i}_pct"]) ? number_format((float)$row["ind28{$i}_pct"],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
  <?php $i++; endforeach; ?>
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
    <td>29. Household, by Type of Water Source</td>
    <td class="number-cell">
      <div>No.</div>
      <div>%</div>
    </td>
  </tr>
  <?php $w=['a. Pipe Water System(Level III)','b. Spring (Level II)','c. Deep Well With Topstand Communal Source Water System (Level II)','d. Deep Well With Individual Faucet (Level III)','e. Purified Station (Level III)','f. Open Shallow Dug Well (Level I)','g. Artesian Well'];
  $i='a'; foreach($w as $label): ?>
  <tr class="indent">
    <td><?= $label ?></td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,"ind29{$i}_no",'int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row["ind29{$i}_pct"]) ? number_format((float)$row["ind29{$i}_pct"],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
  <?php $i++; endforeach; ?>

  <tr>
    <td>30. Household with</td>
    <td class="number-cell">
      <div>No.</div>
      <div>%</div>
    </td>
  </tr>
  <?php $h=['a. Vegetable Garden','b. Livestock/Poultry','c. Fishponds','d. Other Specify: No Garden'];
  $i='a'; foreach($h as $label): ?>
  <tr class="indent">
    <td><?= $label ?></td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,"ind30{$i}_no",'int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row["ind30{$i}_pct"]) ? number_format((float)$row["ind30{$i}_pct"],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
  <?php $i++; endforeach; ?>



  <tr>
    <td>31. Households according to type of dwelling unit:</td>
    <td class="number-cell">
      <div>No.</div>
      <div>%</div>
    </td>
  </tr>
  <?php 
  $d=['a. Concrete','b. Semi Concrete','c. Wooden House','d. Nipa Bamboo House','e. Barong-Barong Makeshift', 'f. Makeshift']; 
  $i='a'; 
  foreach($d as $label): ?>
  <tr class="indent">
    <td><?= $label ?></td>
    <td class="number-cell">
      <div><?= $has_bns ? val($row,"ind31{$i}_no",'int') : '—' ?></div>
      <div><?= $has_bns ? (isset($row["ind31{$i}_pct"]) ? number_format((float)$row["ind31{$i}_pct"],2).'%' : '—') : '—' ?></div>
    </td>
  </tr>
  <?php $i++; endforeach; ?>

  <tr>
  <td style="font-weight:normal;">32. Total Number of Households Using Iodized Salt</td>
  <td class="number-cell">
    <div><?= $has_bns ? val($row,"ind32_no",'int') : '—' ?></div>
    <div><?= $has_bns ? (isset($row["ind32_pct"]) ? number_format((float)$row["ind32_pct"],2).'%' : '—') : '—' ?></div>
  </td>
</tr>

<tr>
  <td style="font-weight:normal;">33. Total Number of Eateries/Carenderia</td>
  <td class="number-cell">
    <div><?= $has_bns ? val($row,"ind33_no",'int') : '—' ?></div>
    <div><?= $has_bns ? (isset($row["ind33_pct"]) ? number_format((float)$row["ind33_pct"],2).'%' : '—') : '—' ?></div>
  </td>
</tr>

<tr>
  <td style="font-weight:normal;">34. Total Number of Sari-Sari Stores Related to Iodized Salt</td>
  <td class="number-cell">
    <div><?= $has_bns ? val($row,"ind34_no",'int') : '—' ?></div>
    <div><?= $has_bns ? (isset($row["ind34_pct"]) ? number_format((float)$row["ind34_pct"],2).'%' : '—') : '—' ?></div>
  </td>
</tr>

<tr>
  <td style="font-weight:normal;">35. Total Number of Sari-Sari Stores Related to Cooking Oil</td>
  <td class="number-cell">
    <div><?= $has_bns ? val($row,"ind35_no",'int') : '—' ?></div>
    <div><?= $has_bns ? (isset($row["ind35_pct"]) ? number_format((float)$row["ind35_pct"],2).'%' : '—') : '—' ?></div>
  </td>
</tr>

<tr>
  <td style="font-weight:normal;">36. Total Number of Bakery With Fortified Flour</td>
  <td class="number-cell">
    <div><?= $has_bns ? val($row,"ind36_no",'int') : '—' ?></div>
    <div><?= $has_bns ? (isset($row["ind36_pct"]) ? number_format((float)$row["ind36_pct"],2).'%' : '—') : '—' ?></div>
  </td>
</tr>




  <tr>
    <td>37. Number of Health and Nutrition Workers:</td>
    <td></td>
  </tr>


  <tr class="indent">
    <td>a. Barangay Nutrition Scholar</td>
    <td><?= $has_bns ? val($row,'ind37a','int') : '—' ?></td>
  </tr>
  <tr class="indent">
    <td>b. Barangay Health Worker</td>
    <td><?= $has_bns ? val($row,'ind37b','int') : '—' ?></td>
  </tr>
  <tr>
    <td>38. Total Number of Households Beneficiaries of Pantawid Pamilyang Pilipino Program</td>
    <td><?= $has_bns ? val($row,'ind38','int') : '—' ?></td>
  </tr>
  </table>
    <div class="page-number">Page 3</div>
    </div>


    </body>
    </html>
