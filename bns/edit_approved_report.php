<?php
session_start();
require '../db/config.php';

// ✅ Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// ✅ Activity log function
function logActivity($pdo, $user_id, $action) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->execute([$user_id, $action]);
}

// ✅ Validate report ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Missing or invalid report ID in URL");
}

$reportId = intval($_GET['id']);

// ✅ Fetch report + BNS data
$stmt = $pdo->prepare("
    SELECT r.*, b.*, u.barangay AS user_barangay 
    FROM reports r 
    LEFT JOIN bns_reports b ON r.id = b.report_id 
    LEFT JOIN users u ON u.id = r.user_id
    WHERE r.id = :id
");
$stmt->execute(['id' => $reportId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("❌ Report not found");
}

// ✅ Check if BNS data exists properly
$has_bns = !empty($row['report_id']);

// ✅ Function to map barangay name to logo
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
    $file = $map[$key] ?? 'default.png';

    $path = __DIR__ . '/../logos/barangays/' . $file;
    if (!file_exists($path)) {
        $file = 'default.png';
    }

    return $file;
}

// ✅ Determine Barangay Logo
$barangay_name = $row['barangay'] ?? $row['user_barangay'] ?? '';
$barangay_logo = getBarangayLogo($barangay_name);

// ✅ Log activity
if (!empty($_SESSION['user_id'])) {
    $reportTitle = $row['title'] ?? "Untitled Report";
    logActivity($pdo, $_SESSION['user_id'], "Viewed report (ID: $reportId, Title: $reportTitle)");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>BNS | Edit Report</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
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
/* Red highlight for empty fields */
.missing-field {
    background-color: #ffe6e6 !important;
    border: 2px solid red !important;
}
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

<form action="reports.php" method="post" onsubmit="copyTitle()">
    <input type="hidden" name="report_id" value="<?= $reportId ?>">
    <input type="hidden" id="hidden-title" name="title">

    <!-- Editable Title + Cancel Button -->
    <div class="reports-label">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="font-weight:bold; font-size:22px;">Reports</div>
            <div class="title-input">
                <label for="report-title" style="font-size:14px; font-weight:normal;">Title:</label>
                <input type="text" id="report-title" name="title_display" placeholder="Enter report title"
                       value="<?= isset($row['title']) ? htmlspecialchars($row['title']) : '' ?>">
            </div>
        </div>
        <a href="javascript:history.back()" class="button-cancel top-right-button">Cancel</a>
    </div>

    <!-- PAGE 1 -->
    <div class="document">
        <table class="header-table">
        <tr>
            <td class="header-left">BNS Form No. IC<br>Barangay Nutrition Profile</td>
            <td class="header-logos">
                <img src="../logos/barangays/<?= urlencode($barangay_logo) ?>" alt="Barangay Logo">
                <img src="../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png">
                <img src="../logos/fixed/National_Nutrition_Council__NNC_.svg-removebg-preview.png">
                <img src="../logos/fixed/Bagong-Pilipinas-logo.png">
            </td>
        </tr>
        </table>

        <div class="report-info">
            <h3>BARANGAY SITUATIONAL ANALYSIS (BSA)</h3>
            <strong>Calendar Year:</strong> 
            <input type="number" name="year" value="<?= $has_bns ? htmlspecialchars($row['year']) : '' ?>" style="width:100px;"> &nbsp;
            <strong>Barangay:</strong> 
            <input type="text" name="barangay" value="<?= $has_bns ? htmlspecialchars($row['barangay']) : '' ?>" style="width:150px;"> &nbsp;
            <strong>City:</strong> EL SALVADOR CITY &nbsp;
            <strong>Province:</strong> MISAMIS ORIENTAL
        </div>

        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
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
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <colgroup>
                <col style="width: auto;">
                <col style="width: 180px;"> 
            </colgroup>
            <tbody>

    <tr><td>17. Total Number of Educational Institutions (Pub./Priv.)</td>
        <td class="number-cell"><div>Public</div><div>Private</div></td>
    </tr>
    <tr><td>a. Day Care Centers (Public/Private)</td>
        <td class="number-cell">
            <div><input type="number" name="ind17a_public" value="<?= $has_bns ? htmlspecialchars($row['ind17a_public']) : '' ?>" style="width:70px;"></div>
            <div><input type="number" name="ind17a_private" value="<?= $has_bns ? htmlspecialchars($row['ind17a_private']) : '' ?>" style="width:70px;"></div>
        </td>
    </tr>
    <tr><td>b. Elementary Schools (Public/Private)</td>
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
    <tr><td>20. Actual Number of School Children Weighed at Start of School Year</td>
        <td><input type="number" name="ind20" value="<?= $has_bns ? htmlspecialchars($row['ind20']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>21. Percentage (%) Coverage of School Children Measured</td>
        <td><input type="text" name="ind21" value="<?= $has_bns ? htmlspecialchars($row['ind21']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr>
        <td>22. Number and Percent (%) of School Children According to Nutritional Status Body Mass Index</td>
        <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $nutri20 = ['a. Severely Wasted','b. Wasted','c. Severely Stunted','d. Stunted','e. Normal','f. Overweight','g. Obese'];
    foreach($nutri20 as $key => $label): 
        $letter = chr(97 + $key);
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
    <tr><td>26. Fully Immunized Children (FIC)</td>
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
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
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
    $water = ['a. Pipe Water System (Level III)',
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
            <div><input type="text" name="ind30<?= $i ?>_pct" value="<?= $has_bns ? htmlspecialchars($row["ind30{$i}_pct"]) : '' ?>" style="width:70px;"></div>
        </td>
    </tr>
    <?php $i++; endforeach; ?>


    <tr>
        <td>31. Households according to type of dwelling unit:</td>
        <td class="number-cell"><div>No.</div><div>%</div></td>
    </tr>
    <?php 
    $d=['a. Concrete',
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
            <div><input type="number" name="ind31<?= $i ?>_no" value="<?= $has_bns ? htmlspecialchars($row["ind31{$i}_no"]) : '' ?>" style="width:70px;"></div>
            <div><input type="text" name="ind31<?= $i ?>_pct" value="<?= $has_bns ? htmlspecialchars($row["ind31{$i}_pct"]) : '' ?>" style="width:70px;"></div>
        </td>
    </tr>
    <?php $i++; endforeach; ?>

    <tr><td>32. Total Number of Households Using Iodized Salt</td>
        <td><input type="number" name="ind32" value="<?= $has_bns ? htmlspecialchars($row['ind32']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>33. Total Number of Eateries/Carenderia</td>
        <td><input type="number" name="ind33" value="<?= $has_bns ? htmlspecialchars($row['ind33']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>34. Total Number Sari-Sari Stores Related Iodized Salt</td>
        <td><input type="number" name="ind34" value="<?= $has_bns ? htmlspecialchars($row['ind34']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>35. Total Number of Sari-Sari Stores Related to Cooking Oil</td>
        <td><input type="number" name="ind35" value="<?= $has_bns ? htmlspecialchars($row['ind35']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr><td>36. Total Number of Bakery With Fortified Flour</td>
        <td><input type="number" name="ind36" value="<?= $has_bns ? htmlspecialchars($row['ind36']) : '' ?>" style="width:100px;"></td>
    </tr>

    <tr><td>37. Number of Health and Nutrition Workers:</td><td></td></tr>
    <tr class="indent">
        <td>a. Barangay Nutrition Scholar</td>
        <td><input type="number" name="ind37a" value="<?= $has_bns ? htmlspecialchars($row['ind37a']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr class="indent">
        <td>b. Barangay Health Worker</td>
        <td><input type="number" name="ind37b" value="<?= $has_bns ? htmlspecialchars($row['ind37b']) : '' ?>" style="width:100px;"></td>
    </tr>
    <tr>
        <td>38. Total Number of Households Beneficiaries of Pantawid Pamilyang Pilipino Program</td>
        <td><input type="number" name="ind38" value="<?= $has_bns ? htmlspecialchars($row['ind38']) : '' ?>" style="width:100px;"></td>
    </tr>
        </tbody>
        </table>

        <div class="button-group">
            <button type="submit" class="button-save">Update</button>
        </div>

        <div class="page-number">Page 3</div>
    </div>

</form>
</div>
</div>

<script>
// ============================================
// EDIT REPORT FORMULAS - MATCHING ADD REPORT PAGE
// ============================================

// 1️⃣ Total Population (ind1 = ind_male + ind_female)
const indMale = document.querySelector("input[name='ind_male']");
const indFemale = document.querySelector("input[name='ind_female']");
const ind1 = document.querySelector("input[name='ind1']");

function computeTotalPopulation() {
    const male = parseFloat(indMale?.value) || 0;
    const female = parseFloat(indFemale?.value) || 0;
    if (ind1) ind1.value = male + female;
}

if (indMale) indMale.addEventListener('input', computeTotalPopulation);
if (indFemale) indFemale.addEventListener('input', computeTotalPopulation);

// 2️⃣ Preschool Children Measured Coverage (ind9a = (ind9 / ind8) * 100)
const ind8 = document.querySelector("input[name='ind8']");
const ind9 = document.querySelector("input[name='ind9']");
const ind9a = document.querySelector("input[name='ind9a']");
if (ind9a) ind9a.readOnly = true;

function computeMeasuredCoverage() {
    const total = parseFloat(ind8?.value) || 0;
    const measured = parseFloat(ind9?.value) || 0;
    if (ind9a) ind9a.value = total > 0 ? ((measured / total) * 100).toFixed(2) : 0;
}
if (ind8) ind8.addEventListener('input', computeMeasuredCoverage);
if (ind9) ind9.addEventListener('input', computeMeasuredCoverage);

// 3️⃣ Preschool Nutritional Status Percentages (ind9bX_pct = (ind9bX_no / ind9) * 100)
const ind9Total = ind9;
for (let i = 1; i <= 9; i++) {
    const noInput = document.querySelector(`input[name='ind9b${i}_no']`);
    const pctInput = document.querySelector(`input[name='ind9b${i}_pct']`);
    if (pctInput) pctInput.readOnly = true;
    
    if (noInput && pctInput && ind9Total) {
        const updatePercentage = () => {
            const total = parseFloat(ind9Total.value) || 0;
            const val = parseFloat(noInput.value) || 0;
            pctInput.value = total > 0 ? ((val / total) * 100).toFixed(2) : 0;
        };
        noInput.addEventListener('input', updatePercentage);
        if (ind9Total) ind9Total.addEventListener('input', updatePercentage);
        updatePercentage(); // Initial calculation
    }
}

// 4️⃣ School Children Coverage (ind21 = (ind20 / (ind18 + ind19)) * 100)
const ind18 = document.querySelector("input[name='ind18']");
const ind19 = document.querySelector("input[name='ind19']");
const ind20 = document.querySelector("input[name='ind20']");
const ind21 = document.querySelector("input[name='ind21']");
if (ind21) ind21.readOnly = true;

function computeInd21() {
    const val18 = parseFloat(ind18?.value) || 0;
    const val19 = parseFloat(ind19?.value) || 0;
    const val20 = parseFloat(ind20?.value) || 0;
    const total = val18 + val19;
    if (ind21) ind21.value = total > 0 ? ((val20 / total) * 100).toFixed(2) : 0;
}
if (ind18) ind18.addEventListener('input', computeInd21);
if (ind19) ind19.addEventListener('input', computeInd21);
if (ind20) ind20.addEventListener('input', computeInd21);

// 5️⃣ School Children Nutritional Status Percentages (ind22X_pct = (ind22X_no / ind20) * 100)
for (let i = 0; i <= 6; i++) {
    const letter = String.fromCharCode(97 + i);
    const noInput = document.querySelector(`input[name='ind22${letter}_no']`);
    const pctInput = document.querySelector(`input[name='ind22${letter}_pct']`);
    if (pctInput) pctInput.readOnly = true;
    
    if (noInput && pctInput && ind20) {
        const updatePercentage = () => {
            const total = parseFloat(ind20.value) || 0;
            const val = parseFloat(noInput.value) || 0;
            pctInput.value = total > 0 ? ((val / total) * 100).toFixed(2) : 0;
        };
        noInput.addEventListener('input', updatePercentage);
        ind20.addEventListener('input', updatePercentage);
        updatePercentage(); // Initial calculation
    }
}

// 6️⃣ Household Sections using ind2 as denominator
const ind2Total = document.querySelector("input[name='ind2']");

function setupPercentCalculation(sectionPrefix, count, totalInput) {
    for (let i = 0; i < count; i++) {
        const letter = String.fromCharCode(97 + i);
        const noInput = document.querySelector(`input[name='${sectionPrefix}${letter}_no']`);
        const pctInput = document.querySelector(`input[name='${sectionPrefix}${letter}_pct']`);
        if (pctInput) pctInput.readOnly = true;
        
        if (noInput && pctInput && totalInput) {
            const updatePercentage = () => {
                const total = parseFloat(totalInput.value) || 0;
                const val = parseFloat(noInput.value) || 0;
                pctInput.value = total > 0 ? ((val / total) * 100).toFixed(2) : 0;
            };
            noInput.addEventListener('input', updatePercentage);
            totalInput.addEventListener('input', updatePercentage);
            updatePercentage(); // Initial calculation
        }
    }
}

if (ind2Total) {
    setupPercentCalculation('ind27', 5, ind2Total);
    setupPercentCalculation('ind28', 4, ind2Total);
    setupPercentCalculation('ind29', 7, ind2Total);
    setupPercentCalculation('ind30', 4, ind2Total);
    setupPercentCalculation('ind31', 6, ind2Total);
}

// 7️⃣ Make all calculated fields readonly (final safety)
const readonlyFields = [
    'ind1', 'ind9a', 'ind21',
    ...Array.from({length: 9}, (_, i) => `ind9b${i+1}_pct`),
    ...Array.from({length: 7}, (_, i) => `ind22${String.fromCharCode(97+i)}_pct`),
    ...Array.from({length: 5}, (_, i) => `ind27${String.fromCharCode(97+i)}_pct`),
    ...Array.from({length: 4}, (_, i) => `ind28${String.fromCharCode(97+i)}_pct`),
    ...Array.from({length: 7}, (_, i) => `ind29${String.fromCharCode(97+i)}_pct`),
    ...Array.from({length: 4}, (_, i) => `ind30${String.fromCharCode(97+i)}_pct`),
    ...Array.from({length: 6}, (_, i) => `ind31${String.fromCharCode(97+i)}_pct`)
];

readonlyFields.forEach(name => {
    const el = document.querySelector(`input[name='${name}']`);
    if (el) el.readOnly = true;
});

// 8️⃣ Number input validation (blocks letters)
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        const isNumber = /[0-9]/.test(char);
        const isDecimal = char === '.' && !this.value.includes('.');
        if (!isNumber && !isDecimal) e.preventDefault();
    });
    input.addEventListener('paste', function(e) {
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        if (!/^\d*\.?\d*$/.test(paste)) e.preventDefault();
    });
});

