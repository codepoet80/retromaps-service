<?php
header('Content-Type: application/json');
include('common.php');
$config = include('config.php');
$bingKey = $config['bingAPIKey'];
$client_key = $config['clientids'];
$mapType = $config['defaultMapType'];
$mapSize = $config['defaultMapSize'];
$zoomLevel = $config['defaultZoomLevel'];

//Check authentication
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

//Figure the query
$mapType = "Road";
$zoomLevel = 10;
if (isset($_GET['q'])) {
	$the_query = $_GET['q'];
	if (isset($_GET["mapType"]))
		$mapType = $_GET["mapType"];
	if (isset($_GET["mapSize"]))
		$mapSize = $_GET["mapSize"];
	$pushPin = ";122";
	if (isset($_GET["pushPin"]))
		$pushPin = $_GET["pushPin"];
	if (isset($_GET["zoomLevel"]))
		$zoomLevel = $_GET["zoomLevel"];
	if (isset($_GET["key"]) && $_GET["key"] != "")
		$bingKey = $_GET["key"];
} else {
	$the_query = $_SERVER['QUERY_STRING'];
}

//Get results
$mapData = getDataForLocation($the_query, $mapType, $mapSize, $pushPin, $zoomLevel, $bingKey);
if (!isset($mapData) || $mapData == "") {
	echo "{\"status\": \"error\", \"msg\": \"ERROR: No usable response from Map service. Query may have been malformed, or API quota may have been exceeded.\"}";
	die;
}

//Collect and show the results
header('Content-Type: application/json');
print_r (json_encode($mapData));

?>
