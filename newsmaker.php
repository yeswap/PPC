<?php
#if ( function_exists("DebugBreak") ) {
#DebugBreak();
#}
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set("error_log", "/usr/home/yeswap/php-error.log");
error_reporting(E_ALL & ~E_NOTICE);
//write PPC RSS feed headers
$rssFile=fopen("feed.rss", "w");
fwrite($rssFile, '<?xml version="1.0" encoding="utf-8"?>'."\n");
fwrite($rssFile, '<rss version="2.0"  xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/">'."\n");
fwrite($rssFile, "<channel>\n");
fwrite($rssFile, "<title>Prepaid News RSS Feed</title>\n");
fwrite($rssFile, "<link>https://prepaidcompare.net/</link>\n");
fwrite($rssFile,'<atom:link href="https://prepaidcompare.net/feed.rss" rel="self" type="application/rss+xml" />'."\n");
fwrite($rssFile, "<description>The latest US prepaid mobile news from around the web</description>\n");
ob_start();
// Make sure SimplePie is included. You may need to change this to match the location of simplepie.inc.
require_once('../simplepie/autoloader.php');
//require_once('../admin/PPClibrary.php');
date_default_timezone_set('America/Los_Angeles');

function replace_tags($string){
  //replace tags with a single space (code from https://stackoverflow.com/questions/12824899)
$spaceString = str_replace( '<', ' <',$string );
$doubleSpace = strip_tags( $spaceString );
$singleSpace = str_replace( '  ', ' ', $doubleSpace );
return $singleSpace;
}

// Original my Truncate PHP code by Chirp Internet: www.chirp.com.au
// Please acknowledge use of this code by including this header.

function myTruncate($string, $limit, $break=" ", $pad="...")
{
  // return with no change if string is shorter than $limit
  $string = replace_tags($string);
  if(strlen($string) <= $limit) return $string;

  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }

  return $string;
}

// Connect to the database
$connection = new mysqli('db151c.pair.com', 'yeswap_3', 'odfft2v02cc0', 'yeswap_ppcompare');

if ($connection->connect_errno > 0) {
 	die ('Unable to connect to database [' . $connection->connect_error . ']');
}

$connection->query("SET time_zone='-07:00';");

// We'll process this feed with all of the default options.
$feed = new SimplePie();

// Set which feed to process.

$feed->set_feed_url(array(
	'http://bestmvno.com/feed/',
	'http://prepaidmobilephonereviews.com/feed/',
	'https://www.tmonews.com/tag/prepaid/feed/',
  'https://www.tmonews.com/tag/metropcs/feed/',
  'https://coveragecritic.com/feed/',
	'https://blog.google/products/google-fi/rss/',
	'https://bgr.com/tag/metropcs/feed/',
	'https://bgr.com/tag/boost/feed/',
	'http://www.prepaidreviews.com/blog/feed/atom/'
));

$feed->set_cache_duration (600); //set the cache duration to 10 minutes
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

	// Compare the timestamp of the feed item with two weeks ago.
	if ($item->get_date('U') > $oldnews) {
		// If the item was posted within the last 2 weeks, store the item in our array we set up.
		// Check if item is already in the database
    $hashedID = md5($item->get_id());
    $sql = "SELECT * FROM NewsItems WHERE hashedID ='$hashedID' LIMIT 1";
    if (!$result = $connection->query($sql)) {
  	    die ('There was an error running if exists query[' . $connection->error . ']');
    }
    if(mysqli_num_rows($result) == 0){
      //echo "not found<br/>";
      // It's a new item, add it to the table
      //$content= $connection->real_escape_string(trim(myTruncate($item->get_content(), 220)));
      //echo $content;
  		$content = '<blockquote>'.$connection->real_escape_string(trim(myTruncate($item->get_content(), 220))).'</blockquote>';

  		//Get the author if she exists
  		if ($author = $item->get_author()){
  	    $by = $author->get_name();
  		}else{
  		  $creator = $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'creator');
  		  $by = $connection->real_escape_string($creator[0]['data']);
  		}
  		
  		$title = $connection->real_escape_string($item->get_feed()->get_title());
	    $permalink = $connection->real_escape_string($item->get_permalink());
	    $subject = $connection->real_escape_string($item->get_title());
	    $date = $item->get_date('Y-m-d H:i');

      $sql = "INSERT INTO NewsItems(HashedID,title,permalink,creator,subject,date,	content) VALUES ('$hashedID', '$title', '$permalink', '$by', '$subject', '$date', '$content')";
      if (!$result = $connection->query($sql)) {
        echo $sql . '<br />';
  	    die ('There was an error running INSERT query[' . $connection->error . ']');
      }
    }
  }
}

// Retrieve and display items published in the last two weeks

$sql = "SELECT NewsItemID,title,permalink,creator,subject,UNIX_TIMESTAMP(date) as date,content FROM NewsItems WHERE date BETWEEN ADDDATE(NOW(),INTERVAL -2 WEEK) AND NOW()";

