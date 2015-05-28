<?php 

require '../utils/open311_utils.php';

$id = $_GET["id"];
$format = $_GET["format"];
$extensions = ($_GET["extensions"] == "true" ? true : false);

header('Content-Type: application/json; charset=utf-8');

$requests_file = "../requests.csv";

$handle = fopen($requests_file, "r");
print "[\n";
if ($handle) {
	$row = 0;
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        	if ($id == $data[0]) {
			if ($row != 0) print ",\n";
                        $row++;
                        print_service_request($data,$extensions,$format);
		}
        }

} else {
	// error opening the file.
}

fclose($handle);

print "]";

?>
