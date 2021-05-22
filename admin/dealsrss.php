<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set("error_log", "/usr/home/yeswap/php-error.log");
error_reporting(E_ALL & ~E_NOTICE);
//write PPC RSS feed headers
$rssFile=fopen("../deals/feed.rss", "w");
fwrite($rssFile, '<?xml version="1.0" encoding="utf-8"?>'."\n");
fwrite($rssFile, '<rss version="2.0"  xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/">'."\n");
fwrite($rssFile, "<channel>\n");
fwrite($rssFile, "<title>Prepaid Deals</title>\n");
fwrite($rssFile, "<link>https://prepaidcompare.net/deals/</link>\n");
fwrite($rssFile, "<language>en-us</language>\n");
$pubDate= date('r', time());
fwrite($rssFile, "<pubDate>$pubDate</pubDate>\n");
fwrite($rssFile, "<lastBuildDate>$pubDate</lastBuildDate>\n");
fwrite($rssFile,'<atom:link href="https://prepaidcompare.net/deals/feed.rss" rel="self" type="application/rss+xml" />'."\n");
fwrite($rssFile, "<description>This week's best online deals on unlocked and prepaid phones and plans. </description>\n");
fwrite($rssFile, "<item>\n");
fwrite($rssFile, "<title><![CDATA[PrepaidCompare - Online Prepaid and Unlocked Phone Deals]]></title>\n");
fwrite($rssFile, "<link>https://prepaidcompare.net/deals/</link>\n");
$uid = uniqid();
fwrite($rssFile, "<guid isPermaLink='false'>prepaidcompare.net/deals/{$uid}</guid>\n");
fwrite($rssFile, "<dc:creator>Dennis</dc:creator>\n");
$innerHTML = '';
$doc = new DOMDocument();
$doc->loadHTMLFile("../deals/index.html");
$blurb = getContent($doc->getElementById("blurb"));
$elem = getContent($doc->getElementById("content"));

Function getContent($elem){
    // loop through all childNodes, getting html
    $children = $elem->childNodes;
    foreach ($children as $child) {
        $tmp_doc = new DOMDocument();
        $tmp_doc->appendChild($tmp_doc->importNode($child,true));
        $innerHTML .= $tmp_doc->saveHTML();
    }
    return str_replace(array("\r", "\n"), '', $innerHTML);
}

fwrite($rssFile, "<description><![CDATA[$blurb...]]></description>\n");
fwrite($rssFile, "<content:encoded><![CDATA[$elem]]></content:encoded>\n");
fwrite($rssFile, "<pubDate>$pubDate</pubDate>\n");
fwrite($rssFile, "</item>\n");


fwrite($rssFile, "</channel>\n");
fwrite($rssFile, "</rss>\n");
fclose($rssFile);
?>
<html>
  <head>
    <title>RSS updated</title>
</head>
  <h1>RSS updated</h1>
  <p><a class="button" href=backupdeals.php">Backup Deals page</a></p>
</html>