if (!$result = $connection->query($sql)) {
  	die ('There was an error running SELECT query[' . $connection->error . ']');
}
$rows = $result->num_rows;
$cols = $result->field_count;
if($rows > 0) {
  while ($row = $result->fetch_array()) {
	  $news[] = array(
    "guid"=>$row['NewsItemID'],
	  "title"=>$row['title'],
	  "permalink"=>$row['permalink'],
	  "creator"=>$row['creator'],
	  "subject" =>$row['subject'],
    "date"=>$row['date'],
    "content"=>($row['content']));
	  //"content"=>nl2br($row['content']));
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
    fwrite($rssFile, "<item>\n");
    fwrite($rssFile, "<title><![CDATA[".$item["title"]." - ".$item["subject"]."]]></title>\n");
    fwrite($rssFile, "<link>".htmlentities($item["permalink"])."</link>\n");
    fwrite($rssFile, '<guid isPermaLink="false">prepaidcompare.net/'.$item["guid"]."</guid>\n");
    fwrite($rssFile, '<dc:creator>'.htmlentities($item["creator"])."</dc:creator>\n");
    fwrite($rssFile, "<description><![CDATA[".htmlentities(strip_tags($item["content"]))."]]></description>\n");
    fwrite($rssFile, "<content:encoded><![CDATA[".$item["content"]."]]></content:encoded>\n");
    fwrite($rssFile, "<pubDate>".date('r', $item["date"])."</pubDate>\n");
    fwrite($rssFile, "</item>\n");
?>
		<div class="item">
			<b><?php echo $item["title"]; ?> - <a href="<?php echo $item["permalink"]; ?>"><?php echo $item["subject"]; ?></a></b>

<?php
			$creator = $item["creator"];
?>

			<p class="entry-meta">Posted <?php if($creator)echo 'by '. $creator . ' '; ?><?php echo date('j F Y | g:i a', $item["date"]); ?></p>
			<p><?php echo $item["content"]; ?></p>
		</div>
 		<hr class="bar"/>
<?php }
echo "</div>\n";
fwrite($rssFile, "</channel>\n");
fwrite($rssFile, "</rss>\n");
fclose($rssFile);
$news = ob_get_contents();
ob_end_clean();
$newsfile = fopen("news.inc.php", "w");
fwrite($newsfile, $news);
fclose($newsfile);
ob_start();
?>
<!DOCTYPE html>
<!-- test comment -->
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
<link rel="canonical" href="https://prepaidcompare.net" />
<meta name="description" content="Find the Best Prepaid Plan for Any Budget">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=rMJdgme0zY">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=rMJdgme0zY">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=rMJdgme0zY">
<link rel="manifest" href="/site.webmanifest?v=rMJdgme0zY">
<link rel="mask-icon" href="/safari-pinned-tab.svg?v=rMJdgme0zY" color="#5bbad5">
<link rel="shortcut icon" href="/favicon.ico?v=rMJdgme0zY">
<link rel="manifest" href="site.webmanifest">
<link rel="alternate" href="/feed.rss" title="Prepaid News RSS feed" type="application/rss+xml" />
<meta name="apple-mobile-web-app-title" content="PPCompare">
<meta name="application-name" content="PPCompare">
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
<meta property="og:image" content="https://prepaidcompare.net/ms-icon-310x310.png" />

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
  "url": "https://prepaidcompare.net",
  "name": "PrepaidCompare",
   "author": {
      "@type": "Person",
      "name": "Dennis Bournique",
       "sameAs" : [ "https://www.facebook.com/wapreview",
      "https://www.reddit.com/user/yeswap",
      "https://twitter.com/yeswap",
      "https://www.howardforums.com/member.php/262421-Yeswap"]
    },
  "description" : "A comprehensive database of prepaid mobile operators and plans that can be searched and filtered to show the lowest priced plans for any number of minutes, texts, data and lines of service",
  "publisher": "PrepaidCompare"
}
]
</script>
<link href="compare.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div id="loader">
    <img src="wait.gif" class="img-responsive" alt="please wait" />
  </div>
  <div id="page">
    <div id="main">
    <div id="branding" role="banner">
    <div id="site-title">
      <span id="left-menu"><a id="showPlans" href="#plans">Plans</a></span>
      <h1><a href="http://prepaidcompare.net/" title="Prepaid Compare" rel="home">prepaid<span style="color:#007d15;">compare</span></a></h1>
      <span id="right-menu"><a id="showNews" href="#news">News</a></span>
    </div>
    <div class="separator"></div>
    </div><!-- #branding -->
    <div id="menu-container">
      <nav>
        <ul class="menu">
          <li class="current_page_item"><a href="/">Home</a></li>
          <li class="page_item"><a href="/deals" title="Deals">Deals</a></li>
          <li class="page_item"><a href="/profiles/" title="Operator Profiles">Operators</a></li>
          <li class="page_item"><a href="/about.html">About</a></li>
          <!--<li class="page_item"><a href="/privacy.html" title="Privacy Policy">Privacy</a></li>-->
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
          <div><span class="moItem" >Network:</span> <span id="moNetw"></span></div>
          <div class="moItem">Website: <span id="moURL"></span></div>
          <div id="moOprNotes"></div>
          <hr />
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
          <div><span class="moItem">VoLTE?</span> <span id="moVoLTE"></span></div>
          <div><span class="moItem">WiFi Calling?</span> <span id="moVoWiFi"></span></div>
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
      include "news.inc.php";
?>
    </div>
      <div id="plans"></div>
  	  <div id="primary">
		  <div id="content" role="main">
<?php
      include "compare.inc.php";
?>
      <script src="compare.js" type="text/javascript">
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