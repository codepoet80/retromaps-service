<?php
function base64url_encode($data)
{
  // First of all you should encode $data to Base64 string
  $b64 = base64_encode($data);

  // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
  if ($b64 === false) {
    return false;
  }

  // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
  $url = strtr($b64, '+/', '-_');

  // Remove padding character from the end of line and return the Base64URL result
  return rtrim($url, '=');
}

function base64url_decode($data, $strict = false)
{
  // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
  $b64 = strtr($data, '-_', '+/');

  // Decode Base64 string and return the original data
  return base64_decode($b64, $strict);
}

function getVisitorIP($hostname)
{
  $serverIP = explode('.',$_SERVER['SERVER_ADDR']);
  $localIP  = explode('.',$_SERVER['REMOTE_ADDR']);
  $isLocal = ( ($_SERVER['SERVER_NAME'] == 'localhost') ||
    ($serverIP[0] == $localIP[0]) && 
    (in_array($serverIP[0],array('192') ) ||
    in_array($serverIP[0],array('127') ) ) 
  );
  if($isLocal)
  {
      $visitorIP = gethostbyname($hostname);
  }
  else 
  {
    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $visitorIP=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $visitorIP=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $visitorIP=$_SERVER['REMOTE_ADDR'];
    }
  }
  return $visitorIP;
}

function get_request_headers() {
	//Cross platform way to get request headers, thanks to https://stackoverflow.com/a/20164575/8216691
	$request_headers = [];
	if (!function_exists('getallheaders')) {
	    foreach ($_SERVER as $name => $value) {
	        /* RFC2616 (HTTP/1.1) defines header fields as case-insensitive entities. */
	        if (strtolower(substr($name, 0, 5)) == 'http_') {
	            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
	        }
	    }
	    $request_headers = $headers;
	} else {
	    $request_headers = getallheaders();
	}
	return $request_headers;
}

function geolocateByIP($ip, $ipinfoKey)
{
  //Make the request to an API endpoint
  $ch = curl_init();
  $the_query = "http://ipinfo.io/" . $ip . "?token=" . $ipinfoKey;
  curl_setopt($ch, CURLOPT_URL, $the_query);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = curl_exec ($ch);
  curl_close ($ch);
  $loc_response = json_decode($response, true);
  if (isset($loc_response["loc"])) {
    return $loc_response["loc"];
  }
}

//valid imagerySets: Road, Aerial, AerialWithLabels
function getDataForLocation($useLoc, $imagerySet, $mapSize, $pushPin, $zoomLevel, $bingKey)
{
  if ($useLoc!= "")//if query value is provided, find location using query  
  {  
    if (!isset($imagerySet) || $imagerySet == "")
      $imagerySet = "Road";

    // Create URL to find a location by query  
    $query = str_ireplace(" ","%20",$useLoc);
    $baseURL = "http://dev.virtualearth.net/REST/v1/Locations"; 
    $findURL = $baseURL."/".$query."?output=xml&key=".$bingKey;    
    $output = file_get_contents($findURL);
    $response = new SimpleXMLElement($output);  

    $latitude = $response->ResourceSets->ResourceSet->Resources->Location->Point->Latitude;
    $longitude = $response->ResourceSets->ResourceSet->Resources->Location->Point->Longitude;
    $imageryBaseURL = "http://dev.virtualearth.net/REST/v1/Imagery/Map";  
    $centerPoint = $latitude.",".$longitude;

    $imgOrig = $imageryBaseURL."/".$imagerySet."/".$centerPoint."/".$zoomLevel."?mapSize=" . $mapSize;
    if (!isset($pushPin) || empty($pushPin) || $pushPin == "" || $pushPin == false || strtolower($pushPin) == "false" || strtolower($pushPin) == "off" || strtolower($pushPin) == "no")
      $imgOrig .= "&key=";
    else
      $imgOrig .= "&pushpin=".$centerPoint . $pushPin ."&key=";
    $img = getBaseURLPath() . "getmapimage.php?img=" . base64url_encode($imgOrig);

    $data = (object) [
      'latitude' => (string) $latitude,
      'longitude' => (string) $longitude,
      'centerpoint' => (string) $centerPoint,
      'zoomLevel' => $zoomLevel,
      'mapType' => $imagerySet,
      'img' => $img
    ];
    return $data;
  } 
}

function getBaseURLPath()
{
  $uri = $_SERVER['REQUEST_URI'];
  $uriParts = explode("/", $uri);
  $uri = str_replace(end($uriParts), "", $uri);
  $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  $url = $protocol . $_SERVER['HTTP_HOST'] . $uri;
  return $url;
}

