<?php
// nearby.php
// Expects POST { lat, lng, radius(optional meters) }
// Returns JSON list of places (temples) from Google Places Nearby Search
header('Content-Type: application/json');

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST only']);
    exit;
}

// Read JSON body
$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (!$data) $data = $_POST;

$lat = isset($data['lat']) ? floatval($data['lat']) : null;
$lng = isset($data['lng']) ? floatval($data['lng']) : null;
$radius = isset($data['radius']) ? intval($data['radius']) : 3000; // default 3km

if (!$lat || !$lng) {
    http_response_code(400);
    echo json_encode(['error' => 'lat and lng required']);
    exit;
}

// Put your server key here (IMPORTANT: keep secret and restrict in Google Console)
$GOOGLE_SERVER_KEY = 'YOUR_GOOGLE_SERVER_KEY_HERE';

// Build Places Nearby Search request
$location = $lat . ',' . $lng;
$type = 'place_of_worship'; // temples are place_of_worship (you can filter by keyword 'temple')
$keyword = 'temple';

// Use nearbysearch
$url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json"
    . "?location=" . urlencode($location)
    . "&radius=" . urlencode($radius)
    . "&type=" . urlencode($type)
    . "&keyword=" . urlencode($keyword)
    . "&key=" . urlencode($GOOGLE_SERVER_KEY);

// Perform request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    http_response_code(500);
    echo json_encode(['error' => 'Request failed', 'details' => $err]);
    exit;
}

// Optionally: you can parse and trim fields before returning
$places = json_decode($response, true);
if (!$places) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid response from Google']);
    exit;
}

// Return the full Google Places response (or a trimmed structure)
echo json_encode($places);
