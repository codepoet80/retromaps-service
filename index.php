<?php
$config = include('config.php');
$key = $config['apikey'];
?>
<html>  
  <head>  
    <title>webOS Maps Test</title>  
  </head>  
  <body>  
    <form method="post">  
      Address: <input type="text" style="width:280px" name="query" value="<?php echo (isset($_POST['query'])?$_POST['query']:'') ?>"><br>
      Zoom Level: <select name="zoom" value="15">
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        </select><br/>
        Custom API Key: <input type="text" name="key" value=""> (Leave blank to use test key)<br>  
      <input type="submit" value="Submit">  
  </form>  
<?php  
  
if(isset($_POST['query']))  
  {  
    if (isset($_POST['key']) && $_POST['key'] != "") {
      $key = $_POST['key'];
    }
    // URL of Bing Maps REST Locations API;   
    $baseURL = "http://dev.virtualearth.net/REST/v1/Locations";   
  
  if ($_POST['query']!= "")//if query value is provided, find location using query  
  {  
   // Create URL to find a location by query  
    $query = str_ireplace(" ","%20",$_POST['query']);  
    $findURL = $baseURL."/".$query."?output=xml&key=".$key;  
  }  
  else //if query value is not provided, find location using specified US address values  
    // Create a URL to find a location by address  
  {  
    $country = "US";  
    $addressLine = str_ireplace(" ","%20",$_POST['address']);  
    $adminDistrict = str_ireplace(" ","%20",$_POST['state']);  
    $locality = str_ireplace(" ","%20",$_POST['city']);  
    $postalCode = str_ireplace(" ","%20",$_POST['zipcode']);    
    // Construct final URL for call to Locations API  
    $findURL = $baseURL."/".$country."/".$adminDistrict."/".$postalCode."/".$locality."/".$addressLine."?output=xml&key=".$key;  
  }  
  
   // Get output from URL and convert to XML element using php_xml  
   $output = file_get_contents($findURL);
   $response = new SimpleXMLElement($output);  
  
  // Extract and pring latitude and longitude coordinates from results  
  $latitude = $response->ResourceSets->ResourceSet->Resources->Location->Point->Latitude;  
  $longitude = $response->ResourceSets->ResourceSet->Resources->Location->Point->Longitude;  
  
  echo "Latitude: ".$latitude."<br>";  
  echo "Longitude: ".$longitude."<br>";  
  
  // Display the location on a map using the Imagery API  
  $imageryBaseURL = "http://dev.virtualearth.net/REST/v1/Imagery/Map";  
  //http://dev.virtualearth.net/REST/v1/Imagery/Map/imagerySet/centerPoint/zoomLevel=zoomLevel&mapSize=mapSize&pushpin=pushpin&mapLayer=mapLayer&key={BingMapsKey}
  
  $imagerySet = "Road";  //Aerial, AerialWithLabels
  $centerPoint = $latitude.",".$longitude;  
  $pushpin = $centerPoint.";4;ID";  
  $zoomLevel = "5";
  if (isset($_POST['zoom'])) {
    $zoomLevel = $_POST['zoom'];
  }
  
  echo "<img src='".$imageryURL = $imageryBaseURL."/".$imagerySet."/".$centerPoint."/".$zoomLevel."?pushpin=".$pushpin."&mapSize=1024,768&key=".$key."'>";  
  
}  
else  
{  
  echo "<p>Please enter your Bing Maps key and complete all address fields for a US address or the Query field, then click submit.</p>";  
}  
?>  
</body>  
</html>