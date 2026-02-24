<?php
header('Content-Type: application/json');
require '../db/config.php'; // adjust if needed

// 1. Load base GeoJSON
$geojsonPath = __DIR__ . '/barangay_boundary.geojson';
if (!file_exists($geojsonPath)) {
    http_response_code(500);
    echo json_encode(["error" => "GeoJSON not found"]);
    exit;
}
$geojson = json_decode(file_get_contents($geojsonPath), true);

// 2. Query all approved reports (including year)
$sql = "SELECT 
            b.barangay,
            b.year,
            b.ind9b1_pct, b.ind9b2_pct, b.ind9b3_pct,
            b.ind9b4_pct, b.ind9b5_pct, b.ind9b6_pct,
            b.ind9b7_pct, b.ind9b8_pct, b.ind9b9_pct
        FROM bns_reports b
        JOIN reports r ON b.report_id = r.id
        WHERE r.status = 'approved'";
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Define merged indicators
$mergedIndicators = [
    'UNDERWEIGHT' => ['IND9B1_PCT', 'IND9B2_PCT'],
    'NORMAL' => ['IND9B3_PCT'],
    'WASTED' => ['IND9B4_PCT', 'IND9B5_PCT'],
    'OVERWEIGHT_OBESE' => ['IND9B6_PCT', 'IND9B7_PCT'],
    'STUNTED' => ['IND9B8_PCT', 'IND9B9_PCT']
];

// 4. Group data by barangay and year
$lookup = [];
foreach ($data as $row) {
    $b = strtoupper(trim($row['barangay']));
    $y = $row['year'];
    if (!isset($lookup[$b])) $lookup[$b] = [];
    
    // Convert keys to uppercase for consistency
    $rowUpper = [];
    foreach ($row as $k => $v) $rowUpper[strtoupper($k)] = $v;
    
    // Compute merged values
    foreach ($mergedIndicators as $mergedKey => $fields) {
        $sum = 0;
        foreach ($fields as $f) {
            if (isset($rowUpper[$f])) $sum += floatval($rowUpper[$f]);
        }
        $rowUpper[$mergedKey] = $sum;
    }

    $lookup[$b][$y] = $rowUpper;
}

// 5. Create new GeoJSON features — one per (barangay, year)
$newFeatures = [];
foreach ($geojson['features'] as $feature) {
    $bName = strtoupper(trim($feature['properties']['BARANGAY']));
    if (isset($lookup[$bName])) {
        foreach ($lookup[$bName] as $year => $vals) {
            $newFeature = $feature; // copy geometry
            foreach ($vals as $key => $val) {
                if ($key !== 'BARANGAY') {
                    $newFeature['properties'][$key] = $val;
                }
            }
            $newFeature['properties']['YEAR'] = $year;
            $newFeatures[] = $newFeature;
        }
    } else {
        // Barangay with no approved data
        $feature['properties']['NO_APPROVED_DATA'] = true;
        $newFeatures[] = $feature;
    }
}

// 6. Replace original features
$geojson['features'] = $newFeatures;

// 7. Output
echo json_encode($geojson);
?>