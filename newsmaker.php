<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set("display_errors", 1); # 0 - production, 1 - development
ob_start();
// Make sure SimplePie is included. You may need to change this to match the location of simplepie.inc.
require_once('../simplepie/autoloader.php');
date_default_timezone_set('America/Los_Angeles');

// Original my Truncate PHP code by Chirp Internet: www.chirp.com.au
// Please acknowledge use of this code by including this header.

function myTruncate($string, $limit, $break=" ", $pad="...")
{
  // return with no change if string is shorter than $limit
  $string = strip_tags($string);
  if(strlen($string) <= $limit) return $string;

  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }

  return $string;
}

// We'll process this feed with all of the default options.
$feed = new SimplePie();

// Set which feed to process.

$feed->set_feed_url(array(
	'http://bestmvno.com/feed/',
	'http://prepaidmobilephonereviews.com/feed/',
	'http://feeds.feedburner.com/PrepaidPhoneNews?format=xml',
	'http://cricketwireless.mediaroom.com/news-releases?pagetemplate=rss',
	'https://www.tmonews.com/tag/prepaid/feed/',
	'https://www.tmonews.com/tag/metropcs/feed/',
	'http://blog.freedompop.com/feed/',
	'https://blog.google/products/google-fi/rss/',
	'https://bgr.com/tag/metropcs/feed/',
	'https://bgr.com/tag/boost/feed/',
	'http://www.prepaidreviews.com/blog/feed/atom/'
));

$feed->set_cache_duration (600); //The cache duration
$feed->set_useragent('Mozilla/5.0 (X11; CrOS armv7l 9765.85.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.123 Safari/537.36');

// Run SimplePie.
$feed->init();

// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
$feed->handle_content_type();

// Create an array to hold combined data in
$news = array();

// Loop through all of the items in the feed
foreach ($feed->get_items() as $item) {

	// Calculate a two weeks ago
	$oldnews = time() - (14*24*60*60);

	// Compare the timestamp of the feed item with a week ago.
	if ($item->get_date('U') > $oldnews) {
		// If the item was posted within the last week, store the item in our array we set up.

  	$creator = $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'creator');
		$content = myTruncate($item->get_content(), 220);

	  $news[] = array(
	  "title"=>$item->get_feed()->get_title(),
	  "permalink"=>$item->get_permalink(),
	  "creator"=>$creator[0]['data'],
	  "subject" =>$item->get_title(),
	  "date"=>$item->get_date('U'),
	  "content"=>$content);
  }
}
//Get user entered items from newsitems table
$connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');

if ($connection->connect_errno > 0) {
 	die ('Unable to connect to database [' . $connection->connect_error . ']');
}
$connection->query("SET time_zone='-07:00';");
$sql = "SELECT title,permalink,creator,subject,UNIX_TIMESTAMP(date) as date,content FROM NewsItems WHERE date BETWEEN ADDDATE(NOW(),INTERVAL -2 WEEK) AND NOW()";

if (!$result = $connection->query($sql)) {
  	die ('There was an error running query[' . $connection->error . ']');
}
$rows = $result->num_rows;
$cols = $result->field_count;
if($rows > 0) {
  while ($row = $result->fetch_array()) {
	  $news[] = array(
	  "title"=>$row['title'],
	  "permalink"=>$row['permalink'],
	  "creator"=>$row['creator'],
	  "subject" =>$row['subject'],
	  "date"=>$row['date'],
	  "content"=>nl2br(myTruncate($row['content'], 220)));
  }
}
// sort the array
usort($news, function($a, $b) {
    return $b['date'] - $a['date'];
});

?>
<div id = "newsHeader">
<h2 id="newsDesc"><b>The latest US prepaid mobile news from around the web</b></h2>
<?php
echo "<p id='updated'>Retrieved " . date('j-M-Y') . " at " . date('g:i a'). "</p>\n";
echo "</div>\n";
echo "<div id='newswrap'>\n";