// 9️⃣ VALIDATION ON SUBMIT - Turn red and scroll to missing fields
const form = document.querySelector('form');
const hiddenTitle = document.getElementById('hidden-title');
const titleInput = document.getElementById('report-title');

function removeAllRedHighlights() {
    const allInputs = document.querySelectorAll('form input');
    allInputs.forEach(input => {
        input.classList.remove('missing-field');
        input.style.backgroundColor = '';
        input.style.border = '';
    });
}

function highlightEmptyFields() {
    const missingFields = [];
    // Get all editable inputs (not readonly and not hidden)
    const allInputs = document.querySelectorAll('form input:not([readonly])');
    
    allInputs.forEach(input => {
        // Skip hidden inputs
        if (input.type === 'hidden') return;
        // Check if empty
        if (input.value.trim() === '') {
            missingFields.push(input);
            input.classList.add('missing-field');
        }
    });
    
    return missingFields;
}

function scrollToFirstMissingField(fields) {
    if (fields.length === 0) return;
    fields[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    // Add temporary glow effect
    fields[0].style.transition = 'all 0.2s ease';
    fields[0].style.boxShadow = '0 0 0 3px rgba(255,0,0,0.4)';
    setTimeout(() => {
        fields[0].style.boxShadow = '';
    }, 1200);
}

// Form submit handler - button is ALWAYS enabled
if (form) {
    form.addEventListener('submit', function(e) {
        // Remove previous red highlights
        removeAllRedHighlights();
        
        // Check for empty fields
        const missingFields = highlightEmptyFields();
        
        // If there are missing fields
        if (missingFields.length > 0) {
            e.preventDefault();  // Stop form submission
            scrollToFirstMissingField(missingFields);  // Scroll to first missing field
            return false;
        }
        
        // All fields are filled - set title and submit
        if (hiddenTitle && titleInput) {
            hiddenTitle.value = titleInput.value.trim();
        }
        return true;
    });
}

// Optional: Remove red when user types (clean UX, but does NOT add red on blur)
document.querySelectorAll('form input').forEach(input => {
    if (!input.readOnly && input.type !== 'hidden') {
        input.addEventListener('input', function() {
            this.classList.remove('missing-field');
            this.style.backgroundColor = '';
            this.style.border = '';
        });
    }
});

// 🔟 Run initial calculations on page load
window.addEventListener('DOMContentLoaded', function() {
    computeTotalPopulation();
    computeMeasuredCoverage();
    computeInd21();
    
    // Trigger all percentage calculations
    if (ind2Total) {
        const event = new Event('input');
        ind2Total.dispatchEvent(event);
    }
});
</script>
</body>
</html>