function LatLongToPixel(&$x, &$y, $lat, $long, $clat, $clong, $zoom, $mapWidth, $mapHeight)  
{  
  $sinLatCenter = sin($clat * pi() / 180);  
  $pixelXCenter = ($clong + 180) / 360 * 256 * pow(2,$zoom);  
  $pixelYCenter = (0.5 - log((1 + $sinLatCenter) / (1-$sinLatCenter)) / (4*pi())) * 256 * pow(2,$zoom);  
    
  $sinLat = sin($lat * pi() / 180);  
  $pixelX = ($long + 180) / 360 * 256 * pow(2,$zoom);  
  $pixelY = (0.5 - log((1 + $sinLat) / (1-$sinLat)) / (4*pi())) * 256 * pow(2,$zoom);  
    
  $topLeftPixelX = $pixelXCenter - $mapWidth / 2;  
  $topLeftPixelY = $pixelYCenter - $mapHeight /2;  
  $x = $pixelX - $topLeftPixelX;  
  $y = $pixelY - $topLeftPixelY;  
}  
  
function haversineDistance($lat1, $lon1, $lat2, $lon2)  
{  
  $radius = 6371;  
  $factor = pi() / 180;  
  $dLat = ($lat2-$lat1)*$factor;  
  $dLon = ($lon2-$lon1)*$factor;  
  $a = sin($dLat/2) * sin($dLat/2) + cos($lat1*$factor) * cos($lat2*$factor) * sin($dLon/2) * sin($dLon/2);  
  $c = 2 * atan2(sqrt($a), sqrt(1-$a));  
  return $radius*$c;  
}  
  
function calculateView($points)  
{  
  
  global $mapWidth, $mapHeight;  
  
      $maxLat = -90;  
      $minLat = 90;  
      $maxLon = -180;  
      $minLon = 180;  
  
      $defaultScales = array(78.27152, 39.13576, 19.56788, 9.78394, 4.89197, 2.44598, 1.22299, 0.61150, 0.30575, 0.15287, .07644, 0.03822, 0.01911, 0.00955, 0.00478, 0.00239, 0.00119, 0.0006, 0.0003);  
  
  // calculate bounding box for array of locations  
  for($i = 0; $i < count($points); $i++)  
      {  
        if ($points[$i]->Latitude > $maxLat) $maxLat = $points[$i]->Latitude;  
        if ($points[$i]->Latitude < $minLat) $minLat = $points[$i]->Latitude;  
        if ($points[$i]->Longitude > $maxLon) $maxLon = $points[$i]->Longitude;  
        if ($points[$i]->Longitude < $minLon) $minLon = $points[$i]->Longitude;  
  }  
  
  // calculate center coordinate of bounding box  
  $centerLat = ($maxLat + $minLat) / 2;  
  $centerLon = ($maxLon + $minLon) / 2;  
  
  // create a Location object for the center point  
  $centerPoint = array(  
    'Latitude' => $centerLat,  
    'Longitude' => $centerLon  
  );  
  
  // want to calculate the distance in km along the center latitude between the two longitudes  
  $meanDistanceX = haversineDistance($centerLat, $minLon, $centerLat, $maxLon);  
  
  // want to calculate the distance in km along the center longitude between the two latitudes  
  $meanDistanceY = haversineDistance($maxLat, $centerLon, $minLat, $centerLon) * 2;  
  
  // calculate the X and Y scales  
  $meanScaleValueX = $meanDistanceX / $mapWidth;  
  $meanScaleValueY = $meanDistanceY / $mapHeight;  
  
  // gets the largest scale value to work with  
  if ($meanScaleValueX > $meanScaleValueY)   
    $meanScale = $meanScaleValueX;  
  else  
    $meanScale = $meanScaleValueY;  
  
      // initialize zoom level variable  
  $zoom = 1;  
  
  // calculate zoom level  
  for ($i = 1; $i < 19; $i++) {  
    if ($meanScale >= $defaultScales[$i]) {$zoom = $i; break;}  
  }  
  
  // return a BestView "object" with the center point and zoom level to use  
  $bestView = array(  
    'CenterPoint' => $centerPoint,  
'Zoom' => $zoom  
  );  
  
  return $bestView;  
}  
  
function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)  
{  
    /* this way it works well only for orthogonal lines  
    imagesetthickness($image, $thick);  
    return imageline($image, $x1, $y1, $x2, $y2, $color);  
    */  
    if ($thick == 1) {  
        return imageline($image, $x1, $y1, $x2, $y2, $color);  
    }  
    $t = $thick / 2 - 0.5;  
    if ($x1 == $x2 || $y1 == $y2) {  
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);  
    }  
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q  
    $a = $t / sqrt(1 + pow($k, 2));  
    $points = array(  
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),  
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),  
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),  
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),  
    );  
    imagefilledpolygon($image, $points, 4, $color);  
    return imagepolygon($image, $points, 4, $color);  
}
?>