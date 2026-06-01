<?php
header('Content-Type: application/json');
require '../db/config.php';

// 1. Load base GeoJSON
$geojsonPath = __DIR__ . '/barangay_boundary.geojson';
if (!file_exists($geojsonPath)) {
    http_response_code(500);
    echo json_encode(["error" => "GeoJSON not found"]);
    exit;
}
$geojson = json_decode(file_get_contents($geojsonPath), true);

// 2. Get latest report per USER per barangay per year
$sql = "SELECT 
            br.barangay,
            br.year,
            r.user_id,
            br.ind9 as total_measured,
            br.ind9b1_no, br.ind9b2_no, br.ind9b3_no,
            br.ind9b4_no, br.ind9b5_no, br.ind9b6_no,
            br.ind9b7_no, br.ind9b8_no, br.ind9b9_no,
            r.created_at
        FROM bns_reports br
        JOIN reports r ON br.report_id = r.id
        LEFT JOIN report_archives ra ON r.id = ra.report_id AND ra.user_type = 'CNO'
        WHERE 
            r.status = 'approved'
            AND (ra.is_archived IS NULL OR ra.is_archived = 0)
            AND (ra.is_deleted IS NULL OR ra.is_deleted = 0)
        ORDER BY r.created_at DESC";

$stmt = $pdo->query($sql);
$allReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Keep only latest report per user per barangay per year
$latestPerUser = [];
foreach ($allReports as $report) {
    $key = $report['barangay'] . '|' . $report['year'] . '|' . $report['user_id'];
    if (!isset($latestPerUser[$key])) {
        $latestPerUser[$key] = $report;
    }
}

// 4. Aggregate by barangay and year (sum all counts)
$aggregated = [];
foreach ($latestPerUser as $report) {
    $key = $report['barangay'] . '|' . $report['year'];
    
    if (!isset($aggregated[$key])) {
        $aggregated[$key] = [
            'barangay' => $report['barangay'],
            'year' => $report['year'],
            'total_measured' => 0,
            'ind9b1_no' => 0,
            'ind9b2_no' => 0,
            'ind9b3_no' => 0,
            'ind9b4_no' => 0,
            'ind9b5_no' => 0,
            'ind9b6_no' => 0,
            'ind9b7_no' => 0,
            'ind9b8_no' => 0,
            'ind9b9_no' => 0,
            'users_count' => 0
        ];
    }
    
    $aggregated[$key]['total_measured'] += intval($report['total_measured']);
    $aggregated[$key]['ind9b1_no'] += intval($report['ind9b1_no']);
    $aggregated[$key]['ind9b2_no'] += intval($report['ind9b2_no']);
    $aggregated[$key]['ind9b3_no'] += intval($report['ind9b3_no']);
    $aggregated[$key]['ind9b4_no'] += intval($report['ind9b4_no']);
    $aggregated[$key]['ind9b5_no'] += intval($report['ind9b5_no']);
    $aggregated[$key]['ind9b6_no'] += intval($report['ind9b6_no']);
    $aggregated[$key]['ind9b7_no'] += intval($report['ind9b7_no']);
    $aggregated[$key]['ind9b8_no'] += intval($report['ind9b8_no']);
    $aggregated[$key]['ind9b9_no'] += intval($report['ind9b9_no']);
    $aggregated[$key]['users_count']++;
}

// 5. Calculate percentages from aggregated counts
$lookup = [];
$allYears = [];

