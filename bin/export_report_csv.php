<?php
ob_start();
error_reporting(E_ERROR | E_PARSE);
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';

// ---------- Helper ----------
function val(array $a, string $k, string $fmt = 'int'): string {
    if (!isset($a[$k]) || $a[$k] === '' || $a[$k] === null) return '—';
    if ($fmt === 'int')  return (string)(int)$a[$k];
    if ($fmt === 'pct')  return number_format((float)$a[$k], 2) . '%';
    if ($fmt === 'dec2') return number_format((float)$a[$k], 2);
    return htmlspecialchars((string)$a[$k]);
}

// ---------- Fetch the report ----------
$report_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($report_id <= 0) die("Report not found!");

$sql = "SELECT * FROM bns_reports WHERE report_id = :report_id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['report_id'=>$report_id]);
$totals = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$totals) die("Report not found!");

// ---------- Prepare CSV Data ----------
$data = [];

// Add header row
$data[] = ['Indicator','No.','%'];

// Indicators 1–5
$data[] = ['1. Total Population', val($totals,'ind1'), ''];
$data[] = ['Male', val($totals,'ind_male'), ''];
$data[] = ['Female', val($totals,'ind_female'), ''];
$data[] = ['2. Number of Households', val($totals,'ind2'), ''];
$data[] = ['3. Total Number of Family', val($totals,'ind3'), ''];
$data[] = ['4. Total Number of HHs More Than 5 Below Members', val($totals,'ind4'), ''];
$data[] = ['5. Total Number of HHs More Than 5 Above Members', val($totals,'ind5'), ''];

// Indicator 6
$data[] = ['6. Total number of women who are:', '', ''];
$data[] = ['a. Total Number of Women Who Are Pregnant', val($totals,'ind6a'), ''];
$data[] = ['b. Total Number of Women Who Are Lactating', val($totals,'ind6b'), ''];

// Indicators 7–9
$data[] = ['7. Total Number of Households with Preschool Children (0–59 mos.)', val($totals,'ind7'), ''];
$data[] = ['8. Estimate Population of Preschool Children (0–59 mos.)', val($totals,'ind8'), ''];
$data[] = ['9. Actual Number of Preschool Children 0–59 mos. Measured During OPT Plus', val($totals,'ind9'), ''];
$data[] = ['a. Percent Measured Coverage (OPT Plus)', '', val($totals,'ind9a','pct')];

// Nutrition indicators 9b1–9b7
$data[] = ['b. Number and percent (%) of preschool children according to Nutritional Status', 'No.','%'];
$nutri = ['1. Severely Underweight','2. Underweight','3. Normal Weight','4. Severely Wasted','5. Wasted','6. Overweight','7. Obese'];
for($i=1;$i<=7;$i++){
    $data[] = [$nutri[$i-1], val($totals,"ind9b{$i}_no"), val($totals,"ind9b{$i}_pct",'pct')];
}

// Indicators 10–16
for($i=10;$i<=16;$i++){
    $data[] = ["$i. ".ucwords(str_replace('_',' ',$totals["ind$i"] ?? '')), val($totals,"ind$i"), ''];
}

// Indicator 17
$data[] = ['17. Total number of Educational Institutions','Public','Private'];
$data[] = ['a. Number of Day Care Centers', val($totals,'ind17a_public'), val($totals,'ind17a_private')];
$data[] = ['b. Number of Elementary Schools', val($totals,'ind17b_public'), val($totals,'ind17b_private')];

// Indicators 18–21
$data[] = ['18. Total Number of Children Enrolled in Kindergarten', val($totals,'ind18'), ''];
$data[] = ['19. Total Number of School Children (Grades 1–6)', val($totals,'ind19'), ''];
$data[] = ['20. Total Number of School Children Weighed at Start of School Year', val($totals,'ind20'), ''];
$data[] = ['21. Percentage Coverage of School Children Measured', '', val($totals,'ind21','pct')];

// Indicator 22
$data[] = ['22. Number and percent (%) of school children according to Nutritional Status', 'No.','%'];
$school = ['a. Severely Wasted','b. Wasted','c. Severely Stunted','d. Stunted','e. Normal','f. Overweight','g. Obese'];
for($i=0;$i<count($school);$i++){
    $c = chr(97 + $i);
    $data[] = [$school[$i], val($totals,"ind22{$c}_no"), val($totals,"ind22{$c}_pct",'pct')];
}

// Indicators 23–26
$data[] = ['23. 0–5 Months Old Children Exclusively Breastfed', val($totals,'ind23'), ''];
$data[] = ['24. Households with Severely Wasted School Children', val($totals,'ind24'), ''];
$data[] = ['25. School Children Dewormed at Start of School Year', val($totals,'ind25'), ''];
$data[] = ['26. Fully Immunized Children (FIC)', val($totals,'ind26'), ''];

// Indicator 27–31 (Toilet, Garbage, Water, Household, Dwelling)
// You can continue here using same pattern as PDF arrays, e.g. ind27a, ind28a, etc.

// Indicators 32–38
$data[] = ['32. Total Number of Households Using Iodized Salt', val($totals,'ind32'), ''];
$data[] = ['33. Total Number of Eateries/Carinderia', val($totals,'ind33'), ''];
$data[] = ['34. Total Number of Sari-Sari Stores Related to Iodized Salt', val($totals,'ind34'), ''];
$data[] = ['35. Total Number of Sari-Sari Stores Related to Cooking Oil', val($totals,'ind35'), ''];
$data[] = ['36. Total Number of Bakeries with Fortified Flour', val($totals,'ind36'), ''];
$data[] = ['37. Number of Health and Nutrition Workers:', '', ''];
$data[] = ['a. Barangay Nutrition Scholar', val($totals,'ind37a'), ''];
$data[] = ['b. Barangay Health Worker', val($totals,'ind37b'), ''];
$data[] = ['38. Total Number of Households Beneficiaries of Pantawid Pamilyang Pilipino Program', val($totals,'ind38'), ''];

// ---------- Export CSV ----------
ob_end_clean(); // clear buffer before sending headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="BSA_Report.csv"');
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');

foreach($data as $row){
    // Replace — with empty string for Excel
    $cleanRow = array_map(function($v){ return $v === '—' ? '' : $v; }, $row);
    fputcsv($output, $cleanRow);
}

fclose($output);
exit;