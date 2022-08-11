<?php
$config = include('config.php');
include ("common.php");
$bingKey = $config['bingAPIKey'];
$ipinfoKey = $config['ipinfoKey'];
$mapType = $config['defaultMapType'];
$mapSize = $config['defaultMapSize'];
$zoomLevel = $config['defaultZoomLevel'];
?>
<html>  
  <head>  
    <title>webOS Maps</title>
    <link rel="shortcut icon" sizes="256x256" href="icon-256.png">
    <link rel="shortcut icon" sizes="196x196" href="icon-196.png">
    <link rel="shortcut icon" sizes="128x128" href="icon-128.png">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" type="image/png" href="icon.png" />
    <link rel="apple-touch-icon" href="icon.png"/>
    <link rel="apple-touch-startup-image" href="icon-256.png">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="white" />
    
    <link rel="stylesheet" href="style.css">
    <script>
      function findScreenSize() {
        document.getElementById("screenSize").value = window.innerWidth;
      }
    </script>
  </head>  
  <body onload="findScreenSize()">  
    <?php
    //Figure out what protocol the client wanted
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
      echo file_get_contents("https://www.webosarchive.org/menu.php?content=maps");
      $REQUEST_PROTOCOL = "https";
    }
    else {
      echo file_get_contents("http://www.webosarchive.org/menu.php?content=maps");
      $REQUEST_PROTOCOL = "http";
    }
    $useLoc = geolocateByIP(getVisitorIP($config['hostname']), $ipinfoKey);

    if (isset($_POST['query'])) {
      $useLoc = $_POST['query'];
    }

    if (isset($_POST['zoom'])) {
      $zoomLevel = $_POST['zoom'];
    }

    if (isset($_POST['screenSize']) && $_POST['screenSize'] != "") {
      $mapSize = round((int)$_POST['screenSize'] * 0.7);
      $mapSize = $mapSize . "," . $mapSize;
    }
    ?>
    <div class="content">
      <p align='middle' style='margin-top:50px;'>
        <a href='../'>
        <img src='icon-128.png' style="width:128px; height: 128px;" border="0"></a><br/>
        <strong>Retro Maps</strong><br/>
        <small>A project of <a href="<?php echo $REQUEST_PROTOCOL; ?>://www.webosarchive.org">webOS Archive</a></small><br>
    <br/>

      </p>
      <form method="post">
        <table border="0" cellpadding="0" cellspacing="0" class="content" style="margin: 0 auto;">
            <tr><td>Address: </td><td><input type="text" style="width:200px" name="query" value="<?php echo $useLoc ?>"></td></tr>
            <tr><td>Zoom Level: </td><td>&nbsp;<select name="zoom">
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
          </select></td></tr>
          <!--
                <tr><td>Custom API Key: </td><td><input type="text" name="key" value=""> <i>Leave blank to use test key</i></td></tr> 
          -->
          <tr><td colspan="3" align="center"><input type="submit" value="Update Map"></td></tr>
        </table> 
        <input type="hidden" name="screenSize" id="screenSize" style="display:none"/> 
    </form>  
    
  <?php  
    
  if(isset($useLoc))  
    {  
      if (isset($_POST['key']) && $_POST['key'] != "") {
        $bingKey = $_POST['key'];
      }

      if (isset($_POST['maptype'])) {
        $mapType = $_POST['maptype'];
      }

      if (isset($_POST['mapsize'])) {
        $mapSize = $_POST['mapsize'];
      }

      $mapInfo = getDataForLocation($useLoc, $mapType, $mapSize, ";36", $zoomLevel, $bingKey);
      echo "<p align='middle'><img src='" . $mapInfo->img . "' style='margin: 0 auto; border-radius:2%; -webkit-border-radius:10px '></p>";
      echo "<!--";
      print_r($mapInfo);
      echo "-->";
  }  
  ?>  

<p align='middle' style="margin-top: 28px"><small>Location provided by <a href='https://ipinfo.io'>IPInfo</a>, Maps provided by <a href='https://docs.microsoft.com/en-us/bingmaps/articles/accessing-the-bing-maps-rest-services-using-php'>Bing</a> | <a href="https://github.com/codepoet80/retromaps-service">Host this yourself</a> | <a href='<?php echo $REQUEST_PROTOCOL; ?>://appcatalog.webosarchive.org/app/maplite'>Download the webOS App</a></small></p>
  </div>
</body>  
</html>