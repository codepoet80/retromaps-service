<?php
include ("common.php");

//Handle more specific queries
$img = null;
if (isset($_GET['img']) && $_GET['img'] != "") {
    $img = $_GET['img'];
} else { //Accept a blanket query
    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "")
        $img = $_SERVER['QUERY_STRING'];
}
if (!isset($img)) {    //Deal with no usable request
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    die;
}
$url = base64url_decode($img);

//Prepare the cache
$path = "cache";
if (!file_exists($path)) {
    mkdir($path, 0755, true);
}

//Make sure our filename isn't too long
$fullWritePath = getcwd() . "/" . $path . "/";
$availLength = 250 - strlen($fullWritePath);
$startPos = strlen($cacheID) - $availLength;
if ($startPos < 0)
    $startPos = 0;
$cacheID = substr($cacheID, $startPos);

//Fetch and cache the file if its not already cached
$path = $path . "/" . $cacheID . ".png";
if (!file_exists($path)) {
    file_put_contents($path, fopen($url, 'r'));
}

// send the right headers
$info = getimagesize($path);
header("Content-Type: " . $info['mime']);
header("Content-Length: " . filesize($path));
// dump the file and stop the script
$fp = fopen($path, 'r');
fpassthru($fp);
exit;
?>
