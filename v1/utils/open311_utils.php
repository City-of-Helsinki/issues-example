<?php

function print_service_request($data,$extensions,$format){
	print "{\n";
        print "\"service_request_id\":\"$data[0]\",\n";
        print "\"service_code\":\"$data[10]\",\n";
        if ($data[1] != "") print "\"lat\":$data[1],\n";
        if ($data[2] != "") print "\"long\":$data[2],\n";
        print "\"address\":\"$data[3]\",\n";
        print "\"zipcode\":\"$data[4]\",\n";

	// JSON requires new line characters be escaped
	$text = str_replace("\r\n", "\n", $data[6]);
    	$text = str_replace("\r", "\n", $text);
    	$text = str_replace("\n", "\\n", $text);        
	print "\"description\":\"$text\",\n";
        print "\"status\":\"$data[7]\",\n";
        print "\"status_notes\":\"$data[9]\",\n";
        if ($data[15] != "") print "\"media_url\":\"$data[15]\",\n";
        print "\"requested_datetime\":\"$data[11]\",\n";
        print "\"updated_datetime\":\"$data[12]\"";
        if ($extensions) {
        	print ",\n\"extended_attributes\": {\n";
        	print "\"title\": \"$data[5]\",\n";
        	print "\"service_object_type\": \"$data[13]\",\n";
        	print "\"service_object_id\": \"$data[14]\",\n";
        	if ($data[15] != "") {
			print "\"media_urls\": [\n";
			$num = count($data);
			for ($c=15; $c < $num; $c++) {
				if ($c > 15) print ",\n";
           			print "\"$data[$c]\"";
        		}
			print "],\n";
		} 
		// marked as RECEIVED
		print "\"detailed_status\": \"RECEIVED\"\n";
		print "}";
        }
       	print "\n}\n";
}

function check_api_key($api_key){
        if ($api_key != "f1301b1ded935eabc5faa6a2ce975f6") return false;
	else return true;
}

function check_service_code($service_code){
        global $services_file;
        $string = file_get_contents($services_file);
        $json = json_decode($string, true);
        foreach ($json as $v) {
                if ($v['service_code'] == $service_code) return true;
        }
        return false;
}



function error($code, $message){
        http_response_code($code);
        print "[\n";
        print "{\n";
        print "\"code\":$code,";
        print "\"description\":\"$message\"";
        print "}\n";
        print "]";
}



?>