// Loop through all of the items in the new array and display whatever we want.
	foreach($news as $item) {
?>
		<div class="item">
			<b><?php echo $item["title"]; ?> - <a href="<?php echo $item["permalink"]; ?>"><?php echo $item["subject"]; ?></a></b>

<?php
			$creator = $item["creator"];
?>

			<p><small>Posted <?php if($creator)echo 'by '. $creator . ' '; ?><?php echo date('j F Y | g:i a', $item["date"]); ?> </small></p>
			<p><?php echo $item["content"]; ?></p>
		</div>
 		<hr class="bar"/>
<?php }
echo "</div>\n";
$news = ob_get_contents();
ob_end_clean();
$newsfile = fopen("news.inc.php", "w");
fwrite($newsfile, $news);
fclose($newsfile);
ob_start();
?>
<!DOCTYPE html>
<!-- test coment -->
<!--[if IE 6]>
<html id="ie6" lang="en-US">
<![endif]-->
<!--[if IE 7]>
<html id="ie7" lang="en-US">
<![endif]-->
<!--[if IE 8]>
<html id="ie8" lang="en-US">
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html lang="en-US">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Compare Prepaid Cellphone Plans | PrepaidCompare</title>
<meta name="description" content="Find the Best Prepaid Plan for Any Budget">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">
<meta name="google-site-verification" content="DeM5wQ7YdbcrTzfRPF1HYi7TXEud4qUszziPyAYqQJk" />

<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="@yeswap" />
<meta name="twitter:title" content="Compare Prepaid Cellphone Plans" />
<meta name="twitter:description" content="Find the Best Prepaid Plan for Any Budget." />
<meta name="twitter:image" content="https://prepaidcompare.net/apple-icon.png" />

<meta property="fb:app_id" content="448013232393047"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="Compare Prepaid Cellphone Plans" />
<meta property="og:description" content="Find the Best Prepaid Plan for Any Budget." />
<meta property="og:url" content="https://prepaidcompare.net" />
<meta property="og:site_name" content="PrepaidCompare" />
<meta property="og:image" content="https://prepaidcompare.net/apple-icon.png" />

<script type="application/ld+json">
[
{
  "@context": "http://schema.org",
  "@type": "Organization",
  "url": "https://prepaidcompare.net",
  "logo": "https://prepaidcompare.net/logo.png",
  "ethicsPolicy" : "Prepaidcompare.net is non-commercial and strives to be impartial. We don't accept payment from operators and list plans solely by monthly cost. We aim to include all operators and plans that offer competitive pricing at one or more price points.",
  "potentialAction": "Find the best prepaid plan for your budget",
  "name" : "PrepaidCompare"
},
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url": "https://prepaidcompare.net/profiles/",
  "name": "Prepaid Cellphone Operator Profiles",
   "author": {
      "@type": "Person",
      "name": "Dennis Bournique",
       "sameAs" : [ "https://www.facebook.com/wapreview",
      "https://www.reddit.com/user/yeswap",
      "https://twitter.com/yeswap",
      "https://www.howardforums.com/member.php/262421-Yeswap",
      "https://plus.google.com/u/0/+DennisBournique"]
    },
  "description" : "A comprehensive database of prepaid mobile operators and plans that can be searched and filtered to show the lowest priced plans for any number of minutes, texts, data and lines of service",
  "publisher": "PrepaidCompare"
}
]
</script>
<style>
/* =Reset default browser CSS. Based on work by Eric Meyer: http://meyerweb.com/eric/tools/css/reset/index.html
-------------------------------------------------------------- */

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
  border: 0;
  font-family: inherit;
  font-size: 100%;
  font-style: inherit;
  font-weight: inherit;
  margin: 0;
  outline: 0;
  padding: 0;
  vertical-align: baseline;
}
html {
  background: -moz-linear-gradient(top, rgba(38, 38, 38, 0.5) 0%, rgba(38, 38, 38, 0.5) 17%, rgba(38, 38, 38, 0) 100%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(38, 38, 38, 0.5)), color-stop(17%,rgba(38, 38, 38, 0.5)), color-stop(100%,rgba(38, 38, 38, 0)));
  background: -webkit-linear-gradient(top, rgba(38, 38, 38, 0.5) 0%,rgba(38, 38, 38, 0.5) 17%,rgba(38, 38, 38, 0) 100%);
  background: -o-linear-gradient(top, rgba(38, 38, 38, 0.5) 0%,rgba(38, 38, 38, 0.5) 17%,rgba(38, 38, 38, 0) 100%);
  background: -ms-linear-gradient(top, rgba(38, 38, 38, 0.5) 0%,rgba(38, 38, 38, 0.5) 17%,rgba(38, 38, 38, 0) 100%);
  background: linear-gradient(to bottom, rgba(38, 38, 38, 0.5) 0%,rgba(38, 38, 38, 0.5) 17%,rgba(38, 38, 38, 0) 100%);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#80262626', endColorstr='#00262626',GradientType=0 );
  background-color: #E0DED7;
}

