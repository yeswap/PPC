<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <style type="text/css">
    	html{
    	  width:100%;
    	  padding:0;
    	  margin:0;
    	}
    	body{
    	  height:100%;
    	  padding:1em;
    	  background-color:white;
    	}
    	textarea{ 
    	  width:100%;
      }
    </style>
    <title>Add News Item</title>
  </head>
  <body>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set("display_errors", 1); # 0 - production, 1 - development

// The Use of fetcher.function.php - Oritro Ahmed
//[www.oritro.com]
//Filename: fetcher.php
//Usefull for Link Shortener, link generator, Backlink generator
// A part Of Tankz Easy PHP Use Scripts 
require_once('fetcher.function.php');
function url_get_contents ($Url) {
    if (!function_exists('curl_init')){ 
        die('CURL is not installed!');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}    

$url = 'http://www.callingmart.com/promo/';
$content = url_get_contents($url);
$title = getMetaTitle($content);
//$description = getMetaDescription($content);
$excerpt = getExcerpt($content);
print "title ==> $title ";
print "<br>";
//print "description ==> $description ";
print "<br>";
print "excerpt ==> $excerpt";
?>
  </body>
</html>