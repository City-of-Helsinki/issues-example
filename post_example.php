<?php 

//Use this to send test environment
$url = 'https://pate.affecto.com/restWAR/open311/v1/requests.json';

// Use this to send to production environment
//$url = 'https://asiointi.hel.fi/palautews/rest/v1/requests.json';

$ch = curl_init();

$data = array(
    'api_key' => 'YOUR_API_KEY',
    'service_code' => '202',
    'description' => 'Please fix this traffic sign!',
    'lat' => '60.168321',
    'long' => '24.952397',
    'email' => 'jaakko.rajaniemi@hel.fi',
    'name' => 'Jaakko Rajaniemi'
);

$opts = array(  CURLOPT_URL => $url,
       	CURLOPT_RETURNTRANSFER => 1,
       	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $data,
	CURLOPT_SSLVERSION => 1);

curl_setopt_array($ch, $opts);
$json = curl_exec($ch);
$json = json_decode($json);
$info = curl_getinfo($ch);

if ($info['http_code'] == 200){
   print "SUCCESS\n";
   foreach ($json as $value) {
	print "Service request ID: " . $value->service_request_id . "\n";
   }
}

?>
