<?php

// A simple Function To Fetch Details Of a Webpage- Oritro Ahmed
//[www.oritro.com]
//Filename: fetcher.function.php
//Usefull for Link Shortener, link generator, Backlink generator
// A part Of Tankz Easy PHP Use Scripts 

// This Will Bring the <title></title> Of the Webpage

function getMetaTitle($content){
$pattern = "|<[\s]*title[\s]*>([^<]+)<[\s]*/[\s]*title[\s]*>|Ui";
if(preg_match($pattern, $content, $match))
return $match[1];
else
return false;
}

// This Will Bring The Description 


//Basically It will Read the Entire page first
//Then it will look for the Body Tag <p> </p>
//And Bring Whatever Inside It.

function getExcerpt($content) {
$text = html_entity_decode($content);
$excerpt = array();
//match all tags
preg_match_all("|<[^>]+>(.*)]+>|", $text, $p, PREG_PATTERN_ORDER);
for ($x = 0; $x < sizeof($p[0]); $x++) {
if (preg_match('< body >i', $p[0][$x])) {
$strip = strip_tags($p[0][$x]);
if (preg_match("/\./", $strip))
$excerpt[] = $strip;
}
if (isset($excerpt[0])){
preg_match("/([^.]+.)/", $strip,$matches);
return $matches[1];
}
}
return false;
}

?>


