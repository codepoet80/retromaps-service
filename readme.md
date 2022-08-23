# Overview

A PHP service, leveraging Bing Maps and IPInfo.org to provide a podcast directory, and a proxy service, for retro devices that are capable of displaying images, but may not be able to get a geofix, load tiles or render vectors.

# Requirements

Provide your Bing Maps API key and IPInfo.org token in a file called config.php. See the config-example.php for structure.

Get your Bing Maps API credentials here: https://www.microsoft.com/en-us/maps/create-a-bing-maps-key/#basic

Get your IPInfo token here: https://ipinfo.io/

Create a `cache` folder (or symlink) that the web user can write to

# Prerequisites

* Apache (or other web server) with PHP 7
* sudo apt install php-gd
* sudo apt install php7.x-curl
* sudo apt install php7.x-xml
