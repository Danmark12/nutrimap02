<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';
require_once('../vendor/autoload.php'); // TCPDF

// ---------- Access Control ----------
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}

$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// ---------- Helper ----------
function val(array $a, string $k, string $fmt = 'int'): string {
    if (!isset($a[$k]) || $a[$k] === '' || $a[$k] === null) return '—';
    if ($fmt === 'int')  return (string)(int)$a[$k];
    if ($fmt === 'pct')  return number_format((float)$a[$k], 2) . '%';
    if ($fmt === 'dec2') return number_format((float)$a[$k], 2);
    return htmlspecialchars((string)$a[$k]);
}

function makeTable(array $rows, bool $hidePctHeader = false): string {
    $html  = '<table cellpadding="2" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11px;">';
    $html .= '<thead><tr>'
          .  '<th width="60%" style="border:1px solid #000;background:#dcdcdc;font-weight:bold;text-align:left;padding:2px;line-height:2;">Indicator</th>'
          .  '<th width="40%" style="border:1px solid #000;background:#dcdcdc;font-weight:bold;text-align:center;padding:2px;line-height:2;">No.</th>';
    if (!$hidePctHeader) {
        $html .= '<th width="33%" style="padding:2px;line-height:2;"></th>';
    }
    $html .= '</tr></thead><tbody>';
    foreach ($rows as $r) {
        $indicator = $r[0];
        $no        = $r[1] ?? '—';
        $pct       = $r[2] ?? '';
        $html .= '<tr>';
        $html .= '<td width="60%" style="border:1px solid #000;padding:2px;line-height:2;">'.$indicator.'</td>';
        if ($pct === '' || $pct === null) {
            // Only number, merge columns
            $colspan = $hidePctHeader ? 1 : 2;
            $html .= '<td colspan="'.$colspan.'" width="40%" style="border:1px solid #000;text-align:center;padding:2px;line-height:2;">'.$no.'</td>';
        } else {
            // Number + percent, separate columns
            $html .= '<td width="20%" style="border:1px solid #000;text-align:center;padding:2px;line-height:2;">'.$no.'</td>';
            if (!$hidePctHeader) {
                $html .= '<td width="20%" style="border:1px solid #000;text-align:center;padding:2px;line-height:2;">'.$pct.'</td>';
            }
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

// ---------- Get Report ----------
$report_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($report_id <= 0) die("Report not found!");

// ---------- Fields ----------
$base = [
    'ind1','ind_male','ind_female','ind2','ind3','ind4','ind5',
    'ind6a','ind6b','ind7','ind8','ind9','ind9a','ind10','ind11',
    'ind12','ind13','ind14','ind15','ind16','ind18','ind19',
    'ind20','ind21','ind23','ind24','ind25','ind26',
    'ind37a','ind37b','ind38'
];

$groups = [
    '9b'  => ['ind9b1','ind9b2','ind9b3','ind9b4','ind9b5','ind9b6','ind9b7','ind9b8','ind9b9'],
    '22'  => ['ind22a','ind22b','ind22c','ind22d','ind22e','ind22f','ind22g'],
    '27'  => ['ind27a','ind27b','ind27c','ind27d','ind27e'],
    '28'  => ['ind28a','ind28b','ind28c','ind28d'],
    '29'  => ['ind29a','ind29b','ind29c','ind29d','ind29e','ind29f','ind29g'],
    '30'  => ['ind30a','ind30b','ind30c','ind30d'],
    '31'  => ['ind31a','ind31b','ind31c','ind31d','ind31e','ind31f'],
    '32'  => ['ind32'],
    '33'  => ['ind33'],
    '34'  => ['ind34'],
    '35'  => ['ind35'],
    '36'  => ['ind36']
];

// ---------- SELECT fields ----------
$sel = [];
foreach($base as $f) $sel[] = "SUM(bns.$f) AS $f";
foreach($groups as $arr){
    foreach($arr as $f){
        $sel[] = "SUM(bns.{$f}_no)  AS {$f}_no";
        $sel[] = "SUM(bns.{$f}_pct) AS {$f}_pct";
    }
}

// keep only numeric ones for 17a, 17b
$sel[]="SUM(bns.ind17a_public)  AS ind17a_public";
$sel[]="SUM(bns.ind17a_private) AS ind17a_private";
$sel[]="SUM(bns.ind17b_public)  AS ind17b_public";
$sel[]="SUM(bns.ind17b_private) AS ind17b_private";
$sel[]="SUM(bns.ind37a) AS ind37a";
$sel[]="SUM(bns.ind37b) AS ind37b";

// ---------- Fetch the report ----------
$sql = "SELECT ".implode(",", $sel)." 
        FROM bns_reports bns
        JOIN reports r ON bns.report_id = r.id
        WHERE r.status = 'approved' AND bns.report_id = :report_id
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['report_id'=>$report_id]);
$totals = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$totals) die("Report not found or not approved!");

// ---------- Get Report Details ----------
$stmt = $pdo->prepare("
    SELECT r.id, r.report_date, r.report_time, r.status, b.barangay,
    CASE WHEN b.barangay='Bolobolo' THEN 'Pedro sa Baculio' ELSE b.barangay END AS normalized_barangay
    FROM bns_reports b
    JOIN reports r ON b.report_id = r.id
    WHERE b.report_id = :report_id
    LIMIT 1
");
$stmt->execute(['report_id' => $report_id]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$report) die("Report not found!");

// ---------- Barangay logo ----------
function getBarangayLogo($barangay) {
    $logos = [
        'CNO'=>'CNO.png','Amoros'=>'Amoros.jpg','Bolisong'=>'Bolisong.jpg',
        'Cogon'=>'Cogon.jpg','Himaya'=>'Himaya.jpg','Hinigdaan'=>'Hinigdaan.jpg',
        'Kalabaylabay'=>'Kalabaylabay.jpg','Molugan'=>'Molugan.jpg',
        'Pedro sa Baculio'=>'Bolobolo.jpg','Poblacion'=>'Poblacion.jpg',
        'Kibonbon'=>'Kibonbon.jpg','Sambulawan'=>'Sambulawan.jpg',
        'Calongonan'=>'Calongonan.jpg','Sinaloc'=>'Sinaloc.jpg',
        'Taytay'=>'Taytay.jpg','Ulaliman'=>'Ulaliman.jpg'
    ];
    return $logos[$barangay] ?? 'default.png';
}
$barangay_logo = getBarangayLogo($report['normalized_barangay'] ?? '');
$barangay_name = $report['normalized_barangay'] ?? '—';

// ---------- PDF Header ----------
class MYPDF extends TCPDF {
    public $reportYear = null;
    public $barangayName = '';
    public $barangayLogo = '';

    public function Header() {
        $this->SetFont('times','B',12);
        $this->SetXY(12, 10);
        $this->MultiCell(60, 5, "BNS Form No. IC\nBarangay Nutrition Profile", 0, 'L', 0, 0);

        if($this->barangayLogo){
            $this->Image(__DIR__ . '/../logos/barangay/' . $this->barangayLogo, 110, 8, 20);
        }

        $this->Image(__DIR__.'/../logos/fixed/Seal_of_El_Salvador__Misamis_Oriental-removebg-preview.jpg', 130, 8.5, 17);
        $this->Image(__DIR__.'/../logos/fixed/National_Nutrition_Council__NNC_.svg-removebg-preview.jpg', 150, 8.5, 17);
        $this->Image(__DIR__.'/../logos/fixed/Bagong-Pilipinas-logo.jpg', 170, 8.5, 17);

        $this->SetY(35);
        $this->SetFont('times','B',14);
        $this->Cell(0, 0, 'BARANGAY SITUATIONAL ANALYSIS (BSA)', 0, 1, 'C');

        $this->Ln(2);
        $this->SetFont('times','',11);
        $year = $this->reportYear ?? date('Y');
        $this->Cell(0, 0, "Calendar Year: $year | Barangay: {$this->barangayName} | City: EL SALVADOR CITY | Province: MISAMIS ORIENTAL", 0, 1, 'C');

        $this->Ln(8);
    }

public function Footer() {
    date_default_timezone_set('Asia/Manila'); // PH TIME

    $this->SetY(-15);
    $this->SetFont('times','I',10);

    // Philippine time export timestamp
    $exported = date("F d, Y h:i A");
    $this->Cell(0, 10, "$exported", 0, 0, 'L');

    // Page number
    $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, 0, 'R');
}
}

// ---------- PDF Init ----------
$pdf = new MYPDF('P','mm','A4',true,'UTF-8',false);
$pdf->reportYear = $selectedYear;
$pdf->barangayName = $barangay_name;
$pdf->barangayLogo = $barangay_logo;
$pdf->SetCreator('Nutrimap');
$pdf->SetAuthor('CNO');
$pdf->SetTitle('CNO | Export Barangay Situational Analysis');
$pdf->SetMargins(12, 50, 12);
$pdf->SetAutoPageBreak(true,15);
$pdf->SetFont('times','',11);

// ---------- PAGE 1 ----------
$pdf->AddPage();
$pdf->SetFont('times','B',14);
$pdf->Ln(6);
$pdf->SetFont('times','',11);

$p1 = [
    ['Total Population', val($totals,'ind1')],
    ['Male', val($totals,'ind_male')],
    ['Female', val($totals,'ind_female')],
    ['Total Number of Households', val($totals,'ind2')],
    ['Total Number of Family', val($totals,'ind3')],
    ['Total Number of HHs More Than 5 Below Members', val($totals,'ind4')],
    ['Total Number of HHs More Than 5 Above Members', val($totals,'ind5')],
    ['Total Number of Women Who Are Pregnant', val($totals,'ind6a')],
    ['Total Number of Women Who Are Lactating', val($totals,'ind6b')],
    ['Total Number of Households with Preschool Children (0–59 mos.)', val($totals,'ind7')],
    ['Actual Population of Preschool Children (0–59 mos.)', val($totals,'ind8')],
    ['Total Number of Preschool Children 0–59 mos. Measured During OPT Plus', val($totals,'ind9')],
    ['Percent Measured Coverage (OPT Plus)', val($totals,'ind9a','pct')]
];

// Nutrition indicators
$nutri = ['Severely Underweight','Underweight','Normal Weight','Severely Wasted','Wasted','Overweight','Obese','Severely Stunted','Stunted'];
for($i=1;$i<=9;$i++){
    $p1[] = [$nutri[$i-1], val($totals,"ind9b{$i}_no"), val($totals,"ind9b{$i}_pct",'pct')];
}



$pdf->writeHTML(makeTable($p1), true, false, false, false, '');

// ---------- PAGE 2 ----------
$pdf->AddPage();
$p2 = [];

$p2 = array_merge($p2, [
    ['Total Number of Infants 0–5 Months Old', val($totals,'ind10')],
    ['Total Number of Infants 6–11 Months Old', val($totals,'ind11')],
    ['Total Number of Preschool Children 0–23 Months Old', val($totals,'ind12')],
    ['Total Number of Preschool Children 12–59 Months Old', val($totals,'ind13')],
    ['Total Number of Preschool Children 24–59 Months Old', val($totals,'ind14')],
    ['Total Number of Families with Wasted and Severely Wasted Preschool Children', val($totals,'ind15')],
    ['Total Number of Families with Stunted and Severely Stunted Preschool Children', val($totals,'ind16')]
]);

$p2[] = ['Number of Day Care Centers', val($totals,'ind17a_public'), val($totals,'ind17a_private','no')];
$p2[] = ['Number of Elementary Schools', val($totals,'ind17b_public'), val($totals,'ind17b_private','no')];

$p2[] = ['Total Number of Children Enrolled in Kindergarten', val($totals,'ind18'), ''];
$p2[] = ['Total Number of School Children (Grades 1–6)', val($totals,'ind19'), ''];
$p2[] = ['Total Number of School Children Weighed at Start of School Year', val($totals,'ind20'), ''];
$p2[] = ['Percentage Coverage of School Children Measured', val($totals,'ind21','pct')];

// School nutrition
$school = ['Severely Wasted','Wasted','Severely Stunted','Stunted','Normal','Overweight','Obese'];
for($i=0;$i<count($school);$i++){
    $c = chr(97 + $i);
    $p2[] = [$school[$i], val($totals,"ind22{$c}_no"), val($totals,"ind22{$c}_pct",'pct')];
}

$pdf->writeHTML(makeTable($p2), true, false, false, false, '');

// ---------- PAGE 3 ----------
$pdf->AddPage();
$p3 = [];

$p3[] = ['0–5 Months Old Children Exclusively Breastfed', val($totals,'ind23'), ''];
$p3[] = ['Households with Severely Wasted School Children', val($totals,'ind24'), ''];
$p3[] = ['School Children Dewormed at Start of School Year', val($totals,'ind25'), ''];
$p3[] = ['Fully Immunized Children (FIC)', val($totals,'ind26'), ''];

// Toilet types
$toilet = ['Water-sealed toilet','Antipolo (Unsanitary Toilet)','Open Pit','Shared','No Toilet'];
for($i=0;$i<count($toilet);$i++){
    $c = chr(97 + $i);
    $p3[] = [$toilet[$i], val($totals,"ind27{$c}_no"), val($totals,"ind27{$c}_pct",'pct')];
}

// Garbage
$garbage = ['Barangay/City Garbage Collection','Own Compost Pit','Burning','Dumping'];
for($i=0;$i<count($garbage);$i++){
    $c = chr(97 + $i);
    $p3[] = [$garbage[$i], val($totals,"ind28{$c}_no"), val($totals,"ind28{$c}_pct",'pct')];
}

// Water source
$water = ['Pipe Water System (Level III)','Spring (Level II)','Deep Well with Communal Source (Level II)','Deep Well with Individual Faucet (Level III)','Purified Station (Level III)','Open Shallow Dug Well (Level I)','Artesian Well'];
for($i=0;$i<count($water);$i++){
    $c = chr(97 + $i);
    $p3[] = [$water[$i], val($totals,"ind29{$c}_no"), val($totals,"ind29{$c}_pct",'pct')];
}

// Home/farming
$home = ['Vegetable Garden','Livestock/Poultry','Fishponds','Other Specify: No Garden'];
for($i=0;$i<count($home);$i++){
    $c = chr(97 + $i);
    $p3[] = [$home[$i], val($totals,"ind30{$c}_no"), val($totals,"ind30{$c}_pct",'pct')];
}

$pdf->writeHTML(makeTable($p3), true, false, false, false, '');

//Page 4 (if needed)
$pdf->AddPage();
$p4 = [];

// Dwelling
$dwelling = ['Concrete','Semi Concrete','Wooden House','Nipa Bamboo House','Barong-Barong Makeshift','Makeshift'];
for($i=0;$i<count($dwelling);$i++){
    $c = chr(97 + $i);
    $p4[] = [$dwelling[$i], val($totals,"ind31{$c}_no"), val($totals,"ind31{$c}_pct",'pct')];
}

// Number-only indicators
$p4[] = ['Total Number of Households Using Iodized Salt', val($totals,'ind32_no'), val($totals,'ind32_pct','pct')];
$p4[] = ['Total Number of Eateries/Carinderia', val($totals,'ind33_no'), val($totals,'ind33_pct','pct')];
$p4[] = ['Total Number of Sari-Sari Stores Related to Iodized Salt', val($totals,'ind34_no'), val($totals,'ind34_pct','pct')];
$p4[] = ['Total Number of Sari-Sari Stores Related to Cooking Oil', val($totals,'ind35_no'), val($totals,'ind35_pct','pct')];
$p4[] = ['Total Number of Bakeries with Fortified Flour', val($totals,'ind36_no'), val($totals,'ind36_pct','pct')];

$p4[] = ['Barangay Nutrition Scholar', val($totals,'ind37a'), ''];
$p4[] = ['Barangay Health Worker', val($totals,'ind37b'), ''];
$p4[] = ['Total Number of Households Beneficiaries of Pantawid Pamilyang Pilipino Program', val($totals,'ind38'), ''];
$pdf->writeHTML(makeTable($p4), true, false, false, false, '');

// ---------- Determine output format ----------
$format = isset($_GET['format']) ? strtolower($_GET['format']) : 'pdf';

if($format === 'csv') {
    // ---------- CSV Export ----------
    function exportCSV($filename, $pages) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');

        foreach($pages as $page) {
            foreach($page as $row) {
                // Replace — with empty string for CSV
                $cleanRow = array_map(function($v){ return $v === '—' ? '' : $v; }, $row);
                fputcsv($output, $cleanRow);
            }
            // Add an empty line between pages
            fputcsv($output, []);
        }

        fclose($output);
        exit;
    }

    // Prepare all pages in order
    $allPages = [$p1, $p2, $p3, $p4];
    exportCSV('Barangay_Situational_Analysis.csv', $allPages);

} else {
    // ---------- PDF Output ----------
    $pdf->Output('Barangay_Situation_Analysis.pdf','I');
}

