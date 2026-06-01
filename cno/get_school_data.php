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

// 2. Get latest report per USER per barangay per year for SCHOOL data
$sql = "SELECT 
            br.barangay,
            br.year,
            r.user_id,
            br.ind20 as school_total_measured,
            br.ind22a_no, br.ind22b_no, br.ind22c_no, br.ind22d_no,
            br.ind22e_no, br.ind22f_no, br.ind22g_no,
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
            'school_total_measured' => 0,
            'ind22a_no' => 0,
            'ind22b_no' => 0,
            'ind22c_no' => 0,
            'ind22d_no' => 0,
            'ind22e_no' => 0,
            'ind22f_no' => 0,
            'ind22g_no' => 0,
            'users_count' => 0
        ];
    }
    
    $aggregated[$key]['school_total_measured'] += intval($report['school_total_measured']);
    $aggregated[$key]['ind22a_no'] += intval($report['ind22a_no']);
    $aggregated[$key]['ind22b_no'] += intval($report['ind22b_no']);
    $aggregated[$key]['ind22c_no'] += intval($report['ind22c_no']);
    $aggregated[$key]['ind22d_no'] += intval($report['ind22d_no']);
    $aggregated[$key]['ind22e_no'] += intval($report['ind22e_no']);
    $aggregated[$key]['ind22f_no'] += intval($report['ind22f_no']);
    $aggregated[$key]['ind22g_no'] += intval($report['ind22g_no']);
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
    
    $totalMeasured = $row['school_total_measured'] > 0 ? $row['school_total_measured'] : 1;
    
    // Calculate percentages using the correct formulas
    $wastedCount = $row['ind22a_no'] + $row['ind22b_no'];
    $wastedPct = round(($wastedCount / $totalMeasured) * 100, 1);
    
    $stuntedCount = $row['ind22c_no'] + $row['ind22d_no'];
    $stuntedPct = round(($stuntedCount / $totalMeasured) * 100, 1);
    
    $normalCount = $row['ind22e_no'];
    $normalPct = round(($normalCount / $totalMeasured) * 100, 1);
    
    $overweightObeseCount = $row['ind22f_no'] + $row['ind22g_no'];
    $overweightObesePct = round(($overweightObeseCount / $totalMeasured) * 100, 1);
    
    // Store in lookup array
    $lookup[$b][$y] = [
        'SCHOOL_TOTAL_MEASURED' => $totalMeasured,
        'WASTED' => $wastedPct,
        'WASTED_COUNT' => $wastedCount,
        'STUNTED' => $stuntedPct,
        'STUNTED_COUNT' => $stuntedCount,
        'NORMAL' => $normalPct,
        'NORMAL_COUNT' => $normalCount,
        'OVERWEIGHT_OBESE' => $overweightObesePct,
        'OVERWEIGHT_OBESE_COUNT' => $overweightObeseCount,
        'ALL' => max($wastedPct, $stuntedPct, $overweightObesePct),
        'TOTAL_MALNUTRITION' => $wastedPct + $stuntedPct + $overweightObesePct,
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
            $newFeature['properties']['WASTED'] = null;
            $newFeature['properties']['STUNTED'] = null;
            $newFeature['properties']['OVERWEIGHT_OBESE'] = null;
            $newFeature['properties']['NORMAL'] = null;
            $newFeature['properties']['ALL'] = null;
            $newFeature['properties']['SCHOOL_TOTAL_MEASURED'] = 0;
            $newFeature['properties']['USERS_CONTRIBUTED'] = 0;
        }
        $newFeatures[] = $newFeature;
    }
}

$geojson['features'] = $newFeatures;
$geojson['metadata'] = [
    'availableYears' => $allYears,
    'minYear' => $minYear,
    'maxYear' => $maxYear,
    'hasAnyData' => !empty($allYears),
    'completeYearRange' => $completeYearRange
];

echo json_encode($geojson);
?>