<?php 

$base_url = 'https://asiointi.hel.fi/palautews/rest/v1/requests.json';
$all_ids = array();
$start_time = time();
$end_time = time();
//
$DAYS = 3;
$TIMELAP = $DAYS * 24 * 60 * 60;
$c=1;
$total=0;
$duplicates = 0;

while ($c > 0){
	$start = urlencode(date(DATE_W3C,$start_time-$TIMELAP));
	$end = urlencode(date(DATE_W3C,$start_time));
	$url = $base_url . '?start_date=' . $start . '&end_date=' . $end . "&extensions=true";
	//echo $url . "\n";
	$start_time = $start_time-$TIMELAP;
	$c = getRequests($url);
	$total=$total+$c;
}

print "Total: $total\n";
print "Duplicates: $duplicates\n";

function getRequests($url){
	global $all_ids, $duplicates;
	$ch = curl_init();
	$opts = array(  CURLOPT_URL => $url,
       	CURLOPT_RETURNTRANSFER => 1,
       	CURLOPT_TIMEOUT => 4,
	CURLOPT_SSLVERSION => 1);
	curl_setopt_array($ch, $opts);

	$json = curl_exec($ch);
	$json = json_decode($json);
	$c = count($json);
	$i=0;

	$myFile = "all_issues_test.csv";
	$fh = fopen($myFile, 'a');

	foreach ($json as $value) {
		try {
			if (isset($value->status)) $status = $value->status;
			if (isset($value->service_request_id)) $service_request_id = $value->service_request_id;
			if (isset($value->requested_datetime)) $requested_datetime = $value->requested_datetime;
			if (isset($value->updated_datetime)) $updated_datetime = $value->updated_datetime;
			if (isset( $value->service_code)) $service_code = $value->service_code;
			if (isset($value->description)) {
				$description =  $value->description;
				$description = str_replace("\"", "\"\"", $description);
			} else $description = "";

			if (isset($value->status_notes)) { 
				$status_notes =  $value->status_notes;
				$status_notes = str_replace("\"", "\"\"", $status_notes);
			} else $status_notes = "";

			//API has bugs on these queries and may give some requests twice
			//We must detect duplicates
			if (in_array($service_request_id,$all_ids)) {
				print"Duplicate: " . $service_request_id . "\n";
				echo "Query URL: " . $url . "\n";
				$duplicates++;
				continue;
			} else $all_ids[] = $service_request_id;

		if (isset($value->lat)) $lat= $value->lat;
		if (isset($value->long)) $long = $value->long;
	
		if (isset($value->extended_attributes->title)) {
			$title =  $value->extended_attributes->title;
			$title = str_replace("\"", "\"\"", $title);
		}
	
		if (isset($value->extended_attributes->detailed_status)) $detailed_status =  utf8_decode($value->extended_attributes->detailed_status);
 

		}catch (Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

	if (!isset($lat) || !isset($long)) {
		echo "No location coordinates: " . $id;
		continue;
	}
	//TODO
	$address = "";
	$zipcode = "";
	$media_url = "";
	$service_object_id = "";
	$service_object_type = "";
	
	$i++;
	$stringData =  "$service_request_id,$lat,$long,$address,$zipcode,\"$title\",\"$description\",$status,$detailed_status,\"$status_notes\",$service_code,$requested_datetime,$updated_datetime,$service_object_id,$service_object_type,$media_url\n";
	//echo $stringData ."\n";
	fwrite($fh, $stringData);
}

fclose($fh);
return $c;
}


?>