/* needs refactoring v */
/* defaults for for pre WP8 IE Mobile and other browsers that ignore media queries */
body,page {
  max-width:100%;
  font-family: sans-serif;
}
#branding{
  margin-bottom: .5em;
}

.spacer{height:70px;width:100%;}
#chooser, #newsHeader{
  border:1px solid #007d15;
  text-align: center;
  margin-bottom: .25em;
}
#chooser {
  padding: .25em 0;
}
#chooser p{
  display: inline-block;
  margin: .1em 0;
  border:none;
}
#newsHeader{
  padding: .25em 0;
}
#newsDesc {
  text-align: center;
  margin-bottom: .25em;
}
#newsDesc span {
  display: inline-block;
  vertical-align: middle;
  line-height: 1.5;
  font-weight: bold;
}
#page {
  float:none;
  padding:0 1em;
  margin:0 auto;
  position:static;
  left:0;
  width:auto;
  right:auto;
   overflow:hidden;
  box-shadow: 0 5px 18px rgba(0, 0, 0, 0.3);
  -moz-box-shadow: 0 5px 16px rgba(0, 0, 0, 0.3);
  -webkit-box-shadow: 0 5px 18px rgba(0, 0, 0, 0.3);
}
#footer{
  padding: 1em 0;
  margin-top: 1em;
  margin-right: 1em;
  border-top: 1px solid #007d15;
  display: table;
  width: 100%;
  table-layout: fixed;
}

#footer a {
    display: table-cell;
    text-align: center;
}

body {
  margin:1.5em auto;
  width:100%;
  max-width:69em;
  background-color:white;
}
#updated {text-align: center;}

a, a:hover, a:active {color:#007d15;}

a {text-decoration: none;}

a:hover, a:active{text-decoration: underline;}

#site-title {
  border-bottom: 1px solid #007d15;
  text-align: center;
  padding:.7em 1em .7em .7em;
}
#site-title h1 {
  display: inline-block;
}
#site-title h1 a {
  font-size: 38px;
  font-weight: bold;
  line-height: 28px;
  text-decoration: none;
}

.item{width:100%;}

#site-title a {
  color: black;
}

#site-description {
  color: black;
  display: inline-block;
}
/* Assistive text */
.assistive-text {
  position: absolute !important;
  clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
  clip: rect(1px, 1px, 1px, 1px);
}
#access a.assistive-text:active,
#access a.assistive-text:focus {
  background: #eee;
  border-bottom: 1px solid #007d15;
  color: #1982d1;
  clip: auto !important;
  font-size: 12px;
  position: absolute;
  text-decoration: underline;
  top: 0;
  left: 7.6%;
}
.hiddenLabel{
    border: 0;
    clip: rect(0 0 0 0);
    height: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
    width: 1px;
}
.chk {
  white-space:nowrap;
  padding: 2px;
}
.chk input {margin-right:0;}
.separator {clear:both;}
#left-menu {float:left;}
#right-menu {float:right;}
#left-menu, #right-menu {
  line-height: 20px;
}
#left-menu a, #right-menu a {color:#007d15;}

hr {
    color: #007d15;
    background-color: #007d15;
    height: 1px;
}
    
table{
  table-layout:auto;
  text-align:center;
  width:100%;
}
table, td, th{
  border: 1px solid gray;
  border-collapse: collapse;
}
td, th {
  word-wrap:break-word;
  overflow-wrap: break-word;
  -ms-hyphens: auto;
  -moz-hyphens: auto;
  -webkit-hyphens: auto;
  hyphens: auto;
}
th.hidden, td.hidden, th.optional, td.optional {display: none;}