foreach ($aggregated as $row) {
    $b = strtoupper(trim($row['barangay']));
    $y = $row['year'];
    
    if (!isset($lookup[$b])) $lookup[$b] = [];
    if (!in_array($y, $allYears)) $allYears[] = $y;
    
    $totalMeasured = $row['total_measured'] > 0 ? $row['total_measured'] : 1;
    
    // Calculate percentages using the correct formulas
    $underweightCount = $row['ind9b1_no'] + $row['ind9b2_no'];
    $underweightPct = round(($underweightCount / $totalMeasured) * 100, 1);
    
    $normalCount = $row['ind9b3_no'];
    $normalPct = round(($normalCount / $totalMeasured) * 100, 1);
    
    $wastedCount = $row['ind9b4_no'] + $row['ind9b5_no'];
    $wastedPct = round(($wastedCount / $totalMeasured) * 100, 1);
    
    $overweightObeseCount = $row['ind9b6_no'] + $row['ind9b7_no'];
    $overweightObesePct = round(($overweightObeseCount / $totalMeasured) * 100, 1);
    
    $stuntedCount = $row['ind9b8_no'] + $row['ind9b9_no'];
    $stuntedPct = round(($stuntedCount / $totalMeasured) * 100, 1);
    
    // Store in lookup array
    $lookup[$b][$y] = [
        'TOTAL_MEASURED' => $totalMeasured,
        'UNDERWEIGHT' => $underweightPct,
        'UNDERWEIGHT_COUNT' => $underweightCount,
        'NORMAL' => $normalPct,
        'NORMAL_COUNT' => $normalCount,
        'WASTED' => $wastedPct,
        'WASTED_COUNT' => $wastedCount,
        'OVERWEIGHT_OBESE' => $overweightObesePct,
        'OVERWEIGHT_OBESE_COUNT' => $overweightObeseCount,
        'STUNTED' => $stuntedPct,
        'STUNTED_COUNT' => $stuntedCount,
        'ALL' => max($underweightPct, $wastedPct, $overweightObesePct, $stuntedPct),
        'TOTAL_MALNUTRITION' => $underweightPct + $wastedPct + $overweightObesePct + $stuntedPct,
        'USERS_CONTRIBUTED' => $row['users_count']
    ];
}

// Get all barangay names from GeoJSON
$allBarangays = [];
foreach ($geojson['features'] as $feature) {
    $bName = strtoupper(trim($feature['properties']['BARANGAY']));
    $allBarangays[] = $bName;
}

// Determine min and max years from data
sort($allYears);
$minYear = !empty($allYears) ? min($allYears) : date('Y');
$maxYear = !empty($allYears) ? max($allYears) : date('Y');

// Generate complete year range
$completeYearRange = range($minYear, $maxYear);

// Create new GeoJSON features
$newFeatures = [];
foreach ($geojson['features'] as $feature) {
    $bName = strtoupper(trim($feature['properties']['BARANGAY']));
    
    foreach ($completeYearRange as $year) {
        $newFeature = $feature;
        
        if (isset($lookup[$bName]) && isset($lookup[$bName][$year])) {
            $vals = $lookup[$bName][$year];
            foreach ($vals as $key => $val) {
                $newFeature['properties'][$key] = $val;
            }
            $newFeature['properties']['YEAR'] = $year;
            $newFeature['properties']['HAS_DATA'] = true;
            $newFeature['properties']['NO_APPROVED_DATA'] = false;
        } else {
            $newFeature['properties']['YEAR'] = $year;
            $newFeature['properties']['HAS_DATA'] = false;
            $newFeature['properties']['NO_APPROVED_DATA'] = true;
            $newFeature['properties']['UNDERWEIGHT'] = null;
            $newFeature['properties']['WASTED'] = null;
            $newFeature['properties']['OVERWEIGHT_OBESE'] = null;
            $newFeature['properties']['STUNTED'] = null;
            $newFeature['properties']['ALL'] = null;
            $newFeature['properties']['TOTAL_MEASURED'] = 0;
            $newFeature['properties']['USERS_CONTRIBUTED'] = 0;
        }
        $newFeatures[] = $newFeature;
    }
}

$geojson['features'] = $newFeatures;

// Add metadata
$geojson['metadata'] = [
    'availableYears' => $allYears,
    'minYear' => $minYear,
    'maxYear' => $maxYear,
    'hasAnyData' => !empty($allYears),
    'completeYearRange' => $completeYearRange
];

echo json_encode($geojson);
?>