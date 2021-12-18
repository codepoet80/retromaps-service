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

$data = (object) [
	'returnValue' => true,
	'altitude' => -1,
	'heading' => -1,
	'horizAccuracy' => -1,
	'latitude' => -1,
	'longitude' => -1,
	'timestamp' => round(microtime(true) * 1000),
	'velocity' => -1,
	'vertAccuracy' => -1,
	'errorCode' => 0,
	'responseText' => ''
];

$request_headers = get_request_headers();
if ($client_key != '') {	//If configuration includes both client key values, enforce them
	if (!array_key_exists('Client-Id', $request_headers)) {
		$data->errorCode = 8;
		$data->responseText = "No Client-Id in request header";
		print_r (json_encode($data));
	} else {
			$request_key = $request_headers['Client-Id'];
			if (!in_array($request_key, $client_key)) {
				$data->errorCode = 6;
				$data->responseText = "Client-Id in request was not known";
				print_r (json_encode($data));		
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
	$data->errorCode = 2;
	$data->responseText = "Upstream location resolver response was empty";
	print_r (json_encode($data));
}

$locationparts = explode(",", (string)$useLoc);
$data->latitude = $locationparts[0];
$data->longitude = end($locationparts);

print_r (json_encode($data));

?>
