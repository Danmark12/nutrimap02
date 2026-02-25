<?php
session_start();
require '../../db/config.php';

// ✅ Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// ✅ Activity log function
function logActivity($pdo, $user_id, $action) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->execute([$user_id, $action]);
}

// ✅ Validate report ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid report ID.");
}

$reportId = (int) $_GET['id'];

// ✅ Fetch report + BNS data
$stmt = $pdo->prepare("
    SELECT r.*, b.*, u.barangay AS user_barangay 
    FROM reports r
    LEFT JOIN bns_reports b ON b.report_id = r.id
    JOIN users u ON u.id = r.user_id
    WHERE r.id = :id
");
$stmt->execute(['id' => $reportId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Report not found.");
}

// ✅ Check if BNS data exists (FIXED)
$has_bns = !empty($row['report_id']); 

// ✅ Allow editing only if status is Pending or Rejected
$can_edit = in_array($row['status'], ['Pending', 'Rejected']);

// ✅ Barangay logo function
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
        'bolobolo' => 'Bolobolo.png',
        'poblacion' => 'Poblacion.png',
        'kibonbon' => 'Kibonbon.png',
        'sambulawan' => 'Sambulawan.png',
        'calongonan' => 'Calongonan.png',
        'sinaloc' => 'Sinaloc.png',
        'taytay' => 'Taytay.png',
        'ulaliman' => 'Ulaliman.png'
    ];

    $key = strtolower(trim($barangay ?? ''));
    return $map[$key] ?? 'default.png';
}

// ✅ Correct barangay name mapping (FIXED)
$barangay_name = $row['barangay'] ?? $row['user_barangay'] ?? '';
$barangay_logo = getBarangayLogo($barangay_name);

