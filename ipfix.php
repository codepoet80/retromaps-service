<?php
/*
Send a Geolocate by IP search request to IPInfo
*/

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

echo getVisitorIP();

$useLoc = geolocateByIP(getVisitorIP(), $ipinfoKey);

print_r ($useLoc);
die();

if ($_GET["q"] == "")
{
	$max = 10;
	if (isset($_GET["maxResults"]))
		$max = $_GET["maxResults"];
	$search_path = "https://youtube.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&chart=mostPopular&maxResults=" . $max . "&regionCode=US&key=". $api_key;
} else {
	$search_path = "https://www.googleapis.com/youtube/v3/search?" . $the_query . "&safeSearch=". $safeSearch . "&key=". $api_key;
}

$myfile = fopen($search_path, "rb");
$content = stream_get_contents($myfile);
fclose($myfile);
if (!isset($content) || $content == "") {
	if (isset($_GET["key"])) {
	        echo "{\"status\": \"error\", \"msg\": \"ERROR: No usable response from Google. API key not allowed or quota exceeded.\"}";
	} else {
	        echo "{\"status\": \"error\", \"msg\": \"ERROR: No usable response from Google. API quota may have been exceeded.\"}";
	}
	die;
}

$json_a = json_decode($content);
$items = $json_a->items;
$newitems = array();
foreach ($items as $item) { 
	foreach ( $item as $key => $val) {
		if ($key == "snippet"){

			$myArray = (array) $val;
			if ($myArray['liveBroadcastContent'] != 'live')
			{
				array_push($newitems, $item);
			}
		}
	 }
}
$json_a->items = $newitems;
print_r (json_encode($json_a));
?>
