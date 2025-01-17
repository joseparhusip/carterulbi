<?php  
function getDistance($origin, $destination, $apiKey) {  
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . urlencode($origin) . "&destinations=" . urlencode($destination) . "&key=" . $apiKey;  
  
    $response = file_get_contents($url);  
    $data = json_decode($response, true);  
  
    if ($data['status'] == 'OK') {  
        $distance = $data['rows'][0]['elements'][0]['distance']['value']; // Distance in meters  
        $duration = $data['rows'][0]['elements'][0]['duration']['value']; // Duration in seconds  
        return [  
            'distance_km' => $distance / 1000, // Convert to kilometers  
            'duration' => $duration // Duration in seconds  
        ];  
    } else {  
        return null; // Handle error  
    }  
}  
?>  