// ✅ Log activity
if (isset($_SESSION['user_id'])) {
    $reportTitle = $row['title'] ?? "Untitled Report";
    $status = $row['status'] ?? "Unknown";
    logActivity(
        $pdo, 
        $_SESSION['user_id'], 
        "Accessed report (ID: $reportId, Title: $reportTitle, Status: $status)"
    );
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>BNS | Edit Report</title>
<link rel="icon" type="image/png" href="../../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* Keep your original CSS here */
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f0f0f0;font-family:"Times New Roman",serif;font-size:12px;line-height:1.4}
.body-layout{display:flex;justify-content:center;padding:20px 0;}
.container{max-width:1000px;width:100%;margin:0 auto;}
.document{background:#fff;width:21cm;min-height:33cm;margin:0 auto 30px auto;padding:2.5cm;box-shadow:0 0 8px rgba(0,0,0,0.15);position:relative;page-break-after:always;}
@media print {body{background:#fff;}.document{box-shadow:none;margin:0;width:100%;min-height:auto;padding:2cm;}}
.header-table{width:100%;border-collapse:collapse;margin-bottom:20px}
.header-table td{border:none;padding:4px 6px;vertical-align:middle}
.header-left{font-weight:bold;font-size:14px}
.header-logos {display:flex;justify-content:flex-start;align-items:center;gap:10px;}
.header-logos img {height:75px;display:block;}
.report-info{text-align:center;margin-bottom:20px;font-size:12px}
table{width:100%;border-collapse:collapse;margin-bottom:15px;table-layout:fixed}
th,td{border:1px solid #000;padding:6px 8px;text-align:left;font-size:12px;vertical-align:top}
th{background:#ddd}
.indent{padding-left:20px}
table td:nth-child(2), table th:nth-child(2){width:180px;text-align:center;}
.number-cell{display:flex;justify-content:space-between;text-align:center;}
.number-cell div{flex:1;padding:4px;border-left:1px solid #000;}
.number-cell div:first-child{border-left:none;}
.page-number{text-align:right;font-size:12px;color:#555;margin-top:10px}
.notice{background:#fff3cd;padding:10px;border:1px solid #ffeeba;margin-bottom:15px}
.reports-label{display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;}
.reports-label .title-input{display:flex;align-items:center;gap:5px;}
.reports-label input[type=text]{padding:4px 6px;font-size:14px;height:28px;width:250px;}
.button-group{display:flex;justify-content:flex-end;margin-top:20px;}
.button-group button{padding:10px 20px;font-size:14px;cursor:pointer;}
.button-save{background:#4CAF50;color:#fff;border:none;}
.button-cancel{text-decoration:none;color:#000;padding:10px 20px;font-size:14px;border:1px solid #ccc;}
.top-right-button{align-self:flex-start;}
</style>
<script>
function copyTitle() {
    const title = document.getElementById('report-title').value;
    document.getElementById('hidden-title').value = title;
}
</script>
</head>
<body>
<div class="layout">


<div class="body-layout">
<div class="container">

<?php if (!$has_bns): ?>
<div class="notice">
<strong>Note:</strong> Report exists (ID: <?= htmlspecialchars($row['id']) ?>) but no BNS data was found.
</div>
<?php endif; ?>

<?php if (!$can_edit): ?>
<div class="notice" style="background:#f8d7da;border-color:#f5c2c7;color:#842029;">
You cannot edit this report because its status is <?= htmlspecialchars($row['status']) ?>.
</div>
<?php endif; ?>

<!-- Single Form for All Pages -->
<form action="save_report.php" method="post" onsubmit="copyTitle()">
    <input type="hidden" name="report_id" value="<?= $reportId ?>">
    <input type="hidden" id="hidden-title" name="title">

    <!-- Editable Title + Cancel Button -->
    <div class="reports-label">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="font-weight:bold; font-size:22px;">Reports</div>
            <div class="title-input">
                <label for="report-title" style="font-size:14px; font-weight:normal;">Title:</label>
                <input type="text" id="report-title" name="title_display" placeholder="Enter report title"
                       value="<?= isset($row['title']) ? htmlspecialchars($row['title']) : '' ?>"
                       <?= !$can_edit ? 'readonly' : '' ?>>
            </div>
        </div>
        <!-- ✅ Fixed Cancel Button -->
        <div>
            <a href="../reports.php" class="button-cancel">Cancel</a>
        </div>
    </div>

    <!-- PAGE 1 -->
    <div class="document">
        <table class="header-table">
        <tr>
            <td class="header-left">BNS Form No. IC<br>Barangay Nutrition Profile</td>
            <td class="header-logos">
                <img src="../../logos/barangays/<?= urlencode($barangay_logo) ?>" alt="Barangay Logo">
                <img src="../../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png">
                <img src="../../logos/fixed/National_Nutrition_Council__NNC_.svg-removebg-preview.png">
                <img src="../../logos/fixed/Bagong-Pilipinas-logo.png">
            </td>
        </tr>
        </table>

        <div class="report-info">
            <h3>BARANGAY SITUATIONAL ANALYSIS (BSA)</h3>
            <strong>Calendar Year:</strong> 
            <input type="number" name="year" value="<?= $has_bns ? htmlspecialchars($row['year']) : '' ?>" style="width:100px;" <?= !$can_edit ? 'readonly' : '' ?>> &nbsp;
            <strong>Barangay:</strong> 
            <input type="text" name="barangay" value="<?= $has_bns ? htmlspecialchars($row['barangay']) : '' ?>" style="width:150px;" <?= !$can_edit ? 'readonly' : '' ?>> &nbsp;
            <strong>City:</strong> EL SALVADOR CITY &nbsp;
            <strong>Province:</strong> MISAMIS ORIENTAL
        </div>

        <table>
        <thead>
            <tr><th>Indicator</th><th>Number / %</th></tr>
        </thead>
        <tbody>
    <tr><td>1. Total Population</td>
        <td><input type="number" name="ind1" value="<?= $has_bns ? htmlspecialchars($row['ind1']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr class="indent"><td>Male</td>
        <td><input type="number" name="ind_male" value="<?= $has_bns ? htmlspecialchars($row['ind_male']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr class="indent"><td>Female</td>
        <td><input type="number" name="ind_female" value="<?= $has_bns ? htmlspecialchars($row['ind_female']) : '' ?>" style="width:100px;"></td>
    </tr>

    <tr><td>2. Total Number of Households</td>
        <td><input type="number" name="ind2" value="<?= $has_bns ? htmlspecialchars($row['ind2']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>3. Total Number of Family</td>
        <td><input type="number" name="ind3" value="<?= $has_bns ? htmlspecialchars($row['ind3']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>4. Total Number of HHs More Than 5 Below Members</td>
        <td><input type="number" name="ind4" value="<?= $has_bns ? htmlspecialchars($row['ind4']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>5. Total Number of HHs more Than 5 Above Members</td>
        <td><input type="number" name="ind5" value="<?= $has_bns ? htmlspecialchars($row['ind5']) : '' ?>" style="width:100px;"></td>
    </tr>



    <tr><td>6. Total Number of Women Who Are:</td><td></td></tr>
    <tr class="indent"><td>a. Pregnant</td>
        <td><input type="number" name="ind6a" value="<?= $has_bns ? htmlspecialchars($row['ind6a']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr class="indent"><td>b. Lactating</td>
        <td><input type="number" name="ind6b" value="<?= $has_bns ? htmlspecialchars($row['ind6b']) : '' ?>" style="width:100px;"></td>
    </tr>

    <tr><td>7. Total Number of Households With Preschool Children 0-59 Months</td>
        <td><input type="number" name="ind7" value="<?= $has_bns ? htmlspecialchars($row['ind7']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>8. Estimate Population of Preschool Children 0-59 Months</td>
        <td><input type="number" name="ind8" value="<?= $has_bns ? htmlspecialchars($row['ind8']) : '' ?>" style="width:100px;"></td>
    </tr>
        <tr><td>9. Actual Number of Preschool Children 0-50 Months Old Measured During OPT Plus</td>
        <td><input type="number" name="ind9" value="<?= $has_bns ? htmlspecialchars($row['ind9']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>a. Percent (%) Measured Coverage (OPT Plus)</td>
        <td><input type="text" name="ind9a" value="<?= $has_bns ? htmlspecialchars($row['ind9a']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>b. Number and Percent (%) of Preschool Children According to Nutritional Status</td>
        <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $nutri = ['Severely underweight','Underweight','Normal weight','Severely wasted','Wasted','Overweight','Obese','Severely stunted','Stunted'];
    for ($i=1;$i<=9;$i++): ?>
    <tr class="indent">
        <td><?= $i.'. '.$nutri[$i-1] ?></td>
        <td class="number-cell">
            <div><input type="number" name="ind9b<?= $i ?>_no" value="<?= $has_bns ? htmlspecialchars($row["ind9b{$i}_no"]) : '' ?>" style="width:70px;"></div>
            <div><input type="text" name="ind9b<?= $i ?>_pct" value="<?= $has_bns ? htmlspecialchars($row["ind9b{$i}_pct"]) : '' ?>" style="width:70px;"></div>
        </td>
    </tr>
    <?php endfor; ?>

    <tr><td>10. Total Number of Infants 0-5 Months Old</td>
        <td><input type="number" name="ind10" value="<?= $has_bns ? htmlspecialchars($row['ind10']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>11. Total Number of Infants 6-11 Months Old</td>
        <td><input type="number" name="ind11" value="<?= $has_bns ? htmlspecialchars($row['ind11']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>12. Total Number of Preschool Children 0-23 Months Old</td>
        <td><input type="number" name="ind12" value="<?= $has_bns ? htmlspecialchars($row['ind12']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>13. Total Number of Preschool Children 12-59 Months Old</td>
        <td><input type="number" name="ind13" value="<?= $has_bns ? htmlspecialchars($row['ind13']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>14. Total Number of Preschool Children 24-59 Months Old</td>
        <td><input type="number" name="ind14" value="<?= $has_bns ? htmlspecialchars($row['ind14']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>15. Total Number of Families With Wasted and Severely Wasted Preschool Children</td>
        <td><input type="number" name="ind15" value="<?= $has_bns ? htmlspecialchars($row['ind15']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>16. Total Number of Families With Stunted and Severely Stunted Preschool Children</td>
        <td><input type="number" name="ind16" value="<?= $has_bns ? htmlspecialchars($row['ind16']) : '' ?>" style="width:100px;"></td>
    </tr>
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
        <td>17. Total Bumber of Educational Institutions(Pub./Priv.)</td>
        <td class="number-cell"><div>Public</div><div>Private</div></td>
    </tr>
    <tr>
        <td>a. Day Care Centers (Public/Private)</td>
        <td class="number-cell">
            <div><input type="number" name="ind17a_public" value="<?= $has_bns ? htmlspecialchars($row['ind17a_public']) : '' ?>" style="width:70px;"></div>
            <div><input type="number" name="ind17a_private" value="<?= $has_bns ? htmlspecialchars($row['ind17a_private']) : '' ?>" style="width:70px;"></div>
        </td>
    </tr>
    <tr>
        <td>b. Elementary Schools (Public/Private)</td>
        <td class="number-cell">
            <div><input type="number" name="ind17b_public" value="<?= $has_bns ? htmlspecialchars($row['ind17b_public']) : '' ?>" style="width:70px;"></div>
            <div><input type="number" name="ind17b_private" value="<?= $has_bns ? htmlspecialchars($row['ind17b_private']) : '' ?>" style="width:70px;"></div>
        </td>
    </tr>

 <tr><td>18. Total Number of Children Enrolled in Kindergarten</td>
        <td><input type="number" name="ind18" value="<?= $has_bns ? htmlspecialchars($row['ind18']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>19. Total Number of School Children (grades 1-6)</td>
        <td><input type="number" name="ind19" value="<?= $has_bns ? htmlspecialchars($row['ind19']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>20. Total Number of School Children Weighed at Start of School Year</td>
        <td><input type="number" name="ind20" value="<?= $has_bns ? htmlspecialchars($row['ind20']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>21. Percentage (%) Coverage of School Children Measured</td>
        <td><input type="text" name="ind21" value="<?= $has_bns ? htmlspecialchars($row['ind21']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr>
      <td>22. Number and Percent (%) of School Children According to Nutritional Status Body Mas Index</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $nutri20 = ['a. Severely Wasted','b. Wasted','c. Severly Stunted','d. Stunted','e. Normal ','f. Overweight','g. Obese'];
    foreach($nutri20 as $key => $label): 
        $letter = chr(97 + $key); // a,b,c...
    ?>
    <tr class="indent">
        <td><?= $label ?></td>
        <td class="number-cell">
            <div><input type="number" name="ind22<?= $letter ?>_no" value="<?= $has_bns ? htmlspecialchars($row["ind22{$letter}_no"]) : '' ?>" style="width:70px;"></div>
            <div><input type="text" name="ind22<?= $letter ?>_pct" value="<?= $has_bns ? htmlspecialchars($row["ind22{$letter}_pct"]) : '' ?>" style="width:70px;"></div>
        </td>
    </tr>
    <?php endforeach; ?>

    <tr><td>23. 0-5 Months Old Children Exclusively Breastfeed</td>
        <td><input type="number" name="ind23" value="<?= $has_bns ? htmlspecialchars($row['ind23']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>24. Households with Severely Wasted School Children</td>
        <td><input type="number" name="ind24" value="<?= $has_bns ? htmlspecialchars($row['ind24']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>25. School Children Dewormed at the Start of the School Year</td>
        <td><input type="number" name="ind25" value="<?= $has_bns ? htmlspecialchars($row['ind25']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>26. Fully Immunized Children(FIC)</td>
        <td><input type="number" name="ind26" value="<?= $has_bns ? htmlspecialchars($row['ind26']) : '' ?>" style="width:100px;"></td>
    </tr>

    <tr>
      <td>27. Households, by Type of Toilet Facility</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $toilet = [      
      'a. Water-sealed toilet',
      'b. Antipolo (Unsanitary Toilet)',
      'c. Open Pit',
      'd. Shared',
      'e. No Toilet'];
    $i='a';
    foreach($toilet as $label): ?>
    <tr class="indent">
      <td><?= $label ?></td>
      <td class="number-cell">
        <div><input type="number" name="ind27<?= $i ?>_no" value="<?= $has_bns ? htmlspecialchars($row["ind27{$i}_no"]) : '' ?>" style="width:70px;"></div>
        <div><input type="text" name="ind27<?= $i ?>_pct" value="<?= $has_bns ? htmlspecialchars($row["ind27{$i}_pct"]) : '' ?>" style="width:70px;"></div>
      </td>
    </tr>
    <?php $i++; endforeach; ?>

    <tr>
      <td>28. Households, by Type of Garbage Disposal:</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $garbage = [      
      'a. Barangay/City Garbage Collection',
      'b. Own Compose Pit',
      'c. Burning',
      'd. Dumping'];
    $i='a';
    foreach($garbage as $label): ?>
    <tr class="indent">
      <td><?= $label ?></td>
      <td class="number-cell">
        <div><input type="number" name="ind28<?= $i ?>_no" value="<?= $has_bns ? htmlspecialchars($row["ind28{$i}_no"]) : '' ?>" style="width:70px;"></div>
        <div><input type="text" name="ind28<?= $i ?>_pct" value="<?= $has_bns ? htmlspecialchars($row["ind28{$i}_pct"]) : '' ?>" style="width:70px;"></div>
      </td>
    </tr>
    <?php $i++; endforeach; ?>
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
      <td>29. Household, by Type of Water Source</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $water = ['a. Pipe Water System(Level III)',
      'b. Spring (Level II)',
      'c. Deep Well With Topstand Communal Source Water System (Level II)',
      'd. Deep Well With Individual Faucet (Level III)',
      'e. Purified Station (Level III)',
      'f. Open Shallow Dug Well (Level I)',
      'g. Artesian Well '];
    $i='a';
    foreach($water as $label): ?>
    <tr class="indent">
      <td><?= $label ?></td>
      <td class="number-cell">
        <div><input type="number" name="ind29<?= $i ?>_no" value="<?= $has_bns ? htmlspecialchars($row["ind29{$i}_no"]) : '' ?>" style="width:70px;"></div>
        <div><input type="text" name="ind29<?= $i ?>_pct" value="<?= $has_bns ? htmlspecialchars($row["ind29{$i}_pct"]) : '' ?>" style="width:70px;"></div>
      </td>
    </tr>
    <?php $i++; endforeach; ?>

    <tr>
      <td>30. Household with</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $household = ['a. Vegetable Garden',
      'b. Livestock Poultry',
      'c. Fishponds',
      'd. Other Specify: No Garden'];
    $i='a';
    foreach($household as $label): ?>
    <tr class="indent">
      <td><?= $label ?></td>
      <td class="number-cell">
        <div><input type="number" name="ind30<?= $i ?>_no" value="<?= $has_bns ? htmlspecialchars($row["ind30{$i}_no"]) : '' ?>" style="width:70px;"></div>
        <div><input type="text" name="ind0<3?= $i ?>_pct" value="<?= $has_bns ? htmlspecialchars($row["ind30{$i}_pct"]) : '' ?>" style="width:70px;"></div>
      </td>
    </tr>
    <?php $i++; endforeach; ?>


    <tr>
      <td>31. Households according to type of dwelling unit:</td>
      <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $d=[ 'a. Concrete',
      'b. Semi Concrete',
      'c. Wooden House',
      'd. Nipa Bamboo House',
      'e. Barong-Barong Makeshift',
      'f. Makeshift']; 
    $i='a'; 
    foreach($d as $label): ?>
    <tr class="indent">
      <td><?= $label ?></td>
      <td class="number-cell">
        <div>
            <input type="number" name="ind31<?= $i ?>_no" 
                   value="<?= $has_bns ? htmlspecialchars($row["ind31{$i}_no"]) : '' ?>" style="width:70px;">
        </div>
        <div>
            <input type="text" name="ind31<?= $i ?>_pct" 
                   value="<?= $has_bns ? htmlspecialchars($row["ind31{$i}_pct"]) : '' ?>" style="width:70px;">
        </div>
      </td>
    </tr>
    <?php $i++; endforeach; ?>

    <tr class="indent">
     <tr>

    <tr><td>32. Total Number of Households Using Iodized Salt</td>
        <td><input type="number" name="ind23" value="<?= $has_bns ? htmlspecialchars($row['ind32']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>33. Total Number of Eateries/Carenderia</td>
        <td><input type="number" name="ind24" value="<?= $has_bns ? htmlspecialchars($row['ind33']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>34. Total Number Sari-Sari Stores Related Iodized Salt</td>
        <td><input type="number" name="ind25" value="<?= $has_bns ? htmlspecialchars($row['ind34']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>35. Total Number of Sari-Sari Stores Related to Cooking Oil</td>
        <td><input type="number" name="ind26" value="<?= $has_bns ? htmlspecialchars($row['ind35']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>36. Total Number of Bakery With Fortified Flour</td>
        <td><input type="number" name="ind26" value="<?= $has_bns ? htmlspecialchars($row['ind36']) : '' ?>" style="width:100px;"></td>
    </tr>


      <td>37. Number of Health and Nutrition Workers:</td>
      <td></td>
    </tr>
    <tr class="indent">
      <td>a. Barangay Nutrition Scholar</td>
      <td>
        <input type="number" name="ind37a" value="<?= $has_bns ? htmlspecialchars($row['ind37a']) : '' ?>" style="width:100px;">
      </td>
    </tr>
    <tr class="indent">
      <td>b. Barangay Health Worker</td>
      <td>
        <input type="number" name="ind37b" value="<?= $has_bns ? htmlspecialchars($row['ind37b']) : '' ?>" style="width:100px;">
      </td>
    </tr>
    <tr>
      <td>38. Total Number of Households Beneficiaries of Pantawid Pamilyang Pilipino Program</td>
      <td>
        <input type="number" name="ind38" value="<?= $has_bns ? htmlspecialchars($row['ind38']) : '' ?>" style="width:100px;">
      </td>
    </tr>
        </tbody>
        </table>
    <!-- Repeat same logic for all input fields: add <?= !$can_edit ? 'readonly' : '' ?> -->

        <?php if ($can_edit): ?>
        <div class="button-group">
            <button type="submit" class="button-save">Update</button> 
        </div>
        <?php endif; ?>
            <div class="page-number">Page 3</div>
    </div>


</form>
</div>
</div>
</body>
</html>
