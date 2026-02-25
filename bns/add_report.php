  <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
  require '../db/config.php';


  // ✅ Require login
  if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'BNS') {
    header("Location: ../login.php");
    exit();
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

    return $logos[$barangay] ?? 'default.png';
}

  $barangay = $_SESSION['barangay']; // auto-fill from session
  $year = date('Y'); // default year
  $user_id = $_SESSION['user_id'];

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
      $title = trim($_POST['title']); // report title from form
      $year = $_POST['year'] ?? $year; // optional year from form
      $barangay = $_POST['barangay'] ?? $barangay;

          if ($title === '') {
        die("Error: Title cannot be empty!");
    }
      try {
          // Start transaction to ensure both inserts succeed
          $pdo->beginTransaction();

          // 1️⃣ Insert into reports table
          $stmt = $pdo->prepare("
    INSERT INTO reports (user_id, report_time, report_date, is_submitted)
    VALUES (:user_id, :report_time, :report_date, 1)
");
          $stmt->execute([
              ':user_id' => $user_id,
              ':report_time' => date('H:i:s'),
              ':report_date' => date('Y-m-d')
          ]);
          // Get the inserted report ID
          $report_id = $pdo->lastInsertId();

          // 2️⃣ Prepare data for bns_reports
            $fields = [
            'report_id', 'barangay', 'year', 'title',          
            'ind1', 'ind_male', 'ind_female',
            'ind2', 'ind3', 'ind4', 'ind5', 
            'ind6a', 'ind6b',
            'ind7', 'ind8', 'ind9',   
            'ind9a',
            'ind9b1_no','ind9b1_pct','ind9b2_no','ind9b2_pct','ind9b3_no','ind9b3_pct',
            'ind9b4_no','ind9b4_pct','ind9b5_no','ind9b5_pct','ind9b6_no','ind9b6_pct',
            'ind9b7_no','ind9b7_pct','ind9b8_no','ind9b8_pct','ind9b9_no','ind9b9_pct',
            'ind10','ind11','ind12','ind13','ind14','ind15','ind16',
            'ind17a_public','ind17a_private','ind17b_public','ind17b_private',
            'ind18','ind19','ind20','ind21',
            'ind22a_no','ind22a_pct','ind22b_no','ind22b_pct','ind22c_no','ind22c_pct',
            'ind22d_no','ind22d_pct','ind22e_no','ind22e_pct', 'ind22f_no','ind22f_pct', 'ind22g_no','ind22g_pct',            
            'ind23','ind24','ind25','ind26',
            'ind27a_no','ind27a_pct','ind27b_no','ind27b_pct','ind27c_no','ind27c_pct',
            'ind27d_no','ind27d_pct','ind27e_no','ind27e_pct',
            'ind28a_no','ind28a_pct','ind28b_no','ind28b_pct','ind28c_no','ind28c_pct',
            'ind28d_no','ind28d_pct',
            'ind29a_no','ind29a_pct','ind29b_no','ind29b_pct','ind29c_no','ind29c_pct',
            'ind29d_no','ind29d_pct','ind29e_no','ind29e_pct','ind29f_no','ind29f_pct',
            'ind29g_no','ind29g_pct',
            'ind30a_no','ind30a_pct','ind30b_no','ind30b_pct','ind30c_no','ind30c_pct',
            'ind30d_no','ind30d_pct',
            'ind31a_no','ind31a_pct','ind31b_no','ind31b_pct','ind31c_no','ind31c_pct',
            'ind31d_no','ind31d_pct','ind31e_no','ind31e_pct','ind31f_no','ind31f_pct',
            'ind32',       
            'ind33',
            'ind34',
            'ind35',
            'ind36',
            'ind37a','ind37b','ind38'
        ];

          $placeholders = [];
          $params = [];

          foreach ($fields as $f) {
              $placeholders[] = ':' . $f;

              // report_id is from $report_id, others from POST
              if ($f === 'report_id') {
                  $params[':' . $f] = $report_id;
} elseif ($f === 'barangay') {
    $params[':' . $f] = $barangay;
} elseif ($f === 'year') {
    $params[':' . $f] = $year;
} elseif ($f === 'title') {
    $params[':' . $f] = $title;
}
 else {
                  $params[':' . $f] = $_POST[$f] ?? null;
              }
          }

          // Prepare and execute insert
          $sql = "INSERT INTO bns_reports (" . implode(',', $fields) . ") 
                  VALUES (" . implode(',', $placeholders) . ")";
          $stmt2 = $pdo->prepare($sql);
          $stmt2->execute($params);

                  // 3️⃣ Log activity
        $logStmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, action, details, created_at)
            VALUES (:user_id, :action, :details, NOW())
        ");
        $logStmt->execute([
            ':user_id' => $user_id,
            ':action' => 'Report Added',
            ':details' => "Report ID: $report_id, Created for Barangay: $barangay, Year: $year, Title: '$title'"
        ]);

           // NOTIFICATION
         $cnoStmt = $pdo->query("SELECT id FROM users WHERE user_type = 'CNO'");
$cnoUsers = $cnoStmt->fetchAll(PDO::FETCH_COLUMN);

$notifMessage = "A new report has been submitted by {$barangay}.";

$notifStmt = $pdo->prepare("
    INSERT INTO notifications (user_id, sender_id, message, date)
    VALUES (:user_id, :sender_id, :message, NOW())
");

foreach ($cnoUsers as $cnoId) {
    $notifStmt->execute([
        ':user_id' => $cnoId,
        ':sender_id' => $user_id,
        ':message' => $notifMessage
    ]);
}
          // Commit transaction
          $pdo->commit();

          // ✅ Redirect with success message
          $_SESSION['success'] = "Report and barangay data submitted successfully.";
          header("Location: home.php");
          exit();

      } catch (PDOException $e) {
          $pdo->rollBack();
          die("Error: " . $e->getMessage());
      }
  }
  ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
  <meta charset="UTF-8">
  <title>BNS | Add Report</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
 <link rel="stylesheet" href="css/add_report.css">
  </head>
  <body>
    <div class="layout">
      <div class="body-layout">
        <main class="content">
          <a href="reports.php" class="btn back-btn">Back</a>
