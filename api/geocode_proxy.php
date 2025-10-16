<?php
// geocode_proxy.php
header('Content-Type: application/json');
$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;

if(!$lat || !$lon){
    echo json_encode(['error'=>'Missing lat/lon']);
    exit;
}

// Use Nominatim reverse geocoding
$ch = curl_init("https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=$lat&lon=$lon");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'DeliveryApp/1.0');
$response = curl_exec($ch);
curl_close($ch);
echo $response;
?>
