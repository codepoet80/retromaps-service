<?php
$config = include('config.php');
include ("common.php");
$bingKey = $config['bingAPIKey'];

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
$url = $url . $bingKey;

//Prepare the cache
$path = "cache";
if (!file_exists($path)) {
    mkdir($path, 0755, true);
}

//Make sure our filename isn't too long
$fullWritePath = getcwd() . "/" . $path . "/";
$availLength = 250 - strlen($fullWritePath);
$startPos = strlen($img) - $availLength;
if ($startPos < 0)
    $startPos = 0;
$cacheID = substr($img, $startPos);

//Fetch and cache the file if its not already cached
$path = $path . "/" . $cacheID . ".jpg";
if (!file_exists($path)) {
    file_put_contents($path, fopen($url, 'r'));
}
if (filesize($path) > 0) {
    // send the right headers
    $info = getimagesize($path);
    header("Content-Type: " . $info['mime']);
    header("Content-Length: " . filesize($path));
    // dump the file and stop the script
    $fp = fopen($path, 'r');
    fpassthru($fp);
    exit;
} else {
    http_response_code(405);
    exit();
}
?>
