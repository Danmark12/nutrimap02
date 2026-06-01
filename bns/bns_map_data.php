<?php
header('Content-Type: application/json');
require '../db/config.php';

session_start();

// ---------------------------------------------------------
// 0. Get logged-in user's barangay
// ---------------------------------------------------------
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$stmt = $pdo->prepare("SELECT barangay FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userBarangay = strtoupper(trim($stmt->fetchColumn()));

if (!$userBarangay) {
    http_response_code(500);
    echo json_encode(["error" => "User barangay not found"]);
    exit;
}

// ---------------------------------------------------------
// 1. Load base GeoJSON
// ---------------------------------------------------------
$geojsonPath = __DIR__ . '/../landing_page/barangay_boundary.geojson';

if (!file_exists($geojsonPath)) {
    http_response_code(500);
    echo json_encode(["error" => "GeoJSON not found at: " . $geojsonPath]);
    exit;
}

$geojson = json_decode(file_get_contents($geojsonPath), true);

if (!$geojson || !isset($geojson['features'])) {
    http_response_code(500);
    echo json_encode(["error" => "Invalid GeoJSON format"]);
    exit;
}

// ---------------------------------------------------------
// 2. Get ALL approved reports for this barangay from ALL users
//    Then get latest report per user per year
//    Then aggregate (sum) counts
// ---------------------------------------------------------

// First: Get all approved reports for this barangay
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
        WHERE 
            r.status = 'approved'
            AND UPPER(br.barangay) = UPPER(?)
            AND NOT EXISTS (
                SELECT 1 FROM report_archives ra
                WHERE ra.report_id = r.id
                  AND ra.user_type = 'CNO'
                  AND (ra.is_archived = 1 OR ra.is_deleted = 1)
            )
        ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userBarangay]);
$allReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Keep only latest report per user per year
$latestPerUser = [];
foreach ($allReports as $report) {
    $key = $report['year'] . '|' . $report['user_id'];
    if (!isset($latestPerUser[$key])) {
        $latestPerUser[$key] = $report;
    }
}

// Aggregate by year (sum all counts from latest reports)
$aggregated = [];
foreach ($latestPerUser as $report) {
    $year = $report['year'];
    
    if (!isset($aggregated[$year])) {
        $aggregated[$year] = [
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
    
    $aggregated[$year]['total_measured'] += intval($report['total_measured']);
    $aggregated[$year]['ind9b1_no'] += intval($report['ind9b1_no']);
    $aggregated[$year]['ind9b2_no'] += intval($report['ind9b2_no']);
    $aggregated[$year]['ind9b3_no'] += intval($report['ind9b3_no']);
    $aggregated[$year]['ind9b4_no'] += intval($report['ind9b4_no']);
    $aggregated[$year]['ind9b5_no'] += intval($report['ind9b5_no']);
    $aggregated[$year]['ind9b6_no'] += intval($report['ind9b6_no']);
    $aggregated[$year]['ind9b7_no'] += intval($report['ind9b7_no']);
    $aggregated[$year]['ind9b8_no'] += intval($report['ind9b8_no']);
    $aggregated[$year]['ind9b9_no'] += intval($report['ind9b9_no']);
    $aggregated[$year]['users_count']++;
}

// Calculate percentages from aggregated counts
$lookup = [];
foreach ($aggregated as $year => $row) {
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
    
    $lookup[$year] = [
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
        'TOTAL_MEASURED' => $totalMeasured,
        'USERS_CONTRIBUTED' => $row['users_count']
    ];
}

// ---------------------------------------------------------
// 3. Filter GeoJSON: keep ONLY the user's barangay
// ---------------------------------------------------------
$userFeatures = [];

foreach ($geojson['features'] as $feature) {
    $bName = strtoupper(trim($feature['properties']['BARANGAY']));

    if ($bName !== $userBarangay) continue;

    if (empty($lookup)) {
        $feature['properties']['NO_APPROVED_DATA'] = true;
        $feature['properties']['NO_DATA'] = true;
        $feature['properties']['YEAR'] = date('Y');
        $feature['properties']['UNDERWEIGHT'] = null;
        $feature['properties']['WASTED'] = null;
        $feature['properties']['OVERWEIGHT_OBESE'] = null;
        $feature['properties']['STUNTED'] = null;
        $feature['properties']['USERS_CONTRIBUTED'] = 0;
        $userFeatures[] = $feature;
        continue;
    }

    // Create one feature PER YEAR that has data
    foreach ($lookup as $year => $values) {
        $newFeature = json_decode(json_encode($feature), true);
        
        $newFeature['properties']['BARANGAY'] = $feature['properties']['BARANGAY'];
        $newFeature['properties']['YEAR'] = $year;
        $newFeature['properties']['UNDERWEIGHT'] = $values['UNDERWEIGHT'];
        $newFeature['properties']['WASTED'] = $values['WASTED'];
        $newFeature['properties']['OVERWEIGHT_OBESE'] = $values['OVERWEIGHT_OBESE'];
        $newFeature['properties']['STUNTED'] = $values['STUNTED'];
        $newFeature['properties']['NORMAL'] = $values['NORMAL'];
        $newFeature['properties']['TOTAL_MEASURED'] = $values['TOTAL_MEASURED'];
        $newFeature['properties']['USERS_CONTRIBUTED'] = $values['USERS_CONTRIBUTED'];
        $newFeature['properties']['NO_DATA'] = false;
        $newFeature['properties']['NO_APPROVED_DATA'] = false;
        
        $userFeatures[] = $newFeature;
    }
}

// ---------------------------------------------------------
// 4. If no features created, add empty feature for display
// ---------------------------------------------------------
if (empty($userFeatures)) {
    foreach ($geojson['features'] as $feature) {
        $bName = strtoupper(trim($feature['properties']['BARANGAY']));
        if ($bName === $userBarangay) {
            $feature['properties']['NO_APPROVED_DATA'] = true;
            $feature['properties']['NO_DATA'] = true;
            $feature['properties']['YEAR'] = date('Y');
            $feature['properties']['UNDERWEIGHT'] = null;
            $feature['properties']['WASTED'] = null;
            $feature['properties']['OVERWEIGHT_OBESE'] = null;
            $feature['properties']['STUNTED'] = null;
            $feature['properties']['USERS_CONTRIBUTED'] = 0;
            $userFeatures[] = $feature;
            break;
        }
    }
}

// ---------------------------------------------------------
// 5. Output
// ---------------------------------------------------------
echo json_encode([
    "type" => "FeatureCollection",
    "features" => $userFeatures,
    "metadata" => [
        "user_barangay" => $userBarangay,
        "years_found" => array_keys($lookup),
        "population_type" => "preschool"
    ]
]);
?>