<?php
// geocode_forward.php - Proxy for forward geocoding using Nominatim
header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
    echo json_encode(['success' => false, 'message' => 'Missing query']);
    exit;
}

$url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&q=' . urlencode($q);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'DeliveryApp/1.0');
$response = curl_exec($ch);
if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Geocoding request failed']);
    exit;
}
curl_close($ch);

$data = json_decode($response, true);
if (!is_array($data) || count($data) === 0) {
    echo json_encode(['success' => false, 'message' => 'No results']);
    exit;
}

$first = $data[0];
echo json_encode([
    'success' => true,
    'lat' => $first['lat'] ?? null,
    'lon' => $first['lon'] ?? null,
    'display_name' => $first['display_name'] ?? null
]);
?>


