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
    echo json_encode(["error" => "GeoJSON not found"]);
    exit;
}

$geojson = json_decode(file_get_contents($geojsonPath), true);

// ---------------------------------------------------------
// 2. Get all approved reports for this ONLY barangay
// ---------------------------------------------------------
$sql = "SELECT 
            b.barangay,
            b.year,
            b.ind9b1_pct, b.ind9b2_pct, b.ind9b3_pct,
            b.ind9b4_pct, b.ind9b5_pct,
            b.ind9b6_pct, b.ind9b7_pct,
            b.ind9b8_pct, b.ind9b9_pct
        FROM bns_reports b
        JOIN reports r ON b.report_id = r.id
        WHERE r.status = 'approved'
          AND UPPER(b.barangay) = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userBarangay]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------------------------------------
// 3. Merged indicators
// ---------------------------------------------------------
$mergedIndicators = [
    'UNDERWEIGHT'       => ['IND9B1_PCT', 'IND9B2_PCT'],
    'NORMAL'            => ['IND9B3_PCT'],
    'WASTED'            => ['IND9B4_PCT', 'IND9B5_PCT'],
    'OVERWEIGHT_OBESE'  => ['IND9B6_PCT', 'IND9B7_PCT'],
    'STUNTED'           => ['IND9B8_PCT', 'IND9B9_PCT']
];

// ---------------------------------------------------------
// 4. Group data by year for this barangay
// ---------------------------------------------------------
$lookup = [];
foreach ($data as $row) {
    $year = $row['year'];

    $rowUpper = [];
    foreach ($row as $k => $v) {
        $rowUpper[strtoupper($k)] = $v;
    }

    foreach ($mergedIndicators as $mergedKey => $fields) {
        $sum = 0;
        foreach ($fields as $f) {
            if (isset($rowUpper[$f])) $sum += floatval($rowUpper[$f]);
        }
        $rowUpper[$mergedKey] = $sum;
    }

    $lookup[$year] = $rowUpper;
}

// ---------------------------------------------------------
// 5. Filter GeoJSON: keep ONLY the user's barangay
// ---------------------------------------------------------
$userFeatures = [];
foreach ($geojson['features'] as $feature) {
    $bName = strtoupper(trim($feature['properties']['BARANGAY']));

    if ($bName !== $userBarangay) continue; // 🔥 skip others!

    // If no approved data → return as is
    if (!isset($lookup) || empty($lookup)) {
        $feature['properties']['NO_APPROVED_DATA'] = true;
        $userFeatures[] = $feature;
        continue;
    }

    // Create one feature PER YEAR
    foreach ($lookup as $year => $vals) {
        $newFeature = $feature;

        foreach ($vals as $key => $val) {
            if ($key !== 'BARANGAY') {
                $newFeature['properties'][$key] = $val;
            }
        }

        $newFeature['properties']['YEAR'] = $year;

        $userFeatures[] = $newFeature;
    }
}

// ---------------------------------------------------------
// 6. Output only user's barangay features
// ---------------------------------------------------------
echo json_encode([
    "type" => "FeatureCollection",
    "features" => $userFeatures
]);
?>
