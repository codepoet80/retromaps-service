<?php
$config = include('config.php');
$bingKey = $config['bingAPIKey'];
$ipinfoKey = $config['ipinfoKey'];
include ("common.php");
?>
<html>  
  <head>  
    <title>webOS Maps Test</title>  
  </head>  
  <body>  
    <?php

    $useLoc = geolocateByIP(getVisitorIP(), $ipinfoKey);
 
    if (isset($_POST['query'])) {
      $useLoc = $_POST['query'];
    }

    $zoomLevel = 5;
    if (isset($_POST['zoom'])) {
      $zoomLevel = $_POST['zoom'];
    }
    ?>
    <form method="post">  
      Address: <input type="text" style="width:280px" name="query" value="<?php echo $useLoc ?>"><br>
      Zoom Level: <select name="zoom">
        <option value="<?php echo $zoomLevel; ?>">[<?php echo $zoomLevel; ?>]</option>
        <option value="4">4</option>
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
      </select>&nbsp;&nbsp;
      Map Type: <select name="maptype">
        <option value="Road">Road</option>
        <option value="Aerial">Aerial</option>
      </select><br/>
        Custom API Key: <input type="text" name="key" value=""> (Leave blank to use test key)<br>  
      <input type="submit" value="Submit">  
  </form>  
<?php  
  
if(isset($useLoc))  
  {  
    if (isset($_POST['key']) && $_POST['key'] != "") {
      $bingKey = $_POST['key'];
    }

    $zoomLevel = "9";
    if (isset($_POST['zoom'])) {
      $zoomLevel = $_POST['zoom'];
    }

    $mapType = "Road";
    if (isset($_POST['maptype'])) {
      $mapType = $_POST['maptype'];
    }

    $mapInfo = getDataForLocation($useLoc, $mapType, $zoomLevel, $bingKey);    
    echo "<img src='" . $mapInfo->img . "'>";
}  
?>  
</body>  
</html>