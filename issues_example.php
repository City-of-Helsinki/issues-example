<!DOCTYPE html>
<html>
<head>
	<title>Issue reporting API example</title>
	<meta charset="utf-8" />

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.css" />
 <!--[if lte IE 8]>
     <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.ie.css" />
 <![endif]-->
 <script src="http://cdn.leafletjs.com/leaflet-0.5/leaflet.js"></script>

</head>
<body>
<div id="map" style="width: 600px; height: 400px"></div>
<script>
var map = L.map('map').setView([60.20, 24.93125813367534], 11);
L.tileLayer('http://{s}.tile.cloudmade.com/31d379fb8a444330931fb9f0baa6411f/998/256/{z}/{x}/{y}.png', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
    maxZoom: 18
}).addTo(map);
var RedIcon = L.Icon.Default.extend({
    options: {
    	iconUrl: 'icons/marker-icon-red.png' 
    }
});
var redIcon = new RedIcon();
<?php

function escape_marker_text($text){
	return preg_replace(array('/</', '/>/', '/"/', '/&/', '/\'/', '/\n/', '/\r/'), array('&lt;', '&gt;', '&quot;' , '&amp;', '&quot;', '<br>', '<br>'), $text);
}

$url = 'https://asiointi.hel.fi/palautews/rest/v1/requests.json';
$ch = curl_init();

// Curl settings
// SSL version 1 is TLS 1.0
$opts = array(CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_SSLVERSION => 1);
curl_setopt_array($ch, $opts);

$json = curl_exec($ch);
$json = json_decode($json);
foreach ($json as $value) {
	$lat = $value->lat;
	$lon = $value->long;
	$status = $value->status;

	//Check if status_notes is available
	$status_notes =  (isset($value->status_notes) ? $value->status_notes : "");
	$status_notes = escape_marker_text($status_notes);
	
	$description = $value->description;
	$description = escape_marker_text($description);
	
	$marker = "var marker = L.marker([". $lat . "," . $lon . "]).addTo(map);\n"; 
	$popup = "marker.bindPopup(\"". $description . "<br/><br/><strong>Kaupungin vastaus:</strong><br/>" .$status_notes . "\");\n";
	print $marker;
	
	//If status open, then use red icon.
	if ($status == "open") print "marker.setIcon(redIcon);";
	print $popup;
}
?>
</script>
</body>
</html>