thead, th {color:white; background-color:#007d15;}
#caption {background-color:#4CAF50; color:black}
#wrap, #newswrap {
    overflow: auto;
    width:100%;
    overflow-x: hidden;
}
#newswrap, #wrap{height: 73vh;}
.textfield {
   width: 10ex;
}
.inputGroup{
  white-space: nowrap;
}
.inputGroup input {
  margin:3px 0;
}
/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}
/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: 1em auto; /* 1em from the top and centered */
    padding: 10px;
    border: 5px solid #007d15;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    width: 80%; /* Could be more or less, depending on screen size */
    max-width:380px;
}
#moTitle {
  margin: .5em auto;
  text-align:center;
}
/* The Close Button */
.close {
    color: #007d15;
    float: right;
    font-size: 20px;
    font-weight: bold;
}
.moItem{
  font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

#news, #plans {
  position:relative;
  top:-50px;
}
#loader {
  display: none;
  background-color: transparent;
  position: fixed;
  z-index: +100 !important;
  width: 100%;
  height:100%;
  left: 0;
  top: 0;
}

#loader img {
  position: fixed;
  top:50%;
  left:50%;
}
/* =Menu
-------------------------------------------------------------- */

ul.menu {
  margin-top: 0pt;
  margin-right: 0pt;
  margin-bottom: 5pt;
  margin-left: 0pt;

}

ul.menu li {
  display: inline-block;
  margin: 0;
}

ul.menu li a {
  padding-top: 5px;
  padding-right: 15px;
  padding-bottom: 5px;
  padding-left: 15px;
  margin-bottom : 0px;
  text-decoration: none;
  font-family: Verdana,Helvetica,Arial,Sans-Serif;
  font-style: normal;
  font-variant: normal;
  font-size: 80%;
  font-size-adjust: none;
  font-stretch: normal;
  font-weight: bold;
  line-height: normal;
  margin-top: 0pt;
  margin-right: 0pt;
  margin-bottom: 0pt;
  margin-left: 0pt;
  color: black;
  background-color: #4caf50;
  background-image: none;
  background-repeat: repeat;
  background-attachment: scroll;
}


ul.menu li a:hover {
  background-color: #007d15;
  background-image: none;
  background-repeat: repeat;
  background-attachment: scroll;
  color: white;
  text-decoration: none;
}

ul.menu li.current_page_item a, ul.menu li.current_page_item a:hover {
  color: white;
  background-color: #007d15;
  background-image: none;
  background-repeat: repeat;
  background-attachment: scroll;
}

#menu-container nav{
  height:auto;
  width:auto;
  float:left;
}
#socialicons img{
  height:25px;
  width:auto;
}
#socialicons a{
  position: relative;
  top: -15%;
}
#socialicons {
  float:right;
  height: 25px;
}

/* media queries for progressive enhancement */

/* greater than 976px two Columns on the desktop and tablet */
@media screen and (min-width: 61em) {

  #primary {
    width: 68%;
    float: right;
    overflow: auto;
  }
  #secondary {
    float: left;
    width: 30%;
    min-width:300px;
    overflow: auto;
  }
  .mobileBar, #left-menu, #right-menu {display:none}
}
/* less than 976px single column primary and secondary */
@media screen and (max-width: 61em){
  #page {padding:0;}
  #primary, #secondary, .menu{
    width: 100%;
    padding-left:.25em;
  }
  #newsHeader{
    height: 3em;
  }
  #main{
    padding-right:.5em;
  }
  #branding{
    position:fixed;
    top: 0;
    width:100%;
    background:white;
    overflow:hidden;
    /* make scocial icons hide behond branding when scrolled */
    z-index:1;
  }
  #branding + * {
    margin-top: 35px;
  }
  #secondary {
    margin-top:0;
  }

  .mobileBar, #left-menu, #right-menu {
    display:block;
  }
}
/* < 640px Reduce font-sizes for better readability on smaller devices */
@media (max-width: 40em) {
  th, td, #chooser {
    font-size: 13px;
  }
  #newsHeader, #chooser{
    height: auto;
  }
  #site-title h1 a {
    font-size: 24px;
    line-height: 20px;
  }
  .modal-content {
    max-width: 75%;
  }
  ul.menu li a {
    padding-right: 5px;
    padding-left: 5px;
  }
  #branding + * {
    margin-top: 28px;
  }
}

