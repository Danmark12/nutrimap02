<?php
require '../vendor/autoload.php';
require '../db/config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// ---------- Access Control ----------
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'BNS') {
    die("Access denied!");
}

// ---------- Helper ----------
function val(array $a, string $k, string $fmt = 'int') {
    if (!isset($a[$k]) || $a[$k] === '' || $a[$k] === null) return '—';
    if ($fmt === 'int')  return (string)(int)$a[$k];
    if ($fmt === 'pct')  return number_format((float)$a[$k], 2) . '%';
    if ($fmt === 'dec2') return number_format((float)$a[$k], 2);
    return (string)$a[$k];
}

// ---------- Get Report ----------
$report_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($report_id <= 0) die("Report not found!");

// Fetch report totals (same as your PDF query)
$sql = "SELECT * FROM bns_reports bns WHERE bns.report_id = :report_id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['report_id'=>$report_id]);
$totals = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$totals) die("Report not found!");

// ---------- Spreadsheet ----------
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ---------- Headers ----------
$sheet->setCellValue('A1', 'Indicator');
$sheet->setCellValue('B1', 'No.');
$sheet->setCellValue('C1', '%');

// Style headers
$sheet->getStyle('A1:C1')->getFont()->setBold(true);
$sheet->getStyle('A1:C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');
$sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Auto width
foreach (range('A','C') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// ---------- Prepare data like PDF ----------
$data = [];

// Page 1 indicators (replicating your $p1)
$data[] = ['1. Total Population', val($totals,'ind1'), ''];
$data[] = ['Male', val($totals,'ind_male'), ''];
$data[] = ['Female', val($totals,'ind_female'), ''];
$data[] = ['2. Number of Households', val($totals,'ind2'), ''];
$data[] = ['3. Total Number of Family', val($totals,'ind3'), ''];
$data[] = ['4. Total Number of HHs More Than 5 Below Members', val($totals,'ind4'), ''];
$data[] = ['5. Total Number of HHs More Than 5 Above Members', val($totals,'ind5'), ''];

// Example grouped indicator
$data[] = ['6. Total number of women who are:', '', ''];
$data[] = ['a. Total Number of Women Who Are Pregnant', val($totals,'ind6a'), ''];
$data[] = ['b. Total Number of Women Who Are Lactating', val($totals,'ind6b'), ''];
$data[] = ['7. Total Number of Households with Preschool Children (0–59 mos.)', val($totals,'ind7'), ''];
$data[] = ['8. Estimate Population of Preschool Children (0–59 mos.)', val($totals,'ind8'), ''];
$data[] = ['9. Actual Number of Preschool Children 0–59 mos. Measured During OPT Plus', val($totals,'ind9'), ''];
$data[] = ['a. Percent Measured Coverage (OPT Plus)', '', val($totals,'ind9a','pct')];

// Nutrition indicators 1–7
$data[] = ['b. Number and percent (%) of preschool children according to Nutritional Status', 'No.', '%'];
for ($i=1; $i<=7; $i++) {
    $data[] = ["{$i}. " . ['Severely Underweight','Underweight','Normal Weight','Severely Wasted','Wasted','Overweight','Obese'][$i-1], 
               val($totals,"ind9b{$i}_no"), 
               val($totals,"ind9b{$i}_pct",'pct')];
}

// ---------- Page 2 ----------
$data[] = ['8. Nutrition Indicators Continued', '', ''];
$data[] = ['8. Severely Stunted', val($totals,'ind9b8_no'), val($totals,'ind9b8_pct','pct')];
$data[] = ['9. Stunted', val($totals,'ind9b9_no'), val($totals,'ind9b9_pct','pct')];

$data[] = ['10. Total Number of Infants 0–5 Months Old', val($totals,'ind10'), ''];
$data[] = ['11. Total Number of Infants 6–11 Months Old', val($totals,'ind11'), ''];
$data[] = ['12. Total Number of Preschool Children 0–23 Months Old', val($totals,'ind12'), ''];
$data[] = ['13. Total Number of Preschool Children 12–59 Months Old', val($totals,'ind13'), ''];
$data[] = ['14. Total Number of Preschool Children 24–59 Months Old', val($totals,'ind14'), ''];
$data[] = ['15. Total Number of Families with Wasted and Severely Wasted Preschool Children', val($totals,'ind15'), ''];
$data[] = ['16. Total Number of Families with Stunted and Severely Stunted Preschool Children', val($totals,'ind16'), ''];

// Educational Institutions
$data[] = ['17. Total number of Educational Institutions', 'Public', 'Private'];
$data[] = ['a. Number of Day Care Centers', val($totals,'ind17a_public'), val($totals,'ind17a_private')];
$data[] = ['b. Number of Elementary Schools', val($totals,'ind17b_public'), val($totals,'ind17b_private')];

$data[] = ['18. Total Number of Children Enrolled in Kindergarten', val($totals,'ind18'), ''];
$data[] = ['19. Total Number of School Children (Grades 1–6)', val($totals,'ind19'), ''];
$data[] = ['20. Total Number of School Children Weighed at Start of School Year', val($totals,'ind20'), ''];
$data[] = ['21. Percentage Coverage of School Children Measured', '', val($totals,'ind21','pct')];

// School nutrition
$data[] = ['22. Number and percent (%) of school children according to Nutritional Status', 'No.', '%'];
$school = ['a. Severely Wasted','b. Wasted','c. Severely Stunted','d. Stunted','e. Normal','f. Overweight','g. Obese'];
for($i=0;$i<count($school);$i++){
    $c = chr(97+$i);
    $data[] = [$school[$i], val($totals,"ind22{$c}_no"), val($totals,"ind22{$c}_pct",'pct')];
}

$data[] = ['23. 0–5 Months Old Children Exclusively Breastfed', val($totals,'ind23'), ''];
$data[] = ['24. Households with Severely Wasted School Children', val($totals,'ind24'), ''];
$data[] = ['25. School Children Dewormed at Start of School Year', val($totals,'ind25'), ''];
$data[] = ['26. Fully Immunized Children (FIC)', val($totals,'ind26'), ''];

// Toilet types
$data[] = ['27. Households by type of toilet facility:', 'No.', '%'];
$toilet = ['a. Water-sealed toilet','b. Antipolo (Unsanitary Toilet)','c. Open Pit','d. Shared','e. No Toilet'];
for($i=0;$i<count($toilet);$i++){
    $c = chr(97+$i);
    $data[] = [$toilet[$i], val($totals,"ind27{$c}_no"), val($totals,"ind27{$c}_pct",'pct')];
}

// Garbage disposal
$data[] = ['28. Households by type of garbage disposal:', 'No.', '%'];
$garbage = ['a. Barangay/City Garbage Collection','b. Own Compost Pit','c. Burning','d. Dumping'];
for($i=0;$i<count($garbage);$i++){
    $c = chr(97+$i);
    $data[] = [$garbage[$i], val($totals,"ind28{$c}_no"), val($totals,"ind28{$c}_pct",'pct')];
}

// Water sources
$data[] = ['29. Households by type of water source:', 'No.', '%'];
$water = ['a. Pipe Water System (Level III)','b. Spring (Level II)','c. Deep Well with Communal Source (Level II)','d. Deep Well with Individual Faucet (Level III)','e. Purified Station (Level III)','f. Open Shallow Dug Well (Level I)','g. Artesian Well'];
for($i=0;$i<count($water);$i++){
    $c = chr(97+$i);
    $data[] = [$water[$i], val($totals,"ind29{$c}_no"), val($totals,"ind29{$c}_pct",'pct')];
}

// Household with resources
$data[] = ['30. Household with:', 'No.', '%'];
$home = ['a. Vegetable Garden','b. Livestock/Poultry','c. Fishponds','d. Other Specify: No Garden'];
for($i=0;$i<count($home);$i++){
    $c = chr(97+$i);
    $data[] = [$home[$i], val($totals,"ind30{$c}_no"), val($totals,"ind30{$c}_pct",'pct')];
}

// Dwelling types
$data[] = ['31. Households according to type of dwelling unit:', 'No.', '%'];
$dwelling = ['a. Concrete','b. Semi Concrete','c. Wooden House','d. Nipa Bamboo House','e. Barong-Barong Makeshift','f. Makeshift'];
for($i=0;$i<count($dwelling);$i++){
    $c = chr(97+$i);
    $data[] = [$dwelling[$i], val($totals,"ind31{$c}_no"), val($totals,"ind31{$c}_pct",'pct')];
}

// Iodized salt, stores, bakeries
$data[] = ['32. Total Number of Households Using Iodized Salt', val($totals,'ind32'), ''];
$data[] = ['33. Total Number of Eateries/Carinderia', val($totals,'ind33'), ''];
$data[] = ['34. Total Number of Sari-Sari Stores Related to Iodized Salt', val($totals,'ind34'), ''];
$data[] = ['35. Total Number of Sari-Sari Stores Related to Cooking Oil', val($totals,'ind35'), ''];
$data[] = ['36. Total Number of Bakeries with Fortified Flour', val($totals,'ind36'), ''];

// Health & Nutrition Workers
$data[] = ['37. Number of Health and Nutrition Workers:', '', ''];
$data[] = ['a. Barangay Nutrition Scholar', val($totals,'ind37a'), ''];
$data[] = ['b. Barangay Health Worker', val($totals,'ind37b'), ''];

// Pantawid Pamilyang Pilipino Program
$data[] = ['38. Total Number of Households Beneficiaries of Pantawid Pamilyang Pilipino Program', val($totals,'ind38'), ''];


// ---------- Write data ----------
$row = 2;
foreach ($data as $d) {
    $sheet->setCellValue("A$row", $d[0]);
    $sheet->setCellValue("B$row", $d[1]);
    $sheet->setCellValue("C$row", $d[2]);

    // Center alignment for No. and %
    $sheet->getStyle("B$row:C$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // If indicator is grouped (like "6. Total number of women who are:"), make bold
    if ($d[1]==='' && $d[2]==='') {
        $sheet->getStyle("A$row")->getFont()->setBold(true);
    }

    $row++;
}

// ---------- Download Excel ----------
$filename = 'BSA_Report_'.date('Y-m-d_H-i-s').'.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;