<div class="reports-label" style="display:flex; align-items:center; gap:10px;">
    <div style="font-weight:bold; font-size:22px;">Reports</div>
    <div style="display:flex; align-items:center; gap:5px;">
        <label for="report-title" style="font-size:14px; font-weight:normal;">Title:</label> 
        <input type="text" id="report-title" placeholder="Enter report title"
              style="padding:4px 6px; font-size:14px; height:28px; width:250px;">        
    </div>
    <a style="font-size:14px; font-weight:normal;">Import CSV:</a> <input type="file" id="csvFile" accept=".csv">
</div>
          <?php if (!empty($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
          <?php endif; ?>
    <div class="form-wrapper">
<form method="post">
    <input type="hidden" id="hidden-title" name="title">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
      <!-- Left: Titles -->
      <div style="text-align:left;">
        <h2 style="margin:0; font-size:15px;">BNS Form No. IC</h2>
        <h3 style="margin:0; font-size:15px;">Barangay Nutrition Profile</h3>
      </div>
      <!-- Right: Logos -->
      <div style="display:flex; align-items:center; gap:15px;">
        <!-- Dynamic Barangay Logo -->
        <img src="../logos/barangays/<?= getBarangayLogo($barangay) ?>" 
     alt="<?= htmlspecialchars($barangay) ?> Logo" 
     style="height:100px;">
        <!-- Fixed Logos -->
        <img src="../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.png" alt="Logo 1" style="height:100px;">
        <img src="../logos/fixed/National_Nutrition_Council__NNC_.svg-removebg-preview.png" alt="Logo 2" style="height:100px;">
        <img src="../logos/fixed/Bagong-Pilipinas-logo.png" alt="Logo 3" style="height:100px;">
      </div>
    </div>
    <h3 style="text-align:center; margin:0 0 15px 0;">BARANGAY SITUATIONAL ANALYSIS (BSA)</h3>
    <h3 style="text-align:center; margin:0;"  ><strong>Calendar Year</strong> 
      <input type="number" name="year" id="yearInput" style="width:80px;"> &nbsp;&nbsp;</h3>
    <div style="text-align:center; margin-bottom:20px; font-size:14px;">
      <strong>Barangay:</strong> 
      <input type="text" name="barangay" value="<?= htmlspecialchars($barangay) ?>" readonly style="width:200px;"> &nbsp;&nbsp;
      <strong>City/Municipality:</strong> EL SALVADOR CITY &nbsp;&nbsp;
      <strong>Province:</strong> MISAMIS ORIENTAL
    </div>
          <!-- Full Indicators Form -->
           <div class="form-section">
            <table>
              <tr><th>Indicator</th><th>Number</th></tr>
              <tr><td>1. Total Population</td><td><input type="number" id="total" name="ind1" required min="0"></td></tr>
              <tr><td class="indent">Male</td><td><input type="number" id="male" name="ind_male" required min="0"></td></tr>
              <tr><td class="indent">Female</td><td><input type="number" id="female" name="ind_female" required min="0"></td></tr>
              <tr><td>2. Total Number of Households</td><td><input type="number" name="ind2" required></td></tr>
              <tr><td>3. Total Number of Family</td><td><input type="number" name="ind3" required></td></tr>
              <tr><td>4. Total Number of HHs More Than 5 Below Members</td><td><input type="number" name="ind4" required></td></tr>
              <tr><td>5. Total Number of HHs more Than 5 Above Members</td><td><input type="number" name="ind5" required></td></tr>
              <tr><td>6. Total Number of Women Who Are:</td><td></td></tr>
              <tr><td class="indent">a. Pregnant</td><td><input type="number" name="ind6a" required></td></tr>
              <tr><td class="indent">b. Lactating</td><td><input type="number" name="ind6b" required></td></tr>
              <tr><td>7. Total Number of Households With Preschool Children 0-59 Months</td><td><input type="number" name="ind7" required></td></tr>
              <tr><td>8. Estimate Population of Preschool Children 0-59 Months</td><td><input type="number" name="ind8" required></td></tr>
              <tr><td>9. Actual Number of Preschool Children 0-50 Months Old Measured During OPT Plus</td><td><input type="number" name="ind9" required></td></tr>
              <tr><td>a. Percent (%) Measured Coverage (OPT Plus)</td><td><input type="number" step="0.01" name="ind9a" required></td></tr>
  <tr>
    <td>b. Number and Percent (%) of Preschool Children According to Nutritional Status</td>
    <td style="display:flex; gap:10px; font-weight:bold;">
      <span style="flex:1; text-align:center;">No.</span>
      <span style="flex:1; text-align:center;">%</span>
    </td>
  </tr>
  <?php
  $nutri = [
      '1. Severely Underweight',
      '2. Underweight',
      '3. Normal Weight',
      '4. Severely Wasted',
      '5. Wasted',
      '6. Overweight',
      '7. Obese',
      '8. Severely Stunted',
      '9. Stunted'
  ];
  foreach($nutri as $i => $name) {
      $n = $i + 1;
      echo "<tr>
              <td style='width:60%;'>$name</td>
              <td style='display:flex; gap:10px;'>
                  <input type='number' name='ind9b{$n}_no' placeholder='No' style='flex:1;' required>
                  <input type='number' step='0.01' name='ind9b{$n}_pct' placeholder='%' style='flex:1;' required>
              </td>
            </tr>";
  }
  ?>
  <!-- All other table rows remain unchanged -->
              <tr><td>10. Total Number of Infants 0-5 Months Old</td><td><input type="number" name="ind10" required></td></tr>
              <tr><td>11. Total Number of Infants 6-11 Months Old</td><td><input type="number" name="ind11" required></td></tr>
              <tr><td>12. Total Number of Preschool Children 0-23 Months Old</td><td><input type="number" name="ind12" required></td></tr>
              <tr><td>13. Total Number of Preschool Children 12-59 Months Old</td><td><input type="number" name="ind13" required></td></tr>
              <tr><td>14. Total Number of Preschool Children 24-59 Months Old</td><td><input type="number" name="ind14" required></td></tr>
              <tr><td>15. Total Number of Families With Wasted and Severely Wasted Preschool Children</td><td><input type="number" name="ind15" required></td></tr>
              <tr><td>16. Total Number of Families With Stunted and Severely Stunted Preschool Children</td><td><input type="number" name="ind16" required></td></tr>
  <tr>
    <td>17. Total Number of Educational Institutions(Pub./Priv.)</td>
    <td style="display:flex; gap:10px; font-weight:bold;">
      <span style="flex:1; text-align:center;">Public</span>
      <span style="flex:1; text-align:center;">Private</span>
    </td>
  <?php
  $edu = [
      'a. Number of Day Care Centers',
      'b. Number of Elementary Schools'
  ];
  foreach($edu as $i => $name) {
      $n = chr(97 + $i); // a, b
      echo "<tr>
              <td style='width:60%;'>$name</td>
              <td style='display:flex; gap:10px;'>
                  <input type='number' name='ind17{$n}_public' placeholder='Public' style='flex:1; text-align:center;' required>
                  <input type='number' name='ind17{$n}_private' placeholder='Private' style='flex:1; text-align:center;' required>
              </td>
            </tr>";
  }
  ?>
              <tr><td>18. Total Number of Children Enrolled in Kindergarten</td><td><input type="number" name="ind18" required></td></tr>
              <tr><td>19. Total Number of School Children (grades 1-6)</td><td><input type="number" name="ind19" required></td></tr>
              <tr><td>20. Total Number of School Children Weighed at Start of School Year</td><td><input type="number" name="ind20" required></td></tr>
              <tr><td>21. Percentage (%) Coverage of School Children Measured</td><td><input type="number" step="0.01" name="ind21" required></td></tr>
  <tr>
    <td>22. Number and Percent (%) of School Children According to Nutritional Status Body Mas Index</td>
    <td style="display:flex; gap:10px; font-weight:bold;">
      <span style="flex:1; text-align:center;">No.</span>
      <span style="flex:1; text-align:center;">%</span>
    </td>
  </tr>
  <?php
  $school = [
      'a. Severely Wasted',
      'b. Wasted',
      'c. Severely Stunted',
      'd. Stunted',
      'e. Normal',
      'f. Overweight',
      'g. Obese'
  ];
  foreach($school as $i => $name) {
      $n = chr(97 + $i); 
      echo "<tr>
              <td style='width:60%;'>$name</td>
              <td style='display:flex; gap:10px;'>
                  <input type='number' name='ind22{$n}_no' placeholder='No' style='flex:1;' required>
                  <input type='number' step='0.01' name='ind22{$n}_pct' placeholder='%' style='flex:1;' required>
              </td>
            </tr>";
  }
  ?>
              <tr><td>23. 0-5 Months Old Children Exclusively Breastfeed</td><td><input type="number" name="ind23" required></td></tr>
              <tr><td>24. Households with Severely Wasted School Children</td><td><input type="number" name="ind24" required></td></tr>
              <tr><td>25. School Children Dewormed at the Start of the School Year</td><td><input type="number" name="ind25" required></td></tr>
              <tr><td>26. Fully Immunized Children(FIC)</td><td><input type="number" name="ind26" required></td></tr>
  <tr>
    <td>27. Households, by Type of Toilet Facility</td>
    <td style="display:flex; gap:10px; font-weight:bold;">
      <span style="flex:1; text-align:center;">No.</span>
      <span style="flex:1; text-align:center;">%</span>
    </td>
  </tr><?php
  $toilet = [
      'a. Water-sealed toilet',
      'b. Antipolo (Unsanitary Toilet)',
      'c. Open Pit',
      'd. Shared',
      'e. No Toilet'
  ];
  foreach($toilet as $i => $name) {
      $n = chr(97 + $i); 
      echo "<tr>
              <td style='width:60%;'>$name</td>
              <td style='display:flex; gap:10px;'>
                  <input type='number' name='ind27{$n}_no' placeholder='No' style='flex:1;' required>
                  <input type='number' step='0.01' name='ind27{$n}_pct' placeholder='%' style='flex:1;' required>
              </td>
            </tr>";
  }
  ?>
  <tr>
    <td>28. Households, by Type of Garbage Disposal</td>
    <td style="display:flex; gap:10px; font-weight:bold;">
      <span style="flex:1; text-align:center;">No.</span>
      <span style="flex:1; text-align:center;">%</span>
    </td>
  </tr>
  <?php
  $garbage_types = [
      'a. Barangay/City Garbage Collection',
      'b. Own Compose Pit',
      'c. Burning',
      'd. Dumping'
  ];
  foreach($garbage_types as $i => $name) {
      $n = chr(97 + $i);
      echo "<tr>
              <td style='width:60%;'>$name</td>
              <td style='display:flex; gap:10px;'>
                  <input type='number' name='ind28{$n}_no' placeholder='No' style='flex:1;' required>
                  <input type='number' step='0.01' name='ind28{$n}_pct' placeholder='%' style='flex:1;' required>
              </td>
            </tr>";
  }
  ?>
  <tr>
    <td>29. Household, by Type of Water Source</td>
    <td style="display:flex; gap:10px; font-weight:bold;">
      <span style="flex:1; text-align:center;">No.</span>
      <span style="flex:1; text-align:center;">%</span>
    </td>
  </tr>
              <?php
  $water_sources = [
      'a. Pipe Water System(Level III)',
      'b. Spring (Level II)',
      'c. Deep Well With Topstand Communal Source Water System (Level II)',
      'd. Deep Well With Individual Faucet (Level III)',
      'e. Purified Station (Level III)',
      'f. Open Shallow Dug Well (Level I)',
      'g. Artesian Well '
  ];
  foreach($water_sources as $i => $name) {
      $n = chr(97 + $i); 
      echo "<tr>
              <td style='width:60%;'>$name</td>
              <td style='display:flex; gap:10px;'>
                  <input type='number' name='ind29{$n}_no' placeholder='No' style='flex:1;' required>
                  <input type='number' step='0.01' name='ind29{$n}_pct' placeholder='%' style='flex:1;' required>
              </td>
            </tr>";
  }
  ?>
  <tr>
    <td>30. Household with</td>
    <td style="display:flex; gap:10px; font-weight:bold;">
      <span style="flex:1; text-align:center;">No.</span>
      <span style="flex:1; text-align:center;">%</span>
    </td>
  </tr>
  <?php
  $household_items = [
      'a. Vegetable Garden',
      'b. Livestock Poultry',
      'c. Fishponds',
      'd. Other Specify: No Garden'
  ];
  foreach($household_items as $i => $name) {
      $n = chr(97 + $i);
      echo "<tr>
              <td style='width:60%;'>$name</td>
              <td style='display:flex; gap:10px;'>
                  <input type='number' name='ind30{$n}_no' placeholder='No' style='flex:1;' required>
                  <input type='number' step='0.01' name='ind30{$n}_pct' placeholder='%' style='flex:1;' required>
              </td>
            </tr>";
  }
  ?>
  <tr>
    <td>31. Households according to type of dwelling unit:</td>
    <td style="display:flex; gap:10px; font-weight:bold;">
      <span style="flex:1; text-align:center;">No.</span>
      <span style="flex:1; text-align:center;">%</span>
    </td>
  <?php
  $dwelling_types = [
      'a. Concrete',
      'b. Semi Concrete',
      'c. Wooden House',
      'd. Nipa Bamboo House',
      'e. Barong-Barong Makeshift',
      'f. Makeshift'
  ];
  foreach($dwelling_types as $i => $name) {
      $n = chr(97 + $i);
      echo "<tr>
              <td style='width:60%;'>$name</td>
              <td style='display:flex; gap:10px;'>
                  <input type='number' name='ind31{$n}_no' placeholder='No' style='flex:1;' required>
                  <input type='number' step='0.01' name='ind31{$n}_pct' placeholder='%' style='flex:1;' required>
              </td>
            </tr>";
  }
  ?>

              <tr><td>32. Total Number of Households Using Iodized Salt</td><td><input type="number" name="ind32" required></td></tr>
              <tr><td>33. Total Number of Eateries/Carenderia</td><td><input type="number" name="ind33" required></td></tr>
              <tr><td>34. Total Number of Sari-Sari Stores Related to Iodized Salt</td><td><input type="number" name="ind34" required></td></tr>
              <tr><td>35. Total Number of Sari-Sari Stores Related to Cooking Oil</td><td><input type="number" name="ind35" required></td></tr>
              <tr><td>36. Total Number of Bakery With Fortified Flour</td><td><input type="number" name="ind36" required></td></tr>
  <tr><td>37. Number of Health and Nutrition Workers:</td><td></td></tr>
  <?php
  $health_workers = [
      'a. Barangay Nutrition Scholar',
      'b. Barangay Health Worker'
  ];
  foreach($health_workers as $i => $name) {
      $n = chr(97 + $i);
      echo "<tr>
              <td style='width:60%;'>$name</td>
              <td style='display:flex; gap:10px;'>
                  <input type='number' name='ind37{$n}' placeholder='No' style='flex:1;' required>
              </td>
            </tr>";
  }
  ?>
  <tr><td>38. Total Number of Households Beneficiaries of Pantawid Pamilyang Pilipino Program</td><td><input type="number" name="ind38" required></td></tr>
            </table>
          </div>
      <!-- all your form fields here (unchanged) -->
      <div class="form-bottom">
        <button type="submit" class="submit-btn" disabled>Submit</button>
      </div>      
    </form>
  </div>
        </main>
      </div>
    </div>

<script src="js/add_report.js"></script>

<!-- Year of the Calendar -->
<script>
  document.getElementById("yearInput").value = new Date().getFullYear();
</script>
      
</body>
</html>