@media (pointer:coarse) {
  /* make buttons and other “touch targets” bigger */
  tbody tr{height:2em;}
  td{vertical-align: middle;}
}
</style></head>
<body>
  <div id="loader">
    <img src="wait.gif" class="img-responsive" alt="please wait" />
  </div>
  <div id="page">
    <div id="main">
    <div id="branding" role="banner">
    <div id="site-title">
      <span id="left-menu"><a href="#plans">Plans</a></span>
      <h1><a href="http://prepaidcompare.net/" title="Prepaid Compare" rel="home">prepaid<span style="color:#007d15;">compare</span></a></h1>
      <span id="right-menu"><a href="#news">News</a></span>
    </div>
    <div class="separator"></div>
    </div><!-- #branding -->
    <div id="menu-container">
      <nav>
        <ul class="menu">
          <li class="current_page_item"><a href="/">Home</a></li>
          <li class="page_item"><a href="/profiles/" title="Operator Profiles">Operators</a></li>
          <li class="page_item"><a href="/about.html">About</a></li>
          <li class="page_item"><a href="/privacy.html" title="Privacy Policy">Privacy</a></li>
          <li class="page_item"><a href="/help.html">Help</a></li>
        </ul>
      </nav>
      <div id="socialicons">
        <a target="_blank" title="Twitter" rel="noopener" href="https://www.twitter.com/yeswap"><img alt="twitter" src="twit.png" border=0></a>
        <a target="_blank" title="Facebook" rel="noopener" href="https://www.facebook.com/PrepaidCompare"><img alt="Facebook" src="fb.png" border=0></a>
      </div>
    </div>
      <!-- The Modal -->
      <div id="myModal" class="modal">
      
        <!-- Modal content -->
        <div class="modal-content">
          <span class="close">&times;</span>
          <p id="moTitle"><b><span id="moBrand">Brand</span>
          <i><span id="moPlan"></span></i></b></p>
          <div class="moItem">Website: <span id="moURL"></span></div>
          <div><span class="moItem" >Network:</span> <span id="moNetw"></span></div>
          <div><span id ="moCostLbl" class="moItem"></span><span id="moCost"></span>
          per <span id="moCostTyp"></span></div>
          <div id = "moAutopay"></div>
          <div><span class="moItem">Calculated Monthly Cost:</span> <span id="moMonCost"></span></div>
          <div><span class="moItem">Taxes Collected:</span> <span id="moTaxes"></span></div>
          <div id="moFmlyPlns"></div>
          <div><span class="moItem" >Included Minutes:</span> <span id="moMins"></span>,
          <span class="moItem" >Texts:</span> <span id="moTxts"></span>,
          <span class="moItem">Data:</span> <span id="moData"></span></div>
          <div><span class="moItem">Unlimited Throttled Data?</span> <span id="moThrotld"></span></div>
          <div><span class="moItem">MMS supported on iOS?</span> <span id="moIosMms"></span></div>
          <div><span class="moItem">Hotspot:</span> <span id="moHotspot"></span></div>
          <div><span class="moItem">Domestic Roaming:</span> <span id="moRoaming"></span></div>
          <div><span class="moItem">PayGo?</span>  <span id="moIsPayGo"></span></div>
          <div><span class="moItem">Has Rollover?</span> <span id="moHasRollover"></span></div>
          <div id="moAddons"></div>
          <div id="moNotes"></div>
          <div id="moProfile"></div>
        </div>
      
      </div>
      <div class="separator"></div>
      <div id="news"></div>
      <div id="secondary">
<!-- end of newsmaker static content Part 1 (starts with DOCTYPE) -->
<?Php
    
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "news.inc.php";
?>
    </div><!-- #secondary -->
    <hr class="mobileBar">
    <div id="plans"></div>
  	<div id="primary">
		<div id="content" role="main">

<?php
include "compare.inc.php";
?>
<script>
<?php
include "compare.js";
?>
</script>
	    </div><!-- #primary -->


</div> <!-- #main -->
<div class="separator"></div>
<div id="footer">
  <a href="/profiles/">Operators</a>
  <a href="about.html">About</a>
  <a href="privacy.html">Privacy</a>
  <a href="help.html">Help</a>
</div>

</div>
</body>
</html>
<?php
$site = ob_get_contents();
ob_end_clean();
$sitefile = fopen("index.html", "w");
fwrite($sitefile, $site);
fclose($sitefile);
?>
