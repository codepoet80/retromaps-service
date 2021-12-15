<?php
/*
Send a Geolocate by IP search request to IPInfo
*/

//Check authentication
header('Content-Type: application/json');
include('common.php');
$config = include('config.php');
$ipinfoKey = $config['ipinfoKey'];
$client_key = $config['clientids'];

$request_headers = get_request_headers();
if ($client_key != '') {	//If configuration includes both client key values, enforce them
	if (!array_key_exists('Client-Id', $request_headers)) {
			echo "{\"status\": \"error\", \"msg\": \"ERROR: Not authorized\"}";
			die;
	} else {
			$request_key = $request_headers['Client-Id'];
			if (!in_array($request_key, $client_key)) {
					echo "{\"status\": \"error\", \"msg\": \"ERROR: No authorized user.\"}";
					echo $request_key;
					echo $client_key;
					die;
			}
	}
}

if (isset($_GET["key"]) && $_GET["key"] != "") {
	$ipinfoKey = $_GET["key"];
}

//Get location
$useLoc = geolocateByIP(getVisitorIP(), $ipinfoKey);

//Get results
$myfile = fopen($search_path, "rb");
$content = stream_get_contents($myfile);
fclose($myfile);
if (!isset($useLoc) || $useLoc == "") {
	echo "{\"status\": \"error\", \"msg\": \"ERROR: No usable response from Geolocation service. Query may have been malformed, or API quota may have been exceeded.\"}";
	die;
}

//Return results
$data = (object) [
	'location' => (string) $useLoc,
  ];
print_r (json_encode($data));

?>
