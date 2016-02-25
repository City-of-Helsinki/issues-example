<?php

require 'utils/open311_utils.php';

$format = $_REQUEST["format"];
//print "format: " . $format;
header('Content-Type: application/json; charset=utf-8');

$requests_file = "requests.csv";
$services_file = "services.json";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	$service_request_id = hash('ripemd160', rand() . time());;
	$api_key = $_POST["api_key"];
	$first_name = $_POST["first_name"];
	$last_name = $_POST["last_name"];
	$description = $_POST["description"];
	$title = $_POST["title"];
	$address_string = $_POST["address_string"];
	$zipcode = $_POST["zipcode"];
	$lat = $_POST["lat"];
	$long = $_POST["long"];
	$service_object_id = $_POST["service_object_id"];
	$service_object_type = $_POST["service_object_type"];
	$requested_datetime = date(DATE_ATOM, time());;
	$updated_datetime = $requested_datetime;
	$status = "open";
	$media_url = $_POST["media_url"];
	$base_url = "http://dev.hel.fi/open311-test/v1/data/";
	
	// Check if API Key is correct
	if (check_api_key($_POST["api_key"])) {
		$api_key = $_POST["api_key"];
	} else {
		error(400,"Missing or Invalid API Key");
		exit;
	}
	
	//Check if service_code is correct
	if (check_service_code($_POST["service_code"])) {
		$service_code = $_POST["service_code"];
	} else {
		error(404,"Service_code was not found.");
		exit;
	}

	//Load media from media_url
	if ($media_url !="") {
		//create filename for storing
		$h = hash('ripemd160', rand() . time());
                $output = "data/" . $h;
		file_put_contents($output, file_get_contents($media_url));
		$media_url = $base_url . $h;

	}

	//Sotre media from multipart form post
	foreach ($_FILES["media"]["error"] as $key => $error) {
    		if ($error == UPLOAD_ERR_OK) {
	        	$tmp_name = $_FILES["media"]["tmp_name"][$key];
        		$name = $_FILES["media"]["name"][$key];
			//create filename for storing
			$h = hash('ripemd160', $tmp_name );
			$d = "data/" . $h;
			$media_url = $base_url . $h;
			$b = move_uploaded_file($tmp_name, $d);
    		}
	}
        $escaped = array();
        $arr = array(
             $service_request_id,
             $lat,
             $long,
             $address_string,
             $zipcode,
             $title,
             $description,
             $status,
             $detailed_status,
             $status_notes,
             $service_code,
             $requested_datetime,
             $updated_datetime,
             $service_object_id,
             $service_object_type,
             $media_url);
        foreach ($arr as $val) {
             array_push($escaped, json_encode($val));
        }
        $string_data = implode(',', $escaped);
        $string_data .= "\n";

	//echo $stringData . "\n";

	$fh = fopen($requests_file, 'a');
	fwrite($fh, $string_data);
	fclose($fh);
	print "[{\"service_request_id\":\"$service_request_id\",\"service_notice\":\"\"}]";
	
} else if ($_SERVER['REQUEST_METHOD'] == 'GET'){
	$extensions = ($_GET["extensions"] == "true" ? true : false);
	$handle = fopen($requests_file, "r");
	print "[\n";
	if ($handle) {
		$row = 0;
 		while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
        		if ($row != 0) print ",\n"; 
			$row++;
			print_service_request($data,$extensions,$format);
			//print print_r($data) . "\n";  
  		}
	} else {
    		// error opening the file.
	} 
	
	fclose($handle);
	
	print "]";
} else {
	error(400,"The URL request is invalid or service is not running or reachable. Client should notify us after checking URL.");
}


?>

